<?php
/**
 * Kosheru - Funciones de Ayuda y Utilidades
 */

require_once __DIR__ . '/session.php';

/**
 * Emite una respuesta JSON limpia compatible con InfinityFree sin contaminación de warnings/notices
 */
function sendJsonResponse($data, $statusCode = 200) {
    ob_start();
    error_reporting(0);
    ini_set('display_errors', '0');
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    ob_clean();
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function safeJSONResponse($data, $statusCode = 200) {
    sendJsonResponse($data, $statusCode);
}

/**
 * Formatea un precio numérico a Pesos Colombianos (COP)
 * Ejemplo: 18900 => "$18.900 COP"
 */
function formatCOP($amount) {
    return '$' . number_format((float)$amount, 0, ',', '.') . ' COP';
}

/**
 * Sanitiza una cadena de texto para evitar XSS
 */
function sanitize($input) {
    return htmlspecialchars(trim((string)$input), ENT_QUOTES, 'UTF-8');
}

/**
 * Genera un código de factura único con formato FAC-XXXXX (ej: FAC-91622)
 */
function generateInvoiceNumber($conn) {
    do {
        $rand_num = rand(10000, 99999);
        $code = "FAC-" . $rand_num;
        $esc_code = mysqli_real_escape_string($conn, $code);
        $res = mysqli_query($conn, "SELECT id FROM purchases WHERE invoice_number = '$esc_code' LIMIT 1");
    } while ($res && mysqli_num_rows($res) > 0);
    
    return $code;
}

/**
 * Escribe un registro transaccional en logs/pedidos_log.txt
 */
function logOrderTransaction($invoice_number, $user_name, $total) {
    $log_dir = __DIR__ . '/../logs';
    if (!is_dir($log_dir)) {
        @mkdir($log_dir, 0755, true);
    }
    $file = $log_dir . '/pedidos_log.txt';
    $date = date('Y-m-d H:i:s');
    $line = "[$date] Factura: $invoice_number | Usuario: $user_name | Total: " . formatCOP($total) . PHP_EOL;
    @file_put_contents($file, $line, FILE_APPEND);
}
?>
