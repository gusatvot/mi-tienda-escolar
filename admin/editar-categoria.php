<?php
// admin/editar-categoria.php - Formulario para editar categoría

require_once 'protegido.php';
require_once '../php/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    die("ID inválido.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = htmlspecialchars(trim($_POST['nombre']));
    if (!$nombre) {
        echo "<p style='color:red;'>El nombre de la categoría es obligatorio.</p>";
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE categorias SET nombre = ? WHERE id = ?");
            $stmt->execute([$nombre, $id]);

            header("Location: gestion-categorias.php");
            exit();
        } catch (PDOException $e) {
            echo "<p style='color:red;'>Error al actualizar la categoría: " . $e->getMessage() . "</p>";
        }
    }
}

try {
    $stmt = $pdo->prepare("SELECT * FROM categorias WHERE id = ?");
    $stmt->execute([$id]);
    $categoria = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$categoria) {
        die("Categoría no encontrada.");
    }
} catch (PDOException $e) {
    die("Error al obtener los datos: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Editar Categoría - Panel Admin</title>
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
        <li><a href="gestion-categorias.php">Volver</a></li>
        <li><a href="logout.php">Cerrar Sesión</a></li>
      </ul>
    </nav>
  </header>

  <!-- Contenido principal -->
  <main>
    <section class="contenido-pagina">
      <h2>Editar Categoría</h2>

      <form method="POST">
        <label for="nombre">Nombre:</label>
        <input type="text" name="nombre" value="<?= $categoria['nombre'] ?>" required style="width: 100%; padding: 10px; margin-bottom: 10px;" />

        <button type="submit" class="btn">Actualizar Categoría</button>
      </form>
    </section>
  </main>

  <!-- Pie de página -->
  <footer>
    <p>&copy; 2025 Tienda Escolar S.A. | Hecho con ayuda de un gran mentor 💡</p>
  </footer>

</body>
</html>