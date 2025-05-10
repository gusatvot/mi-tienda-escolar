<?php
// php/protegido.php - Verificar si el usuario está logueado

session_start();

if (!isset($_SESSION['usuario'])) {
    // Si no está logueado, redirigir al login
    header("Location: ../login.html");
    exit();
}
?>