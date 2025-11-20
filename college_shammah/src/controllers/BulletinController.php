<?php
require_once 'Controller.php';
require_once __DIR__ . '/../models/Bulletin.php';
require_once __DIR__ . '/../models/Eleve.php';
require_once __DIR__ . '/../models/Classe.php';
require_once __DIR__ . '/../models/Note.php';
require_once __DIR__ . '/../utils/PDFGenerator.php';

class BulletinController extends Controller {
    private $bulletinModel;
    private $eleveModel;
    private $classeModel;
    private $noteModel;

    public function __construct() {
        parent::__construct();
        $this->bulletinModel = new Bulletin();
        $this->eleveModel = new Eleve();
        $this->classeModel = new Classe();
        $this->noteModel = new Note();
    }

    // Page principale des bulletins
    public function index() {
        $classes = $this->classeModel->getAll();
        $annee_scolaire = $this->getAnneeScolaireActive();
        
        $this->render('bulletins/generation', [
            'classes' => $classes,
            'annee_scolaire' => $annee_scolaire
        ]);
    }

    // Générer les bulletins pour une classe
    public function generer() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $classe_id = $_POST['classe_id'] ?? '';
            $trimestre = $_POST['trimestre'] ?? '';
            $annee_scolaire_id = $_POST['annee_scolaire_id'] ?? '';
            
            try {
                if (empty($classe_id) || empty($trimestre)) {
                    throw new Exception("Veuillez sélectionner une classe et un trimestre");
                }

                // Obtenir les élèves de la classe
                $eleves = $this->eleveModel->getByClasse($classe_id);
                $bulletins_generes = [];
                
                foreach ($eleves as $eleve) {
                    try {
                        // Vérifier si le bulletin existe déjà
                        $bulletin_existant = $this->bulletinModel->bulletinExists($eleve['id'], $trimestre, $annee_scolaire_id);
                        
                        if ($bulletin_existant) {
                            $bulletins_generes[] = [
                                'eleve' => $eleve['nom'] . ' ' . $eleve['prenom'],
                                'statut' => 'existant',
                                'bulletin_id' => $bulletin_existant['id']
                            ];
                            continue;
                        }

                        // Calculer les moyennes
                        $moyennes = $this->calculerMoyennesEleve($eleve['id'], $trimestre, $annee_scolaire_id);
                        
                        if ($moyennes['generale'] == 0) {
                            $bulletins_generes[] = [
                                'eleve' => $eleve['nom'] . ' ' . $eleve['prenom'],
                                'statut' => 'sans_notes',
                                'message' => 'Aucune note pour ce trimestre'
                            ];
                            continue;
                        }

                        // Déterminer le rang
                        $rang = $this->determinerRang($eleve['id'], $classe_id, $trimestre, $annee_scolaire_id, $moyennes['generale']);
                        
                        // Déterminer l'appréciation
                        $appreciation = $this->getAppreciationNote($moyennes['generale']);
                        $decision = $moyennes['generale'] >= 10 ? 'Admis' : 'Redouble';

                        // Obtenir les statistiques de la classe
                        $stats_classe = $this->getStatsClasse($classe_id, $trimestre, $annee_scolaire_id);

                        // Créer le bulletin
                        $bulletin_id = $this->creerBulletin([
                            'eleve_id' => $eleve['id'],
                            'annee_scolaire_id' => $annee_scolaire_id,
                            'trimestre' => $trimestre,
                            'moyenne_generale' => $moyennes['generale'],
                            'rang' => $rang,
                            'appreciation' => $appreciation,
                            'decision' => $decision,
                            'stats_classe' => $stats_classe
                        ]);

                        $bulletins_generes[] = [
                            'eleve' => $eleve['nom'] . ' ' . $eleve['prenom'],
                            'statut' => 'generé',
                            'bulletin_id' => $bulletin_id,
                            'moyenne' => $moyennes['generale']
                        ];

                    } catch (Exception $e) {
                        $bulletins_generes[] = [
                            'eleve' => $eleve['nom'] . ' ' . $eleve['prenom'],
                            'statut' => 'erreur',
                            'message' => $e->getMessage()
                        ];
                    }
                }

                $_SESSION['bulletins_generes'] = $bulletins_generes;
                $_SESSION['success'] = "Génération des bulletins terminée";
                redirect('/college_shammah/public/bulletins.php?action=resultats&classe_id=' . $classe_id . '&trimestre=' . $trimestre);
                
            } catch (Exception $e) {
                $_SESSION['error'] = "Erreur lors de la génération: " . $e->getMessage();
                redirect('/college_shammah/public/bulletins.php');
            }
        }
    }

    // Afficher les résultats de génération
    public function resultats() {
        $classe_id = $_GET['classe_id'] ?? '';
        $trimestre = $_GET['trimestre'] ?? '1';
        $annee_scolaire_id = $this->getAnneeScolaireActive()['id'];
        
        $bulletins = $this->bulletinModel->getByClasse($classe_id, $trimestre, $annee_scolaire_id);
        $classe = $this->classeModel->getById($classe_id);
        $bulletins_generes = $_SESSION['bulletins_generes'] ?? [];
        unset($_SESSION['bulletins_generes']);
        
        $this->render('bulletins/resultats', [
            'bulletins' => $bulletins,
            'bulletins_generes' => $bulletins_generes,
            'classe' => $classe,
            'trimestre' => $trimestre
        ]);
    }

    // Générer le PDF d'un bulletin
    public function pdf() {
        $bulletin_id = $_GET['id'] ?? '';
        
        if (!$bulletin_id) {
            $_SESSION['error'] = "Bulletin non spécifié";
            redirect('/college_shammah/public/bulletins.php');
        }
        
        try {
            $bulletin_complet = $this->getBulletinComplet($bulletin_id);
            
            if (!$bulletin_complet) {
                throw new Exception("Bulletin non trouvé");
            }
            
            $pdfGenerator = new PDFGenerator();
            $pdf_data = $pdfGenerator->genererBulletinPDF($bulletin_complet);
            
            $filename = "Bulletin_" . $bulletin_complet['eleve']['nom'] . "_" . $bulletin_complet['eleve']['prenom'] . "_T" . $bulletin_complet['bulletin']['trimestre'] . ".pdf";
            $pdfGenerator->showPDF($pdf_data, $filename);
            
        } catch (Exception $e) {
            $_SESSION['error'] = "Erreur: " . $e->getMessage();
            redirect('/college_shammah/public/bulletins.php');
        }
    }

    // Télécharger tous les bulletins d'une classe en ZIP
    public function downloadZip() {
        $classe_id = $_GET['classe_id'] ?? '';
        $trimestre = $_GET['trimestre'] ?? '1';
        $annee_scolaire_id = $this->getAnneeScolaireActive()['id'];
        
        try {
            $bulletins = $this->bulletinModel->getByClasse($classe_id, $trimestre, $annee_scolaire_id);
            $classe = $this->classeModel->getById($classe_id);
            
            if (empty($bulletins)) {
                throw new Exception("Aucun bulletin à télécharger");
            }

            // Créer un ZIP temporaire
            $zip = new ZipArchive();
            $zip_filename = tempnam(sys_get_temp_dir(), 'bulletins_') . '.zip';
            
            if ($zip->open($zip_filename, ZipArchive::CREATE) === TRUE) {
                $pdfGenerator = new PDFGenerator();
                
                foreach ($bulletins as $bulletin) {
                    $bulletin_complet = $this->getBulletinComplet($bulletin['id']);
                    $pdf_data = $pdfGenerator->genererBulletinPDF($bulletin_complet);
                    
                    $filename = "Bulletin_" . $bulletin['nom'] . "_" . $bulletin['prenom'] . "_T" . $trimestre . ".pdf";
                    $zip->addFromString($filename, $pdf_data);
                }
                
                $zip->close();
                
                // Télécharger le ZIP
                header('Content-Type: application/zip');
                header('Content-Disposition: attachment; filename="bulletins_' . $classe['nom'] . '_T' . $trimestre . '.zip"');
                header('Content-Length: ' . filesize($zip_filename));
                readfile($zip_filename);
                
                // Supprimer le fichier temporaire
                unlink($zip_filename);
                exit;
                
            } else {
                throw new Exception("Impossible de créer le fichier ZIP");
            }
            
        } catch (Exception $e) {
            $_SESSION['error'] = "Erreur: " . $e->getMessage();
            redirect('/college_shammah/public/bulletins.php');
        }
    }

    // Méthodes utilitaires privées
    private function calculerMoyennesEleve($eleve_id, $trimestre, $annee_scolaire_id) {
        $matieres = $this->getMatieresAvecCoefficients();
        $resultats = [];
        $total_points = 0;
        $total_coefficients = 0;

        foreach ($matieres as $matiere) {
            // Calculer moyenne de la matière
            $moyenne_matiere = $this->noteModel->calculerMoyenneMatiere($eleve_id, $matiere['id'], $trimestre, $annee_scolaire_id);
            
            if ($moyenne_matiere && $moyenne_matiere['moyenne'] > 0) {
                $moyenne = round($moyenne_matiere['moyenne'], 2);
                $note_definitive = $moyenne * $matiere['coefficient'];
                
                $resultats['matieres'][] = [
                    'matiere_id' => $matiere['id'],
                    'matiere_nom' => $matiere['nom'],
                    'matiere_code' => $matiere['code'],
                    'categorie' => $matiere['categorie'],
                    'moyenne_devoir' => $moyenne,
                    'note_composition' => $moyenne, // Simplifié pour l'exemple
                    'moyenne_trimestre' => $moyenne,
                    'coefficient' => $matiere['coefficient'],
                    'note_definitive' => $note_definitive,
                    'appreciation' => $this->getAppreciationNote($moyenne)
                ];

                $total_points += $note_definitive;
                $total_coefficients += $matiere['coefficient'];
            }
        }

        $resultats['moyenne_generale'] = $total_coefficients > 0 ? round($total_points / $total_coefficients, 2) : 0;
        
        return $resultats;
    }

    private function determinerRang($eleve_id, $classe_id, $trimestre, $annee_scolaire_id, $moyenne_eleve) {
        // Calculer les moyennes de tous les élèves de la classe
        $query = "SELECT e.id, 
                         (SELECT AVG(n.note) FROM notes n 
                          WHERE n.eleve_id = e.id AND n.trimestre = :trimestre 
                          AND n.annee_scolaire_id = :annee_scolaire_id) as moyenne
                  FROM eleves e 
                  WHERE e.classe_id = :classe_id AND e.est_archive = 0
                  HAVING moyenne IS NOT NULL
                  ORDER BY moyenne DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            ':classe_id' => $classe_id,
            ':trimestre' => $trimestre,
            ':annee_scolaire_id' => $annee_scolaire_id
        ]);
        
        $eleves = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Trouver le rang
        foreach ($eleves as $index => $e) {
            if ($e['id'] == $eleve_id) {
                return $index + 1;
            }
        }
        
        return null;
    }

    private function getStatsClasse($classe_id, $trimestre, $annee_scolaire_id) {
        $query = "SELECT 
                 MIN(b.moyenne_generale) as moyenne_min,
                 MAX(b.moyenne_generale) as moyenne_max,
                 AVG(b.moyenne_generale) as moyenne_generale
                 FROM bulletins b
                 JOIN eleves e ON b.eleve_id = e.id
                 WHERE e.classe_id = :classe_id 
                 AND b.trimestre = :trimestre 
                 AND b.annee_scolaire_id = :annee_scolaire_id";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            ':classe_id' => $classe_id,
            ':trimestre' => $trimestre,
            ':annee_scolaire_id' => $annee_scolaire_id
        ]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC) ?? [
            'moyenne_min' => 0,
            'moyenne_max' => 0,
            'moyenne_generale' => 0
        ];
    }

    private function creerBulletin($data) {
        $query = "INSERT INTO bulletins 
                 (eleve_id, annee_scolaire_id, trimestre, moyenne_generale, rang, appreciation, decision, 
                  moyenne_min_classe, moyenne_max_classe, moyenne_generale_classe) 
                 VALUES (:eleve_id, :annee_scolaire_id, :trimestre, :moyenne_generale, :rang, :appreciation, :decision,
                         :moyenne_min, :moyenne_max, :moyenne_generale_classe)";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            ':eleve_id' => $data['eleve_id'],
            ':annee_scolaire_id' => $data['annee_scolaire_id'],
            ':trimestre' => $data['trimestre'],
            ':moyenne_generale' => $data['moyenne_generale'],
            ':rang' => $data['rang'],
            ':appreciation' => $data['appreciation'],
            ':decision' => $data['decision'],
            ':moyenne_min' => $data['stats_classe']['moyenne_min'],
            ':moyenne_max' => $data['stats_classe']['moyenne_max'],
            ':moyenne_generale_classe' => $data['stats_classe']['moyenne_generale']
        ]);

        $bulletin_id = $this->db->lastInsertId();

        // Créer les détails du bulletin
        $this->creerDetailsBulletin($bulletin_id, $data['moyennes']['matieres']);

        return $bulletin_id;
    }

    private function creerDetailsBulletin($bulletin_id, $matieres) {
        $query = "INSERT INTO details_bulletins 
                 (bulletin_id, matiere_id, moyenne_devoir, note_composition, moyenne_trimestre, coefficient, note_definitive, appreciation) 
                 VALUES (:bulletin_id, :matiere_id, :moyenne_devoir, :note_composition, :moyenne_trimestre, :coefficient, :note_definitive, :appreciation)";
        
        $stmt = $this->db->prepare($query);
        
        foreach ($matieres as $matiere) {
            $stmt->execute([
                ':bulletin_id' => $bulletin_id,
                ':matiere_id' => $matiere['matiere_id'],
                ':moyenne_devoir' => $matiere['moyenne_devoir'],
                ':note_composition' => $matiere['note_composition'],
                ':moyenne_trimestre' => $matiere['moyenne_trimestre'],
                ':coefficient' => $matiere['coefficient'],
                ':note_definitive' => $matiere['note_definitive'],
                ':appreciation' => $matiere['appreciation']
            ]);
        }
    }

    private function getBulletinComplet($bulletin_id) {
        // Récupérer les informations du bulletin
        $query = "SELECT b.*, e.*, c.nom as classe_nom, c.niveau,
                         a.libelle as annee_scolaire
                 FROM bulletins b
                 JOIN eleves e ON b.eleve_id = e.id
                 JOIN classes c ON e.classe_id = c.id
                 JOIN annees_scolaires a ON b.annee_scolaire_id = a.id
                 WHERE b.id = :bulletin_id";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([':bulletin_id' => $bulletin_id]);
        $bulletin = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$bulletin) {
            return null;
        }

        // Récupérer les détails du bulletin
        $query = "SELECT db.*, m.nom as matiere_nom, m.code as matiere_code, m.categorie
                 FROM details_bulletins db
                 JOIN matieres m ON db.matiere_id = m.id
                 WHERE db.bulletin_id = :bulletin_id
                 ORDER BY m.categorie, m.nom";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([':bulletin_id' => $bulletin_id]);
        $details = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return [
            'eleve' => [
                'id' => $bulletin['eleve_id'],
                'matricule' => $bulletin['matricule'],
                'nom' => $bulletin['nom'],
                'prenom' => $bulletin['prenom'],
                'date_naissance' => $bulletin['date_naissance'],
                'lieu_naissance' => $bulletin['lieu_naissance'],
                'adresse' => $bulletin['adresse'],
                'classe_nom' => $bulletin['classe_nom'],
                'niveau' => $bulletin['niveau']
            ],
            'bulletin' => [
                'id' => $bulletin['id'],
                'trimestre' => $bulletin['trimestre'],
                'moyenne_generale' => $bulletin['moyenne_generale'],
                'rang' => $bulletin['rang'],
                'appreciation' => $bulletin['appreciation'],
                'decision' => $bulletin['decision'],
                'moyenne_min_classe' => $bulletin['moyenne_min_classe'],
                'moyenne_max_classe' => $bulletin['moyenne_max_classe'],
                'moyenne_generale_classe' => $bulletin['moyenne_generale_classe'],
                'annee_scolaire' => $bulletin['annee_scolaire']
            ],
            'details' => $details
        ];
    }

    private function getMatieresAvecCoefficients() {
        $query = "SELECT id, nom, code, categorie, coefficient FROM matieres WHERE est_active = 1 ORDER BY categorie, nom";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>