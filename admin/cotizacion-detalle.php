<?php
// admin/cotizacion-detalle.php - Detalles de una cotización + responder

require_once '../php/admin/protegido.php';
require_once '../php/db.php';

$cotizacion_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($cotizacion_id <= 0) {
    die("ID de cotización inválido.");
}

try {
    // Obtener datos de la cotización
    $stmt = $pdo->prepare("
        SELECT c.id, u.nombre AS cliente, u.email, c.fecha 
        FROM cotizaciones c
        JOIN usuarios u ON c.usuario_id = u.id
        WHERE c.id = ?
    ");
    $stmt->execute([$cotizacion_id]);
    $cotizacion = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$cotizacion) {
        die("Cotización no encontrada.");
    }

    // Obtener detalles de los productos
    $stmt_detalle = $pdo->prepare("
        SELECT * FROM cotizaciones_detalle
        WHERE cotizacion_id = ?
    ");
    $stmt_detalle->execute([$cotizacion_id]);
    $detalles = $stmt_detalle->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al obtener los datos: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Detalles de Cotización #<?= $cotizacion['id'] ?></title>
  <link href="https://fonts.googleapis.com/css2?family=Roboto :wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../css/styles.css" />
</head>
<body>

  <!-- Encabezado -->
  <header>
    <div class="logo">
      <img src="../images/logo.png" alt="Logo de la Tienda" width="50">
      <h1>Panel de Administrador</h1>
    </div>

    <nav>
      <ul>
        <li><a href="gestion-cotizaciones.php">Volver</a></li>
        <li><a href="logout.php">Cerrar Sesión</a></li>
      </ul>
    </nav>
  </header>

  <!-- Contenido principal -->
  <main>
    <section class="contenido-pagina">
      <h2>Cotización #<?= $cotizacion['id'] ?></h2>
      <p><strong>Cliente:</strong> <?= $cotizacion['cliente'] ?></p>
      <p><strong>Email:</strong> <?= $cotizacion['email'] ?></p>
      <p><strong>Fecha:</strong> <?= date('d/m/Y H:i', strtotime($cotizacion['fecha'])) ?></p>

      <h3>Productos Solicitados</h3>

      <table border="1" cellpadding="10" cellspacing="0" width="100%">
        <tr>
          <th>Producto</th>
          <th>Cantidad</th>
          <th>Precio Unitario</th>
          <th>Total</th>
        </tr>

        <?php
        $total = 0;
        foreach ($detalles as $d) {
            $subtotal = $d['precio_unitario'] * $d['cantidad'];
            $total += $subtotal;
            echo "<tr>";
            echo "<td>{$d['nombre_producto']}</td>";
            echo "<td>{$d['cantidad']}</td>";
            echo "<td>\$ {$d['precio_unitario']}</td>";
            echo "<td>\$ " . number_format($subtotal, 2) . "</td>";
            echo "</tr>";
        }
        echo "<tr><td colspan='3' align='right'><strong>Total Estimado:</strong></td><td><strong>\$ " . number_format($total, 2) . "</strong></td></tr>";
        ?>
      </table>

      <h3>Responder Cotización</h3>

      <form action="responder-cotizacion.php" method="POST">
        <input type="hidden" name="cotizacion_id" value="<?= $cotizacion['id'] ?>">
        <input type="hidden" name="cliente_email" value="<?= $cotizacion['email'] ?>">

        <label for="asunto">Asunto:</label>
        <input type="text" id="asunto" name="asunto" required style="width:100%; padding:10px; margin-bottom:10px;">

        <label for="mensaje">Mensaje:</label>
        <textarea id="mensaje" name="mensaje" rows="6" required style="width:100%; padding:10px; margin-bottom:10px;"></textarea>

        <button type="submit" class="btn">Enviar Respuesta</button>
      </form>

    </section>
  </main>

  <!-- Pie de página -->
  <footer>
    <p>&copy; 2025 Tienda Escolar S.A. | Hecho con ayuda de un gran mentor 💡</p>
  </footer>

</body>
</html>