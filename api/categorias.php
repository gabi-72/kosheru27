<?php
/**
 * Kosheru - API de Categorías
 */
require_once __DIR__ . '/../modelo/connection.php';
require_once __DIR__ . '/../includes/helpers.php';

$query = mysqli_query($conn, "SELECT id, slug, name, description FROM categories ORDER BY id ASC");
$categorias = [];

if ($query) {
    while ($row = mysqli_fetch_assoc($query)) {
        $categorias[] = $row;
    }
}

sendJsonResponse([
    'success' => true,
    'categorias' => $categorias
]);
?>
