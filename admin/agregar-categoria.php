<?php
// admin/agregar-categoria.php - Formulario para agregar marca

require_once 'protegido.php';
require_once '../php/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = htmlspecialchars(trim($_POST['nombre']));
    
    if (!$nombre) {
        echo "<!DOCTYPE html>
        <html lang='es'>
        <head>
          <meta charset='UTF-8'>
          <title>Agregar Categoría</title>
          <link href='https://fonts.googleapis.com/css2?family=Roboto :wght@400;700&display=swap' rel='stylesheet'>
          <link rel='stylesheet' href='../css/styles.css'>
        </head>
        <body>

          <!-- Encabezado -->
          <header>
            <div class='logo container'>
              <img src='../images/logo.png' alt='Logo' width='50'>
              <h1>Panel de Administrador</h1>
            </div>

            <nav>
              <ul>
                <li><a href='gestion-categorias.php'>Volver</a></li>
                <li><a href='logout.php'>Cerrar Sesión</a></li>
              </ul>
            </nav>
          </header>

          <!-- Contenido principal -->
          <main class='container'>
            <section class='contenido-pagina'>
              <h2>Agregar Nueva Categoría</h2>
              <p style='color:red;'>❌ El nombre de la categoría es obligatorio.</p>
        ";
    } else {
        try {
            // Verificar si ya existe la categoría
            $stmt = $pdo->prepare("SELECT * FROM categorias WHERE nombre = ?");
            $stmt->execute([$nombre]);
            if ($stmt->rowCount() > 0) {
                echo "<!DOCTYPE html>
                <html lang='es'>
                <head>
                  <meta charset='UTF-8'>
                  <title>Agregar Categoría</title>
                  <link href='https://fonts.googleapis.com/css2?family=Roboto :wght@400;700&display=swap' rel='stylesheet'>
                  <link rel='stylesheet' href='../css/styles.css'>
                </head>
                <body>

                  <!-- Encabezado -->
                  <header>
                    <div class='logo container'>
                      <img src='../images/logo.png' alt='Logo' width='50'>
                      <h1>Panel de Administrador</h1>
                    </div>

                    <nav>
                      <ul>
                        <li><a href='gestion-categorias.php'>Volver</a></li>
                        <li><a href='logout.php'>Cerrar Sesión</a></li>
                      </ul>
                    </nav>
                  </header>

                  <!-- Contenido principal -->
                  <main class='container'>
                    <section class='contenido-pagina'>
                      <h2>Agregar Nueva Categoría</h2>
                      <p style='color:red;'>❌ Ya existe una categoría con ese nombre.</p>
                ";
            } else {
                $stmt = $pdo->prepare("INSERT INTO categorias (nombre) VALUES (?)");
                $stmt->execute([$nombre]);

                header("Location: gestion-categorias.php");
                exit();
            }
        } catch (PDOException $e) {
            die("Error al guardar la categoría: " . $e->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Agregar Categoría - Panel Admin</title>
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
        <li><a href="gestion-categorias.php">Volver</a></li>
        <li><a href="logout.php">Cerrar Sesión</a></li>
      </ul>
    </nav>
  </header>

  <!-- Contenido principal -->
  <main class="container">
    <section class="contenido-pagina">
      <h2>Agregar Nueva Categoría</h2>

      <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($producto)) { ?>
        <p style='color:red;'>Ya existe una categoría con ese nombre.</p>
      <?php } ?>

      <form method="POST">
        <label for="nombre">Nombre de la Categoría:</label>
        <input type="text" name="nombre" required style="width:100%; padding:10px; margin-bottom:10px;" /><br />

        <button type="submit" class="btn">Guardar Categoría</button>
      </form>
    </section>
  </main>

  <!-- Pie de página -->
  <footer>
    <p>&copy; 2025 Tienda Escolar S.A. | Hecho con ayuda de un gran mentor 💡</p>
  </footer>

</body>
</html>