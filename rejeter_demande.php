<?php
session_start();
require_once 'config/db.php';

// Sécurité Admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'administrateur') {
    header('Location: index.php'); exit();
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    try {
        // Au lieu de DELETE, on change le statut
        $stmt = $pdo->prepare("UPDATE demandes SET status = 'rejete' WHERE id = ?");
        
        if ($stmt->execute([$id])) {
            // On redirige avec un message de succès
            header('Location: demandes_gestion.php?msg=rejected');
        }
    } catch (PDOException $e) {
        die("Erreur lors du rejet : " . $e->getMessage());
    }
} else {
    header('Location: demandes_gestion.php');
}
