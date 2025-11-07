<?php include_once '../config/cors.php'; ?>
<?php include_once __DIR__ . '/../config/cors.php'; ?>
<?php
require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

// Obtener usuario_id del parámetro GET o POST
$usuario_id = 0;

if (isset($_GET['usuario_id'])) {
    $usuario_id = intval($_GET['usuario_id']);
} elseif (isset($_POST['usuario_id'])) {
    $usuario_id = intval($_POST['usuario_id']);
} else {
    // Intentar obtener del body JSON
    $data = json_decode(file_get_contents("php://input"), true);
    if (isset($data['usuario_id'])) {
        $usuario_id = intval($data['usuario_id']);
    }
}

if ($usuario_id == 0) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Usuario no especificado'
    ]);
    exit();
}

try {
    $query = "SELECT 
                b.id,
                b.codigo_unico,
                b.qr_hash,
                b.estado_boleto,
                b.fecha_emision,
                b.fecha_validacion,
                e.titulo as evento_titulo,
                e.fecha_evento,
                e.ubicacion,
                e.direccion,
                e.imagen_portada,
                c.cantidad_boletos,
                c.precio_total
              FROM boletos b
              INNER JOIN eventos e ON b.evento_id = e.id
              INNER JOIN compras c ON b.compra_id = c.id
              WHERE b.usuario_id = :usuario_id
              ORDER BY b.fecha_emision DESC";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':usuario_id', $usuario_id);
    $stmt->execute();
    
    $boletos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'boletos' => $boletos,
        'total' => count($boletos)
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>
