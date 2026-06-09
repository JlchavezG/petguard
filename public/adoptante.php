<?php
/**
 * ============================================================
 * PETGUARD - Punto de Entrada para el Dashboard del Adoptante
 * ============================================================
 */
define('PETGUARD_APP', true);

require_once '../config/app.php';
require_once '../config/database.php';

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar autenticación y rol
if (!isset($_SESSION['logged_in']) || !isset($_SESSION['user_role_id']) || $_SESSION['user_role_id'] != 4) {
    header('Location: ' . BASE_URL . '/login.php');
    exit;
}

require_once APP_PATH . '/Controllers/AdoptanteController.php';

$controller = new AdoptanteController();
$controller->index();