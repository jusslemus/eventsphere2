# EventSphere - Instalación de Base de Datos

## Requisitos previos
- MySQL 5.7+ o MariaDB 10.3+
- XAMPP, WAMP, MAMP o servidor MySQL independiente

## Opción 1: Instalación con phpMyAdmin (XAMPP/WAMP)

1. **Inicia XAMPP/WAMP** y activa Apache y MySQL

2. **Abre phpMyAdmin** en tu navegador:
   ```
   http://localhost/phpmyadmin
   ```

3. **Importa el script SQL:**
   - Click en "Importar" en el menú superior
   - Click en "Seleccionar archivo"
   - Selecciona: `database/eventsphere_schema.sql`
   - Click en "Continuar"

4. **Verifica la instalación:**
   - Deberías ver la base de datos `eventsphere_db` en la lista de la izquierda
   - Con 10 tablas creadas
   - Y datos de ejemplo insertados

## Opción 2: Instalación por línea de comandos

### En Windows (PowerShell):
```powershell
# Navega a la carpeta de MySQL (ajusta la ruta según tu instalación)
cd "C:\xampp\mysql\bin"

# Ejecuta el script
.\mysql.exe -u root -p < "c:\Users\kathy\Documents\eventsphere2\database\eventsphere_schema.sql"
```

### En Linux/Mac:
```bash
mysql -u root -p < database/eventsphere_schema.sql
```

## Opción 3: Crear usuario y permisos manualmente

Si necesitas crear el usuario de base de datos, ejecuta estos comandos en MySQL:

```sql
CREATE USER IF NOT EXISTS 'eventsphere_user'@'localhost' IDENTIFIED BY 'juss07lems.';
GRANT ALL PRIVILEGES ON eventsphere_db.* TO 'eventsphere_user'@'localhost';
FLUSH PRIVILEGES;
```

## Verificación de la instalación

Ejecuta esta consulta para verificar que todo esté correcto:

```sql
USE eventsphere_db;
SHOW TABLES;
SELECT COUNT(*) FROM usuarios;
SELECT COUNT(*) FROM eventos;
SELECT COUNT(*) FROM categorias;
```

Deberías ver:
- 10 tablas creadas
- 3 usuarios de prueba
- 3 eventos de ejemplo
- 6 categorías

## Estructura de la base de datos

### Tablas principales:
- `usuarios` - Usuarios registrados
- `categorias` - Categorías de eventos (Conciertos, Deportes, etc.)
- `eventos` - Eventos creados
- `compras` - Compras de boletos
- `boletos` - Boletos individuales con código QR
- `comunidades` - Comunidades por evento
- `mensajes_comunidad` - Chat de la comunidad
- `fotos_evento` - Galería de fotos
- `resenas` - Reseñas y calificaciones

## Usuarios de prueba

Después de instalar, puedes usar estos usuarios para probar:

| Email | Password | Nombre |
|-------|----------|---------|
| admin@eventsphere.com | test123 | Admin EventSphere |
| juan@example.com | test123 | Juan Pérez |
| maria@example.com | test123 | María García |

## Configuración del backend

El archivo `api/config/database.php` ya está configurado con:
- Host: `localhost`
- Database: `eventsphere_db`
- Usuario: `eventsphere_user`
- Password: `juss07lems.`

Si usas credenciales diferentes, edita ese archivo.

## Problemas comunes

### Error: Access denied for user
- Verifica que el usuario MySQL exista o usa `root` temporalmente
- Cambia las credenciales en `api/config/database.php`

### Error: Database already exists
- Si ya existe, elimínala primero:
  ```sql
  DROP DATABASE eventsphere_db;
  ```

### Tablas no se crean
- Verifica permisos del usuario MySQL
- Asegúrate de tener MySQL 5.7+ o MariaDB 10.3+

## Soporte

Si tienes problemas, revisa:
1. Los logs de MySQL/phpMyAdmin
2. Las credenciales en `api/config/database.php`
3. Que MySQL esté corriendo

¡Listo! 🎉 Tu base de datos EventSphere está configurada.