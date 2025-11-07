<?php include_once '../config/cors.php'; ?>
<?php include_once __DIR__ . '/../config/cors.php'; ?>
<?php
require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

// Obtener datos JSON
$data = json_decode(file_get_contents("php://input"));

$usuario_id = isset($data->usuario_id) ? intval($data->usuario_id) : 0;
$evento_id = isset($data->evento_id) ? intval($data->evento_id) : 0;
$cantidad = isset($data->cantidad) ? intval($data->cantidad) : 0;
$metodo_pago = isset($data->metodo_pago) ? trim($data->metodo_pago) : '';

// Validaciones básicas
if ($usuario_id == 0 || $evento_id == 0 || $cantidad == 0 || empty($metodo_pago)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Datos incompletos'
    ]);
    exit();
}

try {
    // Verificar disponibilidad
    $queryEvento = "SELECT boletos_disponibles, precio_boleto FROM eventos WHERE id = :evento_id";
    $stmtEvento = $db->prepare($queryEvento);
    $stmtEvento->bindParam(':evento_id', $evento_id);
    $stmtEvento->execute();
    $evento = $stmtEvento->fetch(PDO::FETCH_ASSOC);

    if (!$evento) {
        throw new Exception('Evento no encontrado');
    }

   // Verificar organizador
    $queryOrganizador = "SELECT organizador_id FROM eventos WHERE id = :evento_id";
    $stmtOrg = $db->prepare($queryOrganizador);
    $stmtOrg->bindParam(':evento_id', $evento_id);
    $stmtOrg->execute();
    $eventoData = $stmtOrg->fetch(PDO::FETCH_ASSOC);
    
    if ($eventoData['organizador_id'] == $usuario_id) {
        throw new Exception('No puedes comprar boletos de tu propio evento');
    }

   // Verificar que el usuario no sea el organizador
    if ($evento['organizador_id'] == $usuario_id) {
        throw new Exception('No puedes comprar boletos de tu propio evento');
    }

    if ($evento['boletos_disponibles'] < $cantidad) {
        throw new Exception('No hay suficientes boletos disponibles');
    }

    // Calcular total
    $precio_total = $evento['precio_boleto'] * $cantidad;

    // Iniciar transacción
    $db->beginTransaction();

    // Crear compra (usando los nombres correctos de columnas)
    $queryCompra = "INSERT INTO compras (usuario_id, evento_id, cantidad_boletos, precio_total, metodo_pago, estado_compra)
                    VALUES (:usuario_id, :evento_id, :cantidad_boletos, :precio_total, :metodo_pago, 'completada')";
    $stmtCompra = $db->prepare($queryCompra);
    $stmtCompra->bindParam(':usuario_id', $usuario_id);
    $stmtCompra->bindParam(':evento_id', $evento_id);
    $stmtCompra->bindParam(':cantidad_boletos', $cantidad);
    $stmtCompra->bindParam(':precio_total', $precio_total);
    $stmtCompra->bindParam(':metodo_pago', $metodo_pago);
    $stmtCompra->execute();
    $compra_id = $db->lastInsertId();

    // Crear boletos individuales (usando los nombres correctos de columnas)
    for ($i = 0; $i < $cantidad; $i++) {
        $codigo_unico = 'BOL-' . strtoupper(substr(md5(uniqid(rand(), true)), 0, 10));
        $qr_hash = hash('sha256', $codigo_unico . time() . $i);
        
        $queryBoleto = "INSERT INTO boletos (compra_id, evento_id, usuario_id, codigo_unico, qr_hash, estado_boleto)
                        VALUES (:compra_id, :evento_id, :usuario_id, :codigo_unico, :qr_hash, 'activo')";
        $stmtBoleto = $db->prepare($queryBoleto);
        $stmtBoleto->bindParam(':compra_id', $compra_id);
        $stmtBoleto->bindParam(':evento_id', $evento_id);
        $stmtBoleto->bindParam(':usuario_id', $usuario_id);
        $stmtBoleto->bindParam(':codigo_unico', $codigo_unico);
        $stmtBoleto->bindParam(':qr_hash', $qr_hash);
        $stmtBoleto->execute();
    }

    // Actualizar boletos disponibles
    $nuevos_disponibles = $evento['boletos_disponibles'] - $cantidad;
    $queryUpdate = "UPDATE eventos SET boletos_disponibles = :boletos_disponibles WHERE id = :evento_id";
    $stmtUpdate = $db->prepare($queryUpdate);
    $stmtUpdate->bindParam(':boletos_disponibles', $nuevos_disponibles);
    $stmtUpdate->bindParam(':evento_id', $evento_id);
    $stmtUpdate->execute();

    // Confirmar transacción
    $db->commit();

    http_response_code(201);
    echo json_encode([
        'success' => true,
        'message' => 'Compra realizada exitosamente',
        'compra_id' => $compra_id
    ]);

} catch (Exception $e) {
    // Revertir transacción en caso de error
    if ($db->inTransaction()) {
        $db->rollBack();
    }

    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>
