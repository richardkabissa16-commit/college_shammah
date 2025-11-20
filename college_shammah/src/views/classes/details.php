<?php include __DIR__ . '../../partials/entete.php'; ?>

<?php
$classe = $data['classe'] ?? null;
$eleves = $data['eleves'] ?? [];
if (!$classe) {
    echo "<div class='alert alert-error'>Classe non trouvée</div>";
    include __DIR__ . '../../partials/pied.php'; 
    exit;
}
?>

<div class="dashboard">
<div class="content-header">
    <h1>Détails de la classe : <?php echo htmlspecialchars($classe['nom']); ?></h1>
    <div class="header-actions">
        <a href="/college_shammah/public/classes.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour à la liste
        </a>
        <!-- Bouton pour imprimer la liste des élèves -->
        <a href="/college_shammah/public/classes.php?action=listeEleves&id=<?php echo $classe['id']; ?>" 
           class="btn btn-primary" >
            <i class="fas fa-print"></i> Imprimer la liste
        </a>
    </div>
</div>

    <div class="class-details">
        <div class="class-header">
            <h2><?php echo htmlspecialchars($classe['nom']); ?> - <?php echo htmlspecialchars($classe['niveau']); ?></h2>
        </div>
        <div class="class-stats">
            <div class="stat-card">
                <div class="stat-info">
                    <h3><?php echo count($eleves); ?></h3> <!-- Affiche le nombre d'élèves récupérés -->
                    <p>Élèves inscrits</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-info">
                    <h3><?php echo htmlspecialchars($classe['effectif_max']); ?></h3>
                    <p>Effectif maximum</p>
                </div>
            </div>
        </div>
        <!-- Section pour afficher la liste des élèves -->
        <div class="table-section">
            <h3>Élèves de la classe</h3>
            <?php if (!empty($eleves)): ?>
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Matricule</th>
                                <th>Nom</th>
                                <th>Prénom</th>
                                <th>Date de naissance</th>
                                <th>Sexe</th>
                                <th>Photo</th> <!-- Colonne photo ajoutée -->
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($eleves as $eleve): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($eleve['matricule']); ?></td>
                                <td><?php echo htmlspecialchars($eleve['nom']); ?></td>
                                <td><?php echo htmlspecialchars($eleve['prenom']); ?></td>
                                <td><?php echo htmlspecialchars($eleve['date_naissance']); ?></td>
                                <td><?php echo $eleve['sexe'] === 'M' ? '♂' : '♀'; ?></td>
                                <td class="text-center">
                                    <?php if (!empty($eleve['photo'])): ?>
                                        <img src="/college_shammah/public/uploads/photos/<?php echo $eleve['photo']; ?>" 
                                             alt="Photo de <?php echo htmlspecialchars($eleve['nom'] . ' ' . $eleve['prenom']); ?>" 
                                             class="student-photo-small">
                                    <?php else: ?>
                                        <div class="no-photo-small">
                                            <i class="fas fa-user"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p>Aucun élève inscrit dans cette classe.</p>
            <?php endif; ?>
        </div>
    </div>
</div>
<style>
/* Styles pour les détails de la classe */
.content-header {
    margin-bottom: 20px;
}
.class-details {
    background: white;
    padding: 25px;
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
}
.class-header {
    margin-bottom: 20px;
}
.class-header h2 {
    color: #3498db;
}
.class-stats {
    display: flex;
    gap: 20px;
    margin-bottom: 30px;
}
.stat-card {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    flex: 1;
    text-align: center;
}
.stat-card h3 {
    margin: 0 0 5px 0;
    color: #3498db;
}
.stat-card p {
    margin: 0;
    color: #6c757d;
}
/* Styles pour la section de la liste des élèves */
.table-section {
    margin-top: 30px;
}
.table-section h3 {
    margin-bottom: 15px;
    color: #333;
}
.table-container {
    overflow-x: auto; /* Permet le défilement horizontal si le tableau est trop large */
}
.data-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
}
.data-table th,
.data-table td {
    border: 1px solid #ddd;
    padding: 8px;
    text-align: left;
}
.data-table th {
    background-color: #f2f2f2;
}
/* Styles pour les photos des élèves dans le tableau */
.student-photo-small {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover; /* Assure que l'image remplit le conteneur sans déformation */
    border: 2px solid #3498db;
}
.no-photo-small {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #e9ecef;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6c757d;
    font-size: 0.8rem;
    margin: 0 auto; /* Centre l'icône dans la cellule */
}
.text-center {
    text-align: center;
}
.form-actions {
    display: flex;
    gap: 15px;
    align-items: center;
    flex-wrap: wrap;
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
    font-weight: 500;
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

.btn-danger {
    background: #e74c3c;
    color: white;
}

.btn-danger:hover {
    background: #c0392b;
}

.info-box {
    background: #e8f4fd;
    border-left: 4px solid #3498db;
    padding: 20px;
    border-radius: 5px;
    margin-top: 20px;
}

.info-box h3 {
    color: #3498db;
    margin-bottom: 15px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.info-box ul {
    margin: 10px 0;
    padding-left: 20px;
}

.info-box li {
    margin-bottom: 8px;
    line-height: 1.4;
}

@media (max-width: 768px) {
    .form-row {
        grid-template-columns: 1fr;
    }
    
    .page-header {
        flex-direction: column;
        gap: 15px;
        align-items: flex-start;
    }
    
    .form-actions {
        flex-direction: column;
        align-items: stretch;
    }
    
    .form-actions .btn {
        justify-content: center;
    }
}
</style>

<?php include __DIR__ . '../../partials/pied.php'; ?>