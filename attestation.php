<?php
session_start();
require_once 'config/db.php';
require 'vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;


if ($_SESSION['role'] !== 'stagiaire') {
    die("Accès refusé");
}

$id_user = $_SESSION['user_id'];

// Récupération des données
$sql = "SELECT u.nom, u.prenom, s.titre as promo, s.date_debut, s.date_fin 
        FROM users u 
        JOIN sessions s ON u.id_session_actuelle = s.id 
        JOIN rapports r ON r.id_stagiaire = u.id
        WHERE u.id = ? AND r.status = 'valide' 
        ORDER BY r.date_depot DESC LIMIT 1";

$stmt = $pdo->prepare($sql);
$stmt->execute([$id_user]);
$data = $stmt->fetch();

if (!$data) {
    die("Rapport non validé ou inexistant.");
}

$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);

// Préparation du logo en Base64 pour éviter les problèmes de chemin PDF
$logoPath = 'assets/logo/logo.png'; // Vérifie bien l'extension .png ou .jpg
$logoData = "";
if (file_exists($logoPath)) {
    $logoData = base64_encode(file_get_contents($logoPath));
}

$html = '
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 0; }
        body { font-family: "Georgia", serif; margin: 0; padding: 0; background-color: #fff; }
        
        /* Cadre Ornemental */
        .certificate-container {
            padding: 50px;
            height: 100%;
            position: relative;
        }
        
        .outer-border {
            border: 15px solid #1a2a6c; /* Bleu profond */
            height: 94%;
            padding: 5px;
        }
        
        .inner-border {
            border: 2px solid #b8860b; /* Couleur Or */
            height: 98%;
            padding: 40px;
            position: relative;
            background-image: url("data:image/png;base64,' . $logoData . '");
            background-repeat: no-repeat;
            background-position: center;
            background-size: 400px;
            /* Opacité du filigrane simulée par superposition */
        }

        /* Overlay pour blanchir le filigrane */
        .overlay {
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            background-color: rgba(255, 255, 255, 0.92);
            z-index: 1;
        }

        .content-box { position: relative; z-index: 2; text-align: center; }

        .header-logo { width: 150px; margin-bottom: 10px; }
        
        .company-name { 
            font-size: 22px; 
            font-weight: bold; 
            color: #1a2a6c; 
            letter-spacing: 2px;
            margin-bottom: 40px;
        }

        .main-title { 
            font-size: 55px; 
            color: #b8860b; 
            margin: 20px 0; 
            font-weight: normal;
            text-transform: uppercase;
            font-family: "Times New Roman", serif;
        }

        .certify-text { font-size: 20px; font-style: italic; color: #555; }

        .stagiaire-name { 
            font-size: 45px; 
            font-weight: bold; 
            color: #1a2a6c; 
            margin: 20px 0;
            border-bottom: 1px double #b8860b;
            display: inline-block;
            padding-bottom: 5px;
        }

        .description { 
            font-size: 18px; 
            line-height: 1.8; 
            color: #333; 
            width: 80%; 
            margin: 0 auto;
        }

        .session-highlight { font-weight: bold; color: #1a2a6c; }

        .footer-table { 
            width: 100%; 
            margin-top: 60px; 
            text-align: left;
        }

        .signature-box { 
            text-align: center; 
            width: 250px;
        }

        .signature-line { border-top: 2px solid #1a2a6c; margin-top: 50px; padding-top: 10px; }

        .stamp-circle {
            width: 100px;
            height: 100px;
            border: 2px dashed #b8860b;
            border-radius: 50%;
            display: inline-block;
            margin-top: -50px;
            opacity: 0.3;
        }
    </style>
</head>
<body>
    <div class="certificate-container">
        <div class="outer-border">
            <div class="inner-border">
                <div class="overlay"></div>
                
                <div class="content-box">
                    <div class="company-name">RESOTEL SERVICES S.A.</div>
                    
                    <h1 class="main-title">Attestation de Stage</h1>
                    
                    <p class="certify-text">La Direction des Ressources Humaines certifie que</p>
                    
                    <div class="stagiaire-name">' . strtoupper(htmlspecialchars($data['prenom'] . ' ' . $data['nom'])) . '</div>
                    
                    <div class="description">
                        a accompli avec distinction et assiduité un stage professionnel<br>
                        au sein de nos services techniques pour la session <span class="session-highlight">' . htmlspecialchars($data['promo']) . '</span>.<br>
                        Le stage s\'est déroulé sur la période du 
                        <strong>' . date('d/m/Y', strtotime($data['date_debut'])) . '</strong> au 
                        <strong>' . date('d/m/Y', strtotime($data['date_fin'])) . '</strong>.
                    </div>

                    <table class="footer-table">
                        <tr>
                            <td style="width: 50%; padding-left: 50px;">
                                <p>Fait à Douala, le <strong>' . date('d/m/Y') . '</strong></p>
                                <div class="stamp-circle"></div>
                            </td>
                            <td style="width: 50%; text-align: right; padding-right: 50px;">
                                <div class="signature-box" style="display: inline-block;">
                                    <strong>Le Directeur Général</strong>
                                    <div class="signature-line"></div>
                                    <small style="font-style: italic;">Cachet et Signature autorisée</small>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>';

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();
$dompdf->stream("Attestation_" . $data['nom'] . ".pdf", ["Attachment" => false]);
//$dompdf->download("Attestation_" . $data['nom'] . ".pdf", ["Attachment" => false]);
