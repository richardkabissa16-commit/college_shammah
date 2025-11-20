<?php
class Note extends Model {
    protected $table = "notes";

    public function __construct() {
        parent::__construct();
    }

    // Ajouter une note
    public function addNote($data) {
        $query = "INSERT INTO " . $this->table . " 
                 (eleve_id, matiere_id, type_note_id, trimestre, annee_scolaire_id, note, saisie_par) 
                 VALUES (:eleve_id, :matiere_id, :type_note_id, :trimestre, :annee_scolaire_id, :note, :saisie_par)";
        
        $stmt = $this->db->prepare($query);
        return $stmt->execute($data);
    }

    // Obtenir les notes d'un élève pour un trimestre
    public function getNotesByEleveTrimestre($eleve_id, $trimestre, $annee_scolaire_id) {
        $query = "SELECT n.*, m.nom as matiere_nom, m.code as matiere_code, m.categorie, m.coefficient,
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

    // Calculer la moyenne d'un élève par matière pour un trimestre
    public function calculerMoyenneMatiere($eleve_id, $matiere_id, $trimestre, $annee_scolaire_id) {
        $query = "SELECT 
                 AVG(n.note * tn.coefficient) / AVG(tn.coefficient) as moyenne,
                 COUNT(n.id) as nombre_notes
                 FROM notes n
                 JOIN types_notes tn ON n.type_note_id = tn.id
                 WHERE n.eleve_id = :eleve_id 
                 AND n.matiere_id = :matiere_id
                 AND n.trimestre = :trimestre
                 AND n.annee_scolaire_id = :annee_scolaire_id";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            ':eleve_id' => $eleve_id,
            ':matiere_id' => $matiere_id,
            ':trimestre' => $trimestre,
            ':annee_scolaire_id' => $annee_scolaire_id
        ]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>