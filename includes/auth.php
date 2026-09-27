<?php
/**
 * Kosheru - Control de Permisos y Autenticación
 */

require_once __DIR__ . '/session.php';
require_once __DIR__ . '/helpers.php';

function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function isAdmin() {
    return isLoggedIn() && isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin';
}

function getCurrentUser() {
    if (!isLoggedIn()) return null;
    return [
        'id' => $_SESSION['user_id'],
        'name' => $_SESSION['name'] ?? 'Usuario',
        'lastname' => $_SESSION['lastname'] ?? '',
        'username' => $_SESSION['username'] ?? '',
        'email' => $_SESSION['email'] ?? '',
        'rol' => $_SESSION['rol'] ?? 'usuario'
    ];
}

function loginUser($user) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    session_regenerate_id(true);
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['name'] = $user['name'];
    $_SESSION['lastname'] = $user['lastname'] ?? '';
    $_SESSION['username'] = $user['username'] ?? '';
    $_SESSION['email'] = $user['email'] ?? '';
    $_SESSION['rol'] = $user['rol'] ?? 'usuario';
}

function setAuthenticatedUser($user) {
    loginUser($user);
}

function requireAuth($is_api = false) {
    if (!isLoggedIn()) {
        if ($is_api) {
            safeJSONResponse(['success' => false, 'message' => 'Sesión requerida. Inicia sesión para continuar.'], 401);
        } else {
            header('Location: login.php');
            exit;
        }
    }
}

function requireAdmin($is_api = false) {
    if (!isAdmin()) {
        if ($is_api) {
            safeJSONResponse(['success' => false, 'message' => 'Acceso denegado. Permisos de administrador requeridos.'], 403);
        } else {
            header('Location: ../index.php');
            exit;
        }
    }
}
?>
