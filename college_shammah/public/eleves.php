<?php
require_once '../config/config.php';
requireAuth();

require_once '../src/controllers/EleveController.php';

$action = $_GET['action'] ?? 'index';
$controller = new EleveController();

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
    case 'edit':
        $controller->edit();
        break;
    case 'update':
        $controller->update();
        break;
    case 'delete':
        $controller->delete();
        break;
    case 'show':
        $controller->show();
        break;
    case 'by_classe':
        $controller->byClasse();
        break;
    default:
        $controller->index();
        break;
}
?>