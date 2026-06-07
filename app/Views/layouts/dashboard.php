<?php
/**
 * ============================================================
 * PETGUARD - Layout del Dashboard de Administración
 * ============================================================
 */
if (!isset($data)) {
    $data = [
        'stats' => [
            'total_usuarios' => 0,
            'voluntarios_pendientes' => 0,
            'veterinarios_pendientes' => 0,
            'mascotas_disponibles' => 0
        ],
        'pending_volunteers' => [],
        'pending_vets' => [],
        'usuarios' => [],
        'roles' => [],
        'search' => '',
        'rol_filter' => 0,
        'active_section' => 'dashboard'
    ];
}

$activeSection = $data['active_section'] ?? 'dashboard';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin | PetGuard</title>
    <link rel="stylesheet" href="/petguard/public/css/dashboard.css?v=<?php echo time(); ?>">
</head>
<body class="dashboard-body">

    <!-- SIDEBAR IZQUIERDO -->
    <div class="sidebar-left" id="sidebar">
        <svg class="sidebar-logo" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
            <path d="M50 50 C45 45, 40 48, 42 53 C44 58, 50 60, 55 58 C60 56, 62 50, 58 46 C54 42, 48 44, 50 50 Z M35 40 C32 38, 28 40, 29 44 C30 48, 34 49, 37 47 C40 45, 40 42, 35 40 Z M65 40 C68 38, 72 40, 71 44 C70 48, 66 49, 63 47 C60 45, 60 42, 65 40 Z M40 30 C38 28, 34 29, 35 33 C36 37, 40 38, 42 36 C44 34, 43 31, 40 30 Z M60 30 C62 28, 66 29, 65 33 C64 37, 60 38, 58 36 C56 34, 57 31, 60 30 Z"/>
        </svg>

        <div class="sidebar-nav">
            <div class="sidebar-icon <?php echo $activeSection === 'dashboard' ? 'active' : ''; ?>" onclick="showSection('dashboard')">
                <svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                <span class="icon-label">Dashboard</span>
                <span class="tooltip">Dashboard</span>
            </div>
            <div class="sidebar-icon <?php echo $activeSection === 'usuarios' ? 'active' : ''; ?>" onclick="showSection('usuarios')">
                <svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                <span class="icon-label">Usuarios</span>
                <span class="tooltip">Usuarios</span>
            </div>
            <div class="sidebar-icon <?php echo $activeSection === 'voluntarios' ? 'active' : ''; ?>" onclick="showSection('voluntarios')">
                <svg viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>
                <span class="icon-label">Voluntarios</span>
                <span class="tooltip">Voluntarios</span>
            </div>
            <div class="sidebar-icon <?php echo $activeSection === 'veterinarios' ? 'active' : ''; ?>" onclick="showSection('veterinarios')">
                <svg viewBox="0 0 24 24"><path d="M22 12h-4l-3 9L9 3l-3 9H2"></path></svg>
                <span class="icon-label">Veterinarios</span>
                <span class="tooltip">Veterinarios</span>
            </div>
            <div class="sidebar-icon <?php echo $activeSection === 'mascotas' ? 'active' : ''; ?>" onclick="showSection('mascotas')">
                <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><path d="M8 14s1.5 2 4 2 4-2 4-2"></path><line x1="9" y1="9" x2="9.01" y2="9"></line><line x1="15" y1="9" x2="15.01" y2="9"></line></svg>
                <span class="icon-label">Mascotas</span>
                <span class="tooltip">Mascotas</span>
            </div>
            <div class="sidebar-icon <?php echo $activeSection === 'donaciones' ? 'active' : ''; ?>" onclick="showSection('donaciones')">
                <svg viewBox="0 0 24 24"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                <span class="icon-label">Donaciones</span>
                <span class="tooltip">Donaciones</span>
            </div>
        </div>

        <div class="sidebar-bottom">
            <div class="sidebar-toggle" onclick="toggleSidebar()">
                <svg viewBox="0 0 24 24"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
                <span class="toggle-label">Contraer</span>
            </div>
            <div class="sidebar-logout" onclick="logout()">
                <svg viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                <span class="logout-label">Cerrar Sesión</span>
            </div>
        </div>
    </div>

    <!-- PANEL MEDIO -->
    <div class="panel-middle">
        <div class="admin-profile">
            <div class="admin-avatar"><?php echo strtoupper(substr($_SESSION['user_name'] ?? 'A', 0, 1)); ?></div>
            <div class="admin-info">
                <h3><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Administrador'); ?></h3>
                <p><?php echo htmlspecialchars($_SESSION['user_email'] ?? 'admin@petguard.mx'); ?></p>
            </div>
        </div>

        <div class="nav-sections">
            <div class="nav-section-title">Gestión</div>
            <div class="nav-item <?php echo $activeSection === 'dashboard' ? 'active' : ''; ?>" onclick="showSection('dashboard')">
                <div class="nav-item-left">
                    <svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                    <span class="nav-item-label">Dashboard</span>
                </div>
            </div>
            <div class="nav-item <?php echo $activeSection === 'usuarios' ? 'active' : ''; ?>" onclick="showSection('usuarios')">
                <div class="nav-item-left">
                    <svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle></svg>
                    <span class="nav-item-label">Usuarios</span>
                </div>
            </div>
            <div class="nav-item <?php echo $activeSection === 'voluntarios' ? 'active' : ''; ?>" onclick="showSection('voluntarios')">
                <div class="nav-item-left">
                    <svg viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>
                    <span class="nav-item-label">Voluntarios</span>
                </div>
                <span class="nav-badge"><?php echo $data['stats']['voluntarios_pendientes'] ?? 0; ?></span>
            </div>
            <div class="nav-item <?php echo $activeSection === 'veterinarios' ? 'active' : ''; ?>" onclick="showSection('veterinarios')">
                <div class="nav-item-left">
                    <svg viewBox="0 0 24 24"><path d="M22 12h-4l-3 9L9 3l-3 9H2"></path></svg>
                    <span class="nav-item-label">Veterinarios</span>
                </div>
                <span class="nav-badge"><?php echo $data['stats']['veterinarios_pendientes'] ?? 0; ?></span>
            </div>

            <div class="nav-section-title">Mascotas</div>
            <div class="nav-item <?php echo $activeSection === 'mascotas' ? 'active' : ''; ?>" onclick="showSection('mascotas')">
                <div class="nav-item-left">
                    <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><path d="M8 14s1.5 2 4 2 4-2 4-2"></path><line x1="9" y1="9" x2="9.01" y2="9"></line><line x1="15" y1="9" x2="15.01" y2="9"></line></svg>
                    <span class="nav-item-label">Disponibles</span>
                </div>
                <span class="nav-badge"><?php echo $data['stats']['mascotas_disponibles'] ?? 0; ?></span>
            </div>

            <div class="nav-section-title">Reportes</div>
            <div class="nav-item <?php echo $activeSection === 'donaciones' ? 'active' : ''; ?>" onclick="showSection('donaciones')">
                <div class="nav-item-left">
                    <svg viewBox="0 0 24 24"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                    <span class="nav-item-label">Donaciones</span>
                </div>
            </div>
        </div>
    </div>

    <!-- ÁREA PRINCIPAL -->
    <div class="panel-main">
        <?php include APP_PATH . '/Views/dashboards/admin.php'; ?>
    </div>

    <!-- SCRIPTS -->
    <script src="/petguard/public/js/dashboard.js?v=<?php echo time(); ?>"></script>
    <script src="/petguard/public/js/admin-mascotas.js?v=<?php echo time(); ?>"></script>
</body>
</html>