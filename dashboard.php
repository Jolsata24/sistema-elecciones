<?php

session_start();

require_once 'api/conexion.php';


/* =========================================================
   VALIDAR SESIÓN Y ROL
========================================================= */

if (
    !isset($_SESSION['usuario_id']) ||
    !isset($_SESSION['rol']) ||
    $_SESSION['rol'] !== 'admin'
) {

    header("Location: login.html");
    exit;
}


/* =========================================================
   OBTENER TOTALES
========================================================= */

$stmtTotales = $pdo->query("
    SELECT

        COUNT(*) AS mesas_registradas,

        COALESCE(
            SUM(votos_somos_peru),
            0
        ) AS total_sp,

        COALESCE(
            SUM(votos_blancos),
            0
        ) AS total_blancos,

        COALESCE(
            SUM(votos_nulos),
            0
        ) AS total_nulos,

        COALESCE(
            SUM(
                votos_somos_peru
                +
                votos_blancos
                +
                votos_nulos
            ),
            0
        ) AS total_votos

    FROM resultado
");


$totales = $stmtTotales->fetch();


/* =========================================================
   VARIABLES DE INDICADORES
========================================================= */

$mesasRegistradas =
    (int) ($totales['mesas_registradas'] ?? 0);

$totalSomosPeru =
    (int) ($totales['total_sp'] ?? 0);

$totalBlancos =
    (int) ($totales['total_blancos'] ?? 0);

$totalNulos =
    (int) ($totales['total_nulos'] ?? 0);

$totalVotos =
    (int) ($totales['total_votos'] ?? 0);


/* =========================================================
   CALCULAR PORCENTAJE
========================================================= */

$porcentajeSomosPeru = 0;

if ($totalVotos > 0) {

    $porcentajeSomosPeru =
        ($totalSomosPeru / $totalVotos) * 100;
}


/* =========================================================
   OBTENER ACTAS / MESAS
========================================================= */

$stmtMesas = $pdo->query("
    SELECT

        id,
        mesa_id,
        region,
        provincia,
        distrito,
        votos_somos_peru,
        votos_blancos,
        votos_nulos,
        foto_acta_url,
        fecha_registro

    FROM resultado

    ORDER BY fecha_registro DESC
");


$mesas = $stmtMesas->fetchAll();

?>
<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Dashboard - Transmisión de Actas
    </title>

    <link
        rel="stylesheet"
        href="css/global.css"
    >

    <link
        rel="stylesheet"
        href="css/dashboard.css"
    >

</head>


<body>


<!-- =====================================================
     CABECERA
===================================================== -->

<header class="dashboard-header">

    <div>

        <h1>
            Dashboard de Resultados
        </h1>

        <p>
            Información registrada mediante las actas ingresadas al sistema
        </p>

    </div>


    <div>

        <span class="usuario-admin">

            <?php
            echo htmlspecialchars(
                $_SESSION['nombres'] ?? 'Administrador'
            );
            ?>

        </span>


        <a
            href="api/logout.php"
            class="enlace-salir"
        >
            Cerrar sesión
        </a>

    </div>

</header>



<main class="dashboard-container">


    <!-- =================================================
         INDICADORES PRINCIPALES
    ================================================== -->

    <section class="kpi-grid">


        <!-- TOTAL DE VOTOS -->

        <article class="kpi-card">

            <h3>
                Total votos registrados
            </h3>


            <div class="numero">

                <?php
                echo number_format(
                    $totalVotos,
                    0,
                    ',',
                    '.'
                );
                ?>

            </div>


            <small>
                Total digitado en las actas registradas
            </small>

        </article>



        <!-- VOTOS SOMOS PERÚ -->

        <article class="kpi-card">

            <h3>
                Votos Somos Perú
            </h3>


            <div class="numero">

                <?php
                echo number_format(
                    $totalSomosPeru,
                    0,
                    ',',
                    '.'
                );
                ?>

            </div>


            <small>
                Acumulado registrado en el sistema
            </small>

        </article>



        <!-- PORCENTAJE -->

        <article class="kpi-card porcentaje">

            <h3>
                % sobre votos registrados
            </h3>


            <div class="numero">

                <?php
                echo number_format(
                    $porcentajeSomosPeru,
                    2,
                    ',',
                    '.'
                );
                ?>%

            </div>


            <div class="barra-porcentaje">

                <div
                    class="barra-progreso"
                    style="
                        width:
                        <?php
                        echo min(
                            100,
                            max(
                                0,
                                $porcentajeSomosPeru
                            )
                        );
                        ?>%;
                    "
                ></div>

            </div>


            <small>
                Votos Somos Perú respecto al total registrado
            </small>

        </article>



        <!-- MESAS -->

        <article class="kpi-card rojo">

            <h3>
                Mesas registradas
            </h3>


            <div class="numero">

                <?php
                echo number_format(
                    $mesasRegistradas,
                    0,
                    ',',
                    '.'
                );
                ?>

            </div>


            <small>
                Actas ingresadas al sistema
            </small>

        </article>


    </section>



    <!-- =================================================
         INDICADORES SECUNDARIOS
    ================================================== -->

    <section class="kpi-secundarios">


        <div class="mini-kpi">

            <span>
                Votos en blanco
            </span>

            <strong>

                <?php
                echo number_format(
                    $totalBlancos,
                    0,
                    ',',
                    '.'
                );
                ?>

            </strong>

        </div>



        <div class="mini-kpi">

            <span>
                Votos nulos
            </span>

            <strong>

                <?php
                echo number_format(
                    $totalNulos,
                    0,
                    ',',
                    '.'
                );
                ?>

            </strong>

        </div>



        <div class="mini-kpi">

            <span>
                Blancos + nulos
            </span>

            <strong>

                <?php
                echo number_format(
                    $totalBlancos + $totalNulos,
                    0,
                    ',',
                    '.'
                );
                ?>

            </strong>

        </div>


    </section>



    <!-- =================================================
         CABECERA TABLA
    ================================================== -->

    <section class="seccion-tabla">


        <div class="cabecera-tabla">

            <div>

                <h2>
                    Actas registradas
                </h2>

                <p>
                    Detalle de la información ingresada por mesa
                </p>

            </div>


            <a
                href="api/exportar_excel.php"
                class="btn-exportar"
            >
                Exportar Excel
            </a>

        </div>



        <!-- =================================================
             TABLA
        ================================================== -->

        <div class="tabla-responsiva">

            <table>

                <thead>

                    <tr>

                        <th>
                            Mesa
                        </th>

                        <th>
                            Región
                        </th>

                        <th>
                            Provincia
                        </th>

                        <th>
                            Distrito
                        </th>

                        <th>
                            Somos Perú
                        </th>

                        <th>
                            Blancos
                        </th>

                        <th>
                            Nulos
                        </th>

                        <th>
                            Total
                        </th>

                        <th>
                            % SP
                        </th>

                        <th>
                            Fecha
                        </th>

                        <th>
                            Evidencia
                        </th>

                    </tr>

                </thead>


                <tbody>


                <?php if (!empty($mesas)): ?>


                    <?php foreach ($mesas as $mesa): ?>


                        <?php

                        /*
                        Total registrado en esta mesa
                        */

                        $totalMesa =
                            (int) $mesa['votos_somos_peru']
                            +
                            (int) $mesa['votos_blancos']
                            +
                            (int) $mesa['votos_nulos'];


                        /*
                        Porcentaje SP en esta mesa
                        */

                        $porcentajeMesa = 0;


                        if ($totalMesa > 0) {

                            $porcentajeMesa =
                                (
                                    (int) $mesa['votos_somos_peru']
                                    /
                                    $totalMesa
                                )
                                * 100;
                        }

                        ?>


                        <tr>


                            <!-- MESA -->

                            <td>

                                <strong>

                                    <?php
                                    echo htmlspecialchars(
                                        (string) $mesa['mesa_id']
                                    );
                                    ?>

                                </strong>

                            </td>



                            <!-- REGIÓN -->

                            <td>

                                <?php
                                echo htmlspecialchars(
                                    $mesa['region']
                                );
                                ?>

                            </td>



                            <!-- PROVINCIA -->

                            <td>

                                <?php
                                echo htmlspecialchars(
                                    $mesa['provincia']
                                );
                                ?>

                            </td>



                            <!-- DISTRITO -->

                            <td>

                                <?php
                                echo htmlspecialchars(
                                    $mesa['distrito']
                                );
                                ?>

                            </td>



                            <!-- SOMOS PERÚ -->

                            <td>

                                <strong>

                                    <?php
                                    echo number_format(
                                        (int) $mesa['votos_somos_peru'],
                                        0,
                                        ',',
                                        '.'
                                    );
                                    ?>

                                </strong>

                            </td>



                            <!-- BLANCOS -->

                            <td>

                                <?php
                                echo number_format(
                                    (int) $mesa['votos_blancos'],
                                    0,
                                    ',',
                                    '.'
                                );
                                ?>

                            </td>



                            <!-- NULOS -->

                            <td>

                                <?php
                                echo number_format(
                                    (int) $mesa['votos_nulos'],
                                    0,
                                    ',',
                                    '.'
                                );
                                ?>

                            </td>



                            <!-- TOTAL -->

                            <td>

                                <strong>

                                    <?php
                                    echo number_format(
                                        $totalMesa,
                                        0,
                                        ',',
                                        '.'
                                    );
                                    ?>

                                </strong>

                            </td>



                            <!-- PORCENTAJE -->

                            <td>

                                <span class="porcentaje-mesa">

                                    <?php
                                    echo number_format(
                                        $porcentajeMesa,
                                        2,
                                        ',',
                                        '.'
                                    );
                                    ?>%

                                </span>

                            </td>



                            <!-- FECHA -->

                            <td>

                                <?php

                                if (!empty($mesa['fecha_registro'])) {

                                    echo date(
                                        'd/m/Y H:i',
                                        strtotime(
                                            $mesa['fecha_registro']
                                        )
                                    );

                                } else {

                                    echo '-';

                                }

                                ?>

                            </td>



                            <!-- EVIDENCIA -->

                            <td>


                                <?php if (!empty($mesa['foto_acta_url'])): ?>


                                    <button
                                        type="button"
                                        class="btn-ver"
                                        data-imagen="<?php
                                            echo htmlspecialchars(
                                                $mesa['foto_acta_url'],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            );
                                        ?>"
                                        data-mesa="<?php
                                            echo htmlspecialchars(
                                                (string) $mesa['mesa_id'],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            );
                                        ?>"
                                    >
                                        Ver Acta
                                    </button>


                                <?php else: ?>


                                    <span>
                                        Sin evidencia
                                    </span>


                                <?php endif; ?>


                            </td>


                        </tr>


                    <?php endforeach; ?>


                <?php else: ?>


                    <tr>

                        <td
                            colspan="11"
                            class="sin-registros"
                        >

                            Aún no se han registrado actas.

                        </td>

                    </tr>


                <?php endif; ?>


                </tbody>

            </table>

        </div>

    </section>


</main>



<!-- =====================================================
     MODAL PARA VER ACTA
===================================================== -->

<div
    id="modalActa"
    class="modal"
    aria-hidden="true"
>


    <div class="modal-contenido">


        <button
            type="button"
            class="modal-cerrar"
            id="cerrarModal"
            aria-label="Cerrar"
        >
            ×
        </button>


        <div class="modal-header">

            <h2>
                Evidencia del Acta
            </h2>

            <p id="modalMesa">
                Mesa
            </p>

        </div>


        <div class="modal-imagen">

            <img
                id="imagenActa"
                src=""
                alt="Fotografía del acta"
            >

        </div>


    </div>

</div>



<script>

/* =========================================================
   MODAL DE ACTA
========================================================= */

document.addEventListener(
    'DOMContentLoaded',
    () => {

        const modal =
            document.getElementById(
                'modalActa'
            );

        const imagen =
            document.getElementById(
                'imagenActa'
            );

        const modalMesa =
            document.getElementById(
                'modalMesa'
            );

        const cerrar =
            document.getElementById(
                'cerrarModal'
            );


        /*
        Abrir modal
        */

        document
            .querySelectorAll('.btn-ver')
            .forEach(boton => {

                boton.addEventListener(
                    'click',
                    () => {

                        imagen.src =
                            boton.dataset.imagen;

                        modalMesa.textContent =
                            'Mesa ' +
                            boton.dataset.mesa;

                        modal.classList.add(
                            'activo'
                        );

                        modal.setAttribute(
                            'aria-hidden',
                            'false'
                        );

                        document.body.style.overflow =
                            'hidden';

                    }
                );

            });


        /*
        Cerrar
        */

        function cerrarModal() {

            modal.classList.remove(
                'activo'
            );

            modal.setAttribute(
                'aria-hidden',
                'true'
            );

            imagen.src = '';

            document.body.style.overflow =
                '';

        }


        cerrar.addEventListener(
            'click',
            cerrarModal
        );


        /*
        Cerrar pulsando fondo
        */

        modal.addEventListener(
            'click',
            (event) => {

                if (event.target === modal) {

                    cerrarModal();

                }

            }
        );


        /*
        Cerrar con ESC
        */

        document.addEventListener(
            'keydown',
            (event) => {

                if (
                    event.key === 'Escape' &&
                    modal.classList.contains(
                        'activo'
                    )
                ) {

                    cerrarModal();

                }

            }
        );

    }
);

</script>


</body>
</html>