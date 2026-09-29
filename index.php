<?php
session_start();

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

    <img
        src="img/SOMOS PERU.png"
        alt="Logo Somos Perú"
        class="logo-partido"
    >

    <h1>Transmisión de Resultados</h1>
    <h2>Partido Democrático Somos Perú</h2>

</header>


<main class="contenedor">

    <!-- PROGRESO -->

    <div class="wizard-progreso">

        <div class="paso-indicador activo" data-paso="1">
            <span>1</span>
            <small>Ubicación</small>
        </div>

        <div class="linea"></div>

        <div class="paso-indicador" data-paso="2">
            <span>2</span>
            <small>Mesa</small>
        </div>

        <div class="linea"></div>

        <div class="paso-indicador" data-paso="3">
            <span>3</span>
            <small>Evidencia</small>
        </div>

        <div class="linea"></div>

        <div class="paso-indicador" data-paso="4">
            <span>4</span>
            <small>Confirmar</small>
        </div>

    </div>


    <form
        id="formularioActa"
        enctype="multipart/form-data"
    >

        <!-- =====================================
             PASO 1 - UBICACIÓN
        ====================================== -->

        <section
            class="paso-formulario activo"
            data-paso="1"
        >

            <div class="titulo-paso">

                <span class="numero-paso">
                    1
                </span>

                <div>
                    <h3>
                        Ubicación de la mesa
                    </h3>

                    <p>
                        Selecciona región, provincia y distrito.
                    </p>
                </div>

            </div>


            <div class="grupo-campo">

                <label for="region">
                    Región
                </label>

                <select
                    id="region"
                    name="region"
                    required
                >

                    <option value="">
                        Selecciona una región
                    </option>

                    <option value="Pasco">
                        Pasco
                    </option>

                </select>

            </div>


            <div class="grupo-campo">

                <label for="provincia">
                    Provincia
                </label>

                <select
                    id="provincia"
                    name="provincia"
                    required
                    disabled
                >

                    <option value="">
                        Selecciona una provincia
                    </option>

                </select>

            </div>


            <div class="grupo-campo">

                <label for="distrito">
                    Distrito
                </label>

                <select
                    id="distrito"
                    name="distrito"
                    required
                    disabled
                >

                    <option value="">
                        Selecciona un distrito
                    </option>

                </select>

            </div>


            <div class="acciones-paso">

                <button
                    type="button"
                    class="btn-principal btn-siguiente"
                    data-siguiente="2"
                >
                    Continuar
                </button>

            </div>

        </section>


        <!-- =====================================
             PASO 2 - MESA Y VOTOS
        ====================================== -->

        <section
            class="paso-formulario"
            data-paso="2"
        >

            <div class="titulo-paso">

                <span class="numero-paso">
                    2
                </span>

                <div>

                    <h3>
                        Mesa y resultados
                    </h3>

                    <p>
                        Ingresa el número de mesa y los votos registrados.
                    </p>

                </div>

            </div>


            <div class="resumen-seleccion">

                <span>
                    Ubicación seleccionada
                </span>

                <strong id="resumenUbicacion">
                    -
                </strong>

            </div>


            <div class="grupo-campo">

    <label for="total_votos">
        Total de votos emitidos
    </label>

    <input
        type="number"
        id="total_votos"
        name="total_votos"
        required
        min="0"
        inputmode="numeric"
        placeholder="Ej: 275"
    >

</div>


            <fieldset class="grupo-votos">

                <legend>
                    Resultados de la Mesa
                </legend>


                <div class="grupo-campo">

                    <label for="votos_somos_peru">
                        Votos Somos Perú
                    </label>

                    <input
                        type="number"
                        id="votos_somos_peru"
                        name="votos_somos_peru"
                        required
                        min="0"
                        inputmode="numeric"
                        placeholder="0"
                    >

                </div>


                <div class="grupo-campo">

                    <label for="votos_blancos">
                        Votos en Blanco
                    </label>

                    <input
                        type="number"
                        id="votos_blancos"
                        name="votos_blancos"
                        required
                        min="0"
                        inputmode="numeric"
                        placeholder="0"
                    >

                </div>


                <div class="grupo-campo">

                    <label for="votos_nulos">
                        Votos Nulos
                    </label>

                    <input
                        type="number"
                        id="votos_nulos"
                        name="votos_nulos"
                        required
                        min="0"
                        inputmode="numeric"
                        placeholder="0"
                    >

                </div>

            </fieldset>


            <div class="acciones-paso dos-botones">

                <button
                    type="button"
                    class="btn-secundario btn-anterior"
                    data-anterior="1"
                >
                    Atrás
                </button>

                <button
                    type="button"
                    class="btn-principal btn-siguiente"
                    data-siguiente="3"
                >
                    Continuar
                </button>

            </div>

        </section>


        <!-- =====================================
             PASO 3 - EVIDENCIA
        ====================================== -->

        <section
            class="paso-formulario"
            data-paso="3"
        >

            <div class="titulo-paso">

                <span class="numero-paso">
                    3
                </span>

                <div>

                    <h3>
                        Evidencia del acta
                    </h3>

                    <p>
                        Adjunta una fotografía clara y completa del acta.
                    </p>

                </div>

            </div>


            <div class="resumen-seleccion">

                <span>
                    Mesa registrada
                </span>

                <strong id="resumenMesaEvidencia">
                    -
                </strong>

            </div>


            <div class="grupo-campo">

                <label for="foto_acta">
                    Fotografía del Acta
                </label>

                <div class="zona-foto">

                    <input
                        type="file"
                        id="foto_acta"
                        name="foto_acta"
                        accept="image/jpeg,image/png,image/webp"
                        required
                    >

                    <p>
                        Formatos permitidos:
                        JPG, PNG o WEBP
                    </p>

                </div>

            </div>


            <div
                id="previewContainer"
                class="preview-container oculto"
            >

                <span>
                    Vista previa
                </span>

                <img
                    id="previewImagen"
                    src=""
                    alt="Vista previa del acta"
                >

            </div>


            <div class="acciones-paso dos-botones">

                <button
                    type="button"
                    class="btn-secundario btn-anterior"
                    data-anterior="2"
                >
                    Atrás
                </button>

                <button
                    type="button"
                    class="btn-principal btn-siguiente"
                    data-siguiente="4"
                >
                    Revisar datos
                </button>

            </div>

        </section>


        <!-- =====================================
             PASO 4 - CONFIRMACIÓN
        ====================================== -->

        <section
            class="paso-formulario"
            data-paso="4"
        >

            <div class="titulo-paso">

                <span class="numero-paso">
                    4
                </span>

                <div>

                    <h3>
                        Revisar y confirmar
                    </h3>

                    <p>
                        Verifica cuidadosamente la información antes de enviarla.
                    </p>

                </div>

            </div>


            <!-- UBICACIÓN -->

            <div class="tarjeta-resumen">

                <div class="cabecera-resumen">
                    Ubicación
                </div>


                <div class="dato-resumen">

                    <span>
                        Región
                    </span>

                    <strong id="confirmarRegion">
                        -
                    </strong>

                </div>

                


                <div class="dato-resumen">

                    <span>
                        Provincia
                    </span>

                    <strong id="confirmarProvincia">
                        -
                    </strong>

                </div>


                <div class="dato-resumen">

                    <span>
                        Distrito
                    </span>

                    <strong id="confirmarDistrito">
                        -
                    </strong>

                </div>

            </div>


            <!-- MESA Y VOTOS -->

            <div class="tarjeta-resumen">

                <div class="cabecera-resumen">
                    Mesa y resultados
                </div>


                <div class="dato-resumen destacado">

                    <span>
                        Número de Mesa
                    </span>

                    <strong id="confirmarMesa">
                        -
                    </strong>

                </div>


                <div class="dato-resumen">

                    <span>
                        Votos Somos Perú
                    </span>

                    <strong id="confirmarSP">
                        0
                    </strong>

                </div>


                <div class="dato-resumen">

                    <span>
                        Votos en Blanco
                    </span>

                    <strong id="confirmarBlancos">
                        0
                    </strong>

                </div>


                <div class="dato-resumen">

                    <span>
                        Votos Nulos
                    </span>

                    <strong id="confirmarNulos">
                        0
                    </strong>

                </div>


                <div class="dato-resumen total">

                    <span>
                        Total registrado
                    </span>

                    <strong id="confirmarTotal">
                        0
                    </strong>

                </div>

                <div class="dato-resumen total">

    <span>
        Total de votos emitidos
    </span>

    <strong id="confirmarTotal">
        0
    </strong>

</div>

            </div>


            <!-- FOTO -->

            <div class="tarjeta-resumen">

                <div class="cabecera-resumen">
                    Evidencia
                </div>


                <div class="imagen-confirmacion">

                    <img
                        id="imagenConfirmacion"
                        src=""
                        alt="Fotografía del acta"
                    >

                </div>

            </div>


            <div class="aviso-confirmacion">

                <strong>
                    Verifica la información.
                </strong>

                <p>
                    Al confirmar, el acta será enviada al sistema.
                </p>

            </div>


            <div class="acciones-paso dos-botones">

                <button
                    type="button"
                    class="btn-secundario btn-anterior"
                    data-anterior="3"
                >
                    Corregir
                </button>


                <button
                    type="submit"
                    id="btnEnviar"
                    class="btn-confirmar"
                >
                    Confirmar y enviar
                </button>

            </div>


            <div id="mensajeNotificacion"></div>

        </section>

    </form>

</main>


<script src="js/app.js"></script>

</body>
</html>