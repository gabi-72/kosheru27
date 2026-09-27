<?php
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/modelo/connection.php';

if (isLoggedIn()) {
    header("Location: index.php");
    exit();
}

$error_msg = "";
$success_msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $lastname = trim($_POST['lastname'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($name) || empty($lastname) || empty($username) || empty($email) || empty($password)) {
        $error_msg = "Por favor completa todos los campos obligatorios.";
    } elseif ($password !== $confirm_password) {
        $error_msg = "Las contraseñas no coinciden.";
    } elseif (strlen($password) < 6) {
        $error_msg = "La contraseña debe tener al menos 6 caracteres.";
    } else {
        $username_esc = mysqli_real_escape_string($conn, $username);
        $email_esc = mysqli_real_escape_string($conn, $email);

        $check_query = "SELECT id FROM users WHERE username = '$username_esc' OR email = '$email_esc' LIMIT 1";
        $check_result = mysqli_query($conn, $check_query);

        if ($check_result && mysqli_num_rows($check_result) > 0) {
            $error_msg = "El nombre de usuario o correo electrónico ya está registrado.";
        } else {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $name_esc = mysqli_real_escape_string($conn, $name);
            $lastname_esc = mysqli_real_escape_string($conn, $lastname);

            $insert_query = "INSERT INTO users (name, lastname, username, email, password, rol, state) VALUES ('$name_esc', '$lastname_esc', '$username_esc', '$email_esc', '$password_hash', 'usuario', 'activo')";

            if (mysqli_query($conn, $insert_query)) {
                $success_msg = "¡Registro exitoso! Ya puedes iniciar sesión con tu cuenta.";
            } else {
                $error_msg = "Error al registrar la cuenta. Por favor intentalo de nuevo.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Cuenta | Kosheru</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" href="img/logos/Isotipo transparente.png" type="image/png">
</head>
<body style="background-color: var(--color-bg-main); display: flex; align-items: center; justify-content: center; min-height: 100vh; padding: 2rem 1rem;">

    <div class="login-modal-card" style="max-width: 520px;">
        <a href="index.php" class="btn-close-modal" title="Volver al Inicio">&times;</a>

        <h2 class="modal-title">Crear Cuenta</h2>
        <p class="modal-subtitle">Únete a Kosheru para disfrutar de la mejor selección de superalimentos.</p>

        <?php if (!empty($error_msg)): ?>
            <div class="modal-alert error" style="display: block;"><?= htmlspecialchars($error_msg) ?></div>
        <?php endif; ?>

        <?php if (!empty($success_msg)): ?>
            <div class="modal-alert" style="display: block; background-color: rgba(39, 174, 96, 0.2); color: #2ecc71; border: 1px solid rgba(39, 174, 96, 0.4);"><?= htmlspecialchars($success_msg) ?></div>
        <?php endif; ?>

        <form action="registro.php" method="POST">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="modal-form-group">
                    <label for="name" class="modal-label">NOMBRE</label>
                    <input type="text" id="name" name="name" class="modal-input" placeholder="Tu nombre" required>
                </div>
                <div class="modal-form-group">
                    <label for="lastname" class="modal-label">APELLIDO</label>
                    <input type="text" id="lastname" name="lastname" class="modal-input" placeholder="Tu apellido" required>
                </div>
            </div>

            <div class="modal-form-group">
                <label for="username" class="modal-label">USUARIO</label>
                <input type="text" id="username" name="username" class="modal-input" placeholder="Nombre de usuario" required>
            </div>

            <div class="modal-form-group">
                <label for="email" class="modal-label">CORREO ELECTRÓNICO</label>
                <input type="email" id="email" name="email" class="modal-input" placeholder="tu@correo.com" required>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="modal-form-group">
                    <label for="password" class="modal-label">CONTRASEÑA</label>
                    <input type="password" id="password" name="password" class="modal-input" placeholder="........" required>
                </div>
                <div class="modal-form-group">
                    <label for="confirm_password" class="modal-label">CONFIRMAR</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="modal-input" placeholder="........" required>
                </div>
            </div>

            <button type="submit" class="btn-modal-submit">Crear Mi Cuenta</button>
        </form>

        <p class="modal-footer-link">
            ¿Ya tienes cuenta? <a href="login.php">Inicia sesión aquí</a>
        </p>
    </div>

</body>
</html>
