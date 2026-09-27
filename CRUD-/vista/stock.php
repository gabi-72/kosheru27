<?php
require_once __DIR__ . '/../includes/auth-admin.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Control de Stock - Kosheru Admin</title>
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
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
                <h1 class="admin-page-title" style="margin: 0;">Control de Inventario</h1>
                <button type="button" class="btn-primary" onclick="AdminStock.openRefillModal()" style="border: none; display: inline-flex; align-items: center; gap: 0.45rem;">
                    <img src="../../img/icons/cart.svg" alt="" class="inline-icon" aria-hidden="true"> Reabastecer Inventario
                </button>
            </div>

            <!-- Estado Actual de Productos -->
            <div style="margin-bottom: 2.5rem;">
                <h3 style="font-size: 1.15rem; font-weight: 800; color: var(--color-cafe-oscuro); margin-bottom: 1rem;">
                    Nivel de Stock por Producto
                </h3>
                <div class="admin-table-container">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Categoría</th>
                                <th>Stock Actual</th>
                                <th>Estado</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody id="stockStatusTableBody">
                            <!-- Carga dinámicamente vía JS -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Historial de Movimientos -->
            <div>
                <h3 style="font-size: 1.15rem; font-weight: 800; color: var(--color-cafe-oscuro); margin-bottom: 1rem;">
                    Historial de Movimientos de Inventario
                </h3>
                <div class="admin-table-container">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Fecha / Hora</th>
                                <th>Producto</th>
                                <th>Tipo Movimiento</th>
                                <th style="text-align: center;">Cantidad</th>
                                <th style="text-align: center;">Stock Ant.</th>
                                <th style="text-align: center;">Stock Nuevo</th>
                                <th>Motivo / Referencia</th>
                            </tr>
                        </thead>
                        <tbody id="stockMovsTableBody">
                            <!-- Carga dinámicamente vía JS -->
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal Reabastecer Stock -->
    <div class="admin-modal-overlay" id="modalStockOverlay">
        <div class="admin-modal-box">
            <div class="admin-modal-header">
                <div class="admin-modal-title">Reabastecer Inventario</div>
                <button type="button" onclick="AdminStock.closeRefillModal()" style="font-size: 1.5rem;">&times;</button>
            </div>
            <form id="formRefillStockModal">
                <div class="form-group">
                    <label for="modalStockProductSelect">Seleccionar Producto *</label>
                    <select name="product_id" id="modalStockProductSelect" required></select>
                </div>

                <div class="form-group">
                    <label for="modalStockQty">Cantidad a Añadir *</label>
                    <input type="number" name="quantity" id="modalStockQty" min="1" placeholder="ej. 25" required>
                </div>

                <div class="form-group">
                    <label for="modalStockReason">Motivo de Reabastecimiento *</label>
                    <input type="text" name="reason" id="modalStockReason" placeholder="ej. Compra a proveedor importador" required>
                </div>

                <div style="display: flex; gap: 1rem; margin-top: 1.5rem;">
                    <button type="submit" class="btn-primary" style="flex: 1; border: none;">Confirmar Reabastecimiento</button>
                    <button type="button" class="btn-secondary" onclick="AdminStock.closeRefillModal()" style="color: var(--color-cafe-oscuro); border-color: #ccc;">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <script src="../../js/app.js"></script>
    <script src="../js/admin.js"></script>
    <script src="../js/stock.js"></script>
</body>
</html>
