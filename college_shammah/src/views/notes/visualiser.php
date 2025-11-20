<?php include __DIR__ . '../../partials/entete.php'; ?>
<?php
$classes = $data['classes'] ?? [];
$matieres = $data['matieres'] ?? [];
$notes_par_eleve = $data['notes_par_eleve'] ?? [];
$statistiques = $data['statistiques'] ?? [];
$classe_selectionnee = $data['classe_selectionnee'] ?? '';
$trimestre = $data['trimestre'] ?? '1';
$classe = $data['classe'] ?? null;
?>

<div class="dashboard">
    <div class="content-header">
    <h1>Visualisation des Notes</h1>
    <div class="header-actions">
        <a href="/college_shammah/public/notes.php?action=saisie" class="btn btn-primary">
            <i class="fas fa-edit"></i> Saisir des notes
        </a>
    </div>
</div>
    <!-- Filtres -->
    <div class="form-container">
        <form method="GET" action="/college_shammah/public/notes.php" class="filter-form">
            <input type="hidden" name="action" value="visualiser">
            
            <div class="form-row">
                <div class="form-group">
                    <label for="classe_id">Classe</label>
                    <select name="classe_id" id="classe_id" required onchange="this.form.submit()">
                        <option value="">Sélectionner une classe</option>
                        <?php foreach ($classes as $classe_opt): ?>
                            <option value="<?php echo $classe_opt['id']; ?>" 
                                <?php echo $classe_selectionnee == $classe_opt['id'] ? 'selected' : ''; ?>>
                                <?php echo $classe_opt['nom'] . ' - ' . $classe_opt['niveau']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="trimestre">Trimestre</label>
                    <select name="trimestre" id="trimestre" onchange="this.form.submit()">
                        <option value="1" <?php echo $trimestre == '1' ? 'selected' : ''; ?>>1er Trimestre</option>
                        <option value="2" <?php echo $trimestre == '2' ? 'selected' : ''; ?>>2ème Trimestre</option>
                        <option value="3" <?php echo $trimestre == '3' ? 'selected' : ''; ?>>3ème Trimestre</option>
                    </select>
                </div>
            </div>
        </form>
    </div>

    <?php if ($classe_selectionnee && !empty($notes_par_eleve)): ?>
    <!-- Statistiques de la classe -->
    <div class="stats-cards">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $statistiques['moyenne_classe']; ?></h3>
                <p>Moyenne de classe</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-arrow-up"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $statistiques['moyenne_max']; ?></h3>
                <p>Meilleure moyenne</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-arrow-down"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $statistiques['moyenne_min']; ?></h3>
                <p>Plus basse moyenne</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $statistiques['effectif_avec_notes']; ?></h3>
                <p>Élèves notés</p>
            </div>
        </div>
    </div>

    <!-- Tableau des notes -->
    <div class="table-section">
        <h3>
            <i class="fas fa-table"></i> 
            Notes de la classe <?php echo $classe['nom'] ?? ''; ?> - Trimestre <?php echo $trimestre; ?>
        </h3>
        
        <div class="table-container">
            <table class="data-table notes-table">
                <thead>
                    <tr>
                        <th rowspan="2">Élève</th>
                        <th colspan="<?php echo count($matieres); ?>">Matières</th>
                        <th rowspan="2">Moyenne</th>
                        <th rowspan="2">Rang</th>
                    </tr>
                    <tr>
                        <?php foreach ($matieres as $matiere): ?>
                            <th title="<?php echo $matiere['nom']; ?>">
                                <?php echo $matiere['code']; ?><br>
                                <small>(Coef. <?php echo $matiere['coefficient']; ?>)</small>
                            </th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    // Trier les élèves par moyenne générale
                    $eleves_avec_moyennes = [];
                    foreach ($notes_par_eleve as $eleve_id => $data) {
                        if ($data['moyennes']['generale'] > 0) {
                            $eleves_avec_moyennes[$eleve_id] = $data['moyennes']['generale'];
                        }
                    }
                    arsort($eleves_avec_moyennes);
                    $rang = 0;
                    $previous_moyenne = null;
                    $compteur_rang = 0;
                    
                    foreach ($eleves_avec_moyennes as $eleve_id => $moyenne_generale):
                        $data = $notes_par_eleve[$eleve_id];
                        $compteur_rang++;
                        
                        // Gestion des ex-aequo
                        if ($moyenne_generale !== $previous_moyenne) {
                            $rang = $compteur_rang;
                        }
                        $previous_moyenne = $moyenne_generale;
                    ?>
                    <tr>
                        <td>
                            <strong><?php echo $data['eleve']['nom'] . ' ' . $data['eleve']['prenom']; ?></strong>
                            <br><small class="matricule"><?php echo $data['eleve']['matricule']; ?></small>
                        </td>
                        
                        <?php foreach ($matieres as $matiere): 
                            $moyenne_matiere = $data['moyennes']['par_matiere'][$matiere['id']]['moyenne'] ?? 0;
                            $appreciation = $this->getAppreciationNote($moyenne_matiere);
                            $class_appreciation = $this->getClassAppreciation($moyenne_matiere);
                        ?>
                            <td class="text-center note-cell <?php echo $class_appreciation; ?>">
                                <?php if ($moyenne_matiere > 0): ?>
                                    <strong><?php echo $moyenne_matiere; ?></strong>
                                    <br>
                                    <small class="appreciation"><?php echo $appreciation; ?></small>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                        <?php endforeach; ?>
                        
                        <td class="text-center moyenne-generale">
                            <strong><?php echo $data['moyennes']['generale']; ?></strong>
                            <br>
                            <small><?php echo $this->getAppreciationNote($data['moyennes']['generale']); ?></small>
                        </td>
                        
                        <td class="text-center">
                            <span class="rang"><?php echo $rang; ?>ème</span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    
                    <!-- Élèves sans notes -->
                    <?php foreach ($notes_par_eleve as $eleve_id => $data): 
                        if ($data['moyennes']['generale'] == 0): ?>
                    <tr class="no-notes">
                        <td>
                            <strong><?php echo $data['eleve']['nom'] . ' ' . $data['eleve']['prenom']; ?></strong>
                            <br><small class="matricule"><?php echo $data['eleve']['matricule']; ?></small>
                        </td>
                        <td colspan="<?php echo count($matieres) + 2; ?>" class="text-center text-muted">
                            Aucune note enregistrée
                        </td>
                        <td class="text-center">-</td>
                    </tr>
                    <?php endif; endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php elseif ($classe_selectionnee && empty($notes_par_eleve)): ?>
        <div class="empty-state">
            <i class="fas fa-chart-bar fa-3x"></i>
            <h3>Aucune note enregistrée</h3>
            <p>Aucune note n'a été saisie pour cette classe et ce trimestre.</p>
            <a href="/college_shammah/public/notes.php?action=saisie&classe_id=<?php echo $classe_selectionnee; ?>" 
               class="btn btn-primary">
                <i class="fas fa-edit"></i> Commencer la saisie
            </a>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-filter fa-3x"></i>
            <h3>Sélectionnez une classe</h3>
            <p>Veuillez choisir une classe pour visualiser les notes.</p>
        </div>
    <?php endif; ?>
</div>

<style>
.content-header{
    margin-bottom: 15px;
}
.notes-table {
    font-size: 0.9rem;
}

.notes-table th {
    text-align: center;
    vertical-align: middle;
    background: #3498db;
    color: white;
}

.notes-table th:first-child {
    background: #2980b9;
}

.note-cell {
    padding: 8px 4px;
}

.note-cell.excellent { background-color: #d4edda; }
.note-cell.tres-bien { background-color: #d1ecf1; }
.note-cell.bien { background-color: #d1f1e1; }
.note-cell.assez-bien { background-color: #fff3cd; }
.note-cell.passable { background-color: #ffeaa7; }
.note-cell.insuffisant { background-color: #f8d7da; }
.note-cell.tres-insuffisant { background-color: #f5c6cb; }

.moyenne-generale {
    background-color: #e8f4fd;
    font-weight: bold;
}

.rang {
    background: #3498db;
    color: white;
    padding: 4px 8px;
    border-radius: 12px;
    font-weight: bold;
}

.appreciation {
    font-size: 0.7rem;
    color: #666;
}

.no-notes {
    background-color: #f8f9fa;
}

.no-notes td {
    color: #6c757d;
    font-style: italic;
}

.stats-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: white;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    display: flex;
    align-items: center;
}

.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 15px;
    font-size: 1.5rem;
    background-color: rgba(52, 152, 219, 0.1);
    color: #3498db;
}

.stat-info h3 {
    font-size: 1.8rem;
    margin-bottom: 5px;
    color: #333;
}

.stat-info p {
    color: #666;
    font-size: 0.9rem;
}
.form-section {
    background: white;
    padding: 25px;
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    margin-bottom: 25px;
}

.form-section h3 {
    margin-bottom: 20px;
    color: #3498db;
    display: flex;
    align-items: center;
    gap: 10px;
    border-bottom: 2px solid #f0f0f0;
    padding-bottom: 10px;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #333;
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 10px 12px;
    border: 2px solid #e1e1e1;
    border-radius: 5px;
    font-size: 16px;
    transition: border-color 0.3s;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    outline: none;
    border-color: #3498db;
    box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
}

.form-group textarea {
    resize: vertical;
    min-height: 80px;
}

.required::after {
    content: " *";
    color: #e74c3c;
}

@media (max-width: 768px) {
    .form-row {
        grid-template-columns: 1fr;
    }
}
.form-actions {
    display: flex;
    gap: 15px;
    margin-top: 30px;
}

.btn {
    padding: 12px 24px;
    border: none;
    border-radius: 5px;
    font-size: 16px;
    cursor: pointer;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s;
}

.btn-primary {
    background: #3498db;
    color: white;
}

.btn-primary:hover {
    background: #2980b9;
    transform: translateY(-2px);
}

.btn-secondary {
    background: #95a5a6;
    color: white;
}

.btn-secondary:hover {
    background: #7f8c8d;
}

.info-box {
    background: #e8f4fd;
    border-left: 4px solid #3498db;
    padding: 20px;
    border-radius: 5px;
}

.info-box h3 {
    color: #3498db;
    margin-bottom: 15px;
}

.info-box ul {
    margin: 10px 0;
    padding-left: 20px;
}

.info-box li {
    margin-bottom: 5px;
}
/* Styles pour les boutons d'action */
.actions {
    display: flex;
    gap: 5px;
}
.btn-sm {
    padding: 6px 10px;
    font-size: 0.8rem;
    border-radius: 4px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}
.btn-warning {
    background-color: #ffc107;
    color: #212529;
}
.btn-warning:hover {
    background-color: #e0a800;
}
.btn-danger {
    background-color: #dc3545;
    color: white;
}
.btn-danger:hover {
    background-color: #c82333;
}
</style>

<?php
// Fonctions utilitaires pour l'appréciation
function getAppreciationNote($note) {
    if ($note >= 16) return 'Exc.';
    if ($note >= 14) return 'TB';
    if ($note >= 12) return 'B';
    if ($note >= 10) return 'AB';
    if ($note >= 8) return 'P';
    if ($note >= 6) return 'I';
    return 'TI';
}

function getClassAppreciation($note) {
    if ($note >= 16) return 'excellent';
    if ($note >= 14) return 'tres-bien';
    if ($note >= 12) return 'bien';
    if ($note >= 10) return 'assez-bien';
    if ($note >= 8) return 'passable';
    if ($note >= 6) return 'insuffisant';
    return 'tres-insuffisant';
}
?>
<?php include __DIR__ . '../../partials/pied.php'; ?>