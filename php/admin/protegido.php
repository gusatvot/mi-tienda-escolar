<?php
// php/admin/protegido.php - Verifica si el usuario es administrador

session_start();

if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo'] !== 'admin') {
    header("Location: login.php");
    exit();
}
?>