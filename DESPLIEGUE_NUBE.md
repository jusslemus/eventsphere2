# 🚀 Guía Rápida de Despliegue en kathyap.ddns.net

## ✅ Cambios realizados
- Se corrigieron las rutas de CSS y JS en los archivos HTML
- Se actualizó la configuración de CORS para permitir el dominio

## 📤 Archivos que DEBES subir al servidor

### Subir TODO el proyecto a: `/public_html/eventsphere2/` (o tu carpeta web)

**Archivos principales:**
```
eventsphere2/
├── index.html ✅ (corregido)
├── login.html ✅
├── register.html ✅
├── eventos.html ✅
├── evento-detalle.html ✅
├── crear-evento.html ✅
├── mis-boletos.html ✅
├── validar-boleto.html ✅
├── mi-perfil.html ✅
├── comunidad.html ✅
├── .htaccess ✅
│
├── css/ ✅ (toda la carpeta)
│   ├── style.css
│   ├── theme.css
│   ├── componentes.css
│   └── responsive.css
│
├── js/ ✅ (toda la carpeta)
│   ├── config.js
│   ├── auth.js
│   ├── eventos.js
│   ├── evento-detalle.js
│   ├── boletos.js
│   ├── comunidad.js
│   ├── utils.js
│   └── validador.js
│
├── api/ ✅ (toda la carpeta - ya funciona)
│   ├── auth/
│   ├── eventos/
│   ├── boletos/
│   └── config/
│
├── assets/ ✅ (toda la carpeta)
│   ├── icons/
│   └── images/
│
└── uploads/ ✅ (toda la carpeta con permisos 777)
    └── fotos/
```

## 🔧 Método 1: FTP (FileZilla/WinSCP)

1. **Conectar por FTP a:** `kathyap.ddns.net`
2. **Navegar a:** `/public_html/eventsphere2/`
3. **Subir todos los archivos** (sobreescribir si ya existen)
4. **Carpetas importantes:**
   - `css/` - Estilos
   - `js/` - Scripts
   - `assets/` - Imágenes
   - `api/` - Backend PHP
   - `uploads/` - Subidas de usuarios

## 🔧 Método 2: Panel de Control (cPanel)

1. Ir a tu panel de hosting
2. **Administrador de Archivos**
3. Navegar a `public_html/eventsphere2/`
4. **Subir archivos** o usar **Extraer ZIP**

## 🔧 Método 3: Crear ZIP y subirlo

```powershell
# En PowerShell (ya en la carpeta del proyecto)
Compress-Archive -Path .\* -DestinationPath eventsphere2-fixed.zip -Force
```

Luego:
1. Sube `eventsphere2-fixed.zip` al servidor
2. Extráelo en `/public_html/eventsphere2/`
3. Elimina el ZIP

## ✅ Verificar después de subir

### 1. Probar CSS
Abre: `https://kathyap.ddns.net/eventsphere2/login.html`
- ¿Se ve el diseño bonito? ✅
- ¿Los colores están correctos? ✅
- ¿Los botones tienen estilo? ✅

### 2. Probar API
Abre: `https://kathyap.ddns.net/eventsphere2/api/test.php`
Debe mostrar:
```json
{
  "success": true,
  "message": "✅ Conexión exitosa a la base de datos"
}
```

### 3. Probar Login
1. Ir a: `https://kathyap.ddns.net/eventsphere2/login.html`
2. Credenciales:
   - **Email:** admin@eventsphere.com
   - **Password:** test123
3. Click "Iniciar Sesión"
4. Debe redirigir a eventos.html

## 🐛 Si el diseño TODAVÍA no se ve

### Verificar en el servidor:
1. Verifica que estas carpetas existan:
   - `/public_html/eventsphere2/css/`
   - `/public_html/eventsphere2/js/`
   - `/public_html/eventsphere2/assets/`

2. Verifica permisos de archivos:
   - Archivos: 644
   - Carpetas: 755
   - `uploads/`: 777

3. Abre el Navegador > F12 > Console
   - ¿Hay errores en rojo?
   - ¿Dice "404 Not Found" para algún archivo CSS/JS?

4. Ve a la pestaña "Network" (Red)
   - Recarga la página
   - ¿Qué archivos fallan al cargar?

## 📱 URLs Finales

- **Home:** https://kathyap.ddns.net/eventsphere2/
- **Login:** https://kathyap.ddns.net/eventsphere2/login.html
- **Registro:** https://kathyap.ddns.net/eventsphere2/register.html
- **Eventos:** https://kathyap.ddns.net/eventsphere2/eventos.html

## ✨ Usuarios de prueba (ya en la BD)

- **Admin:**
  - Email: admin@eventsphere.com
  - Password: test123

- **Usuario 1:**
  - Email: juan@example.com
  - Password: test123

- **Usuario 2:**
  - Email: maria@example.com
  - Password: test123

---

## 🎉 ¡Todo listo!

Una vez subidos los archivos, deberías ver el sitio completamente funcional con el diseño correcto.

**¿Sigues teniendo problemas?** Abre el navegador, ve a tu sitio, presiona F12 y revisa la pestaña "Console" para ver qué archivo está fallando.
