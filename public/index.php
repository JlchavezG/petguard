<?php
/**
 * ============================================================
 * PETGUARD - Punto de Entrada Principal (Landing Page)
 * ============================================================
 */
define('PETGUARD_APP', true);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../config/app.php';
require_once '../config/database.php';

// Cargar el controlador de la Landing Page
require_once APP_PATH . '/Controllers/LandingController.php';

$controller = new LandingController();
$controller->index();