<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/auth.php';

$user_data = getCurrentUser();
?>
<header class="admin-navbar">
    <div style="display: flex; align-items: center; gap: 1rem;">
        <button type="button" class="btn-admin-toggle" id="btnAdminMobileToggle" aria-label="Toggle Sidebar">
            ☰
        </button>
        <div style="font-weight: 800; font-size: 1.1rem; color: var(--color-cafe-oscuro);">
            Panel de Control Kosheru S.A.S.
        </div>
    </div>

    <div style="display: flex; align-items: center; gap: 1rem;">
        <div style="text-align: right; font-size: 0.85rem;">
            <div style="font-weight: 800; color: var(--color-rojo-oscuro);"><?= htmlspecialchars($user_data['name']) ?></div>
            <div style="color: #777; font-size: 0.75rem;">Administrador</div>
        </div>
        <a href="../../logout.php" class="btn-primary" style="padding: 0.4rem 0.9rem; font-size: 0.85rem; background: var(--color-rojo-kosheru); display: inline-flex; align-items: center; gap: 0.35rem;">
            <img src="../../img/icons/door-open-alt.svg" alt="Salir" class="inline-icon" aria-hidden="true"> Salir
        </a>
    </div>
</header>
