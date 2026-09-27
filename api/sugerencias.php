<?php
/**
 * Kosheru - API de Recepción de Sugerencias / Contacto
 */
require_once __DIR__ . '/../modelo/connection.php';
require_once __DIR__ . '/../includes/helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJsonResponse(['success' => false, 'message' => 'Método no permitido.'], 405);
}

$name = isset($_POST['name']) ? sanitize($_POST['name']) : '';
$email = isset($_POST['email']) ? sanitize($_POST['email']) : '';
$message = isset($_POST['message']) ? sanitize($_POST['message']) : '';

if (empty($name) || empty($email) || empty($message)) {
    sendJsonResponse(['success' => false, 'message' => 'Por favor completa todos los campos del formulario.'], 400);
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    sendJsonResponse(['success' => false, 'message' => 'Por favor ingresa un correo electrónico válido.'], 400);
}

$esc_name = mysqli_real_escape_string($conn, $name);
$esc_email = mysqli_real_escape_string($conn, $email);
$esc_msg = mysqli_real_escape_string($conn, $message);

$sql = "INSERT INTO suggestions (name, email, message, status) VALUES ('$esc_name', '$esc_email', '$esc_msg', 'pendiente')";

if (mysqli_query($conn, $sql)) {
    sendJsonResponse([
        'success' => true,
        'message' => '¡Muchas gracias por tu sugerencia! Tu mensaje ha sido enviado correctamente.'
    ]);
} else {
    sendJsonResponse(['success' => false, 'message' => 'No fue posible guardar tu sugerencia en este momento.'], 500);
}
?>
