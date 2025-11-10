@echo off
echo Iniciando servidor PHP para EventSphere API...
echo.

REM Buscar PHP en ubicaciones comunes
if exist "C:\xampp\php\php.exe" (
    set PHP_PATH=C:\xampp\php\php.exe
) else if exist "C:\wamp64\bin\php\php8.2.12\php.exe" (
    set PHP_PATH=C:\wamp64\bin\php\php8.2.12\php.exe
) else if exist "C:\Program Files\PHP\php.exe" (
    set PHP_PATH="C:\Program Files\PHP\php.exe"
) else (
    echo ERROR: No se encontro PHP instalado
    echo.
    echo Por favor instala XAMPP desde: https://www.apachefriends.org/
    echo O especifica la ruta manualmente en este archivo
    pause
    exit /b 1
)

echo PHP encontrado en: %PHP_PATH%
echo.
echo Servidor PHP corriendo en: http://localhost:8000
echo API disponible en: http://localhost:8000/auth/login.php
echo.
echo Presiona Ctrl+C para detener el servidor
echo.

cd api
%PHP_PATH% -S localhost:8000

pause
