/**
 * Kosheru - Buscador Funcional con Debounce (300ms)
 */

document.addEventListener('DOMContentLoaded', () => {
    let debounceTimer = null;

    const performSearch = (query) => {
        const isTiendaPage = window.location.pathname.includes('tienda.php');

        if (isTiendaPage) {
            KosheruProductos.renderGrid('tiendaProductsGrid', '', query);
        } else {
            // Si está en index.php u otra vista, redirigir a tienda.php con el parámetro search
            window.location.href = `tienda.php?search=${encodeURIComponent(query)}`;
        }
    };

    const handleSearchInput = (e) => {
        const query = e.target.value.trim();
        clearTimeout(debounceTimer);

        debounceTimer = setTimeout(() => {
            if (query.length >= 2 || query.length === 0) {
                performSearch(query);
            }
        }, 300);
    };

    // Escuchar buscador del Header
    const headerSearchInput = document.getElementById('headerSearchInput');
    if (headerSearchInput) {
        headerSearchInput.addEventListener('input', handleSearchInput);
        headerSearchInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                performSearch(headerSearchInput.value.trim());
            }
        });
    }

    // Escuchar buscador de la página Tienda
    const tiendaSearchInput = document.getElementById('tiendaSearchInput');
    if (tiendaSearchInput) {
        tiendaSearchInput.addEventListener('input', handleSearchInput);
    }
});
