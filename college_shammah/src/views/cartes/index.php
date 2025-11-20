<?php include __DIR__ . '../../partials/entete.php'; ?>
<?php
$classes = $data['classes'] ?? [];
$cartes_generees = $data['cartes_generees'] ?? [];
$eleves_sans_carte = $data['eleves_sans_carte'] ?? [];
?>

<div class="dashboard">
    <!-- Messages de notification -->
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

    <?php if (isset($_SESSION['warning'])): ?>
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle"></i>
            <?php echo $_SESSION['warning']; unset($_SESSION['warning']); ?>
        </div>
    <?php endif; ?>

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
        
        <div class="stat-card warning">
            <div class="stat-icon">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo count($eleves_sans_carte); ?></h3>
                <p>Cartes à générer</p>
            </div>
        </div>
        
        <div class="stat-card info">
            <div class="stat-icon">
                <i class="fas fa-school"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo count($classes); ?></h3>
                <p>Classes</p>
            </div>
        </div>

        <div class="stat-card primary">
            <div class="stat-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo count($cartes_generees) + count($eleves_sans_carte); ?></h3>
                <p>Total élèves</p>
            </div>
        </div>
    </div>

    <!-- Génération par classe -->
    <div class="form-container">
        <h3><i class="fas fa-cogs"></i> Génération par classe</h3>
        <form method="POST" action="/college_shammah/public/cartes.php?action=generer" class="generation-form">
            <div class="form-row">
                <div class="form-group flex-grow">
                    <label for="classe_id">Sélectionner une classe</label>
                    <select name="classe_id" id="classe_id" required class="form-control">
                        <option value="">-- Choisir une classe --</option>
                        <?php foreach ($classes as $classe): 
                            // Compter les élèves sans carte dans cette classe
                            $eleves_classe_sans_carte = array_filter($eleves_sans_carte, function($eleve) use ($classe) {
                                return $eleve['classe_id'] == $classe['id'];
                            });
                            $nb_sans_carte = count($eleves_classe_sans_carte);
                        ?>
                            <option value="<?php echo $classe['id']; ?>">
                                <?php echo $classe['nom'] . ' - ' . $classe['niveau']; ?>
                                <?php if ($nb_sans_carte > 0): ?>
                                    (<?php echo $nb_sans_carte; ?> carte<?php echo $nb_sans_carte > 1 ? 's' : ''; ?> à générer)
                                <?php else: ?>
                                    (Toutes les cartes générées)
                                <?php endif; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-id-card"></i> Générer les cartes de cette classe
                </button>
            </div>
        </form>
    </div>

    <!-- Cartes générées -->
    <div class="table-section">
        <h3>
            <i class="fas fa-check-circle"></i> 
            Cartes générées (<?php echo count($cartes_generees); ?>)
        </h3>
        
        <?php if (!empty($cartes_generees)): ?>
            <div class="table-actions">
                <a href="/college_shammah/public/cartes.php?action=pdf" class="btn btn-primary" target="_blank">
                    <i class="fas fa-file-pdf"></i> Télécharger toutes les cartes (PDF)
                </a>
            </div>

            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Photo</th>
                            <th>N° Carte</th>
                            <th>Matricule</th>
                            <th>Nom & Prénom</th>
                            <th>Classe</th>
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
                                <span class="numero-carte">
                                    <i class="fas fa-id-badge"></i>
                                    <?php echo $carte['numero_carte']; ?>
                                </span>
                            </td>
                            <td>
                                <strong><?php echo $carte['matricule']; ?></strong>
                            </td>
                            <td>
                                <strong><?php echo $carte['nom'] . ' ' . $carte['prenom']; ?></strong>
                            </td>
                            <td>
                                <span class="badge badge-info">
                                    <?php echo $carte['classe_nom'] . ' - ' . $carte['niveau']; ?>
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
                                       target="_blank" 
                                       title="Télécharger la carte PDF">
                                        <i class="fas fa-download"></i>
                                    </a>
                                    <a href="/college_shammah/public/cartes.php?action=reinitialiser&eleve_id=<?php echo $carte['id']; ?>" 
                                       class="btn btn-sm btn-danger"
                                       onclick="return confirm('Êtes-vous sûr de vouloir réinitialiser cette carte ? L\'élève recevra un nouveau numéro.')"
                                       title="Réinitialiser la carte">
                                        <i class="fas fa-redo"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="empty-state-small">
                <i class="fas fa-id-card"></i>
                <p>Aucune carte générée pour le moment</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Élèves sans carte -->
    <?php if (!empty($eleves_sans_carte)): ?>
    <div class="table-section">
        <h3>
            <i class="fas fa-exclamation-circle"></i> 
            Élèves sans carte (<?php echo count($eleves_sans_carte); ?>)
        </h3>
        
        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Matricule</th>
                        <th>Nom & Prénom</th>
                        <th>Classe</th>
                        <th>Date de naissance</th>
                        <th>Date d'inscription</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($eleves_sans_carte as $eleve): ?>
                    <tr>
                        <td>
                            <strong><?php echo $eleve['matricule']; ?></strong>
                        </td>
                        <td>
                            <strong><?php echo $eleve['nom'] . ' ' . $eleve['prenom']; ?></strong>
                        </td>
                        <td>
                            <span class="badge badge-secondary">
                                <?php echo $eleve['classe_nom'] . ' - ' . $eleve['niveau']; ?>
                            </span>
                        </td>
                        <td>
                            <?php 
                            if (!empty($eleve['date_naissance'])) {
                                echo date('d/m/Y', strtotime($eleve['date_naissance']));
                            } else {
                                echo '<span class="text-muted">N/A</span>';
                            }
                            ?>
                        </td>
                        <td>
                            <?php 
                            if (!empty($eleve['date_inscription'])) {
                                echo date('d/m/Y', strtotime($eleve['date_inscription']));
                            } else {
                                echo '<span class="text-muted">N/A</span>';
                            }
                            ?>
                        </td>
                        <td class="text-center">
                            <form method="POST" action="/college_shammah/public/cartes.php?action=generer" style="display: inline;">
                                <input type="hidden" name="classe_id" value="<?php echo $eleve['classe_id']; ?>">
                                <button type="submit" class="btn btn-sm btn-success">
                                    <i class="fas fa-id-card"></i> Générer
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>
</div>

<style>
/* Photos d'élèves */
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

/* Numéro de carte */
.numero-carte {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 6px 12px;
    border-radius: 20px;
    font-weight: bold;
    font-size: 0.85rem;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    white-space: nowrap;
}

/* Statistiques */
.stats-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: white;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.08);
    display: flex;
    align-items: center;
    transition: transform 0.3s, box-shadow 0.3s;
    border-left: 4px solid #3498db;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.12);
}

.stat-card.success {
    border-left-color: #27ae60;
}

.stat-card.success .stat-icon {
    background-color: rgba(39, 174, 96, 0.1);
    color: #27ae60;
}

.stat-card.warning {
    border-left-color: #f39c12;
}

.stat-card.warning .stat-icon {
    background-color: rgba(243, 156, 18, 0.1);
    color: #f39c12;
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
    color: #2c3e50;
}

.stat-info p {
    color: #7f8c8d;
    font-size: 0.95rem;
    margin: 0;
}

/* Sections de tableau */
.table-section {
    background: white;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
    margin-bottom: 25px;
}

.table-section h3 {
    margin-bottom: 20px;
    color: #2c3e50;
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 1.3rem;
    border-bottom: 2px solid #ecf0f1;
    padding-bottom: 15px;
}

.table-actions {
    margin-bottom: 20px;
    display: flex;
    justify-content: flex-end;
}

/* Actions */
.action-buttons {
    display: flex;
    gap: 5px;
    justify-content: center;
}

/* Badges */
.badge {
    padding: 5px 10px;
    border-radius: 12px;
    font-size: 0.85rem;
    font-weight: 500;
    white-space: nowrap;
}

.badge-info {
    background-color: #d1ecf1;
    color: #0c5460;
}

.badge-secondary {
    background-color: #e2e3e5;
    color: #383d41;
}

/* Empty state */
.empty-state-small {
    text-align: center;
    padding: 40px 20px;
    color: #95a5a6;
}

.empty-state-small i {
    font-size: 3rem;
    margin-bottom: 10px;
    color: #bdc3c7;
}

.empty-state-small p {
    font-size: 1.1rem;
    margin: 0;
}

/* Alerts */
.alert {
    padding: 15px 20px;
    border-radius: 8px;
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

.alert-warning {
    background-color: #fff3cd;
    color: #856404;
    border: 1px solid #ffeaa7;
}

/* Form */
.generation-form .form-group {
    margin-bottom: 0;
}

.flex-grow {
    flex: 1;
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