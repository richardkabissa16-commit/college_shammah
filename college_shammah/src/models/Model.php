<?php
require_once __DIR__ . '/../../config/config.php';

class Model {
    protected $db;
    protected $table;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function getAll() {

        // Vérifier si la colonne est_archive existe
        if ($this->hasColumn('est_archive')) {
            $query = "SELECT * FROM " . $this->table . " WHERE est_archive = 0";
        } else {
            $query = "SELECT * FROM " . $this->table;
        }

        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function delete($id) {

        // Soft delete si la colonne existe
        if ($this->hasColumn('est_archive')) {
            $query = "UPDATE " . $this->table . " 
                      SET est_archive = 1 
                      WHERE id = :id";
        } else {
            $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        }

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }

    public function count() {

        if ($this->hasColumn('est_archive')) {
            $query = "SELECT COUNT(*) as total FROM " . $this->table . " WHERE est_archive = 0";
        } else {
            $query = "SELECT COUNT(*) as total FROM " . $this->table;
        }

        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    protected function hasColumn($column) {
        try {
            $query = "SHOW COLUMNS FROM " . $this->table . " LIKE :column";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":column", $column);
            $stmt->execute();
            return $stmt->rowCount() > 0;
        } catch (Exception $e) {
            return false;
        }
    }

    protected function executeQuery($query, $params = []) {
        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            throw new Exception("Erreur de requête: " . $e->getMessage());
        }
    }
}
?>
