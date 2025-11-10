# Script para preparar archivos para producción en kathyap.ddns.net/eventsphere2/

Write-Host "🔧 Preparando archivos para producción..." -ForegroundColor Cyan
Write-Host ""

$basePath = "eventsphere2"  # Cambia esto si tu carpeta se llama diferente en el servidor

# Archivos HTML a procesar
$htmlFiles = Get-ChildItem -Path . -Filter "*.html" -File

foreach ($file in $htmlFiles) {
    Write-Host "📝 Procesando: $($file.Name)" -ForegroundColor Yellow
    
    $content = Get-Content $file.FullName -Raw -Encoding UTF8
    $modified = $false
    
    # Corregir rutas absolutas a relativas
    if ($content -match 'href="/css/') {
        $content = $content -replace 'href="/css/', 'href="css/'
        $modified = $true
        Write-Host "  ✓ Corregido href CSS" -ForegroundColor Green
    }
    
    if ($content -match 'href="/js/') {
        $content = $content -replace 'href="/js/', 'href="js/'
        $modified = $true
        Write-Host "  ✓ Corregido href JS" -ForegroundColor Green
    }
    
    if ($content -match 'src="/css/') {
        $content = $content -replace 'src="/css/', 'src="css/'
        $modified = $true
        Write-Host "  ✓ Corregido src CSS" -ForegroundColor Green
    }
    
    if ($content -match 'src="/js/') {
        $content = $content -replace 'src="/js/', 'src="js/'
        $modified = $true
        Write-Host "  ✓ Corregido src JS" -ForegroundColor Green
    }
    
    if ($content -match 'src="/assets/') {
        $content = $content -replace 'src="/assets/', 'src="assets/'
        $modified = $true
        Write-Host "  ✓ Corregido src assets" -ForegroundColor Green
    }
    
    if ($content -match 'href="/assets/') {
        $content = $content -replace 'href="/assets/', 'href="assets/'
        $modified = $true
        Write-Host "  ✓ Corregido href assets" -ForegroundColor Green
    }
    
    # Corregir referencias a /src/ (Vue)
    if ($content -match 'src="/src/') {
        $content = $content -replace 'src="/src/', 'src="src/'
        $modified = $true
        Write-Host "  ✓ Corregido src Vue" -ForegroundColor Green
    }
    
    if ($modified) {
        # Guardar archivo
        $content | Set-Content -Path $file.FullName -Encoding UTF8 -NoNewline
        Write-Host "  [OK] Guardado" -ForegroundColor Green
    } else {
        Write-Host "  [OK] Ya correcto" -ForegroundColor Gray
    }
    
    Write-Host ""
}

Write-Host "✅ ¡Listo! Archivos preparados para producción" -ForegroundColor Green
Write-Host ""
Write-Host "📤 Próximos pasos:" -ForegroundColor Cyan
Write-Host "1. Sube todos los archivos HTML al servidor (sobreescribiendo los anteriores)"
Write-Host "2. Verifica que las carpetas css/, js/, assets/ estén en el servidor"
Write-Host "3. Abre: https://kathyap.ddns.net/eventsphere2/login.html"
Write-Host ""
