# 🚀 Guía de Despliegue - EventSphere en Servidor LAMP

## 📋 Requisitos del Servidor
- Sistema Operativo: Linux (Ubuntu 20.04+ recomendado)
- Apache 2.4+
- MySQL 5.7+ o MariaDB 10.3+
- PHP 8.0+
- SSL/HTTPS configurado (opcional pero recomendado)

## 🔧 Paso 1: Preparar los archivos

### 1.1 Comprimir el proyecto
Excluye las carpetas innecesarias:
```bash
# En tu computadora local
cd C:\Users\kathy\Documents\eventsphere2
```

Crea un ZIP con estos archivos/carpetas:
- ✅ `api/` (backend PHP)
- ✅ `database/` (scripts SQL)
- ✅ `uploads/` (carpeta de archivos subidos)
- ❌ `node_modules/` (NO incluir)
- ❌ `src/` (NO incluir - es solo para desarrollo Vue)
- ❌ `.git/` (NO incluir)

### 1.2 Build del frontend Vue
Antes de subir, construye el frontend para producción:
```bash
npm run build
```

Esto creará una carpeta `dist/` con los archivos optimizados.

## 🌐 Paso 2: Configurar el Frontend Vue

Actualiza la URL de la API en `js/config.js`:

```javascript
// Cambiar de:
const API_URL = 'http://localhost/eventsphere2/api';

// A tu dominio real:
const API_URL = 'https://tudominio.com/api';
// o si está en subcarpeta:
const API_URL = 'https://tudominio.com/eventsphere2/api';
```

## 📤 Paso 3: Subir archivos al servidor

### Opción A: Por FTP/SFTP
1. Conecta a tu servidor con FileZilla o WinSCP
2. Sube los archivos a `/var/www/html/eventsphere2/` (o tu directorio web)

### Opción B: Por SSH
```bash
# Comprimir en local
zip -r eventsphere2.zip api/ database/ uploads/ dist/ *.html *.php

# Subir al servidor
scp eventsphere2.zip usuario@tu-servidor.com:/var/www/html/

# En el servidor, descomprimir
ssh usuario@tu-servidor.com
cd /var/www/html
unzip eventsphere2.zip
```

## 🗄️ Paso 4: Configurar la Base de Datos

### 4.1 Conectarse al servidor MySQL
```bash
mysql -u root -p
```

### 4.2 Crear la base de datos
```sql
-- Crear base de datos
CREATE DATABASE eventsphere_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Crear usuario (CAMBIAR password)
CREATE USER 'eventsphere_user'@'localhost' IDENTIFIED BY 'TU_PASSWORD_SEGURO_AQUI';

-- Dar permisos
GRANT ALL PRIVILEGES ON eventsphere_db.* TO 'eventsphere_user'@'localhost';
FLUSH PRIVILEGES;

-- Salir
EXIT;
```

### 4.3 Importar el schema
```bash
mysql -u root -p eventsphere_db < /var/www/html/eventsphere2/database/eventsphere_schema.sql
```

### 4.4 Verificar
```bash
mysql -u eventsphere_user -p eventsphere_db -e "SHOW TABLES;"
```

Deberías ver 10 tablas creadas.

## ⚙️ Paso 5: Configurar el Backend PHP

### 5.1 Editar configuración de BD
```bash
nano /var/www/html/eventsphere2/api/config/database.php
```

Actualizar con tus credenciales reales:
```php
private $host = "localhost";
private $db_name = "eventsphere_db";
private $username = "eventsphere_user";  // Tu usuario real
private $password = "TU_PASSWORD_AQUI";   // Tu password real
```

### 5.2 Configurar permisos
```bash
cd /var/www/html/eventsphere2
chmod -R 755 .
chmod -R 777 uploads/
chown -R www-data:www-data .
```

### 5.3 Actualizar CORS en database.php
Si tu frontend estará en un dominio diferente:
```php
// Cambiar de:
header('Access-Control-Allow-Origin: *');

// A tu dominio específico:
header('Access-Control-Allow-Origin: https://tudominio.com');
```

## 🔐 Paso 6: Configurar Apache

### 6.1 Crear VirtualHost (opcional)
```bash
sudo nano /etc/apache2/sites-available/eventsphere2.conf
```

```apache
<VirtualHost *:80>
    ServerName tudominio.com
    ServerAlias www.tudominio.com
    
    DocumentRoot /var/www/html/eventsphere2
    
    <Directory /var/www/html/eventsphere2>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    # Configuración PHP
    <FilesMatch \.php$>
        SetHandler "proxy:unix:/run/php/php8.1-fpm.sock|fcgi://localhost"
    </FilesMatch>
    
    ErrorLog ${APACHE_LOG_DIR}/eventsphere2-error.log
    CustomLog ${APACHE_LOG_DIR}/eventsphere2-access.log combined
</VirtualHost>
```

### 6.2 Habilitar módulos y sitio
```bash
sudo a2enmod rewrite
sudo a2enmod headers
sudo a2ensite eventsphere2.conf
sudo systemctl restart apache2
```

### 6.3 Crear archivo .htaccess para la API
```bash
nano /var/www/html/eventsphere2/api/.htaccess
```

```apache
# Habilitar CORS
Header set Access-Control-Allow-Origin "*"
Header set Access-Control-Allow-Methods "GET, POST, PUT, DELETE, OPTIONS"
Header set Access-Control-Allow-Headers "Content-Type, Authorization"

# Reescritura de URLs limpias (opcional)
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]
```

## 🔒 Paso 7: Configurar HTTPS (SSL)

### Con Let's Encrypt (Gratis):
```bash
sudo apt install certbot python3-certbot-apache
sudo certbot --apache -d tudominio.com -d www.tudominio.com
```

Certbot configurará automáticamente SSL y la renovación automática.

## ✅ Paso 8: Verificar la instalación

### 8.1 Probar la API
```bash
curl https://tudominio.com/api/auth/register.php
```

Debería devolver un error JSON (es normal, falta el body).

### 8.2 Probar el frontend
Abre en el navegador:
```
https://tudominio.com
```

### 8.3 Probar registro
1. Ve a la página de registro
2. Llena el formulario
3. Si funciona, verás un mensaje de éxito

## 🐛 Troubleshooting

### Error: "Access denied for user"
```bash
# Verificar usuario MySQL
mysql -u eventsphere_user -p eventsphere_db
```

### Error: "404 Not Found" en la API
```bash
# Verificar que mod_rewrite esté habilitado
sudo a2enmod rewrite
sudo systemctl restart apache2

# Verificar permisos
ls -la /var/www/html/eventsphere2/api
```

### Error: "Permission denied" al subir archivos
```bash
# Dar permisos a la carpeta uploads
sudo chmod -R 777 /var/www/html/eventsphere2/uploads
sudo chown -R www-data:www-data /var/www/html/eventsphere2/uploads
```

### Ver logs de errores
```bash
# Apache error log
sudo tail -f /var/log/apache2/error.log

# PHP error log
sudo tail -f /var/log/apache2/eventsphere2-error.log
```

## 📝 Checklist Final

- [ ] Archivos subidos al servidor
- [ ] Base de datos creada e importada
- [ ] Credenciales de BD actualizadas en `api/config/database.php`
- [ ] Permisos de carpetas configurados (especialmente `uploads/`)
- [ ] URL de API actualizada en `js/config.js`
- [ ] Apache configurado y reiniciado
- [ ] SSL/HTTPS configurado
- [ ] Prueba de registro funcionando
- [ ] Prueba de login funcionando
- [ ] Prueba de creación de eventos funcionando

## 🎉 ¡Listo!

Tu aplicación EventSphere ya está en producción.

### URLs importantes:
- Frontend: `https://tudominio.com`
- API: `https://tudominio.com/api`
- phpMyAdmin: `https://tudominio.com/phpmyadmin` (si está instalado)

### Usuarios de prueba:
- Email: `admin@eventsphere.com` / Password: `test123`
- Email: `juan@example.com` / Password: `test123`
- Email: `maria@example.com` / Password: `test123`
