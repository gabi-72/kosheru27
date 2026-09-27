const KosheruCheckoutApp = window.KosheruApp || {
    showToast(message, type = 'success') {
        console.log(`[checkout:${type}] ${message}`);
    },
    formatCOP(amount) {
        return '$' + new Intl.NumberFormat('es-CO').format(Number(amount || 0)) + ' COP';
    }
};

document.addEventListener('DOMContentLoaded', () => {
    const checkoutForm = document.getElementById('checkoutForm');
    const checkoutMessage = document.getElementById('checkoutMessage');

    function showMessage(text, type = 'error') {
        if (!checkoutMessage) return;
        checkoutMessage.className = 'status ' + type;
        checkoutMessage.textContent = text;
    }

    function getSelectedPaymentMethod() {
        const selected = document.querySelector('input[name="payment_method"]:checked');
        return selected ? selected.value : 'tarjeta';
    }

    function togglePaymentFields() {
        const selected = getSelectedPaymentMethod();
        document.querySelectorAll('.payment-fields').forEach(panel => {
            panel.classList.toggle('active', panel.dataset.method === selected);
        });
    }

    function renderSummary() {
        const itemsContainer = document.getElementById('checkoutCartItems');
        const subtotalEl = document.getElementById('checkoutSubtotal');
        const ivaEl = document.getElementById('checkoutIva');
        const totalEl = document.getElementById('checkoutTotal');

        if (!itemsContainer) return;

        const cart = KosheruCart.getCart();
        if (!cart || cart.length === 0) {
            itemsContainer.innerHTML = '<div class="empty-state">Tu carrito está vacío. Agrega productos antes de continuar.</div>';
            subtotalEl.textContent = KosheruCheckoutApp.formatCOP(0);
            ivaEl.textContent = KosheruCheckoutApp.formatCOP(0);
            totalEl.textContent = KosheruCheckoutApp.formatCOP(0);
            return;
        }

        let subtotal = 0;
        itemsContainer.innerHTML = cart.map(item => {
            const totalItem = Number(item.price) * Number(item.quantity);
            subtotal += totalItem;
            return `
                <div class="summary-item">
                    <div>
                        <div class="name">${item.name}</div>
                        <div class="meta">Cantidad: ${item.quantity}</div>
                    </div>
                    <div>
                        <div class="name">${KosheruCheckoutApp.formatCOP(totalItem)}</div>
                    </div>
                </div>
            `;
        }).join('');

        const iva = subtotal * 0.19;
        const total = subtotal + iva;
        subtotalEl.textContent = KosheruCheckoutApp.formatCOP(subtotal);
        ivaEl.textContent = KosheruCheckoutApp.formatCOP(iva);
        totalEl.textContent = KosheruCheckoutApp.formatCOP(total);
    }

    function collectPaymentDetails(method) {
        const details = {};

        if (method === 'tarjeta') {
            details.card_name = document.getElementById('card_name')?.value.trim() || '';
            details.card_number = document.getElementById('card_number')?.value.trim() || '';
            details.card_expiry = document.getElementById('card_expiry')?.value.trim() || '';
            details.card_cvv = document.getElementById('card_cvv')?.value.trim() || '';
        }

        if (method === 'nequi') {
            details.nequi_phone = document.getElementById('nequi_phone')?.value.trim() || '';
            details.nequi_name = document.getElementById('nequi_name')?.value.trim() || '';
        }

        if (method === 'bancolombia') {
            details.bancolombia_number = document.getElementById('bancolombia_number')?.value.trim() || '';
            details.bancolombia_name = document.getElementById('bancolombia_name')?.value.trim() || '';
        }

        if (method === 'consignacion') {
            details.consignacion_bank = document.getElementById('consignacion_bank')?.value.trim() || '';
            details.consignacion_reference = document.getElementById('consignacion_reference')?.value.trim() || '';
        }

        return details;
    }

    const paymentRadios = document.querySelectorAll('input[name="payment_method"]');
    paymentRadios.forEach(radio => {
        radio.addEventListener('change', togglePaymentFields);
    });

    if (checkoutForm) {
        checkoutForm.addEventListener('submit', async (event) => {
            event.preventDefault();
            showMessage('', 'error');

            const cart = KosheruCart.getCart();
            if (!cart || cart.length === 0) {
                showMessage('Tu carrito está vacío. Agrega productos antes de comprar.', 'error');
                return;
            }

            const formData = new FormData(checkoutForm);
            const method = getSelectedPaymentMethod();
            const requiredFields = [
                'recipient_name',
                'recipient_email',
                'recipient_phone',
                'address',
                'city',
                'department'
            ];

            let invalid = false;
            requiredFields.forEach(field => {
                const input = checkoutForm.querySelector(`[name="${field}"]`);
                if (input && !input.value.trim()) {
                    invalid = true;
                    input.style.borderColor = '#ff7a6f';
                } else if (input) {
                    input.style.borderColor = 'rgba(255,255,255,0.12)';
                }
            });

            const paymentDetails = collectPaymentDetails(method);
            const paymentRequired = {
                tarjeta: ['card_name', 'card_number', 'card_expiry', 'card_cvv'],
                nequi: ['nequi_phone', 'nequi_name'],
                bancolombia: ['bancolombia_number', 'bancolombia_name'],
                consignacion: ['consignacion_bank', 'consignacion_reference']
            }[method] || [];

            paymentRequired.forEach(fieldName => {
                const input = document.getElementById(fieldName);
                if (input && !String(paymentDetails[fieldName] || '').trim()) {
                    invalid = true;
                    input.style.borderColor = '#ff7a6f';
                } else if (input) {
                    input.style.borderColor = 'rgba(255,255,255,0.12)';
                }
            });

            if (invalid) {
                showMessage('Completa todos los campos requeridos del formulario y del medio de pago.', 'error');
                return;
            }

            const payload = {
                recipient_name: formData.get('recipient_name')?.toString().trim() || '',
                recipient_email: formData.get('recipient_email')?.toString().trim() || '',
                recipient_phone: formData.get('recipient_phone')?.toString().trim() || '',
                address: formData.get('address')?.toString().trim() || '',
                city: formData.get('city')?.toString().trim() || '',
                department: formData.get('department')?.toString().trim() || '',
                postal_code: formData.get('postal_code')?.toString().trim() || '',
                payment_method: method,
                payment_details: paymentDetails,
                items: cart.map(item => ({ id: item.id, quantity: item.quantity }))
            };

            const submitButton = checkoutForm.querySelector('button[type="submit"]');
            if (submitButton) {
                submitButton.disabled = true;
                submitButton.textContent = 'Procesando compra...';
            }

            try {
                const response = await fetch('checkout.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });

                const result = await response.json();

                if (result.success) {
                    KosheruCart.clearCart();
                    showMessage('¡Compra realizada con éxito! Redirigiendo...', 'success');
                    window.location.href = 'mis-compras.php?fac=' + encodeURIComponent(result.invoice_number || '');
                    return;
                }

                showMessage(result.message || 'No se pudo completar la compra.', 'error');
            } catch (error) {
                showMessage('Hubo un problema con la compra. Inténtalo nuevamente.', 'error');
            } finally {
                if (submitButton) {
                    submitButton.disabled = false;
                    submitButton.textContent = 'Confirmar compra';
                }
            }
        });
    }

    renderSummary();
    togglePaymentFields();
});
