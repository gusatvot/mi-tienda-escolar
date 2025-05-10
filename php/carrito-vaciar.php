<?php
// php/carrito-vaciar.php - Vaciar completamente el carrito

session_start();

// Vaciar el carrito
$_SESSION['carrito'] = [];

// Responder con un JSON
header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'mensaje' => 'Carrito vaciado'
]);
?>