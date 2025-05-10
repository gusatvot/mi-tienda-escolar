<?php
// admin/gestion-productos.php - Panel de gestión de productos

require_once 'protegido.php';
require_once '../php/db.php';

// Obtener categorías para el filtro
try {
    $stmt_cats = $pdo->query("SELECT * FROM categorias ORDER BY nombre ASC");
    $categorias = $stmt_cats->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $categorias = [];
}

// Configuración de ordenamiento y filtros
$columnas_orden = ['p.nombre', 'c.nombre', 'p.precio_minorista', 'p.precio_mayorista', 'p.stock'];
$ordenes_validos = ['ASC', 'DESC'];

$orderby = in_array($_GET['orderby'] ?? '', $columnas_orden) ? $_GET['orderby'] : 'p.nombre';
$orderdir = in_array(strtoupper($_GET['orderdir'] ?? ''), $ordenes_validos) ? strtoupper($_GET['orderdir']) : 'ASC';
$categoria_id = !empty($_GET['categoria_id']) ? (int)$_GET['categoria_id'] : null;
$busqueda_nombre = trim($_GET['buscar'] ?? '');

function enlace_orden($columna, $actual_columna, $direccion_actual, $categoria_id = null, $busqueda = null) {
    $dir = ($columna === $actual_columna && $direccion_actual === 'ASC') ? 'DESC' : 'ASC';
    $params = ['orderby' => $columna, 'orderdir' => $dir];
    if ($categoria_id) $params['categoria_id'] = $categoria_id;
    if ($busqueda) $params['buscar'] = urlencode($busqueda);
    return "?" . http_build_query($params);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Gestión de Productos - Panel Admin</title>
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
        <li><a href="gestion-productos.php" class="activo">Productos</a></li>
        <li><a href="gestion-categorias.php">Categorías</a></li>
        <li><a href="logout.php">Cerrar Sesión</a></li>
      </ul>
    </nav>
  </header>

  <!-- Contenido principal -->
  <main>
    <section class="contenido-pagina">
      <h2>Lista de Productos</h2>
      <p><a href="agregar-producto.php" class="btn">Agregar Nuevo Producto</a></p>

      <!-- Filtros -->
      <form method="GET" style="margin-bottom: 20px;">
        <div style="display:flex; gap:10px; flex-wrap: wrap;">
          <div style="flex:1; min-width:200px;">
            <label for="buscar">Buscar por nombre:</label>
            <input type="text" name="buscar" id="buscar" value="<?= htmlspecialchars($busqueda_nombre) ?>" placeholder="Nombre del producto..." style="width:100%; padding:8px;" />
          </div>

          <div style="flex:1; min-width:200px;">
            <label for="categoria">Filtrar por categoría:</label>
            <select name="categoria_id" id="categoria" onchange="this.form.submit()" style="width:100%; padding:8px;">
              <option value="">Todas las categorías</option>
              <?php foreach ($categorias as $cat): ?>
                <option value="<?= $cat['id'] ?>" <?= $categoria_id == $cat['id'] ? 'selected' : '' ?>>
                  <?= htmlspecialchars($cat['nombre']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div style="align-self:center;">
            <button type="submit" class="btn secondary" style="padding:8px 15px;">Filtrar</button>
          </div>
        </div>
      </form>

      <!-- Tabla de productos -->
      <?php
      try {
          // Consulta base
          $sql = "
              SELECT p.id, p.nombre, p.precio_minorista, p.precio_mayorista, p.stock, c.nombre AS categoria_nombre 
              FROM productos p
              LEFT JOIN categorias c ON p.categoria_id = c.id
              WHERE 1=1
          ";

          $params = [];

          // Si hay filtro de categoría
          if (!empty($categoria_id)) {
              $sql .= " AND p.categoria_id = ?";
              $params[] = $categoria_id;
          }

          // Si hay búsqueda por nombre
          if (!empty($busqueda_nombre)) {
              $sql .= " AND p.nombre LIKE ?";
              $params[] = "%" . $busqueda_nombre . "%";
          }

          // Ordenar por columna y dirección
          $sql .= " ORDER BY $orderby $orderdir";

          $stmt = $pdo->prepare($sql);
          $stmt->execute($params);
          $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

          if (empty($productos)) {
              echo "<p>No hay productos registrados aún.</p>";
          } else {
              echo "<table border='1' cellpadding='10' cellspacing='0' width='100%'>";
              echo "<tr>";

              echo "<th><a href='" . enlace_orden('p.nombre', $orderby, $orderdir, $categoria_id, $busqueda_nombre) . "'>Nombre</a></th>";
              echo "<th><a href='" . enlace_orden('c.nombre', $orderby, $orderdir, $categoria_id, $busqueda_nombre) . "'>Categoría</a></th>";
              echo "<th><a href='" . enlace_orden('p.precio_minorista', $orderby, $orderdir, $categoria_id, $busqueda_nombre) . "'>Precio Minorista</a></th>";
              echo "<th><a href='" . enlace_orden('p.precio_mayorista', $orderby, $orderdir, $categoria_id, $busqueda_nombre) . "'>Precio Mayorista</a></th>";
              echo "<th><a href='" . enlace_orden('p.stock', $orderby, $orderdir, $categoria_id, $busqueda_nombre) . "'>Stock</a></th>";
              echo "<th>Acciones</th>";

              echo "</tr>";

              foreach ($productos as $p) {
                  echo "<tr>";
                  echo "<td>{$p['nombre']}</td>";
                  echo "<td>" . ($p['categoria_nombre'] ?: '<em>Sin categoría</em>') . "</td>";
                  echo "<td>\$ {$p['precio_minorista']}</td>";
                  echo "<td>\$ {$p['precio_mayorista']}</td>";
                  echo "<td>{$p['stock']}</td>";
                  echo "<td>
                            <a href='editar-producto.php?id={$p['id']}' class='btn secondary'>Editar</a>
                            <a href='eliminar-producto.php?id={$p['id']}' onclick=\"return confirm('¿Eliminar este producto?')\" class='btn'>Eliminar</a>
                        </td>";
                  echo "</tr>";
              }

              echo "</table>";
          }
      } catch (PDOException $e) {
          echo "<p style='color:red;'>Error al cargar los productos: " . $e->getMessage() . "</p>";
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