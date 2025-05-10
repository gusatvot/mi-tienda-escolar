<?php
// php/carrito-agregar.php - Agregar producto al carrito

session_start();

// Simular productos con IDs y precios
$productos = [
    1 => ['nombre' => 'Cuaderno ABC', 'precio_minorista' => 350, 'precio_mayorista' => 290],
    2 => ['nombre' => 'Mochila Escolar', 'precio_minorista' => 1800, 'precio_mayorista' => 1500],
    3 => ['nombre' => 'Juego de Lápices', 'precio_minorista' => 450, 'precio_mayorista' => 380],
];

if (isset($_GET['id'])) {
    $producto_id = (int)$_GET['id'];

    if (!isset($productos[$producto_id])) {
        die("Producto no encontrado.");
    }

    // Inicializar carrito si no existe
    if (!isset($_SESSION['carrito'])) {
        $_SESSION['carrito'] = [];
    }

    // Agregar o incrementar cantidad
    if (isset($_SESSION['carrito'][$producto_id])) {
        $_SESSION['carrito'][$producto_id]['cantidad']++;
    } else {
        $_SESSION['carrito'][$producto_id] = [
            'nombre' => $productos[$producto_id]['nombre'],
            'precio_minorista' => $productos[$producto_id]['precio_minorista'],
            'precio_mayorista' => $productos[$producto_id]['precio_mayorista'],
            'cantidad' => 1
        ];
    }

    // Redirigir a productos.html
    header("Location: ../productos.html");
    exit();
}
?>