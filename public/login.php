<?php
/**
 * ============================================================
 * PETGUARD - Punto de Entrada para Login
 * ============================================================
 */

define('PETGUARD_APP', true);

require_once '../config/app.php';
require_once '../config/database.php';
require_once '../app/Controllers/AuthController.php';

$authController = new AuthController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $authController->processLogin();
} else {
    require_once '../app/Views/auth/login.php';
}