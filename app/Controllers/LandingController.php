    <?php
/**
 * ============================================================
 * PETGUARD - Controlador de la Landing Page
 * ============================================================
 */
if (!defined('PETGUARD_APP')) {
    http_response_code(403);
    exit('Acceso no permitido.');
}

class LandingController {
    private $db;

    public function __construct() {
        $this->db = getDB();
    }

    public function index() {
        try {
            // Obtener estadísticas ligeras para mostrar impacto en la landing
            $stats = [
                'mascotas_disponibles' => 0,
                'adopciones_completadas' => 0
            ];

            // CORREGIDO: Usar los valores exactos del ENUM de la tabla mascotas
            // ('rescatado', 'en_albergue', 'en_adopcion') son los estados de búsqueda de hogar
            $stmt1 = $this->db->query("
                SELECT COUNT(*) as count 
                FROM mascotas 
                WHERE activo = 1 
                AND estatus IN ('rescatado', 'en_albergue', 'en_adopcion')
            ");
            $stats['mascotas_disponibles'] = $stmt1->fetch()['count'] ?? 0;

            // Contar adopciones completadas
            $stmt2 = $this->db->query("
                SELECT COUNT(*) as count 
                FROM adopciones 
                WHERE estatus = 'completada'
            ");
            $stats['adopciones_completadas'] = $stmt2->fetch()['count'] ?? 0;

            $data = [
                'stats' => $stats
            ];

            require APP_PATH . '/Views/landing/index.php';
        } catch (Exception $e) {
            error_log('LandingController::index Error: ' . $e->getMessage());
            
            // Fallback a vista básica si falla la BD
            $data = [
                'stats' => ['mascotas_disponibles' => 0, 'adopciones_completadas' => 0]
            ];
            require APP_PATH . '/Views/landing/index.php';
        }
    }
}