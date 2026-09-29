<?php

session_start();

header('Content-Type: application/json; charset=utf-8');

require_once 'conexion.php';


/* =========================================================
   1. VALIDAR SESIÓN
========================================================= */

if (!isset($_SESSION['usuario_id'])) {

    http_response_code(401);

    echo json_encode([
        'status' => 'error',
        'message' => 'Tu sesión ha expirado. Inicia sesión nuevamente.'
    ]);

    exit;
}


/* =========================================================
   2. VALIDAR MÉTODO HTTP
========================================================= */

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    http_response_code(405);

    echo json_encode([
        'status' => 'error',
        'message' => 'Método no permitido.'
    ]);

    exit;
}


/* =========================================================
   3. RECIBIR UBICACIÓN
========================================================= */

$region =
    trim($_POST['region'] ?? '');

$provincia =
    trim($_POST['provincia'] ?? '');

$distrito =
    trim($_POST['distrito'] ?? '');


/* =========================================================
   4. RECIBIR DATOS NUMÉRICOS
========================================================= */

$mesa_id = filter_input(
    INPUT_POST,
    'mesa_id',
    FILTER_VALIDATE_INT
);

$votos_sp = filter_input(
    INPUT_POST,
    'votos_somos_peru',
    FILTER_VALIDATE_INT
);

$votos_blancos = filter_input(
    INPUT_POST,
    'votos_blancos',
    FILTER_VALIDATE_INT
);

$votos_nulos = filter_input(
    INPUT_POST,
    'votos_nulos',
    FILTER_VALIDATE_INT
);

$total_votos = filter_input(
    INPUT_POST,
    'total_votos',
    FILTER_VALIDATE_INT
);


/* =========================================================
   5. VALIDAR UBICACIÓN
========================================================= */

if (
    $region === '' ||
    $provincia === '' ||
    $distrito === ''
) {

    http_response_code(400);

    echo json_encode([
        'status' => 'error',
        'message' => 'Debes seleccionar región, provincia y distrito.'
    ]);

    exit;
}


/* =========================================================
   6. VALIDAR MESA
========================================================= */

if (
    $mesa_id === false ||
    $mesa_id === null ||
    $mesa_id <= 0
) {

    http_response_code(400);

    echo json_encode([
        'status' => 'error',
        'message' => 'El número de mesa no es válido.'
    ]);

    exit;
}


/* =========================================================
   7. VALIDAR CAMPOS DE VOTOS
========================================================= */

if (
    $votos_sp === false ||
    $votos_sp === null ||
    $votos_blancos === false ||
    $votos_blancos === null ||
    $votos_nulos === false ||
    $votos_nulos === null ||
    $total_votos === false ||
    $total_votos === null
) {

    http_response_code(400);

    echo json_encode([
        'status' => 'error',
        'message' => 'Completa correctamente todos los datos de votación.'
    ]);

    exit;
}


/* =========================================================
   8. VALIDAR VALORES NEGATIVOS
========================================================= */

if (
    $votos_sp < 0 ||
    $votos_blancos < 0 ||
    $votos_nulos < 0 ||
    $total_votos < 0
) {

    http_response_code(400);

    echo json_encode([
        'status' => 'error',
        'message' => 'Los valores de votos no pueden ser negativos.'
    ]);

    exit;
}


/* =========================================================
   9. VALIDAR CONSISTENCIA DEL TOTAL
========================================================= */

$minimo_registrado =
    $votos_sp +
    $votos_blancos +
    $votos_nulos;


if ($total_votos < $minimo_registrado) {

    http_response_code(400);

    echo json_encode([
        'status' => 'error',
        'message' =>
            'El total de votos no puede ser menor que la suma de Somos Perú, blancos y nulos.'
    ]);

    exit;
}


/* =========================================================
   10. VALIDAR FOTOGRAFÍA
========================================================= */

if (
    !isset($_FILES['foto_acta']) ||
    $_FILES['foto_acta']['error'] !== UPLOAD_ERR_OK
) {

    http_response_code(400);

    echo json_encode([
        'status' => 'error',
        'message' => 'Es obligatorio adjuntar la fotografía del acta.'
    ]);

    exit;
}


$archivo = $_FILES['foto_acta'];


/* =========================================================
   11. VALIDAR TAMAÑO
========================================================= */

$max_size =
    5 * 1024 * 1024;


if ($archivo['size'] > $max_size) {

    http_response_code(400);

    echo json_encode([
        'status' => 'error',
        'message' => 'La fotografía no puede superar los 5 MB.'
    ]);

    exit;
}


/* =========================================================
   12. VALIDAR TIPO MIME REAL
========================================================= */

$finfo =
    new finfo(
        FILEINFO_MIME_TYPE
    );


$mime =
    $finfo->file(
        $archivo['tmp_name']
    );


$tipos_permitidos = [

    'image/jpeg' =>
        'jpg',

    'image/png' =>
        'png',

    'image/webp' =>
        'webp'

];


if (!isset($tipos_permitidos[$mime])) {

    http_response_code(400);

    echo json_encode([
        'status' => 'error',
        'message' =>
            'Formato no permitido. Solo se aceptan imágenes JPG, PNG o WEBP.'
    ]);

    exit;
}


/* =========================================================
   13. CREAR DIRECTORIO
========================================================= */

$upload_dir =
    '../uploads/actas/';


if (!is_dir($upload_dir)) {

    $creado =
        mkdir(
            $upload_dir,
            0755,
            true
        );


    if (!$creado) {

        http_response_code(500);

        echo json_encode([
            'status' => 'error',
            'message' =>
                'No se pudo crear el directorio para almacenar las actas.'
        ]);

        exit;
    }

}


/* =========================================================
   14. GENERAR NOMBRE SEGURO
========================================================= */

$extension =
    $tipos_permitidos[$mime];


$nombre_archivo =
    'acta_mesa_' .
    $mesa_id .
    '_' .
    bin2hex(
        random_bytes(8)
    ) .
    '.' .
    $extension;


/* Ruta física del servidor */

$ruta_destino_fisica =
    $upload_dir .
    $nombre_archivo;


/* Ruta que guardaremos en MySQL */

$foto_url =
    'uploads/actas/' .
    $nombre_archivo;


/* =========================================================
   15. MOVER LA FOTOGRAFÍA
========================================================= */

if (
    !move_uploaded_file(
        $archivo['tmp_name'],
        $ruta_destino_fisica
    )
) {

    http_response_code(500);

    echo json_encode([
        'status' => 'error',
        'message' =>
            'No se pudo guardar la fotografía del acta en el servidor.'
    ]);

    exit;
}


/* =========================================================
   16. INSERTAR O ACTUALIZAR EN MYSQL
========================================================= */

try {

    $sql = "

        INSERT INTO resultado
        (
            mesa_id,
            region,
            provincia,
            distrito,
            votos_somos_peru,
            votos_blancos,
            votos_nulos,
            total_votos,
            foto_acta_url,
            fecha_registro
        )

        VALUES
        (
            :mesa_id,
            :region,
            :provincia,
            :distrito,
            :votos_sp,
            :votos_blancos,
            :votos_nulos,
            :total_votos,
            :foto_url,
            NOW()
        )

        ON DUPLICATE KEY UPDATE

            region =
                VALUES(region),

            provincia =
                VALUES(provincia),

            distrito =
                VALUES(distrito),

            votos_somos_peru =
                VALUES(votos_somos_peru),

            votos_blancos =
                VALUES(votos_blancos),

            votos_nulos =
                VALUES(votos_nulos),

            total_votos =
                VALUES(total_votos),

            foto_acta_url =
                VALUES(foto_acta_url),

            fecha_registro =
                NOW()

    ";


    $stmt =
        $pdo->prepare(
            $sql
        );


    $stmt->execute([

        ':mesa_id' =>
            $mesa_id,

        ':region' =>
            $region,

        ':provincia' =>
            $provincia,

        ':distrito' =>
            $distrito,

        ':votos_sp' =>
            $votos_sp,

        ':votos_blancos' =>
            $votos_blancos,

        ':votos_nulos' =>
            $votos_nulos,

        ':total_votos' =>
            $total_votos,

        ':foto_url' =>
            $foto_url

    ]);


/* =========================================================
   17. RESPUESTA EXITOSA
========================================================= */

    $otros_votos =
        $total_votos
        -
        $votos_sp
        -
        $votos_blancos
        -
        $votos_nulos;


    $porcentaje_sp =
        0;


    if ($total_votos > 0) {

        $porcentaje_sp =
            (
                $votos_sp
                /
                $total_votos
            )
            * 100;

    }


    echo json_encode([

        'status' =>
            'success',

        'message' =>
            "Acta de la mesa $mesa_id registrada correctamente.",

        'data' => [

            'mesa_id' =>
                $mesa_id,

            'region' =>
                $region,

            'provincia' =>
                $provincia,

            'distrito' =>
                $distrito,

            'votos_somos_peru' =>
                $votos_sp,

            'votos_blancos' =>
                $votos_blancos,

            'votos_nulos' =>
                $votos_nulos,

            'total_votos' =>
                $total_votos,

            'otros_votos' =>
                $otros_votos,

            'porcentaje_somos_peru' =>
                round(
                    $porcentaje_sp,
                    2
                ),

            'foto_acta_url' =>
                $foto_url

        ]

    ]);


/* =========================================================
   18. ERROR DE BASE DE DATOS
========================================================= */

} catch (PDOException $e) {


    /*
    Si MySQL falla después de subir la imagen,
    eliminamos la imagen nueva.
    */

    if (
        file_exists(
            $ruta_destino_fisica
        )
    ) {

        unlink(
            $ruta_destino_fisica
        );

    }


    error_log(
        'Error guardar_acta.php: ' .
        $e->getMessage()
    );


    http_response_code(500);


    echo json_encode([

        'status' =>
            'error',

        'message' =>
            'Ocurrió un error al guardar el acta en la base de datos.'

    ]);

}

?>