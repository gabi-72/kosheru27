<?php
require_once __DIR__ . '/../includes/auth-admin.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios - Kosheru Admin</title>
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="../css/tablas.css">
    <link rel="stylesheet" href="../css/formularios.css">
    <link rel="stylesheet" href="../css/responsive-admin.css">
    <link rel="icon" href="img/logos/Isotipo transparente.png" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Outfit:wght@600;800;900&display=swap" rel="stylesheet">
</head>
<body class="admin-body">

    <?php include __DIR__ . '/../includes/sidebar.php'; ?>

    <div class="admin-main-wrapper">
        <?php include __DIR__ . '/../includes/navbar.php'; ?>

        <main class="admin-content">
            <h1 class="admin-page-title">Gestión de Usuarios</h1>

            <div class="admin-table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Apellido</th>
                            <th>Correo Electrónico</th>
                            <th>Usuario</th>
                            <th>Contraseña</th>
                            <th>Rol</th>
                            <th>Estado</th>
                            <th>Fecha</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="usersTableBody">
                        <!-- Carga dinámicamente vía JS -->
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <!-- Modal Cambiar Contraseña -->
    <div class="admin-modal-overlay" id="modalPassOverlay">
        <div class="admin-modal-box">
            <div class="admin-modal-header">
                <div class="admin-modal-title">Cambiar Contraseña de Usuario</div>
                <button type="button" onclick="AdminUsuarios.closePassModal()" style="font-size: 1.5rem;">&times;</button>
            </div>
            <form id="formChangePasswordModal">
                <input type="hidden" id="modalPassUserId">
                <p style="font-size: 0.95rem; color: #555; margin-bottom: 1rem;">
                    Usuario: <strong id="modalPassUsernameText" style="color: var(--color-rojo-kosheru);"></strong>
                </p>
                <div class="form-group">
                    <label for="modalPassInput">Nueva Contraseña (Mín. 6 caracteres)</label>
                    <input type="password" id="modalPassInput" placeholder="••••••••" required>
                </div>
                <div style="display: flex; gap: 1rem; margin-top: 1.5rem;">
                    <button type="submit" class="btn-primary" style="flex: 1; border: none;">Guardar Nueva Contraseña</button>
                    <button type="button" class="btn-secondary" onclick="AdminUsuarios.closePassModal()" style="color: var(--color-cafe-oscuro); border-color: #ccc;">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <script src="../../js/app.js"></script>
    <script src="../js/admin.js"></script>
    <script src="../js/usuarios.js"></script>
</body>
</html>
