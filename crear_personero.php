<?php 
require_once 'api/conexion.php'; 

// DATOS DEL PERSONERO DE PRUEBA
$dni = '87654321'; 
$password = 'personero123'; 
$nombres = 'Juan Pérez - Mesa 004562'; 
$rol = 'personero'; // ESTO ES LO QUE DEFINE A DÓNDE IRÁ

// Encriptamos la contraseña
$password_encriptada = password_hash($password, PASSWORD_DEFAULT); 

try {     
    $sql = "INSERT INTO usuario (dni, nombres, password_hash, rol) VALUES (:dni, :nombres, :hash, :rol)";     
    $stmt = $pdo->prepare($sql);     
    $stmt->execute([         
        ':dni' => $dni,         
        ':nombres' => $nombres,         
        ':hash' => $password_encriptada,         
        ':rol' => $rol     
    ]);     
    
    echo "<div style='font-family: sans-serif; padding: 20px; border: 2px solid green; background: #e6ffed;'>";
    echo "<h2>✅ ¡Personero creado con éxito!</h2>";     
    echo "<p>Ya puedes ir al login e ingresar con:</p>";     
    echo "<ul><li><b>DNI:</b> 87654321</li><li><b>Contraseña:</b> personero123</li></ul>"; 
    echo "</div>";
} catch (PDOException $e) {     
    echo "<div style='font-family: sans-serif; padding: 20px; border: 2px solid red; background: #ffe6e6;'>";
    echo "Error al crear usuario (¿Quizás el DNI ya existe?): " . $e->getMessage(); 
    echo "</div>";
} 
?>