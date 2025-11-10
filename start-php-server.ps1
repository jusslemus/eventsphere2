# Script de PowerShell para iniciar el servidor PHP
Write-Host "Iniciando servidor PHP para EventSphere API..." -ForegroundColor Cyan
Write-Host ""

# Buscar PHP en ubicaciones comunes (XAMPP primero)
$phpPaths = @(
    "C:\xampp\php\php.exe",
    "C:\XAMPP\php\php.exe",
    "D:\xampp\php\php.exe",
    "C:\wamp64\bin\php\php8.2.12\php.exe",
    "C:\wamp64\bin\php\php8.1.0\php.exe",
    "C:\php\php.exe",
    "C:\Program Files\PHP\php.exe"
)

$phpExe = $null
foreach ($path in $phpPaths) {
    if (Test-Path $path) {
        $phpExe = $path
        break
    }
}

if ($null -eq $phpExe) {
    Write-Host "ERROR: No se encontró PHP instalado" -ForegroundColor Red
    Write-Host ""
    Write-Host "Por favor instala XAMPP desde: https://www.apachefriends.org/" -ForegroundColor Yellow
    Write-Host "O especifica la ruta de PHP manualmente en este script" -ForegroundColor Yellow
    Read-Host "Presiona Enter para salir"
    exit 1
}

Write-Host "PHP encontrado en: $phpExe" -ForegroundColor Green
Write-Host ""
Write-Host "Servidor PHP corriendo en: http://localhost:8000" -ForegroundColor Green
Write-Host "API disponible en: http://localhost:8000/auth/login.php" -ForegroundColor Green
Write-Host ""
Write-Host "Presiona Ctrl+C para detener el servidor" -ForegroundColor Yellow
Write-Host ""

Set-Location api
& $phpExe -S localhost:8000
