<?php
session_start();
require_once 'config/db.php';
require 'vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'stagiaire') {
//     die("Accès refusé.");
// }

// $id_user = $_SESSION['user_id'];

// 1. PROTECTION ÉLARGIE
// On autorise stagiaire, administrateur et encadreur
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['stagiaire', 'administrateur', 'encadreur'])) {
    die("Accès refusé.");
}

// 2. RÉCUPÉRATION DE L'ID (Soit depuis l'URL, soit depuis la session)
// Si un admin/encadreur veut voir l'attestation d'un stagiaire précis : attestation.php?id=XX
if (in_array($_SESSION['role'], ['administrateur', 'encadreur']) && isset($_GET['id'])) {
    $id_user = intval($_GET['id']);
} else {
    // Si c'est le stagiaire lui-même
    $id_user = $_SESSION['user_id'];
}

// $sql = "SELECT u.nom, u.prenom, s.titre as promo, s.date_debut, s.date_fin,
//         (SELECT AVG(note) FROM taches WHERE id_stagiaire = u.id AND note IS NOT NULL) as moyenne_calculee
//         FROM users u 
//         JOIN sessions s ON u.id_session_actuelle = s.id 
//         JOIN rapports r ON r.id_stagiaire = u.id
//         WHERE u.id = ? AND r.status = 'valide' 
//         LIMIT 1";

$sql = "SELECT u.nom, u.prenom, s.titre as promo, s.date_debut, s.date_fin,
        (SELECT AVG(note) FROM taches WHERE id_stagiaire = u.id AND note IS NOT NULL) as moyenne_calculee
        FROM users u 
        JOIN sessions s ON u.id_session_actuelle = s.id 
        LEFT JOIN rapports r ON r.id_stagiaire = u.id
        WHERE u.id = ? AND (r.status = 'valide' OR ? != 'stagiaire') 
        LIMIT 1";

$stmt = $pdo->prepare($sql);
// $stmt->execute([$id_user]);
$stmt->execute([$id_user, $_SESSION['role']]);
$data = $stmt->fetch();

if (!$data) {
    die("Attestation indisponible : Rapport non validé.");
}

$note_finale = ($data['moyenne_calculee']) ? round($data['moyenne_calculee'], 2) : "N/A";

$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);
$options->set('defaultFont', 'serif'); // Plus prestigieux par défaut
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
            margin: 0; 
            padding: 0; 
            color: #2c3e50;
            background-color: #fff;
        }

        .page-wrapper {
            width: 100%;
            height: 100%;
            padding: 30px;
            box-sizing: border-box;
            position: relative;
        }

        /* Cadre Double Luxe */
        .outer-border {
            border: 5px solid #0055A4;
            height: 92%;
            padding: 10px;
            position: relative;
        }

        .inner-border {
            border: 2px solid #009640;
            height: 100%;
            position: relative;
            background: #fff;
        }

        /* Coins ornementaux */
        .corner {
            position: absolute;
            width: 60px;
            height: 60px;
            z-index: 5;
        }
        .top-left { top: -5px; left: -5px; border-top: 10px solid #0055A4; border-left: 10px solid #0055A4; }
        .bottom-right { bottom: -5px; right: -5px; border-bottom: 10px solid #0055A4; border-right: 10px solid #0055A4; }

        .watermark {
            position: absolute;
            top: 50%; left: 50%;
            transform: translate(-50%, -50%);
            width: 450px;
            opacity: 0.05;
            z-index: 0;
        }

        .content {
            position: relative;
            z-index: 10;
            text-align: center;
            padding: 20px;
        }

        .header-logo { width: 100px; margin-bottom: 5px; }
        .company-title { font-size: 24px; font-weight: bold; color: #0055A4; text-transform: uppercase; letter-spacing: 1px; }
        .company-subtitle { font-family: sans-serif; font-size: 10px; color: #7f8c8d; letter-spacing: 3px; }

        .cert-title {
            font-size: 55px;
            color: #009640;
            margin: 20px 0;
            font-weight: normal;
            letter-spacing: 5px;
            text-transform: uppercase;
        }

        .certify-line { font-family: sans-serif; font-size: 16px; color: #34495e; font-style: italic; margin-bottom: 5px; }

        .recipient-name {
            font-size: 45px;
            font-weight: bold;
            color: #0055A4;
            margin: 10px 0;
            display: inline-block;
            border-bottom: 3px double #009640;
            padding: 0 50px;
        }

        .text-body {
            font-family: sans-serif;
            font-size: 18px;
            line-height: 1.5;
            width: 85%;
            margin: 15px auto;
            color: #2c3e50;
        }

        .score-badge {
            margin: 15px auto;
            border: 1px solid #0055A4;
            display: inline-block;
            padding: 10px 30px;
        }
        .score-num { font-size: 24px; font-weight: bold; color: #0055A4; }

        .footer-table {
            width: 90%;
            margin: 30px auto 0 auto;
        }
        .sign-title { font-family: sans-serif; font-weight: bold; color: #0055A4; font-size: 14px; text-transform: uppercase; }
        .date-place { font-size: 14px; margin-bottom: 40px; }

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
                    
                    <div class="recipient-name">' . strtoupper(htmlspecialchars($data['prenom'] . ' ' . $data['nom'])) . '</div>

                    <div class="text-body">
                        a complété avec succès son stage professionnel au sein de notre établissement pour la session <strong>' . htmlspecialchars($data['promo']) . '</strong>,
                        sur la période du <strong>' . date('d/m/Y', strtotime($data['date_debut'])) . '</strong> au 
                        <strong>' . date('d/m/Y', strtotime($data['date_fin'])) . '</strong>.
                    </div>

                    <div class="score-badge">
                        <span style="font-family: sans-serif; font-size: 12px; display:block;">ÉVALUATION GLOBALE</span>
                        <span class="score-num">' . $note_finale . ' / 20</span>
                    </div>

                    <table class="footer-table">
                        <tr>
                            <td style="text-align: left; width: 50%;">
                                <p class="date-place">Fait à Douala, le ' . date('d/m/Y') . '</p>
                                <span class="sign-title">La Direction des Ressources Humaines</span>
                            </td>
                            <td style="text-align: right; width: 50%;">
                                <br><br><br>
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
$dompdf->stream("Attestation_Resotel_" . $data['nom'] . ".pdf", ["Attachment" => false]);
