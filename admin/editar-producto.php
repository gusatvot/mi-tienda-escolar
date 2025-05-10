<?php
// admin/editar-producto.php - Editar producto

require_once 'protegido.php';
require_once '../php/db.php';

$upload_dir = '../images/productos/uploads/';
$default_image = '../images/productos/default.jpg';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    die("ID inválido.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $descripcion = trim($_POST['descripcion']);
    $precio_minorista = filter_input(INPUT_POST, 'precio_minorista', FILTER_VALIDATE_FLOAT, ['options' => ['min_range' => 0.01]]);
    $precio_mayorista = filter_input(INPUT_POST, 'precio_mayorista', FILTER_VALIDATE_FLOAT, ['options' => ['min_range' => 0.01]]);
    $stock = filter_input(INPUT_POST, 'stock', FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]);
    $categoria_id = filter_input(INPUT_POST, 'categoria_id', FILTER_VALIDATE_INT);
    $marca_id = filter_input(INPUT_POST, 'marca_id', FILTER_VALIDATE_INT);

    $errores = [];

    if (!$nombre) {
        $errores[] = "El nombre del producto es obligatorio.";
    }

    if ($precio_minorista === false || is_null($precio_minorista)) {
        $errores[] = "Precio minorista inválido.";
    }

    if ($precio_mayorista === false || is_null($precio_mayorista)) {
        $errores[] = "Precio mayorista inválido.";
    }

    if ($stock === false || is_null($stock)) {
        $errores[] = "Stock inválido.";
    }

    // Mostrar errores si hay
    if (!empty($errores)) {
        echo "<div class='alerta-carrito-vacio'>";
        foreach ($errores as $error) {
            echo "<p>$error</p>";
        }
        echo "</div>";
    } else {
        // Manejo de imagen
        $imagen_url = $_POST['imagen_actual']; // Valor predeterminado

        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];

            if (in_array($ext, $allowed)) {
                $new_name = uniqid('prod_', true) . '.' . $ext;
                $target = $upload_dir . $new_name;

                if (move_uploaded_file($_FILES['imagen']['tmp_name'], $target)) {
                    // Eliminar imagen anterior si no es por defecto
                    $stmt = $pdo->prepare("SELECT imagen_url FROM productos WHERE id = ?");
                    $stmt->execute([$id]);
                    $producto = $stmt->fetch(PDO::FETCH_ASSOC);
                    $imagen_anterior = $producto['imagen_url'];

                    if ($imagen_anterior && $imagen_anterior !== 'images/productos/default.jpg' && file_exists('../' . $imagen_anterior)) {
                        unlink('../' . $imagen_anterior); // Borrar imagen anterior
                    }

                    $imagen_url = 'images/productos/uploads/' . $new_name;
                }
            }
        }

        try {
            $stmt = $pdo->prepare("
                UPDATE productos SET
                nombre = ?, descripcion = ?, precio_minorista = ?, precio_mayorista = ?, stock = ?, 
                imagen_url = ?, categoria_id = ?, marca_id = ?
                WHERE id = ?
            ");
            $stmt->execute([
                $nombre,
                $descripcion,
                $precio_minorista,
                $precio_mayorista,
                $stock,
                $imagen_url,
                $categoria_id,
                $marca_id,
                $id
            ]);

            header("Location: gestion-productos.php");
            exit();
        } catch (PDOException $e) {
            echo "<p style='color:red;'>Error al actualizar el producto: " . $e->getMessage() . "</p>";
        }
    }
}

try {
    $stmt = $pdo->prepare("SELECT * FROM productos WHERE id = ?");
    $stmt->execute([$id]);
    $producto = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$producto) {
        die("Producto no encontrado.");
    }

    // Cargar categorías para el selector
    $stmt_cats = $pdo->query("SELECT * FROM categorias ORDER BY nombre ASC");
    $categorias = $stmt_cats->fetchAll(PDO::FETCH_ASSOC);

    // Cargar marcas para el selector
    $stmt_marcas = $pdo->query("SELECT * FROM marcas ORDER BY nombre ASC");
    $marcas = $stmt_marcas->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al obtener los datos: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Editar Producto - Panel Admin</title>
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
      <h2>Editar Producto</h2>

      <form method="POST" enctype="multipart/form-data">
        <label for="nombre">Nombre:</label>
        <input type="text" name="nombre" value="<?= htmlspecialchars($producto['nombre']) ?>" required><br><br>

        <label for="descripcion">Descripción:</label>
        <textarea name="descripcion"><?= htmlspecialchars($producto['descripcion']) ?></textarea><br><br>

        <label for="imagen">Cambiar imagen:</label>
        <input type="file" name="imagen" accept="image/*"><br><br>

        <input type="hidden" name="imagen_actual" value="<?= $producto['imagen_url'] ?>">

        <label for="categoria">Categoría:</label>
        <select name="categoria_id" id="categoria">
          <option value="">Selecciona una categoría</option>
          <?php foreach ($categorias as $cat): ?>
            <option value="<?= $cat['id'] ?>" <?= $producto['categoria_id'] == $cat['id'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($cat['nombre']) ?>
            </option>
          <?php endforeach; ?>
        </select><br><br>

        <label for="marca">Marca:</label>
        <select name="marca_id" id="marca">
          <option value="">Selecciona una marca</option>
          <?php foreach ($marcas as $marca): ?>
            <option value="<?= $marca['id'] ?>" <?= $producto['marca_id'] == $marca['id'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($marca['nombre']) ?>
            </option>
          <?php endforeach; ?>
        </select><br><br>

        <label for="precio_minorista">Precio Minorista:</label>
        <input type="number" step="0.01" name="precio_minorista" value="<?= $producto['precio_minorista'] ?>" required><br><br>

        <label for="precio_mayorista">Precio Mayorista:</label>
        <input type="number" step="0.01" name="precio_mayorista" value="<?= $producto['precio_mayorista'] ?>" required><br><br>

        <label for="stock">Stock Disponible:</label>
        <input type="number" name="stock" value="<?= $producto['stock'] ?>" required><br><br>

        <button type="submit" class="btn">Actualizar Producto</button>
      </form>
    </section>
  </main>

  <!-- Pie de página -->
  <footer>
    <p>&copy; 2025 Tienda Escolar S.A. | Hecho con ayuda de un gran mentor 💡</p>
  </footer>

</body>
</html>