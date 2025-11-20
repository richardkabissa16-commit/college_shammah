<?php
class CarteScolaire extends Model {
    protected $table = "eleves"; // On utilise la table élèves

    public function __construct() {
        parent::__construct();
    }

    // Obtenir les élèves sans carte
    public function getElevesSansCarte($classe_id = null) {
        $query = "SELECT e.*, c.nom as classe_nom, c.niveau 
                 FROM eleves e 
                 JOIN classes c ON e.classe_id = c.id 
                 WHERE e.est_archive = 0 AND e.numero_carte IS NULL";
        
        if ($classe_id) {
            $query .= " AND e.classe_id = :classe_id";
        }
        
        $query .= " ORDER BY e.nom, e.prenom";
        
        $stmt = $this->db->prepare($query);
        
        if ($classe_id) {
            $stmt->execute([':classe_id' => $classe_id]);
        } else {
            $stmt->execute();
        }
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtenir les cartes générées
    public function getCartesGenerees($classe_id = null) {
        $query = "SELECT e.*, c.nom as classe_nom, c.niveau 
                 FROM eleves e 
                 JOIN classes c ON e.classe_id = c.id 
                 WHERE e.est_archive = 0 AND e.numero_carte IS NOT NULL";
        
        if ($classe_id) {
            $query .= " AND e.classe_id = :classe_id";
        }
        
        $query .= " ORDER BY c.niveau, c.nom, e.nom, e.prenom";
        
        $stmt = $this->db->prepare($query);
        
        if ($classe_id) {
            $stmt->execute([':classe_id' => $classe_id]);
        } else {
            $stmt->execute();
        }
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ========================================================================
    // MÉTHODES AJOUTÉES POUR CORRIGER L'ERREUR
    // ========================================================================

    /**
     * Générer les cartes pour tous les élèves d'une classe
     * @param int $classe_id ID de la classe
     * @return array Liste des cartes générées avec les informations des élèves
     */
    public function genererCartesClasse($classe_id) {
        // Récupérer tous les élèves de la classe sans carte
        $eleves = $this->getElevesSansCarte($classe_id);
        
        if (empty($eleves)) {
            // Si tous ont déjà une carte, récupérer les cartes existantes
            return $this->getCartesGenerees($classe_id);
        }
        
        $cartes_generees = [];
        
        foreach ($eleves as $eleve) {
            try {
                // Générer un numéro de carte pour chaque élève
                $numero_carte = $this->genererNumeroCarte($eleve['id']);
                
                if ($numero_carte) {
                    // Récupérer les informations mises à jour de l'élève
                    $eleve_updated = $this->getById($eleve['id']);
                    $eleve_updated['classe_nom'] = $eleve['classe_nom'];
                    $eleve_updated['niveau'] = $eleve['niveau'];
                    $cartes_generees[] = $eleve_updated;
                }
            } catch (Exception $e) {
                // En cas d'erreur, continuer avec les autres élèves
                error_log("Erreur génération carte pour élève " . $eleve['id'] . ": " . $e->getMessage());
                continue;
            }
        }
        
        return $cartes_generees;
    }

    /**
     * Générer un numéro de carte unique pour un élève
     * @param int $eleve_id ID de l'élève
     * @return string Le numéro de carte généré
     */
    public function genererNumeroCarte($eleve_id) {
        // Récupérer l'élève
        $eleve = $this->getById($eleve_id);
        
        if (!$eleve) {
            throw new Exception("Élève non trouvé");
        }
        
        if ($eleve['numero_carte']) {
            // L'élève a déjà une carte
            return $eleve['numero_carte'];
        }
        
        // Récupérer l'année scolaire active
        $annee_scolaire = $this->getAnneeScolaireActive();
        $annee_code = substr($annee_scolaire['annee'], 0, 4); // Ex: 2024
        
        // Format du numéro de carte: CS-ANNEE-XXXXX
        // Exemple: CS-2024-00001
        
        // Trouver le dernier numéro de carte de l'année en cours
        $query = "SELECT numero_carte FROM eleves 
                 WHERE numero_carte LIKE :pattern 
                 ORDER BY numero_carte DESC 
                 LIMIT 1";
        
        $stmt = $this->db->prepare($query);
        $pattern = "CS-" . $annee_code . "-%";
        $stmt->execute([':pattern' => $pattern]);
        $derniere_carte = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($derniere_carte) {
            // Extraire le numéro séquentiel
            $parts = explode('-', $derniere_carte['numero_carte']);
            $dernier_numero = intval($parts[2] ?? 0);
            $nouveau_numero = $dernier_numero + 1;
        } else {
            // Première carte de l'année
            $nouveau_numero = 1;
        }
        
        // Formater le numéro avec des zéros à gauche (5 chiffres)
        $numero_carte = sprintf("CS-%s-%05d", $annee_code, $nouveau_numero);
        
        // Enregistrer le numéro dans la base de données
        $query = "UPDATE eleves 
                 SET numero_carte = :numero_carte, 
                     date_emission_carte = NOW() 
                 WHERE id = :id";
        
        $stmt = $this->db->prepare($query);
        $success = $stmt->execute([
            ':numero_carte' => $numero_carte,
            ':id' => $eleve_id
        ]);
        
        if (!$success) {
            throw new Exception("Erreur lors de l'enregistrement du numéro de carte");
        }
        
        return $numero_carte;
    }

    /**
     * Réinitialiser la carte d'un élève (supprimer le numéro de carte)
     * @param int $eleve_id ID de l'élève
     * @return bool True si succès
     */
    public function reinitialiserCarte($eleve_id) {
        $query = "UPDATE eleves 
                 SET numero_carte = NULL, 
                     date_emission_carte = NULL 
                 WHERE id = :id";
        
        $stmt = $this->db->prepare($query);
        return $stmt->execute([':id' => $eleve_id]);
    }

    /**
     * Obtenir un élève par son ID avec les informations de classe
     * @param int $eleve_id ID de l'élève
     * @return array|false Les informations de l'élève
     */
    public function getById($eleve_id) {
        $query = "SELECT e.*, c.nom as classe_nom, c.niveau 
                 FROM eleves e 
                 LEFT JOIN classes c ON e.classe_id = c.id 
                 WHERE e.id = :id";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([':id' => $eleve_id]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Vérifier si un numéro de carte existe déjà
     * @param string $numero_carte Le numéro à vérifier
     * @return bool True si le numéro existe déjà
     */
    public function numeroCarteExiste($numero_carte) {
        $query = "SELECT COUNT(*) as count FROM eleves WHERE numero_carte = :numero_carte";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':numero_carte' => $numero_carte]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['count'] > 0;
    }

    /**
     * Obtenir l'année scolaire active
     * @return array Les informations de l'année scolaire active
     */
    private function getAnneeScolaireActive() {
        $query = "SELECT * FROM annees_scolaires WHERE est_active = 1 LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $annee = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$annee) {
            // Si aucune année active, utiliser l'année en cours
            $annee = [
                'id' => 1,
                'annee' => date('Y') . '-' . (date('Y') + 1)
            ];
        }
        
        return $annee;
    }

    /**
     * Obtenir les statistiques des cartes scolaires
     * @return array Statistiques (total élèves, cartes générées, cartes manquantes)
     */
    public function getStatistiques() {
        $query = "SELECT 
                    COUNT(*) as total_eleves,
                    SUM(CASE WHEN numero_carte IS NOT NULL THEN 1 ELSE 0 END) as cartes_generees,
                    SUM(CASE WHEN numero_carte IS NULL THEN 1 ELSE 0 END) as cartes_manquantes
                 FROM eleves 
                 WHERE est_archive = 0";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>