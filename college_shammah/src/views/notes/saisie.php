<?php include __DIR__ . '../../partials/entete.php'; ?>
<?php
$classes = $data['classes'] ?? [];
$matieres = $data['matieres'] ?? [];
$types_notes = $data['types_notes'] ?? [];
$eleves = $data['eleves'] ?? [];
$notes_existantes = $data['notes_existantes'] ?? [];
$classe_selectionnee = $data['classe_selectionnee'] ?? '';
$matiere_selectionnee = $data['matiere_selectionnee'] ?? '';
$trimestre = $data['trimestre'] ?? '1';
$annee_scolaire = $data['annee_scolaire'] ?? [];
?>

<div class="dashboard">
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error">
            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['warning'])): ?>
        <div class="alert alert-warning">
            <?php echo $_SESSION['warning']; unset($_SESSION['warning']); ?>
        </div>
    <?php endif; ?>

    <div class="content-header">
    <h1>Saisie des Notes</h1>
    <div class="header-actions">
        <a href="/college_shammah/public/notes.php?action=visualiser" class="btn btn-secondary">
            <i class="fas fa-chart-bar"></i> Visualiser les notes
        </a>
        <a href="/college_shammah/public/notes.php?action=gestion" class="primary">
            <i class="fas fa-cog"></i> Gérer les notes
        </a>
    </div>
</div>

    <!-- Formulaire de sélection -->
    <div class="form-container">
        <form method="GET" action="/college_shammah/public/notes.php" class="filter-form">
            <input type="hidden" name="action" value="saisie">
            
            <div class="form-row">
                <div class="form-group">
                    <label for="classe_id">Classe</label>
                    <select name="classe_id" id="classe_id" required onchange="this.form.submit()">
                        <option value="">Sélectionner une classe</option>
                        <?php foreach ($classes as $classe): ?>
                            <option value="<?php echo $classe['id']; ?>" 
                                <?php echo $classe_selectionnee == $classe['id'] ? 'selected' : ''; ?>>
                                <?php echo $classe['nom'] . ' - ' . $classe['niveau']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="matiere_id">Matière</label>
                    <select name="matiere_id" id="matiere_id" required onchange="this.form.submit()">
                        <option value="">Sélectionner une matière</option>
                        <?php foreach ($matieres as $matiere): ?>
                            <option value="<?php echo $matiere['id']; ?>" 
                                <?php echo $matiere_selectionnee == $matiere['id'] ? 'selected' : ''; ?>>
                                <?php echo $matiere['nom'] . ' (' . $matiere['code'] . ')'; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="trimestre">Trimestre</label>
                    <select name="trimestre" id="trimestre" onchange="this.form.submit()">
                        <option value="1" <?php echo $trimestre == '1' ? 'selected' : ''; ?>>1er Trimestre</option>
                        <option value="2" <?php echo $trimestre == '2' ? 'selected' : ''; ?>>2ème Trimestre</option>
                        <option value="3" <?php echo $trimestre == '3' ? 'selected' : ''; ?>>3ème Trimestre</option>
                    </select>
                </div>
            </div>
        </form>
    </div>

    <!-- Formulaire de saisie des notes -->
    <?php if ($classe_selectionnee && $matiere_selectionnee && !empty($eleves)): ?>
    <div class="saisie-container">
        <form method="POST" action="/college_shammah/public/notes.php?action=enregistrer">
            <input type="hidden" name="classe_id" value="<?php echo $classe_selectionnee; ?>">
            <input type="hidden" name="matiere_id" value="<?php echo $matiere_selectionnee; ?>">
            <input type="hidden" name="trimestre" value="<?php echo $trimestre; ?>">
            <input type="hidden" name="annee_scolaire_id" value="<?php echo $annee_scolaire['id']; ?>">
            
            <div class="saisie-header">
                <h3>
                    <i class="fas fa-edit"></i> 
                    Saisie des notes - 
                    <?php 
                    $matiere_nom = '';
                    foreach ($matieres as $m) {
                        if ($m['id'] == $matiere_selectionnee) {
                            $matiere_nom = $m['nom'];
                            break;
                        }
                    }
                    echo $matiere_nom;
                    ?>
                    - Trimestre <?php echo $trimestre; ?>
                </h3>
                
                <div class="type-note-selector">
                    <label for="type_note_id">Type de note:</label>
                    <select name="type_note_id" id="type_note_id" required>
                        <option value="">Sélectionner le type</option>
                        <?php foreach ($types_notes as $type): ?>
                            <option value="<?php echo $type['id']; ?>">
                                <?php echo $type['libelle']; ?> (Coeff. <?php echo $type['coefficient']; ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="table-container">
                <table class="data-table saisie-table">
                    <thead>
                        <tr>
                            <th width="50">N°</th>
                            <th>Matricule</th>
                            <th>Nom et Prénom</th>
                            <th width="120">Note /20</th>
                            <th width="100">Historique</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($eleves as $index => $eleve): 
                            $notes_eleve = $notes_existantes[$eleve['id']] ?? [];
                        ?>
                        <tr>
                            <td class="text-center"><?php echo $index + 1; ?></td>
                            <td>
                                <span class="matricule"><?php echo $eleve['matricule']; ?></span>
                            </td>
                            <td>
                                <strong><?php echo $eleve['nom'] . ' ' . $eleve['prenom']; ?></strong>
                            </td>
                            <td>
                                <input type="text" 
                                       name="notes[<?php echo $eleve['id']; ?>]" 
                                       class="note-input"
                                       placeholder="0-20"
                                       maxlength="5"
                                       oninput="validateNote(this)">
                            </td>
                            <td class="historique-notes">
                                <?php if (!empty($notes_eleve)): ?>
                                    <?php foreach ($notes_eleve as $type_id => $note): ?>
                                        <small>
                                            <?php 
                                            $type_nom = '';
                                            foreach ($types_notes as $t) {
                                                if ($t['id'] == $type_id) {
                                                    $type_nom = $t['libelle'];
                                                    break;
                                                }
                                            }
                                            echo $type_nom . ': ' . $note['note']; 
                                            ?>
                                        </small><br>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <small class="text-muted">Aucune note</small>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="form-actions">
                <button type="submit" class="primary">
                    <i class="fas fa-save"></i> Enregistrer les notes
                </button>
                <button type="reset" class="btn btn-secondary">
                    <i class="fas fa-undo"></i> Réinitialiser
                </button>
            </div>
        </form>
    </div>
    <?php elseif ($classe_selectionnee && $matiere_selectionnee && empty($eleves)): ?>
        <div class="empty-state">
            <i class="fas fa-users fa-3x"></i>
            <h3>Aucun élève dans cette classe</h3>
            <p>La classe sélectionnée ne contient aucun élève.</p>
        </div>
    <?php elseif (!$classe_selectionnee || !$matiere_selectionnee): ?>
        <div class="empty-state">
            <i class="fas fa-edit fa-3x"></i>
            <h3>Sélectionnez une classe et une matière</h3>
            <p>Veuillez choisir une classe et une matière pour commencer la saisie des notes.</p>
        </div>
    <?php endif; ?>
</div>

<style>
    .content-header{
    margin-bottom: 15px;
}
.filter-form {
    background: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    margin-bottom: 20px;
}

.saisie-container {
    background: white;
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    overflow: hidden;
}

.saisie-header {
    padding: 20px;
    background: #f8f9fa;
    border-bottom: 1px solid #e1e1e1;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.saisie-header h3 {
    margin: 0;
    color: #333;
    
}

.type-note-selector {
    display: flex;
    align-items: center;
    gap: 10px;
}

.type-note-selector select {
    padding: 8px 12px;
    border: 2px solid #e1e1e1;
    border-radius: 5px;
    min-width: 200px;
}

.saisie-table th {
    background: #3498db;
    color: white;
    font-weight: 600;
}

.note-input {
    width: 100%;
    padding: 8px 10px;
    border: 2px solid #e1e1e1;
    border-radius: 5px;
    text-align: center;
    font-size: 14px;
    font-weight: bold;
}

.note-input:focus {
    border-color: #3498db;
    outline: none;
    box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
}

.note-input.invalid {
    border-color: #e74c3c;
    background-color: #fdf2f2;
}

.historique-notes {
    font-size: 0.8rem;
    line-height: 1.3;
}

.historique-notes small {
    display: block;
    margin-bottom: 2px;
}

.text-muted {
    color: #6c757d;
}

.text-center {
    text-align: center;
}

.btn-large {
    padding: 12px 30px;
    font-size: 16px;
}

.alert-warning {
    background-color: #fff3cd;
    color: #856404;
    border: 1px solid #ffeaa7;
    border-radius: 5px;
    padding: 12px;
    margin-bottom: 20px;
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

<script>
function validateNote(input) {
    // Remplacer la virgule par un point
    input.value = input.value.replace(',', '.');
    
    // Valider que c'est un nombre entre 0 et 20
    const value = parseFloat(input.value);
    
    if (input.value !== '' && (isNaN(value) || value < 0 || value > 20)) {
        input.classList.add('invalid');
    } else {
        input.classList.remove('invalid');
    }
}

// Validation avant soumission
document.querySelector('form').addEventListener('submit', function(e) {
    const typeNote = document.getElementById('type_note_id');
    if (!typeNote.value) {
        alert('Veuillez sélectionner un type de note');
        e.preventDefault();
        return;
    }
    
    const inputs = document.querySelectorAll('.note-input');
    let hasInvalidNote = false;
    
    inputs.forEach(input => {
        const value = parseFloat(input.value);
        if (input.value !== '' && (isNaN(value) || value < 0 || value > 20)) {
            input.classList.add('invalid');
            hasInvalidNote = true;
        }
    });
    
    if (hasInvalidNote) {
        alert('Certaines notes sont invalides. Veuillez corriger les notes en rouge.');
        e.preventDefault();
    }
});
</script>
<?php include __DIR__ . '../../partials/pied.php'; ?>