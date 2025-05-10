<?php
// admin/gestion-categorias.php - Panel de gestión de categorías

require_once 'protegido.php';
require_once '../php/db.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Gestión de Categorías - Panel Admin</title>
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
        <li><a href="gestion-categorias.php" class="activo">Categorías</a></li>
        <li><a href="logout.php">Cerrar Sesión</a></li>
      </ul>
    </nav>
  </header>

  <!-- Contenido principal -->
  <main>
    <section class="contenido-pagina">
      <h2>Categorías Disponibles</h2>
      <p><a href="agregar-categoria.php" class="btn">Agregar Nueva Categoría</a></p>

      <?php
      try {
          $stmt = $pdo->query("SELECT * FROM categorias ORDER BY nombre ASC");
          $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

          if (empty($categorias)) {
              echo "<p>No hay categorías registradas aún.</p>";
          } else {
              echo "<table border='1' cellpadding='10' cellspacing='0' width='100%'>";
              echo "<tr><th>ID</th><th>Nombre</th><th>Fecha de creación</th><th>Acciones</th></tr>";

              foreach ($categorias as $c) {
                  echo "<tr>";
                  echo "<td>{$c['id']}</td>";
                  echo "<td>{$c['nombre']}</td>";
                  echo "<td>" . date('d/m/Y H:i', strtotime($c['creado_en'])) . "</td>";
                  echo "<td>
                            <a href='editar-categoria.php?id={$c['id']}' class='btn secondary'>Editar</a>
                            <a href='eliminar-categoria.php?id={$c['id']}' onclick=\"return confirm('¿Eliminar esta categoría?')\" class='btn'>Eliminar</a>
                        </td>";
                  echo "</tr>";
              }

              echo "</table>";
          }
      } catch (PDOException $e) {
          echo "Error al cargar las categorías: " . $e->getMessage();
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