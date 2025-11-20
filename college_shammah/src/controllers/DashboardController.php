<?php
require_once 'Controller.php';
require_once __DIR__ . '/../models/Eleve.php';
require_once __DIR__ . '/../models/Enseignant.php';
require_once __DIR__ . '/../models/Classe.php';
require_once __DIR__ . '/../models/Note.php';

class DashboardController extends Controller {
    private $eleveModel;
    private $enseignantModel;
    private $classeModel;
    private $noteModel;

    public function __construct() {
        parent::__construct();
        $this->eleveModel = new Eleve();
        $this->enseignantModel = new Enseignant();
        $this->classeModel = new Classe();
        $this->noteModel = new Note();
    }

    public function index() {
        // Récupérer les statistiques pour le dashboard
        $stats = $this->getDashboardStats();
        $activites_recentes = $this->getActivitesRecentes();
        $effectifs_par_classe = $this->getEffectifsParClasse();
        
        $this->render('dashboard', [
            'stats' => $stats,
            'activites_recentes' => $activites_recentes,
            'effectifs_par_classe' => $effectifs_par_classe
        ]);
    }

    private function getDashboardStats() {
        // Nombre total d'élèves
        $query = "SELECT COUNT(*) as total FROM eleves WHERE est_archive = 0";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $total_eleves = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Nombre total d'enseignants
        $query = "SELECT COUNT(*) as total FROM enseignants WHERE est_actif = 1";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $total_enseignants = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Nombre total de classes
        $query = "SELECT COUNT(*) as total FROM classes WHERE est_active = 1";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $total_classes = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Nombre de matières
        $query = "SELECT COUNT(*) as total FROM matieres WHERE est_active = 1";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $total_matieres = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        return [
            'total_eleves' => $total_eleves,
            'total_enseignants' => $total_enseignants,
            'total_classes' => $total_classes,
            'total_matieres' => $total_matieres
        ];
    }

    private function getActivitesRecentes() {
        $activites = [];

        // Derniers élèves inscrits
        $query = "SELECT nom, prenom, date_inscription 
                 FROM eleves 
                 WHERE est_archive = 0 
                 ORDER BY date_inscription DESC 
                 LIMIT 5";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $eleves_recents = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($eleves_recents as $eleve) {
            $activites[] = [
                'type' => 'eleve',
                'icone' => 'user-plus',
                'message' => "Nouvel élève inscrit: {$eleve['prenom']} {$eleve['nom']}",
                'date' => $eleve['date_inscription']
            ];
        }

        // Dernières notes saisies
        $query = "SELECT n.date_saisie, e.nom, e.prenom, m.nom as matiere
                 FROM notes n
                 JOIN eleves e ON n.eleve_id = e.id
                 JOIN matieres m ON n.matiere_id = m.id
                 ORDER BY n.date_saisie DESC
                 LIMIT 5";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $notes_recentes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($notes_recentes as $note) {
            $activites[] = [
                'type' => 'note',
                'icone' => 'edit',
                'message' => "Note saisie pour {$note['prenom']} {$note['nom']} en {$note['matiere']}",
                'date' => $note['date_saisie']
            ];
        }

        // Trier par date et limiter à 5 activités
        usort($activites, function($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });

        return array_slice($activites, 0, 5);
    }

    private function getEffectifsParClasse() {
        $query = "SELECT c.nom, c.niveau, COUNT(e.id) as effectif
                 FROM classes c
                 LEFT JOIN eleves e ON c.id = e.classe_id AND e.est_archive = 0
                 WHERE c.est_active = 1
                 GROUP BY c.id, c.nom, c.niveau
                 ORDER BY c.niveau, c.nom";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>