<?php
/**
 * ============================================================
 * PETGUARD - Landing Page Profesional (Archivo Completo)
 * ============================================================
 */
if (!isset($data)) {
    $data = ['stats' => ['mascotas_disponibles' => 0, 'adopciones_completadas' => 0]];
}

$fallbackImage = 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgc3Ryb2tlPSIjZmY2YjRhIiBzdHJva2Utd2lkdGg9IjEuNSI+PHBhdGggZD0iTTEwIDUuMTcyQzEwIDMuNzgyIDguNDIzIDIuNjc5IDYuNSAzYy0yLjgyMy40Ny00LjExMyA2LjAwNi00IDcgLjA4LjcwMyAxLjcyNSAxLjcyMiAzLjY1NiAxIDEuMjYxLS40NzIgMS45Ni0xLjQ1IDIuMzQ0LTIuNU0xNC4yNjcgNS4xNzJjMC0xLjM5IDEuNTc3LTIuNDkzIDMuNS0yLjE3MiAyLjgyMy40NyA0LjExMyA2LjAwNiA0IDcgLS4wOC43MDMtMS43MjUgMS43MjItMy42NTYgMS0xLjI2MS0uNDcyLTEuODU1LTEuNDUtMi4yMzktMi41TTggMTR2LjVNMTYgMTR2LjVNMTEuMjUgMTYuMjVoMS41TDEyIDE3bC0uNzUtLjc1ek00LjQyIDExLjI0N0ExMy4xNTIgMTMuMTUyIDAgMDA0IDE0LjVjMCAyLjA3Ljg0IDMuNSAyLjUgMy41aDExYzEuNjYgMCAyLjUtMS40MyAyLjUtMy41IDAtMS4wNy0uMTQtMi4yNy0uNDItMy4yNTNNOSAxMGEyIDAgMTEtNCAwIDIgMiAwIDAxNCAwem0xMCAwYTIgMiAwIDExLTQgMCAyIDIgMCAwMTQgMHoiLz48L3N2Zz4=';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PetGuard - Encuentra a tu mejor amigo</title>
    <link rel="stylesheet" href="/petguard/public/css/landing.css?v=<?php echo time(); ?>">
</head>
<body>

    <header class="landing-header">
        <div class="nav-container">
            <a href="/petguard/public/index.php" class="logo">
                <svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                    <path d="M50 50 C45 45, 40 48, 42 53 C44 58, 50 60, 55 58 C60 56, 62 50, 58 46 C54 42, 48 44, 50 50 Z M35 40 C32 38, 28 40, 29 44 C30 48, 34 49, 37 47 C40 45, 40 42, 35 40 Z M65 40 C68 38, 72 40, 71 44 C70 48, 66 49, 63 47 C60 45, 60 42, 65 40 Z M40 30 C38 28, 34 29, 35 33 C36 37, 40 38, 42 36 C44 34, 43 31, 40 30 Z M60 30 C62 28, 66 29, 65 33 C64 37, 60 38, 58 36 C56 34, 57 31, 60 30 Z"/>
                </svg>
                PetGuard
            </a>
            <nav class="nav-links">
                <a href="#como-funciona">Cómo funciona</a>
                <a href="#beneficios">Beneficios</a>
                <a href="#contacto">Contacto</a>
            </nav>
            <div class="nav-buttons">
                <a href="/petguard/public/login.php" class="btn btn-secondary">Iniciar Sesión</a>
                <a href="/petguard/public/register.php" class="btn btn-primary">Registrarse</a>
            </div>
        </div>
    </header>

    <section class="hero">
        <div class="hero-content">
            <div class="hero-badge">Más de <?php echo number_format($data['stats']['adopciones_completadas']); ?> adopciones exitosas</div>
            <h1>No compres,<br><span>adopta</span></h1>
            <p>Conectamos mascotas rescatadas que buscan un hogar con personas maravillosas como tú. El proceso es seguro, transparente y lleno de amor.</p>
            <div class="hero-buttons">
                <a href="/petguard/public/catalogo.php" class="btn btn-primary">🐾 Ver Mascotas</a>
                <a href="#como-funciona" class="btn btn-secondary">Conoce el proceso</a>
            </div>
            <div class="hero-stats">
                <div class="stat"><div class="stat-value"><?php echo number_format($data['stats']['mascotas_disponibles']); ?></div><div class="stat-label">Mascotas disponibles</div></div>
                <div class="stat"><div class="stat-value">48h</div><div class="stat-label">Tiempo de respuesta</div></div>
                <div class="stat"><div class="stat-value">100%</div><div class="stat-label">Seguimiento</div></div>
            </div>
        </div>
        <div class="hero-visual">
            <div class="hero-image-container">
                <img src="https://images.unsplash.com/photo-1450778869180-41d0601e046e?w=800&h=600&fit=crop" alt="Mascota feliz" class="hero-image" onerror="this.src='<?php echo $fallbackImage; ?>'; this.style.objectFit='contain'; this.style.padding='40px';">
            </div>
            <div class="floating-card card-1"><div class="floating-card-icon">🏠</div><h4>2,500+</h4><p>Hogares felices</p></div>
            <div class="floating-card card-2"><div class="floating-card-icon">❤️</div><h4>98%</h4><p>Adopciones exitosas</p></div>
        </div>
    </section>

    <section class="timeline-section" id="como-funciona">
        <div class="section-header">
            <div class="section-label">Proceso Simple</div>
            <h2 class="section-title">¿Cómo funciona?</h2>
            <p class="section-subtitle">Adoptar es más fácil de lo que crees. Solo sigue estos 4 pasos y estarás más cerca de encontrar a tu nuevo mejor amigo.</p>
        </div>

        <div class="timeline-wrapper">
            <div class="timeline-track">
                <div class="timeline-step">
                    <div class="timeline-step-icon">
                        <span class="step-number">1</span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    </div>
                    <div class="timeline-step-content">
                        <h3>Explora el Catálogo</h3>
                        <p>Navega por nuestras mascotas disponibles. Filtra por especie, tamaño o edad para encontrar a tu compañero ideal.</p>
                    </div>
                </div>

                <div class="timeline-step">
                    <div class="timeline-step-icon">
                        <span class="step-number">2</span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                    </div>
                    <div class="timeline-step-content">
                        <h3>Crea tu Cuenta y Solicita</h3>
                        <p>Regístrate en menos de 2 minutos y envía tu solicitud. Nuestro formulario asegura el mejor match para la mascota.</p>
                    </div>
                </div>

                <div class="timeline-step">
                    <div class="timeline-step-icon">
                        <span class="step-number">3</span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>
                    </div>
                    <div class="timeline-step-content">
                        <h3>Entrevista y Visita</h3>
                        <p>Revisamos tu solicitud en 48h. Si todo está en orden, agendamos una visita para que conozcas a tu posible compañero.</p>
                    </div>
                </div>

                <div class="timeline-step">
                    <div class="timeline-step-icon">
                        <span class="step-number">4</span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                    </div>
                    <div class="timeline-step-content">
                        <h3>¡Adopción Completada!</h3>
                        <p>Firmamos el compromiso y te llevas a tu nuevo amigo a casa. Recibirás seguimiento y asesoría veterinaria continua.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="features-section" id="beneficios">
        <div class="section-header">
            <div class="section-label">¿Por qué adoptar?</div>
            <h2 class="section-title">Beneficios de adoptar</h2>
            <p class="section-subtitle">Adoptar no solo le das una segunda oportunidad a una mascota, también recibes beneficios increíbles.</p>
        </div>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon"><svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg></div>
                <h3>Amor incondicional</h3>
                <p>Las mascotas adoptadas desarrollan un vínculo especial y lealtad incomparable con sus nuevos dueños.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg></div>
                <h3>Menor costo</h3>
                <p>La adopción es más económica que comprar. Incluye vacunas, esterilización y microchip.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 12h-4l-3 9L9 3l-3 9H2"></path></svg></div>
                <h3>Salvas una vida</h3>
                <p>Al adoptar, liberas espacio en el albergue para que otra mascota pueda ser rescatada.</p>
            </div>
        </div>
    </section>

    <section class="cta-section" id="contacto">
        <div class="cta-container">
            <h2>¿Listo para cambiar una vida?</h2>
            <p>Únete a nuestra comunidad de adoptantes y dale a una mascota la segunda oportunidad que merece. Tu mejor amigo te está esperando.</p>
            <div class="cta-buttons">
                <a href="/petguard/public/catalogo.php" class="btn btn-white">Ver Mascotas Disponibles</a>
                <a href="/petguard/public/register.php" class="btn btn-outline-white">Crear Cuenta Gratis</a>
            </div>
        </div>
    </section>

    <footer class="landing-footer">
        <div class="footer-content">
            <div class="footer-logo">
                <svg width="32" height="32" viewBox="0 0 100 100" fill="var(--accent)"><path d="M50 50 C45 45, 40 48, 42 53 C44 58, 50 60, 55 58 C60 56, 62 50, 58 46 C54 42, 48 44, 50 50 Z"/></svg>
                PetGuard
            </div>
            <div class="footer-links">
                <a href="/petguard/public/catalogo.php">Catálogo</a>
                <a href="/petguard/public/login.php">Iniciar Sesión</a>
                <a href="/petguard/public/register.php">Registrarse</a>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> PetGuard. Todos los derechos reservados. | <a href="/petguard/public/login.php">Acceso al Sistema</a></p>
        </div>
    </footer>

    <script src="/petguard/public/js/landing.js?v=<?php echo time(); ?>"></script>
</body>
</html>