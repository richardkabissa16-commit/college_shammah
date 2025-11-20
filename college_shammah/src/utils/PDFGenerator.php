<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

class PDFGenerator {
    private $dompdf;

    public function __construct() {
        $options = new Options();
        $options->set('defaultFont', 'DejaVu Sans');
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);
        
        $this->dompdf = new Dompdf($options);
    }

    // Générer le bulletin en PDF
    public function genererBulletinPDF($bulletin_data) {
        $html = $this->genererHTMLBulletin($bulletin_data);
        
        $this->dompdf->loadHtml($html);
        $this->dompdf->setPaper('A4', 'portrait');
        $this->dompdf->render();
        
        return $this->dompdf->output();
    }

    // Générer le HTML du bulletin selon votre modèle
    private function genererHTMLBulletin($data) {
        $eleve = $data['eleve'];
        $bulletin = $data['bulletin'];
        $details = $data['details'];
        
        // Grouper les matières par catégorie
        $matieres_scientifiques = array_filter($details, function($detail) {
            return $detail['categorie'] === 'scientifique';
        });
        
        $matieres_litteraires = array_filter($details, function($detail) {
            return $detail['categorie'] === 'litteraire';
        });
        
        $matieres_complementaires = array_filter($details, function($detail) {
            return $detail['categorie'] === 'complementaire';
        });

        // Calculer les totaux
        $total_scientifique = array_sum(array_column($matieres_scientifiques, 'note_definitive'));
        $total_litteraire = array_sum(array_column($matieres_litteraires, 'note_definitive'));
        $total_complementaire = array_sum(array_column($matieres_complementaires, 'note_definitive'));
        $total_general = $total_scientifique + $total_litteraire + $total_complementaire;

        ob_start();
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                .bulletin-container {
                    font-family: DejaVu Sans, Arial, sans-serif;
                    width: 100%;
                    margin: 0;
                    padding: 10px;
                    font-size: 12px;
                    line-height: 1.2;
                }
                
                .header {
                    text-align: center;
                    margin-bottom: 15px;
                    border-bottom: 2px solid #000;
                    padding-bottom: 8px;
                }
                
                .etablissement {
                    font-weight: bold;
                    font-size: 14px;
                    margin-bottom: 5px;
                }
                
                .ministere {
                    font-size: 10px;
                    margin: 3px 0;
                }
                
                .titre-bulletin {
                    font-size: 12px;
                    font-weight: bold;
                    margin: 8px 0;
                }
                
                .info-eleve {
                    display: table;
                    width: 100%;
                    margin-bottom: 15px;
                    font-size: 10px;
                    border-collapse: collapse;
                }
                
                .info-row {
                    display: table-row;
                }
                
                .info-cell {
                    display: table-cell;
                    padding: 2px 5px;
                    border: 1px solid #000;
                }
                
                .table-notes {
                    width: 100%;
                    border-collapse: collapse;
                    font-size: 9px;
                    margin-bottom: 15px;
                }
                
                .table-notes th, .table-notes td {
                    border: 1px solid #000;
                    padding: 3px;
                    text-align: center;
                }
                
                .table-notes th {
                    background-color: #f0f0f0;
                    font-weight: bold;
                }
                
                .categorie {
                    font-weight: bold;
                    background-color: #e0e0e0;
                }
                
                .total-categorie {
                    font-weight: bold;
                    background-color: #d0d0d0;
                }
                
                .table-trimestres {
                    width: 100%;
                    border-collapse: collapse;
                    font-size: 9px;
                    margin-bottom: 15px;
                }
                
                .signatures {
                    display: table;
                    width: 100%;
                    margin-top: 20px;
                    font-size: 10px;
                }
                
                .signature-cell {
                    display: table-cell;
                    width: 50%;
                    vertical-align: top;
                }
                
                .signature-area {
                    border-top: 1px solid #000;
                    padding-top: 5px;
                    text-align: center;
                    margin-top: 10px;
                }
                
                .appreciation-conseil {
                    border: 1px solid #000;
                    padding: 8px;
                    margin-top: 15px;
                    font-size: 10px;
                }
                
                .footer {
                    text-align: center;
                    margin-top: 15px;
                    font-size: 10px;
                }
            </style>
        </head>
        <body>
            <div class="bulletin-container">
                <!-- En-tête du bulletin -->
                <div class="header">
                    <div class="etablissement">COLLEGE SHAMMAH<br>BP 20176, Lomé-Adélikopé<br>Tel. 93 54 15 04<br>Enseignement secondaire</div>
                    <div class="ministere">MINISTÈRE DES ENSEIGNEMENTS PRIMAIRE, SECONDAIRE ET DE L'ARTISANAT<br>DIRECTION RÉGIONALE DE L'ÉDUCATION GRAND LOMÉ<br>INSPECTION DE L'ENSEIGNEMENT SECONDAIRE GÉNÉRAL-LOMÉ<br>RÉPUBLIQUE TOGO</div>
                    <div class="titre-bulletin">Bulletin de notes du Trimestre <?php echo $bulletin['trimestre']; ?><br>Année scolaire <?php echo $bulletin['annee_scolaire']; ?></div>
                </div>
                
                <!-- Informations élève -->
                <div class="info-eleve">
                    <div class="info-row">
                        <div class="info-cell" style="width: 25%;"><strong>Matricule:</strong></div>
                        <div class="info-cell" style="width: 25%;"><?php echo $eleve['matricule']; ?></div>
                        <div class="info-cell" style="width: 25%;"><strong>Classe/Effectif:</strong></div>
                        <div class="info-cell" style="width: 25%;"><?php echo $eleve['classe_nom']; ?>/40</div>
                    </div>
                    <div class="info-row">
                        <div class="info-cell"><strong>Nom et prénom:</strong></div>
                        <div class="info-cell"><?php echo $eleve['nom'] . ' ' . $eleve['prenom']; ?></div>
                        <div class="info-cell"><strong>Classe doublée:</strong></div>
                        <div class="info-cell">-</div>
                    </div>
                    <div class="info-row">
                        <div class="info-cell"><strong>Date et lieu de naissance:</strong></div>
                        <div class="info-cell"><?php echo date('d/m/Y', strtotime($eleve['date_naissance'])) . ' - ' . $eleve['lieu_naissance']; ?></div>
                        <div class="info-cell"><strong>Adresse:</strong></div>
                        <div class="info-cell"><?php echo $eleve['adresse']; ?></div>
                    </div>
                </div>
                
                <!-- Tableau des notes -->
                <table class="table-notes">
                    <thead>
                        <tr>
                            <th>Matière</th>
                            <th>Devoir</th>
                            <th>M.Classe</th>
                            <th>N.Compo</th>
                            <th>M.Trim</th>
                            <th>Coef.</th>
                            <th>N.Déf</th>
                            <th>Appréciation</th>
                            <th>Nom des prof</th>
                            <th>Signature</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Matières scientifiques -->
                        <tr class="categorie">
                            <td colspan="10">Matières Scientifiques</td>
                        </tr>
                        <?php foreach ($matieres_scientifiques as $matiere): ?>
                        <tr>
                            <td><?php echo $matiere['matiere_nom']; ?></td>
                            <td><?php echo $matiere['moyenne_devoir']; ?></td>
                            <td><?php echo $matiere['moyenne_trimestre']; ?></td>
                            <td><?php echo $matiere['note_composition']; ?></td>
                            <td><?php echo $matiere['moyenne_trimestre']; ?></td>
                            <td><?php echo $matiere['coefficient']; ?></td>
                            <td><?php echo $matiere['note_definitive']; ?></td>
                            <td><?php echo $matiere['appreciation']; ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <?php endforeach; ?>
                        <tr class="total-categorie">
                            <td colspan="6">Total Matières Scientifiques</td>
                            <td><?php echo $total_scientifique; ?></td>
                            <td colspan="3"></td>
                        </tr>
                        
                        <!-- Matières littéraires -->
                        <tr class="categorie">
                            <td colspan="10">Matières Littéraires</td>
                        </tr>
                        <?php foreach ($matieres_litteraires as $matiere): ?>
                        <tr>
                            <td><?php echo $matiere['matiere_nom']; ?></td>
                            <td><?php echo $matiere['moyenne_devoir']; ?></td>
                            <td><?php echo $matiere['moyenne_trimestre']; ?></td>
                            <td><?php echo $matiere['note_composition']; ?></td>
                            <td><?php echo $matiere['moyenne_trimestre']; ?></td>
                            <td><?php echo $matiere['coefficient']; ?></td>
                            <td><?php echo $matiere['note_definitive']; ?></td>
                            <td><?php echo $matiere['appreciation']; ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <?php endforeach; ?>
                        <tr class="total-categorie">
                            <td colspan="6">Total Matières Littéraires</td>
                            <td><?php echo $total_litteraire; ?></td>
                            <td colspan="3"></td>
                        </tr>
                        
                        <!-- Matières complémentaires -->
                        <tr class="categorie">
                            <td colspan="10">Matières Complémentaires</td>
                        </tr>
                        <?php foreach ($matieres_complementaires as $matiere): ?>
                        <tr>
                            <td><?php echo $matiere['matiere_nom']; ?></td>
                            <td><?php echo $matiere['moyenne_devoir']; ?></td>
                            <td><?php echo $matiere['moyenne_trimestre']; ?></td>
                            <td><?php echo $matiere['note_composition']; ?></td>
                            <td><?php echo $matiere['moyenne_trimestre']; ?></td>
                            <td><?php echo $matiere['coefficient']; ?></td>
                            <td><?php echo $matiere['note_definitive']; ?></td>
                            <td><?php echo $matiere['appreciation']; ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <?php endforeach; ?>
                        <tr class="total-categorie">
                            <td colspan="6">Total Matières Complémentaires</td>
                            <td><?php echo $total_complementaire; ?></td>
                            <td colspan="3"></td>
                        </tr>
                        
                        <!-- Total général -->
                        <tr class="total-categorie">
                            <td colspan="6">Total Général :</td>
                            <td><?php echo $total_general; ?></td>
                            <td colspan="3"></td>
                        </tr>
                    </tbody>
                </table>
                
                <!-- Tableau des trimestres -->
                <table class="table-trimestres">
                    <thead>
                        <tr>
                            <th>Trimestre</th>
                            <th>Moyenne</th>
                            <th>Rang</th>
                            <th>Travail</th>
                            <th>Moy. min</th>
                            <th>Moy. max</th>
                            <th>Moy. Générale</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Trimestre <?php echo $bulletin['trimestre']; ?></td>
                            <td><?php echo $bulletin['moyenne_generale']; ?></td>
                            <td><?php echo $bulletin['rang']; ?>ème</td>
                            <td><?php echo $bulletin['appreciation']; ?></td>
                            <td><?php echo $bulletin['moyenne_min_classe']; ?></td>
                            <td><?php echo $bulletin['moyenne_max_classe']; ?></td>
                            <td><?php echo $bulletin['moyenne_generale_classe']; ?></td>
                        </tr>
                    </tbody>
                </table>
                
                <!-- Section appréciations et décisions -->
                <div class="signatures">
                    <div class="signature-cell">
                        <div style="margin-bottom: 15px;">
                            <div>FÉLICITATION : ______</div>
                            <div>ENCOURAGEMENT : ______</div>
                            <div>TABLEAU D'HONNEUR : ______</div>
                        </div>
                        <div class="signature-area">
                            Professeur Titulaire<br>
                            [NOM PROFESSEUR TITULAIRE]
                        </div>
                    </div>
                    <div class="signature-cell">
                        <div style="margin-bottom: 15px;">
                            <div>TRAV. : ______ RETARD : ______</div>
                            <div>AVERTISSEMENT : ______ ABSENCE : ______</div>
                            <div>DISC. : ______ PUNITIONS : ______ EXCLUSIONS : ______</div>
                        </div>
                        <div class="signature-area">
                            LE DIRECTEUR DES ÉTUDES
                        </div>
                    </div>
                </div>
                
                <!-- Appréciation du conseil -->
                <div class="appreciation-conseil">
                    <strong>APPRÉCIATION ET DÉCISION DU CONSEIL DES PROFESSEURS</strong><br>
                    <?php echo $bulletin['decision']; ?> LA CLASSE<br>
                    Travail <?php echo $bulletin['appreciation']; ?><br>
                    <?php echo $eleve['prenom'] . ' ' . $eleve['nom']; ?>
                </div>
                
                <!-- Pied de page -->
                <div class="footer">
                    Moyenne en lettre : [MOYENNE EN LETTRES]<br>
                    Fait à Lomé le, <?php echo date('d/m/Y'); ?><br>
                    Cachet de l'établissement
                </div>
            </div>
        </body>
        </html>
        <?php
        return ob_get_clean();
    }

    // Télécharger le PDF
    public function downloadPDF($pdf_data, $filename) {
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . strlen($pdf_data));
        echo $pdf_data;
        exit;
    }

    // Afficher le PDF dans le navigateur
    public function showPDF($pdf_data, $filename) {
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="' . $filename . '"');
        echo $pdf_data;
        exit;
    }
}
?>