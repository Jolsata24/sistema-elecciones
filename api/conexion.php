<?php
// Configuración de la base de datos MySQL
$host = 'localhost'; 
$dbname = 'elecciones_db';
$username = 'root'; // Tu usuario de MySQL
$password = ''; // Tu contraseña

try {
    // CORRECCIÓN: Se cambió 'pgsql:' por 'mysql:' y se agregó el charset
    $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password);
    
    // Configurar PDO para que lance excepciones cuando ocurra un error
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Configurar el modo de obtención de datos por defecto a array asociativo
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // ESTO TE DIRÁ EL MOTIVO EXACTO DEL ERROR
    echo "Fallo la conexión: " . $e->getMessage();
    exit;
}
?>