<?php
/**
 * Kosheru - API de Gestión de Sugerencias Admin
 */
require_once __DIR__ . '/../../../modelo/connection.php';
require_once __DIR__ . '/../../../includes/session.php';
require_once __DIR__ . '/../../../includes/auth.php';
require_once __DIR__ . '/../../../includes/helpers.php';

requireAdmin(true);

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $sql = "SELECT * FROM suggestions ORDER BY id DESC";
    $res = mysqli_query($conn, $sql);
    $suggestions = [];

    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            $suggestions[] = $row;
        }
    }

    sendJsonResponse(['success' => true, 'suggestions' => $suggestions]);
}

if ($method === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $action = $_POST['action'] ?? '';

    if ($id > 0 && $action === 'mark_reviewed') {
        $sql = "UPDATE suggestions SET status = 'revisado' WHERE id = $id";
        if (mysqli_query($conn, $sql)) {
            sendJsonResponse(['success' => true, 'message' => 'Sugerencia marcada como revisada.']);
        }
    }

    sendJsonResponse(['success' => false, 'message' => 'Error al procesar la operación.'], 400);
}
?>
