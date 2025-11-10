-- =====================================================
-- Script de Actualización para EventSphere
-- Ejecutar esto si ya tienes la base de datos creada
-- =====================================================

USE eventsphere_db;

-- =====================================================
-- Actualizar tabla usuarios (agregar columnas faltantes)
-- =====================================================

-- Agregar foto_perfil si no existe
ALTER TABLE usuarios 
ADD COLUMN IF NOT EXISTS foto_perfil VARCHAR(255) DEFAULT NULL AFTER email;

-- Agregar telefono si no existe
ALTER TABLE usuarios 
ADD COLUMN IF NOT EXISTS telefono VARCHAR(20) DEFAULT NULL AFTER foto_perfil;

-- Agregar bio si no existe
ALTER TABLE usuarios 
ADD COLUMN IF NOT EXISTS bio TEXT DEFAULT NULL AFTER telefono;

-- Agregar o modificar columna estado
ALTER TABLE usuarios 
ADD COLUMN IF NOT EXISTS estado ENUM('activo', 'inactivo', 'suspendido') DEFAULT 'activo' AFTER password;

-- Eliminar columna 'activo' antigua si existe y migrar datos
-- UPDATE usuarios SET estado = IF(activo = 1, 'activo', 'inactivo') WHERE activo IS NOT NULL;
-- ALTER TABLE usuarios DROP COLUMN IF EXISTS activo;

-- =====================================================
-- Verificar estructura actualizada
-- =====================================================
DESCRIBE usuarios;

-- =====================================================
-- Opcional: Agregar índice en estado si no existe
-- =====================================================
-- ALTER TABLE usuarios ADD INDEX IF NOT EXISTS idx_estado (estado);

-- =====================================================
-- Mensaje de confirmación
-- =====================================================
SELECT 'Base de datos actualizada exitosamente!' as Mensaje;
SELECT COUNT(*) as 'Total de usuarios' FROM usuarios;
SELECT COUNT(*) as 'Total de eventos' FROM eventos;
SELECT COUNT(*) as 'Total de categorías' FROM categorias;
