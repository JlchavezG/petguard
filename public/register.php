<?php
/**
 * ============================================================
 * PETGUARD - Punto de Entrada para Registro
 * ============================================================
 * Maneja GET (mostrar formulario) y POST (procesar registro)
 * ============================================================
 */

define('PETGUARD_APP', true);

require_once '../config/app.php';
require_once '../config/database.php';
require_once '../app/Controllers/AuthController.php';

$authController = new AuthController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Procesar registro
    $authController->processRegister();
} else {
    // Mostrar formulario
    require_once '../app/Views/auth/register.php';
}