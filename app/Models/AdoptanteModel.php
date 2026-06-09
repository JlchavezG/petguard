<?php
/**
 * ============================================================
 * PETGUARD - Modelo para el Dashboard del Adoptante
 * ============================================================
 */
if (!defined('PETGUARD_APP')) {
    http_response_code(403);
    exit('Acceso no permitido.');
}

class AdoptanteModel {
    private $db;

    public function __construct() {
        $this->db = getDB();
    }

    public function getUserProfile($userId) {
        try {
            $sql = "SELECT id, nombre, apellido_paterno, email, telefono, foto_url, activo 
                    FROM usuarios WHERE id = :id AND activo = 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['id' => $userId]);
            return $stmt->fetch();
        } catch (Exception $e) {
            error_log('AdoptanteModel::getUserProfile Error: ' . $e->getMessage());
            return null;
        }
    }

    public function getAvailablePets($limit = 12) {
        try {
            $sql = "SELECT m.id, m.nombre, m.nombre_interno, m.genero, m.edad_aprox_meses, 
                           m.peso_kg, m.foto_principal, m.estatus, m.urgente,
                           e.nombre as especie_nombre, e.icono as especie_icono,
                           r.nombre as raza_nombre
                    FROM mascotas m
                    LEFT JOIN especies e ON m.especie_id = e.id
                    LEFT JOIN razas r ON m.raza_id = r.id
                    WHERE m.activo = 1 
                      AND m.estatus IN ('rescatado', 'en_albergue', 'en_adopcion', 'proceso_adopcion', 'disponible')
                    ORDER BY m.urgente DESC, m.created_at DESC
                    LIMIT :limit";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log('AdoptanteModel::getAvailablePets Error: ' . $e->getMessage());
            return [];
        }
    }

    public function getMyAdoptionRequests($adoptanteId) {
        try {
            $sql = "SELECT a.id, a.mascota_id, a.estatus, a.fecha_solicitud, a.motivo_rechazo,
                           m.nombre as mascota_nombre, m.foto_principal,
                           e.nombre as especie_nombre
                    FROM adopciones a
                    INNER JOIN mascotas m ON a.mascota_id = m.id
                    LEFT JOIN especies e ON m.especie_id = e.id
                    WHERE a.adoptante_id = :adoptante_id
                    ORDER BY a.fecha_solicitud DESC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['adoptante_id' => $adoptanteId]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log('AdoptanteModel::getMyAdoptionRequests Error: ' . $e->getMessage());
            return [];
        }
    }

    public function createAdoptionRequest($data) {
        try {
            $sql = "INSERT INTO adopciones 
                    (mascota_id, adoptante_id, tipo_vivienda, tiene_jardin, otras_mascotas, 
                     descripcion_otras_mascotas, ninos_en_casa, experiencia_previa, motivo, compromisos, estatus, fecha_solicitud) 
                    VALUES 
                    (:mascota_id, :adoptante_id, :tipo_vivienda, :tiene_jardin, :otras_mascotas, 
                     :descripcion_otras_mascotas, :ninos_en_casa, :experiencia_previa, :motivo, :compromisos, 'solicitada', CURRENT_TIMESTAMP)";
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                'mascota_id' => $data['mascota_id'],
                'adoptante_id' => $data['adoptante_id'],
                'tipo_vivienda' => $data['tipo_vivienda'],
                'tiene_jardin' => isset($data['tiene_jardin']) ? 1 : 0,
                'otras_mascotas' => isset($data['otras_mascotas']) ? 1 : 0,
                'descripcion_otras_mascotas' => $data['descripcion_otras_mascotas'] ?? null,
                'ninos_en_casa' => isset($data['ninos_en_casa']) ? 1 : 0,
                'experiencia_previa' => isset($data['experiencia_previa']) ? 1 : 0,
                'motivo' => $data['motivo'],
                'compromisos' => $data['compromisos']
            ]);
        } catch (Exception $e) {
            error_log('AdoptanteModel::createAdoptionRequest Error: ' . $e->getMessage());
            return false;
        }
    }

    public function cancelAdoptionRequest($requestId, $adoptanteId) {
        try {
            $sql = "UPDATE adopciones SET estatus = 'cancelada' 
                    WHERE id = :id AND adoptante_id = :adoptante_id AND estatus = 'solicitada'";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['id' => $requestId, 'adoptante_id' => $adoptanteId]);
            return $stmt->rowCount() > 0;
        } catch (Exception $e) {
            error_log('AdoptanteModel::cancelAdoptionRequest Error: ' . $e->getMessage());
            return false;
        }
    }
}
