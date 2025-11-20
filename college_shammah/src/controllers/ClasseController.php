<?php
require_once 'Controller.php';
require_once __DIR__ . '/../models/Classe.php';
require_once __DIR__ . '/../models/Eleve.php';

class ClasseController extends Controller {
    private $classeModel;
    private $eleveModel;

    public function __construct() {
        parent::__construct();
        $this->classeModel = new Classe();
        $this->eleveModel = new Eleve();
    }

    // Fonction de redirection
    protected function redirect($url, $statusCode = 303) {
        header('Location: ' . $url, true, $statusCode);
        exit();
    }

    // Lister toutes les classes
    public function index() {
        $classes = $this->classeModel->getStats();
        
        $this->render('classes/liste', [
            'classes' => $classes
        ]);
    }

    // Afficher les détails d'une classe
    public function show() {
        $id = $_GET['id'] ?? '';
        
        if (!$id || !is_numeric($id)) {
            $_SESSION['error'] = "ID de classe invalide";
            $this->redirect('/classes.php');
        }
        
        $classe = $this->classeModel->getById($id);
        $eleves = $this->eleveModel->getByClasse($id);
        
        if (!$classe) {
            $_SESSION['error'] = "Classe non trouvée";
            $this->redirect('/classes.php');
        }
        
        $this->render('classes/details', [
            'classe' => $classe,
            'eleves' => $eleves
        ]);
    }

    // Afficher le formulaire de création
    public function create() {
        $this->render('classes/formulaire', [
            'classe' => null,
            'action' => 'create'
        ]);
    }

    // Enregistrer une nouvelle classe
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = $this->validateClasseData($_POST);
            
            if (empty($errors)) {
                try {
                    $success = $this->createClasse($_POST);
                    
                    if ($success) {
                        $_SESSION['success'] = "Classe créée avec succès";
                        $this->redirect('/classes.php');
                    } else {
                        $_SESSION['error'] = "Erreur lors de la création de la classe";
                    }
                } catch (Exception $e) {
                    $_SESSION['error'] = "Erreur: " . $e->getMessage();
                }
            } else {
                $_SESSION['error'] = implode('<br>', $errors);
            }
            
            $_SESSION['form_data'] = $_POST;
            $this->redirect('/classes.php?action=create');
        }
    }

    // Afficher le formulaire d'édition
    public function edit() {
        $id = $_GET['id'] ?? '';
        
        if (!$id || !is_numeric($id)) {
            $_SESSION['error'] = "ID de classe invalide";
            $this->redirect('/classes.php');
        }
        
        $classe = $this->classeModel->getById($id);
        
        if (!$classe) {
            $_SESSION['error'] = "Classe non trouvée";
            $this->redirect('/classes.php');
        }
        
        $this->render('classes/formulaire', [
            'classe' => $classe,
            'action' => 'edit'
        ]);
    }

    // Mettre à jour une classe
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? '';
            
            if (!$id || !is_numeric($id)) {
                $_SESSION['error'] = "ID de classe invalide";
                $this->redirect('/classes.php');
            }
            
            $errors = $this->validateClasseData($_POST, false);
            
            if (empty($errors)) {
                try {
                    $success = $this->updateClasse($id, $_POST);
                    
                    if ($success) {
                        $_SESSION['success'] = "Classe modifiée avec succès";
                        $this->redirect('/classes.php');
                    } else {
                        $_SESSION['error'] = "Erreur lors de la modification de la classe";
                    }
                } catch (Exception $e) {
                    $_SESSION['error'] = "Erreur: " . $e->getMessage();
                }
            } else {
                $_SESSION['error'] = implode('<br>', $errors);
            }
            
            $this->redirect('/classes.php?action=edit&id=' . $id);
        }
    }

    // Désactiver une classe
    public function delete() {
        $id = $_GET['id'] ?? '';
        
        if (!$id || !is_numeric($id)) {
            $_SESSION['error'] = "ID de classe invalide";
            $this->redirect('/classes.php');
        }
        
        try {
            // Vérifier s'il y a des élèves dans la classe
            $eleves = $this->eleveModel->getByClasse($id);
            if (!empty($eleves)) {
                $_SESSION['error'] = "Impossible de désactiver cette classe car elle contient des élèves";
                $this->redirect('/classes.php');
            }
            
            $query = "UPDATE classes SET est_active = 0 WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $success = $stmt->execute([':id' => $id]);
            
            if ($success) {
                $_SESSION['success'] = "Classe désactivée avec succès";
            } else {
                $_SESSION['error'] = "Erreur lors de la désactivation de la classe";
            }
        } catch (Exception $e) {
            $_SESSION['error'] = "Erreur: " . $e->getMessage();
        }
        
        $this->redirect('/classes.php');
    }

    // Valider les données classe
    private function validateClasseData($data, $is_create = true) {
        $errors = [];
        $required = ['nom', 'niveau'];
        
        foreach ($required as $field) {
            if (empty(trim($data[$field] ?? ''))) {
                $errors[] = "Le champ " . $this->getFieldName($field) . " est requis";
            }
        }
        
        // Validation de la longueur du nom
        if (!empty($data['nom']) && strlen($data['nom']) > 10) {
            $errors[] = "Le nom de la classe ne doit pas dépasser 10 caractères";
        }
        
        // Validation du niveau
        $niveaux_valides = ['6ème', '5ème', '4ème', '3ème'];
        if (!empty($data['niveau']) && !in_array($data['niveau'], $niveaux_valides)) {
            $errors[] = "Niveau de classe invalide";
        }
        
        // Validation de l'unicité (seulement pour la création)
        if ($is_create && !empty($data['nom'])) {
            $query = "SELECT id FROM classes WHERE nom = :nom AND est_active = 1";
            $stmt = $this->db->prepare($query);
            $stmt->execute([':nom' => trim($data['nom'])]);
            if ($stmt->fetch()) {
                $errors[] = "Une classe avec ce nom existe déjà";
            }
        }
        
        return $errors;
    }

    // Créer une classe
    private function createClasse($data) {
        $query = "INSERT INTO classes (nom, niveau, effectif_max) 
                 VALUES (:nom, :niveau, :effectif_max)";
        
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            ':nom' => trim($data['nom']),
            ':niveau' => $data['niveau'],
            ':effectif_max' => $data['effectif_max'] ?? 40
        ]);
    }

    // Mettre à jour une classe
    private function updateClasse($id, $data) {
        $query = "UPDATE classes SET nom = :nom, niveau = :niveau, effectif_max = :effectif_max 
                 WHERE id = :id";
        
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            ':nom' => trim($data['nom']),
            ':niveau' => $data['niveau'],
            ':effectif_max' => $data['effectif_max'] ?? 40,
            ':id' => $id
        ]);
    }

    // Obtenir le libellé d'un champ
    private function getFieldName($field) {
        $fields = [
            'nom' => 'Nom',
            'niveau' => 'Niveau',
            'effectif_max' => 'Effectif maximum'
        ];
        
        return $fields[$field] ?? $field;
    }
}
?>