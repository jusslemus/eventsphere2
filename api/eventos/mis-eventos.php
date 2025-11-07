<?php include_once '../config/cors.php'; ?>
<?php include_once __DIR__ . '/../config/cors.php'; ?>
<?php
require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

// Obtener organizador_id del parámetro GET
$organizador_id = isset($_GET['organizador_id']) ? intval($_GET['organizador_id']) : 0;

if ($organizador_id == 0) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'ID de organizador no válido'
    ]);
    exit();
}

try {
    $query = "SELECT e.*, c.nombre as categoria,
              CONCAT(u.nombre, ' ', u.apellido) as organizador,
              (SELECT COUNT(*) FROM boletos WHERE evento_id = e.id) as total_boletos_vendidos,
              (SELECT SUM(precio_total) FROM compras WHERE evento_id = e.id) as ingresos_totales
              FROM eventos e
              LEFT JOIN categorias c ON e.categoria_id = c.id
              LEFT JOIN usuarios u ON e.organizador_id = u.id
              WHERE e.organizador_id = :organizador_id
              ORDER BY e.fecha_evento DESC";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':organizador_id', $organizador_id);
    $stmt->execute();
    
    $eventos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Calcular estadísticas totales
    $total_eventos = count($eventos);
    $total_boletos = 0;
    $total_ingresos = 0;
    
    foreach ($eventos as $evento) {
        $total_boletos += intval($evento['total_boletos_vendidos']);
        $total_ingresos += floatval($evento['ingresos_totales']);
    }
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'eventos' => $eventos,
        'estadisticas' => [
            'total_eventos' => $total_eventos,
            'total_boletos' => $total_boletos,
            'total_ingresos' => $total_ingresos
        ]
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>
