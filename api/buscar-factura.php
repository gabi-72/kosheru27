<?php
/**
 * Kosheru - API de Búsqueda y Detalle de Facturas
 * Garantiza ownership: Un usuario normal solo puede consultar SUS facturas.
 */
require_once __DIR__ . '/../modelo/connection.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';

requireAuth(true);

$invoice_number = isset($_GET['invoice_number']) ? trim($_GET['invoice_number']) : '';

if (empty($invoice_number)) {
    sendJsonResponse(['success' => false, 'message' => 'Por favor proporciona un número de factura.'], 400);
}

$esc_invoice = mysqli_real_escape_string($conn, $invoice_number);

// Consulta base de la factura/compra
$sql_purchase = "SELECT * FROM purchases WHERE invoice_number = '$esc_invoice' LIMIT 1";
$res_p = mysqli_query($conn, $sql_purchase);

if (!$res_p || mysqli_num_rows($res_p) === 0) {
    sendJsonResponse(['success' => false, 'message' => 'Factura no encontrada.'], 404);
}

$purchase = mysqli_fetch_assoc($res_p);

// Verificación de propiedad (Ownership): El admin puede ver todo, el usuario normal solo la suya
$current_user = getCurrentUser();
if (!isAdmin() && (int)$purchase['user_id'] !== (int)$current_user['id']) {
    sendJsonResponse(['success' => false, 'message' => 'Acceso denegado. No tienes permisos para consultar esta factura.'], 403);
}

// Formateo de montos
$purchase['subtotal_formatted'] = formatCOP($purchase['subtotal']);
$purchase['tax_iva_formatted'] = formatCOP($purchase['tax_iva']);
$purchase['total_price_formatted'] = formatCOP($purchase['total_price']);

// Consultar ítems de la compra
$purchase_id = (int)$purchase['id'];
$sql_items = "SELECT * FROM purchase_items WHERE purchase_id = $purchase_id ORDER BY id ASC";
$res_items = mysqli_query($conn, $sql_items);

$items = [];
if ($res_items) {
    while ($item = mysqli_fetch_assoc($res_items)) {
        $item['unit_price_formatted'] = formatCOP($item['unit_price']);
        $item['total_price_formatted'] = formatCOP($item['total_price']);
        $items[] = $item;
    }
}

sendJsonResponse([
    'success' => true,
    'purchase' => $purchase,
    'items' => $items
]);
?>
