/**
 * Kosheru - Formulario de Sugerencias y Contacto AJAX
 */

document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('suggestionsForm');
    const alertBox = document.getElementById('suggestionsAlert');

    if (form) {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            const formData = new FormData(form);
            const btn = form.querySelector('button[type="submit"]');

            btn.disabled = true;
            btn.textContent = 'Enviando...';

            try {
                const response = await fetch('api/sugerencias.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    if (alertBox) {
                        alertBox.className = 'auth-alert success';
                        alertBox.style.display = 'block';
                        alertBox.textContent = result.message;
                    } else {
                        KosheruApp.showToast(result.message, 'success');
                    }
                    form.reset();
                } else {
                    if (alertBox) {
                        alertBox.className = 'auth-alert error';
                        alertBox.style.display = 'block';
                        alertBox.textContent = result.message;
                    } else {
                        KosheruApp.showToast(result.message, 'error');
                    }
                }
            } catch (err) {
                KosheruApp.showToast('Error de conexión al enviar el mensaje.', 'error');
            } finally {
                btn.disabled = false;
                btn.textContent = 'Enviar Mensaje';
            }
        });
    }
});
