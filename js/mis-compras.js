/**
 * Kosheru - Carga y Renderizado Visual de Facturas Electrónicas
 */

const KosheruMisCompras = {
    async cargarFactura(invoiceNumber) {
        if (!invoiceNumber) return;

        try {
            const response = await fetch(`api/buscar-factura.php?invoice_number=${encodeURIComponent(invoiceNumber)}`);
            const data = await response.json();

            if (data.success) {
                this.renderFacturaModal(data.purchase, data.items);
            } else {
                KosheruApp.showToast(data.message || 'No se encontró la factura solicitada.', 'error');
            }
        } catch (err) {
            KosheruApp.showToast('Error de conexión al cargar la factura.', 'error');
        }
    },

    renderFacturaModal(p, items) {
        const modalContainer = document.getElementById('invoiceModalContainer');
        const modalContent = document.getElementById('invoiceModalContent');

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
                <!-- Banner Vino Tinto / Dorado -->
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

                    <!-- Datos del Comprador y Entrega -->
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

                    <!-- Tabla de Productos -->
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

                    <!-- Totales -->
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

                <!-- Botón de Impresión -->
                <div class="factura-actions-bar">
                    <button type="button" class="btn-print-invoice" onclick="window.print();">
                        <span>🖨️</span> Imprimir / Guardar PDF
                    </button>
                </div>
            </div>
        `;

        modalContainer.style.display = 'block';
    }
};

document.addEventListener('DOMContentLoaded', () => {
    const inputSearchInvoice = document.getElementById('inputSearchInvoice');
    if (inputSearchInvoice) {
        inputSearchInvoice.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                KosheruMisCompras.cargarFactura(inputSearchInvoice.value.trim());
            }
        });
    }
});
