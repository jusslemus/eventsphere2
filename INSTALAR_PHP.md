# 🚀 EventSphere - Instalación de PHP (Solo Backend)

## ⚡ Opción Rápida: PHP Standalone (Recomendado)

### 1. Descargar PHP
- Ve a: https://windows.php.net/download/
- Descarga: **PHP 8.2 Thread Safe (x64)** - ZIP
- Ejemplo: `php-8.2.12-Win32-vs16-x64.zip`

### 2. Extraer PHP
- Extrae el ZIP a: `C:\php\`
- Debería quedar: `C:\php\php.exe`

### 3. Verificar instalación
Abre PowerShell y ejecuta:
```powershell
C:\php\php.exe -v
```
Debe mostrar la versión de PHP.

### 4. Configurar extensiones necesarias
1. En `C:\php\`, copia el archivo `php.ini-development` y renómbralo a `php.ini`
2. Abre `php.ini` con un editor de texto
3. Busca y descomenta (quitar el `;` al inicio) estas líneas:
```ini
extension=mysqli
extension=pdo_mysql
extension=openssl
extension=mbstring
```

### 5. Iniciar servidor PHP
```powershell
cd C:\Users\kathy\Documents\eventsphere2\api
C:\php\php.exe -S localhost:8000
```

### 6. Actualizar script de inicio
Edita `start-php-server.ps1` y cambia la línea de rutas por:
```powershell
$phpPaths = @(
    "C:\php\php.exe",
    "C:\xampp\php\php.exe",
    "C:\wamp64\bin\php\php8.2.12\php.exe"
)
```

---

## 🔧 Opción 2: XAMPP (Más completo)

### 1. Descargar XAMPP
- Ve a: https://www.apachefriends.org/
- Descarga: **XAMPP para Windows**
- Versión recomendada: 8.2.12

### 2. Instalar XAMPP
- Ejecuta el instalador
- Selecciona: Apache, MySQL, PHP
- Instalar en: `C:\xampp\`

### 3. Verificar instalación
```powershell
C:\xampp\php\php.exe -v
```

### 4. Iniciar solo MySQL
- Abre XAMPP Control Panel
- Click **Start** solo en MySQL (Apache no es necesario)

---

## ✅ Después de instalar PHP

### Prueba rápida:
```powershell
# En la carpeta del proyecto
cd C:\Users\kathy\Documents\eventsphere2

# Ejecutar el script
.\start-php-server.ps1
```

O manualmente:
```powershell
cd api
C:\php\php.exe -S localhost:8000
```

### Verificar que funciona:
Abre en el navegador: http://localhost:8000/test.php

Debe mostrar:
```json
{
  "success": true,
  "message": "✅ Conexión exitosa a la base de datos"
}
```

---

## 🎯 Resumen

**Archivos que ya funcionan sin Vue.js:**
- ✅ `index.html` - Página principal
- ✅ `login.html` - Iniciar sesión  
- ✅ `register.html` - Crear cuenta
- ✅ `eventos.html` - Ver eventos
- ✅ `evento-detalle.html` - Detalle de evento
- ✅ `crear-evento.html` - Crear evento
- ✅ `mis-boletos.html` - Mis boletos
- ✅ `validar-boleto.html` - Validar entrada
- ✅ `mi-perfil.html` - Mi perfil
- ✅ `comunidad.html` - Comunidad

**Solo necesitas:**
1. Instalar PHP (Opción 1 o 2)
2. Iniciar MySQL en XAMPP
3. Crear base de datos
4. Iniciar servidor PHP: `.\start-php-server.ps1`
5. Abrir: `http://localhost:8000/../login.html`

¡No necesitas npm, Vite ni Vue.js para que funcione! 🎉
