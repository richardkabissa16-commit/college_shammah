<?php
require_once __DIR__ . '/../config/config.php';
requireAuth();

require_once __DIR__ . '/../src/controllers/ClasseController.php';

$action = $_GET['action'] ?? 'index';
$controller = new ClasseController();

switch ($action) {
    case 'index':
        $controller->index();
        break;
    case 'create':
        $controller->create();
        break;
    case 'store':
        $controller->store();
        break;
    case 'show':
        $controller->show();
        break;
    case 'edit':
        $controller->edit();
        break;
    case 'update':
        $controller->update();
        break;
    case 'delete':
        $controller->delete();
        break;
    default:
        $controller->index();
        break;
}
?>