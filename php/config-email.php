<?php
// php/config-email.php - Configuración de correo

// Datos del remitente
define('EMAIL_HOST', 'smtp.gmail.com'); // Ej: smtp.gmail.com
define('EMAIL_PORT', 587);             // Puerto TLS
define('EMAIL_USER', 'tu-correo@gmail.com');     // Tu correo
define('EMAIL_PASS', 'tu-contraseña-o-app-password'); // Tu contraseña o App Password
define('EMAIL_FROM', 'no-reply@tiendaescolar.com');
define('EMAIL_NAME', 'Tienda Escolar S.A.');
define('EMAIL_ADMIN', 'tu-correo@gmail.com'); // Correo donde recibirás las cotizaciones
?>