<?php
$stats = $data['stats'] ?? [];
$activites_recentes = $data['activites_recentes'] ?? [];
$effectifs_par_classe = $data['effectifs_par_classe'] ?? [];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord - Collège Shammah</title>
    <link rel="stylesheet" href="/college_shammah/public/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <?php include __DIR__ . '/partials/entete.php'; ?>
        
        <main class="main-content">
            <header class="content-header">
                <h1>Tableau de Bord</h1>
                <div class="header-actions">
                    <div class="user-auth">
                        <div class="auth-icon">
                            <i class="fas fa-user-circle"></i>
                        </div>
                        <div class="auth-info">
                            <span class="auth-name"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                            <span class="auth-role"><?php echo htmlspecialchars(ucfirst($_SESSION['role'])); ?></span>
                        </div>
                    </div>
                </div>
            </header>

            <div class="dashboard">
                <!-- Cartes de statistiques -->
                <div class="stats-cards">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo $stats['total_eleves'] ?? 0; ?></h3>
                            <p>Élèves inscrits</p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-chalkboard-teacher"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo $stats['total_enseignants'] ?? 0; ?></h3>
                            <p>Enseignants</p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-book"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo $stats['total_matieres'] ?? 0; ?></h3>
                            <p>Matières enseignées</p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo $stats['total_classes'] ?? 0; ?></h3>
                            <p>Classes actives</p>
                        </div>
                    </div>
                </div>

                <!-- Graphiques et tableaux -->
                <div class="dashboard-content">
                    <div class="content-row">
                        <div class="chart-container">
                            <div class="chart-header">
                                <h3>Répartition des effectifs par classe</h3>
                            </div>
                            <div class="chart-placeholder">
                                <?php foreach ($effectifs_par_classe as $classe): 
                                    $pourcentage = min(($classe['effectif'] / 40) * 100, 100);
                                ?>
                                    <div class="chart-bar" 
                                         style="height: <?php echo $pourcentage; ?>%;"
                                         data-level="<?php echo htmlspecialchars($classe['nom']); ?>"
                                         data-value="<?php echo $classe['effectif']; ?>">
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="chart-labels">
                                <?php foreach ($effectifs_par_classe as $classe): ?>
                                    <span title="<?php echo htmlspecialchars($classe['nom']); ?>">
                                        <?php echo substr($classe['nom'], 0, 5); ?> (<?php echo $classe['effectif']; ?>)
                                    </span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        
                        <div class="recent-activities">
                            <div class="activities-header">
                                <h3>Activités récentes</h3>
                            </div>
                            <ul class="activities-list">
                                <?php if (!empty($activites_recentes)): ?>
                                    <?php foreach ($activites_recentes as $activite): ?>
                                    <li>
                                        <div class="activity-icon">
                                            <i class="fas fa-<?php echo $activite['icone']; ?>"></i>
                                        </div>
                                        <div class="activity-details">
                                            <p><?php echo htmlspecialchars($activite['message']); ?></p>
                                            <span><?php echo $this->formatDate($activite['date']); ?></span>
                                        </div>
                                    </li>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <li>
                                        <div class="activity-details">
                                            <p>Aucune activité récente</p>
                                        </div>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>

                   
                </div>
            </div>
        </main>
    </div>

    <script src="/college_shammah/public/js/script.js"></script>
</body>
</html>
