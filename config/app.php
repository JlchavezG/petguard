<?php
/**
 * ============================================================
 * PETGUARD - Configuración Global de la Aplicación
 * ============================================================
 * Define constantes, rutas base y configuración de seguridad.
 * Proyecto de Ingeniería de Software · 9no Semestre 2026
 * ============================================================
 */

// 1. SEGURIDAD: Evitar acceso directo
if (!defined('PETGUARD_APP')) {
    http_response_code(403);
    exit('Acceso no permitido.');
}

// 2. ENTORNO DE TRABAJO
// 'development' para mostrar errores, 'production' para ocultarlos
define('APP_ENV', 'development'); 

// 3. RUTAS DEL SISTEMA (Paths absolutos del servidor)
define('BASE_PATH', dirname(__DIR__));          // C:/xampp/htdocs/petguard
define('APP_PATH', BASE_PATH . '/app');         // C:/xampp/htdocs/petguard/app
define('CONFIG_PATH', BASE_PATH . '/config');   // C:/xampp/htdocs/petguard/config
define('PUBLIC_PATH', BASE_PATH . '/public');   // C:/xampp/htdocs/petguard/public

// 4. URL BASE (Para enlaces y assets en el navegador)
// Ajusta esto si tu proyecto está en una subcarpeta diferente
define('BASE_URL', 'http://localhost/petguard/public');

// 5. ZONA HORARIA (México)
date_default_timezone_set('America/Mexico_City');

// 6. CONFIGURACIÓN DE SEGURIDAD DE SESIONES (Crítico para Login)
// Se ejecuta solo si no hay una sesión activa
if (session_status() === PHP_SESSION_NONE) {
    
    // Configuración de cookies de sesión seguras
    ini_set('session.cookie_httponly', 1);      // Evita que JavaScript acceda a la cookie (Anti-XSS)
    ini_set('session.cookie_samesite', 'Lax');  // Protección básica contra CSRF
    ini_set('session.use_strict_mode', 1);      // Rechaza IDs de sesión no inicializados por el servidor
    
    // Si en el futuro usas HTTPS, descomenta la siguiente línea:
    // ini_set('session.cookie_secure', 1);      // La cookie solo se envía por HTTPS
    
    // Iniciar sesión
    session_start();
}

// 7. CONSTANTES DE LA APLICACIÓN
define('APP_NAME', 'PetGuard');
define('APP_VERSION', '1.1.0');

// 8. RUTAS DE REDIRECCIÓN POR ROL (Se usará en el AuthController)
define('ROLE_REDIRECTS', [
    'administrador' => BASE_URL . '/admin/dashboard',
    'voluntario'    => BASE_URL . '/voluntario/dashboard',
    'veterinario'   => BASE_URL . '/veterinario/dashboard',
    'adoptante'     => BASE_URL . '/adoptante/dashboard'
]);