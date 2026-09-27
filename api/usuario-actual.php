<?php
/**
 * Kosheru - API de Usuario Actual en Sesión
 */
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';

$user = getCurrentUser();

sendJsonResponse([
    'success' => true,
    'logged_in' => isLoggedIn(),
    'user' => $user
]);
?>
