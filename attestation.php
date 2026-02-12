<?php
session_start();
require_once 'config/db.php';
require 'vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['stagiaire', 'administrateur', 'encadreur'])) {
    die("Accès refusé.");
}

if (in_array($_SESSION['role'], ['administrateur', 'encadreur']) && isset($_GET['id'])) {
    $id_user = intval($_GET['id']);
} else {
    $id_user = $_SESSION['user_id'];
}

$sql = "SELECT u.nom, u.prenom, u.note_final, s.titre as promo, s.date_debut, s.date_fin
        FROM users u 
        JOIN sessions s ON u.id_session_actuelle = s.id 
        LEFT JOIN rapports r ON r.id_stagiaire = u.id
        WHERE u.id = ? AND (r.status = 'valide' OR ? != 'stagiaire') 
        LIMIT 1";

$stmt = $pdo->prepare($sql);
$stmt->execute([$id_user, $_SESSION['role']]);
$data = $stmt->fetch();

if (!$data) {
    die("Attestation indisponible : Rapport non validé.");
}

// Gestion de la note (Arrondi à 2 chiffres)
if (isset($data['note_final']) && $data['note_final'] !== "" && $data['note_final'] !== null) {
    $note_finale = round($data['note_final'], 2);
} else {
    $note_finale = "N/A";
}

$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);
$options->set('defaultFont', 'serif');
$dompdf = new Dompdf($options);

$logoPath = 'assets/img/logoentreprose.jpeg';
$logoData = file_exists($logoPath) ? base64_encode(file_get_contents($logoPath)) : "";

$html = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 0; }
        body { 
            font-family: "Times New Roman", Times, serif; 
            margin: 0; padding: 0; color: #2c3e50;
            width: 297mm; height: 210mm; overflow: hidden;
        }
        .page-wrapper {
            width: 100%; height: 100%; padding: 20px; box-sizing: border-box;
        }
        .outer-border {
            border: 5px solid #0055A4; height: 95%; padding: 8px; position: relative;
        }
        .inner-border {
            border: 2px solid #009640; height: 100%; position: relative; background: #fff;
        }
        .corner {
            position: absolute; width: 50px; height: 50px; z-index: 5;
        }
        .top-left { top: -5px; left: -5px; border-top: 8px solid #0055A4; border-left: 8px solid #0055A4; }
        .bottom-right { bottom: -5px; right: -5px; border-bottom: 8px solid #0055A4; border-right: 8px solid #0055A4; }
        
        .watermark {
            position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);
            width: 400px; opacity: 0.05; z-index: 0;
        }
        .content { position: relative; z-index: 10; text-align: center; padding: 15px; }
        
        .header-logo { width: 90px; margin-bottom: 5px; }
        .company-title { font-size: 22px; font-weight: bold; color: #0055A4; text-transform: uppercase; }
        .company-subtitle { font-family: sans-serif; font-size: 9px; color: #7f8c8d; letter-spacing: 2px; }

        .cert-title {
            font-size: 45px; color: #009640; margin: 10px 0;
            letter-spacing: 5px; text-transform: uppercase;
        }
        .certify-line { font-family: sans-serif; font-size: 16px; font-style: italic; }
        .recipient-name {
            font-size: 38px; font-weight: bold; color: #0055A4;
            margin: 5px 0; border-bottom: 3px double #009640; padding: 0 30px;
        }
        .text-body {
            font-family: sans-serif; font-size: 17px; line-height: 1.4;
            width: 80%; margin: 10px auto;
        }
        .score-badge {
            margin: 10px auto; border: 1px solid #0055A4; padding: 8px 25px; display: inline-block;
        }
        .score-num { font-size: 22px; font-weight: bold; color: #0055A4; }
        .footer-table { width: 90%; margin: 10px auto 0 auto; }
        .sign-title { font-family: sans-serif; font-weight: bold; color: #0055A4; font-size: 13px; text-transform: uppercase; }
    </style>
</head>
<body>
    <div class="page-wrapper">
        <div class="outer-border">
            <div class="inner-border">
                <div class="corner top-left"></div>
                <div class="corner bottom-right"></div>
                <img src="data:image/png;base64,' . $logoData . '" class="watermark">
                <div class="content">
                    <img src="data:image/png;base64,' . $logoData . '" class="header-logo"><br>
                    <span class="company-title">RESOTEL SERVICES S.A.</span><br>
                    <span class="company-subtitle">Réseaux, Télécommunications & Informatique</span>
                    <h1 class="cert-title">ATTESTATION</h1>
                    <p class="certify-line">Nous certifions par la présente que</p>
                    <div class="recipient-name">' . mb_strtoupper(htmlspecialchars($data['prenom'] . ' ' . $data['nom']), 'UTF-8') . '</div>
                    <div class="text-body">
                        a complété avec succès son stage professionnel pour la session <strong>' . htmlspecialchars($data['promo']) . '</strong>,<br>
                        du <strong>' . date('d/m/Y', strtotime($data['date_debut'])) . '</strong> au <strong>' . date('d/m/Y', strtotime($data['date_fin'])) . '</strong>.
                    </div>
                    <div class="score-badge">
                        <span style="font-family: sans-serif; font-size: 11px; display:block;">ÉVALUATION GLOBALE</span>
                        <span class="score-num">' . $note_finale . ' / 20</span>
                    </div>
                    <table class="footer-table">
                        <tr>
                            <td style="text-align: left; width: 50%;">
                                <p>Fait à Dschang, le ' . date('d/m/Y') . '</p>
                                <span class="sign-title">La Direction des RH</span>
                            </td>
                            <td style="text-align: right; width: 50%;">
                                <br><br>
                                <span class="sign-title">Le Directeur Général</span><br>
                                <small style="font-style: italic;">(Sceau et signature)</small>
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

ob_end_clean();
$dompdf->stream("Attestation_" . $data['nom'] . ".pdf", ["Attachment" => false]);
