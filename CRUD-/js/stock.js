/**
 * Kosheru - Control de Inventario y Movimientos JS
 */

const AdminStock = {
    async cargarStockData() {
        try {
            const res = await fetch('../api/stock/index.php');
            const data = await res.json();

            if (data.success) {
                // 1. Renderizar Estado Actual de Productos
                const tbodyStock = document.getElementById('stockStatusTableBody');
                const selectModal = document.getElementById('modalStockProductSelect');

                if (tbodyStock) {
                    tbodyStock.innerHTML = data.products.map(p => `
                        <tr>
                            <td style="font-weight:700; color:var(--color-cafe-oscuro);">${p.name}</td>
                            <td>${p.category}</td>
                            <td style="font-size:1.1rem; font-weight:800;">${p.stock} un.</td>
                            <td><span class="factura-status-badge ${p.status_class}">${p.status_label}</span></td>
                            <td>
                                <button type="button" class="btn-action-edit" onclick="AdminStock.openRefillModal(${p.id}, '${p.name}')">➕ Reabastecer</button>
                            </td>
                        </tr>
                    `).join('');
                }

                if (selectModal) {
                    selectModal.innerHTML = data.products.map(p => `<option value="${p.id}">${p.name} (Stock: ${p.stock})</option>`).join('');
                }

                // 2. Renderizar Historial de Movimientos
                const tbodyMovs = document.getElementById('stockMovsTableBody');
                if (tbodyMovs) {
                    tbodyMovs.innerHTML = data.movements.map(m => `
                        <tr>
                            <td>${m.created_at}</td>
                            <td style="font-weight:700;">${m.product_name || 'Producto N/A'}</td>
                            <td>
                                <span class="factura-status-badge ${m.movement_type === 'entrada' ? 'badge-completado' : 'badge-cancelado'}">
                                    ${m.movement_type.toUpperCase()}
                                </span>
                            </td>
                            <td style="font-weight:700; text-align:center;">${m.quantity}</td>
                            <td style="text-align:center; color:#777;">${m.previous_stock}</td>
                            <td style="text-align:center; font-weight:800; color:var(--color-rojo-kosheru);">${m.new_stock}</td>
                            <td style="font-size:0.85rem; color:#555;">${m.reason}</td>
                        </tr>
                    `).join('');
                }
            }
        } catch (e) {
            console.error('Error al cargar datos de inventario:', e);
        }
    },

    openRefillModal(productId = null, productName = '') {
        const select = document.getElementById('modalStockProductSelect');
        if (productId && select) {
            select.value = productId;
        }
        document.getElementById('modalStockQty').value = '';
        document.getElementById('modalStockReason').value = 'Reabastecimiento de inventario';
        document.getElementById('modalStockOverlay').classList.add('active');
    },

    closeRefillModal() {
        document.getElementById('modalStockOverlay').classList.remove('active');
    },

    async submitRefill(e) {
        e.preventDefault();
        const form = document.getElementById('formRefillStockModal');
        const formData = new FormData(form);

        try {
            const res = await fetch('../api/stock/index.php', { method: 'POST', body: formData });
            const data = await res.json();

            if (data.success) {
                KosheruApp.showToast(data.message, 'success');
                this.closeRefillModal();
                this.cargarStockData();
            } else {
                KosheruApp.showToast(data.message, 'error');
            }
        } catch (err) {
            KosheruApp.showToast('Error de conexión al actualizar stock.', 'error');
        }
    }
};

document.addEventListener('DOMContentLoaded', () => {
    AdminStock.cargarStockData();

    const form = document.getElementById('formRefillStockModal');
    if (form) {
        form.addEventListener('submit', (e) => AdminStock.submitRefill(e));
    }
});
