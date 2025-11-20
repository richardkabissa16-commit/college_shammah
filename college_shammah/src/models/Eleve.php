<?php
class Eleve extends Model {
    protected $table = "eleves";

    public function __construct() {
        parent::__construct();
    }

    // Créer un nouvel élève
    public function create($data) {
        // Générer le matricule automatiquement
        $matricule = $this->generateMatricule();
        
        $query = "INSERT INTO " . $this->table . " 
                 (matricule, nom, prenom, date_naissance, lieu_naissance, sexe, telephone, email, adresse, classe_id, annee_scolaire_id, nom_pere, nom_mere, telephone_parent) 
                 VALUES (:matricule, :nom, :prenom, :date_naissance, :lieu_naissance, :sexe, :telephone, :email, :adresse, :classe_id, :annee_scolaire_id, :nom_pere, :nom_mere, :telephone_parent)";
        
        $stmt = $this->db->prepare($query);
        
        return $stmt->execute([
            ':matricule' => $matricule,
            ':nom' => $data['nom'],
            ':prenom' => $data['prenom'],
            ':date_naissance' => $data['date_naissance'],
            ':lieu_naissance' => $data['lieu_naissance'],
            ':sexe' => $data['sexe'],
            ':telephone' => $data['telephone'],
            ':email' => $data['email'],
            ':adresse' => $data['adresse'],
            ':classe_id' => $data['classe_id'],
            ':annee_scolaire_id' => $data['annee_scolaire_id'],
            ':nom_pere' => $data['nom_pere'],
            ':nom_mere' => $data['nom_mere'],
            ':telephone_parent' => $data['telephone_parent']
        ]);
    }

    // Générer matricule automatique
    private function generateMatricule() {
        $year = date('Y');
        $query = "SELECT COUNT(*) as count FROM eleves WHERE matricule LIKE :year_pattern";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':year_pattern' => $year . '%']);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $nextNumber = $result['count'] + 1;
        return $year . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    // Mettre à jour un élève
    public function update($id, $data) {
        $query = "UPDATE " . $this->table . " SET 
                 nom = :nom, prenom = :prenom, date_naissance = :date_naissance, lieu_naissance = :lieu_naissance, 
                 sexe = :sexe, telephone = :telephone, email = :email, adresse = :adresse, 
                 classe_id = :classe_id, nom_pere = :nom_pere, nom_mere = :nom_mere, telephone_parent = :telephone_parent 
                 WHERE id = :id";
        
        $stmt = $this->db->prepare($query);
        $data[':id'] = $id;
        return $stmt->execute($data);
    }

    // Obtenir les élèves par classe
    public function getByClasse($classe_id) {
        $query = "SELECT e.*, c.nom as classe_nom 
                 FROM eleves e 
                 JOIN classes c ON e.classe_id = c.id 
                 WHERE e.classe_id = :classe_id AND e.est_archive = 0 
                 ORDER BY e.nom, e.prenom";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":classe_id", $classe_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtenir les statistiques des élèves
    public function getStats() {
        $query = "SELECT 
                 COUNT(*) as total_eleves,
                 COUNT(CASE WHEN sexe = 'M' THEN 1 END) as garcons,
                 COUNT(CASE WHEN sexe = 'F' THEN 1 END) as filles,
                 c.niveau,
                 COUNT(*) as effectif_par_niveau
                 FROM eleves e 
                 JOIN classes c ON e.classe_id = c.id 
                 WHERE e.est_archive = 0 
                 GROUP BY c.niveau";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>