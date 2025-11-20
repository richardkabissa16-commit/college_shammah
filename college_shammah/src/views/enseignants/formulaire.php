<?php include __DIR__ . '../../partials/entete.php'; ?>
<?php
$enseignant = $data['enseignant'] ?? null;
$matieres = $data['matieres'] ?? [];
$action = $data['action'] ?? 'create';
$form_data = $_SESSION['form_data'] ?? [];
unset($_SESSION['form_data']);

$title = $action === 'create' ? 'Ajout d\'un nouvel enseignant' : 'Modification de l\'enseignant';
$submit_text = $action === 'create' ? 'Ajouter l\'enseignant' : 'Modifier l\'enseignant';
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
        <a href="/college_shammah/public/enseignants.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour à la liste
        </a>
    </div>
</div>

    <div class="form-container">
        <form method="POST" action="/college_shammah/public/enseignants.php?action=<?php echo $form_action; ?>">
            <?php if ($action === 'edit'): ?>
                <input type="hidden" name="id" value="<?php echo $enseignant['id']; ?>">
            <?php endif; ?>

            <div class="form-section">
                <h3><i class="fas fa-user-tie"></i> Informations personnelles</h3>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="nom">Nom *</label>
                        <input type="text" id="nom" name="nom" 
                               value="<?php echo htmlspecialchars($form_data['nom'] ?? $enseignant['nom'] ?? ''); ?>" 
                               required>
                    </div>
                    
                    <div class="form-group">
                        <label for="prenom">Prénom *</label>
                        <input type="text" id="prenom" name="prenom" 
                               value="<?php echo htmlspecialchars($form_data['prenom'] ?? $enseignant['prenom'] ?? ''); ?>" 
                               required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="telephone">Téléphone</label>
                        <input type="tel" id="telephone" name="telephone" 
                               value="<?php echo htmlspecialchars($form_data['telephone'] ?? $enseignant['telephone'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" 
                               value="<?php echo htmlspecialchars($form_data['email'] ?? $enseignant['email'] ?? ''); ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label for="specialite">Spécialité *</label>
                    <input type="text" id="specialite" name="specialite" 
                           value="<?php echo htmlspecialchars($form_data['specialite'] ?? $enseignant['specialite'] ?? ''); ?>" 
                           required
                           placeholder="Ex: Mathématiques, Français, Anglais...">
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> <?php echo $submit_text; ?>
                </button>
                <a href="/college_shammah/public/enseignants.php" class="btn btn-secondary">
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

.form-group input {
    width: 100%;
    padding: 10px 12px;
    border: 2px solid #e1e1e1;
    border-radius: 5px;
    font-size: 16px;
    transition: border-color 0.3s;
}

.form-group input:focus {
    outline: none;
    border-color: #3498db;
    box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
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
</style>
<?php include __DIR__ . '../../partials/pied.php'; ?>