<?php
/**
 * Kosheru - API de Dashboard Administrativo
 * Retorna métricas en tiempo real con filtro opcional de fechas,
 * más series de datos para los gráficos (tendencia, top productos, categorías).
 */
require_once __DIR__ . '/../../modelo/connection.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/helpers.php';

requireAdmin(true);

$desde = isset($_GET['desde']) ? trim($_GET['desde']) : '';
$hasta = isset($_GET['hasta']) ? trim($_GET['hasta']) : '';

// 1. Métricas de Usuarios y Productos
$res_users = mysqli_query($conn, "SELECT COUNT(*) as total FROM users");
$total_users = ($row = mysqli_fetch_assoc($res_users)) ? (int)$row['total'] : 0;

$res_prod_avail = mysqli_query($conn, "SELECT COUNT(*) as total FROM products WHERE stock > 5");
$total_avail = ($row = mysqli_fetch_assoc($res_prod_avail)) ? (int)$row['total'] : 0;

$res_prod_out = mysqli_query($conn, "SELECT COUNT(*) as total FROM products WHERE stock = 0");
$total_out = ($row = mysqli_fetch_assoc($res_prod_out)) ? (int)$row['total'] : 0;

$res_prod_low = mysqli_query($conn, "SELECT COUNT(*) as total FROM products WHERE stock > 0 AND stock <= 5");
$total_low = ($row = mysqli_fetch_assoc($res_prod_low)) ? (int)$row['total'] : 0;

// 2. Filtros de Fecha para Ventas
$where_date = "";
if (!empty($desde) && !empty($hasta)) {
    $esc_d = mysqli_real_escape_string($conn, $desde);
    $esc_h = mysqli_real_escape_string($conn, $hasta);
    $where_date = " WHERE order_date BETWEEN '$esc_d' AND '$esc_h'";
}

$today = date('Y-m-d');
$month_start = date('Y-m-01');

// Compras del día
$res_today_orders = mysqli_query($conn, "SELECT COUNT(*) as total, SUM(total_price) as sales FROM purchases WHERE order_date = '$today' AND status = 'completado'");
$row_today = mysqli_fetch_assoc($res_today_orders);
$orders_today = (int)($row_today['total'] ?? 0);
$sales_today = (float)($row_today['sales'] ?? 0);

// Ventas del mes
$res_month_sales = mysqli_query($conn, "SELECT SUM(total_price) as sales FROM purchases WHERE order_date >= '$month_start' AND status = 'completado'");
$row_month = mysqli_fetch_assoc($res_month_sales);
$sales_month = (float)($row_month['sales'] ?? 0);

// Ingresos totales (Filtrados si hay rango)
$sql_total_rev = "SELECT SUM(total_price) as total_rev, COUNT(*) as total_invoices FROM purchases" . $where_date;
$res_rev = mysqli_query($conn, $sql_total_rev);
$row_rev = mysqli_fetch_assoc($res_rev);
$total_revenue = (float)($row_rev['total_rev'] ?? 0);
$total_invoices = (int)($row_rev['total_invoices'] ?? 0);

// Sugerencias recibidas
$res_sugg = mysqli_query($conn, "SELECT COUNT(*) as total FROM suggestions WHERE status = 'pendiente'");
$total_sugg = ($row = mysqli_fetch_assoc($res_sugg)) ? (int)$row['total'] : 0;

// =========================================================================
// 3. DATOS PARA GRÁFICOS
// Estos tres bloques son independientes del filtro desde/hasta de arriba,
// para que el dashboard siempre muestre "la foto reciente" del negocio.
// =========================================================================

// 3a. Tendencia de ventas de los últimos 14 días (rellenando días sin ventas con 0)
$days_map = [];
for ($i = 13; $i >= 0; $i--) {
    $d = date('Y-m-d', strtotime("-$i day"));
    $days_map[$d] = 0.0;
}

$res_trend = mysqli_query($conn, "SELECT DATE(order_date) as day, SUM(total_price) as total
                                   FROM purchases
                                   WHERE status = 'completado' AND order_date >= DATE_SUB(CURDATE(), INTERVAL 13 DAY)
                                   GROUP BY DATE(order_date)");
if ($res_trend) {
    while ($row = mysqli_fetch_assoc($res_trend)) {
        if (isset($days_map[$row['day']])) {
            $days_map[$row['day']] = (float)$row['total'];
        }
    }
}

$sales_trend = [];
foreach ($days_map as $day => $total) {
    $sales_trend[] = ['date' => $day, 'total' => $total];
}

// 3b. Top 5 productos más vendidos (por unidades)
$top_products = [];
$res_top = mysqli_query($conn, "SELECT product_name, SUM(quantity) as total_qty
                                 FROM purchase_items
                                 GROUP BY product_name
                                 ORDER BY total_qty DESC
                                 LIMIT 5");
if ($res_top) {
    while ($row = mysqli_fetch_assoc($res_top)) {
        $top_products[] = ['name' => $row['product_name'], 'qty' => (int)$row['total_qty']];
    }
}

// 3c. Ventas por categoría (distribución de ingresos)
$sales_by_category = [];
$res_cat_sales = mysqli_query($conn, "SELECT p.category, SUM(pi.total_price) as total
                                       FROM purchase_items pi
                                       JOIN products p ON p.id = pi.product_id
                                       GROUP BY p.category
                                       ORDER BY total DESC");
if ($res_cat_sales) {
    while ($row = mysqli_fetch_assoc($res_cat_sales)) {
        $sales_by_category[] = ['category' => $row['category'], 'total' => (float)$row['total']];
    }
}

sendJsonResponse([
    'success' => true,
    'metrics' => [
        'total_users' => $total_users,
        'total_available' => $total_avail,
        'total_out_of_stock' => $total_out,
        'total_low_stock' => $total_low,
        'orders_today' => $orders_today,
        'sales_today_formatted' => formatCOP($sales_today),
        'sales_month_formatted' => formatCOP($sales_month),
        'total_revenue_formatted' => formatCOP($total_revenue),
        'total_invoices' => $total_invoices,
        'pending_suggestions' => $total_sugg
    ],
    'charts' => [
        'sales_trend' => $sales_trend,
        'top_products' => $top_products,
        'sales_by_category' => $sales_by_category
    ]
]);
?>
