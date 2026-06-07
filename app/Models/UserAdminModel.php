<?php
/**
 * ============================================================
 * PETGUARD - Modelo de Gestión de Usuarios (Admin)
 * ============================================================
 */
if (!defined('PETGUARD_APP')) {
    http_response_code(403);
    exit('Acceso no permitido.');
}

class UserAdminModel {
    private $db;

    public function __construct() {
        $this->db = getDB();
    }

    public function getAll($search = '', $rolFilter = 0, $page = 1, $limit = 10) {
        try {
            $sql = "SELECT u.id, u.nombre, u.apellido_paterno, u.apellido_materno, 
                           u.email, u.telefono, u.rol_id, u.activo, u.bloqueado, 
                           u.motivo_bloqueo, u.ultimo_login, u.created_at, u.foto_url,
                           r.nombre as rol_nombre
                    FROM usuarios u
                    INNER JOIN roles r ON u.rol_id = r.id
                    WHERE u.activo = 1";
            
            $params = [];
            $intParams = [];

            if (!empty($search)) {
                $searchTerm = '%' . $search . '%';
                $sql .= " AND (u.nombre LIKE :search1 OR u.apellido_paterno LIKE :search2 OR u.email LIKE :search3)";
                $params['search1'] = $searchTerm;
                $params['search2'] = $searchTerm;
                $params['search3'] = $searchTerm;
            }

            if ($rolFilter > 0) {
                $sql .= " AND u.rol_id = :rol_id";
                $params['rol_id'] = $rolFilter;
            }

            $sql .= " ORDER BY u.created_at DESC";

            $offset = ($page - 1) * $limit;
            $sql .= " LIMIT :limit OFFSET :offset";
            $intParams['limit'] = $limit;
            $intParams['offset'] = $offset;

            $stmt = $this->db->prepare($sql);
            
            foreach ($params as $key => $value) {
                $stmt->bindValue(':' . $key, $value);
            }
            
            foreach ($intParams as $key => $value) {
                $stmt->bindValue(':' . $key, $value, PDO::PARAM_INT);
            }
            
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log('UserAdminModel::getAll Error: ' . $e->getMessage());
            return [];
        }
    }

    public function getTotalCount($search = '', $rolFilter = 0) {
        try {
            $sql = "SELECT COUNT(*) as total
                    FROM usuarios u
                    INNER JOIN roles r ON u.rol_id = r.id
                    WHERE u.activo = 1";
            
            $params = [];

            if (!empty($search)) {
                $searchTerm = '%' . $search . '%';
                $sql .= " AND (u.nombre LIKE :search1 OR u.apellido_paterno LIKE :search2 OR u.email LIKE :search3)";
                $params['search1'] = $searchTerm;
                $params['search2'] = $searchTerm;
                $params['search3'] = $searchTerm;
            }

            if ($rolFilter > 0) {
                $sql .= " AND u.rol_id = :rol_id";
                $params['rol_id'] = $rolFilter;
            }

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetch();
            
            return $result['total'] ?? 0;
        } catch (Exception $e) {
            error_log('UserAdminModel::getTotalCount Error: ' . $e->getMessage());
            return 0;
        }
    }

    public function getById($id) {
        try {
            $sql = "SELECT u.*, r.nombre as rol_nombre
                    FROM usuarios u
                    INNER JOIN roles r ON u.rol_id = r.id
                    WHERE u.id = :id AND u.activo = 1";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['id' => $id]);
            return $stmt->fetch();
        } catch (Exception $e) {
            error_log('UserAdminModel::getById Error: ' . $e->getMessage());
            return null;
        }
    }

    public function getByEmail($email) {
        try {
            $sql = "SELECT * FROM usuarios WHERE email = :email AND activo = 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['email' => $email]);
            return $stmt->fetch();
        } catch (Exception $e) {
            error_log('UserAdminModel::getByEmail Error: ' . $e->getMessage());
            return null;
        }
    }

    public function blockUser($id, $motivo = '') {
        try {
            $sql = "UPDATE usuarios SET bloqueado = 1, motivo_bloqueo = :motivo WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute(['motivo' => $motivo, 'id' => $id]);
        } catch (Exception $e) {
            error_log('UserAdminModel::blockUser Error: ' . $e->getMessage());
            return false;
        }
    }

    public function unblockUser($id) {
        try {
            $sql = "UPDATE usuarios SET bloqueado = 0, motivo_bloqueo = NULL WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute(['id' => $id]);
        } catch (Exception $e) {
            error_log('UserAdminModel::unblockUser Error: ' . $e->getMessage());
            return false;
        }
    }

    public function changeRole($id, $newRoleId) {
        try {
            $stmt = $this->db->prepare("SELECT id FROM roles WHERE id = :rol_id");
            $stmt->execute(['rol_id' => $newRoleId]);
            if ($stmt->rowCount() === 0) {
                return false;
            }

            $sql = "UPDATE usuarios SET rol_id = :rol_id WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute(['rol_id' => $newRoleId, 'id' => $id]);
        } catch (Exception $e) {
            error_log('UserAdminModel::changeRole Error: ' . $e->getMessage());
            return false;
        }
    }

    public function deactivate($id) {
        try {
            $sql = "UPDATE usuarios SET activo = 0 WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute(['id' => $id]);
        } catch (Exception $e) {
            error_log('UserAdminModel::deactivate Error: ' . $e->getMessage());
            return false;
        }
    }

    public function update($id, $data) {
        try {
            $sql = "UPDATE usuarios SET nombre = :nombre, apellido_paterno = :apellido_paterno, 
                           apellido_materno = :apellido_materno, email = :email, telefono = :telefono 
                    WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                'id' => $id,
                'nombre' => $data['nombre'],
                'apellido_paterno' => $data['apellido_paterno'],
                'apellido_materno' => $data['apellido_materno'] ?? '',
                'email' => $data['email'],
                'telefono' => $data['telefono'] ?? ''
            ]);
        } catch (Exception $e) {
            error_log('UserAdminModel::update Error: ' . $e->getMessage());
            return false;
        }
    }

    public function create($data) {
        try {
            $passwordHash = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]);
            
            $sql = "INSERT INTO usuarios (rol_id, nombre, apellido_paterno, apellido_materno, 
                           email, password_hash, telefono, activo, email_verified) 
                    VALUES (:rol_id, :nombre, :apellido_paterno, :apellido_materno, 
                            :email, :password_hash, :telefono, 1, 1)";
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                'rol_id' => $data['rol_id'],
                'nombre' => $data['nombre'],
                'apellido_paterno' => $data['apellido_paterno'],
                'apellido_materno' => $data['apellido_materno'] ?? '',
                'email' => $data['email'],
                'password_hash' => $passwordHash,
                'telefono' => $data['telefono'] ?? ''
            ]);
        } catch (Exception $e) {
            error_log('UserAdminModel::create Error: ' . $e->getMessage());
            return false;
        }
    }

    public function getRoles() {
        try {
            $stmt = $this->db->query("SELECT id, nombre FROM roles ORDER BY id");
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log('UserAdminModel::getRoles Error: ' . $e->getMessage());
            return [];
        }
    }
}