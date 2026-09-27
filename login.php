<?php
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/modelo/connection.php';

// Si ya está logueado, redirigir
if (isLoggedIn()) {
    header("Location: index.php");
    exit();
}

$error_msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    $is_ajax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') || isset($_POST['ajax']);

    if (empty($username) || empty($password)) {
        $msg = "Por favor ingresa tu usuario y contraseña.";
        if ($is_ajax) {
            safeJSONResponse(['status' => 'error', 'message' => $msg], 400);
        }
        $error_msg = $msg;
    } else {
        $username_esc = mysqli_real_escape_string($conn, $username);
        $query = "SELECT id, name, lastname, username, email, password, rol, state FROM users WHERE username = '$username_esc' LIMIT 1";
        $result = mysqli_query($conn, $query);

        if ($result && mysqli_num_rows($result) === 1) {
            $user = mysqli_fetch_assoc($result);

            if ($user['state'] !== 'activo') {
                $msg = "Tu cuenta se encuentra inactiva. Contacta al soporte.";
                if ($is_ajax) {
                    safeJSONResponse(['status' => 'error', 'message' => $msg], 403);
                }
                $error_msg = $msg;
            } elseif (password_verify($password, $user['password'])) {
                // Login exitoso
                setAuthenticatedUser([
                    'id' => $user['id'],
                    'name' => $user['name'],
                    'lastname' => $user['lastname'],
                    'username' => $user['username'],
                    'email' => $user['email'],
                    'rol' => $user['rol']
                ]);

                if ($is_ajax) {
                    safeJSONResponse([
                        'status' => 'success',
                        'message' => '¡Bienvenido a Kosheru!',
                        'user' => [
                            'name' => $user['name'],
                            'rol' => $user['rol']
                        ]
                    ]);
                }

                if ($user['rol'] === 'admin') {
                    header("Location: CRUD-/vista/index.php");
                } else {
                    header("Location: index.php");
                }
                exit();
            } else {
                $msg = "Contraseña incorrecta. Por favor intenta de nuevo.";
                if ($is_ajax) {
                    safeJSONResponse(['status' => 'error', 'message' => $msg], 401);
                }
                $error_msg = $msg;
            }
        } else {
            $msg = "El usuario no existe.";
            if ($is_ajax) {
                safeJSONResponse(['status' => 'error', 'message' => $msg], 404);
            }
            $error_msg = $msg;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión | Kosheru</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" href="img/logos/Isotipo transparente.png" type="image/png">
</head>
<body style="background-color: var(--color-bg-main); display: flex; align-items: center; justify-content: center; min-height: 100vh;">

    <!-- Modal Card idéntica a Captura 2 -->
    <div class="login-modal-card" style="margin: 2rem auto;">
        <a href="index.php" class="btn-close-modal" title="Volver al Inicio">&times;</a>

        <h2 class="modal-title">Iniciar Sesión</h2>
        <p class="modal-subtitle">Accede a tu cuenta de Kosheru para comprar tus superalimentos.</p>

        <?php if (!empty($error_msg)): ?>
            <div class="modal-alert error" style="display: block;"><?= htmlspecialchars($error_msg) ?></div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <div class="modal-form-group">
                <label for="username" class="modal-label">NOMBRE DE USUARIO</label>
                <input type="text" id="username" name="username" class="modal-input" placeholder="Tu usuario" required autocomplete="username">
            </div>

            <div class="modal-form-group">
                <label for="password" class="modal-label">CONTRASEÑA</label>
                <input type="password" id="password" name="password" class="modal-input" placeholder="........" required autocomplete="current-password">
            </div>

            <button type="submit" class="btn-modal-submit">Ingresar</button>
        </form>

        <p class="modal-footer-link">
            ¿No tienes una cuenta? <a href="registro.php">Regístrate aquí</a>
        </p>
    </div>

</body>
</html>
