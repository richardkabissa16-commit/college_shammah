<?php
require_once 'Controller.php';
require_once __DIR__ . '/../models/Eleve.php';
require_once __DIR__ . '/../models/Classe.php';
require_once __DIR__ . '/../models/AnneeScolaire.php';

class EleveController extends Controller {
    private $eleveModel;
    private $classeModel;
    private $anneeScolaireModel;

    public function __construct() {
        parent::__construct();
        $this->eleveModel = new Eleve();
        $this->classeModel = new Classe();
        $this->anneeScolaireModel = new AnneeScolaire();
    }

    // Lister tous les élèves
    public function index() {
        $eleves = $this->eleveModel->getAll();
        $classes = $this->classeModel->getAll();
        $stats = $this->eleveModel->getStats();
        
        $this->render('eleves/liste', [
            'eleves' => $eleves,
            'classes' => $classes,
            'stats' => $stats
        ]);
    }

    // Afficher le formulaire de création
    public function create() {
        $classes = $this->classeModel->getAll();
        $annee_scolaire_active = $this->getAnneeScolaireActive(); // hérité du parent
        
        $this->render('eleves/formulaire', [
            'eleve' => null,
            'classes' => $classes,
            'annee_scolaire' => $annee_scolaire_active,
            'action' => 'create'
        ]);
    }

    // Enregistrer un nouvel élève
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $errors = $this->validateEleveData($_POST);

            if (empty($errors)) {
                try {
                    $success = $this->eleveModel->create($_POST);

                    if ($success) {
                        $_SESSION['success'] = "Élève inscrit avec succès";
                        redirect('/college_shammah/public/eleves.php');
                    } else {
                        $_SESSION['error'] = "Erreur lors de l'inscription de l'élève";
                    }
                } catch (Exception $e) {
                    $_SESSION['error'] = "Erreur: " . $e->getMessage();
                }
            } else {
                $_SESSION['error'] = implode('<br>', $errors);
                $_SESSION['form_data'] = $_POST;
            }

            redirect('/college_shammah/public/eleves.php?action=create');
        }
    }

    // Afficher le formulaire d’édition
    public function edit() {
        $id = $_GET['id'] ?? '';

        if (!$id) {
            $_SESSION['error'] = "Élève non spécifié";
            redirect('/college_shammah/public/eleves.php');
        }

        $eleve = $this->eleveModel->getById($id);
        $classes = $this->classeModel->getAll();
        $annee_scolaire_active = $this->getAnneeScolaireActive();

        if (!$eleve) {
            $_SESSION['error'] = "Élève non trouvé";
            redirect('/college_shammah/public/eleves.php');
        }

        $this->render('eleves/formulaire', [
            'eleve' => $eleve,
            'classes' => $classes,
            'annee_scolaire' => $annee_scolaire_active,
            'action' => 'edit'
        ]);
    }

    // Mettre à jour un élève
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $id = $_POST['id'] ?? '';

            if (!$id) {
                $_SESSION['error'] = "Élève non spécifié";
                redirect('/college_shammah/public/eleves.php');
            }

            $errors = $this->validateEleveData($_POST, false);

            if (empty($errors)) {
                try {
                    $success = $this->eleveModel->update($id, $_POST);

                    if ($success) {
                        $_SESSION['success'] = "Élève modifié avec succès";
                        redirect('/college_shammah/public/eleves.php');
                    } else {
                        $_SESSION['error'] = "Erreur lors de la modification de l'élève";
                    }
                } catch (Exception $e) {
                    $_SESSION['error'] = "Erreur: " . $e->getMessage();
                }
            } else {
                $_SESSION['error'] = implode('<br>', $errors);
            }

            redirect('/college_shammah/public/eleves.php?action=edit&id=' . $id);
        }
    }

    // Supprimer un élève (archiver)
    public function delete() {
        $id = $_GET['id'] ?? '';

        if (!$id) {
            $_SESSION['error'] = "Élève non spécifié";
            redirect('/college_shammah/public/eleves.php');
        }

        try {
            $success = $this->eleveModel->delete($id);

            if ($success) {
                $_SESSION['success'] = "Élève archivé avec succès";
            } else {
                $_SESSION['error'] = "Erreur lors de l'archivage de l'élève";
            }
        } catch (Exception $e) {
            $_SESSION['error'] = "Erreur: " . $e->getMessage();
        }

        redirect('/college_shammah/public/eleves.php');
    }

    // Afficher les détails d’un élève
    public function show() {
        $id = $_GET['id'] ?? '';

        if (!$id) {
            $_SESSION['error'] = "Élève non spécifié";
            redirect('/college_shammah/public/eleves.php');
        }

        $eleve = $this->eleveModel->getById($id);

        if (!$eleve) {
            $_SESSION['error'] = "Élève non trouvé";
            redirect('/college_shammah/public/eleves.php');
        }

        $notes = $this->getNotesEleve($id);

        $this->render('eleves/details', [
            'eleve' => $eleve,
            'notes' => $notes
        ]);
    }

    // Lister les élèves par classe
    public function byClasse() {
        $classe_id = $_GET['classe_id'] ?? '';

        if (!$classe_id) {
            $_SESSION['error'] = "Classe non spécifiée";
            redirect('/college_shammah/public/eleves.php');
        }

        $eleves = $this->eleveModel->getByClasse($classe_id);
        $classe = $this->classeModel->getById($classe_id);
        $classes = $this->classeModel->getAll();

        $this->render('eleves/liste', [
            'eleves' => $eleves,
            'classes' => $classes,
            'classe_filtre' => $classe,
            'stats' => null
        ]);
    }

    // Validation des données élève
    private function validateEleveData($data, $is_create = true) {
        $errors = [];
        $required = ['nom', 'prenom', 'date_naissance', 'sexe', 'adresse', 'classe_id'];

        if ($is_create) {
            $required[] = 'lieu_naissance';
        }

        foreach ($required as $field) {
            if (empty($data[$field])) {
                $errors[] = "Le champ " . $this->getFieldName($field) . " est requis";
            }
        }

        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "L'adresse email n'est pas valide";
        }

        if (!empty($data['date_naissance'])) {
            $date_naissance = DateTime::createFromFormat('Y-m-d', $data['date_naissance']);
            $today = new DateTime();
            $age = $today->diff($date_naissance)->y;

            if ($age < 5 || $age > 25) {
                $errors[] = "L'âge doit être entre 5 et 25 ans";
            }
        }

        return $errors;
    }

    // Libellé des champs
    private function getFieldName($field) {
        $fields = [
            'nom' => 'Nom',
            'prenom' => 'Prénom',
            'date_naissance' => 'Date de naissance',
            'lieu_naissance' => 'Lieu de naissance',
            'sexe' => 'Sexe',
            'adresse' => 'Adresse',
            'classe_id' => 'Classe'
        ];

        return $fields[$field] ?? $field;
    }

    // Notes d’un élève
    private function getNotesEleve($eleve_id) {
        $query = "SELECT n.*, m.nom as matiere_nom, m.code, tn.libelle as type_note
                  FROM notes n
                  JOIN matieres m ON n.matiere_id = m.id
                  JOIN types_notes tn ON n.type_note_id = tn.id
                  WHERE n.eleve_id = :eleve_id
                  ORDER BY n.trimestre DESC, n.date_saisie DESC
                  LIMIT 20";

        $stmt = $this->db->prepare($query);
        $stmt->execute([':eleve_id' => $eleve_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 🚫 SUPPRESSION DE LA MÉTHODE getAnneeScolaireActive()
    // Elle existe déjà dans Controller.php en protected
}
?>
