<?php
require_once '../config/cors.php';
require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$data = json_decode(file_get_contents("php://input"));

if (!isset($data->email) || !isset($data->password)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Email y contraseña son requeridos'
    ]);
    exit();
}

$email = trim($data->email);
$password = $data->password;

try {
    $query = "SELECT id, nombre, apellido, email, password, foto_perfil, estado 
              FROM usuarios 
              WHERE email = :email 
              LIMIT 1";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($usuario['estado'] !== 'activo') {
            http_response_code(403);
            echo json_encode([
                'success' => false,
                'message' => 'Cuenta inactiva'
            ]);
            exit();
        }
        
        if (password_verify($password, $usuario['password'])) {
            $token = bin2hex(random_bytes(32));
            unset($usuario['password']);
            
            http_response_code(200);
            echo json_encode([
                'success' => true,
                'message' => 'Login exitoso',
                'token' => $token,
                'usuario' => $usuario
            ]);
        } else {
            http_response_code(401);
            echo json_encode([
                'success' => false,
                'message' => 'Contraseña incorrecta'
            ]);
        }
    } else {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Usuario no encontrado'
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
