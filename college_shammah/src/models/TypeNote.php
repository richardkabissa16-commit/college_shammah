<?php
class TypeNote extends Model {
    protected $table = "types_notes";

    public function __construct() {
        parent::__construct();
    }

    // Obtenir les types de notes actifs
    public function getActive() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY type_calcul, libelle";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtenir les types de notes par catégorie
    public function getByTypeCalcul($type_calcul) {
        $query = "SELECT * FROM " . $this->table . " WHERE type_calcul = :type_calcul ORDER BY libelle";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":type_calcul", $type_calcul);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>