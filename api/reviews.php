<?php
/**
 * Kosheru - API de Reseñas de Productos
 * GET  ?product_id=X -> lista de reseñas del producto (público)
 * POST                -> envía una nueva reseña (requiere sesión, 1 por usuario/producto)
 *
 * IMPORTANTE: esta API asume que ya corriste la migración que agrega la
 * columna `user_id` a `product_reviews` (ver sql/migracion_wishlist_resenas.sql).
 */
require_once __DIR__ . '/../modelo/connection.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $product_id = (int)($_GET['product_id'] ?? 0);

    if ($product_id <= 0) {
        sendJsonResponse(['success' => false, 'message' => 'Producto inválido.'], 400);
    }

    $sql = "SELECT customer_name, rating, comment, created_at
            FROM product_reviews
            WHERE product_id = $product_id
            ORDER BY id DESC";
    $res = mysqli_query($conn, $sql);

    $reviews = [];
    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            $reviews[] = $row;
        }
    }

    $user_reviewed = false;
    if (isLoggedIn()) {
        $user = getCurrentUser();
        $uid = (int)$user['id'];
        $check = mysqli_query($conn, "SELECT id FROM product_reviews WHERE product_id = $product_id AND user_id = $uid LIMIT 1");
        $user_reviewed = $check && mysqli_num_rows($check) > 0;
    }

    sendJsonResponse(['success' => true, 'reviews' => $reviews, 'user_reviewed' => $user_reviewed]);
}

if ($method === 'POST') {
    requireAuth(true);

    $user = getCurrentUser();
    $user_id = (int)$user['id'];
    $customer_name = trim((string)$user['name'] . ' ' . (string)$user['lastname']);
    if ($customer_name === '') {
        $customer_name = $user['username'] ?? 'Cliente Kosheru';
    }

    $product_id = (int)($_POST['product_id'] ?? 0);
    $rating = (int)($_POST['rating'] ?? 0);
    $comment = sanitize($_POST['comment'] ?? '');

    if ($product_id <= 0 || $rating < 1 || $rating > 5) {
        sendJsonResponse(['success' => false, 'message' => 'Selecciona una calificación de 1 a 5 estrellas.'], 400);
    }

    $check_prod = mysqli_query($conn, "SELECT id FROM products WHERE id = $product_id LIMIT 1");
    if (!$check_prod || mysqli_num_rows($check_prod) === 0) {
        sendJsonResponse(['success' => false, 'message' => 'El producto no existe.'], 404);
    }

    $check_dup = mysqli_query($conn, "SELECT id FROM product_reviews WHERE product_id = $product_id AND user_id = $user_id LIMIT 1");
    if ($check_dup && mysqli_num_rows($check_dup) > 0) {
        sendJsonResponse(['success' => false, 'message' => 'Ya dejaste una reseña para este producto.'], 400);
    }

    $esc_name = mysqli_real_escape_string($conn, $customer_name);
    $esc_comment = mysqli_real_escape_string($conn, $comment);

    $sql = "INSERT INTO product_reviews (product_id, user_id, customer_name, rating, comment)
            VALUES ($product_id, $user_id, '$esc_name', $rating, '$esc_comment')";

    if (mysqli_query($conn, $sql)) {
        sendJsonResponse(['success' => true, 'message' => '¡Gracias por tu reseña!']);
    } else {
        sendJsonResponse(['success' => false, 'message' => 'No se pudo guardar tu reseña. Verifica que hayas corrido la migración de la base de datos.'], 500);
    }
}
?>
