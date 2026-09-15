<?php
// 1. Configurar cabeceras de respuesta a JSON
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Methods: POST');

// 2. Incluir el archivo de conexión
require_once 'conexion.php';

// 3. Recibir y sanitizar los datos numéricos (asegura que solo pasen números enteros)
$mesa_id = filter_input(INPUT_POST, 'mesa_id', FILTER_VALIDATE_INT);
$votos_sp = filter_input(INPUT_POST, 'votos_somos_peru', FILTER_VALIDATE_INT);
$votos_blancos = filter_input(INPUT_POST, 'votos_blancos', FILTER_VALIDATE_INT);
$votos_nulos = filter_input(INPUT_POST, 'votos_nulos', FILTER_VALIDATE_INT);

// Validar que todos los campos requeridos estén presentes y sean válidos
if ($mesa_id === false || $votos_sp === false || $votos_blancos === false || $votos_nulos === false) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Por favor, completa todos los campos numéricos correctamente."]);
    exit;
}

// 4. Procesamiento seguro de la fotografía del acta
$foto_url = null;
if (isset($_FILES['foto_acta']) && $_FILES['foto_acta']['error'] === UPLOAD_ERR_OK) {
    
    // Directorio relativo donde se guardarán las imágenes (subiendo un nivel desde la carpeta 'api')
    $upload_dir = '../uploads/actas/';
    
    // Crear el directorio dinámicamente si no existe (con permisos 0755)
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    $file_info = pathinfo($_FILES['foto_acta']['name']);
    $extension = strtolower($file_info['extension']);
    
    // Validar que realmente sea una imagen
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'webp'];
    if (!in_array($extension, $allowed_extensions)) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Formato de archivo no permitido. Sube una imagen (JPG, PNG)."]);
        exit;
    }
    
    // Generar un nombre único: acta_mesa_1234_1634567890.jpg
    $nombre_archivo = "acta_mesa_" . $mesa_id . "_" . time() . "." . $extension;
    $ruta_destino_fisica = $upload_dir . $nombre_archivo;
    
    // Ruta que se guardará en la base de datos (relativa a la raíz del proyecto)
    $foto_url = "uploads/actas/" . $nombre_archivo;
    
    if (!move_uploaded_file($_FILES['foto_acta']['tmp_name'], $ruta_destino_fisica)) {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "No se pudo guardar la fotografía en el servidor."]);
        exit;
    }
} else {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Es obligatorio adjuntar la fotografía del acta."]);
    exit;
}

// 5. Inserción en la base de datos usando sentencias preparadas (Anti SQL-Injection)
try {
    // ON CONFLICT (mesa_id) garantiza que si hay reintentos por mala conexión,
    // se actualice el registro de esa mesa en lugar de crear un duplicado o lanzar error.
    $sql = "INSERT INTO resultado (mesa_id, votos_somos_peru, votos_blancos, votos_nulos, foto_acta_url, fecha_registro) 
            VALUES (:mesa_id, :votos_sp, :votos_blancos, :votos_nulos, :foto_url, NOW())
            ON CONFLICT (mesa_id) DO UPDATE SET 
                votos_somos_peru = EXCLUDED.votos_somos_peru,
                votos_blancos = EXCLUDED.votos_blancos,
                votos_nulos = EXCLUDED.votos_nulos,
                foto_acta_url = EXCLUDED.foto_acta_url,
                fecha_registro = NOW()";

    $stmt = $pdo->prepare($sql);
    
    // Enlazar las variables
    $stmt->bindParam(':mesa_id', $mesa_id, PDO::PARAM_INT);
    $stmt->bindParam(':votos_sp', $votos_sp, PDO::PARAM_INT);
    $stmt->bindParam(':votos_blancos', $votos_blancos, PDO::PARAM_INT);
    $stmt->bindParam(':votos_nulos', $votos_nulos, PDO::PARAM_INT);
    $stmt->bindParam(':foto_url', $foto_url, PDO::PARAM_STR);
    
    $stmt->execute();

    // Respuesta exitosa al frontend
    echo json_encode(["status" => "success", "message" => "¡Datos de la mesa $mesa_id registrados exitosamente!"]);

} catch (PDOException $e) {
    // Registrar el error real en los logs del servidor para depurar
    error_log("Error en base de datos: " . $e->getMessage());
    
    // Enviar un mensaje genérico al frontend por seguridad
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Ocurrió un error al guardar en la base de datos."]);
}
?>