<?php
// php/carrito-quitar.php - Quitar producto del carrito

session_start();

if (isset($_GET['id'])) {
    $producto_id = (int)$_GET['id'];

    if (isset($_SESSION['carrito'][$producto_id])) {
        unset($_SESSION['carrito'][$producto_id]);

        // Si el carrito queda vacío, pasamos un parámetro especial
        if (empty($_SESSION['carrito'])) {
            header("Location: ../carrito.html?vacio=1");
        } else {
            header("Location: ../carrito.html");
        }
        exit();
    }
}

header("Location: ../carrito.html");
exit();
?>