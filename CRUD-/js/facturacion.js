/**
 * Kosheru - Módulo de Facturación Administrativa JS (PRIORIDAD MÁXIMA)
 * Manejo de los 6 Filtros Acumulables y Visualizador de Factura Electrónica Admin
 */

const AdminFacturacion = {
    async buscarFacturas() {
        const tbody = document.getElementById('facturacionTableBody');
        if (!tbody) return;

        tbody.innerHTML = '<tr><td colspan="8" class="text-center">Buscando facturas...</td></tr>';

        // Obtener valores de los 6 filtros obligatorios
        const inv = document.getElementById('filterInvoiceNum').value.trim();
        const name = document.getElementById('filterCustomerName').value.trim();
        const email = document.getElementById('filterCustomerEmail').value.trim();
        const doc = document.getElementById('filterDocumentId').value.trim();
        const desde = document.getElementById('filterFechaDesde').value;
        const hasta = document.getElementById('filterFechaHasta').value;

        let queryParams = new URLSearchParams();
        if (inv) queryParams.append('invoice_number', inv);
        if (name) queryParams.append('customer_name', name);
        if (email) queryParams.append('customer_email', email);
        if (doc) queryParams.append('document_id', doc);
        if (desde) queryParams.append('fecha_desde', desde);
        if (hasta) queryParams.append('fecha_hasta', hasta);

        try {
            const res = await fetch(`../api/facturacion/index.php?${queryParams.toString()}`);
            const data = await res.json();

            if (data.success) {
                if (data.facturas.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="8" class="text-center">No se encontraron facturas con los filtros aplicados.</td></tr>';
                    return;
                }

                const esc = KosheruApp.escapeHtml;

                tbody.innerHTML = data.facturas.map(f => `
                    <tr>
                        <td style="font-weight:800; color:var(--color-rojo-kosheru);">${esc(f.invoice_number)}</td>
                        <td style="font-weight:700;">${esc(f.user_name)}</td>
                        <td>${esc(f.user_email)}</td>
                        <td>${esc(f.document_id)}</td>
                        <td>${esc(f.order_date)} ${esc(f.order_time)}</td>
                        <td style="font-weight:800; color:var(--color-cafe-oscuro);">${f.total_price_formatted}</td>
                        <td>
                            <span class="factura-status-badge badge-${esc(f.status)}">
                                ${esc(f.status.toUpperCase())}
                            </span>
                        </td>
                        <td>
                            <button type="button" class="btn-action-view" onclick="AdminFacturacion.verFacturaDetalle('${encodeURIComponent(f.invoice_number)}')">
                                🔍 Ver Factura
                            </button>
                        </td>
                    </tr>
                `).join('');
            }
        } catch (e) {
            console.error('Error buscando facturas:', e);
        }
    },

    limpiarFiltros() {
        document.getElementById('filterInvoiceNum').value = '';
        document.getElementById('filterCustomerName').value = '';
        document.getElementById('filterCustomerEmail').value = '';
        document.getElementById('filterDocumentId').value = '';
        document.getElementById('filterFechaDesde').value = '';
        document.getElementById('filterFechaHasta').value = '';
        this.buscarFacturas();
    },

    async verFacturaDetalle(invoiceNumber) {
        try {
            // Consultar detalle completo incluyendo ítems usando la API central
            const res = await fetch(`../../api/buscar-factura.php?invoice_number=${encodeURIComponent(invoiceNumber)}`);
            const data = await res.json();

            if (data.success) {
                this.renderModalFactura(data.purchase, data.items);
            } else {
                KosheruApp.showToast(data.message || 'Error al cargar detalle de factura.', 'error');
            }
        } catch (e) {
            KosheruApp.showToast('Error de conexión al obtener la factura.', 'error');
        }
    },

    renderModalFactura(p, items) {
        const modalContainer = document.getElementById('adminInvoiceModalContainer');
        const modalContent = document.getElementById('adminInvoiceModalContent');

        if (!modalContainer || !modalContent) return;

        const esc = KosheruApp.escapeHtml;

        const itemsRows = items.map(item => `
            <tr>
                <td style="font-weight:700; color:var(--color-cafe-oscuro);">${esc(item.product_name)}</td>
                <td class="text-center" style="font-weight:700;">${esc(item.quantity)}</td>
                <td class="text-right">${item.unit_price_formatted}</td>
                <td class="text-right" style="font-weight:800; color:var(--color-rojo-kosheru);">${item.total_price_formatted}</td>
            </tr>
        `).join('');

        modalContent.innerHTML = `
            <div class="factura-wrapper">
                <!-- Encabezado Vino Tinto / Dorado -->
                <div class="factura-header-banner">
                    <div class="factura-brand-info">
                        <h1>Kosheru S.A.S.</h1>
                        <p>Superalimentos naturales e importados</p>
                        <div class="factura-location">Mosquera, Cundinamarca, Colombia</div>
                    </div>
                    <div class="factura-meta-info">
                        <div class="factura-title-tag">FACTURA ELECTRÓNICA</div>
                        <div class="factura-number">${esc(p.invoice_number)}</div>
                        <div class="factura-date-time">Fecha: ${esc(p.order_date)} | Hora: ${esc(p.order_time)}</div>
                    </div>
                </div>

                <div class="factura-body">
                    <!-- Estado -->
                    <div class="factura-status-bar">
                        <div style="font-size:0.9rem; font-weight:700; color:var(--color-cafe-oscuro);">Estado de la Factura:</div>
                        <span class="factura-status-badge badge-${esc(p.status)}">✓ ${esc(p.status.toUpperCase())}</span>
                    </div>

                    <!-- DATOS DEL COMPRADOR Y ENTREGA -->
                    <div class="factura-details-grid">
                        <div class="factura-block">
                            <div class="factura-block-title">DATOS DEL COMPRADOR</div>
                            <div class="factura-data-row">
                                <span class="data-label">Nombre:</span>
                                <span class="data-value">${esc(p.user_name)}</span>
                            </div>
                            <div class="factura-data-row">
                                <span class="data-label">Correo:</span>
                                <span class="data-value">${esc(p.user_email)}</span>
                            </div>
                            <div class="factura-data-row">
                                <span class="data-label">Cédula / TI:</span>
                                <span class="data-value">${esc(p.document_id)}</span>
                            </div>
                            <div class="factura-data-row">
                                <span class="data-label">Teléfono:</span>
                                <span class="data-value">${esc(p.phone)}</span>
                            </div>
                        </div>

                        <div class="factura-block">
                            <div class="factura-block-title">ENTREGA Y PAGO</div>
                            <div class="factura-data-row">
                                <span class="data-label">Dirección:</span>
                                <span class="data-value">${esc(p.address)}</span>
                            </div>
                            <div class="factura-data-row">
                                <span class="data-label">Ciudad:</span>
                                <span class="data-value">${esc(p.city)}</span>
                            </div>
                            <div class="factura-data-row">
                                <span class="data-label">Método de Pago:</span>
                                <span class="data-value">${esc(p.payment_method)}</span>
                            </div>
                        </div>
                    </div>

                    <!-- PRODUCTOS ADQUIRIDOS -->
                    <div class="factura-table-title">PRODUCTOS ADQUIRIDOS</div>
                    <table class="factura-table">
                        <thead>
                            <tr>
                                <th>PRODUCTO</th>
                                <th class="text-center">CANT.</th>
                                <th class="text-right">PRECIO UNIT.</th>
                                <th class="text-right">SUBTOTAL</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${itemsRows}
                        </tbody>
                    </table>

                    <!-- TOTALES -->
                    <div class="factura-totals-container">
                        <div class="factura-totals-box">
                            <div class="totals-row">
                                <span>Subtotal:</span>
                                <span>${p.subtotal_formatted}</span>
                            </div>
                            <div class="totals-row">
                                <span>IVA (19%):</span>
                                <span>${p.tax_iva_formatted}</span>
                            </div>
                            <div class="totals-row final-total">
                                <span>TOTAL PAGADO:</span>
                                <span>${p.total_price_formatted}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Impresión -->
                <div class="factura-actions-bar">
                    <button type="button" class="btn-print-invoice" onclick="window.print();">
                        <span>🖨️</span> Imprimir Factura Electrónica
                    </button>
                </div>
            </div>
        `;

        modalContainer.style.display = 'block';
    }
};

document.addEventListener('DOMContentLoaded', () => {
    AdminFacturacion.buscarFacturas();

    document.getElementById('btnBuscarFacturas').addEventListener('click', () => AdminFacturacion.buscarFacturas());
    document.getElementById('btnLimpiarFacturas').addEventListener('click', () => AdminFacturacion.limpiarFiltros());
});
