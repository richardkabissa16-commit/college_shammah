<?php include __DIR__ . '../../partials/entete.php'; ?>
<?php
$bulletins = $data['bulletins'] ?? [];
$classe = $data['classe'] ?? [];
$trimestre = $data['trimestre'] ?? '1';
?>

<div class="dashboard">
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Rang</th>
                    <th>Matricule</th>
                    <th>Nom et Prénom</th>
                    <th>Moyenne</th>
                    <th>Appréciation</th>
                    <th>Décision</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bulletins as $bulletin): ?>
                <tr>
                    <td><?php echo $bulletin['rang']; ?>ème</td>
                    <td><?php echo $bulletin['matricule']; ?></td>
                    <td><?php echo $bulletin['nom'] . ' ' . $bulletin['prenom']; ?></td>
                    <td><strong><?php echo $bulletin['moyenne_generale']; ?></strong></td>
                    <td><?php echo $bulletin['appreciation']; ?></td>
                    <td>
                        <span class="badge badge-<?php echo $bulletin['decision'] === 'Admis' ? 'success' : 'danger'; ?>">
                            <?php echo $bulletin['decision']; ?>
                        </span>
                    </td>
                    <td>
                        <a href="/college_shammah/public/bulletins.php?action=pdf&id=<?php echo $bulletin['id']; ?>" 
                           class="btn btn-sm btn-primary" target="_blank">
                            <i class="fas fa-eye"></i> Voir
                        </a>
                        <a href="/college_shammah/public/bulletins.php?action=pdf&id=<?php echo $bulletin['id']; ?>&download=1" 
                           class="btn btn-sm btn-secondary">
                            <i class="fas fa-download"></i> PDF
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php if (empty($bulletins)): ?>
        <div class="empty-state">
            <i class="fas fa-file-alt fa-3x"></i>
            <h3>Aucun bulletin généré</h3>
            <p>Les bulletins n'ont pas encore été générés pour cette classe et ce trimestre.</p>
            <a href="/college_shammah/public/bulletins.php" class="btn btn-primary">
                <i class="fas fa-cogs"></i> Générer les bulletins
            </a>
        </div>
    <?php endif; ?>
</div>

<style>
.table-container {
    background: white;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
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

.badge {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 0.8rem;
    font-weight: 600;
}

.badge-success {
    background-color: #d4edda;
    color: #155724;
}

.badge-danger {
    background-color: #f8d7da;
    color: #721c24;
}

.btn-sm {
    padding: 6px 12px;
    font-size: 0.8rem;
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #6c757d;
}

.empty-state i {
    margin-bottom: 20px;
    color: #dee2e6;
}

.empty-state h3 {
    margin-bottom: 10px;
    color: #6c757d;
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