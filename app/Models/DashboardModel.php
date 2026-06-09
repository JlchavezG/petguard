<?php
/**
 * ============================================================
 * PETGUARD - Modelo del Dashboard (Versión Robusta)
 * ============================================================
 */
if (!defined('PETGUARD_APP')) {
    http_response_code(403);
    exit('Acceso no permitido.');
}

class DashboardModel {
    private $db;

    public function __construct() {
        $this->db = getDB();
    }

    /**
     * Obtiene estadísticas generales (con manejo de errores)
     */
    public function getStats() {
        $stats = [
            'total_usuarios' => 0,
            'voluntarios_pendientes' => 0,
            'veterinarios_pendientes' => 0,
            'mascotas_disponibles' => 0
        ];

        try {
            $stmt = $this->db->query("SELECT COUNT(*) as total FROM usuarios");
            $stats['total_usuarios'] = $stmt->fetch()['total'];
        } catch (Exception $e) { /* Ignorar si falla */ }

        try {
            $stmt = $this->db->query("SELECT COUNT(*) as total FROM voluntarios WHERE verificado = 0");
            $stats['voluntarios_pendientes'] = $stmt->fetch()['total'];
        } catch (Exception $e) { /* Ignorar si falla */ }

        try {
            $stmt = $this->db->query("SELECT COUNT(*) as total FROM veterinarios WHERE disponible = 0");
            $stats['veterinarios_pendientes'] = $stmt->fetch()['total'];
        } catch (Exception $e) { /* Ignorar si falla */ }

        try {
            $stmt = $this->db->query("SELECT COUNT(*) as total FROM mascotas");
            $stats['mascotas_disponibles'] = $stmt->fetch()['total'];
        } catch (Exception $e) { /* Ignorar si falla */ }

        return $stats;
    }

    /**
     * Obtiene lista de voluntarios pendientes
     */
    public function getPendingVolunteers() {
        try {
            $sql = "SELECT u.id, u.nombre, u.apellido_paterno, u.email, 
                           v.zona_cobertura, v.fecha_alta
                    FROM usuarios u
                    INNER JOIN voluntarios v ON u.id = v.usuario_id
                    WHERE u.rol_id = 2 AND v.verificado = 0
                    ORDER BY v.fecha_alta DESC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Obtiene lista de veterinarios pendientes
     */
    public function getPendingVets() {
        try {
            $sql = "SELECT u.id, u.nombre, u.apellido_paterno, u.email, 
                           vet.cedula_prof, vet.especialidad, u.created_at
                    FROM usuarios u
                    INNER JOIN veterinarios vet ON u.id = vet.usuario_id
                    WHERE u.rol_id = 3 AND vet.disponible = 0
                    ORDER BY u.created_at DESC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Aprueba un voluntario
     */
    public function approveVolunteer($userId) {
        try {
            $sql = "UPDATE voluntarios SET verificado = 1 WHERE usuario_id = :uid";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute(['uid' => $userId]);
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Aprueba un veterinario
     */
    public function approveVet($userId) {
        try {
            $sql = "UPDATE veterinarios SET disponible = 1 WHERE usuario_id = :uid";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute(['uid' => $userId]);
        } catch (Exception $e) {
            return false;
        }
    }
}