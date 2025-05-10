<?php
session_start();

// Inicializar el carrito si no existe
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

// Función para agregar un producto al carrito
function agregarAlCarrito($producto) {
    array_push($_SESSION['carrito'], $producto);
}

// Función para eliminar un producto del carrito
function eliminarDelCarrito($productoId) {
    foreach ($_SESSION['carrito'] as $key => $producto) {
        if ($producto['id'] == $productoId) {
            unset($_SESSION['carrito'][$key]);
            break;
        }
    }
}

// Función para obtener el contenido del carrito
function obtenerCarrito() {
    return $_SESSION['carrito'];
}

// Manejo de solicitudes
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['accion'])) {
        switch ($_POST['accion']) {
            case 'agregar':
                agregarAlCarrito($_POST['producto']);
                break;
            case 'eliminar':
                eliminarDelCarrito($_POST['productoId']);
                break;
        }
    }
}

// Mostrar el contenido del carrito
$carrito = obtenerCarrito();
?>