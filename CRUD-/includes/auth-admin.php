<?php
/**
 * Kosheru - Control de Permisos Administrativos
 * Solamente usuarios con rol === 'admin' pueden acceder
 */
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/auth.php';

if (!isAdmin()) {
    header('Location: ../../index.php');
    exit;
}
?>
