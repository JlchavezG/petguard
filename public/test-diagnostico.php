<?php
/**
 * ============================================================
 * PETGUARD - Diagnóstico Profesional de Búsquedas y BD
 * ============================================================
 */
define('PETGUARD_APP', true);
require_once '../config/app.php';
require_once '../config/database.php';

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Diagnóstico BD PetGuard</title>
    <style>
        body { font-family: system-ui, sans-serif; background: #f4f6f8; padding: 40px; line-height: 1.6; }
        .container { max-width: 900px; margin: 0 auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
        h1 { color: #111827; border-bottom: 2px solid #ff9a76; padding-bottom: 10px; }
        h2 { color: #ff9a76; margin-top: 30px; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th, td { border: 1px solid #e5e7eb; padding: 10px; text-align: left; }
        th { background: #f9fafb; }
        .success { color: #059669; font-weight: bold; }
        .error { color: #dc2626; font-weight: bold; }
        .sql-block { background: #1f2937; color: #10b981; padding: 15px; border-radius: 8px; overflow-x: auto; font-family: monospace; font-size: 13px; }
        .box { padding: 15px; background: #eff6ff; border-left: 4px solid #3b82f6; margin: 15px 0; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Diagnóstico de Motor de Búsqueda</h1>
        
        <?php
        $db = getDB();

        // === TEST 1: Estructura de usuarios ===
        echo "<h2>1. Estructura de Tabla: usuarios</h2>";
        try {
            $cols = $db->query("SHOW COLUMNS FROM usuarios")->fetchAll();
            echo "<table><tr><th>Columna</th><th>Tipo</th></tr>";
            foreach($cols as $c) echo "<tr><td>{$c['Field']}</td><td>{$c['Type']}</td></tr>";
            echo "</table>";
            
            $hasActivo = false;
            foreach($cols as $c) if($c['Field'] === 'activo') $hasActivo = true;
            echo $hasActivo ? "<div class='success'>✅ Columna 'activo' existe.</div>" : "<div class='error'>❌ FALTA la columna 'activo' (Esto rompería la consulta si se usa WHERE u.activo = 1)</div>";
        } catch (Exception $e) { echo "<div class='error'>Error: {$e->getMessage()}</div>"; }

        // === TEST 2: Consulta exacta de búsqueda de Usuarios ===
        echo "<h2>2. Simulación de Búsqueda de Usuarios (LIKE 'admin')</h2>";
        $sql = "SELECT u.id, u.nombre, u.email FROM usuarios u INNER JOIN roles r ON u.rol_id = r.id WHERE 1=1 AND (u.nombre LIKE '%admin%' OR u.email LIKE '%admin%')";
        echo "<div class='sql-block'>SQL: {$sql}</div>";
        
        try {
            // Intentamos la consulta con y sin el JOIN para ver dónde falla
            $stmt1 = $db->query("SELECT COUNT(*) as c FROM usuarios WHERE nombre LIKE '%admin%' OR email LIKE '%admin%'");
            $c1 = $stmt1->fetch()['c'];
            echo "<p>Sin JOIN ni filtro 'activo': <strong>{$c1} resultados</strong></p>";
            
            $stmt2 = $db->query("SELECT COUNT(*) as c FROM usuarios u INNER JOIN roles r ON u.rol_id = r.id WHERE 1=1 AND (u.nombre LIKE '%admin%' OR u.email LIKE '%admin%')");
            $c2 = $stmt2->fetch()['c'];
            echo "<p>Con JOIN de roles: <strong>{$c2} resultados</strong></p>";
            
            if ($c2 > 0) {
                echo "<div class='success'>✅ La búsqueda funciona correctamente en SQL directo.</div>";
            } else {
                echo "<div class='error'>⚠️ La búsqueda devuelve 0 en SQL directo. Revisa si los datos contienen espacios ocultos o si los IDs de rol no coinciden.</div>";
            }
        } catch (Exception $e) { echo "<div class='error'>❌ Error SQL: {$e->getMessage()}</div>"; }

        // === TEST 3: Diagnóstico de archivo search-mascotas.php ===
        echo "<h2>3. Sintaxis de: public/api/search-mascotas.php</h2>";
        $path = __DIR__ . '/api/search-mascotas.php';
        if (file_exists($path)) {
            echo "<p>✅ Archivo existe.</p>";
            $output = [];
            exec("php -l " . escapeshellarg($path) . " 2>&1", $output, $ret);
            echo "<div class='sql-block'>" . implode("\n", $output) . "</div>";
            if ($ret === 0) echo "<div class='success'>✅ Sintaxis PHP válida.</div>";
            else echo "<div class='error'>❌ Errores de sintaxis detectados.</div>";
        } else {
            echo "<div class='error'>❌ Archivo NO encontrado en {$path}</div>";
        }

        // === TEST 4: Diagnóstico de archivo search-users.php ===
        echo "<h2>4. Sintaxis de: public/api/search-users.php</h2>";
        $path = __DIR__ . '/api/search-users.php';
        if (file_exists($path)) {
            echo "<p>✅ Archivo existe.</p>";
            $output = [];
            exec("php -l " . escapeshellarg($path) . " 2>&1", $output, $ret);
            echo "<div class='sql-block'>" . implode("\n", $output) . "</div>";
            if ($ret === 0) echo "<div class='success'>✅ Sintaxis PHP válida.</div>";
            else echo "<div class='error'>❌ Errores de sintaxis detectados.</div>";
        } else {
            echo "<div class='error'>❌ Archivo NO encontrado.</div>";
        }

        ?>
        
        <div class="box">
            <strong>📋 Instrucción:</strong> Copia el resultado de esta página y pégalo en tu respuesta. Con esto sabremos exactamente si el error es de SQL, de sintaxis PHP o de estructura de tablas.
        </div>
    </div>
</body>
</html>