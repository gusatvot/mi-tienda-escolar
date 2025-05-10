<?php
// admin/eliminar-categoria.php - Eliminar categoría y desvincular productos

require_once 'protegido.php';
require_once '../php/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    die("ID inválido.");
}

try {
    // Verificar si hay productos con esta categoría
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM productos WHERE categoria_id = ?");
    $stmt->execute([$id]);
    $count = $stmt->fetchColumn();

    if ($count > 0) {
        // Desvincular productos antes de eliminar la categoría
        $stmt = $pdo->prepare("UPDATE productos SET categoria_id = NULL WHERE categoria_id = ?");
        $stmt->execute([$id]);
    }

    // Eliminar la categoría
    $stmt = $pdo->prepare("DELETE FROM categorias WHERE id = ?");
    $stmt->execute([$id]);

    header("Location: gestion-categorias.php");
    exit();
} catch (PDOException $e) {
    die("Error al eliminar la categoría: " . $e->getMessage());
}
?>