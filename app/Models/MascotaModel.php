<?php
/**
 * ============================================================
 * PETGUARD - Modelo de Gestión de Mascotas
 * ============================================================
 */
if (!defined('PETGUARD_APP')) {
    http_response_code(403);
    exit('Acceso no permitido.');
}

class MascotaModel {
    private $db;

    public function __construct() {
        $this->db = getDB();
    }

    public function getAll($search = '', $especieFilter = 0, $estatusFilter = '', $urgente = null, $page = 1, $limit = 10) {
        try {
            $sql = "SELECT m.id, m.especie_id, m.raza_id, m.nombre, m.nombre_interno, 
                           m.genero, m.fecha_nacimiento, m.edad_aprox_meses, m.color, m.peso_kg,
                           m.descripcion, m.personalidad, m.estatus, m.urgente,
                           m.esterilizado, m.vacunado, m.microchip, m.enfermedades, m.discapacidad,
                           m.ubicacion, m.lat, m.lng, m.registrado_por, m.foto_principal,
                           m.activo, m.created_at, m.updated_at,
                           e.nombre as especie_nombre, e.icono as especie_icono,
                           r.nombre as raza_nombre,
                           u.nombre as registrante_nombre, u.apellido_paterno as registrante_apellido
                    FROM mascotas m
                    LEFT JOIN especies e ON m.especie_id = e.id
                    LEFT JOIN razas r ON m.raza_id = r.id
                    LEFT JOIN usuarios u ON m.registrado_por = u.id
                    WHERE m.activo = 1";
            
            $params = [];

            if (!empty($search)) {
                $searchTerm = '%' . $search . '%';
                $sql .= " AND (m.nombre LIKE :search1 OR m.nombre_interno LIKE :search2 OR m.ubicacion LIKE :search3)";
                $params['search1'] = $searchTerm;
                $params['search2'] = $searchTerm;
                $params['search3'] = $searchTerm;
            }

            if ($especieFilter > 0) {
                $sql .= " AND m.especie_id = :especie_id";
                $params['especie_id'] = $especieFilter;
            }

            if (!empty($estatusFilter)) {
                $sql .= " AND m.estatus = :estatus";
                $params['estatus'] = $estatusFilter;
            }

            if ($urgente !== null) {
                $sql .= " AND m.urgente = :urgente";
                $params['urgente'] = $urgente;
            }

            $sql .= " ORDER BY m.created_at DESC";

            $offset = ($page - 1) * $limit;
            $sql .= " LIMIT :limit OFFSET :offset";

            $stmt = $this->db->prepare($sql);
            
            foreach ($params as $key => $value) {
                $stmt->bindValue(':' . $key, $value);
            }
            
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log('MascotaModel::getAll Error: ' . $e->getMessage());
            return [];
        }
    }

    public function getTotalCount($search = '', $especieFilter = 0, $estatusFilter = '', $urgente = null) {
        try {
            $sql = "SELECT COUNT(*) as total FROM mascotas m WHERE m.activo = 1";
            $params = [];

            if (!empty($search)) {
                $searchTerm = '%' . $search . '%';
                $sql .= " AND (m.nombre LIKE :search1 OR m.nombre_interno LIKE :search2 OR m.ubicacion LIKE :search3)";
                $params['search1'] = $searchTerm;
                $params['search2'] = $searchTerm;
                $params['search3'] = $searchTerm;
            }

            if ($especieFilter > 0) {
                $sql .= " AND m.especie_id = :especie_id";
                $params['especie_id'] = $especieFilter;
            }

            if (!empty($estatusFilter)) {
                $sql .= " AND m.estatus = :estatus";
                $params['estatus'] = $estatusFilter;
            }

            if ($urgente !== null) {
                $sql .= " AND m.urgente = :urgente";
                $params['urgente'] = $urgente;
            }

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetch();
            
            return $result['total'] ?? 0;
        } catch (Exception $e) {
            error_log('MascotaModel::getTotalCount Error: ' . $e->getMessage());
            return 0;
        }
    }

    public function getById($id) {
        try {
            $sql = "SELECT m.*, e.nombre as especie_nombre, e.icono as especie_icono,
                           r.nombre as raza_nombre,
                           u.nombre as registrante_nombre, u.apellido_paterno as registrante_apellido
                    FROM mascotas m
                    LEFT JOIN especies e ON m.especie_id = e.id
                    LEFT JOIN razas r ON m.raza_id = r.id
                    LEFT JOIN usuarios u ON m.registrado_por = u.id
                    WHERE m.id = :id AND m.activo = 1";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['id' => $id]);
            return $stmt->fetch();
        } catch (Exception $e) {
            error_log('MascotaModel::getById Error: ' . $e->getMessage());
            return null;
        }
    }

    public function create($data) {
        try {
            $sql = "INSERT INTO mascotas 
                    (especie_id, raza_id, nombre, nombre_interno, genero, fecha_nacimiento, edad_aprox_meses, 
                     color, peso_kg, descripcion, personalidad, estatus, urgente, 
                     esterilizado, vacunado, microchip, enfermedades, discapacidad, 
                     ubicacion, lat, lng, registrado_por, foto_principal) 
                    VALUES 
                    (:especie_id, :raza_id, :nombre, :nombre_interno, :genero, :fecha_nacimiento, :edad_aprox_meses, 
                     :color, :peso_kg, :descripcion, :personalidad, :estatus, :urgente, 
                     :esterilizado, :vacunado, :microchip, :enfermedades, :discapacidad, 
                     :ubicacion, :lat, :lng, :registrado_por, :foto_principal)";
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                'especie_id' => $data['especie_id'] ?? null,
                'raza_id' => $data['raza_id'] ?? null,
                'nombre' => $data['nombre'],
                'nombre_interno' => $data['nombre_interno'] ?? null,
                'genero' => $data['genero'],
                'fecha_nacimiento' => !empty($data['fecha_nacimiento']) ? $data['fecha_nacimiento'] : null,
                'edad_aprox_meses' => !empty($data['edad_aprox_meses']) ? $data['edad_aprox_meses'] : null,
                'color' => $data['color'] ?? null,
                'peso_kg' => !empty($data['peso_kg']) ? $data['peso_kg'] : null,
                'descripcion' => $data['descripcion'] ?? null,
                'personalidad' => $data['personalidad'] ?? null,
                'estatus' => $data['estatus'] ?? 'en_albergue',
                'urgente' => isset($data['urgente']) ? 1 : 0,
                'esterilizado' => isset($data['esterilizado']) ? 1 : 0,
                'vacunado' => isset($data['vacunado']) ? 1 : 0,
                'microchip' => !empty($data['microchip']) ? $data['microchip'] : null,
                'enfermedades' => $data['enfermedades'] ?? null,
                'discapacidad' => $data['discapacidad'] ?? null,
                'ubicacion' => $data['ubicacion'] ?? null,
                'lat' => !empty($data['lat']) ? $data['lat'] : null,
                'lng' => !empty($data['lng']) ? $data['lng'] : null,
                'registrado_por' => $data['registrado_por'] ?? $_SESSION['user_id'] ?? null,
                'foto_principal' => $data['foto_principal'] ?? null
            ]);
        } catch (Exception $e) {
            error_log('MascotaModel::create Error: ' . $e->getMessage());
            return false;
        }
    }

    public function update($id, $data) {
        try {
            $sql = "UPDATE mascotas SET 
                    especie_id = :especie_id, raza_id = :raza_id, nombre = :nombre, 
                    nombre_interno = :nombre_interno, genero = :genero, 
                    fecha_nacimiento = :fecha_nacimiento, edad_aprox_meses = :edad_aprox_meses,
                    color = :color, peso_kg = :peso_kg, descripcion = :descripcion, 
                    personalidad = :personalidad, estatus = :estatus, urgente = :urgente,
                    esterilizado = :esterilizado, vacunado = :vacunado, microchip = :microchip,
                    enfermedades = :enfermedades, discapacidad = :discapacidad,
                    ubicacion = :ubicacion, lat = :lat, lng = :lng, foto_principal = :foto_principal
                    WHERE id = :id";
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                'id' => $id,
                'especie_id' => $data['especie_id'] ?? null,
                'raza_id' => $data['raza_id'] ?? null,
                'nombre' => $data['nombre'],
                'nombre_interno' => $data['nombre_interno'] ?? null,
                'genero' => $data['genero'],
                'fecha_nacimiento' => !empty($data['fecha_nacimiento']) ? $data['fecha_nacimiento'] : null,
                'edad_aprox_meses' => !empty($data['edad_aprox_meses']) ? $data['edad_aprox_meses'] : null,
                'color' => $data['color'] ?? null,
                'peso_kg' => !empty($data['peso_kg']) ? $data['peso_kg'] : null,
                'descripcion' => $data['descripcion'] ?? null,
                'personalidad' => $data['personalidad'] ?? null,
                'estatus' => $data['estatus'] ?? 'en_albergue',
                'urgente' => isset($data['urgente']) ? 1 : 0,
                'esterilizado' => isset($data['esterilizado']) ? 1 : 0,
                'vacunado' => isset($data['vacunado']) ? 1 : 0,
                'microchip' => !empty($data['microchip']) ? $data['microchip'] : null,
                'enfermedades' => $data['enfermedades'] ?? null,
                'discapacidad' => $data['discapacidad'] ?? null,
                'ubicacion' => $data['ubicacion'] ?? null,
                'lat' => !empty($data['lat']) ? $data['lat'] : null,
                'lng' => !empty($data['lng']) ? $data['lng'] : null,
                'foto_principal' => $data['foto_principal'] ?? null
            ]);
        } catch (Exception $e) {
            error_log('MascotaModel::update Error: ' . $e->getMessage());
            return false;
        }
    }

    public function deactivate($id) {
        try {
            $sql = "UPDATE mascotas SET activo = 0 WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute(['id' => $id]);
        } catch (Exception $e) {
            error_log('MascotaModel::deactivate Error: ' . $e->getMessage());
            return false;
        }
    }

    public function changeStatus($id, $status) {
        try {
            $sql = "UPDATE mascotas SET estatus = :estatus WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute(['estatus' => $status, 'id' => $id]);
        } catch (Exception $e) {
            error_log('MascotaModel::changeStatus Error: ' . $e->getMessage());
            return false;
        }
    }

    public function toggleUrgente($id) {
        try {
            $sql = "UPDATE mascotas SET urgente = NOT urgente WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute(['id' => $id]);
        } catch (Exception $e) {
            error_log('MascotaModel::toggleUrgente Error: ' . $e->getMessage());
            return false;
        }
    }

    public function getEspecies() {
        try {
            $stmt = $this->db->query("SELECT id, nombre, icono FROM especies ORDER BY nombre");
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log('MascotaModel::getEspecies Error: ' . $e->getMessage());
            return [];
        }
    }

    public function getRazasByEspecie($especieId) {
        try {
            $sql = "SELECT id, nombre FROM razas WHERE especie_id = :especie_id ORDER BY nombre";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['especie_id' => $especieId]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log('MascotaModel::getRazasByEspecie Error: ' . $e->getMessage());
            return [];
        }
    }

    public function getAllRazas() {
        try {
            $sql = "SELECT r.id, r.nombre, r.especie_id, e.nombre as especie_nombre 
                    FROM razas r 
                    LEFT JOIN especies e ON r.especie_id = e.id 
                    ORDER BY e.nombre, r.nombre";
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log('MascotaModel::getAllRazas Error: ' . $e->getMessage());
            return [];
        }
    }
}