<?php
require_once 'Controller.php';
require_once __DIR__ . '/../models/Enseignant.php';
require_once __DIR__ . '/../models/Matiere.php';

class EnseignantController extends Controller {
    private $enseignantModel;
    private $matiereModel;

    public function __construct() {
        parent::__construct();
        $this->enseignantModel = new Enseignant();
        $this->matiereModel = new Matiere();
    }

    // Lister tous les enseignants
    public function index() {
        $enseignants = $this->enseignantModel->getActive();
        $matieres = $this->matiereModel->getActive();
        
        $this->render('enseignants/liste', [
            'enseignants' => $enseignants,
            'matieres' => $matieres
        ]);
    }

    // Afficher le formulaire de création
    public function create() {
        $matieres = $this->matiereModel->getActive();
        
        $this->render('enseignants/formulaire', [
            'enseignant' => null,
            'matieres' => $matieres,
            'action' => 'create'
        ]);
    }

    // Enregistrer un nouvel enseignant
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = $this->validateEnseignantData($_POST);
            
            if (empty($errors)) {
                try {
                    $success = $this->enseignantModel->create($_POST);
                    
                    if ($success) {
                        $_SESSION['success'] = "Enseignant ajouté avec succès";
                        redirect('/college_shammah/public/enseignants.php');
                    } else {
                        $_SESSION['error'] = "Erreur lors de l'ajout de l'enseignant";
                    }
                } catch (Exception $e) {
                    $_SESSION['error'] = "Erreur: " . $e->getMessage();
                }
            } else {
                $_SESSION['error'] = implode('<br>', $errors);
            }
            
            $_SESSION['form_data'] = $_POST;
            redirect('/college_shammah/public/enseignants.php?action=create');
        }
    }

    // Afficher le formulaire d'édition
    public function edit() {
        $id = $_GET['id'] ?? '';
        
        if (!$id) {
            $_SESSION['error'] = "Enseignant non spécifié";
            redirect('/college_shammah/public/enseignants.php');
        }
        
        $enseignant = $this->enseignantModel->getById($id);
        $matieres = $this->matiereModel->getActive();
        
        if (!$enseignant) {
            $_SESSION['error'] = "Enseignant non trouvé";
            redirect('/college_shammah/public/enseignants.php');
        }
        
        $this->render('enseignants/formulaire', [
            'enseignant' => $enseignant,
            'matieres' => $matieres,
            'action' => 'edit'
        ]);
    }

    // Mettre à jour un enseignant
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? '';
            
            if (!$id) {
                $_SESSION['error'] = "Enseignant non spécifié";
                redirect('/college_shammah/public/enseignants.php');
            }
            
            $errors = $this->validateEnseignantData($_POST, false);
            
            if (empty($errors)) {
                try {
                    $success = $this->enseignantModel->update($id, $_POST);
                    
                    if ($success) {
                        $_SESSION['success'] = "Enseignant modifié avec succès";
                        redirect('/college_shammah/public/enseignants.php');
                    } else {
                        $_SESSION['error'] = "Erreur lors de la modification de l'enseignant";
                    }
                } catch (Exception $e) {
                    $_SESSION['error'] = "Erreur: " . $e->getMessage();
                }
            } else {
                $_SESSION['error'] = implode('<br>', $errors);
            }
            
            redirect('/college_shammah/public/enseignants.php?action=edit&id=' . $id);
        }
    }

    // Désactiver un enseignant
    public function delete() {
        $id = $_GET['id'] ?? '';
        
        if (!$id) {
            $_SESSION['error'] = "Enseignant non spécifié";
            redirect('/college_shammah/public/enseignants.php');
        }
        
        try {
            $query = "UPDATE enseignants SET est_actif = 0 WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $success = $stmt->execute([':id' => $id]);
            
            if ($success) {
                $_SESSION['success'] = "Enseignant désactivé avec succès";
            } else {
                $_SESSION['error'] = "Erreur lors de la désactivation de l'enseignant";
            }
        } catch (Exception $e) {
            $_SESSION['error'] = "Erreur: " . $e->getMessage();
        }
        
        redirect('/college_shammah/public/enseignants.php');
    }

    // Valider les données enseignant
    private function validateEnseignantData($data, $is_create = true) {
        $errors = [];
        $required = ['nom', 'prenom', 'specialite'];
        
        foreach ($required as $field) {
            if (empty($data[$field])) {
                $errors[] = "Le champ " . $this->getFieldName($field) . " est requis";
            }
        }
        
        // Validation email
        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "L'adresse email n'est pas valide";
        }
        
        // Validation téléphone
        if (!empty($data['telephone']) && !preg_match('/^[0-9+\-\s()]{8,20}$/', $data['telephone'])) {
            $errors[] = "Le numéro de téléphone n'est pas valide";
        }
        
        return $errors;
    }

    // Obtenir le libellé d'un champ
    private function getFieldName($field) {
        $fields = [
            'nom' => 'Nom',
            'prenom' => 'Prénom',
            'specialite' => 'Spécialité',
            'telephone' => 'Téléphone',
            'email' => 'Email'
        ];
        
        return $fields[$field] ?? $field;
    }
}
?>