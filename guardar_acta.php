<?php
// Configurar cabeceras para aceptar peticiones de la app (CORS y JSON)
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Methods: POST');

// 1. Conexión segura a PostgreSQL usando PDO
$host = '127.0.0.1';
$dbname = 'elecciones_db';
$username = 'usuario_db';
$password = 'password_seguro';

try {
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Error de conexión al servidor."]);
    exit;
}

// 2. Recibir y validar los datos numéricos enviados por el personero
$mesa_id = filter_input(INPUT_POST, 'mesa_id', FILTER_VALIDATE_INT);
$votos_sp = filter_input(INPUT_POST, 'votos_somos_peru', FILTER_VALIDATE_INT);
$votos_blancos = filter_input(INPUT_POST, 'votos_blancos', FILTER_VALIDATE_INT);
$votos_nulos = filter_input(INPUT_POST, 'votos_nulos', FILTER_VALIDATE_INT);

// Verificación básica: asegurar que no falten datos
if ($mesa_id === false || $votos_sp === false) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Faltan datos obligatorios o formato inválido."]);
    exit;
}

// 3. Procesamiento de la fotografía del acta
$foto_url = null;
if (isset($_FILES['foto_acta']) && $_FILES['foto_acta']['error'] === UPLOAD_ERR_OK) {
    
    // Generar un nombre único para evitar que se sobrescriban actas
    $extension = pathinfo($_FILES['foto_acta']['name'], PATHINFO_EXTENSION);
    $nombre_archivo = "acta_mesa_" . $mesa_id . "_" . time() . "." . $extension;
    
    // Ruta donde se guardará (asegúrate de que la carpeta tenga permisos de escritura)
    $ruta_destino = "uploads/actas/" . $nombre_archivo;
    
    // Mover el archivo temporal a su ubicación final
    if (move_uploaded_file($_FILES['foto_acta']['tmp_name'], $ruta_destino)) {
        $foto_url = $ruta_destino;
    } else {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "No se pudo guardar la imagen del acta."]);
        exit;
    }
}

// 4. Inserción segura en PostgreSQL mediante sentencias preparadas
try {
    // El "ON CONFLICT" es vital para la idempotencia: si el personero pulsa el botón dos veces por error, actualiza en vez de duplicar.
    $sql = "INSERT INTO resultado (mesa_id, votos_somos_peru, votos_blancos, votos_nulos, foto_acta_url, fecha_registro) 
            VALUES (:mesa_id, :votos_sp, :votos_blancos, :votos_nulos, :foto_url, NOW())
            ON CONFLICT (mesa_id) DO UPDATE SET 
                votos_somos_peru = EXCLUDED.votos_somos_peru,
                foto_acta_url = EXCLUDED.foto_acta_url,
                fecha_registro = NOW()";

    $stmt = $pdo->prepare($sql);
    
    // Enlazar los parámetros
    $stmt->bindParam(':mesa_id', $mesa_id, PDO::PARAM_INT);
    $stmt->bindParam(':votos_sp', $votos_sp, PDO::PARAM_INT);
    $stmt->bindParam(':votos_blancos', $votos_blancos, PDO::PARAM_INT);
    $stmt->bindParam(':votos_nulos', $votos_nulos, PDO::PARAM_INT);
    $stmt->bindParam(':foto_url', $foto_url, PDO::PARAM_STR);
    
    $stmt->execute();

    echo json_encode(["status" => "success", "message" => "Acta y votos registrados correctamente."]);

} catch (PDOException $e) {
    http_response_code(500);
    // En producción, no envíes $e->getMessage() al cliente, guárdalo en un log interno
    echo json_encode(["status" => "error", "message" => "Error al guardar en la base de datos."]);
}
?>