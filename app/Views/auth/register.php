<?php 
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Cuenta | PetGuard</title>
    <link rel="stylesheet" href="/petguard/public/css/auth.css?v=<?php echo time(); ?>">
</head>
<body class="auth-body">

    <?php if (session_status() === PHP_SESSION_ACTIVE && isset($_SESSION['flash'])): ?>
        <?php foreach ($_SESSION['flash'] as $type => $message): ?>
            <div class="flash-message flash-<?php echo htmlspecialchars($type); ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endforeach; ?>
        <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>

    <div class="auth-container">
        
        <div class="auth-left">
            <img src="https://images.unsplash.com/photo-1601758228041-f3b2795255f1?auto=format&fit=crop&w=1200&q=80" alt="Abrazo con mascota" class="auth-left-image">
            <div class="auth-left-overlay"></div>
            <div class="auth-left-content">
                <h2>Únete a la comunidad PetGuard.</h2>
                <p>Ya seas un futuro padre/madre de mascota, un rescatista apasionado o un profesional veterinario, aquí tienes un lugar para hacer la diferencia.</p>
            </div>
        </div>

        <div class="auth-right">
            <div class="auth-form-wrapper">
                
                <a href="/petguard/public/login.php" class="auth-logo">
                    <svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                        <path d="M50 50 C45 45, 40 48, 42 53 C44 58, 50 60, 55 58 C60 56, 62 50, 58 46 C54 42, 48 44, 50 50 Z M35 40 C32 38, 28 40, 29 44 C30 48, 34 49, 37 47 C40 45, 40 42, 35 40 Z M65 40 C68 38, 72 40, 71 44 C70 48, 66 49, 63 47 C60 45, 60 42, 65 40 Z M40 30 C38 28, 34 29, 35 33 C36 37, 40 38, 42 36 C44 34, 43 31, 40 30 Z M60 30 C62 28, 66 29, 65 33 C64 37, 60 38, 58 36 C56 34, 57 31, 60 30 Z"/>
                    </svg>
                    PetGuard
                </a>

                <h1 class="auth-title">Crear Cuenta</h1>
                <p class="auth-subtitle">Completa los pasos para unirte a PetGuard.</p>

                <div class="wizard-progress">
                    <div class="wizard-progress-bar" id="progressBar"></div>
                    <div class="wizard-step-indicator active" data-step="1">
                        <div class="wizard-step-circle">1</div>
                        <div class="wizard-step-label">Perfil</div>
                    </div>
                    <div class="wizard-step-indicator" data-step="2">
                        <div class="wizard-step-circle">2</div>
                        <div class="wizard-step-label">Datos</div>
                    </div>
                    <div class="wizard-step-indicator" data-step="3">
                        <div class="wizard-step-circle">3</div>
                        <div class="wizard-step-label">Detalles</div>
                    </div>
                </div>

                <form id="registerForm" action="/petguard/public/register.php" method="POST" enctype="multipart/form-data">
                    
                    <div class="wizard-container" id="wizardContainer">
                        
                        <!-- PASO 1 -->
                        <div class="wizard-step active" data-step="1">
                            <div class="form-group">
                                <label class="form-label">¿Cómo quieres participar?</label>
                                <div class="role-selector">
                                    <div class="role-card active" data-role="4" onclick="selectRole(this)">
                                        <span class="role-icon"></span>
                                        <p class="role-name">Adoptante</p>
                                        <p class="role-desc">Busco mascota</p>
                                    </div>
                                    <div class="role-card" data-role="2" onclick="selectRole(this)">
                                        <span class="role-icon">❤️</span>
                                        <p class="role-name">Voluntario</p>
                                        <p class="role-desc">Quiero ayudar</p>
                                    </div>
                                    <div class="role-card" data-role="3" onclick="selectRole(this)">
                                        <span class="role-icon">🩺</span>
                                        <p class="role-name">Veterinario</p>
                                        <p class="role-desc">Servicios clínicos</p>
                                    </div>
                                </div>
                                <input type="hidden" name="rol_id" id="selectedRole" value="4">
                            </div>

                            <div class="form-group">
                                <label for="email" class="form-label">Correo Electrónico</label>
                                <input type="email" id="email" name="email" class="form-input" placeholder="ejemplo@correo.com" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                                <div class="error-message" id="error-email">Ingresa un correo válido</div>
                            </div>

                            <div class="form-group">
                                <label for="password" class="form-label">Contraseña</label>
                                <input type="password" id="password" name="password" class="form-input" placeholder="Mínimo 8 caracteres" required minlength="8">
                                <div class="error-message" id="error-password">La contraseña debe tener al menos 8 caracteres</div>
                            </div>

                            <div class="form-group">
                                <label for="password_confirm" class="form-label">Confirmar Contraseña</label>
                                <input type="password" id="password_confirm" name="password_confirm" class="form-input" placeholder="Repite tu contraseña" required>
                                <div class="error-message" id="error-password-confirm">Las contraseñas no coinciden</div>
                            </div>
                        </div>

                        <!-- PASO 2 -->
                        <div class="wizard-step" data-step="2">
                            <div class="form-group">
                                <label for="nombre" class="form-label">Nombre(s)</label>
                                <input type="text" id="nombre" name="nombre" class="form-input" placeholder="Juan" required value="<?php echo isset($_POST['nombre']) ? htmlspecialchars($_POST['nombre']) : ''; ?>">
                            </div>

                            <div class="form-group" style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                                <div>
                                    <label for="apellido_paterno" class="form-label">Apellido Paterno</label>
                                    <input type="text" id="apellido_paterno" name="apellido_paterno" class="form-input" placeholder="Pérez" required value="<?php echo isset($_POST['apellido_paterno']) ? htmlspecialchars($_POST['apellido_paterno']) : ''; ?>">
                                </div>
                                <div>
                                    <label for="apellido_materno" class="form-label">Apellido Materno</label>
                                    <input type="text" id="apellido_materno" name="apellido_materno" class="form-input" placeholder="García" value="<?php echo isset($_POST['apellido_materno']) ? htmlspecialchars($_POST['apellido_materno']) : ''; ?>">
                                </div>
                            </div>

                            <div class="form-group" style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                                <div>
                                    <label for="telefono" class="form-label">Teléfono</label>
                                    <input type="tel" id="telefono" name="telefono" class="form-input" placeholder="5512345678" required value="<?php echo isset($_POST['telefono']) ? htmlspecialchars($_POST['telefono']) : ''; ?>">
                                </div>
                                <div>
                                    <label for="curp" class="form-label">CURP</label>
                                    <input type="text" id="curp" name="curp" class="form-input" placeholder="PEGJ900101..." required style="text-transform: uppercase;" value="<?php echo isset($_POST['curp']) ? htmlspecialchars($_POST['curp']) : ''; ?>">
                                </div>
                            </div>
                        </div>

                        <!-- PASO 3 -->
                        <div class="wizard-step" data-step="3">
                            
                            <div id="fields-adoptante" class="role-fields">
                                <div class="form-group">
                                    <label class="form-label">Calle y número</label>
                                    <input type="text" name="calle" class="form-input" placeholder="Ej. Av. Insurgentes 123" value="<?php echo isset($_POST['calle']) ? htmlspecialchars($_POST['calle']) : ''; ?>">
                                </div>
                                <div class="form-group" style="display: grid; grid-template-columns: 2fr 1fr; gap: 12px;">
                                    <div>
                                        <label class="form-label">Colonia</label>
                                        <input type="text" name="colonia" class="form-input" placeholder="Roma Norte" value="<?php echo isset($_POST['colonia']) ? htmlspecialchars($_POST['colonia']) : ''; ?>">
                                    </div>
                                    <div>
                                        <label class="form-label">C.P.</label>
                                        <input type="text" name="cp" class="form-input" placeholder="06700" value="<?php echo isset($_POST['cp']) ? htmlspecialchars($_POST['cp']) : ''; ?>">
                                    </div>
                                </div>
                            </div>

                            <div id="fields-voluntario" class="role-fields" style="display: none;">
                                <div class="form-group">
                                    <label class="form-label">Zona de cobertura principal</label>
                                    <input type="text" name="zona_cobertura" class="form-input" placeholder="Ej. Coyoacán, CDMX" value="<?php echo isset($_POST['zona_cobertura']) ? htmlspecialchars($_POST['zona_cobertura']) : ''; ?>">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">¿Cuentas con hogar temporal?</label>
                                    <select name="hogar_temporal" class="form-select">
                                        <option value="0" <?php echo (isset($_POST['hogar_temporal']) && $_POST['hogar_temporal'] == '0') ? 'selected' : ''; ?>>No, solo puedo ayudar en campo</option>
                                        <option value="1" <?php echo (isset($_POST['hogar_temporal']) && $_POST['hogar_temporal'] == '1') ? 'selected' : ''; ?>>Sí, puedo hospedar mascotas</option>
                                    </select>
                                </div>
                            </div>

                            <div id="fields-veterinario" class="role-fields" style="display: none;">
                                <div class="form-group">
                                    <label class="form-label">Cédula Profesional</label>
                                    <input type="text" name="cedula_prof" class="form-input" placeholder="Número de cédula SEP" value="<?php echo isset($_POST['cedula_prof']) ? htmlspecialchars($_POST['cedula_prof']) : ''; ?>">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Especialidad</label>
                                    <input type="text" name="especialidad" class="form-input" placeholder="Ej. Cirugía, Dermatología..." value="<?php echo isset($_POST['especialidad']) ? htmlspecialchars($_POST['especialidad']) : ''; ?>">
                                </div>
                                
                                <label class="form-label">Subir documentos (Cédula e INE)</label>
                                <div class="file-upload-zone" id="dropzone">
                                    <input type="file" name="documentos[]" id="fileInput" multiple accept=".pdf,.jpg,.jpeg,.png">
                                    <div class="upload-icon">📁</div>
                                    <p class="upload-text">Arrastra tus archivos aquí o haz clic</p>
                                    <p class="upload-hint">PDF, JPG o PNG (Máx. 5MB)</p>
                                    <p id="fileList" style="margin-top: 8px; font-size: 12px; color: #ff9a76; font-weight: 600;"></p>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="wizard-nav">
                        <button type="button" class="btn-wizard btn-wizard-back" id="btnBack" style="display: none;">← Atrás</button>
                        <button type="button" class="btn-wizard btn-wizard-next" id="btnNext">Continuar →</button>
                        <button type="submit" class="btn-wizard btn-wizard-next" id="btnSubmit" style="display: none;">Crear mi cuenta ✓</button>
                    </div>

                </form>

                <div class="auth-footer">
                    ¿Ya tienes una cuenta? <a href="/petguard/public/login.php">Inicia sesión aquí</a>
                </div>
            </div>
        </div>

    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="/petguard/public/js/auth.js?v=<?php echo time(); ?>"></script>
</body>
</html>