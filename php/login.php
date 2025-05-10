<?php
// php/login.php - Iniciar sesión y redirigir

session_start();
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Correo electrónico no válido.");
    }

    try {
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario && password_verify($password, $usuario['password'])) {
            // Simulamos iniciar sesión
            $_SESSION['usuario'] = [
                'id' => $usuario['id'],
                'nombre' => $usuario['nombre'],
                'email' => $usuario['email'],
                'tipo' => $usuario['tipo']
            ];

            // Redirigir a index.html tras login exitoso
            header("Location: ../index.html");
            exit();
        } else {
            echo "Correo o contraseña incorrectos.";
        }
    } catch (PDOException $e) {
        die("Error al iniciar sesión: " . $e->getMessage());
    }
}
?>