<?php
require_once __DIR__ . '/../../config/config.php';

class Controller {
    protected $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    protected function render($view, $data = []) {
        // Extraire les données pour les rendre accessibles dans la vue
        extract($data);
        
        // Chemin vers le fichier de vue
        $view_file = __DIR__ . '/../views/' . $view . '.php';
        
        if (file_exists($view_file)) {
            require_once $view_file;
        } else {
            throw new Exception("Vue non trouvée: " . $view);
        }
    }

    protected function jsonResponse($data, $status = 200) {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    // Fonction de redirection
    protected function redirect($url, $statusCode = 303) {
        header('Location: ' . $url, true, $statusCode);
        exit();
    }

    protected function validateRequired($data, $requiredFields) {
        $errors = [];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                $errors[] = "Le champ $field est requis";
            }
        }
        return $errors;
    }

    protected function sanitizeInput($input) {
        if (is_array($input)) {
            return array_map([$this, 'sanitizeInput'], $input);
        }
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }

    protected function getAnneeScolaireActive() {
        $query = "SELECT * FROM annees_scolaires WHERE est_active = 1 LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    protected function formatDate($date) {
        $timestamp = strtotime($date);
        $now = time();
        $diff = $now - $timestamp;

        if ($diff < 60) {
            return 'Il y a ' . $diff . ' secondes';
        } elseif ($diff < 3600) {
            return 'Il y a ' . floor($diff / 60) . ' minutes';
        } elseif ($diff < 86400) {
            return 'Il y a ' . floor($diff / 3600) . ' heures';
        } else {
            return 'Il y a ' . floor($diff / 86400) . ' jours';
        }
    }

    protected function calculateAge($date_naissance) {
        if (empty($date_naissance)) {
            return null;
        }

        try {
            $date = new DateTime($date_naissance);
            $today = new DateTime();
            return $today->diff($date)->y;
        } catch (Exception $e) {
            return null;
        }
    }
}
?>