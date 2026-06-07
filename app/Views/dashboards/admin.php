<?php
/**
 * ============================================================
 * PETGUARD - Vista del Dashboard de Administración
 * ============================================================
 */

if (!isset($data)) {
    $data = [
        'stats' => ['total_usuarios' => 0, 'voluntarios_pendientes' => 0, 'veterinarios_pendientes' => 0, 'mascotas_disponibles' => 0],
        'pending_volunteers' => [], 'pending_vets' => [], 'mascotas' => [], 'usuarios' => [], 'roles' => [],
        'especies' => [], 'razas' => [],
        'search' => '', 'rol_filter' => 0, 'active_section' => 'dashboard',
        'search_mascotas' => '', 'especie_filter' => 0, 'estatus_filter' => '', 'urgente_filter' => '',
        'pagination_users' => ['current_page' => 1, 'total_pages' => 1, 'total_users' => 0, 'limit' => 10],
        'pagination_mascotas' => ['current_page' => 1, 'total_pages' => 1, 'total_mascotas' => 0, 'limit' => 10]
    ];
}

function getEspecieIcon($icono) {
    $map = [
        '🐕' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 5.172C10 3.782 8.423 2.679 6.5 3c-2.823.47-4.113 6.006-4 7 .08.703 1.725 1.722 3.656 1 1.261-.472 1.96-1.45 2.344-2.5M14.267 5.172c0-1.39 1.577-2.493 3.5-2.172 2.823.47 4.113 6.006 4 7-.08.703-1.725 1.722-3.656 1-1.261-.472-1.855-1.45-2.239-2.5M8 14v.5M16 14v.5M11.25 16.25h1.5L12 17l-.75-.75zM4.42 11.247A13.152 13.152 0 004 14.5c0 2.07.84 3.5 2.5 3.5h11c1.66 0 2.5-1.43 2.5-3.5 0-1.07-.14-2.27-.42-3.253M9 10a2 2 0 11-4 0 2 2 0 014 0zm10 0a2 2 0 11-4 0 2 2 0 014 0z"/></svg>',
        '🐈' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5c.67 0 1.35.09 2 .26 1.78-2 5.03-2.84 6.42-2.26 1.4.58-.42 7-.42 7 .57 1.07 1 2.24 1 3.44C21 17.9 16.97 21 12 21s-9-3-9-7.56c0-1.25.5-2.4 1-3.44 0 0-1.89-6.42-.5-7 1.39-.58 4.72.23 6.5 2.23A9.04 9.04 0 0112 5z"/></svg>',
        '🐰' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M13 16a3 3 0 012.24 5M18 12h.01M12 20h4M6 12a6 6 0 0112 0c0 3.09-1.5 5.5-3 7H9c-1.5-1.5-3-3.91-3-7z"/><path d="M8 8V5a2 2 0 014 0v3M16 8V5a2 2 0 00-4 0v3"/></svg>',
        '🐦' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 7h.01M3.4 18H12a8 8 0 008-8V7a4 4 0 00-7.28-2.3L2 20M20 7l-4 4M16 3l-4 4"/></svg>',
        '🐢' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 10v4M8 12h8M12 6a6 6 0 016 6v2H6v-2a6 6 0 016-6z"/><path d="M4 18h16M6 18v2M18 18v2"/></svg>',
        '' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M8 14s1.5 2 4 2 4-2 4-2M9 9h.01M15 9h.01"/></svg>'
    ];
    return $map[$icono] ?? $map[''];
}

$iconos = [
    'camera' => '<svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M23 19a2 2 0 01-2 2H3a2 2 0 01-2-2V8a2 2 0 012-2h4l2-3h6l2 3h4a2 2 0 012 2z"/><circle cx="12" cy="13" r="4"/></svg>',
    'close' => '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>',
    'syringe' => '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 2l4 4M7.5 20.5l-4 4M15 5l4 4M2 22l4-4M17 7l-9.5 9.5M9.5 14.5L14.5 19.5"/></svg>',
    'scissors' => '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="6" cy="6" r="3"/><circle cx="6" cy="18" r="3"/><line x1="20" y1="4" x2="8.12" y2="15.88"/><line x1="14.47" y1="14.48" x2="20" y2="20"/><line x1="8.12" y1="8.12" x2="12" y2="12"/></svg>',
    'chip' => '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="4" width="16" height="16" rx="2"/><rect x="9" y="9" width="6" height="6"/><line x1="9" y1="1" x2="9" y2="4"/><line x1="15" y1="1" x2="15" y2="4"/><line x1="9" y1="20" x2="9" y2="23"/><line x1="15" y1="20" x2="15" y2="23"/><line x1="20" y1="9" x2="23" y2="9"/><line x1="20" y1="14" x2="23" y2="14"/><line x1="1" y1="9" x2="4" y2="9"/><line x1="1" y1="14" x2="4" y2="14"/></svg>'
];

$estatusLabels = [
    'rescatado' => 'Rescatado',
    'en_albergue' => 'En Albergue',
    'en_adopcion' => 'En Adopción',
    'proceso_adopcion' => 'En Proceso',
    'adoptado' => 'Adoptado',
    'fallecido' => 'Fallecido',
    'extraviado' => 'Extraviado'
];
?>

<!-- Flash Messages -->
<?php if (isset($_SESSION['flash'])): ?>
    <?php foreach ($_SESSION['flash'] as $type => $message): ?>
        <div class="flash-message flash-<?php echo htmlspecialchars($type); ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endforeach; ?>
    <?php unset($_SESSION['flash']); ?>
<?php endif; ?>

<!-- SECCIÓN: DASHBOARD -->
<div id="section-dashboard" class="content-section <?php echo ($data['active_section'] ?? 'dashboard') === 'dashboard' ? 'active' : ''; ?>">
    <div class="dashboard-header">
        <h1>Dashboard</h1>
        <p>Resumen general del sistema PetGuard en tiempo real</p>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon">
                    <svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle></svg>
                </div>
            </div>
            <div class="stat-value"><?php echo number_format($data['stats']['total_usuarios'] ?? 0); ?></div>
            <div class="stat-label">Usuarios Registrados</div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon">
                    <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><path d="M8 14s1.5 2 4 2 4-2 4-2"></path><line x1="9" y1="9" x2="9.01" y2="9"></line><line x1="15" y1="9" x2="15.01" y2="9"></line></svg>
                </div>
            </div>
            <div class="stat-value"><?php echo number_format($data['stats']['mascotas_disponibles'] ?? 0); ?></div>
            <div class="stat-label">Mascotas en Sistema</div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon">
                    <svg viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>
                </div>
            </div>
            <div class="stat-value"><?php echo $data['stats']['voluntarios_pendientes'] ?? 0; ?></div>
            <div class="stat-label">Voluntarios Pendientes</div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon">
                    <svg viewBox="0 0 24 24"><path d="M22 12h-4l-3 9L9 3l-3 9H2"></path></svg>
                </div>
            </div>
            <div class="stat-value"><?php echo $data['stats']['veterinarios_pendientes'] ?? 0; ?></div>
            <div class="stat-label">Veterinarios Pendientes</div>
        </div>
    </div>

    <h2 class="section-title">Últimos Registros</h2>
    <div class="data-table">
        <div class="data-table-header">
            <div>Usuario</div>
            <div>Rol</div>
            <div>Fecha</div>
            <div>Estado</div>
        </div>
        <?php 
        $recentUsers = [];
        if (($data['stats']['total_usuarios'] ?? 0) > 0) {
            try {
                $recentUsers = getDB()->query("SELECT nombre, apellido_paterno, email, rol_id, created_at FROM usuarios ORDER BY id DESC LIMIT 5")->fetchAll();
            } catch (Exception $e) { }
        }
        
        foreach ($recentUsers as $user): 
            $roles = [1 => 'Admin', 2 => 'Voluntario', 3 => 'Veterinario', 4 => 'Adoptante', 5 => 'Sistemas'];
            $initials = strtoupper(substr($user['nombre'], 0, 1) . substr($user['apellido_paterno'], 0, 1));
        ?>
        <div class="data-table-row">
            <div class="user-cell">
                <div class="user-avatar-small"><?php echo $initials; ?></div>
                <div>
                    <div class="user-name"><?php echo htmlspecialchars($user['nombre'] . ' ' . $user['apellido_paterno']); ?></div>
                    <div class="user-email"><?php echo htmlspecialchars($user['email']); ?></div>
                </div>
            </div>
            <div><?php echo $roles[$user['rol_id']] ?? 'Desconocido'; ?></div>
            <div><?php echo date('d M Y', strtotime($user['created_at'])); ?></div>
            <div><span class="status-badge status-active">Activo</span></div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- SECCIÓN: USUARIOS -->
<div id="section-usuarios" class="content-section <?php echo ($data['active_section'] ?? '') === 'usuarios' ? 'active' : ''; ?>">
    <div class="dashboard-header">
        <h1>Gestión de Usuarios</h1>
        <p>Administrar, buscar y controlar accesos del sistema</p>
    </div>

    <div class="crud-form-container">
        <!-- FORMULARIO RESTAURADO CON BOTÓN FILTRAR -->
        <form method="GET" class="crud-form filter-form" id="users-filter-form">
            <input type="hidden" name="section" value="usuarios">
            <div class="form-group search-group">
                <label class="form-label">Buscar por nombre o email</label>
                <input type="text" name="search" class="form-input" value="<?php echo htmlspecialchars($data['search'] ?? ''); ?>" placeholder="Escribe para buscar...">
            </div>
            <div class="form-group filter-group">
                <label class="form-label">Filtrar por rol</label>
                <select name="rol" class="form-select">
                    <option value="0">Todos los roles</option>
                    <?php foreach ($data['roles'] as $rol): ?>
                        <option value="<?php echo $rol['id']; ?>" <?php echo ($data['rol_filter'] ?? 0) == $rol['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($rol['nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="filter-buttons">
                <button type="submit" class="btn-primary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 6px;"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    Filtrar
                </button>
                <a href="/petguard/public/admin.php?section=usuarios" class="btn-secondary">Limpiar</a>
            </div>
        </form>
    </div>

    <div style="margin-bottom: 20px;">
        <button type="button" class="btn-primary" onclick="openModal('modal-create-user')">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 6px;"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            Nuevo Usuario
        </button>
    </div>

    <h2 class="section-title">Usuarios Registrados (<?php echo count($data['usuarios'] ?? []); ?>)</h2>
    <div class="table-responsive">
        <div class="data-table">
            <div class="data-table-header users-table-header">
                <div class="col-user">Usuario</div>
                <div class="col-role">Rol</div>
                <div class="col-status">Estado</div>
                <div class="col-actions">Acciones</div>
            </div>
            <?php if (empty($data['usuarios'])): ?>
                <div style="padding: 40px; text-align: center; color: var(--text-secondary); font-weight: 600;">
                    No se encontraron usuarios con los filtros aplicados.
                </div>
            <?php else: ?>
                <?php foreach ($data['usuarios'] as $u): 
                    $initials = strtoupper(substr($u['nombre'], 0, 1) . substr($u['apellido_paterno'], 0, 1));
                    $isCurrentUser = ($u['id'] == ($_SESSION['user_id'] ?? 0));
                    $isSistemas = ($u['rol_id'] == 5);
                ?>
                <div class="data-table-row users-table-row">
                    <div class="col-user">
                        <div class="user-cell">
                            <div class="user-avatar-small">
                                <?php if (!empty($u['foto_url'])): ?>
                                    <img src="<?php echo htmlspecialchars($u['foto_url']); ?>" alt="Avatar" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
                                <?php else: ?>
                                    <?php echo $initials; ?>
                                <?php endif; ?>
                            </div>
                            <div>
                                <div class="user-name">
                                    <?php echo htmlspecialchars($u['nombre'] . ' ' . $u['apellido_paterno']); ?>
                                    <?php if ($isCurrentUser): ?>
                                        <span class="current-user-badge">(Tú)</span>
                                    <?php endif; ?>
                                    <?php if ($isSistemas): ?>
                                        <span class="current-user-badge" style="color: #8b5cf6;">(Sistemas)</span>
                                    <?php endif; ?>
                                </div>
                                <div class="user-email"><?php echo htmlspecialchars($u['email']); ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-role">
                        <form method="POST" class="role-change-form">
                            <input type="hidden" name="action" value="change_user_role">
                            <input type="hidden" name="id" value="<?php echo $u['id']; ?>">
                            <input type="hidden" name="section" value="usuarios">
                            <select name="rol_id" class="form-select role-select" <?php echo $isSistemas ? 'disabled' : ''; ?> onchange="if(confirm('¿Cambiar rol de <?php echo htmlspecialchars($u['nombre']); ?>?')) this.form.submit();">
                                <?php foreach ($data['roles'] as $rol): ?>
                                    <option value="<?php echo $rol['id']; ?>" <?php echo $u['rol_id'] == $rol['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($rol['nombre']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </form>
                    </div>
                    <div class="col-status">
                        <?php if ($u['bloqueado'] == 1): ?>
                            <span class="status-badge status-blocked">Bloqueado</span>
                        <?php else: ?>
                            <span class="status-badge status-active">Activo</span>
                        <?php endif; ?>
                    </div>
                    <div class="col-actions">
                        <?php if ($isSistemas): ?>
                            <div style="font-size: 11px; color: var(--text-secondary); font-style: italic;">
                                Solo Sistemas puede gestionar esta cuenta
                            </div>
                        <?php else: ?>
                            <div class="action-buttons">
                                <button type="button" class="action-btn action-edit" onclick='openEditModal(<?php echo json_encode($u); ?>)' title="Editar usuario">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                    Editar
                                </button>
                                
                                <?php if ($u['bloqueado'] == 1): ?>
                                    <form method="POST" class="action-form">
                                        <input type="hidden" name="action" value="unblock_user">
                                        <input type="hidden" name="id" value="<?php echo $u['id']; ?>">
                                        <input type="hidden" name="section" value="usuarios">
                                        <button type="submit" class="action-btn action-approve" title="Desbloquear usuario">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"></path></svg>
                                            Desbloquear
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <form method="POST" class="action-form">
                                        <input type="hidden" name="action" value="block_user">
                                        <input type="hidden" name="id" value="<?php echo $u['id']; ?>">
                                        <input type="hidden" name="section" value="usuarios">
                                        <input type="hidden" name="motivo_bloqueo" value="Bloqueado por administrador">
                                        <button type="submit" class="action-btn action-reject" <?php echo $isCurrentUser ? 'disabled' : ''; ?> title="Bloquear usuario">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                                            Bloquear
                                        </button>
                                    </form>
                                <?php endif; ?>
                                
                                <?php if (!$isCurrentUser): ?>
                                    <form method="POST" class="action-form" onsubmit="return confirm('¿Desactivar a <?php echo htmlspecialchars($u['nombre']); ?>?');">
                                        <input type="hidden" name="action" value="deactivate_user">
                                        <input type="hidden" name="id" value="<?php echo $u['id']; ?>">
                                        <input type="hidden" name="section" value="usuarios">
                                        <button type="submit" class="action-btn action-delete" title="Eliminar usuario">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                            Eliminar
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    <div id="pagination-container" class="pagination-container"></div>
</div>

<!-- MODAL: EDITAR USUARIO -->
<div id="modal-edit-user" class="modal-overlay">
    <div class="modal-container">
        <div class="modal-header">
            <h2>Editar Usuario</h2>
            <button type="button" class="modal-close" onclick="closeModal('modal-edit-user')"><?php echo $iconos['close']; ?></button>
        </div>
        <form method="POST" class="modal-form">
            <input type="hidden" name="action" value="edit_user">
            <input type="hidden" name="id" id="edit-user-id">
            <input type="hidden" name="section" value="usuarios">
            
            <div class="modal-body">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Nombre *</label>
                        <input type="text" name="nombre" id="edit-nombre" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Apellido Paterno *</label>
                        <input type="text" name="apellido_paterno" id="edit-apellido-paterno" class="form-input" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Apellido Materno</label>
                        <input type="text" name="apellido_materno" id="edit-apellido-materno" class="form-input">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Teléfono</label>
                        <input type="tel" name="telefono" id="edit-telefono" class="form-input">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Correo Electrónico *</label>
                    <input type="email" name="email" id="edit-email" class="form-input" required>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeModal('modal-edit-user')">Cancelar</button>
                <button type="submit" class="btn-primary">Guardar Cambios</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL: CREAR USUARIO -->
<div id="modal-create-user" class="modal-overlay">
    <div class="modal-container">
        <div class="modal-header">
            <h2>Nuevo Usuario</h2>
            <button type="button" class="modal-close" onclick="closeModal('modal-create-user')"><?php echo $iconos['close']; ?></button>
        </div>
        <form method="POST" class="modal-form">
            <input type="hidden" name="action" value="create_user">
            <input type="hidden" name="section" value="usuarios">
            
            <div class="modal-body">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Rol *</label>
                        <select name="rol_id" class="form-select" required>
                            <?php foreach ($data['roles'] as $rol): ?>
                                <?php if ($rol['id'] != 5): ?>
                                    <option value="<?php echo $rol['id']; ?>">
                                        <?php echo htmlspecialchars($rol['nombre']); ?>
                                    </option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nombre *</label>
                        <input type="text" name="nombre" class="form-input" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Apellido Paterno *</label>
                        <input type="text" name="apellido_paterno" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Apellido Materno</label>
                        <input type="text" name="apellido_materno" class="form-input">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Correo Electrónico *</label>
                        <input type="email" name="email" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Teléfono</label>
                        <input type="tel" name="telefono" class="form-input">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Contraseña *</label>
                    <input type="password" name="password" class="form-input" required minlength="8" placeholder="Mínimo 8 caracteres">
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeModal('modal-create-user')">Cancelar</button>
                <button type="submit" class="btn-primary">Crear Usuario</button>
            </div>
        </form>
    </div>
</div>

<!-- SECCIÓN: MASCOTAS -->
<div id="section-mascotas" class="content-section <?php echo ($data['active_section'] ?? '') === 'mascotas' ? 'active' : ''; ?>">
    <div class="dashboard-header">
        <h1>Gestión de Mascotas</h1>
        <p>Administrar el catálogo de mascotas para adopción</p>
    </div>

    <!-- Filtros AJAX (sin form GET, el JS lo intercepta) -->
    <div class="crud-form-container">
        <div class="crud-form filter-form" id="mascotas-filter-form">
            <div class="form-group search-group">
                <label class="form-label">Buscar por nombre o código (búsqueda en tiempo real)</label>
                <input type="text" name="search_mascotas" class="form-input" value="<?php echo htmlspecialchars($data['search_mascotas'] ?? ''); ?>" placeholder="Escribe para buscar...">
            </div>
            <div class="form-group filter-group">
                <label class="form-label">Especie</label>
                <select name="especie" class="form-select">
                    <option value="0">Todas las especies</option>
                    <?php foreach ($data['especies'] as $esp): ?>
                        <option value="<?php echo $esp['id']; ?>" <?php echo ($data['especie_filter'] ?? 0) == $esp['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($esp['nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group filter-group">
                <label class="form-label">Estatus</label>
                <select name="estatus" class="form-select">
                    <option value="">Todos los estatus</option>
                    <?php foreach ($estatusLabels as $key => $label): ?>
                        <option value="<?php echo $key; ?>" <?php echo ($data['estatus_filter'] ?? '') === $key ? 'selected' : ''; ?>>
                            <?php echo $label; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group filter-group">
                <label class="form-label">Urgente</label>
                <select name="urgente" class="form-select">
                    <option value="">Todos</option>
                    <option value="1" <?php echo ($data['urgente_filter'] ?? '') === '1' ? 'selected' : ''; ?>>Solo urgentes</option>
                    <option value="0" <?php echo ($data['urgente_filter'] ?? '') === '0' ? 'selected' : ''; ?>>No urgentes</option>
                </select>
            </div>
        </div>
    </div>

    <div style="margin-bottom: 20px;">
        <button type="button" class="btn-primary" onclick="openModal('modal-create-mascota')">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 6px;"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            Nueva Mascota
        </button>
    </div>

    <h2 class="section-title">Mascotas Registradas (<?php echo count($data['mascotas'] ?? []); ?>)</h2>
    <div class="table-responsive">
        <div class="data-table">
            <div class="data-table-header">
                <div class="col-mascota">Mascota</div>
                <div class="col-detalles">Detalles</div>
                <div class="col-estatus">Estatus</div>
                <div class="col-actions">Acciones</div>
            </div>
            <?php if (empty($data['mascotas'])): ?>
                <div style="padding: 40px; text-align: center; color: var(--text-secondary); font-weight: 600;">
                    No se encontraron mascotas con los filtros aplicados.
                </div>
            <?php else: ?>
                <?php foreach ($data['mascotas'] as $m): 
                    $generoLabel = $m['genero'] === 'M' ? 'Macho' : ($m['genero'] === 'F' ? 'Hembra' : 'ND');
                ?>
                <div class="data-table-row">
                    <div class="col-mascota">
                        <div class="user-cell">
                            <div class="user-avatar-small" style="font-size: 20px; display: flex; align-items: center; justify-content: center;">
                                <?php if (!empty($m['foto_principal'])): ?>
                                    <img src="<?php echo htmlspecialchars($m['foto_principal']); ?>" alt="<?php echo htmlspecialchars($m['nombre']); ?>" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
                                <?php else: ?>
                                    <?php echo getEspecieIcon($m['especie_icono'] ?? ''); ?>
                                <?php endif; ?>
                            </div>
                            <div>
                                <div class="user-name">
                                    <?php echo htmlspecialchars($m['nombre']); ?>
                                    <?php if ($m['urgente'] == 1): ?>
                                        <span class="current-user-badge" style="color: #ef4444; display: inline-flex; align-items: center; gap: 4px;">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                                            URGENTE
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <div class="user-email">
                                    <?php echo htmlspecialchars($m['nombre_interno'] ?: 'Sin código'); ?> • 
                                    <?php echo htmlspecialchars($m['especie_nombre'] ?: 'N/A'); ?>
                                    <?php if (!empty($m['raza_nombre'])): ?>
                                        - <?php echo htmlspecialchars($m['raza_nombre']); ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-detalles">
                        <div style="font-size: 12px; color: var(--text-secondary); display: flex; align-items: center; gap: 6px; flex-wrap: wrap;">
                            <span><?php echo $m['edad_aprox_meses'] ? $m['edad_aprox_meses'] . ' meses' : '?'; ?></span>
                            <span>•</span>
                            <span><?php echo $generoLabel; ?></span>
                            <span>•</span>
                            <span><?php echo $m['peso_kg'] ? $m['peso_kg'] . 'kg' : '?'; ?></span>
                            <?php if ($m['vacunado'] == 1): ?>
                                <span title="Vacunado" style="color: #10b981;"><?php echo $iconos['syringe']; ?></span>
                            <?php endif; ?>
                            <?php if ($m['esterilizado'] == 1): ?>
                                <span title="Esterilizado" style="color: #3b82f6;"><?php echo $iconos['scissors']; ?></span>
                            <?php endif; ?>
                            <?php if (!empty($m['microchip'])): ?>
                                <span title="Microchip: <?php echo htmlspecialchars($m['microchip']); ?>" style="color: #8b5cf6;"><?php echo $iconos['chip']; ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-estatus">
                        <form method="POST" class="action-form">
                            <input type="hidden" name="action" value="change_mascota_status">
                            <input type="hidden" name="id" value="<?php echo $m['id']; ?>">
                            <input type="hidden" name="section" value="mascotas">
                            <select name="estatus" class="form-select role-select" onchange="if(confirm('¿Cambiar estatus?')) this.form.submit();">
                                <?php foreach ($estatusLabels as $key => $label): ?>
                                    <option value="<?php echo $key; ?>" <?php echo $m['estatus'] == $key ? 'selected' : ''; ?>>
                                        <?php echo $label; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </form>
                    </div>
                    <div class="col-actions">
                        <div class="action-buttons">
                            <button type="button" class="action-btn action-edit" onclick='window.openEditMascotaModal(<?php echo json_encode($m); ?>)' title="Editar mascota">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                Editar
                            </button>
                            
                            <form method="POST" class="action-form">
                                <input type="hidden" name="action" value="toggle_urgente">
                                <input type="hidden" name="id" value="<?php echo $m['id']; ?>">
                                <input type="hidden" name="section" value="mascotas">
                                <button type="submit" class="action-btn <?php echo $m['urgente'] == 1 ? 'action-reject' : 'action-approve'; ?>" title="<?php echo $m['urgente'] == 1 ? 'Quitar urgencia' : 'Marcar urgente'; ?>">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                                    <?php echo $m['urgente'] == 1 ? 'Urgente' : 'Normal'; ?>
                                </button>
                            </form>
                            
                            <form method="POST" class="action-form" onsubmit="return confirm('¿Eliminar esta mascota?');">
                                <input type="hidden" name="action" value="deactivate_mascota">
                                <input type="hidden" name="id" value="<?php echo $m['id']; ?>">
                                <input type="hidden" name="section" value="mascotas">
                                <button type="submit" class="action-btn action-delete" title="Eliminar mascota">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                    Eliminar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    <div id="pagination-mascotas-container" class="pagination-container"></div>
</div>

<!-- MODAL: CREAR MASCOTA -->
<div id="modal-create-mascota" class="modal-overlay">
    <div class="modal-container" style="max-width: 800px;">
        <div class="modal-header">
            <h2>Nueva Mascota</h2>
            <button type="button" class="modal-close" onclick="closeModal('modal-create-mascota')"><?php echo $iconos['close']; ?></button>
        </div>
        <form method="POST" class="modal-form" enctype="multipart/form-data">
            <input type="hidden" name="action" value="create_mascota">
            <input type="hidden" name="section" value="mascotas">
            
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Foto de la Mascota</label>
                    <div class="image-upload-zone" id="create-foto-zone">
                        <input type="file" name="foto_file" id="create-foto-file" accept="image/jpeg,image/jpg,image/png,image/webp">
                        <?php echo $iconos['camera']; ?>
                        <p class="upload-text-main">Arrastra la foto aquí o <span class="upload-browse-link">haz clic para buscar</span></p>
                        <p class="upload-hint-text">JPG, PNG o WEBP • Máximo 5MB</p>
                        <p class="upload-file-info" id="create-foto-info"></p>
                        <p class="upload-error-message" id="create-foto-error"></p>
                        <div class="upload-preview-container" id="create-foto-preview">
                            <button type="button" class="upload-remove-btn"><?php echo $iconos['close']; ?> Quitar foto</button>
                        </div>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Nombre *</label>
                        <input type="text" name="nombre" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Código Interno</label>
                        <input type="text" name="nombre_interno" class="form-input" placeholder="Ej. A-001">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Especie *</label>
                        <select name="especie_id" class="form-select" required onchange="updateRazas(this.value, 'create-raza-id')">
                            <option value="">Seleccionar especie</option>
                            <?php foreach ($data['especies'] as $esp): ?>
                                <option value="<?php echo $esp['id']; ?>">
                                    <?php echo htmlspecialchars($esp['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Raza</label>
                        <select name="raza_id" id="create-raza-id" class="form-select">
                            <option value="">Seleccionar raza</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Género *</label>
                        <select name="genero" class="form-select" required>
                            <option value="">Seleccionar</option>
                            <option value="Macho">Macho</option>
                            <option value="Hembra">Hembra</option>
                            <option value="ND">No definido</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Edad (meses aprox)</label>
                        <input type="number" name="edad_aprox_meses" class="form-input" min="0" placeholder="12">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Color</label>
                        <input type="text" name="color" class="form-input" placeholder="Ej. Café con blanco">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Peso (kg)</label>
                        <input type="number" step="0.1" name="peso_kg" class="form-input" placeholder="10.5">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Estatus</label>
                        <select name="estatus" class="form-select">
                            <?php foreach ($estatusLabels as $key => $label): ?>
                                <option value="<?php echo $key; ?>" <?php echo $key === 'en_albergue' ? 'selected' : ''; ?>>
                                    <?php echo $label; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Ubicación</label>
                        <input type="text" name="ubicacion" class="form-input" placeholder="Albergue o zona">
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Número de Microchip</label>
                    <input type="text" name="microchip" class="form-input" placeholder="Ej. 985112345678901" maxlength="20">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Personalidad (Tags)</label>
                    <input type="text" name="personalidad" class="form-input" placeholder="amigable, tímido, juguetón...">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Descripción</label>
                    <textarea name="descripcion" class="form-input" rows="3"></textarea>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Enfermedades / Discapacidad</label>
                    <div class="form-row">
                        <div class="form-group">
                            <input type="text" name="enfermedades" class="form-input" placeholder="Enfermedades">
                        </div>
                        <div class="form-group">
                            <input type="text" name="discapacidad" class="form-input" placeholder="Discapacidad">
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Características</label>
                    <div style="display: flex; gap: 20px; flex-wrap: wrap;">
                        <label class="form-checkbox"><input type="checkbox" name="urgente"> Urgente</label>
                        <label class="form-checkbox"><input type="checkbox" name="esterilizado"> Esterilizado</label>
                        <label class="form-checkbox"><input type="checkbox" name="vacunado"> Vacunado</label>
                    </div>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeModal('modal-create-mascota')">Cancelar</button>
                <button type="submit" class="btn-primary">Registrar Mascota</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL: EDITAR MASCOTA -->
<div id="modal-edit-mascota" class="modal-overlay">
    <div class="modal-container" style="max-width: 800px;">
        <div class="modal-header">
            <h2>Editar Mascota</h2>
            <button type="button" class="modal-close" onclick="closeModal('modal-edit-mascota')"><?php echo $iconos['close']; ?></button>
        </div>
        <form method="POST" class="modal-form" enctype="multipart/form-data">
            <input type="hidden" name="action" value="edit_mascota">
            <input type="hidden" name="id" id="edit-mascota-id">
            <input type="hidden" name="section" value="mascotas">
            
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Foto de la Mascota</label>
                    <div class="image-upload-zone" id="edit-foto-zone">
                        <input type="file" name="foto_file" id="edit-foto-file" accept="image/jpeg,image/jpg,image/png,image/webp">
                        <?php echo $iconos['camera']; ?>
                        <p class="upload-text-main">Arrastra la foto aquí o <span class="upload-browse-link">haz clic para buscar</span></p>
                        <p class="upload-hint-text">JPG, PNG o WEBP • Máximo 5MB • Deja vacío para mantener la actual</p>
                        <p class="upload-file-info" id="edit-foto-info"></p>
                        <p class="upload-error-message" id="edit-foto-error"></p>
                        <div class="upload-preview-container" id="edit-foto-preview">
                            <button type="button" class="upload-remove-btn"><?php echo $iconos['close']; ?> Quitar foto</button>
                        </div>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Nombre *</label>
                        <input type="text" name="nombre" id="edit-mascota-nombre" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Código Interno</label>
                        <input type="text" name="nombre_interno" id="edit-mascota-nombre-interno" class="form-input">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Especie *</label>
                        <select name="especie_id" id="edit-mascota-especie" class="form-select" required onchange="updateRazas(this.value, 'edit-raza-id')">
                            <option value="">Seleccionar especie</option>
                            <?php foreach ($data['especies'] as $esp): ?>
                                <option value="<?php echo $esp['id']; ?>">
                                    <?php echo htmlspecialchars($esp['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Raza</label>
                        <select name="raza_id" id="edit-raza-id" class="form-select">
                            <option value="">Seleccionar raza</option>
                            <?php foreach ($data['razas'] as $raza): ?>
                                <option value="<?php echo $raza['id']; ?>" data-especie="<?php echo $raza['especie_id']; ?>">
                                    <?php echo htmlspecialchars($raza['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Género *</label>
                        <select name="genero" id="edit-mascota-genero" class="form-select" required>
                            <option value="">Seleccionar</option>
                            <option value="Macho">Macho</option>
                            <option value="Hembra">Hembra</option>
                            <option value="ND">No definido</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Edad (meses)</label>
                        <input type="number" name="edad_aprox_meses" id="edit-mascota-edad" class="form-input" min="0">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Color</label>
                        <input type="text" name="color" id="edit-mascota-color" class="form-input">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Peso (kg)</label>
                        <input type="number" step="0.1" name="peso_kg" id="edit-mascota-peso" class="form-input">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Estatus</label>
                        <select name="estatus" id="edit-mascota-estatus" class="form-select">
                            <?php foreach ($estatusLabels as $key => $label): ?>
                                <option value="<?php echo $key; ?>"><?php echo $label; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Ubicación</label>
                        <input type="text" name="ubicacion" id="edit-mascota-ubicacion" class="form-input">
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Número de Microchip</label>
                    <input type="text" name="microchip" id="edit-mascota-microchip" class="form-input" maxlength="20">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Personalidad</label>
                    <input type="text" name="personalidad" id="edit-mascota-personalidad" class="form-input">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Descripción</label>
                    <textarea name="descripcion" id="edit-mascota-descripcion" class="form-input" rows="3"></textarea>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Enfermedades / Discapacidad</label>
                    <div class="form-row">
                        <div class="form-group">
                            <input type="text" name="enfermedades" id="edit-mascota-enfermedades" class="form-input">
                        </div>
                        <div class="form-group">
                            <input type="text" name="discapacidad" id="edit-mascota-discapacidad" class="form-input">
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Características</label>
                    <div style="display: flex; gap: 20px; flex-wrap: wrap;">
                        <label class="form-checkbox"><input type="checkbox" name="urgente" id="edit-mascota-urgente"> Urgente</label>
                        <label class="form-checkbox"><input type="checkbox" name="esterilizado" id="edit-mascota-esterilizado"> Esterilizado</label>
                        <label class="form-checkbox"><input type="checkbox" name="vacunado" id="edit-mascota-vacunado"> Vacunado</label>
                    </div>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeModal('modal-edit-mascota')">Cancelar</button>
                <button type="submit" class="btn-primary">Guardar Cambios</button>
            </div>
        </form>
    </div>
</div>

<!-- SECCIÓN: VOLUNTARIOS -->
<div id="section-voluntarios" class="content-section <?php echo ($data['active_section'] ?? '') === 'voluntarios' ? 'active' : ''; ?>">
    <div class="dashboard-header">
        <h1>Aprobación de Voluntarios</h1>
        <p>Revisar y aprobar solicitudes de voluntarios</p>
    </div>

    <h2 class="section-title">Solicitudes Pendientes (<?php echo count($data['pending_volunteers'] ?? []); ?>)</h2>
    <?php if (empty($data['pending_volunteers'])): ?>
        <div class="flash-message flash-success" style="position:static; transform:none;">No hay voluntarios pendientes de aprobación.</div>
    <?php else: ?>
        <div class="data-table">
            <div class="data-table-header">
                <div>Voluntario</div>
                <div>Zona</div>
                <div>Fecha</div>
                <div>Acciones</div>
            </div>
            <?php foreach ($data['pending_volunteers'] as $vol): ?>
            <div class="data-table-row">
                <div class="user-cell">
                    <div class="user-avatar-small"><?php echo strtoupper(substr($vol['nombre'], 0, 1) . substr($vol['apellido_paterno'], 0, 1)); ?></div>
                    <div>
                        <div class="user-name"><?php echo htmlspecialchars($vol['nombre'] . ' ' . $vol['apellido_paterno']); ?></div>
                        <div class="user-email"><?php echo htmlspecialchars($vol['email']); ?></div>
                    </div>
                </div>
                <div><?php echo htmlspecialchars($vol['zona_cobertura'] ?: 'No especificada'); ?></div>
                <div><?php echo date('d M Y', strtotime($vol['fecha_alta'])); ?></div>
                <div class="action-buttons">
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="action" value="approve_volunteer">
                        <input type="hidden" name="id" value="<?php echo $vol['id']; ?>">
                        <button type="submit" class="action-btn approve">Aprobar</button>
                    </form>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="action" value="reject_user">
                        <input type="hidden" name="id" value="<?php echo $vol['id']; ?>">
                        <button type="submit" class="action-btn reject">Rechazar</button>
                    </form>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- SECCIÓN: VETERINARIOS -->
<div id="section-veterinarios" class="content-section <?php echo ($data['active_section'] ?? '') === 'veterinarios' ? 'active' : ''; ?>">
    <div class="dashboard-header">
        <h1>Verificación de Veterinarios</h1>
        <p>Revisar documentos y aprobar veterinarios</p>
    </div>

    <h2 class="section-title">Documentos Pendientes (<?php echo count($data['pending_vets'] ?? []); ?>)</h2>
    <?php if (empty($data['pending_vets'])): ?>
        <div class="flash-message flash-success" style="position:static; transform:none;">No hay veterinarios pendientes de verificación.</div>
    <?php else: ?>
        <div class="data-table">
            <div class="data-table-header">
                <div>Veterinario</div>
                <div>Cédula</div>
                <div>Especialidad</div>
                <div>Acciones</div>
            </div>
            <?php foreach ($data['pending_vets'] as $vet): ?>
            <div class="data-table-row">
                <div class="user-cell">
                    <div class="user-avatar-small"><?php echo strtoupper(substr($vet['nombre'], 0, 1) . substr($vet['apellido_paterno'], 0, 1)); ?></div>
                    <div>
                        <div class="user-name"><?php echo htmlspecialchars($vet['nombre'] . ' ' . $vet['apellido_paterno']); ?></div>
                        <div class="user-email"><?php echo htmlspecialchars($vet['email']); ?></div>
                    </div>
                </div>
                <div><?php echo htmlspecialchars($vet['cedula_prof'] ?: 'N/A'); ?></div>
                <div><?php echo htmlspecialchars($vet['especialidad'] ?: 'General'); ?></div>
                <div class="action-buttons">
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="action" value="approve_vet">
                        <input type="hidden" name="id" value="<?php echo $vet['id']; ?>">
                        <button type="submit" class="action-btn approve">Verificar</button>
                    </form>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="action" value="reject_user">
                        <input type="hidden" name="id" value="<?php echo $vet['id']; ?>">
                        <button type="submit" class="action-btn reject">Rechazar</button>
                    </form>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- SECCIÓN: DONACIONES -->
<div id="section-donaciones" class="content-section <?php echo ($data['active_section'] ?? '') === 'donaciones' ? 'active' : ''; ?>">
    <div class="dashboard-header"><h1>Reportes de Donaciones</h1><p>Módulo en construcción</p></div>
</div>

<script>
// Función para actualizar razas según especie seleccionada
function updateRazas(especieId, razaSelectId) {
    const razaSelect = document.getElementById(razaSelectId);
    if (!razaSelect) return;
    
    razaSelect.innerHTML = '<option value="">Seleccionar raza</option>';
    
    if (!especieId) return;
    
    const allRazas = <?php echo json_encode($data['razas']); ?>;
    const razasFiltradas = allRazas.filter(r => r.especie_id == especieId);
    
    razasFiltradas.forEach(raza => {
        const option = document.createElement('option');
        option.value = raza.id;
        option.textContent = raza.nombre;
        razaSelect.appendChild(option);
    });
}

// Función para abrir modal de editar usuario
function openEditModal(userData) {
    document.getElementById('edit-user-id').value = userData.id;
    document.getElementById('edit-nombre').value = userData.nombre;
    document.getElementById('edit-apellido-paterno').value = userData.apellido_paterno;
    document.getElementById('edit-apellido-materno').value = userData.apellido_materno || '';
    document.getElementById('edit-telefono').value = userData.telefono || '';
    document.getElementById('edit-email').value = userData.email;
    openModal('modal-edit-user');
}

// Función para abrir modal de editar mascota (Global)
window.openEditMascotaModal = function(mascota) {
    document.getElementById('edit-mascota-id').value = mascota.id;
    document.getElementById('edit-mascota-nombre').value = mascota.nombre;
    document.getElementById('edit-mascota-nombre-interno').value = mascota.nombre_interno || '';
    document.getElementById('edit-mascota-especie').value = mascota.especie_id;
    
    const generoMap = { 'M': 'Macho', 'F': 'Hembra', 'ND': 'ND' };
    document.getElementById('edit-mascota-genero').value = generoMap[mascota.genero] || 'ND';
    
    document.getElementById('edit-mascota-edad').value = mascota.edad_aprox_meses || '';
    document.getElementById('edit-mascota-color').value = mascota.color || '';
    document.getElementById('edit-mascota-peso').value = mascota.peso_kg || '';
    document.getElementById('edit-mascota-estatus').value = mascota.estatus;
    document.getElementById('edit-mascota-ubicacion').value = mascota.ubicacion || '';
    document.getElementById('edit-mascota-microchip').value = mascota.microchip || '';
    document.getElementById('edit-mascota-personalidad').value = mascota.personalidad || '';
    document.getElementById('edit-mascota-descripcion').value = mascota.descripcion || '';
    document.getElementById('edit-mascota-enfermedades').value = mascota.enfermedades || '';
    document.getElementById('edit-mascota-discapacidad').value = mascota.discapacidad || '';
    document.getElementById('edit-mascota-urgente').checked = mascota.urgente == 1;
    document.getElementById('edit-mascota-esterilizado').checked = mascota.esterilizado == 1;
    document.getElementById('edit-mascota-vacunado').checked = mascota.vacunado == 1;
    
    updateRazas(mascota.especie_id, 'edit-raza-id');
    
    setTimeout(() => {
        document.getElementById('edit-raza-id').value = mascota.raza_id || '';
    }, 100);
    
    if (mascota.foto_principal) {
        const previewContainer = document.getElementById('edit-foto-preview');
        if (previewContainer) {
            const existingImg = previewContainer.querySelector('img');
            if (existingImg) existingImg.remove();
            
            const img = document.createElement('img');
            img.src = mascota.foto_principal;
            img.className = 'upload-preview-image';
            previewContainer.insertBefore(img, previewContainer.firstChild);
            previewContainer.classList.add('active');
        }
    }
    
    openModal('modal-edit-mascota');
};
</script>