<?php
// admin/responder-cotizacion.php - Enviar respuesta a cliente

require_once 'protegido.php';
require_once '../php/db.php';
require_once '../php/config-email.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cotizacion_id = (int)$_POST['cotizacion_id'];
    $cliente_email = filter_var($_POST['cliente_email'], FILTER_VALIDATE_EMAIL);
    $asunto = htmlspecialchars($_POST['asunto']);
    $mensaje = nl2br(htmlspecialchars($_POST['mensaje'])); // Convertir saltos de línea a <br>
    $fecha = date('d/m/Y H:i');

    if (!$cliente_email || !$cotizacion_id) {
        die("Datos inválidos.");
    }

    try {
        // Actualizar estado de la cotización a "respondida"
        $stmt = $pdo->prepare("UPDATE cotizaciones SET estado = 'respondida' WHERE id = ?");
        $stmt->execute([$cotizacion_id]);

        // Diseñar mensaje HTML
        $mensaje_html = '
        <html>
        <head>
          <style>
            body { font-family: Arial, sans-serif; }
            h2 { color: #004080; }
            .detalle { background-color: #f9f9f9; padding: 15px; border-radius: 5px; }
          </style>
        </head>
        <body>
          <h2>Respuesta a tu cotización #'. $cotizacion_id .'</h2>
          <p>Hola '. $cotizacion_id .',<br><br>'. $mensaje .'</p>

          <div class="detalle">
            <strong>Fecha:</strong> '. $fecha .'<br>
            <strong>Atentamente,</strong><br>
            El equipo de Tienda Escolar S.A.
          </div>
        </body>
        </html>
        ';

        // Enviar correo usando PHPMailer
        require '../PHPMailer.php';
        require '../SMTP.php';
        require '../Exception.php';

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
            $mail->addAddress($cliente_email);

            // Contenido
            $mail->isHTML(true);
            $mail->Subject = $asunto;
            $mail->Body = $mensaje_html;

            // Enviar
            $mail->send();

            // Redirigir con éxito
            header("Location: gestion-cotizaciones.php");
            exit();

        } catch (Exception $e) {
            throw new Exception("No se pudo enviar el correo. Error: {$mail->ErrorInfo}");
        }

    } catch (Exception $e) {
        die("Error al responder la cotización: " . $e->getMessage());
    }
}
?>