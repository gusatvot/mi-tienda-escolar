<?php
// admin/eliminar-producto.php - Eliminar producto

require_once 'protegido.php';
require_once '../php/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    die("ID inválido.");
}

try {
    $stmt = $pdo->prepare("DELETE FROM productos WHERE id = ?");
    $stmt->execute([$id]);

    header("Location: gestion-productos.php");
    exit();
} catch (PDOException $e) {
    die("Error al eliminar el producto: " . $e->getMessage());
}
?>