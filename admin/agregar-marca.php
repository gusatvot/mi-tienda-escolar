<?php
// admin/agregar-marca.php - Formulario para agregar marca

require_once 'protegido.php';
require_once '../php/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = htmlspecialchars(trim($_POST['nombre']));
    
    if (!$nombre) {
        echo "<!DOCTYPE html>
        <html lang='es'>
        <head>
          <meta charset='UTF-8'>
          <title>Agregar Marca</title>
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
                <li><a href='gestion-marcas.php'>Volver</a></li>
                <li><a href='logout.php'>Cerrar Sesión</a></li>
              </ul>
            </nav>
          </header>

          <!-- Contenido principal -->
          <main class='container'>
            <section class='contenido-pagina'>
              <h2>Agregar Nueva Marca</h2>
              <p style='color:red;'>❌ El nombre de la marca es obligatorio.</p>
        ";
    } else {
        try {
            // Verificar si ya existe la marca
            $stmt = $pdo->prepare("SELECT * FROM marcas WHERE nombre = ?");
            $stmt->execute([$nombre]);
            if ($stmt->rowCount() > 0) {
                echo "<!DOCTYPE html>
                <html lang='es'>
                <head>
                  <meta charset='UTF-8'>
                  <title>Agregar Marca</title>
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
                        <li><a href='gestion-marcas.php'>Volver</a></li>
                        <li><a href='logout.php'>Cerrar Sesión</a></li>
                      </ul>
                    </nav>
                  </header>

                  <!-- Contenido principal -->
                  <main class='container'>
                    <section class='contenido-pagina'>
                      <h2>Agregar Nueva Marca</h2>
                      <p style='color:red;'>❌ Ya existe una marca con ese nombre.</p>
                ";
            } else {
                $stmt = $pdo->prepare("INSERT INTO marcas (nombre) VALUES (?)");
                $stmt->execute([$nombre]);

                header("Location: gestion-marcas.php");
                exit();
            }
        } catch (PDOException $e) {
            die("Error al guardar la marca: " . $e->getMessage());
        }
    }

    // Si hay errores, mostramos aquí el formulario otra vez
    echo "<form method='POST'>
            <label for='nombre'>Nombre de la Marca:</label>
            <input type='text' name='nombre' value='$nombre' required style='width:100%; padding:10px; margin-bottom:10px;' />
            <button type='submit' class='btn'>Guardar Marca</button>
          </form>
        </section>
      </main>

      <!-- Pie de página -->
      <footer>
        <p>&copy; 2025 Tienda Escolar S.A. | Hecho con ayuda de un gran mentor 💡</p>
      </footer>

    </body>
    </html>";
    exit(); // Salimos para no mostrar dos veces el formulario
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Agregar Marca - Panel Admin</title>
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
        <li><a href="gestion-marcas.php">Volver</a></li>
        <li><a href="logout.php">Cerrar Sesión</a></li>
      </ul>
    </nav>
  </header>

  <!-- Contenido principal -->
  <main class="container">
    <section class="contenido-pagina">
      <h2>Agregar Nueva Marca</h2>

      <form method="POST">
        <label for="nombre">Nombre:</label>
        <input type="text" name="nombre" required style="width:100%; padding:10px; margin-bottom:10px;" />

        <button type="submit" class="btn">Guardar Marca</button>
      </form>
    </section>
  </main>

  <!-- Pie de página -->
  <footer>
    <p>&copy; 2025 Tienda Escolar S.A. | Hecho con ayuda de un gran mentor 💡</p>
  </footer>

</body>
</html>