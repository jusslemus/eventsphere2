<?php
require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$method = $_SERVER['REQUEST_METHOD'];

if ($method !== 'DELETE' && $method !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit();
}

$data = json_decode(file_get_contents("php://input"));

if (!isset($data->evento_id) || !isset($data->usuario_id)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
    exit();
}

try {
    // Verificar que el usuario sea el organizador
    $query_verify = "SELECT organizador_id FROM eventos WHERE id = :evento_id";
    $stmt_verify = $db->prepare($query_verify);
    $stmt_verify->bindParam(':evento_id', $data->evento_id);
    $stmt_verify->execute();
    $evento = $stmt_verify->fetch(PDO::FETCH_ASSOC);

    if (!$evento) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Evento no encontrado']);
        exit();
    }

    if ($evento['organizador_id'] != $data->usuario_id) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'No tienes permiso para eliminar este evento']);
        exit();
    }

    // Eliminar el evento (CASCADE eliminará boletos, reseñas, etc.)
    $query_delete = "DELETE FROM eventos WHERE id = :evento_id";
    $stmt_delete = $db->prepare($query_delete);
    $stmt_delete->bindParam(':evento_id', $data->evento_id);

    if ($stmt_delete->execute()) {
        http_response_code(200);
        echo json_encode(['success' => true, 'message' => 'Evento eliminado exitosamente']);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
