<?php
// php/register.php - Registrar nuevos usuarios y redirigir

require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $tipo = $_POST['tipo'] === 'mayorista' ? 'mayorista' : 'minorista';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Correo electrónico no válido.");
    }

    try {
        // Verificar si el correo ya existe
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->rowCount() > 0) {
            die("El correo ya está registrado.");
        }

        // Insertar nuevo usuario
        $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, email, password, tipo) VALUES (?, ?, ?, ?)");
        $stmt->execute([$nombre, $email, $password, $tipo]);

        // Redirigir a login.html tras registro exitoso
        header("Location: ../login.html");
        exit();
    } catch (PDOException $e) {
        die("Error al registrar el usuario: " . $e->getMessage());
    }
}
?>