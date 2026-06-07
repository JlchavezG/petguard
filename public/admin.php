<?php
/**
 * ============================================================
 * PETGUARD - Punto de Entrada del Dashboard Admin
 * ============================================================
 */
define('PETGUARD_APP', true);

require_once '../config/app.php';
require_once '../config/database.php';

// Verificación extra de seguridad antes de cargar el controlador
if (!isset($_SESSION['logged_in']) || !isset($_SESSION['user_role_id']) || $_SESSION['user_role_id'] != 1) {
    header('Location: ' . BASE_URL . '/login.php');
    exit;
}

require_once APP_PATH . '/Controllers/DashboardController.php';

$dashboard = new DashboardController();
$dashboard->index();