<?php
// php/carrito-actualizar.php - Devuelve cantidad de productos en carrito

session_start();

$contador = 0;

if (isset($_SESSION['carrito']) && is_array($_SESSION['carrito'])) {
    $contador = array_sum(array_map(function ($item) {
        return $item['cantidad'];
    }, $_SESSION['carrito']));
}

header("Content-Type: application/json");
echo json_encode(['cantidad' => $contador]);
?>