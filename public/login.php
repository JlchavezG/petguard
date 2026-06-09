<?php
/**
 * ============================================================
 * PETGUARD - Punto de Entrada de Autenticación (Login)
 * ============================================================
 */
define('PETGUARD_APP', true);

// 1. Iniciar sesión de forma segura
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../config/app.php';
require_once '../config/database.php';

// 2. Manejar cierre de sesión de forma segura y con mensaje flash
if (isset($_GET['logout']) && $_GET['logout'] == '1') {
    session_unset();
    session_destroy();
    
    // Iniciar una nueva sesión temporal solo para el mensaje de éxito
    session_start();
    $_SESSION['flash']['success'] = 'Has cerrado sesión correctamente.';
    
    // Redirigir a la URL limpia
    header('Location: ' . BASE_URL . '/login.php');
    exit;
}

// 3. Cargar el controlador
require_once APP_PATH . '/Controllers/AuthController.php';
$controller = new AuthController();

// 4. Enrutamiento robusto (POST = intentar login, GET = mostrar vista)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller->login();
} else {
    $controller->showLogin();
}