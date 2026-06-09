<?php
if (!defined('PETGUARD_APP')) {
    http_response_code(403);
    exit('Acceso no permitido.');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión | PetGuard</title>
    <link rel="stylesheet" href="/petguard/public/css/auth.css?v=<?php echo time(); ?>">
</head>
<body>
    <div class="auth-container">
        <a href="/petguard/public/index.php" class="auth-logo">
            <svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                <path d="M50 50 C45 45, 40 48, 42 53 C44 58, 50 60, 55 58 C60 56, 62 50, 58 46 C54 42, 48 44, 50 50 Z M35 40 C32 38, 28 40, 29 44 C30 48, 34 49, 37 47 C40 45, 40 42, 35 40 Z M65 40 C68 38, 72 40, 71 44 C70 48, 66 49, 63 47 C60 45, 60 42, 65 40 Z M40 30 C38 28, 34 29, 35 33 C36 37, 40 38, 42 36 C44 34, 43 31, 40 30 Z M60 30 C62 28, 66 29, 65 33 C64 37, 60 38, 58 36 C56 34, 57 31, 60 30 Z"/>
            </svg>
            PetGuard
        </a>
        
        <div class="auth-header">
            <h1>Bienvenido de nuevo</h1>
            <p>Ingresa tus credenciales para acceder a tu cuenta</p>
        </div>

        <?php if (isset($_SESSION['flash'])): ?>
            <?php foreach ($_SESSION['flash'] as $type => $message): ?>
                <div class="flash-message flash-<?php echo htmlspecialchars($type); ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endforeach; ?>
            <?php unset($_SESSION['flash']); ?>
        <?php endif; ?>

        <form method="POST" action="/petguard/public/login.php">
            <div class="form-group">
                <label class="form-label">Correo Electrónico</label>
                <input type="email" name="email" class="form-input" placeholder="ejemplo@correo.com" required autofocus>
            </div>

            <div class="form-group">
                <label class="form-label">Contraseña</label>
                <input type="password" name="password" class="form-input" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn">Iniciar Sesión</button>
        </form>

        <div class="auth-footer">
            ¿No tienes una cuenta? <a href="/petguard/public/register.php">Regístrate aquí</a>
        </div>
        <div class="auth-footer" style="margin-top: 12px;">
            <a href="/petguard/public/index.php">← Volver al inicio</a>
        </div>
    </div>
</body>
</html>