<?php include __DIR__ . '../../partials/entete.php'; ?>
<?php
$enseignants = $data['enseignants'] ?? [];
$matieres = $data['matieres'] ?? []; // Optionnel, si vous souhaitez afficher les matières associées
?>
<div class="dashboard">
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i>
            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <div class="content-header">
        <h1>Gestion des Enseignants</h1>
        <div class="header-actions">
            <a href="/college_shammah/public/enseignants.php?action=create" class="primary">
                <i class="fas fa-user-plus"></i>
                Nouvel enseignant
            </a>
        </div>
    </div>

    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Matricule</th>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Spécialité</th>
                    <th>Téléphone</th>
                    <th>Email</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($enseignants)): ?>
                    <tr>
                        <td colspan="8" class="empty-state-cell">
                            <i class="fas fa-chalkboard-teacher fa-3x"></i>
                            <h3>Aucun enseignant enregistré</h3>
                            <p>Commencez par ajouter un nouvel enseignant.</p>
                            <a href="/college_shammah/public/enseignants.php?action=create" class="btn btn-primary">
                                <i class="fas fa-user-plus"></i>
                                Ajouter le premier enseignant
                            </a>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach($enseignants as $enseignant): ?>
                    <tr>
                        <td>
                            <strong><?php echo htmlspecialchars($enseignant['matricule']); ?></strong>
                        </td>
                        <td>
                            <strong><?php echo htmlspecialchars($enseignant['nom']); ?></strong>
                        </td>
                        <td><?php echo htmlspecialchars($enseignant['prenom']); ?></td>
                        <td>
                            <?php if (!empty($enseignant['specialite'])): ?>
                                <span class="badge badge-info">
                                    <?php echo htmlspecialchars($enseignant['specialite']); ?>
                                </span>
                            <?php else: ?>
                                <span class="text-muted">Non spécifiée</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (!empty($enseignant['telephone'])): ?>
                                <i class="fas fa-phone text-success"></i>
                                <?php echo htmlspecialchars($enseignant['telephone']); ?>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (!empty($enseignant['email'])): ?>
                                <i class="fas fa-envelope text-danger"></i>
                                <a href="mailto:<?php echo htmlspecialchars($enseignant['email']); ?>">
                                    <?php echo htmlspecialchars($enseignant['email']); ?>
                                </a>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($enseignant['est_actif']): ?>
                                <span class="badge badge-success">Actif</span>
                            <?php else: ?>
                                <span class="badge badge-secondary">Inactif</span>
                            <?php endif; ?>
                        </td>
                        <td class="actions">
                            <a href="/college_shammah/public/enseignants.php?action=edit&id=<?php echo $enseignant['id']; ?>" 
                               class="btn btn-sm btn-warning" title="Modifier">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="/college_shammah/public/enseignants.php?action=delete&id=<?php echo $enseignant['id']; ?>" 
                               class="btn btn-sm btn-danger" 
                               onclick="return confirm('Êtes-vous sûr de vouloir désactiver cet enseignant ?')"
                               title="Désactiver">
                                <i class="fas fa-times"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Statistiques -->
    <?php if (!empty($enseignants)): ?>
    <div class="stats-cards" style="margin-top: 30px;">
        <div class="stat-card">
            <h3><?php echo count($enseignants); ?></h3>
            <p>Enseignants total</p>
        </div>
        <div class="stat-card">
            <h3>
                <?php 
                $actifs = array_filter($enseignants, function($e) { return $e['est_actif'] == 1; });
                echo count($actifs);
                ?>
            </h3>
            <p>Actifs</p>
        </div>
        <div class="stat-card">
            <h3>
                <?php 
                $specialites = array_filter(array_column($enseignants, 'specialite'));
                echo count(array_unique($specialites));
                ?>
            </h3>
            <p>Spécialités uniques</p>
        </div>
        <div class="stat-card">
            <h3>
                <?php 
                $avec_email = array_filter($enseignants, function($e) { return !empty($e['email']); });
                echo count($avec_email);
                ?>
            </h3>
            <p>Avec email</p>
        </div>
    </div>
    <?php endif; ?>
</div>
<style>
/* Styles existants de la table */
.content-header{
    margin-bottom: 15px;
}
.table-container {
    background: white;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    margin-bottom: 30px;
}
.data-table {
    width: 100%;
    border-collapse: collapse;
}
.data-table th,
.data-table td {
    padding: 12px 15px;
    text-align: left;
    border-bottom: 1px solid #e1e1e1;
}
.data-table th {
    background-color: #f8f9fa;
    font-weight: 600;
    color: #333;
}
.data-table tr:hover {
    background-color: #f8f9fa;
}
/* Styles pour les badges */
.badge {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 0.8rem;
    font-weight: 500;
    white-space: nowrap;
}
.badge-info {
    background-color: #d1ecf1;
    color: #0c5460;
}
.badge-success {
    background-color: #d4edda;
    color: #155724;
}
.badge-secondary {
    background-color: #e2e3e5;
    color: #383d41;
}
.text-muted {
    color: #6c757d;
}
.text-success {
    color: #28a745;
}
.text-danger {
    color: #dc3545;
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
/* Style pour la cellule vide */
.empty-state-cell {
    text-align: center;
    padding: 60px 20px !important; /* Surcharge du padding de la table */
    color: #6c757d;
}
.empty-state-cell i {
    margin-bottom: 20px;
    color: #bdc3c7;
}
.empty-state-cell h3 {
    margin-bottom: 10px;
    color: #34495e;
}
.empty-state-cell p {
    margin-bottom: 20px;
}
/* Styles pour les cartes de statistiques */
.stats-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
}
.stat-card {
    background: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    text-align: center;
}
.stat-card h3 {
    font-size: 1.8rem;
    margin-bottom: 5px;
    color: #3498db;
}
.stat-card p {
    margin: 0;
    color: #6c757d;
}
/* Styles pour les alertes */
.alert {
    padding: 12px 15px;
    border-radius: 5px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}
.alert i {
    font-size: 1.2rem;
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
.form-actions {
    display: flex;
    gap: 15px;
    margin-top: 30px;
}

.primary {
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

.primary {
    background: #3498db;
    color: white;
}

.primary:hover {
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