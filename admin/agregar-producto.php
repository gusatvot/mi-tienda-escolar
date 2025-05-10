<?php
// admin/agregar-producto.php - Formulario para agregar producto

require_once 'protegido.php';
require_once '../php/db.php';

$upload_dir = '../images/productos/uploads/';
$default_image = '../images/productos/default.jpg';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $descripcion = trim($_POST['descripcion']);
    $precio_minorista = filter_input(INPUT_POST, 'precio_minorista', FILTER_VALIDATE_FLOAT, ['options' => ['min_range' => 0.01]]);
    $precio_mayorista = filter_input(INPUT_POST, 'precio_mayorista', FILTER_VALIDATE_FLOAT, ['options' => ['min_range' => 0.01]]);
    $stock = filter_input(INPUT_POST, 'stock', FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]);
    $categoria_id = filter_input(INPUT_POST, 'categoria_id', FILTER_VALIDATE_INT);
    $marca_id = filter_input(INPUT_POST, 'marca_id', FILTER_VALIDATE_INT);

    $errores = [];

    if (empty($nombre)) {
        $errores[] = "El nombre del producto es obligatorio.";
    }

    if (is_null($precio_minorista)) {
        $errores[] = "Precio minorista inválido.";
    }

    if (is_null($precio_mayorista)) {
        $errores[] = "Precio mayorista inválido.";
    }

    if (is_null($stock)) {
        $stock = 0;
    }

    if (!empty($errores)) {
        echo "<div class='alerta-carrito-vacio'>";
        foreach ($errores as $error) {
            echo "<p>$error</p>";
        }
        echo "</div>";
    } else {
        // Manejo de imagen
        $imagen_url = $default_image;

        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];

            if (in_array($ext, $allowed)) {
                $new_name = uniqid('prod_', true) . '.' . $ext;
                $target = $upload_dir . $new_name;

                if (move_uploaded_file($_FILES['imagen']['tmp_name'], $target)) {
                    $imagen_url = 'images/productos/uploads/' . $new_name;
                } else {
                    echo "<p style='color:red;'>Error al mover la imagen.</p>";
                }
            } else {
                echo "<p style='color:red;'>Formato de imagen no válido.</p>";
            }
        }

        try {
            $stmt = $pdo->prepare("
                INSERT INTO productos 
                (nombre, descripcion, precio_minorista, precio_mayorista, stock, imagen_url, categoria_id, marca_id) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $nombre,
                $descripcion,
                $precio_minorista,
                $precio_mayorista,
                $stock,
                $imagen_url,
                $categoria_id,
                $marca_id
            ]);

            echo "<p style='color:green;'>Producto agregado correctamente.</p>";
            echo "<p><a href='gestion-productos.php'>Volver a Gestión de Productos</a></p>";
        } catch (PDOException $e) {
            echo "<p style='color:red;'>Error al guardar el producto: " . $e->getMessage() . "</p>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Agregar Producto - Panel Admin</title>
  <link href="https://fonts.googleapis.com/css2?family=Roboto :wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../css/styles.css" />
</head>
<body>

  <!-- Encabezado -->
  <header>
    <div class="logo container">
      <img src="../images/logo.png" alt="Logo" width="50">
      <h1>Panel de Administrador</h1>
    </div>

    <nav>
      <ul>
        <li><a href="gestion-productos.php">Volver</a></li>
        <li><a href="logout.php">Cerrar Sesión</a></li>
      </ul>
    </nav>
  </header>

  <!-- Contenido principal -->
  <main class="container">
    <section class="contenido-pagina">
      <h2>Agregar Nuevo Producto</h2>

      <form method="POST" enctype="multipart/form-data">
        <label for="nombre">Nombre del Producto*:</label>
        <input type="text" name="nombre" required style="width:100%; padding:10px; margin-bottom:10px;"><br>

        <label for="descripcion">Descripción:</label>
        <textarea name="descripcion" rows="4" style="width:100%; padding:10px; margin-bottom:10px;"></textarea><br>

        <label for="imagen">Imagen del producto:</label>
        <input type="file" name="imagen" accept="image/*"><br><br>

        <label for="categoria">Categoría:</label>
        <select name="categoria_id" id="categoria" style="width:100%; padding:10px; margin-bottom:10px;">
          <option value="">Selecciona una categoría</option>
          <?php
          try {
              $stmt_cats = $pdo->query("SELECT * FROM categorias ORDER BY nombre ASC");
              while ($cat = $stmt_cats->fetch(PDO::FETCH_ASSOC)) {
                  echo "<option value='{$cat['id']}'>{$cat['nombre']}</option>";
              }
          } catch (PDOException $e) {
              echo "<option value=''>Error al cargar categorías</option>";
          }
          ?>
        </select><br>

        <label for="marca">Marca:</label>
        <select name="marca_id" id="marca" style="width:100%; padding:10px; margin-bottom:10px;">
          <option value="">Selecciona una marca</option>
          <?php
          try {
              $stmt_marcas = $pdo->query("SELECT * FROM marcas ORDER BY nombre ASC");
              while ($marca = $stmt_marcas->fetch(PDO::FETCH_ASSOC)) {
                  echo "<option value='{$marca['id']}'>{$marca['nombre']}</option>";
              }
          } catch (PDOException $e) {
              echo "<option value=''>Error al cargar marcas</option>";
          }
          ?>
        </select><br>

        <label for="precio_minorista">Precio Minorista*:</label>
        <input type="number" step="0.01" name="precio_minorista" min="0.01" required style="width:100%; padding:10px; margin-bottom:10px;"><br>

        <label for="precio_mayorista">Precio Mayorista*:</label>
        <input type="number" step="0.01" name="precio_mayorista" min="0.01" required style="width:100%; padding:10px; margin-bottom:10px;"><br>

        <label for="stock">Stock Disponible*:</label>
        <input type="number" name="stock" value="0" min="0" required style="width:100%; padding:10px; margin-bottom:10px;"><br>

        <button type="submit" class="btn" style="width:100%;">Guardar Producto</button>
      </form>
    </section>
  </main>

  <!-- Pie de página -->
  <footer>
    <p>&copy; 2025 Tienda Escolar S.A. | Hecho con ayuda de un gran mentor 💡</p>
  </footer>

</body>
</html>