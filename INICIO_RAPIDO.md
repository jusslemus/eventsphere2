# 🚀 EventSphere - Guía de Inicio Rápido (Desarrollo Local)

## 📋 Requisitos Previos

1. **Node.js** instalado (para Vite)
2. **XAMPP** instalado (para PHP y MySQL)
   - Descargar: https://www.apachefriends.org/

## 🔧 Configuración Inicial (Solo la primera vez)

### 1. Instalar dependencias de Node.js
```powershell
npm install
```

### 2. Crear la base de datos

#### 2.1 Iniciar MySQL en XAMPP
- Abre XAMPP Control Panel
- Click en **Start** en MySQL (solo MySQL, Apache no es necesario)

#### 2.2 Crear la base de datos
- Abre: http://localhost/phpmyadmin
- Click en "Nueva"
- Nombre: `eventsphere_db`
- Cotejamiento: `utf8mb4_unicode_ci`
- Click "Crear"

#### 2.3 Importar el schema
- Selecciona `eventsphere_db` (izquierda)
- Click "Importar"
- Selecciona el archivo: `database/eventsphere_schema.sql`
- Click "Continuar"
- Debe crear 10 tablas

### 3. Verificar credenciales de BD
Abre: `api/config/database.php` y verifica:
```php
private $username = "root";
private $password = "";  // Vacío para XAMPP por defecto
```

## 🚀 Iniciar el Proyecto (Cada vez que trabajes)

### Opción A: Con scripts automáticos (Recomendado)

**1. Abrir 2 terminales en VS Code**

**Terminal 1 - Frontend (Vite):**
```powershell
npm run dev
```
Esto inicia en: http://localhost:5174/

**Terminal 2 - Backend (PHP):**
```powershell
.\start-php-server.ps1
```
O si no funciona:
```powershell
.\start-php-server.bat
```
Esto inicia en: http://localhost:8000/

### Opción B: Manual

**Terminal 1 - Frontend:**
```powershell
npm run dev
```

**Terminal 2 - Backend (ajusta la ruta de PHP según tu instalación):**
```powershell
cd api
C:\xampp\php\php.exe -S localhost:8000
```

## ✅ Verificar que todo funciona

### 1. Test del Frontend
Abre: http://localhost:5174/
Debe cargar la página principal de EventSphere

### 2. Test del Backend (API)
Abre: http://localhost:8000/test.php
Debe mostrar:
```json
{
  "success": true,
  "message": "✅ Conexión exitosa a la base de datos",
  "api_status": "OK"
}
```

### 3. Test de Login
1. Abre: http://localhost:5174/login.html
2. Credenciales:
   - **Email:** admin@eventsphere.com
   - **Password:** test123
3. Debe redirigir a eventos.html

## 🎯 URLs Importantes (Desarrollo)

- **Frontend (Vite):** http://localhost:5174/
- **Backend (PHP API):** http://localhost:8000/
- **phpMyAdmin:** http://localhost/phpmyadmin

## 📂 Estructura del Proyecto

```
eventsphere2/
├── api/                    # Backend PHP
│   ├── auth/              # Login, registro
│   ├── eventos/           # CRUD de eventos
│   ├── boletos/           # Compra y validación
│   └── config/            # Configuración BD y CORS
├── src/                   # Frontend Vue.js
│   ├── views/            # Páginas
│   └── components/       # Componentes reutilizables
├── js/                    # JavaScript vanilla (para HTML estáticos)
├── css/                   # Estilos
└── database/             # Schema SQL
```

## 🐛 Solución de Problemas

### Error: "php no se reconoce como comando"
**Solución:** Usa los scripts `start-php-server.bat` o `start-php-server.ps1`

### Error: "Port 5173 is in use"
**Normal:** Vite usa automáticamente el siguiente puerto disponible (5174, 5175, etc.)

### Error: "Unexpected end of JSON input"
**Causa:** El servidor PHP no está corriendo
**Solución:** Inicia el servidor PHP en la Terminal 2

### Error: "Connection refused"
**Causa:** MySQL no está corriendo
**Solución:** Inicia MySQL en XAMPP Control Panel

### Error: "Access denied for user 'root'"
**Causa:** Contraseña de MySQL incorrecta
**Solución:** Verifica en `api/config/database.php` que la contraseña sea `""` (vacío)

### Error: "Unknown database 'eventsphere_db'"
**Causa:** Base de datos no creada
**Solución:** Sigue el paso 2 de Configuración Inicial

## 🔄 Flujo de Trabajo Diario

1. Abrir VS Code
2. Abrir 2 terminales
3. Terminal 1: `npm run dev`
4. Terminal 2: `.\start-php-server.ps1`
5. Abrir navegador: http://localhost:5174/
6. Empezar a desarrollar 🎉

## 📝 Comandos Útiles

```powershell
# Instalar dependencias
npm install

# Iniciar frontend
npm run dev

# Iniciar backend (con script)
.\start-php-server.ps1

# Compilar para producción
npm run build

# Preview de producción
npm run preview
```

## 🎉 ¡Todo Listo!

Ahora puedes desarrollar con:
- **Hot Reload** en el frontend (Vite)
- **API PHP** funcionando en paralelo
- **MySQL** para los datos

## 📞 ¿Necesitas Ayuda?

1. Verifica que ambos servidores estén corriendo (Frontend + Backend)
2. Revisa la consola del navegador (F12) para errores
3. Verifica que MySQL esté activo en XAMPP
4. Asegúrate de que la base de datos esté creada e importada

---

**Nota:** No necesitas Apache de XAMPP, solo MySQL. El frontend usa Vite y el backend usa el servidor PHP integrado.
