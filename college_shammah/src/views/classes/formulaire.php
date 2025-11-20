<?php include __DIR__ . '../../partials/entete.php'; ?>
<?php
$classe = $data['classe'] ?? null;
$action = $data['action'] ?? 'create';
$form_data = $_SESSION['form_data'] ?? [];
unset($_SESSION['form_data']);

$title = $action === 'create' ? 'Créer une nouvelle classe' : 'Modifier la classe';
?>

<div class="dashboard">
    <div class="page-header">
        <h1><?php echo $title; ?></h1>
        <a href="/college_shammah/public/classes.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour à la liste
        </a>
    </div>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error">
            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <div class="form-container">
        <form method="POST" action="/college_shammah/public/classes.php?action=<?php echo $action === 'create' ? 'store' : 'update'; ?>">
            <?php if ($action === 'edit' && $classe): ?>
                <input type="hidden" name="id" value="<?php echo $classe['id']; ?>">
            <?php endif; ?>

            <!-- Section Informations de base -->
            <div class="form-section">
                <h3>
                    <i class="fas fa-info-circle"></i>
                    Informations de la classe
                </h3>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="nom" class="required">Nom de la classe</label>
                        <input type="text" 
                               id="nom" 
                               name="nom" 
                               value="<?php echo htmlspecialchars($form_data['nom'] ?? $classe['nom'] ?? ''); ?>" 
                               placeholder="Ex: 6ème A, Terminale S1"
                               required>
                        <small class="form-text">Le nom doit être unique (ex: "6ème A", "Terminale S")</small>
                    </div>

                    <div class="form-group">
                        <label for="niveau" class="required">Niveau</label>
                        <select id="niveau" name="niveau" required>
                            <option value="">Sélectionnez un niveau</option>
                            <option value="6ème" <?php echo ($form_data['niveau'] ?? $classe['niveau'] ?? '') === '6ème' ? 'selected' : ''; ?>>6ème</option>
                            <option value="5ème" <?php echo ($form_data['niveau'] ?? $classe['niveau'] ?? '') === '5ème' ? 'selected' : ''; ?>>5ème</option>
                            <option value="4ème" <?php echo ($form_data['niveau'] ?? $classe['niveau'] ?? '') === '4ème' ? 'selected' : ''; ?>>4ème</option>
                            <option value="3ème" <?php echo ($form_data['niveau'] ?? $classe['niveau'] ?? '') === '3ème' ? 'selected' : ''; ?>>3ème</option>
                            <option value="2nde" <?php echo ($form_data['niveau'] ?? $classe['niveau'] ?? '') === '2nde' ? 'selected' : ''; ?>>2nde</option>
                            <option value="1ère" <?php echo ($form_data['niveau'] ?? $classe['niveau'] ?? '') === '1ère' ? 'selected' : ''; ?>>1ère</option>
                            <option value="Tle" <?php echo ($form_data['niveau'] ?? $classe['niveau'] ?? '') === 'Tle' ? 'selected' : ''; ?>>Terminale</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="effectif_max">Effectif maximum</label>
                        <input type="number" 
                               id="effectif_max" 
                               name="effectif_max" 
                               value="<?php echo htmlspecialchars($form_data['effectif_max'] ?? $classe['effectif_max'] ?? '40'); ?>" 
                               min="10" 
                               max="60"
                               placeholder="40">
                        <small class="form-text">Nombre maximum d'élèves autorisés dans cette classe (défaut: 40)</small>
                    </div>
                    
                    <div class="form-group">
                        <label>Statut</label>
                        <div class="status-display">
                            <?php if ($action === 'edit' && $classe): ?>
                                <span class="status-badge status-active">
                                    <i class="fas fa-check-circle"></i>
                                    Classe active
                                </span>
                            <?php else: ?>
                                <span class="status-badge status-new">
                                    <i class="fas fa-plus-circle"></i>
                                    Nouvelle classe
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section Actions -->
            <div class="form-section">
                <h3>
                    <i class="fas fa-cogs"></i>
                    Actions
                </h3>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        <?php echo $action === 'create' ? 'Créer la classe' : 'Enregistrer les modifications'; ?>
                    </button>
                    
                    <a href="/college_shammah/public/classes.php" class="btn btn-secondary">
                        <i class="fas fa-times"></i>
                        Annuler
                    </a>

                    <?php if ($action === 'edit' && $classe && ($classe['effectif'] ?? 0) == 0): ?>
                        <a href="/college_shammah/public/classes.php?action=delete&id=<?php echo $classe['id']; ?>" 
                           class="btn btn-danger"
                           onclick="return confirm('Êtes-vous sûr de vouloir désactiver cette classe ? Cette action est irréversible.')">
                            <i class="fas fa-times"></i>
                            Désactiver la classe
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Section Informations -->
            <div class="info-box">
                <h3><i class="fas fa-lightbulb"></i> Informations importantes</h3>
                <ul>
                    <li>Le nom de la classe doit être unique dans le système</li>
                    <li>Une classe ne peut être désactivée que si elle ne contient aucun élève</li>
                    <li>L'effectif maximum détermine le taux de remplissage affiché dans le tableau de bord</li>
                    <li>Les classes désactivées n'apparaissent plus dans les listes mais restent dans l'historique</li>
                </ul>
            </div>
        </form>
    </div>
</div>

<style>
.form-container {
    max-width: 800px;
    margin: 0 auto;
}

.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    padding-bottom: 15px;
    border-bottom: 2px solid #f0f0f0;
}

.page-header h1 {
    color: #2c3e50;
    margin: 0;
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
.form-group select {
    width: 100%;
    padding: 12px;
    border: 2px solid #e1e1e1;
    border-radius: 5px;
    font-size: 16px;
    transition: all 0.3s;
}

.form-group input:focus,
.form-group select:focus {
    outline: none;
    border-color: #3498db;
    box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
}

.form-text {
    display: block;
    margin-top: 5px;
    color: #7f8c8d;
    font-size: 0.85rem;
}

.required::after {
    content: " *";
    color: #e74c3c;
}

.status-display {
    padding: 12px 0;
}

.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    border-radius: 20px;
    font-weight: 600;
    font-size: 0.9rem;
}

.status-active {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.status-new {
    background: #cce7ff;
    color: #004085;
    border: 1px solid #b3d7ff;
}

.form-actions {
    display: flex;
    gap: 15px;
    align-items: center;
    flex-wrap: wrap;
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
    font-weight: 500;
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

.btn-danger {
    background: #e74c3c;
    color: white;
}

.btn-danger:hover {
    background: #c0392b;
}

.info-box {
    background: #e8f4fd;
    border-left: 4px solid #3498db;
    padding: 20px;
    border-radius: 5px;
    margin-top: 20px;
}

.info-box h3 {
    color: #3498db;
    margin-bottom: 15px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.info-box ul {
    margin: 10px 0;
    padding-left: 20px;
}

.info-box li {
    margin-bottom: 8px;
    line-height: 1.4;
}

@media (max-width: 768px) {
    .form-row {
        grid-template-columns: 1fr;
    }
    
    .page-header {
        flex-direction: column;
        gap: 15px;
        align-items: flex-start;
    }
    
    .form-actions {
        flex-direction: column;
        align-items: stretch;
    }
    
    .form-actions .btn {
        justify-content: center;
    }
}
</style>

<?php include __DIR__ . '../../partials/pied.php'; ?>