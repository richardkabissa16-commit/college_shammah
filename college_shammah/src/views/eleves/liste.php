<?php include __DIR__ . '../../partials/entete.php'; ?>
<?php
$eleves = $data['eleves'] ?? [];
$classes = $data['classes'] ?? [];
$classe_filtre = $data['classe_filtre'] ?? null;
$stats = $data['stats'] ?? [];
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
    <h1>Gestion des Élèves</h1>
    <div class="header-actions">
        <a href="/college_shammah/public/eleves.php?action=create" class="primary">
            <i class="fas fa-user-plus"></i> Nouvel élève
        </a>
    </div>
</div>

    <!-- Filtres et statistiques -->
    <div class="filters-section">
        <div class="filter-card">
            <h3><i class="fas fa-filter"></i> Filtres</h3>
            <form method="GET" action="/college_shammah/public/eleves.php" class="filter-form">
                <input type="hidden" name="action" value="by_classe">
                <select name="classe_id" onchange="this.form.submit()">
                    <option value="">Toutes les classes</option>
                    <?php foreach ($classes as $classe): ?>
                        <option value="<?php echo $classe['id']; ?>" 
                            <?php echo ($classe_filtre && $classe_filtre['id'] == $classe['id']) ? 'selected' : ''; ?>>
                            <?php echo $classe['nom'] . ' - ' . $classe['niveau']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if ($classe_filtre): ?>
                    <a href="/college_shammah/public/eleves.php" class="btn btn-sm btn-secondary">
                        <i class="fas fa-times"></i> Effacer
                    </a>
                <?php endif; ?>
            </form>
        </div>
        <?php if ($stats && !$classe_filtre): ?>
        <div class="stats-card">
            <h3><i class="fas fa-chart-bar"></i> Statistiques</h3>
            <div class="stats-grid">
                <div class="stat-item">
                    <span class="stat-number"><?php echo $stats[0]['total_eleves'] ?? 0; ?></span>
                    <span class="stat-label">Total élèves</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number"><?php echo $stats[0]['garcons'] ?? 0; ?></span>
                    <span class="stat-label">Garçons</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number"><?php echo $stats[0]['filles'] ?? 0; ?></span>
                    <span class="stat-label">Filles</span>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Tableau des élèves -->
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Matricule</th>
                    <th>Nom et Prénom</th>
                    <th>Classe</th>
                    <th>Date Naissance</th>
                    <th>Sexe</th>
                    <th>Téléphone</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($eleves)): ?>
                    <tr>
                        <td colspan="8" class="empty-state-cell">
                            <i class="fas fa-users fa-3x"></i>
                            <h3>Aucun élève trouvé</h3>
                            <p><?php echo $classe_filtre ? 'Aucun élève dans cette classe.' : 'Aucun élève inscrit pour le moment.'; ?></p>
                            <a href="/college_shammah/public/eleves.php?action=create" class="btn btn-primary">
                                <i class="fas fa-user-plus"></i> Inscrire le premier élève
                            </a>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($eleves as $eleve): ?>
                    <tr>
                        <td>
                            <strong class="matricule"><?php echo $eleve['matricule']; ?></strong>
                        </td>
                        <td>
                            <div class="student-info">
                                <strong><?php echo $eleve['nom'] . ' ' . $eleve['prenom']; ?></strong>
                                <small class="text-muted"><?php echo $eleve['sexe'] === 'M' ? '♂ Garçon' : '♀ Fille'; ?></small>
                            </div>
                        </td>
                        <td>
                            <span class="badge badge-info"><?php echo $eleve['classe_nom'] ?? 'N/A'; ?></span>
                        </td>
                        <td>
                            <?php echo $eleve['date_naissance'] ? date('d/m/Y', strtotime($eleve['date_naissance'])) : '-'; ?>
                            <br>
                            <small class="text-muted">(<?php echo $this->calculateAge($eleve['date_naissance']); ?> ans)</small>
                        </td>
                        <td>
                            <?php echo $eleve['sexe'] === 'M' ? '♂' : '♀'; ?>
                        </td>
                        <td>
                            <?php if (!empty($eleve['telephone'])): ?>
                                <i class="fas fa-phone text-success"></i>
                                <?php echo $eleve['telephone']; ?>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (!empty($eleve['email'])): ?>
                                <i class="fas fa-envelope text-danger"></i>
                                <a href="mailto:<?php echo $eleve['email']; ?>"><?php echo $eleve['email']; ?></a>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td class="actions">
                            <a href="/college_shammah/public/eleves.php?action=show&id=<?php echo $eleve['id']; ?>" 
                               class="btn btn-sm btn-info" title="Voir détails">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="/college_shammah/public/eleves.php?action=edit&id=<?php echo $eleve['id']; ?>" 
                               class="btn btn-sm btn-warning" title="Modifier">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="/college_shammah/public/eleves.php?action=delete&id=<?php echo $eleve['id']; ?>" 
                               class="btn btn-sm btn-danger" 
                               onclick="return confirm('Êtes-vous sûr de vouloir archiver cet élève ?')"
                               title="Archiver">
                                <i class="fas fa-archive"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- ✅ SUPPRESSION DE LA SECTION DE STATISTIQUES CI-DESSOUS -->
    <!--
    <?php if (!empty($eleves) && ($classe_filtre || !$classe_filtre)): ?>
    <div class="stats-cards" style="margin-top: 30px;">
        ... (ancienne section de statistiques)
    </div>
    <?php endif; ?>
    -->

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
.btn-info {
    background-color: #17a2b8;
    color: white;
}
.btn-info:hover {
    background-color: #138496;
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
/* Styles pour la section de filtre */
.filters-section {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 30px;
}
.filter-card, .stats-card {
    background: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
}
.filter-card h3, .stats-card h3 {
    margin-bottom: 15px;
    color: #333;
    display: flex;
    align-items: center;
    gap: 10px;
}
.filter-form {
    display: flex;
    gap: 10px;
    align-items: center;
}
.filter-form select {
    flex: 1;
    padding: 8px 12px;
    border: 2px solid #e1e1e1;
    border-radius: 5px;
}
.stats-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 15px;
}
.stat-item {
    text-align: center;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
}
.stat-number {
    display: block;
    font-size: 1.5rem;
    font-weight: bold;
    color: #3498db;
}
.stat-label {
    font-size: 0.8rem;
    color: #6c757d;
}
.student-info {
    display: flex;
    flex-direction: column;
}
.student-info small {
    color: #6c757d;
    font-size: 0.8rem;
}
.matricule {
    background: #e8f4fd;
    padding: 4px 8px;
    border-radius: 4px;
    font-family: monospace;
    font-weight: bold;
    color: #3498db;
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

<?php
// Fonction pour calculer l'âge
function calculateAge($date_naissance) {
    if (!$date_naissance) return 'N/A';
    $today = new DateTime();
    $birthdate = new DateTime($date_naissance);
    $age = $today->diff($birthdate)->y;
    return $age;
}
?>
<?php include __DIR__ . '../../partials/pied.php'; ?>