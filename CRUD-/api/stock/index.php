<?php
/**
 * Kosheru - API de Control de Inventario y Movimientos de Stock
 */
require_once __DIR__ . '/../../../modelo/connection.php';
require_once __DIR__ . '/../../../includes/session.php';
require_once __DIR__ . '/../../../includes/auth.php';
require_once __DIR__ . '/../../../includes/helpers.php';

requireAdmin(true);

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    // Obtener Estado Actual de Stock
    $sql_stock = "SELECT id, name, category, stock FROM products ORDER BY stock ASC";
    $res_stock = mysqli_query($conn, $sql_stock);
    $products = [];
    if ($res_stock) {
        while ($row = mysqli_fetch_assoc($res_stock)) {
            $st = (int)$row['stock'];
            if ($st === 0) {
                $row['status_label'] = 'Agotado';
                $row['status_class'] = 'badge-cancelado';
            } elseif ($st <= 5) {
                $row['status_label'] = 'Poco Stock';
                $row['status_class'] = 'badge-pendiente';
            } else {
                $row['status_label'] = 'Disponible';
                $row['status_class'] = 'badge-completado';
            }
            $products[] = $row;
        }
    }

    // Obtener Historial de Movimientos de Inventario
    $sql_movs = "SELECT sm.*, p.name as product_name 
                 FROM stock_movements sm 
                 LEFT JOIN products p ON sm.product_id = p.id 
                 ORDER BY sm.id DESC LIMIT 100";
    $res_movs = mysqli_query($conn, $sql_movs);
    $movements = [];
    if ($res_movs) {
        while ($row = mysqli_fetch_assoc($res_movs)) {
            $movements[] = $row;
        }
    }

    sendJsonResponse([
        'success' => true,
        'products' => $products,
        'movements' => $movements
    ]);
}

if ($method === 'POST') {
    // Agregar Entrada Manual de Inventario
    $product_id = (int)($_POST['product_id'] ?? 0);
    $qty = (int)($_POST['quantity'] ?? 0);
    $reason = sanitize($_POST['reason'] ?? 'Entrada manual de inventario');

    if ($product_id <= 0 || $qty <= 0) {
        sendJsonResponse(['success' => false, 'message' => 'Ingresa una cantidad válida para reabastecer.'], 400);
    }

    $res_p = mysqli_query($conn, $sql = "SELECT stock, name FROM products WHERE id = $product_id");
    if (!$res_p || mysqli_num_rows($res_p) === 0) {
        sendJsonResponse(['success' => false, 'message' => 'Producto no encontrado.'], 404);
    }

    $prod = mysqli_fetch_assoc($res_p);
    $prev_stock = (int)$prod['stock'];
    $new_stock = $prev_stock + $qty;

    // Actualizar stock
    mysqli_query($conn, "UPDATE products SET stock = $new_stock WHERE id = $product_id");

    // Registrar movimiento
    $esc_reason = mysqli_real_escape_string($conn, $reason);
    mysqli_query($conn, "INSERT INTO stock_movements (product_id, movement_type, quantity, previous_stock, new_stock, reason) 
                         VALUES ($product_id, 'entrada', $qty, $prev_stock, $new_stock, '$esc_reason')");

    sendJsonResponse(['success' => true, 'message' => "Stock reabastecido. Nuevo inventario: {$new_stock} unidades."]);
}
?>
