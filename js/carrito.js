/**
 * Kosheru - Manejo del Carrito Lateral (Cart Drawer)
 * Persistencia en localStorage e integración con Checkout
 *
 * NOTA: este archivo es la ÚNICA fuente de verdad para abrir/cerrar el
 * drawer del carrito (antes esta lógica estaba duplicada en header.js).
 */

const KosheruAppSafe = window.KosheruApp || {
    showToast(message, type = 'success') {
        console.log(`[cart:${type}] ${message}`);
    },
    formatCOP(amount) {
        return '$' + new Intl.NumberFormat('es-CO').format(Number(amount || 0)) + ' COP';
    }
};

const KosheruCart = {
    storageKey: 'kosheru_cart_items',
    drawerSeenKey: 'kosheru_cart_drawer_seen',
    freeShippingThreshold: 80000, // COP - mismo umbral que ya se promociona en el banner de inicio

    getCart() {
        try {
            return JSON.parse(localStorage.getItem(this.storageKey)) || [];
        } catch (e) {
            return [];
        }
    },

    saveCart(cart) {
        localStorage.setItem(this.storageKey, JSON.stringify(cart));
        this.updateBadge();
        this.renderCart();
    },

    addToCart(product, btnEl = null) {
        let cart = this.getCart();
        const index = cart.findIndex(item => item.id == product.id);

        if (index > -1) {
            cart[index].quantity += 1;
        } else {
            cart.push({
                id: product.id,
                name: product.name,
                price: parseFloat(product.price),
                image: product.image,
                origin: product.origin,
                quantity: 1
            });
        }

        this.saveCart(cart);
        this.pulseBadge();

        if (btnEl) {
            btnEl.classList.add('added');
            setTimeout(() => btnEl.classList.remove('added'), 400);
        }

        KosheruAppSafe.showToast(`¡${product.name} se ha añadido a tu carrito!`, 'success');

        // Solo abrimos el drawer automáticamente la primera vez que se agrega
        // un producto en esta sesión de navegación. Las demás veces solo se
        // muestra la notificación toast.
        if (!sessionStorage.getItem(this.drawerSeenKey)) {
            sessionStorage.setItem(this.drawerSeenKey, '1');
            this.openDrawer();
        }
    },

    /**
     * Hace "pulsar" el badge del carrito (todas sus instancias en la página)
     * cada vez que se agrega un producto, para dar retroalimentación visual
     * inmediata sin necesidad de abrir el drawer.
     */
    pulseBadge() {
        document.querySelectorAll('.cart-badge').forEach(el => {
            el.classList.remove('pulse');
            void el.offsetWidth; // fuerza reflow para poder reiniciar la animación
            el.classList.add('pulse');
        });
    },

    updateQuantity(productId, delta) {
        let cart = this.getCart();
        const index = cart.findIndex(item => item.id == productId);

        if (index > -1) {
            cart[index].quantity += delta;
            if (cart[index].quantity <= 0) {
                cart.splice(index, 1);
            }
            this.saveCart(cart);
        }
    },

    removeFromCart(productId) {
        let cart = this.getCart();
        cart = cart.filter(item => item.id != productId);
        this.saveCart(cart);
        KosheruAppSafe.showToast('Producto eliminado del carrito.', 'info');
    },

    clearCart() {
        localStorage.removeItem(this.storageKey);
        this.updateBadge();
        this.renderCart();
    },

    updateBadge() {
        const cart = this.getCart();
        const totalCount = cart.reduce((sum, item) => sum + item.quantity, 0);
        const badgeEls = document.querySelectorAll('.cart-badge');
        badgeEls.forEach(el => el.textContent = totalCount);
    },

    getTotals() {
        const cart = this.getCart();
        const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        const iva = subtotal * 0.19;
        const total = subtotal + iva;
        return { subtotal, iva, total };
    },

    /**
     * Actualiza el texto y la barra de progreso de "envío gratis" en el
     * header del drawer, según el subtotal actual del carrito.
     */
    renderShippingProgress() {
        const wrapEl = document.getElementById('cartShippingProgress');
        const textEl = document.getElementById('shippingProgressText');
        const fillEl = document.getElementById('shippingProgressFill');
        if (!wrapEl || !textEl || !fillEl) return;

        const cart = this.getCart();
        if (cart.length === 0) {
            wrapEl.style.display = 'none';
            return;
        }
        wrapEl.style.display = 'block';

        const { subtotal } = this.getTotals();
        const threshold = this.freeShippingThreshold;
        const remaining = threshold - subtotal;
        const percent = Math.min(100, Math.max(0, (subtotal / threshold) * 100));

        fillEl.style.width = `${percent}%`;

        if (remaining <= 0) {
            textEl.innerHTML = '🎉 ¡Ya tienes envío gratis!';
            textEl.classList.add('unlocked');
            fillEl.classList.add('complete');
        } else {
            textEl.innerHTML = `Te faltan <strong>${KosheruAppSafe.formatCOP(remaining)}</strong> para envío gratis`;
            textEl.classList.remove('unlocked');
            fillEl.classList.remove('complete');
        }
    },

    openDrawer() {
        const overlay = document.getElementById('cartDrawerOverlay');
        if (overlay) overlay.classList.add('active');
    },

    closeDrawer() {
        const overlay = document.getElementById('cartDrawerOverlay');
        if (overlay) overlay.classList.remove('active');
    },

    renderCart() {
        const listEl = document.getElementById('cartItemsList');
        const emptyEl = document.getElementById('cartEmptyMsg');
        const subtotalEl = document.getElementById('cartSubtotalText');
        const ivaEl = document.getElementById('cartIvaText');
        const totalEl = document.getElementById('cartTotalText');

        if (!listEl) return;

        const cart = this.getCart();
        const totals = this.getTotals();

        if (cart.length === 0) {
            listEl.innerHTML = '';
            listEl.style.display = 'none';
            if (emptyEl) emptyEl.style.display = 'block';
        } else {
            listEl.style.display = 'block';
            if (emptyEl) emptyEl.style.display = 'none';

            const esc = KosheruAppSafe.escapeHtml || (v => v);

            listEl.innerHTML = cart.map(item => `
                <div class="cart-item">
                    <img src="${esc(item.image)}" class="cart-item-img" alt="${esc(item.name)}" onerror="this.src='img/tofu.jpg'">
                    <div class="cart-item-info">
                        <div class="cart-item-title">${esc(item.name)}</div>
                        <div class="cart-item-price">${KosheruAppSafe.formatCOP(item.price)}</div>
                        <div class="cart-item-controls">
                            <button type="button" class="btn-qty" onclick="KosheruCart.updateQuantity(${item.id}, -1)">-</button>
                            <span class="cart-item-qty">${item.quantity}</span>
                            <button type="button" class="btn-qty" onclick="KosheruCart.updateQuantity(${item.id}, 1)">+</button>
                            <button type="button" class="btn-remove-item" onclick="KosheruCart.removeFromCart(${item.id})" title="Eliminar" aria-label="Eliminar producto" style="display:flex;align-items:center;justify-content:center;padding:0;border:none;background:transparent;">
                                <img src="img/icons/trash.svg" alt="Eliminar" class="inline-icon" aria-hidden="true" style="width:16px;height:16px;filter: invert(57%) sepia(98%) saturate(334%) hue-rotate(344deg) brightness(102%) contrast(101%);">
                            </button>
                        </div>
                    </div>
                </div>
            `).join('');
        }

        if (subtotalEl) subtotalEl.textContent = KosheruAppSafe.formatCOP(totals.subtotal);
        if (ivaEl) ivaEl.textContent = KosheruAppSafe.formatCOP(totals.iva);
        if (totalEl) totalEl.textContent = KosheruAppSafe.formatCOP(totals.total);

        this.renderShippingProgress();
    }
};

document.addEventListener('DOMContentLoaded', () => {
    KosheruCart.updateBadge();
    KosheruCart.renderCart();

    // OJO: el botón real en el header tiene id="btnOpenCart" (includes/header.php).
    // Antes este código buscaba "btnCartTrigger", que no existe en ningún HTML
    // del proyecto, así que este listener nunca se disparaba.
    const btnOpenCart = document.getElementById('btnOpenCart');
    const btnCloseCart = document.getElementById('btnCloseCart');
    const overlay = document.getElementById('cartDrawerOverlay');
    const btnCheckoutTrigger = document.getElementById('btnCheckoutTrigger');

    if (btnOpenCart) {
        btnOpenCart.addEventListener('click', () => KosheruCart.openDrawer());
    }

    if (btnCloseCart) {
        btnCloseCart.addEventListener('click', () => KosheruCart.closeDrawer());
    }

    if (btnCheckoutTrigger) {
        btnCheckoutTrigger.addEventListener('click', () => {
            const cart = KosheruCart.getCart();
            if (!cart || cart.length === 0) {
                KosheruAppSafe.showToast('Tu carrito está vacío.', 'error');
                return;
            }
            window.location.href = 'checkout.php';
        });
    }

    if (overlay) {
        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) KosheruCart.closeDrawer();
        });
    }
});
