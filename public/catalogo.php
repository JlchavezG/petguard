<?php
/**
 * ============================================================
 * PETGUARD - Catálogo Público de Mascotas (MVC Puro)
 * ============================================================
 */
define('PETGUARD_APP', true);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../config/app.php';
require_once '../config/database.php';

try {
    $db = getDB();
    
    // Obtener mascotas disponibles (usando los ENUM reales de tu BD)
    $sql = "SELECT m.id, m.nombre, m.nombre_interno, m.genero, m.edad_aprox_meses, 
                   m.peso_kg, m.foto_principal, m.estatus, m.urgente, m.descripcion,
                   e.nombre as especie_nombre, e.icono as especie_icono,
                   r.nombre as raza_nombre
            FROM mascotas m
            LEFT JOIN especies e ON m.especie_id = e.id
            LEFT JOIN razas r ON m.raza_id = r.id
            WHERE m.activo = 1 
              AND m.estatus IN ('rescatado', 'en_albergue', 'en_adopcion')
            ORDER BY m.urgente DESC, m.created_at DESC";
    
    $stmt = $db->query($sql);
    $mascotas = $stmt->fetchAll();
    
    // Obtener estadísticas
    $statsStmt = $db->query("SELECT COUNT(*) as count FROM mascotas WHERE activo = 1 AND estatus IN ('en_albergue', 'en_adopcion', 'rescatado')");
    $totalMascotas = $statsStmt->fetch()['count'] ?? 0;
    
} catch (Exception $e) {
    error_log('Catalogo Error: ' . $e->getMessage());
    $mascotas = [];
    $totalMascotas = 0;
}

// Fallback SVG en Base64 (Ícono de mascota profesional) para evitar imágenes rotas
$fallbackImage = 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgc3Ryb2tlPSIjZmY2YjRhIiBzdHJva2Utd2lkdGg9IjEuNSI+PHBhdGggZD0iTTEwIDUuMTcyQzEwIDMuNzgyIDguNDIzIDIuNjc5IDYuNSAzYy0yLjgyMy40Ny00LjExMyA2LjAwNi00IDcgLjA4LjcwMyAxLjcyNSAxLjcyMiAzLjY1NiAxIDEuMjYxLS40NzIgMS45Ni0xLjQ1IDIuMzQ0LTIuNU0xNC4yNjcgNS4xNzJjMC0xLjM5IDEuNTc3LTIuNDkzIDMuNS0yLjE3MiAyLjgyMy40NyA0LjExMyA2LjAwNiA0IDcgLS4wOC43MDMtMS43MjUgMS43MjItMy42NTYgMS0xLjI2MS0uNDcyLTEuODU1LTEuNDUtMi4yMzktMi41TTggMTR2LjVNMTYgMTR2LjVNMTEuMjUgMTYuMjVoMS41TDEyIDE3bC0uNzUtLjc1ek00LjQyIDExLjI0N0ExMy4xNTIgMTMuMTUyIDAgMDA0IDE0LjVjMCAyLjA3Ljg0IDMuNSAyLjUgMy41aDExYzEuNjYgMCAyLjUtMS40MyAyLjUtMy41IDAtMS4wNy0uMTQtMi4yNy0uNDItMy4yNTNNOSAxMGEyIDAgMTEtNCAwIDIgMiAwIDAxNCAwem0xMCAwYTIgMiAwIDExLTQgMCAyIDIgMCAwMTQgMHoiLz48L3N2Zz4=';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo de Mascotas | PetGuard</title>
    <link rel="stylesheet" href="/petguard/public/css/landing.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="/petguard/public/css/catalogo.css?v=<?php echo time(); ?>">
</head>
<body style="background: var(--bg-secondary);">

    <!-- Header -->
    <header class="landing-header">
        <div class="nav-container">
            <a href="/petguard/public/index.php" class="logo">
                <svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                    <path d="M50 50 C45 45, 40 48, 42 53 C44 58, 50 60, 55 58 C60 56, 62 50, 58 46 C54 42, 48 44, 50 50 Z M35 40 C32 38, 28 40, 29 44 C30 48, 34 49, 37 47 C40 45, 40 42, 35 40 Z M65 40 C68 38, 72 40, 71 44 C70 48, 66 49, 63 47 C60 45, 60 42, 65 40 Z M40 30 C38 28, 34 29, 35 33 C36 37, 40 38, 42 36 C44 34, 43 31, 40 30 Z M60 30 C62 28, 66 29, 65 33 C64 37, 60 38, 58 36 C56 34, 57 31, 60 30 Z"/>
                </svg>
                PetGuard
            </a>
            <nav class="nav-links">
                <a href="/petguard/public/index.php">Inicio</a>
                <a href="/petguard/public/catalogo.php" style="color: var(--accent);">Catálogo</a>
                <a href="/petguard/public/index.php#como-funciona">Cómo funciona</a>
            </nav>
            <div class="nav-buttons">
                <a href="/petguard/public/login.php" class="btn btn-secondary">Iniciar Sesión</a>
                <a href="/petguard/public/register.php" class="btn btn-primary">Registrarse</a>
            </div>
        </div>
    </header>

    <!-- Hero del Catálogo -->
    <section style="padding: 140px 32px 40px; background: linear-gradient(135deg, var(--bg-secondary) 0%, var(--bg-primary) 100%); text-align: center;">
        <div style="max-width: 800px; margin: 0 auto;">
            <div style="display: inline-flex; align-items: center; gap: 8px; padding: 8px 16px; background: var(--accent-light); border: 1px solid rgba(255, 107, 74, 0.2); border-radius: 100px; font-size: 14px; font-weight: 600; color: var(--accent); margin-bottom: 24px;">
                <span style="width: 8px; height: 8px; background: var(--accent); border-radius: 50%; animation: pulse 2s infinite;"></span>
                <?php echo number_format($totalMascotas); ?> mascotas buscando hogar
            </div>
            <h1 style="font-size: 48px; font-weight: 800; color: var(--text-primary); margin-bottom: 16px; letter-spacing: -2px;">
                Encuentra a tu <span style="background: var(--accent-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">mejor amigo</span>
            </h1>
            <p style="font-size: 18px; color: var(--text-secondary); max-width: 600px; margin: 0 auto;">
                Todas nuestras mascotas están vacunadas, esterilizadas y listas para recibir todo tu amor.
            </p>
        </div>
    </section>

    <!-- Grid de Mascotas -->
    <section style="padding: 40px 32px 120px;">
        <div style="max-width: 1280px; margin: 0 auto;">
            
            <?php if (empty($mascotas)): ?>
                <div style="text-align: center; padding: 100px 20px; background: var(--bg-primary); border-radius: var(--radius-xl); border: 1px dashed var(--border-color);">
                    <div style="font-size: 64px; margin-bottom: 24px;">🐾</div>
                    <h3 style="font-size: 24px; font-weight: 700; color: var(--text-primary); margin-bottom: 12px;">
                        No hay mascotas disponibles en este momento
                    </h3>
                    <p style="color: var(--text-secondary); font-size: 16px; margin-bottom: 32px;">
                        Estamos trabajando para traer nuevos amigos peludos muy pronto.
                    </p>
                    <a href="/petguard/public/index.php" class="btn btn-primary">Volver al inicio</a>
                </div>
            <?php else: ?>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 32px;">
                    <?php foreach ($mascotas as $mascota): 
                        $generoLabel = $mascota['genero'] === 'M' ? 'Macho' : ($mascota['genero'] === 'F' ? 'Hembra' : 'No definido');
                        $edadLabel = $mascota['edad_aprox_meses'] ? $mascota['edad_aprox_meses'] . ' meses' : 'Edad desconocida';
                        $razaDisplay = $mascota['raza_nombre'] ? $mascota['raza_nombre'] : ($mascota['especie_nombre'] ?: 'Mestizo');
                        
                        // Lógica de ruta de imagen robusta
                        $foto = trim($mascota['foto_principal'] ?? '');
                        if (!empty($foto)) {
                            $imagenUrl = (strpos($foto, 'http') === 0 || strpos($foto, '/') === 0) ? $foto : BASE_URL . '/uploads/mascotas/' . $foto;
                        } else {
                            $imagenUrl = $fallbackImage;
                        }
                    ?>
                    <div class="pet-card-catalogo">
                        <div class="pet-card-image-catalogo">
                            <img src="<?php echo htmlspecialchars($imagenUrl); ?>" 
                                 alt="<?php echo htmlspecialchars($mascota['nombre']); ?>"
                                 onerror="this.src='<?php echo $fallbackImage; ?>'">
                            
                            <?php if ($mascota['urgente'] == 1): ?>
                                <span class="badge-urgente-catalogo">¡Urgente!</span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="pet-card-body-catalogo">
                            <div class="pet-card-header">
                                <h3><?php echo htmlspecialchars($mascota['nombre']); ?></h3>
                                <span><?php echo htmlspecialchars($razaDisplay); ?></span>
                            </div>
                            
                            <div class="pet-card-info">
                                <div class="info-item">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                    <span><?php echo $edadLabel; ?></span>
                                </div>
                                <div class="info-item">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                                    <span><?php echo $generoLabel; ?></span>
                                </div>
                                <?php if ($mascota['peso_kg']): ?>
                                <div class="info-item">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path><line x1="7" y1="7" x2="7.01" y2="7"></line></svg>
                                    <span><?php echo $mascota['peso_kg']; ?> kg</span>
                                </div>
                                <?php endif; ?>
                            </div>
                            
                            <?php if (!empty($mascota['descripcion'])): ?>
                                <p class="pet-card-desc"><?php echo htmlspecialchars(substr($mascota['descripcion'], 0, 100)) . '...'; ?></p>
                            <?php endif; ?>
                            
                            <div class="card-actions">
                                <a href="/petguard/public/login.php?redirect=adoptante" class="btn btn-primary" style="flex: 1; font-size: 14px;">
                                    Solicitar Adopción
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Footer -->
    <footer class="landing-footer">
        <div class="footer-content">
            <div class="footer-logo">
                <svg width="32" height="32" viewBox="0 0 100 100" fill="var(--accent)">
                    <path d="M50 50 C45 45, 40 48, 42 53 C44 58, 50 60, 55 58 C60 56, 62 50, 58 46 C54 42, 48 44, 50 50 Z"/>
                </svg>
                PetGuard
            </div>
            <div class="footer-links">
                <a href="/petguard/public/index.php">Inicio</a>
                <a href="/petguard/public/catalogo.php">Catálogo</a>
                <a href="/petguard/public/login.php">Iniciar Sesión</a>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> PetGuard. Todos los derechos reservados.</p>
        </div>
    </footer>

</body>
</html>