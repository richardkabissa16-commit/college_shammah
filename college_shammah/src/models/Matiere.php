<?php
class Matiere extends Model {
    protected $table = "matieres";

    public function __construct() {
        parent::__construct();
    }

    // Obtenir les matières par catégorie
    public function getByCategorie($categorie) {
        $query = "SELECT * FROM " . $this->table . " 
                 WHERE categorie = :categorie AND est_active = 1 
                 ORDER BY nom";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":categorie", $categorie);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtenir toutes les matières actives
    public function getActive() {
        $query = "SELECT * FROM " . $this->table . " 
                 WHERE est_active = 1 
                 ORDER BY categorie, nom";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>