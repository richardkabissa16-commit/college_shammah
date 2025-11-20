<?php
require_once '../config/config.php';
requireAuth();

require_once '../src/controllers/BulletinController.php';

$action = $_GET['action'] ?? 'index';
$controller = new BulletinController();

switch ($action) {
    case 'index':
        $controller->index();
        break;
    case 'generer':
        $controller->generer();
        break;
    case 'resultats':
        $controller->resultats();
        break;
    case 'pdf':
        $controller->pdf();
        break;
    case 'download_zip':
        $controller->downloadZip();
        break;
    default:
        $controller->index();
        break;
}
?>