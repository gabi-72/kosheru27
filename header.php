<?php
require_once __DIR__ . '/session.php';
require_once __DIR__ . '/auth.php';

$is_logged = isLoggedIn();
$is_admin = isAdmin();
$user_data = getCurrentUser();
$current_page = basename($_SERVER['PHP_SELF']);
?>
<header class="header-main">
    <div class="header-container">
        <a href="index.php" class="brand-logo" title="Kosheru - Superalimentos">
            <img src="img/logos/Isologo%20transferente%20(2).png" alt="Kosheru Isologo">
            <span class="brand-logo-text"><strong>Kosheru</strong><span>Superalimentos</span></span>
        </a>

        <div class="header-search">
            <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <circle cx="11" cy="11" r="7"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
            <input type="text" id="headerSearchInput" placeholder="Busca quinoa, matcha, spirulina...">
        </div>

        <nav class="nav-menu">
            <a href="index.php" class="nav-link <?= ($current_page == 'index.php') ? 'active' : '' ?>">Inicio</a>
            <a href="tienda.php" class="nav-link <?= ($current_page == 'tienda.php') ? 'active' : '' ?>">Tienda</a>
            <a href="index.php#nosotros" class="nav-link">Nosotros</a>
            <a href="index.php#contacto" class="nav-link">Contacto</a>
        </nav>

        <div class="header-actions">
            <?php if (!$is_logged): ?>
                <button type="button" class="btn-theme-toggle" title="Cambiar tema" aria-label="Cambiar a modo oscuro">
                    <svg class="icon-sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="4"></circle><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"/></svg>
                    <svg class="icon-moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/></svg>
                </button>
            <?php endif; ?>

            <button type="button" class="btn-cart-trigger" id="btnOpenCart" title="Ver Carrito de Compras">
                <img src="img/icons/cart.svg" alt="Carrito" class="inline-icon" aria-hidden="true">
                <span class="cart-badge" id="cartCountBadge">0</span>
            </button>

            <div class="user-menu-container">
                <?php if ($is_logged): ?>
                    <button type="button" class="btn-cta-pill" id="btnUserMenu"><?= htmlspecialchars($user_data['name']) ?></button>
                    <div class="user-dropdown" id="userDropdown">
                        <div class="dropdown-header">
                            <p class="dropdown-greeting">Hola, <?= htmlspecialchars($user_data['name']) ?></p>
                            <p class="dropdown-email"><?= htmlspecialchars($user_data['email']) ?></p>
                        </div>
                        <a href="mis-compras.php" class="dropdown-item-btn btn-dropdown-purchases">Mis Compras</a>
                        <?php if ($is_admin): ?>
                            <a href="CRUD-/vista/index.php" class="dropdown-item-btn btn-dropdown-admin">Panel Admin</a>
                        <?php endif; ?>
                        <button type="button" class="dropdown-item-btn btn-dropdown-theme btn-theme-toggle" title="Cambiar tema" aria-label="Cambiar a modo oscuro">
                            <svg class="icon-sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="4"></circle><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"/></svg>
                            <svg class="icon-moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/></svg>
                            <span class="theme-toggle-label">Modo oscuro</span>
                        </button>
                        <a href="logout.php" class="dropdown-item-btn btn-dropdown-logout">Cerrar Sesión</a>
                    </div>
                <?php else: ?>
                    <button type="button" class="btn-cta-pill" id="btnOpenLoginModal">Ingresar</button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</header>

<div class="modal-overlay" id="loginModalOverlay">
    <div class="login-modal-card">
        <button type="button" class="btn-close-modal" id="btnCloseLoginModal" title="Cerrar">&times;</button>
        <h2 class="modal-title">Iniciar Sesión</h2>
        <p class="modal-subtitle">Accede a tu cuenta de Kosheru para comprar tus superalimentos.</p>
        <div class="modal-alert error" id="modalAuthAlert"></div>
        <form id="modalLoginForm">
            <div class="modal-form-group">
                <label for="modalUsername" class="modal-label">Nombre de usuario</label>
                <input type="text" id="modalUsername" name="username" class="modal-input" placeholder="Tu usuario" required autocomplete="username">
            </div>
            <div class="modal-form-group">
                <label for="modalPassword" class="modal-label">Contraseña</label>
                <input type="password" id="modalPassword" name="password" class="modal-input" placeholder="········" required autocomplete="current-password">
            </div>
            <button type="submit" class="btn-modal-submit" id="btnModalLoginSubmit">Ingresar</button>
        </form>
        <p class="modal-footer-link">¿No tienes una cuenta? <a href="registro.php">Regístrate aquí</a></p>
    </div>
</div>

<div class="cart-drawer-overlay" id="cartDrawerOverlay">
    <div class="cart-drawer">
        <div class="cart-header">
            <h3 class="cart-title">Tu Carrito</h3>
            <button type="button" class="btn-close-cart" id="btnCloseCart">&times;</button>
        </div>
        <div class="cart-body" id="cartItemsContainer">
            <div id="cartItemsList" class="cart-items-list"></div>
            <div id="cartEmptyMsg" class="cart-empty-state" style="display:none; text-align:center; color: var(--color-texto-muted); padding: 2rem 1rem;">
                <p style="font-weight: 700;">Tu carrito está vacío</p>
                <p>Agrega algún producto para verlo aquí.</p>
            </div>
        </div>
        <div class="cart-footer">
            <div class="cart-summary-line"><span>Subtotal:</span><span id="cartSubtotalText">$0 COP</span></div>
            <div class="cart-summary-line"><span>IVA (19% incluido):</span><span id="cartIvaText">$0 COP</span></div>
            <div class="cart-summary-total"><span>Total:</span><span id="cartTotalText">$0 COP</span></div>
            <button type="button" class="btn-process-checkout" id="btnCheckoutTrigger">Realizar Compra</button>
        </div>
    </div>
</div>
