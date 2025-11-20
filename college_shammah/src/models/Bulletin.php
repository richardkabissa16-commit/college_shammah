<?php
class Bulletin extends Model {
    protected $table = "bulletins";

    public function __construct() {
        parent::__construct();
    }

    // Vérifier si un bulletin existe
    public function bulletinExists($eleve_id, $trimestre, $annee_scolaire_id) {
        $query = "SELECT id FROM " . $this->table . " 
                 WHERE eleve_id = :eleve_id 
                 AND trimestre = :trimestre 
                 AND annee_scolaire_id = :annee_scolaire_id";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            ':eleve_id' => $eleve_id,
            ':trimestre' => $trimestre,
            ':annee_scolaire_id' => $annee_scolaire_id
        ]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Obtenir les bulletins d'une classe
    public function getByClasse($classe_id, $trimestre, $annee_scolaire_id) {
        $query = "SELECT b.*, e.matricule, e.nom, e.prenom
                 FROM bulletins b
                 JOIN eleves e ON b.eleve_id = e.id
                 WHERE e.classe_id = :classe_id 
                 AND b.trimestre = :trimestre 
                 AND b.annee_scolaire_id = :annee_scolaire_id
                 ORDER BY b.rang, e.nom, e.prenom";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            ':classe_id' => $classe_id,
            ':trimestre' => $trimestre,
            ':annee_scolaire_id' => $annee_scolaire_id
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // MÉTHODE MANQUANTE : Alias pour la compatibilité avec le contrôleur
    public function getBulletinsByClasse($classe_id, $trimestre, $annee_scolaire_id) {
        return $this->getByClasse($classe_id, $trimestre, $annee_scolaire_id);
    }

    // MÉTHODE MANQUANTE : Générer un bulletin
    public function genererBulletin($eleve_id, $trimestre, $annee_scolaire_id) {
        // Vérifier si le bulletin existe déjà
        $existing = $this->bulletinExists($eleve_id, $trimestre, $annee_scolaire_id);
        if ($existing) {
            return $existing['id'];
        }

        // Calculer la moyenne de l'élève
        $moyenne = $this->calculerMoyenneEleve($eleve_id, $trimestre);
        
        // Obtenir la classe de l'élève pour calculer le rang
        $classe_id = $this->getClasseEleve($eleve_id);
        
        // Calculer le rang
        $rang = $this->calculerRangEleve($eleve_id, $classe_id, $trimestre, $annee_scolaire_id);
        
        // Générer l'appréciation et la décision
        $appreciation = $this->genererAppreciation($moyenne);
        $decision = $this->determinerDecision($moyenne);

        // Insérer le bulletin
        $query = "INSERT INTO bulletins (eleve_id, trimestre, annee_scolaire_id, moyenne_generale, rang, appreciation, decision, created_at) 
                  VALUES (:eleve_id, :trimestre, :annee_scolaire_id, :moyenne, :rang, :appreciation, :decision, NOW())";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            ':eleve_id' => $eleve_id,
            ':trimestre' => $trimestre,
            ':annee_scolaire_id' => $annee_scolaire_id,
            ':moyenne' => $moyenne,
            ':rang' => $rang,
            ':appreciation' => $appreciation,
            ':decision' => $decision
        ]);

        return $this->db->lastInsertId();
    }

    // MÉTHODE MANQUANTE : Obtenir les données complètes d'un bulletin
    public function getBulletinComplet($bulletin_id) {
        $query = "SELECT b.*, e.matricule, e.nom, e.prenom, e.date_naissance, e.lieu_naissance,
                         c.nom as classe_nom, c.niveau, 
                         a.libelle as annee_scolaire
                  FROM bulletins b
                  JOIN eleves e ON b.eleve_id = e.id
                  JOIN classes c ON e.classe_id = c.id
                  JOIN annees_scolaires a ON b.annee_scolaire_id = a.id
                  WHERE b.id = :bulletin_id";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([':bulletin_id' => $bulletin_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // MÉTHODES PRIVÉES POUR LES CALCULS

    private function getClasseEleve($eleve_id) {
        $query = "SELECT classe_id FROM eleves WHERE id = :eleve_id";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':eleve_id' => $eleve_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['classe_id'] ?? null;
    }

    private function calculerMoyenneEleve($eleve_id, $trimestre) {
        // Calculer la moyenne générale de l'élève pour le trimestre
        $query = "SELECT AVG(note) as moyenne 
                  FROM notes 
                  WHERE eleve_id = :eleve_id AND trimestre = :trimestre";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            ':eleve_id' => $eleve_id,
            ':trimestre' => $trimestre
        ]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return round($result['moyenne'] ?? 0, 2);
    }

    private function calculerRangEleve($eleve_id, $classe_id, $trimestre, $annee_scolaire_id) {
        // Calculer le rang de l'élève dans sa classe
        // Cette méthode est simplifiée - vous devrez peut-être l'adapter
        
        $query = "SELECT e.id, AVG(n.note) as moyenne
                  FROM eleves e
                  LEFT JOIN notes n ON e.id = n.eleve_id AND n.trimestre = :trimestre
                  WHERE e.classe_id = :classe_id
                  GROUP BY e.id
                  ORDER BY moyenne DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            ':classe_id' => $classe_id,
            ':trimestre' => $trimestre
        ]);
        
        $eleves = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Trouver le rang de l'élève
        $rang = 1;
        foreach ($eleves as $eleve) {
            if ($eleve['id'] == $eleve_id) {
                return $rang;
            }
            $rang++;
        }
        
        return 1;
    }

    private function genererAppreciation($moyenne) {
        if ($moyenne >= 16) return "Excellent";
        if ($moyenne >= 14) return "Très bien";
        if ($moyenne >= 12) return "Bien";
        if ($moyenne >= 10) return "Assez bien";
        if ($moyenne >= 8) return "Passable";
        return "Insuffisant";
    }

    private function determinerDecision($moyenne) {
        return $moyenne >= 10 ? "Admis" : "Redouble";
    }
}
?>