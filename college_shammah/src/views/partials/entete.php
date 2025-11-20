<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion Collège - Dashboard</title>
    <link rel="stylesheet" href="/college_shammah/public/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <!-- Navigation -->
        <nav class="navbar">
            <div class="nav-header">
                <div class="logo">
                    <i class="fas fa-school"></i>
                    <span>SHAMMAH</span>
                </div>
                <button class="nav-toggle" id="navToggle">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
            
            <ul class="nav-menu" id="navMenu">
                <li class="nav-item">
                    <a href="/college_shammah/public/dashboard.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                
                <li class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle">
                        <i class="fas fa-users"></i>
                        <span>Élèves</span>
                        <i class="fas fa-chevron-down dropdown-icon"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="/college_shammah/public/eleves.php"><i class="fas fa-list"></i> Liste des élèves</a></li>
                        <li><a href="/college_shammah/public/eleves.php?action=create"><i class="fas fa-user-plus"></i> Inscription</a></li>
                    </ul>
                </li>

                <li class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle">
                        <i class="fas fa-chalkboard-teacher"></i>
                        <span>Enseignants</span>
                        <i class="fas fa-chevron-down dropdown-icon"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="/college_shammah/public/enseignants.php"><i class="fas fa-list"></i> Liste</a></li>
                        <li><a href="/college_shammah/public/enseignants.php?action=create"><i class="fas fa-user-plus"></i> Nouvel enseignant</a></li>
                    </ul>
                </li>

                <li class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle">
                        <i class="fas fa-chalkboard-teacher"></i>
                        <span>Classes</span>
                        <i class="fas fa-chevron-down dropdown-icon"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="/college_shammah/public/classes.php"><i class="fas fa-list"></i> Liste</a></li>
                        <li><a href="/college_shammah/public/classes.php?action=create"><i class="fas fa-user-plus"></i> Nouvelle classe</a></li>
                    </ul>
                </li>

                <li class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle">
                        <i class="fas fa-chalkboard-teacher"></i>
                        <span>Matieres</span>
                        <i class="fas fa-chevron-down dropdown-icon"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="/college_shammah/public/matieres.php"><i class="fas fa-list"></i> Liste</a></li>
                        <li><a href="/college_shammah/public/matieres.php?action=create"><i class="fas fa-user-plus"></i> Nouvelle matiere</a></li>
                    </ul>
                </li>
                
                <li class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle">
                        <i class="fas fa-file-alt"></i>
                        <span>Notes</span>
                        <i class="fas fa-chevron-down dropdown-icon"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="/college_shammah/public/notes.php?action=saisie"><i class="fas fa-edit"></i> Saisie des notes</a></li>
                        <li><a href="/college_shammah/public/notes.php?action=visualiser"><i class="fas fa-chart-bar"></i> Visualisation</a></li>
                    </ul>
                </li>
                
                <li class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle">
                        <i class="fas fa-graduation-cap"></i>
                        <span>Bulletins</span>
                        <i class="fas fa-chevron-down dropdown-icon"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="/college_shammah/public/bulletins.php"><i class="fas fa-cogs"></i> Génération</a></li>
                        <li><a href="/college_shammah/public/bulletins.php?action=resultats"><i class="fas fa-print"></i> Impression</a></li>
                    </ul>
                </li>
                
                <li class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle">
                        <i class="fas fa-id-card"></i>
                        <span>Cartes Scolaires</span>
                        <i class="fas fa-chevron-down dropdown-icon"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="/college_shammah/public/cartes.php"><i class="fas fa-cogs"></i> Génération</a></li>
                        <li><a href="/college_shammah/public/cartes.php?action=pdf"><i class="fas fa-print"></i> Impression</a></li>
                    </ul>
                </li>
                
                
            </ul>
            
            <div class="nav-footer">
                <a href="/college_shammah/public/logout.php" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Déconnexion</span>
                </a>
            </div>
        </nav>