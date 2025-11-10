# 🔧 Solución: Error "Unexpected end of JSON input" en Login Local

## 🚨 Problema
Al intentar iniciar sesión localmente, aparece: `Failed to execute 'json' on 'Response': Unexpected end of JSON input`

## ✅ Solución Aplicada

### 1. CORS Configurado ✅
- Actualizado `api/config/cors.php` para permitir localhost
- Permite `http://localhost`, `http://127.0.0.1` y tu dominio de producción

### 2. Archivos PHP Corregidos ✅
- `api/auth/login.php` - Eliminadas líneas duplicadas de CORS
- `api/auth/register.php` - Añadido CORS correctamente
- `api/config/database.php` - Headers CORS separados

## 📋 Pasos para Probar Localmente

### 1. Verificar que XAMPP esté corriendo
- Abrir XAMPP Control Panel
- **Apache** debe estar en verde (Start)
- **MySQL** debe estar en verde (Start)

### 2. Ubicar tu carpeta del proyecto

**Opción A: Si usas la carpeta htdocs de XAMPP** (recomendado)
```
C:\xampp\htdocs\eventsphere2\
```

**Opción B: Si quieres usar tu carpeta actual**
```
C:\Users\kathy\Documents\eventsphere2\
```

### 3. Configurar Apache para tu carpeta (si usas Opción B)

#### 3.1 Editar httpd.conf
1. Abrir: `C:\xampp\apache\conf\httpd.conf`
2. Buscar la línea: `DocumentRoot "C:/xampp/htdocs"`
3. Cambiarla por: `DocumentRoot "C:/Users/kathy/Documents"`
4. Buscar: `<Directory "C:/xampp/htdocs">`
5. Cambiarla por: `<Directory "C:/Users/kathy/Documents">`

#### 3.2 Reiniciar Apache
1. En XAMPP Control Panel, click en **Stop** en Apache
2. Esperar 2 segundos
3. Click en **Start** en Apache

### 4. Crear la Base de Datos

#### 4.1 Abrir phpMyAdmin
- Ir a: http://localhost/phpmyadmin

#### 4.2 Crear la base de datos
1. Click en "Nueva" (izquierda)
2. Nombre: `eventsphere_db`
3. Cotejamiento: `utf8mb4_unicode_ci`
4. Click "Crear"

#### 4.3 Importar el schema
1. Seleccionar `eventsphere_db` (izquierda)
2. Click en "Importar" (arriba)
3. Click "Seleccionar archivo"
4. Buscar: `C:\Users\kathy\Documents\eventsphere2\database\eventsphere_schema.sql`
5. Click "Continuar"
6. Esperar confirmación (debe crear 10 tablas)

### 5. Probar la API

#### Opción A: Desde el navegador
Abre en tu navegador:
```
http://localhost/eventsphere2/api/test.php
```

Debe mostrar:
```json
{
  "success": true,
  "message": "✅ Conexión exitosa a la base de datos",
  "api_status": "OK",
  "timestamp": "2025-11-10 12:00:00"
}
```

#### Opción B: Desde PowerShell
```powershell
Invoke-WebRequest -Uri "http://localhost/eventsphere2/api/test.php" -UseBasicParsing | Select-Object -ExpandProperty Content
```

### 6. Probar el Login

#### 6.1 Abrir la página de login
```
http://localhost/eventsphere2/login.html
```

#### 6.2 Usar las credenciales de prueba
**Email:** admin@eventsphere.com
**Password:** test123

#### 6.3 Si funciona
✅ Deberías ser redirigido a `eventos.html`

#### 6.4 Si NO funciona
1. Abre las Herramientas de Desarrollador del navegador (F12)
2. Ve a la pestaña "Console"
3. Intenta hacer login de nuevo
4. Copia cualquier error que aparezca en rojo
5. Ve a la pestaña "Network"
6. Busca la petición a `login.php`
7. Click en ella y ve a la pestaña "Response"
8. Copia la respuesta completa

## 🧪 Tests Rápidos

### Test 1: Verificar que Apache funciona
```
http://localhost
```
Debe mostrar la página de inicio de XAMPP.

### Test 2: Verificar que PHP funciona
Crea un archivo `test.php` en la raíz:
```php
<?php
phpinfo();
?>
```
Abre: `http://localhost/test.php`
Debe mostrar información de PHP.

### Test 3: Verificar ruta del proyecto
```
http://localhost/eventsphere2/index.html
```
Debe cargar la página principal de EventSphere.

### Test 4: Verificar API de prueba
```
http://localhost/eventsphere2/api/test.php
```
Debe mostrar JSON con éxito.

## 🐛 Solución de Problemas Comunes

### Error: "404 Not Found"
**Causa:** La carpeta no está en htdocs o DocumentRoot mal configurado
**Solución:** 
1. Copia la carpeta `eventsphere2` a `C:\xampp\htdocs\`
2. Reinicia Apache

### Error: "Access Denied for user 'root'@'localhost'"
**Causa:** Contraseña de MySQL incorrecta
**Solución:**
1. Verifica en `api/config/database.php`:
   ```php
   private $username = "root";
   private $password = "";  // Vacío para XAMPP
   ```

### Error: "Unknown database 'eventsphere_db'"
**Causa:** Base de datos no creada
**Solución:**
- Sigue el paso 4 para crear la BD e importar el schema

### Error: "CORS policy error"
**Causa:** Headers CORS mal configurados (ya corregido)
**Solución:**
- Los archivos ya están actualizados con CORS correcto

### Error: "Unexpected end of JSON input"
**Causa:** PHP devuelve HTML o error en lugar de JSON
**Solución:**
1. Abre: `http://localhost/eventsphere2/api/auth/login.php`
2. Verifica qué mensaje de error muestra
3. Corrige según el error mostrado

## ✅ Checklist Final

- [ ] XAMPP está corriendo (Apache + MySQL)
- [ ] Carpeta está en `C:\xampp\htdocs\eventsphere2\` O DocumentRoot configurado
- [ ] Base de datos `eventsphere_db` creada
- [ ] Schema SQL importado (10 tablas)
- [ ] Test API funciona: http://localhost/eventsphere2/api/test.php
- [ ] Página principal carga: http://localhost/eventsphere2/index.html
- [ ] Login funciona: http://localhost/eventsphere2/login.html

## 📞 Si Sigues Teniendo Problemas

Abre PowerShell en la carpeta del proyecto y ejecuta:
```powershell
# Test 1: Ver si Apache responde
Invoke-WebRequest -Uri "http://localhost" -UseBasicParsing

# Test 2: Ver respuesta de la API
Invoke-WebRequest -Uri "http://localhost/eventsphere2/api/test.php" -UseBasicParsing | Select-Object -ExpandProperty Content

# Test 3: Ver respuesta del login (debe dar error sin datos POST)
Invoke-WebRequest -Uri "http://localhost/eventsphere2/api/auth/login.php" -UseBasicParsing | Select-Object -ExpandProperty Content
```

Copia y pega los resultados para ayudarte mejor.

## 🎯 Solución Rápida (Recomendada)

**La forma más fácil:**
1. Copia toda la carpeta `eventsphere2` a `C:\xampp\htdocs\`
2. Crea la BD en phpMyAdmin: http://localhost/phpmyadmin
3. Importa el schema SQL
4. Abre: http://localhost/eventsphere2/login.html
5. Login con: admin@eventsphere.com / test123

¡Listo! 🎉
