/**
 * Kosheru - Lógica del Header y Modal de Login (Exacto a Referencias)
 */
document.addEventListener('DOMContentLoaded', () => {

    // 1. Modal de Iniciar Sesión
    const btnOpenLoginModal = document.getElementById('btnOpenLoginModal');
    const btnCloseLoginModal = document.getElementById('btnCloseLoginModal');
    const loginModalOverlay = document.getElementById('loginModalOverlay');

    if (btnOpenLoginModal && loginModalOverlay) {
        btnOpenLoginModal.addEventListener('click', (e) => {
            e.preventDefault();
            loginModalOverlay.classList.add('active');
        });
    }

    if (btnCloseLoginModal && loginModalOverlay) {
        btnCloseLoginModal.addEventListener('click', () => {
            loginModalOverlay.classList.remove('active');
        });
    }

    if (loginModalOverlay) {
        loginModalOverlay.addEventListener('click', (e) => {
            if (e.target === loginModalOverlay) {
                loginModalOverlay.classList.remove('active');
            }
        });
    }

    // 2. NOTA: la lógica del Cart Drawer (abrir / cerrar / click fuera) vive
    //    ahora únicamente en carrito.js, para no tener dos archivos
    //    controlando el mismo overlay con listeners duplicados.

    // 3. User Dropdown Toggle
    const btnUserMenu = document.getElementById('btnUserMenu');
    const userDropdown = document.getElementById('userDropdown');

    if (btnUserMenu && userDropdown) {
        btnUserMenu.addEventListener('click', (e) => {
            e.stopPropagation();
            userDropdown.classList.toggle('show');
        });

        document.addEventListener('click', (e) => {
            if (!userDropdown.contains(e.target) && e.target !== btnUserMenu) {
                userDropdown.classList.remove('show');
            }
        });
    }

    // 4. Delegación de eventos: botón "Añadir +" de cualquier tarjeta destacada
    //    (Hero, y reutilizable a futuro para otras tarjetas con la misma clase).
    //    Se usa un solo listener y los datos viajan en atributos data-*, así se
    //    evita el bug de comillas de nombres con apóstrofe (ej. "Lion's Mane")
    //    que rompía los onclick con JSON embebido.
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('.btn-add-featured');
        if (!btn) return;

        if (!window.KosheruCart) {
            console.error('KosheruCart no está disponible todavía.');
            return;
        }

        const product = {
            id: parseInt(btn.dataset.id, 10) || 0,
            name: btn.dataset.name || '',
            price: parseFloat(btn.dataset.price) || 0,
            image: btn.dataset.image || '',
            origin: btn.dataset.origin || ''
        };

        KosheruCart.addToCart(product, btn);

        // Abrir el drawer para que el cliente vea de inmediato que se agregó.
        const cartDrawerOverlay = document.getElementById('cartDrawerOverlay');
        if (cartDrawerOverlay) {
            cartDrawerOverlay.classList.add('active');
        }
    });
});
