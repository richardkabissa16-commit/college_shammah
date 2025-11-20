<?php
require_once 'Controller.php';
require_once __DIR__ . '/../models/Matiere.php';

class MatiereController extends Controller {
    private $matiereModel;

    public function __construct() {
        parent::__construct();
        $this->matiereModel = new Matiere();
    }

    // Lister toutes les matières
    public function index() {
        $matieres = $this->matiereModel->getActive();
        
        $this->render('matieres/liste', [
            'matieres' => $matieres
        ]);
    }

    // Afficher le formulaire de création
    public function create() {
        $this->render('matieres/formulaire', [
            'matiere' => null,
            'action' => 'create'
        ]);
    }

    // Enregistrer une nouvelle matière
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = $this->validateMatiereData($_POST);
            
            if (empty($errors)) {
                try {
                    $success = $this->createMatiere($_POST);
                    
                    if ($success) {
                        $_SESSION['success'] = "Matière créée avec succès";
                        redirect('/college_shammah/public/matieres.php');
                    } else {
                        $_SESSION['error'] = "Erreur lors de la création de la matière";
                    }
                } catch (Exception $e) {
                    $_SESSION['error'] = "Erreur: " . $e->getMessage();
                }
            } else {
                $_SESSION['error'] = implode('<br>', $errors);
            }
            
            $_SESSION['form_data'] = $_POST;
            redirect('/college_shammah/public/matieres.php?action=create');
        }
    }

    // Afficher le formulaire d'édition
    public function edit() {
        $id = $_GET['id'] ?? '';
        
        if (!$id) {
            $_SESSION['error'] = "Matière non spécifiée";
            redirect('/college_shammah/public/matieres.php');
        }
        
        $matiere = $this->matiereModel->getById($id);
        
        if (!$matiere) {
            $_SESSION['error'] = "Matière non trouvée";
            redirect('/college_shammah/public/matieres.php');
        }
        
        $this->render('matieres/formulaire', [
            'matiere' => $matiere,
            'action' => 'edit'
        ]);
    }

    // Mettre à jour une matière
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? '';
            
            if (!$id) {
                $_SESSION['error'] = "Matière non spécifiée";
                redirect('/college_shammah/public/matieres.php');
            }
            
            $errors = $this->validateMatiereData($_POST, false);
            
            if (empty($errors)) {
                try {
                    $success = $this->updateMatiere($id, $_POST);
                    
                    if ($success) {
                        $_SESSION['success'] = "Matière modifiée avec succès";
                        redirect('/college_shammah/public/matieres.php');
                    } else {
                        $_SESSION['error'] = "Erreur lors de la modification de la matière";
                    }
                } catch (Exception $e) {
                    $_SESSION['error'] = "Erreur: " . $e->getMessage();
                }
            } else {
                $_SESSION['error'] = implode('<br>', $errors);
            }
            
            redirect('/college_shammah/public/matieres.php?action=edit&id=' . $id);
        }
    }

    // Désactiver une matière
    public function delete() {
        $id = $_GET['id'] ?? '';
        
        if (!$id) {
            $_SESSION['error'] = "Matière non spécifiée";
            redirect('/college_shammah/public/matieres.php');
        }
        
        try {
            // Vérifier si la matière est utilisée dans des notes
            $query = "SELECT COUNT(*) as count FROM notes WHERE matiere_id = :matiere_id";
            $stmt = $this->db->prepare($query);
            $stmt->execute([':matiere_id' => $id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result['count'] > 0) {
                $_SESSION['error'] = "Impossible de supprimer cette matière car elle est utilisée dans des notes";
                redirect('/college_shammah/public/matieres.php');
            }
            
            $query = "UPDATE matieres SET est_active = 0 WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $success = $stmt->execute([':id' => $id]);
            
            if ($success) {
                $_SESSION['success'] = "Matière désactivée avec succès";
            } else {
                $_SESSION['error'] = "Erreur lors de la désactivation de la matière";
            }
        } catch (Exception $e) {
            $_SESSION['error'] = "Erreur: " . $e->getMessage();
        }
        
        redirect('/college_shammah/public/matieres.php');
    }

    // Valider les données matière
    private function validateMatiereData($data, $is_create = true) {
        $errors = [];
        $required = ['nom', 'code', 'categorie', 'coefficient'];
        
        foreach ($required as $field) {
            if (empty($data[$field])) {
                $errors[] = "Le champ " . $this->getFieldName($field) . " est requis";
            }
        }
        
        // Validation du coefficient
        if (!empty($data['coefficient']) && (!is_numeric($data['coefficient']) || $data['coefficient'] <= 0)) {
            $errors[] = "Le coefficient doit être un nombre positif";
        }
        
        // Validation du code (doit être unique)
        if ($is_create) {
            $query = "SELECT id FROM matieres WHERE code = :code AND est_active = 1";
            $stmt = $this->db->prepare($query);
            $stmt->execute([':code' => $data['code']]);
            if ($stmt->fetch()) {
                $errors[] = "Une matière avec ce code existe déjà";
            }
        }
        
        return $errors;
    }

    // Créer une matière
    private function createMatiere($data) {
        $query = "INSERT INTO matieres (nom, code, categorie, coefficient) 
                 VALUES (:nom, :code, :categorie, :coefficient)";
        
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            ':nom' => $data['nom'],
            ':code' => $data['code'],
            ':categorie' => $data['categorie'],
            ':coefficient' => $data['coefficient']
        ]);
    }

    // Mettre à jour une matière
    private function updateMatiere($id, $data) {
        $query = "UPDATE matieres SET nom = :nom, code = :code, categorie = :categorie, coefficient = :coefficient 
                 WHERE id = :id";
        
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            ':nom' => $data['nom'],
            ':code' => $data['code'],
            ':categorie' => $data['categorie'],
            ':coefficient' => $data['coefficient'],
            ':id' => $id
        ]);
    }

    // Obtenir le libellé d'un champ
    private function getFieldName($field) {
        $fields = [
            'nom' => 'Nom',
            'code' => 'Code',
            'categorie' => 'Catégorie',
            'coefficient' => 'Coefficient'
        ];
        
        return $fields[$field] ?? $field;
    }
}
?>