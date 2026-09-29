document.addEventListener('DOMContentLoaded', () => {

    /* ==========================================
       ELEMENTOS PRINCIPALES
    ========================================== */

    const formulario = document.getElementById('formularioActa');
    const btnEnviar = document.getElementById('btnEnviar');
    const mensajeDiv = document.getElementById('mensajeNotificacion');

    const region = document.getElementById('region');
    const provincia = document.getElementById('provincia');
    const distrito = document.getElementById('distrito');

    const mesa = document.getElementById('mesa_id');

    const votosSPInput = document.getElementById('votos_somos_peru');
    const votosBlancosInput = document.getElementById('votos_blancos');
    const votosNulosInput = document.getElementById('votos_nulos');
    const totalVotosInput = document.getElementById('total_votos');

    const foto = document.getElementById('foto_acta');

    const previewContainer = document.getElementById('previewContainer');
    const previewImagen = document.getElementById('previewImagen');
    const imagenConfirmacion = document.getElementById('imagenConfirmacion');


    /* ==========================================
       UBICACIONES DE PASCO
    ========================================== */

    const ubicaciones = {

        Pasco: {

            Pasco: [
                'Chaupimarca',
                'Huachón',
                'Huariaca',
                'Huayllay',
                'Ninacaca',
                'Pallanchacra',
                'Paucartambo',
                'San Francisco de Asís de Yarusyacán',
                'Simón Bolívar',
                'Ticlacayán',
                'Tinyahuarco',
                'Vicco',
                'Yanacancha'
            ],

            'Daniel Alcides Carrión': [
                'Yanahuanca',
                'Chacayán',
                'Goyllarisquizga',
                'Paucar',
                'San Pedro de Pillao',
                'Santa Ana de Tusi',
                'Tapuc',
                'Vilcabamba'
            ],

            Oxapampa: [
                'Oxapampa',
                'Chontabamba',
                'Huancabamba',
                'Palcazú',
                'Pozuzo',
                'Puerto Bermúdez',
                'Villa Rica',
                'Constitución'
            ]

        }

    };


    /* ==========================================
       REGIÓN -> PROVINCIA
    ========================================== */

    region.addEventListener('change', () => {

        provincia.innerHTML =
            '<option value="">Selecciona una provincia</option>';

        distrito.innerHTML =
            '<option value="">Selecciona un distrito</option>';

        provincia.disabled = true;
        distrito.disabled = true;

        if (!region.value) {
            return;
        }

        const provincias =
            Object.keys(
                ubicaciones[region.value]
            );

        provincias.forEach(nombre => {

            const option =
                document.createElement('option');

            option.value = nombre;
            option.textContent = nombre;

            provincia.appendChild(option);

        });

        provincia.disabled = false;

    });


    /* ==========================================
       PROVINCIA -> DISTRITO
    ========================================== */

    provincia.addEventListener('change', () => {

        distrito.innerHTML =
            '<option value="">Selecciona un distrito</option>';

        distrito.disabled = true;

        if (!region.value || !provincia.value) {
            return;
        }

        const distritos =
            ubicaciones[region.value][provincia.value];

        distritos.forEach(nombre => {

            const option =
                document.createElement('option');

            option.value = nombre;
            option.textContent = nombre;

            distrito.appendChild(option);

        });

        distrito.disabled = false;

    });


    /* ==========================================
       CAMBIAR PASO
    ========================================== */

    function cambiarPaso(numeroPaso) {

        document
            .querySelectorAll('.paso-formulario')
            .forEach(paso => {

                paso.classList.remove('activo');

            });

        const nuevoPaso =
            document.querySelector(
                `.paso-formulario[data-paso="${numeroPaso}"]`
            );

        if (!nuevoPaso) {

            console.error(
                `No existe el paso ${numeroPaso}`
            );

            return;
        }

        nuevoPaso.classList.add('activo');

        actualizarIndicadores(numeroPaso);

        const contenedor =
            document.querySelector('.contenedor');

        if (contenedor) {

            contenedor.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });

        }

    }


    /* ==========================================
       INDICADORES
    ========================================== */

    function actualizarIndicadores(pasoActual) {

        document
            .querySelectorAll('.paso-indicador')
            .forEach(indicador => {

                const numero =
                    Number(
                        indicador.dataset.paso
                    );

                indicador.classList.remove(
                    'activo',
                    'completado'
                );

                if (numero < pasoActual) {

                    indicador.classList.add(
                        'completado'
                    );

                }

                if (numero === pasoActual) {

                    indicador.classList.add(
                        'activo'
                    );

                }

            });

    }


    /* ==========================================
       VALIDAR PASO 1
    ========================================== */

    function validarPaso1() {

        if (
            !region.value ||
            !provincia.value ||
            !distrito.value
        ) {

            mostrarMensaje(
                'Selecciona región, provincia y distrito.',
                'error'
            );

            return false;

        }

        limpiarMensaje();

        return true;

    }


    /* ==========================================
       VALIDAR PASO 2
    ========================================== */

    function validarPaso2() {

        if (
            !mesa.value ||
            Number(mesa.value) <= 0
        ) {

            mostrarMensaje(
                'Ingresa un número de mesa válido.',
                'error'
            );

            return false;

        }


        if (
            votosSPInput.value === '' ||
            votosBlancosInput.value === '' ||
            votosNulosInput.value === '' ||
            totalVotosInput.value === ''
        ) {

            mostrarMensaje(
                'Completa todos los datos de votación.',
                'error'
            );

            return false;

        }


        const votosSP =
            Number(votosSPInput.value);

        const blancos =
            Number(votosBlancosInput.value);

        const nulos =
            Number(votosNulosInput.value);

        const total =
            Number(totalVotosInput.value);


        if (
            !Number.isInteger(votosSP) ||
            !Number.isInteger(blancos) ||
            !Number.isInteger(nulos) ||
            !Number.isInteger(total)
        ) {

            mostrarMensaje(
                'Los valores de votos deben ser números enteros.',
                'error'
            );

            return false;

        }


        if (
            votosSP < 0 ||
            blancos < 0 ||
            nulos < 0 ||
            total < 0
        ) {

            mostrarMensaje(
                'Los valores no pueden ser negativos.',
                'error'
            );

            return false;

        }


        /*
        Lo registrado de Somos Perú + blancos + nulos
        no puede superar el total emitido.
        */

        const minimoConocido =
            votosSP +
            blancos +
            nulos;


        if (total < minimoConocido) {

            mostrarMensaje(
                'El total de votos no puede ser menor que la suma de Somos Perú, blancos y nulos.',
                'error'
            );

            return false;

        }


        /*
        Tampoco puede haber más votos de Somos Perú
        que el total de votos emitidos.
        */

        if (votosSP > total) {

            mostrarMensaje(
                'Los votos de Somos Perú no pueden superar el total de votos.',
                'error'
            );

            return false;

        }


        limpiarMensaje();

        return true;

    }


    /* ==========================================
       VALIDAR PASO 3
    ========================================== */

    function validarPaso3() {

        if (!foto.files.length) {

            mostrarMensaje(
                'Debes adjuntar la fotografía del acta.',
                'error'
            );

            return false;

        }


        const archivo =
            foto.files[0];


        const tiposPermitidos = [
            'image/jpeg',
            'image/png',
            'image/webp'
        ];


        if (!tiposPermitidos.includes(archivo.type)) {

            mostrarMensaje(
                'La evidencia debe ser JPG, PNG o WEBP.',
                'error'
            );

            return false;

        }


        const limite =
            5 * 1024 * 1024;


        if (archivo.size > limite) {

            mostrarMensaje(
                'La imagen no puede superar los 5 MB.',
                'error'
            );

            return false;

        }


        limpiarMensaje();

        return true;

    }


    /* ==========================================
       RESUMEN UBICACIÓN
    ========================================== */

    function actualizarResumenUbicacion() {

        const texto =
            `${region.value} / ` +
            `${provincia.value} / ` +
            `${distrito.value}`;

        const resumen =
            document.getElementById(
                'resumenUbicacion'
            );

        if (resumen) {

            resumen.textContent = texto;

        }

    }


    /* ==========================================
       RESUMEN MESA PASO 3
    ========================================== */

    function actualizarResumenMesa() {

        const resumenMesa =
            document.getElementById(
                'resumenMesaEvidencia'
            );

        if (resumenMesa) {

            resumenMesa.textContent =
                `Mesa ${mesa.value}`;

        }

    }


    /* ==========================================
       RESUMEN FINAL PASO 4
    ========================================== */

    function actualizarResumenConfirmacion() {

        const votosSP =
            Number(votosSPInput.value);

        const blancos =
            Number(votosBlancosInput.value);

        const nulos =
            Number(votosNulosInput.value);

        const total =
            Number(totalVotosInput.value);


        document.getElementById(
            'confirmarRegion'
        ).textContent =
            region.value;


        document.getElementById(
            'confirmarProvincia'
        ).textContent =
            provincia.value;


        document.getElementById(
            'confirmarDistrito'
        ).textContent =
            distrito.value;


        document.getElementById(
            'confirmarMesa'
        ).textContent =
            mesa.value;


        document.getElementById(
            'confirmarSP'
        ).textContent =
            votosSP;


        document.getElementById(
            'confirmarBlancos'
        ).textContent =
            blancos;


        document.getElementById(
            'confirmarNulos'
        ).textContent =
            nulos;


        document.getElementById(
            'confirmarTotal'
        ).textContent =
            total;

    }


    /* ==========================================
       BOTONES SIGUIENTE
    ========================================== */

    document
        .querySelectorAll('.btn-siguiente')
        .forEach(boton => {

            boton.addEventListener(
                'click',
                () => {

                    const siguiente =
                        Number(
                            boton.dataset.siguiente
                        );


                    /* PASO 1 -> PASO 2 */

                    if (siguiente === 2) {

                        if (!validarPaso1()) {
                            return;
                        }

                        actualizarResumenUbicacion();

                    }


                    /* PASO 2 -> PASO 3 */

                    if (siguiente === 3) {

                        if (!validarPaso2()) {
                            return;
                        }

                        actualizarResumenMesa();

                    }


                    /* PASO 3 -> PASO 4 */

                    if (siguiente === 4) {

                        if (!validarPaso3()) {
                            return;
                        }

                        actualizarResumenConfirmacion();

                    }


                    cambiarPaso(siguiente);

                }
            );

        });


    /* ==========================================
       BOTONES ATRÁS
    ========================================== */

    document
        .querySelectorAll('.btn-anterior')
        .forEach(boton => {

            boton.addEventListener(
                'click',
                () => {

                    const anterior =
                        Number(
                            boton.dataset.anterior
                        );

                    limpiarMensaje();

                    cambiarPaso(anterior);

                }
            );

        });


    /* ==========================================
       PREVIEW DE FOTO
    ========================================== */

    let urlImagenActual = null;


    foto.addEventListener('change', () => {

        const archivo =
            foto.files[0];


        if (!archivo) {

            previewContainer
                .classList
                .add('oculto');

            previewImagen.src = '';
            imagenConfirmacion.src = '';

            return;

        }


        const tiposPermitidos = [
            'image/jpeg',
            'image/png',
            'image/webp'
        ];


        if (!tiposPermitidos.includes(archivo.type)) {

            mostrarMensaje(
                'Selecciona una imagen JPG, PNG o WEBP.',
                'error'
            );

            foto.value = '';

            previewContainer
                .classList
                .add('oculto');

            previewImagen.src = '';
            imagenConfirmacion.src = '';

            return;

        }


        const limite =
            5 * 1024 * 1024;


        if (archivo.size > limite) {

            mostrarMensaje(
                'La imagen no puede superar los 5 MB.',
                'error'
            );

            foto.value = '';

            previewContainer
                .classList
                .add('oculto');

            previewImagen.src = '';
            imagenConfirmacion.src = '';

            return;

        }


        if (urlImagenActual) {

            URL.revokeObjectURL(
                urlImagenActual
            );

        }


        urlImagenActual =
            URL.createObjectURL(
                archivo
            );


        previewImagen.src =
            urlImagenActual;


        imagenConfirmacion.src =
            urlImagenActual;


        previewContainer
            .classList
            .remove('oculto');


        limpiarMensaje();

    });


    /* ==========================================
       ENVÍO FINAL
    ========================================== */

    formulario.addEventListener(
        'submit',
        async (e) => {

            e.preventDefault();


            /*
            Validar todo nuevamente antes de enviar.
            */

            if (!validarPaso1()) {

                cambiarPaso(1);
                return;

            }


            if (!validarPaso2()) {

                cambiarPaso(2);
                return;

            }


            if (!validarPaso3()) {

                cambiarPaso(3);
                return;

            }


            btnEnviar.disabled = true;

            btnEnviar.textContent =
                'Enviando acta...';


            limpiarMensaje();


            const formData =
                new FormData(
                    formulario
                );


            try {

                const response =
                    await fetch(
                        'api/guardar_acta.php',
                        {
                            method: 'POST',
                            body: formData
                        }
                    );


                /*
                Leer respuesta del servidor.
                */

                const textoRespuesta =
                    await response.text();


                let result;


                try {

                    result =
                        JSON.parse(
                            textoRespuesta
                        );

                } catch (error) {

                    console.error(
                        'Respuesta no JSON:',
                        textoRespuesta
                    );

                    throw new Error(
                        'El servidor devolvió una respuesta inválida.'
                    );

                }


                if (
                    response.ok &&
                    result.status === 'success'
                ) {

                    mostrarMensaje(
                        result.message ||
                        '¡Acta enviada correctamente!',
                        'exito'
                    );


                    /*
                    Limpiar formulario.
                    */

                    formulario.reset();


                    provincia.innerHTML =
                        '<option value="">Selecciona una provincia</option>';

                    distrito.innerHTML =
                        '<option value="">Selecciona un distrito</option>';


                    provincia.disabled =
                        true;

                    distrito.disabled =
                        true;


                    previewImagen.src =
                        '';

                    imagenConfirmacion.src =
                        '';


                    previewContainer
                        .classList
                        .add('oculto');


                    if (urlImagenActual) {

                        URL.revokeObjectURL(
                            urlImagenActual
                        );

                        urlImagenActual =
                            null;

                    }


                    /*
                    Limpiar resúmenes.
                    */

                    const resumenUbicacion =
                        document.getElementById(
                            'resumenUbicacion'
                        );

                    if (resumenUbicacion) {

                        resumenUbicacion.textContent =
                            '-';

                    }


                    const resumenMesa =
                        document.getElementById(
                            'resumenMesaEvidencia'
                        );

                    if (resumenMesa) {

                        resumenMesa.textContent =
                            '-';

                    }


                    /*
                    Regresar al paso 1.
                    */

                    setTimeout(() => {

                        limpiarMensaje();

                        cambiarPaso(1);

                    }, 1800);

                } else {

                    mostrarMensaje(
                        result.message ||
                        'Ocurrió un error al guardar el acta.',
                        'error'
                    );

                }


            } catch (error) {

                console.error(
                    'Error:',
                    error
                );


                mostrarMensaje(
                    'Error de conexión o respuesta del servidor. Intenta nuevamente.',
                    'error'
                );


            } finally {

                btnEnviar.disabled =
                    false;

                btnEnviar.textContent =
                    'Confirmar y enviar';

            }

        }
    );


    /* ==========================================
       MENSAJES
    ========================================== */

    function mostrarMensaje(
        texto,
        tipoClase
    ) {

        mensajeDiv.textContent =
            texto;

        mensajeDiv.className =
            tipoClase;

        mensajeDiv.style.display =
            'block';

    }


    function limpiarMensaje() {

        mensajeDiv.textContent =
            '';

        mensajeDiv.className =
            '';

        mensajeDiv.style.display =
            'none';

    }


    /* ==========================================
       INICIALIZAR
    ========================================== */

    cambiarPaso(1);

});