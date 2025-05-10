<?php
// php/productos-lista.php - Devuelve productos con categoría y marca

require_once 'db.php';

try {
    $stmt = $pdo->query("
        SELECT 
            p.*, 
            c.nombre AS categoria_nombre,
            m.nombre AS marca_nombre
        FROM productos p
        LEFT JOIN categorias c ON p.categoria_id = c.id
        LEFT JOIN marcas m ON p.marca_id = m.id
        ORDER BY p.nombre ASC
    ");
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    header("Content-Type: application/json");
    echo json_encode($productos);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "No se pudieron cargar los productos"]);
}
?>