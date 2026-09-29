<?php
session_start();
require_once 'conexion.php';

// Validar que solo el administrador pueda descargar la data
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'admin') {
    die("Acceso denegado.");
}

// Configurar las cabeceras para forzar la descarga como archivo Excel
header("Content-Type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=Resultados_Mesas_" . date('Ymd_His') . ".xls");
header("Pragma: no-cache");
header("Expires: 0");

// Imprimir el BOM (Byte Order Mark) para que Excel reconozca las tildes y eñes (UTF-8)
echo "\xEF\xBB\xBF";

try {
    $stmtMesas = $pdo->query("SELECT r.mesa_id, r.votos_somos_peru, r.votos_blancos, r.votos_nulos, r.fecha_registro, u.nombres as personero 
                              FROM resultado r 
                              LEFT JOIN usuario u ON u.id = r.mesa_id 
                              ORDER BY r.fecha_registro DESC");
    $mesas = $stmtMesas->fetchAll();
} catch (PDOException $e) {
    die("Error al extraer datos.");
}
?>
<!-- Estructura de la tabla que Excel interpretará -->
<table border="1">
    <thead>
        <tr>
            <th style="background-color: #003366; color: white;">Mesa N°</th>
            <th style="background-color: #003366; color: white;">Personero Asignado</th>
            <th style="background-color: #003366; color: white;">Votos Somos Perú</th>
            <th style="background-color: #003366; color: white;">Votos en Blanco</th>
            <th style="background-color: #003366; color: white;">Votos Nulos</th>
            <th style="background-color: #003366; color: white;">Fecha y Hora de Registro</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($mesas as $mesa): ?>
        <tr>
            <td><?php echo htmlspecialchars($mesa['mesa_id']); ?></td>
            <td><?php echo htmlspecialchars($mesa['personero'] ?? 'No asignado'); ?></td>
            <td><?php echo htmlspecialchars($mesa['votos_somos_peru']); ?></td>
            <td><?php echo htmlspecialchars($mesa['votos_blancos']); ?></td>
            <td><?php echo htmlspecialchars($mesa['votos_nulos']); ?></td>
            <td><?php echo date('d/m/Y H:i', strtotime($mesa['fecha_registro'])); ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>