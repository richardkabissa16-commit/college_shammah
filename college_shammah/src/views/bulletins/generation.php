<?php include __DIR__ . '../../partials/entete.php'; ?>
<?php
$classes = $data['classes'] ?? [];
$annee_scolaire = $data['annee_scolaire'] ?? [];
?>
<div class="dashboard">
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error">
            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <div class="form-container">
        <form method="POST" action="/college_shammah/public/bulletins.php?action=generer">
            <div class="form-group">
                <label for="classe_id">Classe</label>
                <select name="classe_id" id="classe_id" required>
                    <option value="">Sélectionner une classe</option>
                    <?php foreach ($classes as $classe): ?>
                        <option value="<?php echo $classe['id']; ?>">
                            <?php echo $classe['nom'] . ' - ' . $classe['niveau']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="trimestre">Trimestre</label>
                <select name="trimestre" id="trimestre" required>
                    <option value="1">1er Trimestre</option>
                    <option value="2">2ème Trimestre</option>
                    <option value="3">3ème Trimestre</option>
                </select>
            </div>

            <div class="form-group">
                <label for="annee_scolaire_id">Année Scolaire</label>
                <input type="text" value="<?php echo $annee_scolaire['libelle'] ?? ''; ?>" disabled>
                <input type="hidden" name="annee_scolaire_id" value="<?php echo $annee_scolaire['id'] ?? ''; ?>">
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-cogs"></i> Générer les Bulletins
                </button>
                <a href="/college_shammah/public/bulletins.php" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Annuler
                </a>
            </div>
        </form>
    </div>

    <div class="info-box">
        <h3><i class="fas fa-info-circle"></i> Informations</h3>
        <p>La génération des bulletins va :</p>
        <ul>
            <li>Calculer les moyennes de tous les élèves de la classe</li>
            <li>Déterminer les rangs</li>
            <li>Générer les appréciations</li>
            <li>Préparer les bulletins pour impression</li>
        </ul>
        <p><strong>Assurez-vous que toutes les notes sont saisies avant de générer les bulletins.</strong></p>
    </div>
</div>

<style>
.alert {
    padding: 12px;
    border-radius: 5px;
    margin-bottom: 20px;
}

.alert-success {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.alert-error {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.form-container {
    background: white;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    margin-bottom: 30px;
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

.form-group select,
.form-group input {
    width: 100%;
    padding: 10px;
    border: 2px solid #e1e1e1;
    border-radius: 5px;
    font-size: 16px;
}

.form-group select:focus,
.form-group input:focus {
    outline: none;
    border-color: #3498db;
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