/**
 * Kosheru - Modal de Detalle de Producto
 */
const KosheruProductDetail = {
    currentProduct: null,

    open(product) {
        this.currentProduct = product;
        const stock = parseInt(product.stock, 10) || 0;

        document.getElementById('pdImage').src = window.KosheruProductos ? KosheruProductos.getProductImage(product) : product.image;
        document.getElementById('pdImage').alt = product.name;
        document.getElementById('pdOrigin').textContent = product.origin || '';
        document.getElementById('pdName').textContent = product.name || '';
        document.getElementById('pdCategory').textContent = product.category || '';
        document.getElementById('pdDescription').textContent = product.description || 'Sin descripción disponible.';
        document.getElementById('pdPrice').textContent = product.price_formatted || `$${new Intl.NumberFormat('es-CO').format(product.price || 0)} COP`;

        const stockEl = document.getElementById('pdStock');
        if (stock > 0) {
            stockEl.textContent = `${stock} unidades disponibles`;
            stockEl.style.color = '';
        } else {
            stockEl.textContent = 'Agotado';
            stockEl.style.color = 'var(--color-error)';
        }

        document.getElementById('productDetailOverlay').classList.add('active');
    },

    close() {
        document.getElementById('productDetailOverlay').classList.remove('active');
    }
};

document.addEventListener('DOMContentLoaded', () => {
    const overlay = document.getElementById('productDetailOverlay');
    const closeBtn = document.getElementById('btnCloseProductDetail');
    const addBtn = document.getElementById('pdAddToCart');

    if (closeBtn) {
        closeBtn.addEventListener('click', () => KosheruProductDetail.close());
    }
    if (overlay) {
        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) KosheruProductDetail.close();
        });
    }
    if (addBtn) {
        addBtn.addEventListener('click', () => {
            if (KosheruProductDetail.currentProduct && window.KosheruCart) {
                KosheruCart.addToCart(KosheruProductDetail.currentProduct);
                KosheruProductDetail.close();
            }
        });
    }
});
