/**
 * Kosheru - Gestión de Compras Admin JS
 */

const AdminCompras = {
    async cargarCompras() {
        const tbody = document.getElementById('purchasesTableBody');
        if (!tbody) return;

        tbody.innerHTML = '<tr><td colspan="8" class="text-center">Cargando compras...</td></tr>';

        try {
            const res = await fetch('../api/compras/index.php');
            const data = await res.json();

            if (data.success) {
                if (data.compras.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="8" class="text-center">No hay compras registradas en el sistema.</td></tr>';
                    return;
                }

                const esc = KosheruApp.escapeHtml;

                tbody.innerHTML = data.compras.map(c => `
                    <tr>
                        <td style="font-weight:800; color:var(--color-rojo-kosheru);">${esc(c.invoice_number)}</td>
                        <td style="font-weight:700;">${esc(c.user_name)}</td>
                        <td>${esc(c.user_email)}</td>
                        <td>${esc(c.order_date)} ${esc(c.order_time)}</td>
                        <td>${esc(c.payment_method)}</td>
                        <td style="font-weight:800; color:var(--color-cafe-oscuro);">${c.total_formatted}</td>
                        <td>
                            <span class="factura-status-badge badge-${esc(c.status)}">
                                ${esc(c.status.toUpperCase())}
                            </span>
                        </td>
                        <td>
                            <button type="button" class="btn-action-view" onclick="window.location.href='facturacion.php?fac=${encodeURIComponent(c.invoice_number)}'">Ver detalle</button>
                        </td>
                    </tr>
                `).join('');
            }
        } catch (e) {
            console.error('Error al cargar compras:', e);
        }
    }
};

document.addEventListener('DOMContentLoaded', () => {
    AdminCompras.cargarCompras();
});
