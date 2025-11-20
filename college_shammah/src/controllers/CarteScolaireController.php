<?php
require_once 'Controller.php';
require_once __DIR__ . '/../models/CarteScolaire.php';
require_once __DIR__ . '/../models/Classe.php';
require_once __DIR__ . '/../utils/CartePDFGenerator.php';

class CarteScolaireController extends Controller {
    private $carteModel;
    private $classeModel;

    public function __construct() {
        parent::__construct();
        $this->carteModel = new CarteScolaire();
        $this->classeModel = new Classe();
    }

    // Afficher la page de gestion des cartes
    public function index() {
        $classes = $this->classeModel->getAll();
        $cartes_generees = $this->carteModel->getCartesGenerees();
        $eleves_sans_carte = $this->carteModel->getElevesSansCarte();
        
        $this->render('cartes/index', [
            'classes' => $classes,
            'cartes_generees' => $cartes_generees,
            'eleves_sans_carte' => $eleves_sans_carte
        ]);
    }

    // Générer les cartes pour une classe
    public function generer() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $classe_id = $_POST['classe_id'] ?? '';
            
            try {
                if (empty($classe_id)) {
                    throw new Exception("Veuillez sélectionner une classe");
                }

                $cartes_generes = $this->carteModel->genererCartesClasse($classe_id);
                
                $_SESSION['success'] = count($cartes_generes) . " cartes générées avec succès";
                redirect('/college_shammah/public/cartes.php?action=resultats&classe_id=' . $classe_id);
                
            } catch (Exception $e) {
                $_SESSION['error'] = "Erreur lors de la génération: " . $e->getMessage();
                redirect('/college_shammah/public/cartes.php');
            }
        }
    }

    // Afficher les résultats de génération
    public function resultats() {
        $classe_id = $_GET['classe_id'] ?? '';
        $cartes_generees = $this->carteModel->getCartesGenerees($classe_id);
        $classe = $this->classeModel->getById($classe_id);
        
        $this->render('cartes/resultats', [
            'cartes_generees' => $cartes_generees,
            'classe' => $classe
        ]);
    }

    // Générer le PDF des cartes
    public function pdf() {
        $classe_id = $_GET['classe_id'] ?? '';
        
        try {
            $cartes_generees = $this->carteModel->getCartesGenerees($classe_id);
            
            if (empty($cartes_generees)) {
                throw new Exception("Aucune carte à générer");
            }
            
            $pdfGenerator = new CartePDFGenerator();
            $pdf_data = $pdfGenerator->genererCartePDF($cartes_generees);
            
            $classe_nom = $cartes_generees[0]['classe_nom'] ?? 'toutes_classes';
            $filename = "Cartes_Scolaires_" . $classe_nom . ".pdf";
            
            $pdfGenerator->showPDF($pdf_data, $filename);
            
        } catch (Exception $e) {
            $_SESSION['error'] = "Erreur: " . $e->getMessage();
            redirect('/college_shammah/public/cartes.php');
        }
    }

    // Générer une carte individuelle
    public function pdfIndividuel() {
        $eleve_id = $_GET['eleve_id'] ?? '';
        
        try {
            if (!$eleve_id) {
                throw new Exception("Élève non spécifié");
            }

            $eleve = $this->carteModel->getById($eleve_id);
            
            if (!$eleve || !$eleve['numero_carte']) {
                throw new Exception("Carte non générée pour cet élève");
            }

            // Obtenir le nom de la classe
            $classe = $this->classeModel->getById($eleve['classe_id']);
            $eleve['classe_nom'] = $classe['nom'];
            
            $pdfGenerator = new CartePDFGenerator();
            $pdf_data = $pdfGenerator->genererCartePDF([$eleve]);
            
            $filename = "Carte_" . $eleve['nom'] . "_" . $eleve['prenom'] . ".pdf";
            $pdfGenerator->showPDF($pdf_data, $filename);
            
        } catch (Exception $e) {
            $_SESSION['error'] = "Erreur: " . $e->getMessage();
            redirect('/college_shammah/public/cartes.php');
        }
    }

    // Réinitialiser une carte
    public function reinitialiser() {
        $eleve_id = $_GET['eleve_id'] ?? '';
        
        if (!$eleve_id) {
            $_SESSION['error'] = "Élève non spécifié";
            redirect('/college_shammah/public/cartes.php');
        }
        
        try {
            $success = $this->carteModel->reinitialiserCarte($eleve_id);
            
            if ($success) {
                $_SESSION['success'] = "Carte réinitialisée avec succès";
            } else {
                $_SESSION['error'] = "Erreur lors de la réinitialisation";
            }
        } catch (Exception $e) {
            $_SESSION['error'] = "Erreur: " . $e->getMessage();
        }
        
        redirect('/college_shammah/public/cartes.php');
    }

    // Générer toutes les cartes manquantes
    public function genererToutes() {
        try {
            $eleves_sans_carte = $this->carteModel->getElevesSansCarte();
            $cartes_generes = [];

            foreach ($eleves_sans_carte as $eleve) {
                try {
                    $numero_carte = $this->carteModel->genererNumeroCarte($eleve['id']);
                    $cartes_generes[] = $eleve['id'];
                } catch (Exception $e) {
                    // Continuer avec les autres élèves
                    continue;
                }
            }

            $_SESSION['success'] = count($cartes_generes) . " cartes générées avec succès";
            redirect('/college_shammah/public/cartes.php');
            
        } catch (Exception $e) {
            $_SESSION['error'] = "Erreur: " . $e->getMessage();
            redirect('/college_shammah/public/cartes.php');
        }
    }
}
?>