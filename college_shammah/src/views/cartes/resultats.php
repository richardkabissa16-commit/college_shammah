<?php include __DIR__ . '../../partials/entete.php'; ?>
<?php
$cartes_generees = $data['cartes_generees'] ?? [];
$classe = $data['classe'] ?? null;
?>

<div class="content-header">
    <h1>
        <i class="fas fa-id-card"></i> 
        Cartes Générées - <?php echo $classe['nom'] ?? 'Toutes les classes'; ?>
    </h1>
    <div class="header-actions">
        <a href="/college_shammah/public/cartes.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
        <?php if (!empty($cartes_generees)): ?>
        <a href="/college_shammah/public/cartes.php?action=pdf&classe_id=<?php echo $classe['id'] ?? ''; ?>" 
           class="btn btn-primary" target="_blank">
            <i class="fas fa-file-pdf"></i> Télécharger PDF
        </a>
        <?php endif; ?>
    </div>
</div>

<div class="dashboard">
    <?php if (!empty($cartes_generees)): ?>
    
    <!-- Statistiques -->
    <div class="stats-cards">
        <div class="stat-card success">
            <div class="stat-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo count($cartes_generees); ?></h3>
                <p>Cartes générées</p>
            </div>
        </div>
        
        <div class="stat-card info">
            <div class="stat-icon">
                <i class="fas fa-id-card"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $classe['nom'] ?? 'Multiple'; ?></h3>
                <p>Classe</p>
            </div>
        </div>
        
        <div class="stat-card primary">
            <div class="stat-icon">
                <i class="fas fa-calendar"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo date('d/m/Y'); ?></h3>
                <p>Date de génération</p>
            </div>
        </div>
    </div>

    <!-- Liste des cartes générées -->
    <div class="table-section">
        <h3>
            <i class="fas fa-list"></i> 
            Liste des cartes générées
        </h3>
        
        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Photo</th>
                        <th>Matricule</th>
                        <th>Nom & Prénom</th>
                        <th>Classe</th>
                        <th>Numéro de Carte</th>
                        <th>Date d'émission</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cartes_generees as $carte): ?>
                    <tr>
                        <td class="text-center">
                            <?php if (!empty($carte['photo'])): ?>
                                <img src="/college_shammah/public/uploads/photos/<?php echo $carte['photo']; ?>" 
                                     alt="Photo" class="student-photo-small">
                            <?php else: ?>
                                <div class="no-photo-small">
                                    <i class="fas fa-user"></i>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <strong><?php echo $carte['matricule']; ?></strong>
                        </td>
                        <td>
                            <strong><?php echo $carte['nom'] . ' ' . $carte['prenom']; ?></strong>
                            <br>
                            <small class="text-muted">
                                <?php echo $carte['date_naissance'] ? date('d/m/Y', strtotime($carte['date_naissance'])) : 'N/A'; ?>
                            </small>
                        </td>
                        <td>
                            <span class="badge badge-info">
                                <?php echo $carte['classe_nom'] . ' - ' . $carte['niveau']; ?>
                            </span>
                        </td>
                        <td>
                            <span class="numero-carte">
                                <i class="fas fa-id-card"></i>
                                <?php echo $carte['numero_carte']; ?>
                            </span>
                        </td>
                        <td>
                            <?php 
                            if (!empty($carte['date_emission_carte'])) {
                                echo date('d/m/Y', strtotime($carte['date_emission_carte']));
                            } else {
                                echo '<span class="text-muted">Non définie</span>';
                            }
                            ?>
                        </td>
                        <td class="text-center">
                            <div class="action-buttons">
                                <a href="/college_shammah/public/cartes.php?action=pdfIndividuel&eleve_id=<?php echo $carte['id']; ?>" 
                                   class="btn btn-sm btn-primary" 
                                   title="Télécharger la carte" 
                                   target="_blank">
                                    <i class="fas fa-download"></i>
                                </a>
                                <a href="/college_shammah/public/cartes.php?action=reinitialiser&eleve_id=<?php echo $carte['id']; ?>" 
                                   class="btn btn-sm btn-danger" 
                                   title="Réinitialiser la carte"
                                   onclick="return confirm('Êtes-vous sûr de vouloir réinitialiser cette carte ?')">
                                    <i class="fas fa-redo"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php else: ?>
    <!-- Message si aucune carte -->
    <div class="empty-state">
        <i class="fas fa-id-card fa-3x"></i>
        <h3>Aucune carte générée</h3>
        <p>Aucune carte n'a été générée pour cette classe.</p>
        <a href="/college_shammah/public/cartes.php" class="btn btn-primary">
            <i class="fas fa-arrow-left"></i> Retour à la liste
        </a>
    </div>
    <?php endif; ?>
</div>

<style>
.student-photo-small {
    width: 50px;
    height: 50px;
    border-radius: 8px;
    object-fit: cover;
    border: 2px solid #3498db;
}

.no-photo-small {
    width: 50px;
    height: 50px;
    border-radius: 8px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
}

.numero-carte {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 6px 12px;
    border-radius: 20px;
    font-weight: bold;
    font-size: 0.9rem;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.numero-carte i {
    font-size: 1rem;
}

.stats-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
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
    border-left: 4px solid #3498db;
}

.stat-card.success {
    border-left-color: #27ae60;
}

.stat-card.success .stat-icon {
    background-color: rgba(39, 174, 96, 0.1);
    color: #27ae60;
}

.stat-card.info {
    border-left-color: #3498db;
}

.stat-card.info .stat-icon {
    background-color: rgba(52, 152, 219, 0.1);
    color: #3498db;
}

.stat-card.primary {
    border-left-color: #9b59b6;
}

.stat-card.primary .stat-icon {
    background-color: rgba(155, 89, 182, 0.1);
    color: #9b59b6;
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 15px;
    font-size: 1.8rem;
}

.stat-info h3 {
    font-size: 2rem;
    margin-bottom: 5px;
    color: #333;
}

.stat-info p {
    color: #666;
    font-size: 0.95rem;
    margin: 0;
}

.action-buttons {
    display: flex;
    gap: 5px;
    justify-content: center;
}

.badge {
    padding: 5px 10px;
    border-radius: 12px;
    font-size: 0.85rem;
    font-weight: 500;
}

.badge-info {
    background-color: #d1ecf1;
    color: #0c5460;
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.empty-state i {
    color: #bdc3c7;
    margin-bottom: 20px;
}

.empty-state h3 {
    color: #34495e;
    margin-bottom: 10px;
}

.empty-state p {
    color: #7f8c8d;
    margin-bottom: 20px;
}
</style>
<?php include __DIR__ . '../../partials/pied.php'; ?>