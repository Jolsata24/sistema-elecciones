document.addEventListener('DOMContentLoaded', () => {
    const formulario = document.getElementById('formularioActa');
    const btnEnviar = document.getElementById('btnEnviar');
    const mensajeDiv = document.getElementById('mensajeNotificacion');

    formulario.addEventListener('submit', async (e) => {
        // Prevenir que la página se recargue (comportamiento por defecto del form)
        e.preventDefault();

        // 1. Bloquear el botón para evitar envíos duplicados por clics desesperados
        btnEnviar.disabled = true;
        btnEnviar.textContent = 'Subiendo acta... espere por favor';
        mensajeDiv.style.display = 'none'; // Ocultar mensajes anteriores

        // 2. Empaquetar los datos, incluyendo la imagen de forma automática
        const formData = new FormData(formulario);

        try {
            // 3. Enviar la petición al backend (asegúrate de que la ruta sea correcta)
            const response = await fetch('api/guardar_acta.php', {
                method: 'POST',
                body: formData
                // No configuramos el Content-Type manualmente, Fetch lo hace 
                // automáticamente (multipart/form-data) al detectar FormData
            });

            // Parsear la respuesta JSON que nos devuelve PHP
            const result = await response.json();

            if (response.ok && result.status === 'success') {
                mostrarMensaje('¡Acta enviada correctamente! Gracias.', 'exito');
                formulario.reset(); // Limpiar formulario para la siguiente (si aplica)
            } else {
                // El backend devolvió un error (ej. faltan datos o error de BD)
                mostrarMensaje(result.message || 'Ocurrió un error al guardar el acta.', 'error');
            }
        } catch (error) {
            console.error('Error de red:', error);
            // Este error suele saltar si el personero se queda sin internet en ese momento
            mostrarMensaje('Error de conexión. Revisa tus datos móviles e intenta de nuevo.', 'error');
        } finally {
            // 4. Pase lo que pase (éxito o error), volvemos a habilitar el botón
            btnEnviar.disabled = false;
            btnEnviar.textContent = 'Enviar Resultados';
        }
    });

    // Función auxiliar para mostrar alertas en pantalla
    function mostrarMensaje(texto, tipoClase) {
        mensajeDiv.textContent = texto;
        mensajeDiv.className = tipoClase; // 'exito' o 'error'
        mensajeDiv.style.display = 'block';
    }
});