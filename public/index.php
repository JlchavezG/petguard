<?php
// 1. ACTIVAR DEPURACIÓN (Fundamental para detectar errores ocultos)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 2. DEFINIR RUTAS DE FORMA SEGURA E INFALIBLE
// __DIR__ apunta a la carpeta 'public'. dirname(__DIR__) sube un nivel a la carpeta raíz del proyecto.
define('BASE_PATH', dirname(__DIR__));

// 3. CONSTRUIR LAS RUTAS DE LOS ARCHIVOS
$headerPath = BASE_PATH . '/app/Views/layouts/header.php';
$landingPath = BASE_PATH . '/app/Views/landing/index.php';
$footerPath = BASE_PATH . '/app/Views/layouts/footer.php';

// 4. VERIFICACIÓN DE EXISTENCIA (Evita la pantalla en blanco si falta algo)
if (!file_exists($headerPath)) {
    die("<h3>⚠️ ERROR DE RUTA:</h3><p>No se encuentra el archivo en:<br><code>" . $headerPath . "</code></p><p>Por favor, verifica que la carpeta <strong>app/Views/layouts/</strong> exista dentro de la carpeta raíz de tu proyecto.</p>");
}

// 5. INCLUIR LAS VISTAS PARA ENSAMBLAR LA PÁGINA
require_once $headerPath;
require_once $landingPath;
require_once $footerPath;
?>