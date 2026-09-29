<?php
session_start();

// Destruir todas las variables de sesión
$_SESSION = array();

// Destruir la sesión por completo
session_destroy();

// Redirigir de vuelta al login
header("Location: ../login.html");
exit;
?>