<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<aside class="admin-sidebar" id="adminSidebar">
    <div class="sidebar-brand">
        <a href="index.php" style="display: flex; align-items: center; gap: 0.75rem; color: #fff; text-decoration: none;">
            <img src="../../img/logos/Isologo%20transferente%20(2).png" alt="Kosheru Admin" style="height: 38px; filter: brightness(1.8);">
            <div style="font-family: var(--fuente-titulos); font-weight: 900; font-size: 1.2rem; color: #f1c40f; letter-spacing: 1px;">Admin</div>
        </a>
    </div>

    <nav class="sidebar-menu">
        <a href="index.php" class="sidebar-item <?= $current_page === 'index.php' ? 'active' : '' ?>">
            <img src="../../img/icons/search-big.svg" alt="Dashboard" class="sidebar-icon-img" aria-hidden="true">
            <span>Dashboard</span>
        </a>
        <a href="usuarios.php" class="sidebar-item <?= $current_page === 'usuarios.php' ? 'active' : '' ?>">
            <img src="../../img/icons/user.svg" alt="Usuarios" class="sidebar-icon-img" aria-hidden="true">
            <span>Usuarios</span>
        </a>
        <a href="compras.php" class="sidebar-item <?= $current_page === 'compras.php' ? 'active' : '' ?>">
            <img src="../../img/icons/cart.svg" alt="Compras" class="sidebar-icon-img" aria-hidden="true">
            <span>Compras</span>
        </a>
        <a href="productos.php" class="sidebar-item <?= $current_page === 'productos.php' ? 'active' : '' ?>">
            <img src="../../img/icons/basket.svg" alt="Productos" class="sidebar-icon-img" aria-hidden="true">
            <span>Productos</span>
        </a>
        <a href="stock.php" class="sidebar-item <?= $current_page === 'stock.php' ? 'active' : '' ?>">
            <img src="../../img/icons/cart.svg" alt="Stock" class="sidebar-icon-img" aria-hidden="true">
            <span>Stock / Inventario</span>
        </a>
        <a href="facturacion.php" class="sidebar-item <?= $current_page === 'facturacion.php' ? 'active' : '' ?>">
            <img src="../../img/icons/credit-card-alt.svg" alt="Facturación" class="sidebar-icon-img" aria-hidden="true">
            <span>Facturación Admin</span>
        </a>
        <a href="sugerencias.php" class="sidebar-item <?= $current_page === 'sugerencias.php' ? 'active' : '' ?>">
            <img src="../../img/icons/envelope-alt.svg" alt="Sugerencias" class="sidebar-icon-img" aria-hidden="true">
            <span>Sugerencias</span>
        </a>
    </nav>

    <div class="sidebar-footer">
        <a href="../../index.php" class="sidebar-item btn-back-store">
            <img src="../../img/icons/door-open-alt.svg" alt="Volver" class="sidebar-icon-img" aria-hidden="true">
            <span>Volver a la Página</span>
        </a>
    </div>
</aside>
