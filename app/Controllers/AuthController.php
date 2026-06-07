<?php
/**
 * ============================================================
 * PETGUARD - Controlador de Autenticación
 * Versión Final con Dashboard Admin
 * ============================================================
 */

if (!defined('PETGUARD_APP')) {
    http_response_code(403);
    exit('Acceso no permitido.');
}

class AuthController {
    
    private $userModel;
    private $db;

    public function __construct() {
        require_once APP_PATH . '/Models/User.php';
        $this->userModel = new User();
        $this->db = getDB();
    }

    /**
     * Procesa el LOGIN con validación de roles y redirección
     */
    public function processLogin() {
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $this->setFlash('error', 'Por favor, completa todos los campos.');
            $this->redirect('login.php');
        }

        $user = $this->userModel->findByEmail($email);

        if ($user && password_verify($password, $user['password_hash'])) {
            
            // 1. Verificar si la cuenta está activa
            if ($user['activo'] == 0) {
                $this->setFlash('error', 'Tu cuenta ha sido desactivada. Contacta al administrador.');
                $this->redirect('login.php');
            }
            
            // 2. Verificar si la cuenta está bloqueada
            if ($user['bloqueado'] == 1) {
                $this->setFlash('error', 'Cuenta bloqueada: ' . ($user['motivo_bloqueo'] ?? 'Motivo no especificado.'));
                $this->redirect('login.php');
            }

            // 3. VALIDACIÓN POR ROL
            $rol_id = $user['rol_id'];
            
            if ($rol_id == 2) {
                // VOLUNTARIO: Debe estar verificado por el admin
                $stmt = $this->db->prepare("SELECT verificado FROM voluntarios WHERE usuario_id = :uid LIMIT 1");
                $stmt->execute(['uid' => $user['id']]);
                $voluntario = $stmt->fetch();
                
                if (!$voluntario || $voluntario['verificado'] == 0) {
                    $this->setFlash('error', 'Tu cuenta está en revisión. Te notificaremos cuando sea aprobada por un administrador.');
                    $this->redirect('login.php');
                }
            }
            
            if ($rol_id == 3) {
                // VETERINARIO: Debe tener documentos verificados
                $stmt = $this->db->prepare("SELECT disponible FROM veterinarios WHERE usuario_id = :uid LIMIT 1");
                $stmt->execute(['uid' => $user['id']]);
                $veterinario = $stmt->fetch();
                
                if (!$veterinario || $veterinario['disponible'] == 0) {
                    $this->setFlash('error', 'Estamos verificando tus documentos. Te contactaremos en 24-48 horas.');
                    $this->redirect('login.php');
                }
            }

            // 4. Login exitoso: Regenerar ID de sesión
            session_regenerate_id(true);

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['nombre'] . ' ' . $user['apellido_paterno'];
            $_SESSION['user_role_id'] = $user['rol_id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['logged_in'] = true;

            $this->userModel->updateLastLogin($user['id']);
            
            // 5. REDIRECCIÓN SEGÚN ROL
            if ($rol_id == 1) {
                // ADMINISTRADOR → Dashboard
                header('Location: ' . BASE_URL . '/admin.php');
                exit;
            } elseif ($rol_id == 4) {
                // ADOPTANTE → Landing (temporal hasta crear su dashboard)
                $this->setFlash('success', '¡Bienvenido ' . $_SESSION['user_name'] . '!');
                header('Location: ' . BASE_URL . '/index.php');
                exit;
            } elseif ($rol_id == 2) {
                // VOLUNTARIO → Landing (temporal)
                $this->setFlash('success', '¡Bienvenido ' . $_SESSION['user_name'] . '!');
                header('Location: ' . BASE_URL . '/index.php');
                exit;
            } elseif ($rol_id == 3) {
                // VETERINARIO → Landing (temporal)
                $this->setFlash('success', '¡Bienvenido ' . $_SESSION['user_name'] . '!');
                header('Location: ' . BASE_URL . '/index.php');
                exit;
            }

        } else {
            $this->setFlash('error', 'Correo electrónico o contraseña incorrectos.');
            $this->redirect('login.php');
        }
    }

    /**
     * Procesa el REGISTRO
     */
    public function processRegister() {
        $rol_id = intval($_POST['rol_id'] ?? 0);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'] ?? '';
        $password_confirm = $_POST['password_confirm'] ?? '';
        $nombre = trim($_POST['nombre'] ?? '');
        $apellido_paterno = trim($_POST['apellido_paterno'] ?? '');
        $apellido_materno = trim($_POST['apellido_materno'] ?? '');
        $telefono = trim($_POST['telefono'] ?? '');
        $curp = strtoupper(trim($_POST['curp'] ?? ''));

        if (!in_array($rol_id, [2, 3, 4])) {
            $this->setFlash('error', 'Rol de usuario inválido.');
            $this->redirect('register.php');
        }

        if (empty($email) || empty($password) || empty($nombre) || empty($apellido_paterno) || empty($telefono) || empty($curp)) {
            $this->setFlash('error', 'Por favor, completa todos los campos obligatorios.');
            $this->redirect('register.php');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->setFlash('error', 'El correo electrónico no es válido.');
            $this->redirect('register.php');
        }

        if (strlen($password) < 8) {
            $this->setFlash('error', 'La contraseña debe tener al menos 8 caracteres.');
            $this->redirect('register.php');
        }

        if ($password !== $password_confirm) {
            $this->setFlash('error', 'Las contraseñas no coinciden.');
            $this->redirect('register.php');
        }

        $existingUser = $this->userModel->findByEmail($email);
        if ($existingUser) {
            $this->setFlash('error', 'El correo electrónico ya está registrado.');
            $this->redirect('register.php');
        }

        $userData = [
            'rol_id' => $rol_id,
            'nombre' => $nombre,
            'apellido_paterno' => $apellido_paterno,
            'apellido_materno' => $apellido_materno,
            'email' => $email,
            'password' => $password,
            'telefono' => $telefono,
            'curp' => $curp
        ];

        $userId = $this->userModel->create($userData);

        if (!$userId) {
            $this->setFlash('error', 'Error al crear el usuario. Inténtalo de nuevo.');
            $this->redirect('register.php');
        }

        try {
            if ($rol_id == 2) {
                // VOLUNTARIO
                $zona_cobertura = trim($_POST['zona_cobertura'] ?? '');
                $hogar_temporal = intval($_POST['hogar_temporal'] ?? 0);

                $sql = "INSERT INTO voluntarios (usuario_id, zona_cobertura, hogar_temporal, activo, verificado, fecha_alta) 
                        VALUES (:user_id, :zona, :hogar, 1, 0, CURRENT_DATE)";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([
                    'user_id' => $userId,
                    'zona' => $zona_cobertura,
                    'hogar' => $hogar_temporal
                ]);

                $this->setFlash('success', 'Registro exitoso. Tu cuenta está en revisión. Te notificaremos cuando sea aprobada.');
                
            } elseif ($rol_id == 3) {
                // VETERINARIO
                $cedula_prof = trim($_POST['cedula_prof'] ?? '');
                $especialidad = trim($_POST['especialidad'] ?? '');

                $sql = "INSERT INTO veterinarios (usuario_id, cedula_prof, especialidad, disponible) 
                        VALUES (:user_id, :cedula, :especialidad, 0)";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([
                    'user_id' => $userId,
                    'cedula' => $cedula_prof,
                    'especialidad' => $especialidad
                ]);

                if (isset($_FILES['documentos']) && $_FILES['documentos']['error'][0] !== UPLOAD_ERR_NO_FILE) {
                    $uploadDir = BASE_PATH . '/public/uploads/veterinarios/' . $userId . '/';
                    if (!file_exists($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }

                    foreach ($_FILES['documentos']['tmp_name'] as $key => $tmp_name) {
                        if ($_FILES['documentos']['error'][$key] === UPLOAD_ERR_OK) {
                            $file_name = $_FILES['documentos']['name'][$key];
                            $file_tmp = $_FILES['documentos']['tmp_name'][$key];
                            $file_size = $_FILES['documentos']['size'][$key];
                            
                            if ($file_size <= 5242880) {
                                move_uploaded_file($file_tmp, $uploadDir . basename($file_name));
                            }
                        }
                    }
                }

                $this->setFlash('success', 'Registro exitoso. Estamos verificando tus documentos. Te contactaremos en 24-48 horas.');
                
            } else {
                // ADOPTANTE
                $calle = trim($_POST['calle'] ?? '');
                $colonia = trim($_POST['colonia'] ?? '');
                $cp = trim($_POST['cp'] ?? '');

                if (!empty($calle)) {
                    $sql = "UPDATE usuarios SET calle = :calle, colonia = :colonia, cp = :cp WHERE id = :id";
                    $stmt = $this->db->prepare($sql);
                    $stmt->execute([
                        'calle' => $calle,
                        'colonia' => $colonia,
                        'cp' => $cp,
                        'id' => $userId
                    ]);
                }

                session_regenerate_id(true);
                $_SESSION['user_id'] = $userId;
                $_SESSION['user_name'] = $nombre . ' ' . $apellido_paterno;
                $_SESSION['user_role_id'] = $rol_id;
                $_SESSION['user_email'] = $email;
                $_SESSION['logged_in'] = true;

                $this->setFlash('success', '¡Bienvenido ' . $nombre . '! Tu cuenta ha sido creada exitosamente.');
                header('Location: ' . BASE_URL . '/index.php');
                exit;
            }

        } catch (Exception $e) {
            $this->setFlash('error', 'Error al completar el registro: ' . $e->getMessage());
            $this->redirect('register.php');
        }

        $this->redirect('login.php');
    }

    /**
     * Cierra la sesión
     */
    public function logout() {
        $_SESSION = array();
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
        }
        session_destroy();
        $this->setFlash('success', 'Has cerrado sesión correctamente.');
        $this->redirect('login.php');
    }

    private function setFlash($type, $message) {
        $_SESSION['flash'][$type] = $message;
    }

    private function redirect($page) {
        header('Location: ' . BASE_URL . '/' . $page);
        exit;
    }
}