<?php
/**
 * Kosheru - Módulo de Facturación Administrativa (PRIORIDAD MÁXIMA)
 * Soporta los 6 Filtros Acumulables y Detalle Completo de Comprador e Ítems
 */
require_once __DIR__ . '/../../../modelo/connection.php';
require_once __DIR__ . '/../../../includes/session.php';
require_once __DIR__ . '/../../../includes/auth.php';
require_once __DIR__ . '/../../../includes/helpers.php';

requireAdmin(true);

$invoice_number = isset($_GET['invoice_number']) ? trim($_GET['invoice_number']) : '';
$customer_name = isset($_GET['customer_name']) ? trim($_GET['customer_name']) : '';
$customer_email = isset($_GET['customer_email']) ? trim($_GET['customer_email']) : '';
$document_id = isset($_GET['document_id']) ? trim($_GET['document_id']) : '';
$fecha_desde = isset($_GET['fecha_desde']) ? trim($_GET['fecha_desde']) : '';
$fecha_hasta = isset($_GET['fecha_hasta']) ? trim($_GET['fecha_hasta']) : '';

$where_clauses = [];

// 1. Filtro N° Factura
if (!empty($invoice_number)) {
    $esc_inv = mysqli_real_escape_string($conn, $invoice_number);
    $where_clauses[] = "p.invoice_number LIKE '%$esc_inv%'";
}

// 2. Filtro Nombre Cliente
if (!empty($customer_name)) {
    $esc_name = mysqli_real_escape_string($conn, $customer_name);
    $where_clauses[] = "p.user_name LIKE '%$esc_name%'";
}

// 3. Filtro Correo
if (!empty($customer_email)) {
    $esc_email = mysqli_real_escape_string($conn, $customer_email);
    $where_clauses[] = "p.user_email LIKE '%$esc_email%'";
}

// 4. Filtro Cédula / TI
if (!empty($document_id)) {
    $esc_doc = mysqli_real_escape_string($conn, $document_id);
    $where_clauses[] = "p.document_id LIKE '%$esc_doc%'";
}

// 5. Filtro Fecha Desde
if (!empty($fecha_desde)) {
    $esc_fd = mysqli_real_escape_string($conn, $fecha_desde);
    $where_clauses[] = "p.order_date >= '$esc_fd'";
}

// 6. Filtro Fecha Hasta
if (!empty($fecha_hasta)) {
    $esc_fh = mysqli_real_escape_string($conn, $fecha_hasta);
    $where_clauses[] = "p.order_date <= '$esc_fh'";
}

$sql = "SELECT p.* FROM purchases p";
if (!empty($where_clauses)) {
    $sql .= " WHERE " . implode(' AND ', $where_clauses);
}
$sql .= " ORDER BY p.id DESC";

$res = mysqli_query($conn, $sql);
$facturas = [];

if ($res) {
    while ($row = mysqli_fetch_assoc($res)) {
        $row['subtotal_formatted'] = formatCOP($row['subtotal']);
        $row['tax_iva_formatted'] = formatCOP($row['tax_iva']);
        $row['total_price_formatted'] = formatCOP($row['total_price']);
        $facturas[] = $row;
    }
}

sendJsonResponse([
    'success' => true,
    'total_count' => count($facturas),
    'facturas' => $facturas
]);
?>
