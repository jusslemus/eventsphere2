<?php include_once '../config/cors.php'; ?>
<?php include_once __DIR__ . '/../config/cors.php'; ?>
<?php
require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

// Obtener datos del formulario
$titulo = isset($_POST['titulo']) ? trim($_POST['titulo']) : '';
$descripcion = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : '';
$categoria_id = isset($_POST['categoria_id']) ? intval($_POST['categoria_id']) : 0;
$organizador_id = isset($_POST['organizador_id']) ? intval($_POST['organizador_id']) : 0;
$fecha_evento = isset($_POST['fecha_evento']) ? $_POST['fecha_evento'] : '';
$ubicacion = isset($_POST['ubicacion']) ? trim($_POST['ubicacion']) : '';
$direccion = isset($_POST['direccion']) ? trim($_POST['direccion']) : '';
$capacidad_total = isset($_POST['capacidad_total']) ? intval($_POST['capacidad_total']) : 0;
$precio_boleto = isset($_POST['precio_boleto']) ? floatval($_POST['precio_boleto']) : 0;

// Validaciones
if (empty($titulo) || empty($descripcion) || $categoria_id == 0 || $organizador_id == 0 ||
    empty($fecha_evento) || empty($ubicacion) || $capacidad_total == 0 || $precio_boleto == 0) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Todos los campos obligatorios deben estar completos'
    ]);
    exit();
}

// Manejar upload de imagen
$imagen_nombre = 'default-event.jpg';

if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
    $filename = $_FILES['imagen']['name'];
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

    if (in_array($ext, $allowed)) {
        if ($_FILES['imagen']['size'] <= 2097152) { // 2MB
            $imagen_nombre = uniqid() . '_' . time() . '.' . $ext;
            $upload_path = '../uploads/' . $imagen_nombre;

            if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $upload_path)) {
                $imagen_nombre = 'default-event.jpg';
            }
        } else {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'La imagen no debe superar 2MB'
            ]);
            exit();
        }
    } else {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Formato de imagen no permitido. Use JPG, PNG o GIF'
        ]);
        exit();
    }
}

try {
    $query = "INSERT INTO eventos
              (titulo, descripcion, categoria_id, organizador_id, fecha_evento, ubicacion, direccion,
               capacidad_total, boletos_disponibles, precio_boleto, imagen_portada)
              VALUES
              (:titulo, :descripcion, :categoria_id, :organizador_id, :fecha_evento, :ubicacion, :direccion,
               :capacidad_total, :boletos_disponibles, :precio_boleto, :imagen_portada)";

    $stmt = $db->prepare($query);
    $stmt->bindParam(':titulo', $titulo);
    $stmt->bindParam(':descripcion', $descripcion);
    $stmt->bindParam(':categoria_id', $categoria_id);
    $stmt->bindParam(':organizador_id', $organizador_id);
    $stmt->bindParam(':fecha_evento', $fecha_evento);
    $stmt->bindParam(':ubicacion', $ubicacion);
    $stmt->bindParam(':direccion', $direccion);
    $stmt->bindParam(':capacidad_total', $capacidad_total, PDO::PARAM_INT);
    $boletos_disponibles = $capacidad_total;
    $stmt->bindParam(':boletos_disponibles', $boletos_disponibles, PDO::PARAM_INT);
    $stmt->bindParam(':precio_boleto', $precio_boleto);
    $stmt->bindParam(':imagen_portada', $imagen_nombre);

    if ($stmt->execute()) {
        $evento_id = $db->lastInsertId();

        // Crear comunidad automáticamente
        $queryComunidad = "INSERT INTO comunidades (evento_id, nombre, descripcion)
                          VALUES (:evento_id, :nombre, :descripcion)";
        $stmtCom = $db->prepare($queryComunidad);
        $nombreCom = "Comunidad " . $titulo;
        $descCom = "Espacio de discusión para " . $titulo;
        $stmtCom->bindParam(':evento_id', $evento_id);
        $stmtCom->bindParam(':nombre', $nombreCom);
        $stmtCom->bindParam(':descripcion', $descCom);
        $stmtCom->execute();

        http_response_code(201);
        echo json_encode([
            'success' => true,
            'message' => 'Evento creado exitosamente',
            'evento_id' => $evento_id
        ]);
    } else {
        throw new Exception('Error al crear evento');
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>
