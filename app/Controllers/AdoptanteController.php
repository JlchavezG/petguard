<?php
/**
 * ============================================================
 * PETGUARD - Controlador del Dashboard del Adoptante
 * ============================================================
 */
if (!defined('PETGUARD_APP')) {
    http_response_code(403);
    exit('Acceso no permitido.');
}

class AdoptanteController {
    private $model;

    public function __construct() {
        require_once APP_PATH . '/Models/AdoptanteModel.php';
        $this->model = new AdoptanteModel();
    }

    public function index() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';
            $userId = $_SESSION['user_id'];

            try {
                if ($action === 'solicitar_adopcion') {
                    $data = [
                        'mascota_id' => intval($_POST['mascota_id'] ?? 0),
                        'adoptante_id' => $userId,
                        'tipo_vivienda' => $_POST['tipo_vivienda'] ?? 'otro',
                        'tiene_jardin' => isset($_POST['tiene_jardin']),
                        'otras_mascotas' => isset($_POST['otras_mascotas']),
                        'descripcion_otras_mascotas' => trim($_POST['descripcion_otras_mascotas'] ?? ''),
                        'ninos_en_casa' => isset($_POST['ninos_en_casa']),
                        'experiencia_previa' => isset($_POST['experiencia_previa']),
                        'motivo' => trim($_POST['motivo'] ?? ''),
                        'compromisos' => trim($_POST['compromisos'] ?? '')
                    ];

                    if (empty($data['motivo']) || empty($data['compromisos'])) {
                        $_SESSION['flash']['error'] = 'El motivo y los compromisos son obligatorios.';
                    } else {
                        $result = $this->model->createAdoptionRequest($data);
                        if ($result) {
                            $_SESSION['flash']['success'] = '¡Solicitud de adopción enviada correctamente! El albergue la revisará pronto.';
                        } else {
                            $_SESSION['flash']['error'] = 'Error al enviar la solicitud. Intenta de nuevo.';
                        }
                    }
                } elseif ($action === 'cancelar_adopcion') {
                    $requestId = intval($_POST['request_id'] ?? 0);
                    $result = $this->model->cancelAdoptionRequest($requestId, $userId);
                    if ($result) {
                        $_SESSION['flash']['success'] = 'Solicitud cancelada correctamente.';
                    } else {
                        $_SESSION['flash']['error'] = 'No se pudo cancelar la solicitud (quizás ya fue procesada).';
                    }
                }
            } catch (Exception $e) {
                $_SESSION['flash']['error'] = 'Error: ' . $e->getMessage();
            }

            header('Location: ' . BASE_URL . '/adoptante.php?view=' . ($_POST['view'] ?? 'catalogo'));
            exit;
        }

        $userId = $_SESSION['user_id'];
        $currentView = $_GET['view'] ?? 'catalogo';

        $data = [
            'user' => $this->model->getUserProfile($userId),
            'mascotas' => $this->model->getAvailablePets(),
            'solicitudes' => $this->model->getMyAdoptionRequests($userId),
            'active_view' => $currentView
        ];

        require APP_PATH . '/Views/layouts/adoptante_layout.php';
    }
}