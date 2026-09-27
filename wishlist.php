<?php
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/auth.php';

requireAuth(); // Redirige a login si no hay sesión
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Wishlist - Kosheru</title>
    <script>
        (function () {
            var saved = localStorage.getItem('kosheru_theme');
            document.documentElement.setAttribute('data-theme', saved === 'dark' ? 'dark' : 'light');
        })();
    </script>
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" href="img/logos/Isotipo transparente.png" type="image/png">
</head>
<body>

    <?php include __DIR__ . '/includes/header.php'; ?>

    <section class="tienda-hero">
        <div class="tienda-breadcrumb"><a href="index.php">Inicio</a> / <span>Mi Wishlist</span></div>
        <h1 class="tienda-hero-title">Mi Wishlist</h1>
        <p class="tienda-hero-desc">Los productos que has guardado para más adelante. Toca el corazón de nuevo para quitarlos.</p>
    </section>

    <div class="tienda-container">
        <div class="products-grid" id="wishlistGrid">
            <div style="grid-column:1/-1;text-align:center;padding:3rem;font-weight:700;color:var(--color-texto-suave);">Cargando tu wishlist...</div>
        </div>
    </div>

    <?php include __DIR__ . '/includes/footer.php'; ?>

    <script src="js/app.js"></script>
    <script src="js/theme.js"></script>
    <script src="js/header.js"></script>
    <script src="js/carrito.js"></script>
    <script src="js/product-detail.js"></script>
    <script src="js/productos.js"></script>
    <script src="js/wishlist.js"></script>
    <script src="js/wishlist-page.js"></script>
</body>
</html>
