<?php
/**
 * ============================================================
 * PETGUARD - Modelo de Usuario
 * ============================================================
 * Maneja todas las operaciones de la tabla 'usuarios'.
 * Proyecto de Ingeniería de Software · 9no Semestre 2026
 * ============================================================
 */

// Seguridad: Evitar acceso directo
if (!defined('PETGUARD_APP')) {
    http_response_code(403);
    exit('Acceso no permitido.');
}

class User {
    
    private $db;

    /**
     * Constructor: Inyecta la dependencia de la base de datos
     */
    public function __construct() {
        $this->db = getDB();
    }

    /**
     * Buscar un usuario por su correo electrónico
     * @param string $email
     * @return array|false
     */
    public function findByEmail($email) {
        $sql = "SELECT id, rol_id, nombre, apellido_paterno, apellido_materno, 
                       email, password_hash, telefono, curp, activo, bloqueado, 
                       motivo_bloqueo, email_verified 
                FROM usuarios 
                WHERE email = :email 
                LIMIT 1";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['email' => $email]);
        
        return $stmt->fetch();
    }

    /**
     * Buscar un usuario por su ID
     * @param int $id
     * @return array|false
     */
    public function findById($id) {
        $sql = "SELECT id, rol_id, nombre, apellido_paterno, apellido_materno, 
                       email, telefono, curp, foto_url, fecha_nacimiento, genero,
                       calle, num_exterior, colonia, municipio, estado, cp,
                       activo, bloqueado, ultimo_login, created_at
                FROM usuarios 
                WHERE id = :id 
                LIMIT 1";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        
        return $stmt->fetch();
    }

    /**
     * Crear un nuevo usuario (Registro)
     * Hashea la contraseña automáticamente con Bcrypt (cost=12)
     * 
     * @param array $data ['rol_id', 'nombre', 'apellido_paterno', 'apellido_materno', 'email', 'password', 'telefono', 'curp']
     * @return int|false (ID del usuario creado o false en error)
     */
    public function create($data) {
        // 1. Hashear contraseña (Bcrypt cost=12)
        $passwordHash = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]);

        // 2. Preparar la consulta SQL
        $sql = "INSERT INTO usuarios 
                (rol_id, nombre, apellido_paterno, apellido_materno, email, password_hash, telefono, curp, activo, email_verified) 
                VALUES 
                (:rol_id, :nombre, :apellido_paterno, :apellido_materno, :email, :password_hash, :telefono, :curp, 1, 1)";
        
        $stmt = $this->db->prepare($sql);
        
        // 3. Ejecutar con los datos sanitizados
        $success = $stmt->execute([
            'rol_id'             => $data['rol_id'],
            'nombre'             => trim($data['nombre']),
            'apellido_paterno'   => trim($data['apellido_paterno']),
            'apellido_materno'   => trim($data['apellido_materno']),
            'email'              => strtolower(trim($data['email'])),
            'password_hash'      => $passwordHash,
            'telefono'           => $data['telefono'] ?? null,
            'curp'               => strtoupper(trim($data['curp']))
        ]);

        // 4. Retornar el ID del último insertado o false
        return $success ? $this->db->lastInsertId() : false;
    }

    /**
     * Actualizar la fecha del último login
     * @param int $id
     * @return bool
     */
    public function updateLastLogin($id) {
        $sql = "UPDATE usuarios SET ultimo_login = CURRENT_TIMESTAMP WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Actualizar contraseña (Para recuperación)
     * @param string $token
     * @param string $newPassword
     * @return bool
     */
    public function updatePasswordByToken($token, $newPassword) {
        $passwordHash = password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 12]);
        
        $sql = "UPDATE usuarios 
                SET password_hash = :password_hash, token_reset = NULL, token_reset_exp = NULL 
                WHERE token_reset = :token AND token_reset_exp > CURRENT_TIMESTAMP";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'password_hash' => $passwordHash,
            'token'         => $token
        ]);
    }

    /**
     * Guardar token de recuperación de contraseña
     * @param string $email
     * @param string $token
     * @param string $expiration
     * @return bool
     */
    public function saveResetToken($email, $token, $expiration) {
        $sql = "UPDATE usuarios SET token_reset = :token, token_reset_exp = :exp WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'token' => $token,
            'exp'   => $expiration,
            'email' => $email
        ]);
    }
}