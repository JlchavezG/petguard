<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión | PetGuard</title>
    <link rel="stylesheet" href="/petguard/public/css/auth.css">
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
            <img src="https://images.unsplash.com/photo-1548199973-03cce0bbc87b?auto=format&fit=crop&w=1200&q=80" alt="Perro feliz" class="auth-left-image">
            <div class="auth-left-overlay"></div>
            <div class="auth-left-content">
                <h2>Bienvenido de vuelta a PetGuard.</h2>
                <p>Tu compañero de cuatro patas te está esperando. Inicia sesión para gestionar adopciones, rescates o tu perfil veterinario.</p>
            </div>
        </div>

        <div class="auth-right">
            <div class="auth-form-wrapper">
                
                <a href="/petguard/public/" class="auth-logo">
                    <svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                        <path d="M50 50 C45 45, 40 48, 42 53 C44 58, 50 60, 55 58 C60 56, 62 50, 58 46 C54 42, 48 44, 50 50 Z M35 40 C32 38, 28 40, 29 44 C30 48, 34 49, 37 47 C40 45, 40 42, 35 40 Z M65 40 C68 38, 72 40, 71 44 C70 48, 66 49, 63 47 C60 45, 60 42, 65 40 Z M40 30 C38 28, 34 29, 35 33 C36 37, 40 38, 42 36 C44 34, 43 31, 40 30 Z M60 30 C62 28, 66 29, 65 33 C64 37, 60 38, 58 36 C56 34, 57 31, 60 30 Z"/>
                    </svg>
                    PetGuard
                </a>

                <h1 class="auth-title">Iniciar Sesión</h1>
                <p class="auth-subtitle">Ingresa tus credenciales para acceder a tu panel.</p>

                <form id="loginForm" action="/petguard/public/login.php" method="POST">
                    
                    <div class="form-group">
                        <label for="email" class="form-label">Correo Electrónico</label>
                        <input type="email" id="email" name="email" class="form-input" placeholder="ejemplo@correo.com" required>
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">Contraseña</label>
                        <input type="password" id="password" name="password" class="form-input" placeholder="••••••••" required>
                    </div>

                    <div class="form-options">
                        <label class="form-checkbox">
                            <input type="checkbox" name="remember" id="remember">
                            <span>Recordarme</span>
                        </label>
                        <a href="#" class="form-link">¿Olvidaste tu contraseña?</a>
                    </div>

                    <button type="submit" class="btn-auth">Entrar a mi cuenta</button>

                    <div class="auth-footer">
                        ¿Aún no tienes una cuenta? <a href="/petguard/public/register.php">Regístrate aquí</a>
                    </div>

                </form>
            </div>
        </div>

    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const currentHour = new Date().getHours();
            if (currentHour >= 19 || currentHour < 7) document.body.classList.add('dark-mode');

            gsap.from(".auth-left-content", { x: -50, opacity: 0, duration: 1, ease: "power3.out", delay: 0.2 });
            gsap.from(".auth-logo", { y: -20, opacity: 0, duration: 0.6, ease: "power2.out" });
            gsap.from(".auth-title", { y: 20, opacity: 0, duration: 0.6, delay: 0.1, ease: "power2.out" });
            gsap.from(".auth-subtitle", { y: 20, opacity: 0, duration: 0.6, delay: 0.2, ease: "power2.out" });
            gsap.from(".form-group", { y: 20, opacity: 0, duration: 0.6, stagger: 0.1, delay: 0.3, ease: "power2.out" });
            gsap.from(".form-options", { y: 20, opacity: 0, duration: 0.6, delay: 0.5, ease: "power2.out" });
            gsap.from(".btn-auth", { y: 20, opacity: 1, duration: 0.6, delay: 0.6, ease: "power2.out" });
            gsap.from(".auth-footer", { y: 20, opacity: 0, duration: 0.6, delay: 0.7, ease: "power2.out" });
        });
    </script>
</body>
</html>