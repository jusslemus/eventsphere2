<?php
require_once 'config/cors.php';
require_once 'config/database.php';

// Test de conexión a la base de datos
$database = new Database();
$db = $database->getConnection();

if ($db) {
    echo json_encode([
        'success' => true,
        'message' => '✅ Conexión exitosa a la base de datos',
        'api_status' => 'OK',
        'timestamp' => date('Y-m-d H:i:s')
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => '❌ Error de conexión a la base de datos'
    ]);
}
?>
