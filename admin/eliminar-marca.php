<?php
// admin/eliminar-marca.php - Eliminar marca y desvincular productos

require_once 'protegido.php';
require_once '../php/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    die("ID inválido.");
}

try {
    // Verificar si hay productos con esta marca
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM productos WHERE marca_id = ?");
    $stmt->execute([$id]);
    $count = $stmt->fetchColumn();

    if ($count > 0) {
        // Desvincular productos antes de eliminar la marca
        $stmt = $pdo->prepare("UPDATE productos SET marca_id = NULL WHERE marca_id = ?");
        $stmt->execute([$id]);
    }

    // Eliminar la marca
    $stmt = $pdo->prepare("DELETE FROM marcas WHERE id = ?");
    $stmt->execute([$id]);

    header("Location: gestion-marcas.php");
    exit();
} catch (PDOException $e) {
    die("Error al eliminar la marca: " . $e->getMessage());
}
?>