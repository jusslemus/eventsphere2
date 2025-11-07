<?php include_once '../config/cors.php'; ?>
<?php
require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$method = $_SERVER['REQUEST_METHOD'];

// GET - Obtener mensajes de un evento
if ($method === 'GET') {
    $evento_id = isset($_GET['evento_id']) ? intval($_GET['evento_id']) : 0;
    $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 50;
    
    if ($evento_id == 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'ID de evento requerido']);
        exit();
    }

    try {
        // CORREGIDO: Usar comunidad_id y fecha_envio
        $query = "SELECT mc.*, u.nombre, u.email 
                  FROM mensajes_comunidad mc
                  INNER JOIN usuarios u ON mc.usuario_id = u.id
                  WHERE mc.comunidad_id = :evento_id
                  ORDER BY mc.fecha_envio DESC
                  LIMIT :limit";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(':evento_id', $evento_id);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        $mensajes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Invertir para mostrar del más antiguo al más reciente
        $mensajes = array_reverse($mensajes);

        http_response_code(200);
        echo json_encode([
            'success' => true,
            'mensajes' => $mensajes
        ]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
}

// POST - Enviar mensaje
elseif ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"));
    
    if (!isset($data->evento_id) || !isset($data->usuario_id) || !isset($data->mensaje)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
        exit();
    }

    try {
        // Verificar que el usuario tenga un boleto para este evento
        $query_boleto = "SELECT COUNT(*) as tiene_boleto 
                         FROM boletos 
                         WHERE evento_id = :evento_id AND usuario_id = :usuario_id";
        $stmt_boleto = $db->prepare($query_boleto);
        $stmt_boleto->bindParam(':evento_id', $data->evento_id);
        $stmt_boleto->bindParam(':usuario_id', $data->usuario_id);
        $stmt_boleto->execute();
        $result = $stmt_boleto->fetch(PDO::FETCH_ASSOC);

        if ($result['tiene_boleto'] == 0) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Debes tener un boleto para chatear']);
            exit();
        }

        // CORREGIDO: Usar comunidad_id en lugar de evento_id
        $query = "INSERT INTO mensajes_comunidad (comunidad_id, usuario_id, mensaje) 
                  VALUES (:evento_id, :usuario_id, :mensaje)";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(':evento_id', $data->evento_id);
        $stmt->bindParam(':usuario_id', $data->usuario_id);
        $stmt->bindParam(':mensaje', $data->mensaje);
        
        if ($stmt->execute()) {
            // Obtener el mensaje recién creado con info del usuario
            $mensaje_id = $db->lastInsertId();
            $query_msg = "SELECT mc.*, u.nombre, u.email 
                          FROM mensajes_comunidad mc
                          INNER JOIN usuarios u ON mc.usuario_id = u.id
                          WHERE mc.id = :id";
            $stmt_msg = $db->prepare($query_msg);
            $stmt_msg->bindParam(':id', $mensaje_id);
            $stmt_msg->execute();
            $mensaje = $stmt_msg->fetch(PDO::FETCH_ASSOC);

            http_response_code(201);
            echo json_encode([
                'success' => true,
                'message' => 'Mensaje enviado',
                'data' => $mensaje
            ]);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
}

else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}
?>
