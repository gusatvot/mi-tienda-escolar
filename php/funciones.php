<?php
// Funciones auxiliares para la tienda escolar

// Función para sanitizar datos de entrada
function sanitizarEntrada($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// Función para validar el formato de un correo electrónico
function validarEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Función para generar un hash de contraseña
function generarHash($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

// Función para verificar una contraseña
function verificarHash($password, $hash) {
    return password_verify($password, $hash);
}

// Función para redirigir a una página
function redirigir($url) {
    header("Location: $url");
    exit();
}

// Función para mostrar mensajes de error
function mostrarError($mensaje) {
    echo "<div class='error'>$mensaje</div>";
}

// Función para mostrar mensajes de éxito
function mostrarExito($mensaje) {
    echo "<div class='exito'>$mensaje</div>";
}
?>