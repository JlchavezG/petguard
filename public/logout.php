<?php
/**
 * ============================================================
 * PETGUARD - Destrucción Total de Sesión (Herramienta de Pruebas)
 * ============================================================
 */

// 1. Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Limpiar absolutamente todas las variables de sesión
$_SESSION = array();

// 3. Destruir la cookie de sesión en el navegador (si existe)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(), 
        '', 
        time() - 42000,
        $params["path"], 
        $params["domain"],
        $params["secure"], 
        $params["httponly"]
    );
}

// 4. Destruir la sesión en el servidor
session_destroy();

// 5. Iniciar una sesión nueva temporal solo para mostrar el mensaje de éxito
session_start();
$_SESSION['flash']['success'] = 'Sesión cerrada y datos limpiados completamente. Listo para nuevas pruebas.';

// 6. Redirigir al login
header('Location: /petguard/public/login.php');
exit;