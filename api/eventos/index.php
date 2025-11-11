<?php
require_once '../config/cors.php';
require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$search = isset($_GET['search']) ? $_GET['search'] : '';
$categoria = isset($_GET['categoria']) ? $_GET['categoria'] : '';
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 100;

try {
    $query = "SELECT e.*, c.nombre as categoria_nombre, c.id as categoria_id,
              CONCAT(u.nombre, ' ', u.apellido) as organizador
              FROM eventos e
              LEFT JOIN categorias c ON e.categoria_id = c.id
              LEFT JOIN usuarios u ON e.organizador_id = u.id
              WHERE e.estado_evento = 'activo'";
    
    if (!empty($search)) {
        $query .= " AND (e.titulo LIKE :search OR e.descripcion LIKE :search OR e.ubicacion LIKE :search)";
    }
    
    if (!empty($categoria)) {
        $query .= " AND e.categoria_id = :categoria";
    }
    
    $query .= " ORDER BY e.fecha_evento ASC LIMIT :limit";
    
    $stmt = $db->prepare($query);
    
    if (!empty($search)) {
        $searchParam = "%{$search}%";
        $stmt->bindParam(':search', $searchParam);
    }
    
    if (!empty($categoria)) {
        $stmt->bindParam(':categoria', $categoria);
    }
    
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    
    $eventos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'eventos' => $eventos,
        'total' => count($eventos)
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>
