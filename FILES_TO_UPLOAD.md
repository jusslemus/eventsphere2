# 📦 Archivos para Subir al Servidor LAMP

## ✅ INCLUIR estos archivos/carpetas:

### Backend (PHP)
```
📁 api/
  ├── auth/
  │   ├── login.php
  │   └── register.php
  ├── boletos/
  ├── chat/
  ├── config/
  │   ├── database.php ⚠️ (ACTUALIZAR credenciales)
  │   ├── cors.php
  │   └── database.prod.php (referencia)
  ├── eventos/
  ├── fotos/
  └── resenas/
```

### Base de Datos
```
📁 database/
  ├── eventsphere_schema.sql ✨ (IMPORTAR primero)
  └── README.md
```

### Frontend (HTML/CSS/JS)
```
📄 index.html
📄 login.html
📄 register.html
📄 comunidad.html
📄 eventos.html
📄 evento-detalle.html
📄 crear-evento.html
📄 mi-perfil.html
📄 mis-boletos.html
📄 validar-boleto.html
📄 .htaccess ✨ (nuevo)

📁 css/
  ├── style.css
  ├── theme.css
  ├── componentes.css
  └── responsive.css

📁 js/
  ├── config.js ⚠️ (verificar API_URL)
  ├── auth.js
  ├── eventos.js
  ├── boletos.js
  ├── comunidad.js
  ├── evento-detalle.js
  ├── utils.js
  └── validador.js

📁 assets/
  ├── icons/
  └── images/

📁 uploads/
  └── (carpeta vacía o con archivos)
```

### Documentación
```
📄 README.md
📄 DEPLOY.md ✨ (nueva - guía completa)
```

## ❌ NO INCLUIR:

```
❌ node_modules/          (paquetes npm - demasiado grande)
❌ src/                   (código fuente Vue - solo para desarrollo)
❌ .git/                  (historial git)
❌ .vscode/               (configuración VS Code)
❌ dist/                  (se genera automáticamente)
❌ package.json           (no necesario en producción PHP)
❌ package-lock.json
❌ vite.config.js
❌ eslint.config.js
❌ jsconfig.json
❌ .gitignore
```

## 🚀 Orden de Despliegue:

### 1️⃣ Preparar archivos localmente
```powershell
# Comprimir solo lo necesario
Compress-Archive -Path api,database,css,js,assets,uploads,*.html,.htaccess,DEPLOY.md -DestinationPath eventsphere2-deploy.zip
```

### 2️⃣ Subir al servidor
- Usar FTP/SFTP (FileZilla, WinSCP)
- O usar SCP si tienes SSH:
```bash
scp eventsphere2-deploy.zip usuario@servidor:/var/www/html/
```

### 3️⃣ En el servidor (SSH)
```bash
# Descomprimir
cd /var/www/html
unzip eventsphere2-deploy.zip -d eventsphere2/

# Permisos
cd eventsphere2
chmod -R 755 .
chmod -R 777 uploads/
chown -R www-data:www-data .
```

### 4️⃣ Configurar Base de Datos
```bash
# Importar schema
mysql -u root -p < database/eventsphere_schema.sql

# Crear usuario (si no existe)
mysql -u root -p
```
```sql
CREATE USER 'eventsphere_user'@'localhost' IDENTIFIED BY 'TU_PASSWORD';
GRANT ALL PRIVILEGES ON eventsphere_db.* TO 'eventsphere_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### 5️⃣ Actualizar Configuración
```bash
# Editar credenciales de BD
nano api/config/database.php
```
Cambiar:
- `$username = "root"` → Tu usuario real
- `$password = ""` → Tu contraseña real
- `$host = "localhost"` → IP de tu servidor MySQL (si es externo)

### 6️⃣ Verificar API_URL
```bash
nano js/config.js
```
El código ya detecta automáticamente si está en localhost o producción:
```javascript
const API_URL = window.location.hostname === 'localhost' 
    ? 'http://localhost/eventsphere2/api'
    : '/api';
```

### 7️⃣ Probar
```
https://tudominio.com/index.html
https://tudominio.com/api/auth/register.php
```

## ⚙️ Configuración Post-Despliegue

### Apache VirtualHost (opcional)
```bash
sudo nano /etc/apache2/sites-available/eventsphere2.conf
```

### SSL con Let's Encrypt
```bash
sudo certbot --apache -d tudominio.com
```

### Habilitar módulos Apache
```bash
sudo a2enmod rewrite headers expires deflate
sudo systemctl restart apache2
```

## 📊 Tamaño Estimado del ZIP
- Con archivos necesarios: ~5-15 MB
- Sin node_modules ni .git: Ligero y rápido de subir

## 🔍 Verificación Final

### Checklist:
- [ ] Base de datos importada
- [ ] Usuario MySQL creado
- [ ] Credenciales actualizadas en `api/config/database.php`
- [ ] Permisos de `uploads/` configurados (777)
- [ ] Apache reiniciado
- [ ] .htaccess funcionando
- [ ] Probar registro de usuario
- [ ] Probar login
- [ ] Probar creación de evento

## 📞 ¿Necesitas ayuda?

Si tienes problemas, verifica:
1. Logs de Apache: `sudo tail -f /var/log/apache2/error.log`
2. Logs de PHP: `sudo tail -f /var/log/php/error.log`
3. Permisos de carpetas: `ls -la /var/www/html/eventsphere2`
4. Conexión a BD: `mysql -u eventsphere_user -p eventsphere_db`
