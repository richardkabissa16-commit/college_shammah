<?php
require_once 'Controller.php';
require_once __DIR__ . '/../models/Note.php';
require_once __DIR__ . '/../models/Eleve.php';
require_once __DIR__ . '/../models/Matiere.php';
require_once __DIR__ . '/../models/Classe.php';
require_once __DIR__ . '/../models/Enseignant.php';
require_once __DIR__ . '/../models/TypeNote.php';

class NoteController extends Controller {
    private $noteModel;
    private $eleveModel;
    private $matiereModel;
    private $classeModel;
    private $enseignantModel;
    private $typeNoteModel;

    public function __construct() {
        parent::__construct();
        $this->noteModel = new Note();
        $this->eleveModel = new Eleve();
        $this->matiereModel = new Matiere();
        $this->classeModel = new Classe();
        $this->enseignantModel = new Enseignant();
        $this->typeNoteModel = new TypeNote();
    }

    // Page de saisie des notes
    public function saisie() {
        $classe_id = $_GET['classe_id'] ?? '';
        $matiere_id = $_GET['matiere_id'] ?? '';
        $trimestre = $_GET['trimestre'] ?? '1';
        
        $classes = $this->classeModel->getAll();
        $matieres = $this->matiereModel->getActive();
        $types_notes = $this->typeNoteModel->getAll();
        $annee_scolaire = $this->getAnneeScolaireActive();
        
        $eleves = [];
        $notes_existantes = [];
        
        if ($classe_id && $matiere_id) {
            $eleves = $this->eleveModel->getByClasse($classe_id);
            $notes_existantes = $this->getNotesClasseMatiere($classe_id, $matiere_id, $trimestre, $annee_scolaire['id']);
        }
        
        $this->render('notes/saisie', [
            'classes' => $classes,
            'matieres' => $matieres,
            'types_notes' => $types_notes,
            'eleves' => $eleves,
            'notes_existantes' => $notes_existantes,
            'classe_selectionnee' => $classe_id,
            'matiere_selectionnee' => $matiere_id,
            'trimestre' => $trimestre,
            'annee_scolaire' => $annee_scolaire
        ]);
    }

    // Enregistrer les notes
    public function enregistrer() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $classe_id = $_POST['classe_id'] ?? '';
            $matiere_id = $_POST['matiere_id'] ?? '';
            $type_note_id = $_POST['type_note_id'] ?? '';
            $trimestre = $_POST['trimestre'] ?? '';
            $annee_scolaire_id = $_POST['annee_scolaire_id'] ?? '';
            $notes = $_POST['notes'] ?? [];
            
            try {
                $notes_enregistrees = 0;
                $errors = [];
                
                foreach ($notes as $eleve_id => $note_value) {
                    if ($note_value !== '') {
                        // Validation de la note
                        $note_value = floatval(str_replace(',', '.', $note_value));
                        
                        if ($note_value < 0 || $note_value > 20) {
                            $errors[] = "Note invalide pour l'élève ID $eleve_id: $note_value";
                            continue;
                        }
                        
                        // Vérifier si la note existe déjà
                        $note_existante = $this->getNoteExistante($eleve_id, $matiere_id, $type_note_id, $trimestre, $annee_scolaire_id);
                        
                        if ($note_existante) {
                            // Mettre à jour la note existante
                            $success = $this->updateNote($note_existante['id'], $note_value);
                        } else {
                            // Créer une nouvelle note
                            $success = $this->noteModel->addNote([
                                ':eleve_id' => $eleve_id,
                                ':matiere_id' => $matiere_id,
                                ':type_note_id' => $type_note_id,
                                ':trimestre' => $trimestre,
                                ':annee_scolaire_id' => $annee_scolaire_id,
                                ':note' => $note_value,
                                ':saisie_par' => $_SESSION['user_id']
                            ]);
                        }
                        
                        if ($success) {
                            $notes_enregistrees++;
                        }
                    }
                }
                
                if (!empty($errors)) {
                    $_SESSION['error'] = implode('<br>', $errors);
                }
                
                if ($notes_enregistrees > 0) {
                    $_SESSION['success'] = "$notes_enregistrees notes enregistrées avec succès";
                } else {
                    $_SESSION['warning'] = "Aucune note enregistrée";
                }
                
            } catch (Exception $e) {
                $_SESSION['error'] = "Erreur lors de l'enregistrement: " . $e->getMessage();
            }
            
            $params = http_build_query([
                'classe_id' => $classe_id,
                'matiere_id' => $matiere_id,
                'trimestre' => $trimestre
            ]);
            
            redirect('/college_shammah/public/notes.php?action=saisie&' . $params);
        }
    }

    // Visualiser les notes
    public function visualiser() {
        $classe_id = $_GET['classe_id'] ?? '';
        $trimestre = $_GET['trimestre'] ?? '1';
        $annee_scolaire = $this->getAnneeScolaireActive();
        
        $classes = $this->classeModel->getAll();
        $matieres = $this->matiereModel->getActive();
        
        $notes_par_eleve = [];
        $statistiques = [];
        
        if ($classe_id) {
            $eleves = $this->eleveModel->getByClasse($classe_id);
            $classe = $this->classeModel->getById($classe_id);
            
            foreach ($eleves as $eleve) {
                $notes_par_eleve[$eleve['id']] = [
                    'eleve' => $eleve,
                    'notes' => $this->getNotesEleveTrimestre($eleve['id'], $trimestre, $annee_scolaire['id']),
                    'moyennes' => $this->calculerMoyennesEleve($eleve['id'], $trimestre, $annee_scolaire['id'])
                ];
            }
            
            $statistiques = $this->calculerStatistiquesClasse($classe_id, $trimestre, $annee_scolaire['id']);
        }
        
        $this->render('notes/visualiser', [
            'classes' => $classes,
            'matieres' => $matieres,
            'notes_par_eleve' => $notes_par_eleve,
            'statistiques' => $statistiques,
            'classe_selectionnee' => $classe_id,
            'trimestre' => $trimestre,
            'annee_scolaire' => $annee_scolaire,
            'classe' => $classe ?? null
        ]);
    }

    // Gestion des notes (liste et suppression)
    public function gestion() {
        $classe_id = $_GET['classe_id'] ?? '';
        $matiere_id = $_GET['matiere_id'] ?? '';
        $trimestre = $_GET['trimestre'] ?? '';
        
        $classes = $this->classeModel->getAll();
        $matieres = $this->matiereModel->getActive();
        
        $notes = [];
        
        if ($classe_id && $matiere_id && $trimestre) {
            $notes = $this->getNotesClasseMatiereTrimestre($classe_id, $matiere_id, $trimestre);
        }
        
        $this->render('notes/gestion', [
            'classes' => $classes,
            'matieres' => $matieres,
            'notes' => $notes,
            'classe_selectionnee' => $classe_id,
            'matiere_selectionnee' => $matiere_id,
            'trimestre_selectionne' => $trimestre
        ]);
    }

    // Supprimer une note
    public function supprimer() {
        $note_id = $_GET['id'] ?? '';
        
        if (!$note_id) {
            $_SESSION['error'] = "Note non spécifiée";
            redirect('/college_shammah/public/notes.php?action=gestion');
        }
        
        try {
            $success = $this->supprimerNote($note_id);
            
            if ($success) {
                $_SESSION['success'] = "Note supprimée avec succès";
            } else {
                $_SESSION['error'] = "Erreur lors de la suppression de la note";
            }
        } catch (Exception $e) {
            $_SESSION['error'] = "Erreur: " . $e->getMessage();
        }
        
        // Rediriger vers la page précédente avec les filtres
        $referer = $_SERVER['HTTP_REFERER'] ?? '/college_shammah/public/notes.php?action=gestion';
        redirect($referer);
    }

    // Méthodes utilitaires privées
    private function getNotesClasseMatiere($classe_id, $matiere_id, $trimestre, $annee_scolaire_id) {
        $query = "SELECT n.*, e.nom, e.prenom, tn.libelle as type_note
                 FROM notes n
                 JOIN eleves e ON n.eleve_id = e.id
                 JOIN types_notes tn ON n.type_note_id = tn.id
                 WHERE e.classe_id = :classe_id 
                 AND n.matiere_id = :matiere_id
                 AND n.trimestre = :trimestre
                 AND n.annee_scolaire_id = :annee_scolaire_id
                 ORDER BY e.nom, e.prenom";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            ':classe_id' => $classe_id,
            ':matiere_id' => $matiere_id,
            ':trimestre' => $trimestre,
            ':annee_scolaire_id' => $annee_scolaire_id
        ]);
        
        $notes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Organiser par élève et type de note
        $organisees = [];
        foreach ($notes as $note) {
            $organisees[$note['eleve_id']][$note['type_note_id']] = $note;
        }
        
        return $organisees;
    }

    private function getNoteExistante($eleve_id, $matiere_id, $type_note_id, $trimestre, $annee_scolaire_id) {
        $query = "SELECT * FROM notes 
                 WHERE eleve_id = :eleve_id 
                 AND matiere_id = :matiere_id
                 AND type_note_id = :type_note_id
                 AND trimestre = :trimestre
                 AND annee_scolaire_id = :annee_scolaire_id";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            ':eleve_id' => $eleve_id,
            ':matiere_id' => $matiere_id,
            ':type_note_id' => $type_note_id,
            ':trimestre' => $trimestre,
            ':annee_scolaire_id' => $annee_scolaire_id
        ]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    private function updateNote($note_id, $note_value) {
        $query = "UPDATE notes SET note = :note, date_saisie = NOW() WHERE id = :id";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            ':note' => $note_value,
            ':id' => $note_id
        ]);
    }

    private function supprimerNote($note_id) {
        $query = "DELETE FROM notes WHERE id = :id";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([':id' => $note_id]);
    }

    private function getNotesEleveTrimestre($eleve_id, $trimestre, $annee_scolaire_id) {
        $query = "SELECT n.*, m.nom as matiere_nom, m.code, m.categorie, m.coefficient,
                         tn.libelle as type_note, tn.coefficient as coefficient_type
                 FROM notes n
                 JOIN matieres m ON n.matiere_id = m.id
                 JOIN types_notes tn ON n.type_note_id = tn.id
                 WHERE n.eleve_id = :eleve_id 
                 AND n.trimestre = :trimestre 
                 AND n.annee_scolaire_id = :annee_scolaire_id
                 ORDER BY m.categorie, m.nom";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            ':eleve_id' => $eleve_id,
            ':trimestre' => $trimestre,
            ':annee_scolaire_id' => $annee_scolaire_id
        ]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function calculerMoyennesEleve($eleve_id, $trimestre, $annee_scolaire_id) {
        $notes = $this->getNotesEleveTrimestre($eleve_id, $trimestre, $annee_scolaire_id);
        
        $moyennes_par_matiere = [];
        $total_points = 0;
        $total_coefficients = 0;
        
        // Grouper par matière
        $notes_par_matiere = [];
        foreach ($notes as $note) {
            $matiere_id = $note['matiere_id'];
            if (!isset($notes_par_matiere[$matiere_id])) {
                $notes_par_matiere[$matiere_id] = [
                    'matiere' => $note['matiere_nom'],
                    'coefficient' => $note['coefficient'],
                    'notes' => []
                ];
            }
            $notes_par_matiere[$matiere_id]['notes'][] = $note;
        }
        
        // Calculer les moyennes par matière
        foreach ($notes_par_matiere as $matiere_id => $data) {
            $somme_notes_ponderees = 0;
            $somme_coefficients = 0;
            
            foreach ($data['notes'] as $note) {
                $somme_notes_ponderees += $note['note'] * $note['coefficient_type'];
                $somme_coefficients += $note['coefficient_type'];
            }
            
            $moyenne_matiere = $somme_coefficients > 0 ? $somme_notes_ponderees / $somme_coefficients : 0;
            $points_matiere = $moyenne_matiere * $data['coefficient'];
            
            $moyennes_par_matiere[$matiere_id] = [
                'matiere' => $data['matiere'],
                'coefficient' => $data['coefficient'],
                'moyenne' => round($moyenne_matiere, 2),
                'points' => round($points_matiere, 2)
            ];
            
            $total_points += $points_matiere;
            $total_coefficients += $data['coefficient'];
        }
        
        $moyenne_generale = $total_coefficients > 0 ? round($total_points / $total_coefficients, 2) : 0;
        
        return [
            'par_matiere' => $moyennes_par_matiere,
            'generale' => $moyenne_generale,
            'total_points' => round($total_points, 2),
            'total_coefficients' => $total_coefficients
        ];
    }

    private function calculerStatistiquesClasse($classe_id, $trimestre, $annee_scolaire_id) {
        $eleves = $this->eleveModel->getByClasse($classe_id);
        $moyennes_eleves = [];
        
        foreach ($eleves as $eleve) {
            $moyennes = $this->calculerMoyennesEleve($eleve['id'], $trimestre, $annee_scolaire_id);
            if ($moyennes['generale'] > 0) {
                $moyennes_eleves[] = $moyennes['generale'];
            }
        }
        
        if (empty($moyennes_eleves)) {
            return [
                'moyenne_classe' => 0,
                'moyenne_min' => 0,
                'moyenne_max' => 0,
                'ecart_type' => 0
            ];
        }
        
        return [
            'moyenne_classe' => round(array_sum($moyennes_eleves) / count($moyennes_eleves), 2),
            'moyenne_min' => round(min($moyennes_eleves), 2),
            'moyenne_max' => round(max($moyennes_eleves), 2),
            'effectif_avec_notes' => count($moyennes_eleves)
        ];
    }

    private function getNotesClasseMatiereTrimestre($classe_id, $matiere_id, $trimestre) {
        $annee_scolaire = $this->getAnneeScolaireActive();
        
        $query = "SELECT n.*, e.nom, e.prenom, e.matricule,
                         tn.libelle as type_note, tn.coefficient as coefficient_type,
                         u.nom_utilisateur as saisie_par
                 FROM notes n
                 JOIN eleves e ON n.eleve_id = e.id
                 JOIN types_notes tn ON n.type_note_id = tn.id
                 LEFT JOIN utilisateurs u ON n.saisie_par = u.id
                 WHERE e.classe_id = :classe_id 
                 AND n.matiere_id = :matiere_id
                 AND n.trimestre = :trimestre
                 AND n.annee_scolaire_id = :annee_scolaire_id
                 ORDER BY e.nom, e.prenom, n.date_saisie DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            ':classe_id' => $classe_id,
            ':matiere_id' => $matiere_id,
            ':trimestre' => $trimestre,
            ':annee_scolaire_id' => $annee_scolaire['id']
        ]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    protected function getAnneeScolaireActive() {
        $query = "SELECT * FROM annees_scolaires WHERE est_active = 1 LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // ========================================================================
    // MÉTHODES AJOUTÉES POUR CORRIGER L'ERREUR
    // ========================================================================
    
    /**
     * Obtenir l'appréciation textuelle d'une note
     * @param float $note La note à évaluer
     * @return string L'appréciation (Exc., TB, B, AB, P, I, TI)
     */
    public function getAppreciationNote($note) {
        if ($note >= 16) return 'Exc.';      // Excellent
        if ($note >= 14) return 'TB';        // Très Bien
        if ($note >= 12) return 'B';         // Bien
        if ($note >= 10) return 'AB';        // Assez Bien
        if ($note >= 8) return 'P';          // Passable
        if ($note >= 6) return 'I';          // Insuffisant
        return 'TI';                         // Très Insuffisant
    }

    /**
     * Obtenir la classe CSS correspondant à l'appréciation d'une note
     * @param float $note La note à évaluer
     * @return string La classe CSS à appliquer
     */
    public function getClassAppreciation($note) {
        if ($note >= 16) return 'excellent';
        if ($note >= 14) return 'tres-bien';
        if ($note >= 12) return 'bien';
        if ($note >= 10) return 'assez-bien';
        if ($note >= 8) return 'passable';
        if ($note >= 6) return 'insuffisant';
        return 'tres-insuffisant';
    }
}
?>