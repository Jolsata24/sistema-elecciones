<?php 
session_start(); 
// Si no hay un usuario_id en la sesión, lo expulsamos al login 
if (!isset($_SESSION['usuario_id'])) {     
    header("Location: login.html");     
    exit; 
}
?>
<!DOCTYPE html> 
<html lang="es"> 
<head>     
    <meta charset="UTF-8">     
    <meta name="viewport" content="width=device-width, initial-scale=1.0">     
    <title>Transmisión de Actas - Somos Perú</title>     
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/personero.css">
</head> 
<body>     
    <header class="cabecera">
            <img src="img/SOMOS PERU.png" alt="Logo Somos Perú" class="logo-partido" style="width: 60px;">
            <h1>Transmisión de Resultados</h1>
            <h2>Partido Democrático Somos Perú</h2>
        </header>   
    <main class="contenedor">         
        <form id="formularioActa" enctype="multipart/form-data">                          
            <div class="grupo-campo">                 
                <label for="mesa_id">Número de Mesa:</label>                 
                <input type="number" id="mesa_id" name="mesa_id" required placeholder="Ej: 004562">             
            </div>             
            <fieldset class="grupo-votos">                 
                <legend>Resultados de la Mesa</legend>                                  
                <div class="grupo-campo">                     
                    <label for="votos_somos_peru">Votos Somos Perú:</label>                     
                    <input type="number" id="votos_somos_peru" name="votos_somos_peru" required min="0">                 
                </div>                 
                <div class="grupo-campo">                     
                    <label for="votos_blancos">Votos en Blanco:</label>                     
                    <input type="number" id="votos_blancos" name="votos_blancos" required min="0">                 
                </div>                 
                <div class="grupo-campo">                     
                    <label for="votos_nulos">Votos Nulos:</label>                     
                    <input type="number" id="votos_nulos" name="votos_nulos" required min="0">                 
                </div>             
            </fieldset>             
            <div class="grupo-campo">                 
                <label for="foto_acta">Fotografía del Acta (Obligatorio):</label>                 
                <input type="file" id="foto_acta" name="foto_acta" accept="image/*" required>             
            </div>             
            <button type="submit" id="btnEnviar" class="btn-principal">                 
                Enviar Resultados             
            </button>                          
            <div id="mensajeNotificacion"></div>         
        </form>     
    </main>     
    <script src="js/app.js"></script> 
</body> 
</html>