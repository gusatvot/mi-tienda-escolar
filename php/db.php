<?php
// php/db.php - Conexión a la base de datos

$host = 'localhost';       // Servidor local
$user = 'root';            // Usuario por defecto de MySQL
$password = '';            // Contraseña vacía por defecto
$dbname = 'tienda_escolar'; // Nombre de la base de datos

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error al conectar a la base de datos: " . $e->getMessage());
}
?>