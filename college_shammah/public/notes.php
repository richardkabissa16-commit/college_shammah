<?php
require_once '../config/config.php';
requireAuth();

require_once '../src/controllers/NoteController.php';

$action = $_GET['action'] ?? 'saisie';
$controller = new NoteController();

switch ($action) {
    case 'saisie':
        $controller->saisie();
        break;
    case 'enregistrer':
        $controller->enregistrer();
        break;
    case 'visualiser':
        $controller->visualiser();
        break;
    case 'gestion':
        $controller->gestion();
        break;
    case 'supprimer':
        $controller->supprimer();
        break;
    default:
        $controller->saisie();
        break;
}
?>