<?php
define('PETGUARD_APP', true);
require_once '../../config/app.php';
require_once '../../config/database.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['user_role_id'] != 1) {
    http_response_code(403);
    exit(json_encode(['error' => 'No autorizado']));
}

header('Content-Type: application/json; charset=utf-8');

try {
    require_once APP_PATH . '/Models/UserAdminModel.php';
    $model = new UserAdminModel();
    
    $search = trim($_GET['search'] ?? '');
    $rolFilter = intval($_GET['rol'] ?? 0);
    $page = intval($_GET['page'] ?? 1);
    $limit = 10;
    
    $total = $model->getTotalCount($search, $rolFilter);
    $totalPages = ceil($total / $limit);
    $users = $model->getAll($search, $rolFilter, $page, $limit);
    
    $formatted = [];
    foreach ($users as $u) {
        $formatted[] = [
            'id' => $u['id'],
            'nombre' => $u['nombre'],
            'apellido_paterno' => $u['apellido_paterno'],
            'email' => $u['email'],
            'rol_id' => $u['rol_id'],
            'rol_nombre' => $u['rol_nombre'],
            'bloqueado' => $u['bloqueado'],
            'foto_url' => $u['foto_url']
        ];
    }
    
    echo json_encode([
        'success' => true,
        'count' => count($formatted),
        'total' => $total,
        'current_page' => $page,
        'total_pages' => $totalPages,
        'users' => $formatted
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error: ' . $e->getMessage()]);
}