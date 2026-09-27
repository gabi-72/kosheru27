<?php
/**
 * Kosheru - API de Lista de Deseos (Wishlist)
 * GET  -> devuelve los productos guardados por el usuario en sesión
 * POST -> alterna (agrega/quita) un producto de la wishlist (action=toggle implícito)
 */
require_once __DIR__ . '/../modelo/connection.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';

requireAuth(true);

$user = getCurrentUser();
$user_id = (int)$user['id'];
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $sql = "SELECT p.id, p.name, p.category, p.origin, p.price, p.stock, p.image, p.description
            FROM wishlist w
            JOIN products p ON p.id = w.product_id
            WHERE w.user_id = $user_id
            ORDER BY w.id DESC";
    $res = mysqli_query($conn, $sql);

    $items = [];
    $product_ids = [];

    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            $row['price_formatted'] = formatCOP($row['price']);
            $items[] = $row;
            $product_ids[] = (int)$row['id'];
        }
    }

    sendJsonResponse(['success' => true, 'items' => $items, 'product_ids' => $product_ids]);
}

if ($method === 'POST') {
    $product_id = (int)($_POST['product_id'] ?? 0);

    if ($product_id <= 0) {
        sendJsonResponse(['success' => false, 'message' => 'Producto inválido.'], 400);
    }

    $check_prod = mysqli_query($conn, "SELECT id FROM products WHERE id = $product_id LIMIT 1");
    if (!$check_prod || mysqli_num_rows($check_prod) === 0) {
        sendJsonResponse(['success' => false, 'message' => 'El producto no existe.'], 404);
    }

    $check = mysqli_query($conn, "SELECT id FROM wishlist WHERE user_id = $user_id AND product_id = $product_id LIMIT 1");

    if ($check && mysqli_num_rows($check) > 0) {
        // Ya estaba guardado -> lo quitamos
        mysqli_query($conn, "DELETE FROM wishlist WHERE user_id = $user_id AND product_id = $product_id");
        sendJsonResponse(['success' => true, 'in_wishlist' => false, 'message' => 'Producto eliminado de tu wishlist.']);
    } else {
        // No estaba -> lo agregamos
        $ok = mysqli_query($conn, "INSERT INTO wishlist (user_id, product_id) VALUES ($user_id, $product_id)");
        if ($ok) {
            sendJsonResponse(['success' => true, 'in_wishlist' => true, 'message' => 'Producto agregado a tu wishlist.']);
        } else {
            sendJsonResponse(['success' => false, 'message' => 'No se pudo actualizar tu wishlist.'], 500);
        }
    }
}
?>
