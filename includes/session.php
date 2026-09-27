<?php
/**
 * Kosheru - Manejo de Sesiones Seguras
 */

if (session_status() === PHP_SESSION_NONE) {
    // Configuración de cookies de sesión seguras
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    
    session_start();
}
?>
