# ✅ Checklist de Instalación XAMPP

## 📝 Sigue estos pasos en orden:

### □ Paso 1: Descargar XAMPP
- [ ] Ir a: https://www.apachefriends.org/download.html
- [ ] Descargar XAMPP 8.2.12 para Windows (aprox. 150 MB)

### □ Paso 2: Instalar XAMPP
- [ ] Ejecutar el instalador descargado
- [ ] Seleccionar: Apache, MySQL, PHP, phpMyAdmin
- [ ] Instalar en: `C:\xampp`
- [ ] Completar la instalación (5-10 minutos)

### □ Paso 3: Iniciar MySQL
- [ ] Abrir XAMPP Control Panel
- [ ] Click en "Start" en la línea de MySQL
- [ ] Esperar a que el fondo se ponga verde

### □ Paso 4: Crear Base de Datos
- [ ] Click en "Admin" al lado de MySQL (abre phpMyAdmin)
- [ ] Click en "Nueva" (izquierda)
- [ ] Nombre: `eventsphere_db`
- [ ] Cotejamiento: `utf8mb4_unicode_ci`
- [ ] Click "Crear"

### □ Paso 5: Importar Schema
- [ ] Seleccionar `eventsphere_db` (izquierda)
- [ ] Click en "Importar" (arriba)
- [ ] Seleccionar archivo: `C:\Users\kathy\Documents\eventsphere2\database\eventsphere_schema.sql`
- [ ] Click "Continuar"
- [ ] Verificar que se crearon 10 tablas

### □ Paso 6: Verificar PHP
En VS Code, abrir terminal y ejecutar:
```powershell
C:\xampp\php\php.exe -v
```
- [ ] Debe mostrar: PHP 8.2.12 (o similar)

### □ Paso 7: Iniciar Servidor PHP
En VS Code:
```powershell
.\start-php-server.ps1
```
- [ ] Debe mostrar: "Servidor PHP corriendo en: http://localhost:8000"

### □ Paso 8: Probar API
Abrir en navegador: http://localhost:8000/test.php
- [ ] Debe mostrar: `{"success": true, "message": "✅ Conexión exitosa..."}`

### □ Paso 9: Iniciar Frontend (opcional para archivos HTML)
En otra terminal de VS Code:
```powershell
npm run dev
```
- [ ] Debe mostrar: http://localhost:5174/

### □ Paso 10: Probar Login
Opción A (con Vite): http://localhost:5174/login.html
Opción B (directo): http://localhost:8000/../login.html

- [ ] Email: admin@eventsphere.com
- [ ] Password: test123
- [ ] Click "Iniciar Sesión"
- [ ] Debe redirigir a eventos.html

---

## 🎉 ¡Todo Listo!

Si todos los pasos tienen ✅, el proyecto está funcionando correctamente.

## 🆘 Si algo falla:

1. Abre el archivo: `INSTALACION_XAMPP.md`
2. Ve a la sección "🐛 Solución de Problemas"
3. Busca tu error específico

## 📱 Contacto Rápido

Ejecuta este comando si tienes problemas:
```powershell
# Diagnóstico rápido
Write-Host "=== Diagnóstico EventSphere ===" -ForegroundColor Cyan
Write-Host ""
Write-Host "1. PHP instalado:" -ForegroundColor Yellow
Test-Path "C:\xampp\php\php.exe"
Write-Host ""
Write-Host "2. MySQL corriendo:" -ForegroundColor Yellow
Test-NetConnection -ComputerName localhost -Port 3306 -WarningAction SilentlyContinue | Select-Object -ExpandProperty TcpTestSucceeded
Write-Host ""
Write-Host "3. Base de datos:" -ForegroundColor Yellow
Write-Host "   Abre: http://localhost/phpmyadmin"
Write-Host "   Verifica que existe: eventsphere_db"
```
