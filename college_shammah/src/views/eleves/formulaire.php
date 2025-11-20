<?php include __DIR__ . '../../partials/entete.php'; ?>
<?php
$eleve = $data['eleve'] ?? null;
$classes = $data['classes'] ?? [];
$annee_scolaire = $data['annee_scolaire'] ?? [];
$action = $data['action'] ?? 'create';
$form_data = $_SESSION['form_data'] ?? [];
unset($_SESSION['form_data']);

$title = $action === 'create' ? 'Inscription d\'un nouvel élève' : 'Modification de l\'élève';
$submit_text = $action === 'create' ? 'Inscrire l\'élève' : 'Modifier l\'élève';
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
        <a href="/college_shammah/public/eleves.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour à la liste
        </a>
    </div>
</div>

    <div class="form-container">
        <form method="POST" action="/college_shammah/public/eleves.php?action=<?php echo $form_action; ?>">
            <?php if ($action === 'edit'): ?>
                <input type="hidden" name="id" value="<?php echo $eleve['id']; ?>">
            <?php endif; ?>

            <div class="form-section">
                <h3><i class="fas fa-user"></i> Informations personnelles</h3>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="nom">Nom *</label>
                        <input type="text" id="nom" name="nom" 
                               value="<?php echo htmlspecialchars($form_data['nom'] ?? $eleve['nom'] ?? ''); ?>" 
                               required>
                    </div>
                    
                    <div class="form-group">
                        <label for="prenom">Prénom *</label>
                        <input type="text" id="prenom" name="prenom" 
                               value="<?php echo htmlspecialchars($form_data['prenom'] ?? $eleve['prenom'] ?? ''); ?>" 
                               required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="date_naissance">Date de naissance *</label>
                        <input type="date" id="date_naissance" name="date_naissance" 
                               value="<?php echo $form_data['date_naissance'] ?? $eleve['date_naissance'] ?? ''; ?>" 
                               required>
                    </div>
                    
                    <div class="form-group">
                        <label for="lieu_naissance">Lieu de naissance *</label>
                        <input type="text" id="lieu_naissance" name="lieu_naissance" 
                               value="<?php echo htmlspecialchars($form_data['lieu_naissance'] ?? $eleve['lieu_naissance'] ?? ''); ?>" 
                               required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="sexe">Sexe *</label>
                        <select id="sexe" name="sexe" required>
                            <option value="">Sélectionner</option>
                            <option value="M" <?php echo ($form_data['sexe'] ?? $eleve['sexe'] ?? '') === 'M' ? 'selected' : ''; ?>>Masculin</option>
                            <option value="F" <?php echo ($form_data['sexe'] ?? $eleve['sexe'] ?? '') === 'F' ? 'selected' : ''; ?>>Féminin</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="telephone">Téléphone</label>
                        <input type="tel" id="telephone" name="telephone" 
                               value="<?php echo htmlspecialchars($form_data['telephone'] ?? $eleve['telephone'] ?? ''); ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" 
                           value="<?php echo htmlspecialchars($form_data['email'] ?? $eleve['email'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="adresse">Adresse complète *</label>
                    <textarea id="adresse" name="adresse" rows="3" required><?php echo htmlspecialchars($form_data['adresse'] ?? $eleve['adresse'] ?? ''); ?></textarea>
                </div>
            </div>

            <div class="form-section">
                <h3><i class="fas fa-users"></i> Informations parents</h3>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="nom_pere">Nom du père</label>
                        <input type="text" id="nom_pere" name="nom_pere" 
                               value="<?php echo htmlspecialchars($form_data['nom_pere'] ?? $eleve['nom_pere'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="nom_mere">Nom de la mère</label>
                        <input type="text" id="nom_mere" name="nom_mere" 
                               value="<?php echo htmlspecialchars($form_data['nom_mere'] ?? $eleve['nom_mere'] ?? ''); ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label for="telephone_parent">Téléphone des parents</label>
                    <input type="tel" id="telephone_parent" name="telephone_parent" 
                           value="<?php echo htmlspecialchars($form_data['telephone_parent'] ?? $eleve['telephone_parent'] ?? ''); ?>">
                </div>
            </div>

            <div class="form-section">
                <h3><i class="fas fa-graduation-cap"></i> Scolarité</h3>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="classe_id">Classe *</label>
                        <select id="classe_id" name="classe_id" required>
                            <option value="">Sélectionner une classe</option>
                            <?php foreach ($classes as $classe): ?>
                                <option value="<?php echo $classe['id']; ?>"
                                    <?php echo ($form_data['classe_id'] ?? $eleve['classe_id'] ?? '') == $classe['id'] ? 'selected' : ''; ?>>
                                    <?php echo $classe['nom'] . ' - ' . $classe['niveau']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="annee_scolaire_id">Année scolaire</label>
                        <input type="text" value="<?php echo $annee_scolaire['libelle'] ?? ''; ?>" disabled>
                        <input type="hidden" name="annee_scolaire_id" value="<?php echo $annee_scolaire['id'] ?? ''; ?>">
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> <?php echo $submit_text; ?>
                </button>
                <a href="/college_shammah/public/eleves.php" class="btn btn-secondary">
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

</style>
<?php include __DIR__ . '../../partials/pied.php'; ?>