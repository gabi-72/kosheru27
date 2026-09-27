<?php
/**
 * Kosheru - API de Gestión de Usuarios Admin
 */
require_once __DIR__ . '/../../../modelo/connection.php';
require_once __DIR__ . '/../../../includes/session.php';
require_once __DIR__ . '/../../../includes/auth.php';
require_once __DIR__ . '/../../../includes/helpers.php';

requireAdmin(true);

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $sql = "SELECT id, name, lastname, username, email, rol, state, created_at FROM users ORDER BY id DESC";
    $res = mysqli_query($conn, $sql);
    $usuarios = [];

    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            $row['password_masked'] = '••••••••';
            $usuarios[] = $row;
        }
    }

    sendJsonResponse(['success' => true, 'usuarios' => $usuarios]);
}

if ($method === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';

    if ($action === 'change_password') {
        $user_id = (int)($_POST['user_id'] ?? 0);
        $new_pass = trim($_POST['new_password'] ?? '');

        if ($user_id <= 0 || strlen($new_pass) < 6) {
            sendJsonResponse(['success' => false, 'message' => 'La contraseña debe tener al menos 6 caracteres.'], 400);
        }

        $hash = password_hash($new_pass, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET password = '$hash' WHERE id = $user_id";

        if (mysqli_query($conn, $sql)) {
            sendJsonResponse(['success' => true, 'message' => 'Contraseña actualizada correctamente.']);
        } else {
            sendJsonResponse(['success' => false, 'message' => 'Error al actualizar contraseña.'], 500);
        }
    }

    if ($action === 'toggle_state') {
        $user_id = (int)($_POST['user_id'] ?? 0);
        $new_state = ($_POST['state'] === 'activo') ? 'inactivo' : 'activo';

        $sql = "UPDATE users SET state = '$new_state' WHERE id = $user_id";
        if (mysqli_query($conn, $sql)) {
            sendJsonResponse(['success' => true, 'message' => "Estado de usuario cambiado a {$new_state}."]);
        } else {
            sendJsonResponse(['success' => false, 'message' => 'Error al cambiar estado.'], 500);
        }
    }

    if ($action === 'change_role') {
        $user_id = (int)($_POST['user_id'] ?? 0);
        $new_role = ($_POST['rol'] === 'admin') ? 'usuario' : 'admin';

        $sql = "UPDATE users SET rol = '$new_role' WHERE id = $user_id";
        if (mysqli_query($conn, $sql)) {
            sendJsonResponse(['success' => true, 'message' => "Rol de usuario actualizado a {$new_role}."]);
        } else {
            sendJsonResponse(['success' => false, 'message' => 'Error al actualizar rol.'], 500);
        }
    }
}
?>
