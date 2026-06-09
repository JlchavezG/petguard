<?php
/**
 * ============================================================
 * PETGUARD - Layout del Dashboard del Adoptante (Vista MVC)
 * ============================================================
 */
if (!isset($data)) {
    $data = ['user' => null, 'mascotas' => [], 'solicitudes' => [], 'active_view' => 'catalogo'];
}

$user = $data['user'];
$userName = $user ? htmlspecialchars($user['nombre'] . ' ' . $user['apellido_paterno']) : 'Adoptante';
$userInitials = $user ? strtoupper(substr($user['nombre'], 0, 1) . substr($user['apellido_paterno'], 0, 1)) : 'A';
$activeView = $data['active_view'] ?? 'catalogo';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Mascotas | PetGuard</title>
    <link rel="stylesheet" href="/petguard/public/css/dashboard.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="/petguard/public/css/adoptante.css?v=<?php echo time(); ?>">
</head>
<body class="dashboard-body" style="flex-direction: column; height: auto; min-height: 100vh; overflow: auto;">

    <!-- Flash Messages -->
    <?php if (isset($_SESSION['flash'])): ?>
        <?php foreach ($_SESSION['flash'] as $type => $message): ?>
            <div class="flash-message flash-<?php echo htmlspecialchars($type); ?>" style="margin: 20px 40px 0;">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endforeach; ?>
        <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>

    <!-- Header del Adoptante -->
    <div class="adopter-header">
        <div>
            <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 12px;">
                <svg width="32" height="32" viewBox="0 0 100 100" fill="var(--accent-color)">
                    <path d="M50 50 C45 45, 40 48, 42 53 C44 58, 50 60, 55 58 C60 56, 62 50, 58 46 C54 42, 48 44, 50 50 Z M35 40 C32 38, 28 40, 29 44 C30 48, 34 49, 37 47 C40 45, 40 42, 35 40 Z M65 40 C68 38, 72 40, 71 44 C70 48, 66 49, 63 47 C60 45, 60 42, 65 40 Z M40 30 C38 28, 34 29, 35 33 C36 37, 40 38, 42 36 C44 34, 43 31, 40 30 Z M60 30 C62 28, 66 29, 65 33 C64 37, 60 38, 58 36 C56 34, 57 31, 60 30 Z"/>
                </svg>
                <h1>PetGuard</h1>
            </div>
            <div class="adopter-nav">
                <a href="/petguard/public/adoptante.php?view=catalogo" class="<?php echo $activeView === 'catalogo' ? 'active' : ''; ?>">🐾 Catálogo de Mascotas</a>
                <a href="/petguard/public/adoptante.php?view=solicitudes" class="<?php echo $activeView === 'solicitudes' ? 'active' : ''; ?>">📋 Mis Solicitudes</a>
            </div>
        </div>
        <div style="display: flex; align-items: center; gap: 20px;">
            <div style="text-align: right;">
                <div style="font-weight: 700; color: var(--text-primary); font-size: 14px;"><?php echo $userName; ?></div>
                <div style="font-size: 12px; color: var(--text-secondary);">Adoptante</div>
            </div>
            <div class="admin-avatar" style="width: 40px; height: 40px; font-size: 16px;"><?php echo $userInitials; ?></div>
            <a href="/petguard/public/login.php?logout=1" class="logout-link">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                Salir
            </a>
        </div>
    </div>

    <!-- VISTA: CATÁLOGO -->
    <?php if ($activeView === 'catalogo'): ?>
    <div class="pet-grid">
        <?php if (empty($data['mascotas'])): ?>
            <div style="grid-column: 1 / -1; text-align: center; padding: 60px 20px; color: var(--text-secondary);">
                <h3>No hay mascotas disponibles en este momento</h3>
                <p>Estamos trabajando para traer nuevos amigos peludos muy pronto.</p>
            </div>
        <?php else: ?>
            <?php foreach ($data['mascotas'] as $m): 
                $generoLabel = $m['genero'] === 'M' ? 'Macho' : ($m['genero'] === 'F' ? 'Hembra' : 'No definido');
                $edadLabel = $m['edad_aprox_meses'] ? $m['edad_aprox_meses'] . ' meses' : 'Edad desconocida';
                $razaDisplay = $m['raza_nombre'] ? $m['raza_nombre'] : ($m['especie_nombre'] ?: 'Mestizo');
            ?>
            <div class="pet-card">
                <div class="pet-card-image">
                    <?php if (!empty($m['foto_principal'])): ?>
                        <img src="<?php echo htmlspecialchars($m['foto_principal']); ?>" alt="<?php echo htmlspecialchars($m['nombre']); ?>">
                    <?php else: ?>
                        <div class="placeholder-icon">🐾</div>
                    <?php endif; ?>
                    <?php if ($m['urgente'] == 1): ?>
                        <span class="pet-badge badge-urgente">¡Urgente!</span>
                    <?php else: ?>
                        <span class="pet-badge badge-disponible">Disponible</span>
                    <?php endif; ?>
                </div>
                <div class="pet-card-body">
                    <div class="pet-card-name"><?php echo htmlspecialchars($m['nombre']); ?></div>
                    <div class="pet-card-breed"><?php echo htmlspecialchars($razaDisplay); ?></div>
                    <div class="pet-card-details">
                        <div class="pet-card-detail-item">⏱️ <?php echo $edadLabel; ?></div>
                        <div class="pet-card-detail-item">⚧️ <?php echo $generoLabel; ?></div>
                        <?php if ($m['peso_kg']): ?><div class="pet-card-detail-item">⚖️ <?php echo $m['peso_kg']; ?> kg</div><?php endif; ?>
                    </div>
                    <button class="btn-adopt" onclick="openAdoptionModal(<?php echo $m['id']; ?>, '<?php echo htmlspecialchars($m['nombre']); ?>')">Solicitar Adopción</button>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- VISTA: MIS SOLICITUDES -->
    <?php if ($activeView === 'solicitudes'): ?>
    <div class="requests-container">
        <h2 style="margin-bottom: 24px; color: var(--text-primary);">Historial de Solicitudes de Adopción</h2>
        <?php if (empty($data['solicitudes'])): ?>
            <div style="text-align: center; padding: 60px 20px; color: var(--text-secondary); background: var(--bg-main); border-radius: 12px; border: 1px dashed var(--border-color);">
                <h3>Aún no tienes solicitudes</h3>
                <p>Explora nuestro <a href="/petguard/public/adoptante.php?view=catalogo" style="color: var(--accent-color); font-weight: 600;">catálogo de mascotas</a> y envía tu primera solicitud.</p>
            </div>
        <?php else: ?>
            <?php foreach ($data['solicitudes'] as $s): 
                $estatusClass = 'status-' . str_replace('_', '-', $s['estatus']);
                $estatusLabel = ucwords(str_replace('_', ' ', $s['estatus']));
                $puedeCancelar = ($s['estatus'] === 'solicitada');
            ?>
            <div class="request-card">
                <img src="<?php echo !empty($s['foto_principal']) ? htmlspecialchars($s['foto_principal']) : '/petguard/public/assets/default-pet.png'; ?>" class="request-img" alt="Mascota">
                <div class="request-info">
                    <h3><?php echo htmlspecialchars($s['mascota_nombre']); ?> <span style="font-size: 14px; color: var(--text-secondary); font-weight: 400;">(<?php echo htmlspecialchars($s['especie_nombre'] ?: 'Mascota'); ?>)</span></h3>
                    <p>📅 Solicitado el: <?php echo date('d/m/Y', strtotime($s['fecha_solicitud'])); ?></p>
                    <?php if ($s['estatus'] === 'rechazada' && !empty($s['motivo_rechazo'])): ?>
                        <p style="color: #ef4444; margin-top: 8px;"><strong>Motivo:</strong> <?php echo htmlspecialchars($s['motivo_rechazo']); ?></p>
                    <?php endif; ?>
                </div>
                <div class="request-actions">
                    <span class="status-badge <?php echo $estatusClass; ?>"><?php echo $estatusLabel; ?></span>
                    <?php if ($puedeCancelar): ?>
                        <form method="POST" onsubmit="return confirm('¿Estás seguro de cancelar esta solicitud?');">
                            <input type="hidden" name="action" value="cancelar_adopcion">
                            <input type="hidden" name="request_id" value="<?php echo $s['id']; ?>">
                            <input type="hidden" name="view" value="solicitudes">
                            <button type="submit" class="btn-cancel">Cancelar Solicitud</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- MODAL: SOLICITAR ADOPCIÓN -->
    <div id="modal-adopcion" class="modal-overlay">
        <div class="modal-container" style="max-width: 600px;">
            <div class="modal-header">
                <h2>Solicitar Adopción: <span id="modal-mascota-nombre"></span></h2>
                <button type="button" class="modal-close" onclick="closeModal('modal-adopcion')">&times;</button>
            </div>
            <form method="POST" class="modal-form">
                <input type="hidden" name="action" value="solicitar_adopcion">
                <input type="hidden" name="view" value="catalogo">
                <input type="hidden" name="mascota_id" id="modal-mascota-id">
                
                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Tipo de Vivienda *</label>
                            <select name="tipo_vivienda" class="form-select" required>
                                <option value="casa_propia">Casa propia</option>
                                <option value="casa_rentada">Casa rentada</option>
                                <option value="departamento">Departamento</option>
                                <option value="otro">Otro</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">¿Tiene jardín o patio?</label>
                            <select name="tiene_jardin" class="form-select">
                                <option value="1">Sí</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">¿Hay niños en casa?</label>
                            <select name="ninos_en_casa" class="form-select">
                                <option value="1">Sí</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">¿Experiencia previa con mascotas?</label>
                            <select name="experiencia_previa" class="form-select">
                                <option value="1">Sí</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">¿Tiene otras mascotas? (Describa brevemente)</label>
                        <div style="display: flex; gap: 10px; align-items: center; margin-bottom: 10px;">
                            <input type="checkbox" name="otras_mascotas" id="check-otras" style="width: 16px; height: 16px;">
                            <label for="check-otras" style="font-size: 14px; margin: 0;">Sí, tengo otras mascotas</label>
                        </div>
                        <textarea name="descripcion_otras_mascotas" class="form-input" rows="2" placeholder="Ej: 1 perro de 5 años, 1 gato..."></textarea>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Motivo de la adopción *</label>
                        <textarea name="motivo" class="form-input" rows="3" required placeholder="¿Por qué deseas adoptar a esta mascota?"></textarea>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Compromisos de cuidado *</label>
                        <textarea name="compromisos" class="form-input" rows="3" required placeholder="Describe cómo garantizarás su bienestar (alimentación, paseos, veterinario, etc.)"></textarea>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeModal('modal-adopcion')">Cancelar</button>
                    <button type="submit" class="btn-primary">Enviar Solicitud</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openAdoptionModal(mascotaId, mascotaNombre) {
            document.getElementById('modal-mascota-id').value = mascotaId;
            document.getElementById('modal-mascota-nombre').textContent = mascotaNombre;
            openModal('modal-adopcion');
        }

        function openModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.add('active');
                document.body.style.overflow = 'hidden';
            }
        }

        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.remove('active');
                document.body.style.overflow = '';
            }
        }

        document.addEventListener('click', function(event) {
            if (event.target.classList.contains('modal-overlay')) {
                event.target.classList.remove('active');
                document.body.style.overflow = '';
            }
        });
    </script>
</body>
</html>