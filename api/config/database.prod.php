<?php
// EventSphere - Configuración de Base de Datos PRODUCCIÓN

class Database {
    // IMPORTANTE: Cambiar estos valores con los datos de tu servidor
    private $host = "localhost";  // o la IP de tu servidor MySQL
    private $db_name = "eventsphere_db";
    private $username = "tu_usuario_mysql";  // Cambiar por tu usuario
    private $password = "tu_password_mysql";  // Cambiar por tu contraseña
    private $charset = "utf8mb4";
    public $conn;

    // Obtener conexión a la base de datos
    public function getConnection() {
        $this->conn = null;

        try {
            $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=" . $this->charset;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            
            $this->conn = new PDO($dsn, $this->username, $this->password, $options);
        } catch(PDOException $exception) {
            // En producción, NO mostrar el mensaje de error detallado
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Error de conexión a la base de datos'
            ]);
            exit();
        }

        return $this->conn;
    }
}

// Headers CORS
header('Access-Control-Allow-Origin: https://kathyap.ddns.net');  // ✅ Tu dominio específico
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json; charset=UTF-8');

// Manejar preflight OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}
?>
