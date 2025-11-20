<?php
require_once '../config/config.php';
requireAuth();

require_once '../src/controllers/CarteScolaireController.php';

$action = $_GET['action'] ?? 'index';
$controller = new CarteScolaireController();

switch ($action) {
    case 'index':
        $controller->index();
        break;
    case 'generer':
        $controller->generer();
        break;
    case 'generer_toutes':
        $controller->genererToutes();
        break;
    case 'resultats':
        $controller->resultats();
        break;
    case 'pdf':
        $controller->pdf();
        break;
    case 'pdf_individuel':
        $controller->pdfIndividuel();
        break;
    case 'reinitialiser':
        $controller->reinitialiser();
        break;
    default:
        $controller->index();
        break;
}
?>