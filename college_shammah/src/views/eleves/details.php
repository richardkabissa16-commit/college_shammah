<?php include __DIR__ . '../../partials/entete.php'; ?>
<?php
$eleve = $data['eleve'] ?? null;
$notes = $data['notes'] ?? [];

if (!$eleve) {
    echo "<div class='alert alert-error'>Élève non trouvé</div>";
    return;
}
?>


<div class="dashboard">
    <div class="student-profile">
        <!-- En-tête du profil -->
        <div class="profile-header">
            <div class="profile-avatar">
                <i class="fa fa-user-graduate fa-3x"></i>
            </div>
            <div class="profile-info">
                <h2><?php echo $eleve['nom'] . ' ' . $eleve['prenom']; ?></h2>
                <p class="matricule">Matricule: <?php echo $eleve['matricule']; ?></p>
                <div class="profile-badges">
                    <span class="badge badge-info"><?php echo $eleve['sexe'] === 'M' ? 'Garçon' : 'Fille'; ?></span>
                    <span class="badge badge-secondary"><?php echo $this->calculateAge($eleve['date_naissance']); ?> ans</span>
                </div>
            </div>
        </div>

        <!-- Informations détaillées -->
        <div class="info-grid">
            <div class="info-card">
                <h3><i class="fas fa-info-circle"></i> Informations personnelles</h3>
                <div class="info-list">
                    <div class="info-item">
                        <strong>Date de naissance:</strong>
                        <span><?php echo date('d/m/Y', strtotime($eleve['date_naissance'])); ?></span>
                    </div>
                    <div class="info-item">
                        <strong>Lieu de naissance:</strong>
                        <span><?php echo $eleve['lieu_naissance'] ?: '-'; ?></span>
                    </div>
                    <div class="info-item">
                        <strong>Téléphone:</strong>
                        <span><?php echo $eleve['telephone'] ?: '-'; ?></span>
                    </div>
                    <div class="info-item">
                        <strong>Email:</strong>
                        <span><?php echo $eleve['email'] ?: '-'; ?></span>
                    </div>
                    <div class="info-item">
                        <strong>Adresse:</strong>
                        <span><?php echo $eleve['adresse'] ?: '-'; ?></span>
                    </div>
                </div>
            </div>

            <div class="info-card">
                <h3><i class="fas fa-users"></i> Informations parents</h3>
                <div class="info-list">
                    <div class="info-item">
                        <strong>Nom du père:</strong>
                        <span><?php echo $eleve['nom_pere'] ?: '-'; ?></span>
                    </div>
                    <div class="info-item">
                        <strong>Nom de la mère:</strong>
                        <span><?php echo $eleve['nom_mere'] ?: '-'; ?></span>
                    </div>
                    <div class="info-item">
                        <strong>Téléphone parents:</strong>
                        <span><?php echo $eleve['telephone_parent'] ?: '-'; ?></span>
                    </div>
                </div>
            </div>

            <div class="info-card">
                <h3><i class="fas fa-graduation-cap"></i> Informations scolaires</h3>
                <div class="info-list">
                    <div class="info-item">
                        <strong>Date d'inscription:</strong>
                        <span><?php echo date('d/m/Y H:i', strtotime($eleve['date_inscription'])); ?></span>
                    </div>
                    <div class="info-item">
                        <strong>Année scolaire:</strong>
                        <span>2024-2025</span>
                    </div>
                    <div class="info-item">
                        <strong>Statut:</strong>
                        <span class="badge badge-success">Actif</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dernières notes -->
        <div class="notes-section">
            <h3><i class="fas fa-file-alt"></i> Dernières notes</h3>
            
            <?php if (!empty($notes)): ?>
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Matière</th>
                                <th>Type</th>
                                <th>Trimestre</th>
                                <th>Note</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($notes as $note): ?>
                            <tr>
                                <td><?php echo $note['matiere_nom']; ?></td>
                                <td>
                                    <span class="badge badge-info"><?php echo $note['type_note']; ?></span>
                                </td>
                                <td>T<?php echo $note['trimestre']; ?></td>
                                <td>
                                    <strong><?php echo $note['note']; ?></strong>/20
                                </td>
                                <td><?php echo date('d/m/Y H:i', strtotime($note['date_saisie'])); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-file-alt fa-2x"></i>
                    <p>Aucune note enregistrée pour cet élève</p>
                </div>
            <?php endif; ?>
            
            <div class="section-actions">
                <a href="/college_shammah/public/notes.php?action=saisie&eleve_id=<?php echo $eleve['id']; ?>" class="btn btn-primary">
                    <i class="fas fa-edit"></i> Saisir des notes
                </a>
                <a href="/college_shammah/public/bulletins.php?action=generer&eleve_id=<?php echo $eleve['id']; ?>" class="btn btn-secondary">
                    <i class="fas fa-file-alt"></i> Générer bulletin
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.student-profile {
    max-width: 100%;
}

.profile-header {
    background: white;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    display: flex;
    align-items: center;
    gap: 20px;
    margin-bottom: 30px;
}

.profile-avatar {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: linear-gradient(135deg, #3498db, #2980b9);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
}

.profile-info h2 {
    margin: 0 0 5px 0;
    color: #333;
}

.profile-info .matricule {
    color: #6c757d;
    font-size: 0.9rem;
}

.profile-badges {
    display: flex;
    gap: 10px;
    margin-top: 10px;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.info-card {
    background: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
}

.info-card h3 {
    margin-bottom: 15px;
    color: #3498db;
    display: flex;
    align-items: center;
    gap: 10px;
    border-bottom: 1px solid #f0f0f0;
    padding-bottom: 10px;
}

.info-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.info-item {
    display: flex;
    justify-content: between;
    align-items: flex-start;
}

.info-item strong {
    min-width: 150px;
    color: #333;
}

.info-item span {
    flex: 1;
    color: #6c757d;
}

.notes-section {
    background: white;
    padding: 25px;
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
}

.notes-section h3 {
    margin-bottom: 20px;
    color: #3498db;
    display: flex;
    align-items: center;
    gap: 10px;
}

.section-actions {
    display: flex;
    gap: 15px;
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px solid #f0f0f0;
}

@media (max-width: 768px) {
    .profile-header {
        flex-direction: column;
        text-align: center;
    }
    
    .info-item {
        flex-direction: column;
        gap: 5px;
    }
    
    .section-actions {
        flex-direction: column;
    }
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

<?php
// Fonction pour calculer l'âge
function calculateAge($date_naissance) {
    $today = new DateTime();
    $birthdate = new DateTime($date_naissance);
    $age = $today->diff($birthdate)->y;
    return $age;
}
?>
<?php include __DIR__ . '../../partials/pied.php'; ?>