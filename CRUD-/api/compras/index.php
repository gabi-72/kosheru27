<?php
/**
 * Kosheru - API de Gestión de Compras Admin
 */
require_once __DIR__ . '/../../../modelo/connection.php';
require_once __DIR__ . '/../../../includes/session.php';
require_once __DIR__ . '/../../../includes/auth.php';
require_once __DIR__ . '/../../../includes/helpers.php';

requireAdmin(true);

$sql = "SELECT id, invoice_number, user_name, user_email, order_date, order_time, payment_method, total_price, status 
        FROM purchases ORDER BY id DESC";
$res = mysqli_query($conn, $sql);
$compras = [];

if ($res) {
    while ($row = mysqli_fetch_assoc($res)) {
        $row['total_formatted'] = formatCOP($row['total_price']);
        $compras[] = $row;
    }
}

sendJsonResponse(['success' => true, 'compras' => $compras]);
?>
