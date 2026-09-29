<?php
session_start();
require_once 'api/conexion.php';

// 1. DOBLE BARRERA DE SEGURIDAD
// Verificamos que esté logueado Y que sea administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'admin') {
    header("Location: login.html");
    exit;
}

// 2. CONSULTAS PARA LOS INDICADORES GLOBALES (KPIs)
try {
    // Calcular el total de votos acumulados
    $stmtTotales = $pdo->query("SELECT 
        SUM(votos_somos_peru) as total_sp, 
        SUM(votos_blancos) as total_blancos, 
        SUM(votos_nulos) as total_nulos,
        COUNT(mesa_id) as mesas_escrutadas
        FROM resultado");
    $totales = $stmtTotales->fetch();

    // 3. CONSULTA PARA LA TABLA DE MESAS
    $stmtMesas = $pdo->query("SELECT r.*, u.nombres as personero 
                              FROM resultado r 
                              LEFT JOIN usuario u ON u.id = r.mesa_id 
                              ORDER BY r.fecha_registro DESC");
    $mesas = $stmtMesas->fetchAll();

} catch (PDOException $e) {
    die("Error al cargar los datos: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Centro de Cómputo - Admin</title>
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <!-- Estilos específicos para el dashboard -->

    <!-- Importar GSAP -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    
</head>
<body>
    <div class="dashboard-container">
        <header style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
            <div>
                <h1 style="color: #003366;">Panel de Control Administrativo</h1>
                <p>Bienvenido, <?php echo htmlspecialchars($_SESSION['nombres']); ?></p>
            </div>
            <a href="api/logout.php" style="color: #cc0000; font-weight: bold; text-decoration: none;">Cerrar Sesión</a>
        </header>

        <!-- Indicadores Principales -->
        <div class="kpi-grid">
            <div class="kpi-card">
                <h3>VOTOS SOMOS PERÚ</h3>
                <div class="numero"><?php echo $totales['total_sp'] ?? 0; ?></div>
            </div>
            <div class="kpi-card">
                <h3>VOTOS BLANCOS</h3>
                <div class="numero"><?php echo $totales['total_blancos'] ?? 0; ?></div>
            </div>
            <div class="kpi-card">
                <h3>VOTOS NULOS</h3>
                <div class="numero"><?php echo $totales['total_nulos'] ?? 0; ?></div>
            </div>
            <div class="kpi-card rojo">
                <h3>MESAS PROCESADAS</h3>
                <div class="numero"><?php echo $totales['mesas_escrutadas'] ?? 0; ?></div>
            </div>
        </div>

        <!-- Grilla de Datos -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
            <h2 style="margin: 0;">Actas Recibidas</h2>
            <a href="api/exportar_excel.php" style="background-color: #107c41; color: white; padding: 10px 15px; border-radius: 6px; text-decoration: none; font-weight: bold; font-size: 0.9rem;">
                📥 Exportar a Excel
            </a>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Mesa N°</th>
                    <th>Somos Perú</th>
                    <th>Blancos</th>
                    <th>Nulos</th>
                    <th>Fecha / Hora</th>
                    <th>Evidencia</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($mesas as $mesa): ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($mesa['mesa_id']); ?></strong></td>
                    <td><?php echo htmlspecialchars($mesa['votos_somos_peru']); ?></td>
                    <td><?php echo htmlspecialchars($mesa['votos_blancos']); ?></td>
                    <td><?php echo htmlspecialchars($mesa['votos_nulos']); ?></td>
                    <td><?php echo date('d/m/Y H:i', strtotime($mesa['fecha_registro'])); ?></td>
                    <td>
                        <a href="<?php echo htmlspecialchars($mesa['foto_acta_url']); ?>" target="_blank" class="btn-ver">Ver Acta</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                
                <?php if(empty($mesas)): ?>
                <tr>
                    <td colspan="6" style="text-align: center; color: #a0aec0;">Aún no se han recibido actas.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <!-- Ventana Modal Oculta -->
    <div id="modalOverlay" class="modal-overlay">
        <div class="modal-content">
            <h3 style="margin-bottom: 15px; color: #003366;">Evidencia Fotográfica</h3>
            <!-- La imagen se inyectará aquí dinámicamente con JS -->
            <img id="imagenActa" src="" alt="Fotografía del Acta">
            <button id="btnCerrarModal" class="btn-cerrar-modal">Cerrar Visualizador</button>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const botonesVer = document.querySelectorAll('.btn-ver');
            const modal = document.getElementById('modalOverlay');
            const imagenModal = document.getElementById('imagenActa');
            const btnCerrar = document.getElementById('btnCerrarModal');

            // Abrir modal
            botonesVer.forEach(boton => {
                boton.addEventListener('click', (e) => {
                    e.preventDefault(); // Evita que el navegador abra una pestaña nueva
                    
                    // Extraer la ruta de la imagen del atributo href
                    const urlImagen = boton.getAttribute('href');
                    imagenModal.src = urlImagen;

                    // Animación de entrada con GSAP
                    gsap.to(modal, { autoAlpha: 1, duration: 0.3 }); // Aparece el fondo oscuro
                    gsap.fromTo('.modal-content', 
                        { scale: 0.7, y: 30, opacity: 0 }, 
                        { scale: 1, y: 0, opacity: 1, duration: 0.5, ease: "back.out(1.5)" }
                    ); // Efecto de rebote sutil en la tarjeta
                });
            });

            // Función para cerrar animado
            const cerrarModal = () => {
                gsap.to('.modal-content', { scale: 0.8, y: 20, opacity: 0, duration: 0.2, ease: "power2.in" });
                gsap.to(modal, { autoAlpha: 0, duration: 0.3, delay: 0.1 }); // Se desvanece
                
                // Limpiar la imagen después de la animación para ahorrar memoria
                setTimeout(() => { imagenModal.src = ''; }, 400);
            };

            // Eventos de cierre
            btnCerrar.addEventListener('click', cerrarModal);
            
            // Cerrar también si el administrador hace clic fuera del recuadro blanco
            modal.addEventListener('click', (e) => {
                if(e.target === modal) cerrarModal();
            });
        });
    </script>
</body>
</html>