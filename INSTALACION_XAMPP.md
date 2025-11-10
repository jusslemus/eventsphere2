# 🚀 Guía de Instalación de XAMPP para EventSphere

## 📥 Paso 1: Descargar XAMPP

1. Abre tu navegador
2. Ve a: **https://www.apachefriends.org/download.html**
3. Descarga **XAMPP para Windows**
   - Versión recomendada: **8.2.12** o superior
   - Archivo: `xampp-windows-x64-8.2.12-0-VS16-installer.exe`

## 💾 Paso 2: Instalar XAMPP

1. **Ejecuta el instalador** que descargaste
2. Si aparece advertencia de Antivirus/UAC, click **"Sí"** o **"Permitir"**
3. Si aparece advertencia sobre UAC, click **"OK"**
4. **Selecciona los componentes** (marca estos):
   - ✅ Apache
   - ✅ MySQL
   - ✅ PHP
   - ✅ phpMyAdmin
   - ⬜ Otros (opcional)

5. **Carpeta de instalación:**
   - Recomendado: `C:\xampp`
   - Click **"Next"**

6. **Idioma:**
   - Puedes dejar inglés o español
   - Click **"Next"**

7. **Bitnami:**
   - Desmarca "Learn more about Bitnami"
   - Click **"Next"**

8. **Iniciar instalación:**
   - Click **"Next"**
   - Espera a que termine (5-10 minutos)

9. **Finalizar:**
   - Marca "Do you want to start the Control Panel now?"
   - Click **"Finish"**

## 🎛️ Paso 3: Configurar XAMPP

### 3.1 Abrir XAMPP Control Panel

Si no se abrió automáticamente:
- Busca "XAMPP Control Panel" en el menú inicio
- O ejecuta: `C:\xampp\xampp-control.exe`

### 3.2 Iniciar MySQL

1. En XAMPP Control Panel, busca la línea de **MySQL**
2. Click en el botón **"Start"** de MySQL
3. El botón debe cambiar a **"Stop"** y el fondo ponerse verde
4. **NO necesitas iniciar Apache** (usaremos el servidor PHP integrado)

### 3.3 Verificar que MySQL funciona

1. Click en **"Admin"** al lado de MySQL
2. Debe abrir phpMyAdmin en el navegador
3. URL: `http://localhost/phpmyadmin`

## 🗄️ Paso 4: Crear la Base de Datos

### 4.1 En phpMyAdmin:

1. Click en **"Nueva"** (o "New") en el menú izquierdo
2. **Nombre de la base de datos:** `eventsphere_db`
3. **Cotejamiento:** `utf8mb4_unicode_ci`
4. Click **"Crear"** (o "Create")

### 4.2 Importar el Schema:

1. Selecciona `eventsphere_db` en el menú izquierdo
2. Click en la pestaña **"Importar"** (o "Import") arriba
3. Click en **"Seleccionar archivo"** (o "Choose File")
4. Navega a: `C:\Users\kathy\Documents\eventsphere2\database\eventsphere_schema.sql`
5. Click **"Abrir"**
6. Scroll hacia abajo y click **"Continuar"** (o "Go")
7. Debe aparecer un mensaje de éxito en verde
8. En el menú izquierdo, bajo `eventsphere_db`, deben aparecer 10 tablas:
   - boletos
   - categorias_evento
   - chat_mensajes
   - comentarios_evento
   - comunidad_fotos
   - comunidad_posts
   - eventos
   - resenas
   - usuarios
   - usuarios_eventos_guardados

## ✅ Paso 5: Verificar la Instalación

### 5.1 Verificar PHP

Abre PowerShell en VS Code y ejecuta:
```powershell
C:\xampp\php\php.exe -v
```

Debe mostrar algo como:
```
PHP 8.2.12 (cli) (built: Oct 24 2023 21:15:15) (ZTS Visual C++ 2019 x64)
```

### 5.2 Actualizar el script de inicio

El script `start-php-server.ps1` ya está configurado para buscar XAMPP automáticamente.

## 🚀 Paso 6: Iniciar el Proyecto

### 6.1 Abrir 2 Terminales en VS Code

**Terminal 1 - Frontend (Vite):**
```powershell
npm run dev
```
URL: http://localhost:5174/

**Terminal 2 - Backend (PHP):**
```powershell
.\start-php-server.ps1
```
URL: http://localhost:8000/

### 6.2 Probar la API

Abre en el navegador: **http://localhost:8000/test.php**

Debe mostrar:
```json
{
  "success": true,
  "message": "✅ Conexión exitosa a la base de datos",
  "api_status": "OK",
  "timestamp": "2025-11-10 12:00:00"
}
```

### 6.3 Probar el Login

1. Abre: **http://localhost:5174/login.html**
2. Ingresa:
   - **Email:** admin@eventsphere.com
   - **Password:** test123
3. Click **"Iniciar Sesión"**
4. Debe redirigir a la página de eventos

## 🎉 ¡Listo!

Ahora tienes:
- ✅ XAMPP instalado
- ✅ MySQL corriendo
- ✅ Base de datos creada e importada
- ✅ PHP disponible
- ✅ Proyecto listo para desarrollo

## 🔄 Flujo de Trabajo Diario

Cada vez que quieras trabajar en el proyecto:

1. **Abrir XAMPP Control Panel**
2. **Iniciar MySQL** (click en "Start")
3. **Abrir VS Code**
4. **Terminal 1:** `npm run dev`
5. **Terminal 2:** `.\start-php-server.ps1`
6. **Abrir navegador:** http://localhost:5174/

## 🐛 Solución de Problemas

### MySQL no inicia en XAMPP

**Problema:** Puerto 3306 ocupado
**Solución:**
1. En XAMPP, click en "Config" de MySQL
2. Selecciona "my.ini"
3. Busca `port=3306`
4. Cámbialo a `port=3307`
5. Guarda y reinicia MySQL
6. Actualiza `api/config/database.php`:
   ```php
   private $host = "localhost:3307";
   ```

### Error: "Access denied for user 'root'"

**Solución:**
En `api/config/database.php`, verifica:
```php
private $username = "root";
private $password = "";  // Vacío por defecto en XAMPP
```

### Error: "Unknown database 'eventsphere_db'"

**Solución:**
Repite el Paso 4 para crear e importar la base de datos.

## 📞 ¿Necesitas Ayuda?

Si algo no funciona, ejecuta estos comandos en PowerShell:

```powershell
# Verificar PHP
C:\xampp\php\php.exe -v

# Verificar MySQL
Test-NetConnection -ComputerName localhost -Port 3306

# Ver errores de PHP
cd api
C:\xampp\php\php.exe -S localhost:8000
# Y prueba abrir: http://localhost:8000/test.php
```

Copia y pega los resultados para ayudarte mejor.

---

**Siguiente paso:** Una vez instalado XAMPP, ejecuta en VS Code:
```powershell
.\start-php-server.ps1
```
