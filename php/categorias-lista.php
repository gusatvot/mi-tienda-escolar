<?php
// php/categorias-lista.php - Devuelve categorías en formato JSON

require_once 'db.php';

try {
    $stmt = $pdo->query("SELECT * FROM categorias ORDER BY nombre ASC");
    $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

    header("Content-Type: application/json");
    echo json_encode($categorias);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "No se pudieron cargar las categorías"]);
}
?>