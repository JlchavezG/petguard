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
    require_once APP_PATH . '/Models/MascotaModel.php';
    $model = new MascotaModel();
    
    $search = trim($_GET['search'] ?? '');
    $especieFilter = intval($_GET['especie'] ?? 0);
    $estatusFilter = $_GET['estatus'] ?? '';
    $urgenteFilter = isset($_GET['urgente']) && $_GET['urgente'] !== '' ? ($_GET['urgente'] === '1' ? 1 : 0) : null;
    $page = intval($_GET['page'] ?? 1);
    $limit = 10;
    
    $total = $model->getTotalCount($search, $especieFilter, $estatusFilter, $urgenteFilter);
    $totalPages = ceil($total / $limit);
    $items = $model->getAll($search, $especieFilter, $estatusFilter, $urgenteFilter, $page, $limit);
    
    $formatted = [];
    foreach ($items as $m) {
        $formatted[] = [
            'id' => $m['id'],
            'nombre' => $m['nombre'],
            'nombre_interno' => $m['nombre_interno'],
            'especie_nombre' => $m['especie_nombre'],
            'raza_nombre' => $m['raza_nombre'],
            'especie_icono' => $m['especie_icono'],
            'genero' => $m['genero'] === 'M' ? 'Macho' : ($m['genero'] === 'F' ? 'Hembra' : 'ND'),
            'edad_aprox_meses' => $m['edad_aprox_meses'],
            'peso_kg' => $m['peso_kg'],
            'estatus' => $m['estatus'],
            'urgente' => $m['urgente'],
            'vacunado' => $m['vacunado'],
            'esterilizado' => $m['esterilizado'],
            'microchip' => $m['microchip'],
            'foto_principal' => $m['foto_principal']
        ];
    }
    
    echo json_encode([
        'success' => true,
        'count' => count($formatted),
        'total' => $total,
        'current_page' => $page,
        'total_pages' => $totalPages,
        'mascotas' => $formatted
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error: ' . $e->getMessage()]);
}