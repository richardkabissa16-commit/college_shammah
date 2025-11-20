<?php include __DIR__ . '../../partials/entete.php'; ?>
<?php
$matieres = $data['matieres'] ?? [];
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

    <div class="content-header">
    <h1>Gestion des Matières</h1>
    <div class="header-actions">
        <a href="/college_shammah/public/matieres.php?action=create" class="btn btn-primary">
            <i class="fas fa-plus-circle"></i> Nouvelle matière
        </a>
    </div>
</div>

    <!-- Tableau des matières -->
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Nom</th>
                    <th>Catégorie</th>
                    <th>Coefficient</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($matieres as $matiere): ?>
                <tr>
                    <td>
                        <span class="code-matiere"><?php echo $matiere['code']; ?></span>
                    </td>
                    <td>
                        <strong><?php echo $matiere['nom']; ?></strong>
                    </td>
                    <td>
                        <span class="categorie categorie-<?php echo $matiere['categorie']; ?>">
                            <?php 
                            $categories = [
                                'scientifique' => 'Scientifique',
                                'litteraire' => 'Littéraire',
                                'complementaire' => 'Complémentaire'
                            ];
                            echo $categories[$matiere['categorie']] ?? $matiere['categorie'];
                            ?>
                        </span>
                    </td>
                    <td>
                        <span class="coefficient"><?php echo $matiere['coefficient']; ?></span>
                    </td>
                    <td>
                        <div class="action-buttons">
                            <a href="/college_shammah/public/matieres.php?action=edit&id=<?php echo $matiere['id']; ?>" 
                               class="btn btn-sm btn-warning" title="Modifier">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="/college_shammah/public/matieres.php?action=delete&id=<?php echo $matiere['id']; ?>" 
                               class="btn btn-sm btn-danger" 
                               onclick="return confirm('Êtes-vous sûr de vouloir désactiver cette matière ?')"
                               title="Désactiver">
                                <i class="fas fa-times"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php if (empty($matieres)): ?>
        <div class="empty-state">
            <i class="fas fa-book fa-3x"></i>
            <h3>Aucune matière</h3>
            <p>Aucune matière n'est actuellement configurée dans le système.</p>
            <a href="/college_shammah/public/matieres.php?action=create" class="btn btn-primary">
                <i class="fas fa-plus-circle"></i> Ajouter la première matière
            </a>
        </div>
    <?php endif; ?>
</div>

<style>
.content-header{
   margin-bottom: 15px;
   
}
.code-matiere {
    background: #e8f4fd;
    padding: 4px 8px;
    border-radius: 4px;
    font-family: monospace;
    font-weight: bold;
    color: #3498db;
}

.categorie {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 0.8rem;
    font-weight: bold;
    text-transform: uppercase;
}

.categorie-scientifique {
    background: #d4edda;
    color: #155724;
}

.categorie-litteraire {
    background: #d1ecf1;
    color: #0c5460;
}

.categorie-complementaire {
    background: #fff3cd;
    color: #856404;
}

.coefficient {
    background: #f8f9fa;
    padding: 4px 8px;
    border-radius: 4px;
    font-weight: bold;
    color: #2c3e50;
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
<?php include __DIR__ . '../../partials/pied.php'; ?>