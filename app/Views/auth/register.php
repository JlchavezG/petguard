<?php
if (!defined('PETGUARD_APP')) {
    http_response_code(403);
    exit('Acceso no permitido.');
}

$roles = [];
try {
    $db = getDB();
    $stmt = $db->query("SELECT id, nombre FROM roles WHERE id IN (2, 3, 4) ORDER BY id");
    $roles = $stmt->fetchAll();
} catch (Exception $e) {
    $roles = [
        ['id' => 2, 'nombre' => 'Voluntario'],
        ['id' => 3, 'nombre' => 'Veterinario'],
        ['id' => 4, 'nombre' => 'Adoptante']
    ];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrarse | PetGuard</title>
    <link rel="stylesheet" href="/petguard/public/css/auth.css?v=<?php echo time(); ?>">
    <style>
        .dynamic-section { display: none; margin-top: 20px; padding-top: 20px; border-top: 2px dashed var(--border-color); }
        .section-title { font-size: 16px; font-weight: 700; color: var(--accent-color); margin-bottom: 16px; display: flex; align-items: center; gap: 8px; }
        .section-title::before { content: '🩺'; font-size: 20px; }
        .required-badge { background: #fee2e2; color: #991b1b; font-size: 11px; padding: 2px 8px; border-radius: 10px; font-weight: 700; margin-left: 8px; }
    </style>
</head>
<body>
    <div class="auth-container" style="max-width: 600px;">
        <a href="/petguard/public/index.php" class="auth-logo">
            <svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                <path d="M50 50 C45 45, 40 48, 42 53 C44 58, 50 60, 55 58 C60 56, 62 50, 58 46 C54 42, 48 44, 50 50 Z M35 40 C32 38, 28 40, 29 44 C30 48, 34 49, 37 47 C40 45, 40 42, 35 40 Z M65 40 C68 38, 72 40, 71 44 C70 48, 66 49, 63 47 C60 45, 60 42, 65 40 Z M40 30 C38 28, 34 29, 35 33 C36 37, 40 38, 42 36 C44 34, 43 31, 40 30 Z M60 30 C62 28, 66 29, 65 33 C64 37, 60 38, 58 36 C56 34, 57 31, 60 30 Z"/>
            </svg>
            PetGuard
        </a>
        
        <div class="auth-header">
            <h1>Crear nueva cuenta</h1>
            <p>Completa tus datos para registrarte en el sistema</p>
        </div>

        <?php if (isset($_SESSION['flash'])): ?>
            <?php foreach ($_SESSION['flash'] as $type => $message): ?>
                <div class="flash-message flash-<?php echo htmlspecialchars($type); ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endforeach; ?>
            <?php unset($_SESSION['flash']); ?>
        <?php endif; ?>

        <form method="POST" action="/petguard/public/register.php">
            <div class="form-group">
                <label class="form-label">Tipo de Usuario (Rol) *</label>
                <select name="rol_id" id="rol_id" class="form-select" required>
                    <option value="">Seleccionar rol...</option>
                    <?php foreach ($roles as $rol): ?>
                        <option value="<?php echo $rol['id']; ?>">
                            <?php echo htmlspecialchars($rol['nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Nombre(s) *</label>
                    <input type="text" name="nombre" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Apellido Paterno *</label>
                    <input type="text" name="apellido_paterno" class="form-input" required>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Apellido Materno</label>
                <input type="text" name="apellido_materno" class="form-input">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Correo Electrónico *</label>
                    <input type="email" name="email" class="form-input" placeholder="ejemplo@correo.com" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Teléfono</label>
                    <input type="tel" name="telefono" class="form-input" placeholder="10 dígitos">
                </div>
            </div>

            <!-- SECCIÓN DINÁMICA: DATOS PROFESIONALES DE VETERINARIO -->
            <div id="section-veterinario" class="dynamic-section">
                <div class="section-title">
                    Datos Profesionales
                    <span class="required-badge">Obligatorio para validación</span>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Cédula Profesional *</label>
                    <input type="text" name="cedula_prof" class="form-input vet-field" maxlength="20" placeholder="Ej: 12345678">
                    <small style="color: var(--text-secondary); font-size: 12px; margin-top: 4px; display: block;">
                        Sin espacios ni guiones. Será verificada por el administrador.
                    </small>
                </div>

                <div class="form-group">
                    <label class="form-label">Especialidad</label>
                    <select name="especialidad" class="form-select vet-field">
                        <option value="">Seleccionar...</option>
                        <option value="Medicina General">Medicina General</option>
                        <option value="Cirugía">Cirugía</option>
                        <option value="Dermatología">Dermatología</option>
                        <option value="Cardiología">Cardiología</option>
                        <option value="Neurología">Neurología</option>
                        <option value="Oftalmología">Oftalmología</option>
                        <option value="Otra">Otra</option>
                    </select>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Universidad</label>
                        <input type="text" name="universidad" class="form-input vet-field" placeholder="Ej: UNAM">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Años de Experiencia</label>
                        <input type="number" name="anios_exp" class="form-input vet-field" min="0" max="60" placeholder="Ej: 5">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Biografía Profesional</label>
                    <textarea name="bio" class="form-input vet-field" rows="3" placeholder="Cuéntanos sobre tu experiencia y enfoque profesional..."></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">¿Disponible para consultas?</label>
                        <select name="disponible" class="form-select vet-field">
                            <option value="1">Sí</option>
                            <option value="0">No</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">¿Atiende emergencias?</label>
                        <select name="atiende_emergencias" class="form-select vet-field">
                            <option value="0">No</option>
                            <option value="1">Sí</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Tarifa de Consulta (MXN)</label>
                    <input type="number" name="tarifa_consulta" class="form-input vet-field" min="0" step="0.01" placeholder="Ej: 500.00">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Contraseña *</label>
                <input type="password" name="password" class="form-input" placeholder="Mínimo 8 caracteres" minlength="8" required>
            </div>

            <button type="submit" class="btn">Registrar Usuario</button>
        </form>

        <div class="auth-footer">
            ¿Ya tienes una cuenta? <a href="/petguard/public/login.php">Inicia sesión aquí</a>
        </div>
        <div class="auth-footer" style="margin-top: 12px;">
            <a href="/petguard/public/index.php">← Volver al inicio</a>
        </div>
    </div>

    <script>
        document.getElementById('rol_id').addEventListener('change', function() {
            const rol = this.value;
            const sectionVet = document.getElementById('section-veterinario');
            
            // Ocultar sección y quitar required
            sectionVet.style.display = 'none';
            document.querySelectorAll('.vet-field').forEach(el => {
                el.required = false;
                el.disabled = true;
            });

            if (rol === '3') { // Veterinario
                sectionVet.style.display = 'block';
                document.querySelectorAll('.vet-field').forEach(el => {
                    el.disabled = false;
                });
                // Hacer obligatoria la cédula profesional
                const cedulaInput = document.querySelector('[name="cedula_prof"]');
                if (cedulaInput) cedulaInput.required = true;
            }
        });
    </script>
</body>
</html>