<?php include_once '../config/cors.php'; ?>
<?php include_once __DIR__ . '/../config/cors.php'; ?>
<?php
require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

// Obtener datos JSON
$data = json_decode(file_get_contents("php://input"));

$codigo = isset($data->codigo) ? trim($data->codigo) : '';
$accion = isset($data->accion) ? trim($data->accion) : 'consultar'; // consultar o marcar_usado
$validador_id = isset($data->validador_id) ? intval($data->validador_id) : 0;

if (empty($codigo)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Código de boleto requerido'
    ]);
    exit();
}

try {
    // Buscar boleto
    $query = "SELECT 
                b.*,
                e.titulo as evento_titulo,
                e.fecha_evento,
                e.ubicacion,
                e.organizador_id,
                CONCAT(u.nombre, ' ', u.apellido) as usuario_nombre,
                u.email as usuario_email
              FROM boletos b
              INNER JOIN eventos e ON b.evento_id = e.id
              INNER JOIN usuarios u ON b.usuario_id = u.id
              WHERE b.codigo_unico = :codigo";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':codigo', $codigo);
    $stmt->execute();
    
    $boleto = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$boleto) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Boleto no encontrado'
        ]);
        exit();
    }

    // Si la acción es marcar como usado
    if ($accion === 'marcar_usado') {
        if ($boleto['estado_boleto'] === 'usado') {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Este boleto ya fue usado',
                'boleto' => $boleto
            ]);
            exit();
        }

        // Marcar boleto como usado
        $queryUpdate = "UPDATE boletos 
                       SET estado_boleto = 'usado', 
                           fecha_validacion = NOW(),
                           validado_por = :validador_id
                       WHERE id = :boleto_id";
        $stmtUpdate = $db->prepare($queryUpdate);
        $stmtUpdate->bindParam(':validador_id', $validador_id);
        $stmtUpdate->bindParam(':boleto_id', $boleto['id']);
        $stmtUpdate->execute();

        $boleto['estado_boleto'] = 'usado';
        $boleto['fecha_validacion'] = date('Y-m-d H:i:s');
    }

    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => $accion === 'marcar_usado' ? 'Boleto validado exitosamente' : 'Boleto encontrado',
        'boleto' => $boleto
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>
