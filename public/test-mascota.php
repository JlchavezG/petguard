<?php
/**
 * ============================================================
 * PETGUARD - Test de Diagnóstico para Registro de Mascotas
 * ============================================================
 */

define('PETGUARD_APP', true);

require_once '../config/app.php';
require_once '../config/database.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['user_role_id'] != 1) {
    die('No autorizado. Inicia sesión como admin.');
}

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Test Registro Mascota - PetGuard</title>
    <style>
        body { font-family: 'Inter', sans-serif; background: #f5f5f7; padding: 20px; }
        .container { max-width: 900px; margin: 0 auto; background: white; padding: 30px; border-radius: 12px; }
        h1 { color: #0a0a0a; }
        h2 { color: #ff9a76; margin-top: 25px; }
        .success { background: #dcfce7; color: #166534; padding: 12px; border-radius: 8px; margin: 10px 0; }
        .error { background: #fee2e2; color: #991b1b; padding: 12px; border-radius: 8px; margin: 10px 0; }
        .warning { background: #fef3c7; color: #92400e; padding: 12px; border-radius: 8px; margin: 10px 0; }
        pre { background: #f3f4f6; padding: 15px; border-radius: 8px; overflow-x: auto; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #e5e7eb; }
        th { background: #f3f4f6; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Test de Diagnóstico - Registro de Mascotas</h1>
        
        <?php
        $db = getDB();
        
        // TEST 1: Verificar carpeta de uploads
        echo "<h2>Test 1: Carpeta de Uploads</h2>";
        $uploadDir = BASE_PATH . '/public/uploads/mascotas/';
        if (is_dir($uploadDir)) {
            echo "<div class='success'>✅ Carpeta existe: {$uploadDir}</div>";
            echo "<p>Permisos: " . substr(sprintf('%o', fileperms($uploadDir)), -4) . "</p>";
            if (is_writable($uploadDir)) {
                echo "<div class='success'>✅ Carpeta tiene permisos de escritura</div>";
            } else {
                echo "<div class='error'>❌ Carpeta NO tiene permisos de escritura</div>";
            }
        } else {
            echo "<div class='error'>❌ Carpeta NO existe: {$uploadDir}</div>";
            echo "<p>Intentando crear...</p>";
            if (@mkdir($uploadDir, 0755, true)) {
                echo "<div class='success'>✅ Carpeta creada exitosamente</div>";
            } else {
                echo "<div class='error'>❌ No se pudo crear la carpeta. Crea manualmente:</div>";
                echo "<pre>mkdir -p {$uploadDir}\nchmod 755 {$uploadDir}</pre>";
            }
        }
        
        // TEST 2: Verificar tablas relacionadas
        echo "<h2>Test 2: Tablas Relacionadas</h2>";
        try {
            $especies = $db->query("SELECT * FROM especies")->fetchAll();
            echo "<div class='success'>✅ Tabla especies: " . count($especies) . " registros</div>";
            echo "<table><tr><th>ID</th><th>Nombre</th><th>Icono</th></tr>";
            foreach ($especies as $e) {
                echo "<tr><td>{$e['id']}</td><td>{$e['nombre']}</td><td>{$e['icono']}</td></tr>";
            }
            echo "</table>";
        } catch (Exception $ex) {
            echo "<div class='error'>❌ Error en tabla especies: " . $ex->getMessage() . "</div>";
        }
        
        try {
            $razas = $db->query("SELECT * FROM razas")->fetchAll();
            echo "<div class='success'>✅ Tabla razas: " . count($razas) . " registros</div>";
            echo "<table><tr><th>ID</th><th>Nombre</th><th>Especie ID</th></tr>";
            foreach ($razas as $r) {
                echo "<tr><td>{$r['id']}</td><td>{$r['nombre']}</td><td>{$r['especie_id']}</td></tr>";
            }
            echo "</table>";
        } catch (Exception $ex) {
            echo "<div class='error'>❌ Error en tabla razas: " . $ex->getMessage() . "</div>";
        }
        
        // TEST 3: Verificar estructura de tabla mascotas
        echo "<h2>Test 3: Estructura de tabla mascotas</h2>";
        try {
            $cols = $db->query("DESCRIBE mascotas")->fetchAll();
            echo "<div class='success'>✅ Tabla mascotas existe con " . count($cols) . " columnas</div>";
            echo "<table><tr><th>Columna</th><th>Tipo</th><th>Nulo</th><th>Default</th></tr>";
            foreach ($cols as $c) {
                echo "<tr><td>{$c['Field']}</td><td>{$c['Type']}</td><td>{$c['Null']}</td><td>{$c['Default']}</td></tr>";
            }
            echo "</table>";
        } catch (Exception $ex) {
            echo "<div class='error'>❌ Error: " . $ex->getMessage() . "</div>";
        }
        
        // TEST 4: Intentar insertar mascota de prueba
        echo "<h2>Test 4: Insertar Mascota de Prueba</h2>";
        try {
            require_once APP_PATH . '/Models/MascotaModel.php';
            $model = new MascotaModel();
            
            $testData = [
                'especie_id' => 1,
                'raza_id' => 1,
                'nombre' => 'Test_' . time(),
                'nombre_interno' => 'TEST-001',
                'genero' => 'Macho',
                'edad_aprox_meses' => 12,
                'color' => 'Café',
                'peso_kg' => 10.5,
                'descripcion' => 'Mascota de prueba',
                'personalidad' => 'Amigable',
                'estatus' => 'disponible',
                'urgente' => false,
                'esterilizado' => false,
                'vacunado' => true,
                'microchip' => false,
                'enfermedades' => null,
                'discapacidad' => null,
                'ubicacion' => 'Test',
                'lat' => null,
                'lng' => null,
                'foto_principal' => null,
                'registrado_por' => $_SESSION['user_id'] ?? 1
            ];
            
            $result = $model->create($testData);
            if ($result) {
                echo "<div class='success'>✅ Mascota de prueba creada exitosamente</div>";
                
                // Verificar que se insertó
                $lastId = $db->lastInsertId();
                echo "<p>ID de la mascota creada: <strong>{$lastId}</strong></p>";
                
                // Limpiar: desactivar la mascota de prueba
                $db->prepare("UPDATE mascotas SET activo = 0 WHERE id = :id")->execute(['id' => $lastId]);
                echo "<div class='warning'>⚠️ Mascota de prueba desactivada (limpieza automática)</div>";
            } else {
                echo "<div class='error'>❌ El modelo retornó false</div>";
            }
        } catch (Exception $ex) {
            echo "<div class='error'>❌ Error al insertar: " . $ex->getMessage() . "</div>";
            echo "<pre>" . $ex->getTraceAsString() . "</pre>";
        }
        
        // TEST 5: Verificar errores de PHP recientes
        echo "<h2>Test 5: Logs de Errores Recientes</h2>";
        $logFile = '/Applications/XAMPP/xamppfiles/logs/error_log';
        if (file_exists($logFile)) {
            $lines = file($logFile);
            $recentLines = array_slice($lines, -20);
            echo "<pre>" . htmlspecialchars(implode('', $recentLines)) . "</pre>";
        } else {
            echo "<div class='warning'>⚠️ No se encontró el archivo de logs en: {$logFile}</div>";
        }
        ?>
    </div>
</body>
</html>