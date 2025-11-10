<?php include_once '../config/cors.php'; ?>
<?php
require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$method = $_SERVER['REQUEST_METHOD'];

// GET - Obtener fotos de un evento
if ($method === 'GET') {
    $evento_id = isset($_GET['evento_id']) ? intval($_GET['evento_id']) : 0;
    
    if ($evento_id == 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'ID de evento requerido']);
        exit();
    }

    try {
        $query = "SELECT f.*, u.nombre, u.email 
                  FROM fotos_evento f
                  INNER JOIN usuarios u ON f.usuario_id = u.id
                  WHERE f.evento_id = :evento_id
                  ORDER BY f.fecha_subida DESC";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(':evento_id', $evento_id);
        $stmt->execute();
        $fotos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        http_response_code(200);
        echo json_encode([
            'success' => true,
            'fotos' => $fotos
        ]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
}

// POST - Subir foto
elseif ($method === 'POST') {
    // Verificar que se envió un archivo
    if (!isset($_FILES['foto'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'No se envió ninguna foto']);
        exit();
    }

    $evento_id = isset($_POST['evento_id']) ? intval($_POST['evento_id']) : 0;
    $usuario_id = isset($_POST['usuario_id']) ? intval($_POST['usuario_id']) : 0;
    $descripcion = isset($_POST['descripcion']) ? $_POST['descripcion'] : '';

    if ($evento_id == 0 || $usuario_id == 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
        exit();
    }

    try {
        // Verificar que el usuario tenga un boleto
        $query_boleto = "SELECT COUNT(*) as tiene_boleto 
                         FROM boletos 
                         WHERE evento_id = :evento_id AND usuario_id = :usuario_id";
        $stmt_boleto = $db->prepare($query_boleto);
        $stmt_boleto->bindParam(':evento_id', $evento_id);
        $stmt_boleto->bindParam(':usuario_id', $usuario_id);
        $stmt_boleto->execute();
        $result = $stmt_boleto->fetch(PDO::FETCH_ASSOC);

        if ($result['tiene_boleto'] == 0) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Debes tener un boleto para subir fotos']);
            exit();
        }

        // Procesar archivo
        $file = $_FILES['foto'];
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $file['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Formato de archivo no permitido']);
            exit();
        }

        // Crear nombre único
        $new_filename = 'foto_' . $evento_id . '_' . $usuario_id . '_' . time() . '.' . $ext;
        $upload_dir = '../../uploads/fotos/';
        
        // Crear directorio si no existe
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        $upload_path = $upload_dir . $new_filename;

        if (move_uploaded_file($file['tmp_name'], $upload_path)) {
            // Guardar en BD
            $ruta_archivo = 'fotos/' . $new_filename;
            
            $query = "INSERT INTO fotos_evento (evento_id, usuario_id, ruta_archivo, descripcion) 
                      VALUES (:evento_id, :usuario_id, :ruta_archivo, :descripcion)";
            
            $stmt = $db->prepare($query);
            $stmt->bindParam(':evento_id', $evento_id);
            $stmt->bindParam(':usuario_id', $usuario_id);
            $stmt->bindParam(':ruta_archivo', $ruta_archivo);
            $stmt->bindParam(':descripcion', $descripcion);
            
            if ($stmt->execute()) {
                http_response_code(201);
                echo json_encode([
                    'success' => true,
                    'message' => 'Foto subida exitosamente',
                    'ruta' => $ruta_archivo
                ]);
            }
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error al subir el archivo']);
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
