<?php
/**
 * ============================================================
 * PETGUARD - Script de Reinicio de Usuarios
 * ============================================================
 * ⚠️  ELIMINAR ESTE ARCHIVO DESPUÉS DE EJECUTARLO
 * ============================================================
 */

define('PETGUARD_APP', true);
require_once '../config/app.php';
require_once '../config/database.php';

echo "<!DOCTYPE html><html><head><title>Reset Usuarios PetGuard</title>";
echo "<style>
    body { font-family: 'Inter', sans-serif; background: #fafafa; padding: 40px; }
    .container { max-width: 900px; margin: 0 auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); }
    h1 { color: #0a0a0a; margin-bottom: 20px; }
    h2 { color: #ff9a76; margin-top: 30px; margin-bottom: 15px; font-size: 18px; }
    .success { background: #dcfce7; color: #166534; padding: 12px; border-radius: 8px; margin: 10px 0; }
    .error { background: #fee2e2; color: #991b1b; padding: 12px; border-radius: 8px; margin: 10px 0; }
    .user-card { background: #f9fafb; padding: 15px; border-radius: 8px; margin: 10px 0; border-left: 4px solid #ff9a76; }
    code { background: #f3f4f6; padding: 2px 6px; border-radius: 4px; font-size: 13px; }
    .warning { background: #fef3c7; color: #92400e; padding: 15px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #f59e0b; }
    table { width: 100%; border-collapse: collapse; margin: 15px 0; }
    th, td { padding: 10px; text-align: left; border-bottom: 1px solid #e5e7eb; }
    th { background: #f3f4f6; font-weight: 600; }
</style></head><body>";

echo "<div class='container'>";
echo "<h1>🔄 Reinicio de Usuarios PetGuard</h1>";

try {
    $db = getDB();
    
    // ============================================================
    // PASO 1: LIMPIAR TABLAS
    // ============================================================
    echo "<h2>🗑️ Paso 1: Limpiando tablas...</h2>";
    
    // Desactivar foreign key checks temporalmente
    $db->exec("SET FOREIGN_KEY_CHECKS = 0");
    
    // Eliminar datos de tablas relacionadas
    $db->exec("DELETE FROM voluntarios");
    echo "<div class='success'>✅ Tabla 'voluntarios' limpiada</div>";
    
    $db->exec("DELETE FROM veterinarios");
    echo "<div class='success'>✅ Tabla 'veterinarios' limpiada</div>";
    
    $db->exec("DELETE FROM veterinarios_sucursales");
    echo "<div class='success'>✅ Tabla 'veterinarios_sucursales' limpiada</div>";
    
    $db->exec("DELETE FROM mascotas");
    echo "<div class='success'>✅ Tabla 'mascotas' limpiada</div>";
    
    $db->exec("DELETE FROM usuarios");
    echo "<div class='success'>✅ Tabla 'usuarios' limpiada</div>";
    
    // Reactivar foreign key checks
    $db->exec("SET FOREIGN_KEY_CHECKS = 1");
    
    // ============================================================
    // PASO 2: CREAR USUARIOS
    // ============================================================
    echo "<h2>➕ Paso 2: Creando usuarios...</h2>";
    
    $password = 'PetGuard2026!';
    $passwordHash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    
    echo "<p><strong>Contraseña para todos los usuarios:</strong> <code>$password</code></p>";
    
    $usuarios = [
        [
            'rol_id' => 5, // Sistemas
            'nombre' => 'Jose Luis',
            'apellido_paterno' => 'Chavez',
            'apellido_materno' => 'Gomez',
            'email' => 'sistemas@petguard.mx',
            'telefono' => '5500000001',
            'curp' => 'CHGJ900101HDFRRL09'
        ],
        [
            'rol_id' => 1, // Administrador
            'nombre' => 'Jessica',
            'apellido_paterno' => 'Piñon',
            'apellido_materno' => 'Castillo',
            'email' => 'admin@petguard.mx',
            'telefono' => '5500000002',
            'curp' => 'PICJ900101MDFRSL09'
        ],
        [
            'rol_id' => 4, // Adoptante
            'nombre' => 'Ramon',
            'apellido_paterno' => 'Garrido',
            'apellido_materno' => 'Gomez',
            'email' => 'ramogarrido@gmail.com',
            'telefono' => '5512345678',
            'curp' => 'GAGR900101HDFRML09'
        ],
        [
            'rol_id' => 3, // Veterinario
            'nombre' => 'Carlos',
            'apellido_paterno' => 'Hernandez',
            'apellido_materno' => 'Lopez',
            'email' => 'vet@petguard.mx',
            'telefono' => '5500000003',
            'curp' => 'HELC900101HDFRPR09',
            'cedula_prof' => '12345678',
            'especialidad' => 'Medicina General'
        ]
    ];
    
    echo "<table>";
    echo "<tr><th>Rol</th><th>Nombre</th><th>Email</th><th>Estado</th></tr>";
    
    foreach ($usuarios as $userData) {
        $rol_nombres = [
            1 => 'Administrador',
            2 => 'Voluntario',
            3 => 'Veterinario',
            4 => 'Adoptante',
            5 => 'Sistemas'
        ];
        
        try {
            // Insertar usuario
            $sql = "INSERT INTO usuarios 
                    (rol_id, nombre, apellido_paterno, apellido_materno, email, password_hash, telefono, curp, activo, email_verified) 
                    VALUES 
                    (:rol_id, :nombre, :apellido_paterno, :apellido_materno, :email, :password_hash, :telefono, :curp, 1, 1)";
            
            $stmt = $db->prepare($sql);
            $result = $stmt->execute([
                'rol_id' => $userData['rol_id'],
                'nombre' => $userData['nombre'],
                'apellido_paterno' => $userData['apellido_paterno'],
                'apellido_materno' => $userData['apellido_materno'],
                'email' => $userData['email'],
                'password_hash' => $passwordHash,
                'telefono' => $userData['telefono'],
                'curp' => $userData['curp']
            ]);
            
            if ($result) {
                $userId = $db->lastInsertId();
                
                // Si es veterinario, crear registro en tabla veterinarios
                if ($userData['rol_id'] == 3) {
                    $sql_vet = "INSERT INTO veterinarios (usuario_id, cedula_prof, especialidad, disponible) 
                               VALUES (:user_id, :cedula, :especialidad, 0)";
                    $stmt_vet = $db->prepare($sql_vet);
                    $stmt_vet->execute([
                        'user_id' => $userId,
                        'cedula' => $userData['cedula_prof'],
                        'especialidad' => $userData['especialidad']
                    ]);
                }
                
                echo "<tr>";
                echo "<td>{$rol_nombres[$userData['rol_id']]}</td>";
                echo "<td>{$userData['nombre']} {$userData['apellido_paterno']}</td>";
                echo "<td><code>{$userData['email']}</code></td>";
                echo "<td style='color: #166534;'>✅ Creado</td>";
                echo "</tr>";
            } else {
                echo "<tr>";
                echo "<td>{$rol_nombres[$userData['rol_id']]}</td>";
                echo "<td>{$userData['nombre']} {$userData['apellido_paterno']}</td>";
                echo "<td><code>{$userData['email']}</code></td>";
                echo "<td style='color: #991b1b;'>❌ Error</td>";
                echo "</tr>";
            }
            
        } catch (Exception $e) {
            echo "<tr>";
            echo "<td>{$rol_nombres[$userData['rol_id']]}</td>";
            echo "<td>{$userData['nombre']} {$userData['apellido_paterno']}</td>";
            echo "<td><code>{$userData['email']}</code></td>";
            echo "<td style='color: #991b1b;'>❌ " . $e->getMessage() . "</td>";
            echo "</tr>";
        }
    }
    
    echo "</table>";
    
    // ============================================================
    // PASO 3: VERIFICAR CREACIÓN
    // ============================================================
    echo "<h2>🔍 Paso 3: Verificando usuarios creados...</h2>";
    
    $stmt = $db->query("SELECT u.id, u.nombre, u.apellido_paterno, u.email, r.nombre as rol 
                        FROM usuarios u 
                        INNER JOIN roles r ON u.rol_id = r.id 
                        ORDER BY u.id");
    $usuariosCreados = $stmt->fetchAll();
    
    echo "<p><strong>Total de usuarios creados:</strong> " . count($usuariosCreados) . "</p>";
    
    echo "<table>";
    echo "<tr><th>ID</th><th>Nombre</th><th>Email</th><th>Rol</th></tr>";
    foreach ($usuariosCreados as $user) {
        echo "<tr>";
        echo "<td>{$user['id']}</td>";
        echo "<td>{$user['nombre']} {$user['apellido_paterno']}</td>";
        echo "<td><code>{$user['email']}</code></td>";
        echo "<td>{$user['rol']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // ============================================================
    // PASO 4: CREDENCIALES DE ACCESO
    // ============================================================
    echo "<h2>🔑 Credenciales de Acceso</h2>";
    echo "<div class='warning'>";
    echo "<strong>⚠️ IMPORTANTE:</strong><br>";
    echo "1. <strong>ELIMINA ESTE ARCHIVO</strong> (reset-users-final.php) inmediatamente después de usarlo.<br>";
    echo "2. <strong>Contraseña para TODOS los usuarios:</strong> <code>$password</code><br>";
    echo "3. Estos son los usuarios disponibles para pruebas:<br>";
    echo "</div>";
    
    echo "<table>";
    echo "<tr><th>Rol</th><th>Email</th><th>Contraseña</th><th>Acceso</th></tr>";
    echo "<tr><td>Sistemas</td><td><code>sistemas@petguard.mx</code></td><td><code>$password</code></td><td>Dashboard Admin</td></tr>";
    echo "<tr><td>Administrador</td><td><code>admin@petguard.mx</code></td><td><code>$password</code></td><td>Dashboard Admin</td></tr>";
    echo "<tr><td>Adoptante</td><td><code>ramogarrido@gmail.com</code></td><td><code>$password</code></td><td>Landing Page</td></tr>";
    echo "<tr><td>Veterinario</td><td><code>vet@petguard.mx</code></td><td><code>$password</code></td><td>Bloqueado (pendiente verificación)</td></tr>";
    echo "</table>";
    
    echo "<div class='success'>";
    echo "<strong>✅ Proceso completado exitosamente</strong><br>";
    echo "Ahora puedes hacer login con cualquiera de los usuarios listados arriba.";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div class='error'>❌ Error: " . $e->getMessage() . "</div>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "</div></body></html>";