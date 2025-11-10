<?php
// EventSphere - Configuración de Base de Datos

class Database {
    private $host = "localhost";
    private $db_name = "eventsphere_db";
    private $username = "root";
    private $password = "";  // XAMPP por defecto no tiene password para root
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
            echo json_encode([
                'success' => false,
                'message' => 'Error de conexión: ' . $exception->getMessage()
            ]);
            exit();
        }

        return $this->conn;
    }
}
?>