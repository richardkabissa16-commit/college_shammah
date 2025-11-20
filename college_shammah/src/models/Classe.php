<?php
class Classe extends Model {
    protected $table = "classes";

    public function __construct() {
        parent::__construct();
    }

    // Obtenir une classe par son ID
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id AND est_active = 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Obtenir les classes actives
    public function getActive() {
        $query = "SELECT * FROM " . $this->table . " WHERE est_active = 1 ORDER BY niveau, nom";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtenir les classes par niveau
    public function getByNiveau($niveau) {
        $query = "SELECT * FROM " . $this->table . " WHERE niveau = :niveau AND est_active = 1 ORDER BY nom";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":niveau", $niveau);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtenir les statistiques des classes
    public function getStats() {
        $query = "SELECT c.*, COUNT(e.id) as effectif
                 FROM classes c
                 LEFT JOIN eleves e ON c.id = e.classe_id AND e.est_archive = 0
                 WHERE c.est_active = 1
                 GROUP BY c.id
                 ORDER BY c.niveau, c.nom";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>