<?php
session_start();
require_once 'config/db.php';

// On récupère l'ID et le nouveau statut (ex: ?id=5&status=termine)
$id_tache = $_GET['id'] ?? null;
$nouveau_statut = $_GET['status'] ?? 'termine'; 
$id_stagiaire = $_SESSION['user_id'] ?? null;

// Vérification de sécurité de base
if (!$id_tache || !$id_stagiaire || $_SESSION['role'] !== 'stagiaire') {
    header('Location: mes_taches.php');
    exit();
}

// Liste des statuts autorisés (évite l'injection de données bizarres)
$statuts_valides = ['a_faire', 'en_cours', 'termine'];
if (!in_array($nouveau_statut, $statuts_valides)) {
    header('Location: mes_taches.php?msg=invalid_status');
    exit();
}

try {
    // La requête reste focalisée sur l'ID et le propriétaire
    $sql = "UPDATE taches SET status = ? WHERE id = ? AND id_stagiaire = ?";
    $stmt = $pdo->prepare($sql);

    if ($stmt->execute([$nouveau_statut, $id_tache, $id_stagiaire])) {
        header("Location: mes_taches.php?msg=updated&new_status=$nouveau_statut");
    } else {
        header('Location: mes_taches.php?msg=error');
    }
} catch (PDOException $e) {
    header('Location: mes_taches.php?msg=db_error');
}
exit();
