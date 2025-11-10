-- =====================================================
-- EventSphere Database Schema
-- Base de datos: eventsphere_db
-- =====================================================

-- Crear base de datos
CREATE DATABASE IF NOT EXISTS eventsphere_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE eventsphere_db;

-- =====================================================
-- Tabla: usuarios
-- =====================================================
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    foto_perfil VARCHAR(255) DEFAULT NULL,
    telefono VARCHAR(20) DEFAULT NULL,
    bio TEXT DEFAULT NULL,
    password VARCHAR(255) NOT NULL,
    estado ENUM('activo', 'inactivo', 'suspendido') DEFAULT 'activo',
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
    ultima_conexion DATETIME DEFAULT NULL,
    INDEX idx_email (email),
    INDEX idx_estado (estado),
    INDEX idx_fecha_registro (fecha_registro)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Tabla: categorias
-- =====================================================
CREATE TABLE IF NOT EXISTS categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE,
    descripcion TEXT,
    icono VARCHAR(50) DEFAULT '🎭',
    activo BOOLEAN DEFAULT TRUE,
    INDEX idx_nombre (nombre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Tabla: eventos
-- =====================================================
CREATE TABLE IF NOT EXISTS eventos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    descripcion TEXT NOT NULL,
    categoria_id INT NOT NULL,
    organizador_id INT NOT NULL,
    fecha_evento DATETIME NOT NULL,
    ubicacion VARCHAR(255) NOT NULL,
    direccion VARCHAR(500),
    capacidad_total INT NOT NULL,
    boletos_disponibles INT NOT NULL,
    precio_boleto DECIMAL(10, 2) NOT NULL,
    imagen_portada VARCHAR(255) DEFAULT 'default-event.jpg',
    estado_evento ENUM('activo', 'cancelado', 'finalizado') DEFAULT 'activo',
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE RESTRICT,
    FOREIGN KEY (organizador_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_fecha_evento (fecha_evento),
    INDEX idx_categoria (categoria_id),
    INDEX idx_organizador (organizador_id),
    INDEX idx_estado (estado_evento)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Tabla: compras
-- =====================================================
CREATE TABLE IF NOT EXISTS compras (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    evento_id INT NOT NULL,
    cantidad_boletos INT NOT NULL,
    precio_total DECIMAL(10, 2) NOT NULL,
    metodo_pago VARCHAR(50) NOT NULL,
    estado_compra ENUM('pendiente', 'completada', 'cancelada', 'reembolsada') DEFAULT 'completada',
    fecha_compra DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (evento_id) REFERENCES eventos(id) ON DELETE CASCADE,
    INDEX idx_usuario (usuario_id),
    INDEX idx_evento (evento_id),
    INDEX idx_fecha_compra (fecha_compra)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Tabla: boletos
-- =====================================================
CREATE TABLE IF NOT EXISTS boletos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    compra_id INT NOT NULL,
    evento_id INT NOT NULL,
    usuario_id INT NOT NULL,
    codigo_unico VARCHAR(50) NOT NULL UNIQUE,
    qr_hash VARCHAR(255) NOT NULL,
    estado_boleto ENUM('activo', 'usado', 'cancelado') DEFAULT 'activo',
    fecha_validacion DATETIME DEFAULT NULL,
    validado_por INT DEFAULT NULL,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (compra_id) REFERENCES compras(id) ON DELETE CASCADE,
    FOREIGN KEY (evento_id) REFERENCES eventos(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (validado_por) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_codigo_unico (codigo_unico),
    INDEX idx_qr_hash (qr_hash),
    INDEX idx_usuario (usuario_id),
    INDEX idx_evento (evento_id),
    INDEX idx_estado (estado_boleto)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Tabla: comunidades
-- =====================================================
CREATE TABLE IF NOT EXISTS comunidades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    evento_id INT NOT NULL UNIQUE,
    nombre VARCHAR(255) NOT NULL,
    descripcion TEXT,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (evento_id) REFERENCES eventos(id) ON DELETE CASCADE,
    INDEX idx_evento (evento_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Tabla: mensajes_comunidad
-- =====================================================
CREATE TABLE IF NOT EXISTS mensajes_comunidad (
    id INT AUTO_INCREMENT PRIMARY KEY,
    comunidad_id INT NOT NULL,
    usuario_id INT NOT NULL,
    mensaje TEXT NOT NULL,
    fecha_envio DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (comunidad_id) REFERENCES comunidades(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_comunidad (comunidad_id),
    INDEX idx_fecha_envio (fecha_envio)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Tabla: fotos_evento
-- =====================================================
CREATE TABLE IF NOT EXISTS fotos_evento (
    id INT AUTO_INCREMENT PRIMARY KEY,
    evento_id INT NOT NULL,
    usuario_id INT NOT NULL,
    ruta_archivo VARCHAR(255) NOT NULL,
    descripcion VARCHAR(500),
    fecha_subida DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (evento_id) REFERENCES eventos(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_evento (evento_id),
    INDEX idx_usuario (usuario_id),
    INDEX idx_fecha_subida (fecha_subida)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Tabla: resenas
-- =====================================================
CREATE TABLE IF NOT EXISTS resenas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    evento_id INT NOT NULL,
    usuario_id INT NOT NULL,
    calificacion INT NOT NULL CHECK (calificacion BETWEEN 1 AND 5),
    comentario TEXT,
    fecha_resena DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (evento_id) REFERENCES eventos(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_event (evento_id, usuario_id),
    INDEX idx_evento (evento_id),
    INDEX idx_calificacion (calificacion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Insertar datos de ejemplo: Categorías
-- =====================================================
INSERT INTO categorias (id, nombre, descripcion, icono) VALUES
(1, 'Conciertos', 'Eventos musicales y conciertos en vivo', '🎸'),
(2, 'Conferencias', 'Charlas, seminarios y conferencias', '🎤'),
(3, 'Deportes', 'Eventos deportivos y competencias', '🏆'),
(4, 'Talleres', 'Talleres educativos y capacitaciones', '🛠️'),
(5, 'Fiestas', 'Eventos sociales y celebraciones', '🎉'),
(6, 'Gastronómico', 'Eventos culinarios y degustaciones', '🍕');

-- =====================================================
-- Insertar datos de ejemplo: Usuario de prueba
-- =====================================================
-- Password: test123 (bcrypt hash)
INSERT INTO usuarios (nombre, apellido, email, password) VALUES
('Admin', 'EventSphere', 'admin@eventsphere.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Juan', 'Pérez', 'juan@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('María', 'García', 'maria@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- =====================================================
-- Insertar eventos de ejemplo
-- =====================================================
INSERT INTO eventos (titulo, descripcion, categoria_id, organizador_id, fecha_evento, ubicacion, direccion, capacidad_total, boletos_disponibles, precio_boleto, imagen_portada) VALUES
('Concierto de Rock 2025', 'El mejor concierto de rock del año con bandas internacionales', 1, 1, '2025-12-15 20:00:00', 'Estadio Nacional', 'Av. Principal #123', 5000, 5000, 50.00, 'default-event.jpg'),
('Conferencia Tech Summit', 'Conferencia sobre las últimas tecnologías y tendencias en desarrollo', 2, 1, '2025-11-20 09:00:00', 'Centro de Convenciones', 'Calle Tech #456', 500, 500, 25.00, 'default-event.jpg'),
('Torneo de Fútbol Amateur', 'Torneo local de fútbol amateur con equipos de la ciudad', 3, 2, '2025-11-30 15:00:00', 'Estadio Municipal', 'Av. Deportes #789', 2000, 2000, 10.00, 'default-event.jpg');

-- =====================================================
-- Usuario y privilegios
-- =====================================================
-- NOTA: Ejecuta estos comandos manualmente si tienes privilegios de root

-- CREATE USER IF NOT EXISTS 'eventsphere_user'@'localhost' IDENTIFIED BY 'juss07lems.';
-- GRANT ALL PRIVILEGES ON eventsphere_db.* TO 'eventsphere_user'@'localhost';
-- FLUSH PRIVILEGES;

-- =====================================================
-- Fin del script
-- =====================================================
