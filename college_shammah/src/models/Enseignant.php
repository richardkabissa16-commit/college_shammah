<?php
class Enseignant extends Model {
    protected $table = "enseignants";

    public function __construct() {
        parent::__construct();
    }

    // Créer un nouvel enseignant
    public function create($data) {
        // Générer le matricule automatiquement
        $matricule = $this->generateMatricule();
        
        $query = "INSERT INTO " . $this->table . " 
                 (matricule, nom, prenom, telephone, email, specialite) 
                 VALUES (:matricule, :nom, :prenom, :telephone, :email, :specialite)";
        
        $stmt = $this->db->prepare($query);
        
        return $stmt->execute([
            ':matricule' => $matricule,
            ':nom' => $data['nom'],
            ':prenom' => $data['prenom'],
            ':telephone' => $data['telephone'],
            ':email' => $data['email'],
            ':specialite' => $data['specialite']
        ]);
    }

    // Générer matricule automatique
    private function generateMatricule() {
        $query = "SELECT COUNT(*) as count FROM enseignants WHERE matricule LIKE 'ENS%'";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $nextNumber = $result['count'] + 1;
        return 'ENS' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    // Mettre à jour un enseignant
    public function update($id, $data) {
        $query = "UPDATE " . $this->table . " SET 
                 nom = :nom, prenom = :prenom, telephone = :telephone, 
                 email = :email, specialite = :specialite 
                 WHERE id = :id";
        
        $stmt = $this->db->prepare($query);
        $data[':id'] = $id;
        return $stmt->execute($data);
    }

    // Obtenir les enseignants actifs
    public function getActive() {
        $query = "SELECT * FROM " . $this->table . " WHERE est_actif = 1 ORDER BY nom, prenom";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>