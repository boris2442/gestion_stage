<?php
// On utilise une bibliothèque comme FPDF (à télécharger et mettre dans /libs/)
require('libs/fpdf.php'); 
require_once('config/db.php');
require_once 'vendor/autoload.php';
session_start();

if (isset($_GET['session_id'])) {
    $stmt = $pdo->prepare("SELECT s.*, u.nom, u.prenom, u.email FROM sessions s 
                           JOIN users u ON s.id_stagiaire = u.id WHERE s.id = ?");
    $stmt->execute([$_GET['session_id']]);
    $data = $stmt->fetch();

    $pdf = new FPDF();
    $pdf->AddPage();
    
    // Logo de l'entreprise
    // $pdf->Image('assets/img/logo_resotel.png', 10, 10, 30);
    
    $pdf->SetFont('Arial', 'B', 20);
    $pdf->Cell(0, 50, 'ATTESTATION DE FIN DE STAGE', 0, 1, 'C');
    
    $pdf->SetFont('Arial', '', 12);
    $pdf->MultiCell(0, 10, "Je soussigné, Directeur de RESOTEL SARL, certifie que :");
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 10, strtoupper($data['nom']) . " " . $data['prenom'], 0, 1, 'C');
    
    $pdf->SetFont('Arial', '', 12);
    $pdf->MultiCell(0, 10, "A effectue son stage au sein de notre structure du " . $data['date_debut'] . " au " . $data['date_fin'] . ".");
    $pdf->MultiCell(0, 10, "Note obtenue : " . $data['note'] . "/20");
    $pdf->MultiCell(0, 10, "Appreciation : " . $data['observations']);
    
    $pdf->Ln(20);
    $pdf->Cell(0, 10, "Fait a Douala, le " . date('d/m/Y'), 0, 1, 'R');
    
    $pdf->Output('D', 'Attestation_'.$data['nom'].'.pdf');
}
?>
