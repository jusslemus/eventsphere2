<?php
require_once '../config/cors.php';
require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$data = json_decode(file_get_contents("php://input"));

if (!isset($data->nombre) || !isset($data->apellido) || !isset($data->email) || !isset($data->password)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Todos los campos son requeridos'
    ]);
    exit();
}

$nombre = trim($data->nombre);
$apellido = trim($data->apellido);
$email = trim($data->email);
$password = $data->password;

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Email inválido'
    ]);
    exit();
}

if (strlen($password) < 6) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'La contraseña debe tener al menos 6 caracteres'
    ]);
    exit();
}

try {
    $queryCheck = "SELECT id FROM usuarios WHERE email = :email";
    $stmtCheck = $db->prepare($queryCheck);
    $stmtCheck->bindParam(':email', $email);
    $stmtCheck->execute();
    
    if ($stmtCheck->rowCount() > 0) {
        http_response_code(409);
        echo json_encode([
            'success' => false,
            'message' => 'Este email ya está registrado'
        ]);
        exit();
    }
    
    $password_hash = password_hash($password, PASSWORD_BCRYPT);
    
    $query = "INSERT INTO usuarios (nombre, apellido, email, password) 
              VALUES (:nombre, :apellido, :email, :password)";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':apellido', $apellido);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $password_hash);
    
    if ($stmt->execute()) {
        http_response_code(201);
        echo json_encode([
            'success' => true,
            'message' => 'Usuario registrado exitosamente',
            'usuario_id' => $db->lastInsertId()
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
