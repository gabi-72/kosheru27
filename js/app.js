/**
 * Kosheru - Core JavaScript Application Utility
 */

const KosheruApp = {
    /**
     * Muestra una notificación emergente (Toast)
     */
    showToast(message, type = 'success') {
        let toastContainer = document.getElementById('kosheruToastContainer');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.id = 'kosheruToastContainer';
            toastContainer.style.cssText = 'position:fixed;bottom:20px;right:20px;z-index:3000;display:flex;flex-direction:column;gap:10px;';
            document.body.appendChild(toastContainer);
        }

        const toast = document.createElement('div');
        const bg = type === 'success' ? '#27ae60' : (type === 'error' ? '#c0392b' : '#e67e22');
        toast.style.cssText = `background:${bg};color:#fff;padding:12px 20px;border-radius:10px;font-weight:700;font-size:0.95rem;box-shadow:0 8px 20px rgba(0,0,0,0.2);animation:fadeInUp 0.3s forwards;`;
        toast.textContent = message;

        toastContainer.appendChild(toast);

        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transition = 'all 0.3s ease';
            setTimeout(() => toast.remove(), 300);
        }, 3500);
    },

    /**
     * Formatea un valor numérico a Pesos Colombianos (COP)
     */
    formatCOP(amount) {
        return '$' + new Intl.NumberFormat('es-CO').format(amount) + ' COP';
    },

    /**
     * Escapa texto antes de insertarlo en innerHTML para evitar XSS.
     * Úsala SIEMPRE que insertes un dato que provenga de un usuario
     * (nombres, mensajes, direcciones, etc.) dentro de una plantilla HTML.
     */
    escapeHtml(value) {
        if (value === null || value === undefined) return '';
        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }
};

document.addEventListener('DOMContentLoaded', () => {
    // Inicialización global de la app
});
