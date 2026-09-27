/**
 * Kosheru - Gestión de Sugerencias Admin JS
 */

const AdminSugerencias = {
    async cargarSugerencias() {
        const tbody = document.getElementById('suggTableBody');
        if (!tbody) return;

        tbody.innerHTML = '<tr><td colspan="6" class="text-center">Cargando sugerencias...</td></tr>';

        try {
            const res = await fetch('../api/sugerencias/index.php');
            const data = await res.json();

            if (data.success) {
                if (data.suggestions.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="6" class="text-center">No hay sugerencias registradas.</td></tr>';
                    return;
                }

                const esc = KosheruApp.escapeHtml;

                tbody.innerHTML = data.suggestions.map(s => `
                    <tr>
                        <td style="font-weight:700;">${esc(s.name)}</td>
                        <td>${esc(s.email)}</td>
                        <td style="max-width:350px;">${esc(s.message)}</td>
                        <td>${esc(s.created_at)}</td>
                        <td>
                            <span class="factura-status-badge ${s.status === 'revisado' ? 'badge-completado' : 'badge-pendiente'}">
                                ${esc(s.status.toUpperCase())}
                            </span>
                        </td>
                        <td>
                            ${s.status === 'pendiente' ? `
                                <button type="button" class="btn-action-edit" onclick="AdminSugerencias.markReviewed(${s.id})">Marcar Revisado</button>
                            ` : '✓ OK'}
                        </td>
                    </tr>
                `).join('');
            }
        } catch (e) {
            console.error('Error cargando sugerencias:', e);
        }
    },

    async markReviewed(id) {
        const formData = new FormData();
        formData.append('id', id);
        formData.append('action', 'mark_reviewed');

        try {
            const res = await fetch('../api/sugerencias/index.php', { method: 'POST', body: formData });
            const data = await res.json();

            if (data.success) {
                KosheruApp.showToast(data.message, 'success');
                this.cargarSugerencias();
            }
        } catch (e) {
            KosheruApp.showToast('Error al actualizar estado.', 'error');
        }
    }
};

document.addEventListener('DOMContentLoaded', () => {
    AdminSugerencias.cargarSugerencias();
});
