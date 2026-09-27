<?php
require_once __DIR__ . '/../includes/auth-admin.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sugerencias de Clientes - Kosheru Admin</title>
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="../css/tablas.css">
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
            <h1 class="admin-page-title">Sugerencias y Mensajes de Contacto</h1>

            <div class="admin-table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Correo Electrónico</th>
                            <th>Mensaje / Sugerencia</th>
                            <th>Fecha</th>
                            <th>Estado</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody id="suggTableBody">
                        <!-- Carga dinámicamente vía JS -->
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <script src="../../js/app.js"></script>
    <script src="../js/admin.js"></script>
    <script src="../js/sugerencias.js"></script>
</body>
</html>
