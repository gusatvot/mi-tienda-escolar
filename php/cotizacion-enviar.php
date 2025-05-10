<?php
// php/cotizacion-enviar.php - Guardar cotización con código de producto

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

session_start();
require_once 'db.php';
require_once 'protegido.php'; // Verifica si el usuario está logueado
require_once 'config-email.php'; // Configuración de correo

if (empty($_SESSION['carrito'])) {
    die("El carrito está vacío.");
}

try {
    // Iniciar transacción
    $pdo->beginTransaction();

    // Guardar cotización
    $stmt = $pdo->prepare("INSERT INTO cotizaciones (usuario_id) VALUES (?)");
    $stmt->execute([$_SESSION['usuario']['id']]);
    $cotizacion_id = $pdo->lastInsertId();

    // Guardar detalles de los productos
    foreach ($_SESSION['carrito'] as $producto_id => $item) {
        $subtotal = $item['precio_minorista'] * $item['cantidad'];

        $stmt = $pdo->prepare("
            INSERT INTO cotizaciones_detalle 
            (cotizacion_id, producto_id, nombre_producto, codigo_producto, cantidad, precio_unitario, subtotal) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $cotizacion_id,
            $producto_id,
            $item['nombre'],
            $item['codigo'] ?? null, // Si el producto tiene código, lo guardamos
            $item['cantidad'],
            $item['precio_minorista'],
            $subtotal
        ]);
    }

    // Confirmar transacción
    $pdo->commit();

    // Limpiar carrito
    unset($_SESSION['carrito']);

    // Datos del cliente
    $cliente = $_SESSION['usuario'];
    $email_cliente = $cliente['email'];
    $nombre_cliente = $cliente['nombre'];

    // Preparar mensaje HTML
    $mensaje_html = '
    <html>
    <head>
      <style>
        body { font-family: Arial, sans-serif; }
        h2 { color: #004080; }
        .detalle { background-color: #f9f9f9; padding: 15px; border-radius: 5px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        th { background-color: #f4f4f4; }
      </style>
    </head>
    <body>
      <h2>Nueva Cotización</h2>
      <p><strong>Cliente:</strong> ' . $nombre_cliente . '</p>
      <p><strong>Email:</strong> ' . $email_cliente . '</p>
      <p><strong>ID de Cotización:</strong> ' . $cotizacion_id . '</p>
      <p><strong>Fecha:</strong> ' . date('d/m/Y H:i') . '</p>

      <h3>Productos Solicitados</h3>
      <table>
        <tr>
          <th>Producto</th>
          <th>Cantidad</th>
          <th>Precio Unitario</th>
          <th>Total</th>
        </tr>
    ';

    $total = 0;
    foreach ($_SESSION['carrito'] as $item) {
        $subtotal = $item['precio_minorista'] * $item['cantidad'];
        $total += $subtotal;

        $mensaje_html .= "
        <tr>
          <td>{$item['nombre']}</td>
          <td>{$item['cantidad']}</td>
          <td>\$ {$item['precio_minorista']}</td>
          <td>\$ " . number_format($subtotal, 2) . "</td>
        </tr>";
    }

    $mensaje_html .= '
        <tr>
          <td colspan="3" style="text-align:right;"><strong>Total Estimado:</strong></td>
          <td><strong>\$ ' . number_format($total, 2) . '</strong></td>
        </tr>
      </table>

      <div class="detalle">
        Accede al panel de administración para responder esta solicitud.
      </div>
    </body>
    </html>
    ';

    // Enviar correo usando PHPMailer
    require 'PHPMailer.php';
    require 'SMTP.php';
    require 'Exception.php';

    $mail = new PHPMailer\PHPMailer\PHPMailer(true);

    try {
        // Configuración del servidor SMTP
        $mail->isSMTP();
        $mail->Host       = EMAIL_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = EMAIL_USER;
        $mail->Password   = EMAIL_PASS;
        $mail->SMTPSecure = 'tls';
        $mail->Port       = EMAIL_PORT;

        // Quién envía
        $mail->setFrom(EMAIL_FROM, EMAIL_NAME);
        $mail->addAddress(EMAIL_ADMIN);

        // Contenido
        $mail->isHTML(true);
        $mail->Subject = "Nueva cotización #{$cotizacion_id} - {$nombre_cliente}";
        $mail->Body = $mensaje_html;

        // Enviar
        $mail->send();

        // Redirigir a confirmación
        header("Location: ../cotizacion-confirmacion.html");
        exit();

    } catch (Exception $e) {
        throw new Exception("No se pudo enviar el correo. Error: {$mail->ErrorInfo}");
    }

} catch (Exception $e) {
    $pdo->rollBack();
    die("Error al procesar la cotización: " . $e->getMessage());
}
?>