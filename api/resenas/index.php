<?php include_once '../config/cors.php'; ?>
<?php
require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$method = $_SERVER['REQUEST_METHOD'];

// GET - Obtener reseñas de un evento
if ($method === 'GET') {
    $evento_id = isset($_GET['evento_id']) ? intval($_GET['evento_id']) : 0;
    
    if ($evento_id == 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'ID de evento requerido']);
        exit();
    }

    try {
        // Obtener reseñas con info del usuario
        $query = "SELECT r.*, u.nombre, u.email 
                  FROM resenas r
                  INNER JOIN usuarios u ON r.usuario_id = u.id
                  WHERE r.evento_id = :evento_id
                  ORDER BY r.fecha_resena DESC";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(':evento_id', $evento_id);
        $stmt->execute();
        $resenas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Calcular promedio
        $query_avg = "SELECT AVG(calificacion) as promedio, COUNT(*) as total 
                      FROM resenas 
                      WHERE evento_id = :evento_id";
        $stmt_avg = $db->prepare($query_avg);
        $stmt_avg->bindParam(':evento_id', $evento_id);
        $stmt_avg->execute();
        $stats = $stmt_avg->fetch(PDO::FETCH_ASSOC);

        http_response_code(200);
        echo json_encode([
            'success' => true,
            'resenas' => $resenas,
            'promedio' => round($stats['promedio'], 1),
            'total' => $stats['total']
        ]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
}

// POST - Crear nueva reseña
elseif ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"));
    
    if (!isset($data->evento_id) || !isset($data->usuario_id) || !isset($data->calificacion)) {
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
            echo json_encode(['success' => false, 'message' => 'Debes tener un boleto para dejar una reseña']);
            exit();
        }

        // Verificar si ya dejó una reseña
        $query_existe = "SELECT COUNT(*) as existe 
                         FROM resenas 
                         WHERE evento_id = :evento_id AND usuario_id = :usuario_id";
        $stmt_existe = $db->prepare($query_existe);
        $stmt_existe->bindParam(':evento_id', $data->evento_id);
        $stmt_existe->bindParam(':usuario_id', $data->usuario_id);
        $stmt_existe->execute();
        $existe = $stmt_existe->fetch(PDO::FETCH_ASSOC);

        if ($existe['existe'] > 0) {
            // Actualizar reseña existente
            $query = "UPDATE resenas 
                      SET calificacion = :calificacion, comentario = :comentario, fecha_resena = NOW()
                      WHERE evento_id = :evento_id AND usuario_id = :usuario_id";
        } else {
            // Insertar nueva reseña
            $query = "INSERT INTO resenas (evento_id, usuario_id, calificacion, comentario) 
                      VALUES (:evento_id, :usuario_id, :calificacion, :comentario)";
        }

        $stmt = $db->prepare($query);
        $stmt->bindParam(':evento_id', $data->evento_id);
        $stmt->bindParam(':usuario_id', $data->usuario_id);
        $stmt->bindParam(':calificacion', $data->calificacion);
        $stmt->bindParam(':comentario', $data->comentario);
        
        if ($stmt->execute()) {
            http_response_code(201);
            echo json_encode(['success' => true, 'message' => 'Reseña guardada exitosamente']);
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
