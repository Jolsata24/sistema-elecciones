<?php
// Iniciar el manejo de sesiones de PHP
session_start();

header('Content-Type: application/json');
require_once 'conexion.php';

// Limpiar el DNI para evitar inyecciones
$dni = filter_input(INPUT_POST, 'dni', FILTER_SANITIZE_STRING);
$password = $_POST['password'] ?? '';

if (empty($dni) || empty($password)) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Completa todos los campos."]);
    exit;
}

try {
    // Buscar al usuario por DNI
    $sql = "SELECT id, nombres, password_hash, rol FROM usuario WHERE dni = :dni LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':dni', $dni, PDO::PARAM_STR);
    $stmt->execute();
    
    $usuario = $stmt->fetch();

    // password_verify desencripta automáticamente el hash de la BD y lo compara
    if ($usuario && password_verify($password, $usuario['password_hash'])) {
        
        // Login exitoso: Guardar datos en la sesión del servidor
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['nombres'] = $usuario['nombres'];
        $_SESSION['rol'] = $usuario['rol'];

        echo json_encode(["status" => "success", "message" => "Acceso autorizado."]);
    } else {
        http_response_code(401);
        echo json_encode(["status" => "error", "message" => "DNI o contraseña incorrectos."]);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Error de conexión al servidor."]);
}
?>