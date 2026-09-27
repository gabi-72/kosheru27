<?php
require_once __DIR__ . '/../includes/auth-admin.php';
$fac_param = isset($_GET['fac']) ? htmlspecialchars(trim($_GET['fac'])) : '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facturación Administrativa - Kosheru Admin</title>
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="../css/tablas.css">
    <link rel="stylesheet" href="../css/formularios.css">
    <link rel="stylesheet" href="../css/factura-admin.css">
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
            <h1 class="admin-page-title">Módulo de Facturación Administrativa</h1>

            <!-- Tarjeta con los 6 Filtros de Búsqueda Acumulables -->
            <div class="admin-factura-search-card">
                <h3 style="font-size: 1.05rem; font-weight: 800; color: var(--color-rojo-oscuro); margin-bottom: 1rem; text-transform: uppercase; letter-spacing: 1px;">
                    🔍 Filtros de Búsqueda Avanzada de Facturas
                </h3>

                <div class="search-filters-grid">
                    <!-- 1. N° Factura -->
                    <div class="form-group" style="margin:0;">
                        <label for="filterInvoiceNum">1. N° Factura</label>
                        <input type="text" id="filterInvoiceNum" placeholder="ej. FAC-91622" value="<?= $fac_param ?>">
                    </div>

                    <!-- 2. Nombre Cliente -->
                    <div class="form-group" style="margin:0;">
                        <label for="filterCustomerName">2. Nombre Cliente</label>
                        <input type="text" id="filterCustomerName" placeholder="ej. Sara Hernandez">
                    </div>

                    <!-- 3. Correo -->
                    <div class="form-group" style="margin:0;">
                        <label for="filterCustomerEmail">3. Correo Electrónico</label>
                        <input type="text" id="filterCustomerEmail" placeholder="ej. sara@outlook.es">
                    </div>

                    <!-- 4. Cédula/TI -->
                    <div class="form-group" style="margin:0;">
                        <label for="filterDocumentId">4. Cédula / TI</label>
                        <input type="text" id="filterDocumentId" placeholder="ej. 1073236669">
                    </div>

                    <!-- 5. Fecha Desde -->
                    <div class="form-group" style="margin:0;">
                        <label for="filterFechaDesde">5. Fecha Desde</label>
                        <input type="date" id="filterFechaDesde">
                    </div>

                    <!-- 6. Fecha Hasta -->
                    <div class="form-group" style="margin:0;">
                        <label for="filterFechaHasta">6. Fecha Hasta</label>
                        <input type="date" id="filterFechaHasta">
                    </div>
                </div>

                <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                    <button type="button" class="btn-primary" id="btnBuscarFacturas" style="border: none; padding: 0.75rem 1.8rem;">
                        [ Buscar Facturas ]
                    </button>
                    <button type="button" class="btn-secondary" id="btnLimpiarFacturas" style="color: var(--color-cafe-oscuro); border-color: #ccc; padding: 0.75rem 1.5rem;">
                        [ Limpiar Filtros ]
                    </button>
                </div>
            </div>

            <!-- Tabla de Resultados de Facturas -->
            <div class="admin-table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Factura</th>
                            <th>Cliente</th>
                            <th>Correo</th>
                            <th>Documento</th>
                            <th>Fecha</th>
                            <th>Total</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="facturacionTableBody">
                        <!-- Carga dinámicamente vía JS -->
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <!-- Modal Detalle Factura Admin (Factura Admin = Factura Cliente) -->
    <div id="adminInvoiceModalContainer" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.6); backdrop-filter: blur(5px); z-index: 3500; overflow-y: auto; padding: 2rem 1rem;">
        <div style="max-width: 880px; margin: 0 auto; position: relative;">
            <button type="button" onclick="document.getElementById('adminInvoiceModalContainer').style.display='none';" style="position: absolute; right: 15px; top: 15px; z-index: 10; background: #fff; border-radius: 50%; width: 40px; height: 40px; font-size: 1.5rem; font-weight: bold; border: 2px solid var(--color-rojo-kosheru); color: var(--color-rojo-kosheru);">
                &times;
            </button>
            <div id="adminInvoiceModalContent"></div>
        </div>
    </div>

    <script src="../../js/app.js"></script>
    <script src="../js/admin.js"></script>
    <script src="../js/facturacion.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const facParam = '<?= $fac_param ?>';
            if (facParam) {
                AdminFacturacion.verFacturaDetalle(facParam);
            }
        });
    </script>
</body>
</html>
