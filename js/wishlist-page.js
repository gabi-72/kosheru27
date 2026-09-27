/**
 * Kosheru - Renderizado de la página "Mi Wishlist"
 * Reutiliza KosheruProductos.createProductCardHTML para que las tarjetas
 * (imagen, estrellas, botón de carrito, corazón) se vean igual que en la tienda.
 */
document.addEventListener('DOMContentLoaded', async () => {
    const grid = document.getElementById('wishlistGrid');
    if (!grid) return;

    try {
        const res = await fetch('api/wishlist.php');
        const data = await res.json();

        if (!data.success || !data.items || data.items.length === 0) {
            grid.innerHTML = `
                <div class="empty-state">
                    <div class="empty-state-icon"><img src="img/icons/basket.svg" alt="Wishlist vacía" class="inline-icon" aria-hidden="true"></div>
                    <h3 class="empty-state-title">Tu wishlist está vacía</h3>
                    <p class="empty-state-desc">Guarda productos desde la tienda tocando el corazón en cada tarjeta.</p>
                </div>
            `;
            return;
        }

        data.items.forEach(p => { KosheruProductos.productsById[p.id] = p; });
        grid.innerHTML = data.items.map(p => KosheruProductos.createProductCardHTML(p)).join('');

        if (window.KosheruWishlist) KosheruWishlist.syncButtons();
    } catch (e) {
        grid.innerHTML = '<div style="grid-column:1/-1;text-align:center;padding:3rem;">Error al cargar tu wishlist.</div>';
    }
});
