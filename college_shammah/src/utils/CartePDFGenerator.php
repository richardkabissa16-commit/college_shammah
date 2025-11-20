<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

class CartePDFGenerator {
    private $dompdf;

    public function __construct() {
        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);
        
        $this->dompdf = new Dompdf($options);
    }

    // Générer une carte scolaire en PDF
    public function genererCartePDF($eleve_data) {
        $html = $this->genererHTMLCarte($eleve_data);
        
        $this->dompdf->loadHtml($html);
        $this->dompdf->setPaper('A4', 'landscape');
        $this->dompdf->render();
        
        return $this->dompdf->output();
    }

    // Générer le HTML de la carte selon votre modèle
    private function genererHTMLCarte($eleve) {
        ob_start();
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                .page {
                    width: 297mm;
                    height: 210mm;
                    margin: 0;
                    padding: 0;
                    position: relative;
                    page-break-after: always;
                }

                .carte-container {
                    width: 85mm;
                    height: 54mm;
                    border: 2px solid #000;
                    position: absolute;
                    padding: 3mm;
                    font-family: Arial, sans-serif;
                    font-size: 8pt;
                    line-height: 1.2;
                    background: white;
                }

                .carte-left {
                    left: 15mm;
                    top: 30mm;
                }

                .carte-right {
                    left: 115mm;
                    top: 30mm;
                }

                .carte-bottom-left {
                    left: 15mm;
                    top: 110mm;
                }

                .carte-bottom-right {
                    left: 115mm;
                    top: 110mm;
                }

                .header {
                    text-align: center;
                    margin-bottom: 2mm;
                    font-size: 7pt;
                    line-height: 1.1;
                }

                .ministere {
                    font-weight: bold;
                }

                .republic {
                    text-align: center;
                    font-weight: bold;
                    margin: 2mm 0;
                    font-size: 9pt;
                }

                .titre-carte {
                    text-align: center;
                    font-weight: bold;
                    margin: 2mm 0;
                    font-size: 9pt;
                    text-decoration: underline;
                }

                .photo-area {
                    width: 25mm;
                    height: 30mm;
                    border: 1px solid #000;
                    position: absolute;
                    right: 3mm;
                    top: 20mm;
                    background: #f0f0f0;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-size: 6pt;
                    text-align: center;
                }

                .info-eleve {
                    position: absolute;
                    left: 3mm;
                    top: 20mm;
                    width: 50mm;
                }

                .info-line {
                    margin-bottom: 1.5mm;
                }

                .info-label {
                    font-weight: bold;
                    display: inline-block;
                    width: 20mm;
                }

                .signature {
                    position: absolute;
                    bottom: 5mm;
                    right: 3mm;
                    font-size: 7pt;
                    text-align: center;
                    width: 25mm;
                }

                .numero-carte {
                    position: absolute;
                    bottom: 3mm;
                    left: 3mm;
                    font-weight: bold;
                    font-size: 7pt;
                }

                /* Deuxième carte sur la même page */
                .carte-container:nth-child(2) {
                    left: 115mm;
                    top: 30mm;
                }

                .carte-container:nth-child(3) {
                    left: 15mm;
                    top: 110mm;
                }

                .carte-container:nth-child(4) {
                    left: 115mm;
                    top: 110mm;
                }
            </style>
        </head>
        <body>
            <?php
            // Générer 4 cartes par page A4
            $cartes_par_page = 4;
            $total_cartes = count($eleve);
            
            for ($page = 0; $page < ceil($total_cartes / $cartes_par_page); $page++):
            ?>
            <div class="page">
                <?php for ($i = 0; $i < $cartes_par_page; $i++): 
                    $index = $page * $cartes_par_page + $i;
                    if ($index >= $total_cartes) break;
                    
                    $current_eleve = $eleve[$index];
                    $position_class = [
                        'carte-left',
                        'carte-right', 
                        'carte-bottom-left',
                        'carte-bottom-right'
                    ][$i];
                ?>
                <div class="carte-container <?php echo $position_class; ?>">
                    <!-- En-tête -->
                    <div class="header">
                        <div class="ministere">M.E.S.S.R.S<br>D.N.E.S.G<br>A.E. SIKASSO<br>LYCEE : SANZANA</div>
                    </div>

                    <!-- République -->
                    <div class="republic">
                        République du Mali<br>
                        Un Peuple - Un But - Une Foi
                    </div>

                    <!-- Titre -->
                    <div class="titre-carte">
                        CARTE D'IDENTITE SCOLAIRE
                    </div>

                    <!-- Zone photo -->
                    <div class="photo-area">
                        PHOTO<br>
                        <small>3x4 cm</small>
                    </div>

                    <!-- Informations élève -->
                    <div class="info-eleve">
                        <div class="info-line">
                            <span class="info-label">Prénom:</span>
                            <?php echo $current_eleve['prenom']; ?>
                        </div>
                        <div class="info-line">
                            <span class="info-label">Nom:</span>
                            <?php echo $current_eleve['nom']; ?>
                        </div>
                        <div class="info-line">
                            <span class="info-label">Né:</span>
                            <?php echo date('d/m/Y', strtotime($current_eleve['date_naissance'])); ?>
                            à <?php echo $current_eleve['lieu_naissance']; ?>
                        </div>
                        <div class="info-line">
                            <span class="info-label">Classe:</span>
                            <?php 
                            $classe_nom = $current_eleve['classe_nom'] ?? '';
                            // Adapter le format de classe selon votre modèle
                            echo str_replace('ème', '', $classe_nom); 
                            ?>
                        </div>
                    </div>

                    <!-- Numéro de carte -->
                    <div class="numero-carte">
                        N°Mle: <?php echo $current_eleve['numero_carte']; ?>
                    </div>

                    <!-- Signature -->
                    <div class="signature">
                        Signature du Proviseur
                    </div>
                </div>
                <?php endfor; ?>
            </div>
            <?php endfor; ?>
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