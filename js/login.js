document.addEventListener('DOMContentLoaded', () => {
    const formLogin = document.getElementById('formLogin');
    const btnIngresar = document.getElementById('btnIngresar');
    const mensajeDiv = document.getElementById('mensajeLogin');

    formLogin.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        // Bloquear el botón mientras carga
        btnIngresar.disabled = true;
        btnIngresar.textContent = 'Verificando...';
        mensajeDiv.style.display = 'none';

        const formData = new FormData(formLogin);

        try {
            const response = await fetch('api/login.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (response.ok && result.status === 'success') {
                // Redirección inteligente basada en el rol que nos devuelve PHP
                if (result.rol === 'admin') {
                    window.location.href = 'dashboard.php'; // Al centro de cómputo
                } else {
                    window.location.href = 'index.php'; // Al formulario del celular
                }
            } else {
                // Credenciales incorrectas
                mostrarMensaje(result.message || 'Credenciales incorrectas.', '#fee2e2', '#991b1b');
                btnIngresar.disabled = false;
                btnIngresar.textContent = 'Ingresar al Sistema';
            }
        } catch (error) {
            // Error de servidor o de internet
            mostrarMensaje('Error de conexión. Revisa tu internet.', '#fee2e2', '#991b1b');
            btnIngresar.disabled = false;
            btnIngresar.textContent = 'Ingresar al Sistema';
        }
    });

    // Función auxiliar para imprimir las alertas de error
    function mostrarMensaje(texto, bgColor, textColor) {
        mensajeDiv.textContent = texto;
        mensajeDiv.style.backgroundColor = bgColor;
        mensajeDiv.style.color = textColor;
        mensajeDiv.style.display = 'block';
    }
});