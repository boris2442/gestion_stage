<?php
require_once 'config/db.php';
session_start();

if (isset($_GET['id']) && $_SESSION['role'] === 'administrateur') {
    $id_demande = $_GET['id'];

    // 1. Récupérer les infos de la demande
    $stmt = $pdo->prepare("SELECT * FROM demandes WHERE id = ?");
    $stmt->execute([$id_demande]);
    $demande = $stmt->fetch();

    if ($demande) {
        // 2. Création automatique du compte Stagiaire (Password par défaut: Resotel2026)
        $pass_par_defaut = password_hash("Resotel2026", PASSWORD_DEFAULT);
        
        $sql_user = "INSERT INTO users (nom, prenom, email, password, telephone, role) VALUES (?, ?, ?, ?, ?, 'stagiaire')";
        $stmt_user = $pdo->prepare($sql_user);
        
        if ($stmt_user->execute([$demande['nom'], $demande['prenom'], $demande['email'], $pass_par_defaut, $demande['telephone']])) {
            
            // 3. Mettre à jour le statut de la demande
            $pdo->prepare("UPDATE demandes SET status = 'valide' WHERE id = ?")->execute([$id_demande]);
            
            echo "<script>alert('Stagiaire créé ! Identifiant : " . $demande['email'] . " | Pass : Resotel2026'); window.location.href='demandes_gestion.php';</script>";
        }
    }
}
?>
