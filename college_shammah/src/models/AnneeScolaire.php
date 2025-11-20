<?php
class AnneeScolaire extends Model {
    protected $table = "annees_scolaires";

    public function __construct() {
        parent::__construct();
    }

    // Obtenir l'année scolaire active
    public function getActive() {
        $query = "SELECT * FROM " . $this->table . " WHERE est_active = 1 LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Activer une année scolaire
    public function activate($id) {
        // Désactiver toutes les années
        $query = "UPDATE " . $this->table . " SET est_active = 0";
        $stmt = $this->db->prepare($query);
        $stmt->execute();

        // Activer l'année spécifique
        $query = "UPDATE " . $this->table . " SET est_active = 1 WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }
}
?>