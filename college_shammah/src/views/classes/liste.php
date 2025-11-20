<?php include __DIR__ . '../../partials/entete.php'; ?>
<?php
$classes = $data['classes'] ?? [];
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

    <!-- Cartes des classes -->
    <div class="classes-grid">
        <?php foreach ($classes as $classe): 
            $pourcentage_remplissage = min(($classe['effectif'] / $classe['effectif_max']) * 100, 100);
            $couleur = $pourcentage_remplissage >= 90 ? 'danger' : ($pourcentage_remplissage >= 75 ? 'warning' : 'success');
        ?>
        <div class="class-card">
            <div class="class-header">
                <h3><?php echo $classe['nom']; ?></h3>
                <span class="niveau"><?php echo $classe['niveau']; ?></span>
            </div>
            
            <div class="class-stats">
                <div class="stat">
                    <i class="fas fa-users"></i>
                    <span><?php echo $classe['effectif']; ?> / <?php echo $classe['effectif_max']; ?> élèves</span>
                </div>
                
                <div class="progress-bar">
                    <div class="progress-fill fill-<?php echo $couleur; ?>" 
                         style="width: <?php echo $pourcentage_remplissage; ?>%">
                    </div>
                </div>
                
                <div class="stat">
                    <small><?php echo round($pourcentage_remplissage); ?>% de remplissage</small>
                </div>
            </div>
            
            <div class="class-actions">
                <a href="/college_shammah/public/classes.php?action=show&id=<?php echo $classe['id']; ?>" 
                   class="btn btn-sm btn-primary">
                    <i class="fas fa-eye"></i> Voir
                </a>
                <a href="/college_shammah/public/classes.php?action=edit&id=<?php echo $classe['id']; ?>" 
                   class="btn btn-sm btn-warning">
                    <i class="fas fa-edit"></i> Modifier
                </a>
                <?php if ($classe['effectif'] == 0): ?>
                <a href="/college_shammah/public/classes.php?action=delete&id=<?php echo $classe['id']; ?>" 
                   class="btn btn-sm btn-danger"
                   onclick="return confirm('Êtes-vous sûr de vouloir désactiver cette classe ?')">
                    <i class="fas fa-times"></i> Désactiver
                </a>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <?php if (empty($classes)): ?>
        <div class="empty-state">
            <i class="fas fa-door-open fa-3x"></i>
            <h3>Aucune classe</h3>
            <p>Aucune classe n'est actuellement configurée dans le système.</p>
            <a href="/college_shammah/public/classes.php?action=create" class="btn btn-primary">
                <i class="fas fa-plus-circle"></i> Créer la première classe
            </a>
        </div>
    <?php endif; ?>
</div>

<style>
.classes-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
}

.class-card {
    background: white;
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    overflow: hidden;
    transition: transform 0.3s;
}

.class-card:hover {
    transform: translateY(-5px);
}

.class-header {
    background: linear-gradient(135deg, #3498db, #2980b9);
    color: white;
    padding: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.class-header h3 {
    margin: 0;
    font-size: 1.3rem;
}

.niveau {
    background: rgba(255, 255, 255, 0.2);
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 0.8rem;
    font-weight: bold;
}

.class-stats {
    padding: 20px;
}

.stat {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 10px;
}

.stat i {
    color: #3498db;
    width: 20px;
}

.progress-bar {
    width: 100%;
    height: 8px;
    background: #ecf0f1;
    border-radius: 4px;
    overflow: hidden;
    margin: 10px 0;
}

.progress-fill {
    height: 100%;
    border-radius: 4px;
    transition: width 0.3s;
}

.fill-success { background: #2ecc71; }
.fill-warning { background: #f39c12; }
.fill-danger { background: #e74c3c; }

.class-actions {
    padding: 15px 20px;
    background: #f8f9fa;
    border-top: 1px solid #e1e1e1;
    display: flex;
    gap: 10px;
}

.class-actions .btn-sm {
    flex: 1;
    text-align: center;
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
<?php include __DIR__ . '../../partials/pied.php'; ?>