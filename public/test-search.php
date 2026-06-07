<?php
/**
 * ============================================================
 * PETGUARD - Test de Diagnóstico de Búsqueda AJAX
 * ============================================================
 */

define('PETGUARD_APP', true);

require_once '../config/app.php';
require_once '../config/database.php';

// Verificar sesión
if (!isset($_SESSION['logged_in']) || $_SESSION['user_role_id'] != 1) {
    die('No autorizado. Inicia sesión como admin.');
}

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Test Búsqueda AJAX - PetGuard</title>
    <style>
        body { font-family: 'Inter', sans-serif; background: #f5f5f7; padding: 20px; }
        .container { max-width: 900px; margin: 0 auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); }
        h1 { color: #0a0a0a; margin-bottom: 20px; }
        h2 { color: #ff9a76; margin-top: 30px; margin-bottom: 15px; font-size: 18px; }
        .success { background: #dcfce7; color: #166534; padding: 12px; border-radius: 8px; margin: 10px 0; }
        .error { background: #fee2e2; color: #991b1b; padding: 12px; border-radius: 8px; margin: 10px 0; }
        .warning { background: #fef3c7; color: #92400e; padding: 12px; border-radius: 8px; margin: 10px 0; }
        pre { background: #f3f4f6; padding: 15px; border-radius: 8px; overflow-x: auto; font-size: 13px; }
        code { background: #f3f4f6; padding: 2px 6px; border-radius: 4px; font-size: 13px; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #e5e7eb; }
        th { background: #f3f4f6; font-weight: 600; }
        .btn { background: #ff9a76; color: white; padding: 10px 20px; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; margin: 5px; }
        .btn:hover { background: #ff8a66; }
        #test-results { margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Test de Diagnóstico - Búsqueda AJAX</h1>
        
        <div id="test-results">
            <h2>Test 1: Verificar endpoint AJAX</h2>
            <button class="btn" onclick="testEndpoint()">Probar Endpoint</button>
            <div id="endpoint-result"></div>
            
            <h2>Test 2: Verificar consulta SQL directa</h2>
            <button class="btn" onclick="testSQL()">Probar SQL</button>
            <div id="sql-result"></div>
            
            <h2>Test 3: Verificar JavaScript</h2>
            <button class="btn" onclick="testJS()">Probar JavaScript</button>
            <div id="js-result"></div>
        </div>
        
        <h2>Resultados de pruebas manuales</h2>
        <?php
        // Test manual de la consulta SQL
        try {
            require_once APP_PATH . '/Models/UserAdminModel.php';
            $userModel = new UserAdminModel();
            
            echo "<h3>Prueba 1: Obtener todos los usuarios</h3>";
            $allUsers = $userModel->getAll('', 0);
            echo "<p>Total de usuarios activos: <strong>" . count($allUsers) . "</strong></p>";
            
            if (count($allUsers) > 0) {
                echo "<table>";
                echo "<tr><th>ID</th><th>Nombre</th><th>Email</th><th>Rol</th></tr>";
                foreach ($allUsers as $u) {
                    echo "<tr>";
                    echo "<td>{$u['id']}</td>";
                    echo "<td>{$u['nombre']} {$u['apellido_paterno']}</td>";
                    echo "<td>{$u['email']}</td>";
                    echo "<td>{$u['rol_nombre']}</td>";
                    echo "</tr>";
                }
                echo "</table>";
            }
            
            echo "<h3>Prueba 2: Buscar 'admin'</h3>";
            $searchResult = $userModel->getAll('admin', 0);
            echo "<p>Resultados encontrados: <strong>" . count($searchResult) . "</strong></p>";
            
            if (count($searchResult) > 0) {
                echo "<table>";
                echo "<tr><th>ID</th><th>Nombre</th><th>Email</th></tr>";
                foreach ($searchResult as $u) {
                    echo "<tr>";
                    echo "<td>{$u['id']}</td>";
                    echo "<td>{$u['nombre']} {$u['apellido_paterno']}</td>";
                    echo "<td>{$u['email']}</td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "<div class='warning'>No se encontraron usuarios con 'admin'</div>";
            }
            
            echo "<h3>Prueba 3: Buscar 'ramo'</h3>";
            $searchResult2 = $userModel->getAll('ramo', 0);
            echo "<p>Resultados encontrados: <strong>" . count($searchResult2) . "</strong></p>";
            
            if (count($searchResult2) > 0) {
                echo "<table>";
                echo "<tr><th>ID</th><th>Nombre</th><th>Email</th></tr>";
                foreach ($searchResult2 as $u) {
                    echo "<tr>";
                    echo "<td>{$u['id']}</td>";
                    echo "<td>{$u['nombre']} {$u['apellido_paterno']}</td>";
                    echo "<td>{$u['email']}</td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "<div class='warning'>No se encontraron usuarios con 'ramo'</div>";
            }
            
        } catch (Exception $e) {
            echo "<div class='error'>Error: " . $e->getMessage() . "</div>";
        }
        ?>
    </div>
    
    <script>
        function testEndpoint() {
            const resultDiv = document.getElementById('endpoint-result');
            resultDiv.innerHTML = '<div class="warning">Probando endpoint...</div>';
            
            fetch('/petguard/public/api/search-users.php?search=admin&rol=0', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('HTTP ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                resultDiv.innerHTML = '<div class="success">✅ Endpoint funciona correctamente</div>';
                resultDiv.innerHTML += '<pre>' + JSON.stringify(data, null, 2) + '</pre>';
            })
            .catch(error => {
                resultDiv.innerHTML = '<div class="error">❌ Error: ' + error.message + '</div>';
            });
        }
        
        function testSQL() {
            const resultDiv = document.getElementById('sql-result');
            resultDiv.innerHTML = '<div class="warning">Verificando resultados de SQL arriba...</div>';
        }
        
        function testJS() {
            const resultDiv = document.getElementById('js-result');
            
            // Verificar si existe el archivo JS
            fetch('/petguard/public/js/admin-users.js')
            .then(response => {
                if (response.ok) {
                    resultDiv.innerHTML = '<div class="success">✅ Archivo admin-users.js existe y es accesible</div>';
                } else {
                    resultDiv.innerHTML = '<div class="error"> Archivo admin-users.js no encontrado</div>';
                }
            })
            .catch(error => {
                resultDiv.innerHTML = '<div class="error">❌ Error al verificar archivo JS: ' + error.message + '</div>';
            });
            
            // Verificar si existe el input de búsqueda
            const searchInput = document.querySelector('input[name="search"]');
            if (searchInput) {
                resultDiv.innerHTML += '<div class="success">✅ Input de búsqueda encontrado en el DOM</div>';
            } else {
                resultDiv.innerHTML += '<div class="error">❌ Input de búsqueda NO encontrado en el DOM</div>';
            }
            
            // Verificar si existe el select de rol
            const rolSelect = document.querySelector('select[name="rol"]');
            if (rolSelect) {
                resultDiv.innerHTML += '<div class="success">✅ Select de rol encontrado en el DOM</div>';
            } else {
                resultDiv.innerHTML += '<div class="error">❌ Select de rol NO encontrado en el DOM</div>';
            }
        }
    </script>
</body>
</html>