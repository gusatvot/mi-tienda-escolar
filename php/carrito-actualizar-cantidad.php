<?php
// php/carrito-actualizar-cantidad.php - Actualizar la cantidad de un producto en el carrito

session_start();

if (!isset($_SESSION['carrito']) || !is_array($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

// Verificar si se recibió el ID del producto y la cantidad
if (isset($_POST['id']) && isset($_POST['cantidad'])) {
    $producto_id = (int)$_POST['id'];
    $cantidad = (int)$_POST['cantidad'];
    
    // La cantidad debe ser al menos 1
    if ($cantidad < 1) {
        $cantidad = 1;
    }
    
    // Actualizar cantidad si el producto existe en el carrito
    if (isset($_SESSION['carrito'][$producto_id])) {
        $_SESSION['carrito'][$producto_id]['cantidad'] = $cantidad;
        
        // Calcular subtotal
        $subtotal = $_SESSION['carrito'][$producto_id]['precio_minorista'] * $cantidad;
        
        // Responder con un JSON
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'mensaje' => 'Cantidad actualizada',
            'cantidad' => $cantidad,
            'subtotal' => $subtotal
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
    // No se recibieron los parámetros necesarios
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'mensaje' => 'Faltan parámetros'
    ]);
}
?>