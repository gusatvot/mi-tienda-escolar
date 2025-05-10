<?php
// admin/gestion-marcas.php - Panel de gestión de marcas

require_once 'protegido.php';
require_once '../php/db.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Gestión de Marcas - Panel Admin</title>
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
        <li><a href="gestion-cotizaciones.php">Cotizaciones</a></li>
        <li><a href="gestion-productos.php">Productos</a></li>
        <li><a href="gestion-categorias.php">Categorías</a></li>
        <li><a href="gestion-marcas.php" class="activo">Marcas</a></li>
        <li><a href="logout.php">Cerrar Sesión</a></li>
      </ul>
    </nav>
  </header>

  <!-- Contenido principal -->
  <main>
    <section class="contenido-pagina">
      <h2>Marcas Disponibles</h2>
      <p><a href="agregar-marca.php" class="btn">Agregar Nueva Marca</a></p>

      <?php
      try {
          $stmt = $pdo->query("SELECT * FROM marcas ORDER BY nombre ASC");
          $marcas = $stmt->fetchAll(PDO::FETCH_ASSOC);

          if (empty($marcas)) {
              echo "<p>No hay marcas registradas aún.</p>";
          } else {
              echo "<table border='1' cellpadding='10' cellspacing='0' width='100%'>";
              echo "<tr><th>ID</th><th>Nombre</th><th>Fecha de creación</th><th>Acciones</th></tr>";

              foreach ($marcas as $m) {
                  echo "<tr>";
                  echo "<td>{$m['id']}</td>";
                  echo "<td>{$m['nombre']}</td>";
                  echo "<td>" . date('d/m/Y H:i', strtotime($m['creado_en'])) . "</td>";
                  echo "<td>
                            <a href='editar-marca.php?id={$m['id']}' class='btn secondary'>Editar</a>
                            <a href='eliminar-marca.php?id={$m['id']}' onclick=\"return confirm('¿Eliminar esta marca?')\" class='btn'>Eliminar</a>
                        </td>";
                  echo "</tr>";
              }

              echo "</table>";
          }
      } catch (PDOException $e) {
          echo "<p style='color:red;'>Error al cargar las marcas: " . $e->getMessage() . "</p>";
      }
      ?>
    </section>
  </main>

  <!-- Pie de página -->
  <footer>
    <p>&copy; 2025 Tienda Escolar S.A. | Hecho con ayuda de un gran mentor 💡</p>
  </footer>

</body>
</html>