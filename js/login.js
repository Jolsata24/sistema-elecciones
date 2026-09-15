document.addEventListener('DOMContentLoaded', () => {
    const formLogin = document.getElementById('formLogin');
    const btnIngresar = document.getElementById('btnIngresar');
    const mensajeDiv = document.getElementById('mensajeLogin');

    formLogin.addEventListener('submit', async (e) => {
        e.preventDefault();
        
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
                // Si el login es exitoso, redirigimos al formulario de actas
                window.location.href = 'index.php'; 
            } else {
                mostrarMensaje(result.message || 'Credenciales incorrectas.', '#fee2e2', '#991b1b');
                btnIngresar.disabled = false;
                btnIngresar.textContent = 'Ingresar al Sistema';
            }
        } catch (error) {
            mostrarMensaje('Error de conexión. Revisa tu internet.', '#fee2e2', '#991b1b');
            btnIngresar.disabled = false;
            btnIngresar.textContent = 'Ingresar al Sistema';
        }
    });

    function mostrarMensaje(texto, bgColor, textColor) {
        mensajeDiv.textContent = texto;
        mensajeDiv.style.backgroundColor = bgColor;
        mensajeDiv.style.color = textColor;
        mensajeDiv.style.display = 'block';
    }
});