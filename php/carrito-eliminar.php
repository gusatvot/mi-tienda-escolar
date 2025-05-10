<?php
// php/carrito-eliminar.php - Eliminar un producto del carrito

session_start();

if (!isset($_SESSION['carrito']) || !is_array($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

// Verificar si se recibió el ID del producto
if (isset($_POST['id'])) {
    $producto_id = (int)$_POST['id'];
    
    // Eliminar el producto si existe en el carrito
    if (isset($_SESSION['carrito'][$producto_id])) {
        unset($_SESSION['carrito'][$producto_id]);
        
        // Responder con un JSON
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'mensaje' => 'Producto eliminado del carrito',
            'num_productos' => count($_SESSION['carrito'])
        ]);
    } else {
        // El producto no existe en el carrito
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'mensaje' => 'Producto no encontrado en el carrito'
        ]);
    }
} else {
    // No se recibió el ID del producto
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'mensaje' => 'Falta el ID del producto'
    ]);
}
?>