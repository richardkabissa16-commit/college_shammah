<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des élèves - <?php echo htmlspecialchars($data['classe']['nom'] ?? 'Classe'); ?></title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 20px;
            background-color: white;
            color: black;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 1.8em;
            color: #333;
        }
        .header p {
            margin: 5px 0 0 0;
            font-size: 1.1em;
            color: #666;
        }
        .info-classe {
            margin-bottom: 15px;
            font-size: 1.1em;
        }
        .info-classe strong {
            display: inline-block;
            width: 120px;
        }
        .table-container {
            width: 100%;
            overflow-x: auto;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .data-table th,
        .data-table td {
            border: 1px solid #333;
            padding: 8px;
            text-align: left;
        }
        .data-table th {
            background-color: #f0f0f0;
            font-weight: bold;
            color: #333;
        }
        .data-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .footer-info {
            margin-top: 30px;
            text-align: right;
            font-size: 0.9em;
            color: #666;
        }
        /* Styles spécifiques pour l'impression */
        @media print {
            body {
                margin: 0;
                font-size: 12px;
            }
            .header h1 {
                font-size: 1.5em;
            }
            .data-table th,
            .data-table td {
                padding: 5px;
            }
            .footer-info {
                position: fixed;
                bottom: 0;
                width: 100%;
                text-align: right;
            }
            /* Cacher les éléments non pertinents pour l'impression */
            .no-print {
                display: none;
            }
        }
        
    </style>
</head>
<body>
    <div class="header no-print">
        <h1>COLLÈGE SHAMMAH</h1>
        <p>BP 20176, Lomé-Adélikopé - Tel. 93 54 15 04</p>
        <h2>Liste des élèves</h2>
    </div>

    <?php
    $classe = $data['classe'] ?? null;
    $eleves = $data['eleves'] ?? [];
    if (!$classe) {
        echo "<p class='error'>Classe non trouvée.</p>";
        exit;
    }
    ?>
    <div class="info-classe">
        <p><strong>Classe :</strong> <?php echo htmlspecialchars($classe['nom']); ?> - <?php echo htmlspecialchars($classe['niveau']); ?></p>
        <p><strong>Date d'impression :</strong> <?php echo date('d/m/Y H:i'); ?></p>
    </div>

    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>N°</th>
                    <th>Matricule</th>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Date Naissance</th>
                    <th>Lieu Naissance</th>
                    <th>Sexe</th>
                    <th>Adresse</th>
                    <th>Téléphone</th>
                    <th>Nom du Père</th>
                    <th>Nom de la Mère</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($eleves)): ?>
                    <?php $numero = 1; ?>
                    <?php foreach ($eleves as $eleve): ?>
                    <tr>
                        <td><?php echo $numero++; ?></td>
                        <td><?php echo htmlspecialchars($eleve['matricule'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($eleve['nom'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($eleve['prenom'] ?? '-'); ?></td>
                        <td><?php echo !empty($eleve['date_naissance']) ? date('d/m/Y', strtotime($eleve['date_naissance'])) : '-'; ?></td>
                        <td><?php echo htmlspecialchars($eleve['lieu_naissance'] ?? '-'); ?></td>
                        <td><?php echo ($eleve['sexe'] ?? '') === 'M' ? '♂' : '♀'; ?></td>
                        <td><?php echo htmlspecialchars($eleve['adresse'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($eleve['telephone'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($eleve['nom_pere'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($eleve['nom_mere'] ?? '-'); ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="11" style="text-align: center;">Aucun élève inscrit dans cette classe.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="footer-info no-print">
        <p>Imprimé le <?php echo date('d/m/Y H:i'); ?> par <?php echo htmlspecialchars($_SESSION['username'] ?? 'Utilisateur'); ?></p>
    </div>

    <script>
        // Déclencher l'impression automatiquement lorsque la page est chargée
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>