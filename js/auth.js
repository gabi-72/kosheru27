/**
 * Kosheru - Autenticación AJAX (Login Modal & Registro)
 */
document.addEventListener('DOMContentLoaded', () => {
    // 1. Manejo del Login Modal flotante
    const modalLoginForm = document.getElementById('modalLoginForm');
    const modalAuthAlert = document.getElementById('modalAuthAlert');

    if (modalLoginForm) {
        modalLoginForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            if (modalAuthAlert) {
                modalAuthAlert.style.display = 'none';
                modalAuthAlert.textContent = '';
            }

            const username = document.getElementById('modalUsername').value.trim();
            const password = document.getElementById('modalPassword').value;

            if (!username || !password) {
                showModalAlert('Ingresa tu usuario y contraseña.', 'error');
                return;
            }

            try {
                const formData = new FormData();
                formData.append('username', username);
                formData.append('password', password);

                const response = await fetch('login.php', {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: formData
                });

                const data = await response.json();

                if (data.status === 'success') {
                    // Si el usuario es admin, ir al panel de admin, sino recargar
                    if (data.user && data.user.rol === 'admin') {
                        window.location.href = 'CRUD-/vista/index.php';
                    } else {
                        window.location.reload();
                    }
                } else {
                    showModalAlert(data.message || 'Usuario o contraseña incorrectos.', 'error');
                }
            } catch (err) {
                console.error('Error al iniciar sesión:', err);
                showModalAlert('Ocurrió un error al conectar con el servidor.', 'error');
            }
        });
    }

    function showModalAlert(message, type) {
        if (!modalAuthAlert) return;
        modalAuthAlert.textContent = message;
        modalAuthAlert.className = `modal-alert ${type}`;
        modalAuthAlert.style.display = 'block';
    }
});
