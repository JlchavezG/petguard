<?php
/**
 * ============================================================
 * PETGUARD - Controlador del Dashboard de Administración
 * ============================================================
 */
if (!defined('PETGUARD_APP')) {
    http_response_code(403);
    exit('Acceso no permitido.');
}

class DashboardController {
    private $model;
    private $userAdminModel;
    private $mascotaModel;

    public function __construct() {
        if (!isset($_SESSION['logged_in']) || !isset($_SESSION['user_role_id']) || $_SESSION['user_role_id'] != 1) {
            header('Location: ' . BASE_URL . '/login.php');
            exit;
        }

        require_once APP_PATH . '/Models/DashboardModel.php';
        $this->model = new DashboardModel();
        
        require_once APP_PATH . '/Models/UserAdminModel.php';
        $this->userAdminModel = new UserAdminModel();

        require_once APP_PATH . '/Models/MascotaModel.php';
        $this->mascotaModel = new MascotaModel();
    }

    private function handleImageUpload($file, $existingUrl = null) {
        if (!isset($file) || $file['error'] === UPLOAD_ERR_NO_FILE) {
            return $existingUrl;
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new Exception('Error al subir el archivo: ' . $file['error']);
        }

        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
        $maxSize = 5 * 1024 * 1024;

        if (!in_array($file['type'], $allowedTypes)) {
            throw new Exception('Tipo de archivo no permitido. Solo JPG, PNG o WEBP.');
        }

        if ($file['size'] > $maxSize) {
            throw new Exception('El archivo es demasiado grande. Máximo 5MB.');
        }

        $uploadDir = BASE_PATH . '/public/uploads/mascotas/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'mascota_' . time() . '_' . uniqid() . '.' . $extension;
        $filepath = $uploadDir . $filename;

        if (!move_uploaded_file($file['tmp_name'], $filepath)) {
            throw new Exception('No se pudo guardar el archivo.');
        }

        if ($existingUrl && strpos($existingUrl, '/uploads/mascotas/') !== false) {
            $oldPath = BASE_PATH . '/public' . $existingUrl;
            if (file_exists($oldPath)) {
                @unlink($oldPath);
            }
        }

        return '/petguard/public/uploads/mascotas/' . $filename;
    }

    public function index() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';
            $id = intval($_POST['id'] ?? 0);
            $returnSection = $_POST['section'] ?? 'dashboard';

            try {
                // === ACCIONES DE USUARIOS Y APROBACIONES (INTACTAS) ===
                if ($action === 'approve_volunteer') {
                    $this->model->approveVolunteer($id);
                    $_SESSION['flash']['success'] = 'Voluntario aprobado correctamente.';
                    $returnSection = 'voluntarios';
                } elseif ($action === 'approve_vet') {
                    $this->model->approveVet($id);
                    $_SESSION['flash']['success'] = 'Veterinario verificado correctamente.';
                    $returnSection = 'veterinarios';
                } elseif ($action === 'reject_user') {
                    $stmt = getDB()->prepare("UPDATE usuarios SET activo = 0 WHERE id = :id");
                    $stmt->execute(['id' => $id]);
                    $_SESSION['flash']['error'] = 'Solicitud rechazada.';
                } 
                elseif ($action === 'block_user') {
                    $targetUser = $this->userAdminModel->getById($id);
                    if ($targetUser && $targetUser['rol_id'] == 5) {
                        $_SESSION['flash']['error'] = 'No puedes bloquear a un usuario de Sistemas.';
                    } else {
                        $motivo = $_POST['motivo_bloqueo'] ?? 'Bloqueado por administrador';
                        $this->userAdminModel->blockUser($id, $motivo);
                        $_SESSION['flash']['error'] = 'Usuario bloqueado.';
                    }
                    $returnSection = 'usuarios';
                } elseif ($action === 'unblock_user') {
                    $this->userAdminModel->unblockUser($id);
                    $_SESSION['flash']['success'] = 'Usuario desbloqueado.';
                    $returnSection = 'usuarios';
                } elseif ($action === 'change_user_role') {
                    $newRoleId = intval($_POST['rol_id'] ?? 0);
                    if ($id == $_SESSION['user_id']) {
                        $_SESSION['flash']['error'] = 'No puedes cambiar tu propio rol.';
                    } else {
                        $targetUser = $this->userAdminModel->getById($id);
                        if ($targetUser && $targetUser['rol_id'] == 5) {
                            $_SESSION['flash']['error'] = 'No puedes cambiar el rol de un usuario de Sistemas.';
                        } else {
                            $this->userAdminModel->changeRole($id, $newRoleId);
                            $_SESSION['flash']['success'] = 'Rol de usuario actualizado.';
                        }
                    }
                    $returnSection = 'usuarios';
                } elseif ($action === 'deactivate_user') {
                    if ($id == $_SESSION['user_id']) {
                        $_SESSION['flash']['error'] = 'No puedes desactivar tu propia cuenta.';
                    } else {
                        $targetUser = $this->userAdminModel->getById($id);
                        if ($targetUser && $targetUser['rol_id'] == 5) {
                            $_SESSION['flash']['error'] = 'Solo un usuario de Sistemas puede eliminar cuentas de Sistemas.';
                        } else {
                            $this->userAdminModel->deactivate($id);
                            $_SESSION['flash']['error'] = 'Usuario desactivado.';
                        }
                    }
                    $returnSection = 'usuarios';
                }
                elseif ($action === 'edit_user') {
                    $targetUser = $this->userAdminModel->getById($id);
                    if ($targetUser && $targetUser['rol_id'] == 5) {
                        $_SESSION['flash']['error'] = 'No puedes editar un usuario de Sistemas.';
                    } else {
                        $editData = [
                            'nombre' => trim($_POST['nombre'] ?? ''),
                            'apellido_paterno' => trim($_POST['apellido_paterno'] ?? ''),
                            'apellido_materno' => trim($_POST['apellido_materno'] ?? ''),
                            'email' => trim($_POST['email'] ?? ''),
                            'telefono' => trim($_POST['telefono'] ?? '')
                        ];
                        
                        if (empty($editData['nombre']) || empty($editData['apellido_paterno']) || empty($editData['email'])) {
                            $_SESSION['flash']['error'] = 'Los campos nombre, apellido paterno y email son obligatorios.';
                        } else {
                            $existingUser = $this->userAdminModel->getByEmail($editData['email']);
                            if ($existingUser && $existingUser['id'] != $id) {
                                $_SESSION['flash']['error'] = 'El correo electrónico ya está en uso por otro usuario.';
                            } else {
                                $this->userAdminModel->update($id, $editData);
                                $_SESSION['flash']['success'] = 'Usuario actualizado correctamente.';
                            }
                        }
                    }
                    $returnSection = 'usuarios';
                }
                elseif ($action === 'create_user') {
                    $newData = [
                        'rol_id' => intval($_POST['rol_id'] ?? 4),
                        'nombre' => trim($_POST['nombre'] ?? ''),
                        'apellido_paterno' => trim($_POST['apellido_paterno'] ?? ''),
                        'apellido_materno' => trim($_POST['apellido_materno'] ?? ''),
                        'email' => trim($_POST['email'] ?? ''),
                        'password' => $_POST['password'] ?? '',
                        'telefono' => trim($_POST['telefono'] ?? '')
                    ];
                    
                    if ($newData['rol_id'] == 5) {
                        $_SESSION['flash']['error'] = 'No puedes crear usuarios de Sistemas.';
                    } elseif (empty($newData['nombre']) || empty($newData['apellido_paterno']) || empty($newData['email']) || empty($newData['password'])) {
                        $_SESSION['flash']['error'] = 'Los campos nombre, apellido paterno, email y contraseña son obligatorios.';
                    } elseif (strlen($newData['password']) < 8) {
                        $_SESSION['flash']['error'] = 'La contraseña debe tener al menos 8 caracteres.';
                    } else {
                        $existingUser = $this->userAdminModel->getByEmail($newData['email']);
                        if ($existingUser) {
                            $_SESSION['flash']['error'] = 'El correo electrónico ya está registrado.';
                        } else {
                            $this->userAdminModel->create($newData);
                            $_SESSION['flash']['success'] = 'Usuario creado correctamente.';
                        }
                    }
                    $returnSection = 'usuarios';
                }
                
                // === ACCIONES DE MASCOTAS (CORREGIDAS PARA LA BD REAL) ===
                elseif ($action === 'create_mascota') {
                    $fotoUrl = $this->handleImageUpload($_FILES['foto_file'] ?? null);
                    
                    $generoRaw = $_POST['genero'] ?? '';
                    $genero = $generoRaw === 'Macho' ? 'M' : ($generoRaw === 'Hembra' ? 'F' : 'ND');
                    
                    $mascotaData = [
                        'especie_id' => !empty($_POST['especie_id']) ? intval($_POST['especie_id']) : null,
                        'raza_id' => !empty($_POST['raza_id']) ? intval($_POST['raza_id']) : null,
                        'nombre' => trim($_POST['nombre'] ?? ''),
                        'nombre_interno' => trim($_POST['nombre_interno'] ?? ''),
                        'genero' => $genero,
                        'fecha_nacimiento' => !empty($_POST['fecha_nacimiento']) ? $_POST['fecha_nacimiento'] : null,
                        'edad_aprox_meses' => !empty($_POST['edad_aprox_meses']) ? intval($_POST['edad_aprox_meses']) : null,
                        'color' => trim($_POST['color'] ?? ''),
                        'peso_kg' => !empty($_POST['peso_kg']) ? floatval($_POST['peso_kg']) : null,
                        'descripcion' => trim($_POST['descripcion'] ?? ''),
                        'personalidad' => trim($_POST['personalidad'] ?? ''),
                        'estatus' => $_POST['estatus'] ?? 'en_albergue',
                        'urgente' => isset($_POST['urgente']),
                        'esterilizado' => isset($_POST['esterilizado']),
                        'vacunado' => isset($_POST['vacunado']),
                        'microchip' => trim($_POST['microchip'] ?? ''),
                        'enfermedades' => trim($_POST['enfermedades'] ?? ''),
                        'discapacidad' => trim($_POST['discapacidad'] ?? ''),
                        'ubicacion' => trim($_POST['ubicacion'] ?? ''),
                        'lat' => !empty($_POST['lat']) ? $_POST['lat'] : null,
                        'lng' => !empty($_POST['lng']) ? $_POST['lng'] : null,
                        'foto_principal' => $fotoUrl,
                        'registrado_por' => $_SESSION['user_id'] ?? null
                    ];
                    
                    if (empty($mascotaData['nombre']) || empty($mascotaData['genero'])) {
                        $_SESSION['flash']['error'] = 'Los campos nombre y género son obligatorios.';
                    } else {
                        $result = $this->mascotaModel->create($mascotaData);
                        if ($result) {
                            $_SESSION['flash']['success'] = 'Mascota registrada correctamente.';
                        } else {
                            $_SESSION['flash']['error'] = 'Error al registrar la mascota. Verifica los datos.';
                        }
                    }
                    $returnSection = 'mascotas';
                }
                elseif ($action === 'edit_mascota') {
                    $existingMascota = $this->mascotaModel->getById($id);
                    $fotoUrl = $this->handleImageUpload($_FILES['foto_file'] ?? null, $existingMascota['foto_principal'] ?? null);
                    
                    $generoRaw = $_POST['genero'] ?? '';
                    $genero = $generoRaw === 'Macho' ? 'M' : ($generoRaw === 'Hembra' ? 'F' : 'ND');
                    
                    $mascotaData = [
                        'especie_id' => !empty($_POST['especie_id']) ? intval($_POST['especie_id']) : null,
                        'raza_id' => !empty($_POST['raza_id']) ? intval($_POST['raza_id']) : null,
                        'nombre' => trim($_POST['nombre'] ?? ''),
                        'nombre_interno' => trim($_POST['nombre_interno'] ?? ''),
                        'genero' => $genero,
                        'fecha_nacimiento' => !empty($_POST['fecha_nacimiento']) ? $_POST['fecha_nacimiento'] : null,
                        'edad_aprox_meses' => !empty($_POST['edad_aprox_meses']) ? intval($_POST['edad_aprox_meses']) : null,
                        'color' => trim($_POST['color'] ?? ''),
                        'peso_kg' => !empty($_POST['peso_kg']) ? floatval($_POST['peso_kg']) : null,
                        'descripcion' => trim($_POST['descripcion'] ?? ''),
                        'personalidad' => trim($_POST['personalidad'] ?? ''),
                        'estatus' => $_POST['estatus'] ?? 'en_albergue',
                        'urgente' => isset($_POST['urgente']),
                        'esterilizado' => isset($_POST['esterilizado']),
                        'vacunado' => isset($_POST['vacunado']),
                        'microchip' => trim($_POST['microchip'] ?? ''),
                        'enfermedades' => trim($_POST['enfermedades'] ?? ''),
                        'discapacidad' => trim($_POST['discapacidad'] ?? ''),
                        'ubicacion' => trim($_POST['ubicacion'] ?? ''),
                        'lat' => !empty($_POST['lat']) ? $_POST['lat'] : null,
                        'lng' => !empty($_POST['lng']) ? $_POST['lng'] : null,
                        'foto_principal' => $fotoUrl
                    ];
                    
                    if (empty($mascotaData['nombre']) || empty($mascotaData['genero'])) {
                        $_SESSION['flash']['error'] = 'Los campos nombre y género son obligatorios.';
                    } else {
                        $result = $this->mascotaModel->update($id, $mascotaData);
                        if ($result) {
                            $_SESSION['flash']['success'] = 'Mascota actualizada correctamente.';
                        } else {
                            $_SESSION['flash']['error'] = 'Error al actualizar la mascota.';
                        }
                    }
                    $returnSection = 'mascotas';
                }
                elseif ($action === 'deactivate_mascota') {
                    $this->mascotaModel->deactivate($id);
                    $_SESSION['flash']['error'] = 'Mascota eliminada (desactivada).';
                    $returnSection = 'mascotas';
                }
                elseif ($action === 'change_mascota_status') {
                    $newStatus = $_POST['estatus'] ?? 'en_albergue';
                    $this->mascotaModel->changeStatus($id, $newStatus);
                    $_SESSION['flash']['success'] = 'Estatus de la mascota actualizado.';
                    $returnSection = 'mascotas';
                }
                elseif ($action === 'toggle_urgente') {
                    $this->mascotaModel->toggleUrgente($id);
                    $_SESSION['flash']['success'] = 'Estado de urgencia actualizado.';
                    $returnSection = 'mascotas';
                }
            } catch (Exception $e) {
                $_SESSION['flash']['error'] = 'Error: ' . $e->getMessage();
            }
            
            header('Location: ' . BASE_URL . '/admin.php?section=' . $returnSection);
            exit;
        }

        $activeSection = $_GET['section'] ?? 'dashboard';
        $page = intval($_GET['page'] ?? 1);
        $limit = 10;

        $searchUsers = $_GET['search'] ?? '';
        $rolFilter = intval($_GET['rol'] ?? 0);
        $totalUsers = $this->userAdminModel->getTotalCount($searchUsers, $rolFilter);
        $totalPagesUsers = ceil($totalUsers / $limit);

        $searchMascotas = $_GET['search_mascotas'] ?? '';
        $especieFilter = intval($_GET['especie'] ?? 0);
        $estatusFilter = $_GET['estatus'] ?? '';
        $urgenteFilter = isset($_GET['urgente']) ? ($_GET['urgente'] === '1' ? 1 : 0) : null;
        $totalMascotas = $this->mascotaModel->getTotalCount($searchMascotas, $especieFilter, $estatusFilter, $urgenteFilter);
        $totalPagesMascotas = ceil($totalMascotas / $limit);

        $data = [
            'stats' => $this->model->getStats(),
            'pending_volunteers' => $this->model->getPendingVolunteers(),
            'pending_vets' => $this->model->getPendingVets(),
            'usuarios' => $this->userAdminModel->getAll($searchUsers, $rolFilter, $page, $limit),
            'roles' => $this->userAdminModel->getRoles(),
            'search' => $searchUsers,
            'rol_filter' => $rolFilter,
            'active_section' => $activeSection,
            'pagination_users' => [
                'current_page' => $page,
                'total_pages' => $totalPagesUsers,
                'total_users' => $totalUsers,
                'limit' => $limit
            ],
            'mascotas' => $this->mascotaModel->getAll($searchMascotas, $especieFilter, $estatusFilter, $urgenteFilter, $page, $limit),
            'especies' => $this->mascotaModel->getEspecies(),
            'razas' => $this->mascotaModel->getAllRazas(),
            'search_mascotas' => $searchMascotas,
            'especie_filter' => $especieFilter,
            'estatus_filter' => $estatusFilter,
            'urgente_filter' => $urgenteFilter,
            'pagination_mascotas' => [
                'current_page' => $page,
                'total_pages' => $totalPagesMascotas,
                'total_mascotas' => $totalMascotas,
                'limit' => $limit
            ]
        ];

        require APP_PATH . '/Views/layouts/dashboard.php';
    }
}