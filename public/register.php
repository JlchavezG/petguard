<?php
/**
 * ============================================================
 * PETGUARD - Punto de Entrada para Registro de Usuarios
 * ============================================================
 */
define('PETGUARD_APP', true);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../config/app.php';
require_once '../config/database.php';
require_once APP_PATH . '/Controllers/AuthController.php';

$controller = new AuthController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller->register();
} else {
    $controller->showRegister();
}