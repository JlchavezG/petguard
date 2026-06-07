<?php
define('PETGUARD_APP', true);
require_once '../config/app.php';

$cssFile = PUBLIC_PATH . '/css/auth.css';
$cssUrl = BASE_URL . '/css/auth.css?v=' . time();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diagnóstico CSS - PetGuard</title>
    <style>
        body { font-family: monospace; background: #1a1a1a; color: #00ff00; padding: 20px; }
        .section { background: #2d2d2d; padding: 20px; margin: 20px 0; border-radius: 8px; }
        h1 { color: #ff9a76; }
        h2 { color: #ffffff; margin-top: 0; }
        .ok { color: #00ff00; }
        .error { color: #ff3b30; }
        .warning { color: #ffcc00; }
        pre { background: #000; padding: 15px; border-radius: 4px; overflow-x: auto; }
        .test-box { background: white; padding: 20px; margin: 10px 0; border-radius: 8px; }
        .test-input { padding: 10px; border: 2px solid #d1d1d6; border-radius: 8px; margin: 5px 0; width: 100%; }
    </style>
</head>
<body>
    <h1>🔍 Diagnóstico CSS - PetGuard</h1>
    
    <div class="section">
        <h2>1. Verificación de Archivo CSS</h2>
        <?php if (file_exists($cssFile)): ?>
            <p class="ok">✅ Archivo existe: <?php echo $cssFile; ?></p>
            <p class="ok">✅ Tamaño: <?php echo filesize($cssFile); ?> bytes</p>
            <p class="ok">✅ Última modificación: <?php echo date('Y-m-d H:i:s', filemtime($cssFile)); ?></p>
        <?php else: ?>
            <p class="error">❌ Archivo NO existe</p>
        <?php endif; ?>
    </div>

    <div class="section">
        <h2>2. URL del CSS</h2>
        <p>URL generada: <code><?php echo $cssUrl; ?></code></p>
        <p><a href="<?php echo $cssUrl; ?>" target="_blank" class="ok">Ver CSS en navegador →</a></p>
    </div>

    <div class="section">
        <h2>3. Prueba Visual de Estilos</h2>
        <p>Si ves estos inputs con fondo blanco y texto negro, el CSS funciona:</p>
        
        <div class="test-box">
            <label style="color: #1d1d1f; font-weight: 700; display: block; margin-bottom: 5px;">
                Correo Electrónico
            </label>
            <input type="text" class="test-input" placeholder="ejemplo@correo.com" style="color: #1d1d1f; background: white;">
            
            <label style="color: #1d1d1f; font-weight: 700; display: block; margin: 15px 0 5px;">
                Contraseña
            </label>
            <input type="password" class="test-input" placeholder="Mínimo 8 caracteres" style="color: #1d1d1f; background: white;">
        </div>
    </div>

    <div class="section">
        <h2>4. Contenido del CSS (primeras 50 líneas)</h2>
        <pre><?php
        $lines = file($cssFile);
        echo htmlspecialchars(implode('', array_slice($lines, 0, 50)));
        ?></pre>
    </div>

    <div class="section">
        <h2>5. Instrucciones para Solucionar</h2>
        <ol>
            <li>Abre la consola del navegador (F12)</li>
            <li>Ve a la pestaña "Network" o "Red"</li>
            <li>Recarga la página de registro</li>
            <li>Busca "auth.css" en la lista</li>
            <li>Haz clic derecho → "Open in new tab"</li>
            <li>Verifica que el CSS se cargue correctamente</li>
        </ol>
    </div>

    <div class="section">
        <h2>6. Solución Rápida (Forzar Recarga)</h2>
        <p>Agrega esto al inicio de tu archivo <code>register.php</code>:</p>
        <pre>&lt;?php header('Cache-Control: no-cache, no-store, must-revalidate'); ?&gt;</pre>
    </div>
</body>
</html> 