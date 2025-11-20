<?php
// Configuration de l'application
define('APP_NAME', 'Collège Shammah');
define('APP_VERSION', '1.0');
define('BASE_URL', 'http://localhost/college_shammah/public/');
define('UPLOAD_DIR', $_SERVER['DOCUMENT_ROOT'] . '/college_shammah/public/uploads/');

// Inclure la classe Database
require_once __DIR__ . '/database.php';

// Paramètres de session
session_start();

// Fonctions utilitaires
function redirect($url) {
    header("Location: $url");
    exit;
}

function isAuthenticated() {
    return isset($_SESSION['user_id']);
}

function requireAuth() {
    if (!isAuthenticated()) {
        redirect(BASE_URL . 'index.php');
    }
}

// Gestion des erreurs
function handleError($error) {
    error_log($error);
    if (isset($_SESSION['user_id'])) {
        $_SESSION['error'] = "Une erreur s'est produite. Veuillez réessayer.";
    }
}

// Autoloader simple pour les modèles
spl_autoload_register(function ($class_name) {
    $model_file = __DIR__ . '/../src/models/' . $class_name . '.php';
    $controller_file = __DIR__ . '/../src/controllers/' . $class_name . '.php';
    
    if (file_exists($model_file)) {
        require_once $model_file;
    } elseif (file_exists($controller_file)) {
        require_once $controller_file;
    }
});
?>