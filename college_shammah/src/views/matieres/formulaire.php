<?php include __DIR__ . '../../partials/entete.php'; ?>
<?php
$matiere = $data['matiere'] ?? null;
$action = $data['action'] ?? 'create';
$form_data = $_SESSION['form_data'] ?? [];
unset($_SESSION['form_data']);

$title = $action === 'create' ? 'Ajout d\'une nouvelle matière' : 'Modification de la matière';
$submit_text = $action === 'create' ? 'Ajouter la matière' : 'Modifier la matière';
$form_action = $action === 'create' ? 'store' : 'update';
?>

<div class="dashboard">
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error">
            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>
    <div class="content-header">
    <h1><?php echo $title; ?></h1>
    <div class="header-actions">
        <a href="/college_shammah/public/matieres.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour à la liste
        </a>
    </div>
</div>

    <div class="form-container">
        <form method="POST" action="/college_shammah/public/matieres.php?action=<?php echo $form_action; ?>">
            <?php if ($action === 'edit'): ?>
                <input type="hidden" name="id" value="<?php echo $matiere['id']; ?>">
            <?php endif; ?>

            <div class="form-section">
                <h3><i class="fas fa-book"></i> Informations de la matière</h3>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="nom">Nom de la matière *</label>
                        <input type="text" id="nom" name="nom" 
                               value="<?php echo htmlspecialchars($form_data['nom'] ?? $matiere['nom'] ?? ''); ?>" 
                               required
                               placeholder="Ex: Mathématiques, Français...">
                    </div>
                    
                    <div class="form-group">
                        <label for="code">Code *</label>
                        <input type="text" id="code" name="code" 
                               value="<?php echo htmlspecialchars($form_data['code'] ?? $matiere['code'] ?? ''); ?>" 
                               required
                               placeholder="Ex: MATH, FR, ANG..."
                               style="text-transform: uppercase;">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="categorie">Catégorie *</label>
                        <select id="categorie" name="categorie" required>
                            <option value="">Sélectionner une catégorie</option>
                            <option value="scientifique" <?php echo ($form_data['categorie'] ?? $matiere['categorie'] ?? '') === 'scientifique' ? 'selected' : ''; ?>>Scientifique</option>
                            <option value="litteraire" <?php echo ($form_data['categorie'] ?? $matiere['categorie'] ?? '') === 'litteraire' ? 'selected' : ''; ?>>Littéraire</option>
                            <option value="complementaire" <?php echo ($form_data['categorie'] ?? $matiere['categorie'] ?? '') === 'complementaire' ? 'selected' : ''; ?>>Complémentaire</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="coefficient">Coefficient *</label>
                        <input type="number" id="coefficient" name="coefficient" 
                               value="<?php echo htmlspecialchars($form_data['coefficient'] ?? $matiere['coefficient'] ?? '1'); ?>" 
                               required min="1" max="10"
                               placeholder="Ex: 3">
                    </div>
                </div>
            </div>

            <div class="info-box">
                <h4><i class="fas fa-info-circle"></i> Informations sur les catégories</h4>
                <div class="categories-info">
                    <div class="category-item">
                        <span class="categorie categorie-scientifique">Scientifique</span>
                        <p>Mathématiques, Sciences Physiques, SVT, Technologie</p>
                    </div>
                    <div class="category-item">
                        <span class="categorie categorie-litteraire">Littéraire</span>
                        <p>Français, Anglais, Histoire-Géographie, ECM</p>
                    </div>
                    <div class="category-item">
                        <span class="categorie categorie-complementaire">Complémentaire</span>
                        <p>EPS, Arts Plastiques, Musique, Informatique</p>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> <?php echo $submit_text; ?>
                </button>
                <a href="/college_shammah/public/matieres.php" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Annuler
                </a>
            </div>
        </form>
    </div>
</div>

<style>
.content-header{
    margin-bottom: 15px;
}
.categories-info {
    display: grid;
    gap: 15px;
    margin-top: 10px;
}

.category-item {
    display: flex;
    align-items: flex-start;
    gap: 15px;
    padding: 10px;
    background: #f8f9fa;
    border-radius: 5px;
}

.category-item .categorie {
    min-width: 120px;
    text-align: center;
}

.category-item p {
    margin: 0;
    color: #666;
    font-size: 0.9rem;
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
</style>

<script>
// Mettre le code en majuscules automatiquement
document.getElementById('code').addEventListener('input', function() {
    this.value = this.value.toUpperCase();
});
</script>
<?php include __DIR__ . '../../partials/pied.php'; ?>