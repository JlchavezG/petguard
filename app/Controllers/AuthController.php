<?php
if (!defined('PETGUARD_APP')) {
    http_response_code(403);
    exit('Acceso no permitido.');
}

class AuthController {
    private $db;

    public function __construct() {
        $this->db = getDB();
    }

    public function showLogin() {
        if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true && isset($_SESSION['user_role_id'])) {
            $this->redirectByRole($_SESSION['user_role_id']);
        }
        require APP_PATH . '/Views/auth/login.php';
    }

    public function showRegister() {
        if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true && isset($_SESSION['user_role_id'])) {
            $this->redirectByRole($_SESSION['user_role_id']);
        }
        require APP_PATH . '/Views/auth/register.php';
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/login.php');
            exit;
        }

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $_SESSION['flash']['error'] = 'Por favor, completa todos los campos.';
            header('Location: ' . BASE_URL . '/login.php');
            exit;
        }

        try {
            $sql = "SELECT id, rol_id, nombre, apellido_paterno, email, password_hash, activo, bloqueado, motivo_bloqueo 
                    FROM usuarios WHERE email = :email AND activo = 1";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch();

            if (!$user || !password_verify($password, $user['password_hash'])) {
                $_SESSION['flash']['error'] = 'Credenciales incorrectas.';
                header('Location: ' . BASE_URL . '/login.php');
                exit;
            }

            if ($user['bloqueado'] == 1) {
                $_SESSION['flash']['error'] = 'Tu cuenta ha sido bloqueada. Motivo: ' . ($user['motivo_bloqueo'] ?: 'Sin especificar.');
                header('Location: ' . BASE_URL . '/login.php');
                exit;
            }

            $this->db->prepare("UPDATE usuarios SET ultimo_login = CURRENT_TIMESTAMP WHERE id = :id")
                     ->execute(['id' => $user['id']]);

            $_SESSION['logged_in'] = true;
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_role_id'] = $user['rol_id'];
            $_SESSION['user_name'] = $user['nombre'] . ' ' . $user['apellido_paterno'];
            $_SESSION['user_email'] = $user['email'];

            $this->redirectByRole($user['rol_id']);

        } catch (Exception $e) {
            error_log('AuthController::login Error: ' . $e->getMessage());
            $_SESSION['flash']['error'] = 'Ocurrió un error al iniciar sesión.';
            header('Location: ' . BASE_URL . '/login.php');
            exit;
        }
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/register.php');
            exit;
        }

        try {
            $rol_id = intval($_POST['rol_id'] ?? 0);
            $nombre = trim($_POST['nombre'] ?? '');
            $apellido_paterno = trim($_POST['apellido_paterno'] ?? '');
            $apellido_materno = trim($_POST['apellido_materno'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $telefono = trim($_POST['telefono'] ?? '');
            $password = $_POST['password'] ?? '';

            if (empty($nombre) || empty($apellido_paterno) || empty($email) || empty($password)) {
                $_SESSION['flash']['error'] = 'Los campos marcados con * son obligatorios.';
                header('Location: ' . BASE_URL . '/register.php');
                exit;
            }

            if (strlen($password) < 8) {
                $_SESSION['flash']['error'] = 'La contraseña debe tener al menos 8 caracteres.';
                header('Location: ' . BASE_URL . '/register.php');
                exit;
            }

            if (!in_array($rol_id, [2, 3, 4])) {
                $_SESSION['flash']['error'] = 'Rol no permitido para registro público.';
                header('Location: ' . BASE_URL . '/register.php');
                exit;
            }

            $checkStmt = $this->db->prepare("SELECT id FROM usuarios WHERE email = :email");
            $checkStmt->execute(['email' => $email]);
            if ($checkStmt->fetch()) {
                $_SESSION['flash']['error'] = 'Este correo electrónico ya está registrado.';
                header('Location: ' . BASE_URL . '/register.php');
                exit;
            }

            $this->db->beginTransaction();

            try {
                $sql = "INSERT INTO usuarios (
                            rol_id, nombre, apellido_paterno, apellido_materno, email, telefono, 
                            password_hash, activo, bloqueado, created_at, updated_at
                        ) VALUES (
                            :rol_id, :nombre, :apellido_paterno, :apellido_materno, :email, :telefono, 
                            :password_hash, 1, 0, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP
                        )";

                $stmt = $this->db->prepare($sql);
                $stmt->execute([
                    'rol_id' => $rol_id,
                    'nombre' => $nombre,
                    'apellido_paterno' => $apellido_paterno,
                    'apellido_materno' => $apellido_materno,
                    'email' => $email,
                    'telefono' => $telefono,
                    'password_hash' => password_hash($password, PASSWORD_DEFAULT)
                ]);

                $usuario_id = $this->db->lastInsertId();

                if ($rol_id === 3) {
                    $cedula_prof = trim($_POST['cedula_prof'] ?? '');
                    if (empty($cedula_prof)) {
                        throw new Exception('La cédula profesional es obligatoria para veterinarios.');
                    }

                    $checkCedula = $this->db->prepare("SELECT id FROM veterinarios WHERE cedula_prof = :cedula");
                    $checkCedula->execute(['cedula' => $cedula_prof]);
                    if ($checkCedula->fetch()) {
                        throw new Exception('Esta cédula profesional ya está registrada en el sistema.');
                    }

                    // ✅ CORRECCIÓN: Insertar con disponible = 0 (pendiente de aprobación)
                    $sql_vet = "INSERT INTO veterinarios (
                                    usuario_id, cedula_prof, especialidad, universidad, anios_exp, 
                                    bio, disponible, atiende_emergencias, tarifa_consulta, created_at
                                ) VALUES (
                                    :usuario_id, :cedula_prof, :especialidad, :universidad, :anios_exp, 
                                    :bio, 0, :atiende_emergencias, :tarifa_consulta, CURRENT_TIMESTAMP
                                )";

                    $stmt_vet = $this->db->prepare($sql_vet);
                    $stmt_vet->execute([
                        'usuario_id' => $usuario_id,
                        'cedula_prof' => $cedula_prof,
                        'especialidad' => !empty($_POST['especialidad']) ? trim($_POST['especialidad']) : null,
                        'universidad' => !empty($_POST['universidad']) ? trim($_POST['universidad']) : null,
                        'anios_exp' => !empty($_POST['anios_exp']) ? intval($_POST['anios_exp']) : null,
                        'bio' => !empty($_POST['bio']) ? trim($_POST['bio']) : null,
                        'atiende_emergencias' => intval($_POST['atiende_emergencias'] ?? 0),
                        'tarifa_consulta' => !empty($_POST['tarifa_consulta']) ? floatval($_POST['tarifa_consulta']) : null
                    ]);
                }

                $this->db->commit();

                $_SESSION['flash']['success'] = 'Cuenta creada exitosamente. Tu registro está pendiente de aprobación por un administrador.';
                header('Location: ' . BASE_URL . '/login.php');
                exit;

            } catch (Exception $e) {
                $this->db->rollBack();
                throw $e;
            }

        } catch (Exception $e) {
            error_log('AuthController::register Error: ' . $e->getMessage());
            $_SESSION['flash']['error'] = $e->getMessage() ?: 'Ocurrió un error al crear la cuenta. Intenta de nuevo.';
            header('Location: ' . BASE_URL . '/register.php');
            exit;
        }
    }

    public function logout() {
        session_unset();
        session_destroy();
        session_start();
        $_SESSION['flash']['success'] = 'Has cerrado sesión correctamente.';
        header('Location: ' . BASE_URL . '/login.php');
        exit;
    }

    private function redirectByRole($roleId) {
        switch ((int)$roleId) {
            case 1: header('Location: ' . BASE_URL . '/admin.php'); break;
            case 4: header('Location: ' . BASE_URL . '/adoptante.php'); break;
            default: header('Location: ' . BASE_URL . '/index.php'); break;
        }
        exit;
    }
}