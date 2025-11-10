# 🚀 Guía de Despliegue - EventSphere en kathyap.ddns.net

## ✅ Configuración Actual
- **Dominio**: https://kathyap.ddns.net
- **SSL**: ✅ Certificado configurado
- **Backend**: PHP + MySQL
- **Frontend**: HTML/CSS/JS

## 📦 Paso 1: Subir archivos al servidor

### Usando FTP/SFTP (FileZilla o WinSCP):
1. Conectar a `kathyap.ddns.net` con tus credenciales FTP
2. Subir el archivo `eventsphere2-deploy.zip` a la raíz web (usualmente `/public_html` o `/var/www/html`)
3. Descomprimir en el servidor o hacerlo local y subir la carpeta `eventsphere2/`

### Estructura final en el servidor:
```
/public_html/  (o /var/www/html/)
  └── eventsphere2/
      ├── api/
      ├── database/
      ├── css/
      ├── js/
      ├── assets/
      ├── uploads/
      ├── index.html
      ├── login.html
      ├── .htaccess
      └── (otros archivos HTML)
```

## 🗄️ Paso 2: Crear Base de Datos en el Servidor

### Opción A: Con phpMyAdmin
1. Acceder a: `https://kathyap.ddns.net/phpmyadmin`
2. Click en "Nueva" para crear base de datos
3. Nombre: `eventsphere_db`
4. Cotejamiento: `utf8mb4_unicode_ci`
5. Click "Crear"

### Importar el schema:
1. Seleccionar la BD `eventsphere_db` (izquierda)
2. Click en "Importar" (arriba)
3. "Seleccionar archivo" → Buscar `database/eventsphere_schema.sql`
4. Click "Continuar"
5. Esperar confirmación (debe crear 10 tablas)

### Opción B: Por SSH (si tienes acceso)
```bash
# Conectar por SSH
ssh tu_usuario@kathyap.ddns.net

# Crear base de datos
mysql -u root -p
```
```sql
CREATE DATABASE eventsphere_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```
```bash
# Importar schema
mysql -u root -p eventsphere_db < /ruta/al/eventsphere2/database/eventsphere_schema.sql
```

## ⚙️ Paso 3: Configurar credenciales de Base de Datos

**IMPORTANTE**: Una vez en el servidor, edita el archivo:
```
eventsphere2/api/config/database.php
```

Actualiza con las credenciales reales de tu servidor:

```php
class Database {
    private $host = "localhost";  // o la IP que te dieron
    private $db_name = "eventsphere_db";
    private $username = "TU_USUARIO_MYSQL";  // ⚠️ CAMBIAR
    private $password = "TU_PASSWORD_MYSQL";  // ⚠️ CAMBIAR
    private $charset = "utf8mb4";
    // ...
}
```

Pregunta a tu proveedor de hosting:
- ¿Cuál es el usuario de MySQL?
- ¿Cuál es la contraseña?
- ¿El host es `localhost` o una IP específica?

## 🔐 Paso 4: Configurar permisos de carpetas

Si tienes acceso SSH:
```bash
cd /public_html/eventsphere2  # o tu ruta
chmod -R 755 .
chmod -R 777 uploads/
```

Si usas FTP, cambia permisos de la carpeta `uploads/` a **777** (lectura/escritura/ejecución para todos).

## 🌐 Paso 5: Configurar .htaccess (Forzar HTTPS)

El archivo `.htaccess` ya está configurado. Asegúrate de que esté en la raíz de `eventsphere2/` con este contenido:

```apache
# Forzar HTTPS
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{HTTPS} off
    RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
</IfModule>

# SPA routing
<IfModule mod_rewrite.c>
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ index.html [L,QSA]
</IfModule>

# Seguridad y cache (ya configurado)
```

## 🧪 Paso 6: Probar la instalación

### 6.1 Probar acceso al sitio:
```
https://kathyap.ddns.net/eventsphere2/
```
Debería cargar la página principal.

### 6.2 Probar la API:
```
https://kathyap.ddns.net/eventsphere2/api/auth/register.php
```
Debe devolver un JSON con error (es normal sin datos POST).

### 6.3 Probar registro de usuario:
1. Ir a: `https://kathyap.ddns.net/eventsphere2/register.html`
2. Llenar el formulario
3. Click "Crear Cuenta"
4. Debe mostrar mensaje de éxito

### 6.4 Verificar en BD:
Ir a phpMyAdmin → `eventsphere_db` → tabla `usuarios`
Debe aparecer el nuevo usuario registrado.

## ✅ URLs Finales

- **Home**: https://kathyap.ddns.net/eventsphere2/
- **Login**: https://kathyap.ddns.net/eventsphere2/login.html
- **Registro**: https://kathyap.ddns.net/eventsphere2/register.html
- **Eventos**: https://kathyap.ddns.net/eventsphere2/eventos.html
- **API**: https://kathyap.ddns.net/eventsphere2/api/

## 🔧 Configuraciones ya aplicadas:

✅ Dominio configurado: `kathyap.ddns.net`
✅ SSL habilitado en URLs
✅ CORS configurado para tu dominio
✅ API_URL detecta automáticamente localhost vs producción
✅ .htaccess con seguridad y forzar HTTPS

## 🐛 Solución de Problemas

### Error: "No se puede conectar a la BD"
- Verifica credenciales en `api/config/database.php`
- Asegúrate de que el usuario MySQL tenga permisos en `eventsphere_db`

### Error: "404 Not Found" en la API
- Verifica que `.htaccess` esté en la carpeta correcta
- Comprueba que `mod_rewrite` esté habilitado en Apache

### Error: "Permission denied" al subir archivos
- Cambia permisos de `uploads/` a 777
- Verifica que Apache pueda escribir en esa carpeta

### Error de CORS
- Verifica que en `api/config/database.php` el header sea:
  ```php
  header('Access-Control-Allow-Origin: https://kathyap.ddns.net');
  ```

## 📝 Checklist Final

- [ ] Archivos subidos a `/public_html/eventsphere2/` (o tu ruta)
- [ ] Base de datos `eventsphere_db` creada
- [ ] Schema SQL importado (10 tablas)
- [ ] Credenciales MySQL actualizadas en `api/config/database.php`
- [ ] Permisos de `uploads/` configurados (777)
- [ ] `.htaccess` en la raíz de eventsphere2/
- [ ] Probar: https://kathyap.ddns.net/eventsphere2/
- [ ] Probar registro de usuario
- [ ] Probar login
- [ ] Verificar usuarios en phpMyAdmin

## 🎉 Usuarios de prueba

Una vez importada la BD, puedes usar:
- **Email**: admin@eventsphere.com | **Password**: test123
- **Email**: juan@example.com | **Password**: test123
- **Email**: maria@example.com | **Password**: test123

## 📞 Siguiente paso

Dame las credenciales de MySQL de tu servidor (usuario, contraseña, host) y las actualizo en el archivo de configuración para que lo subas listo para funcionar. 

O si prefieres hacerlo tú, solo necesitas editar:
```
eventsphere2/api/config/database.php
```

¡Líneas 6-8! 🎯
