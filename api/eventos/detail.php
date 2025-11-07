<?php include_once '../config/cors.php'; ?>
<?php include_once __DIR__ . '/../config/cors.php'; ?>
<?php
require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

// Obtener ID del evento
$evento_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($evento_id == 0) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'ID de evento no válido'
    ]);
    exit();
}

try {
    $query = "SELECT e.*, c.nombre as categoria
              FROM eventos e
              LEFT JOIN categorias c ON e.categoria_id = c.id
              WHERE e.id = :evento_id";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':evento_id', $evento_id);
    $stmt->execute();
    
    $evento = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($evento) {
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'evento' => $evento
        ]);
    } else {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Evento no encontrado'
        ]);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>
