<?php
require_once 'config/db.php';
session_start();

// Sécurité : On vérifie l'ID et le rôle admin
if (isset($_GET['id']) && isset($_SESSION['role']) && $_SESSION['role'] === 'administrateur') {
    $id_demande = $_GET['id'];

    // 1. Récupérer TOUTES les infos de la demande (y compris niveau_etude et type_stage)
    $stmt = $pdo->prepare("SELECT * FROM demandes WHERE id = ?");
    $stmt->execute([$id_demande]);
    $demande = $stmt->fetch();

    if ($demande) {
        try {
            $pdo->beginTransaction(); // Sécurité : Tout passe ou rien ne passe

            // 2. Création du compte avec les données déjà saisies par l'internaute
            $pass_par_defaut = password_hash("Resotel2026", PASSWORD_DEFAULT);

            // On ajoute niveau_etude et type_stage dans l'INSERT
            $sql_user = "INSERT INTO users (nom, prenom, email, password, telephone, role, niveau_etude, type_stage) 
                         VALUES (?, ?, ?, ?, ?, 'stagiaire', ?, ?)";

            $stmt_user = $pdo->prepare($sql_user);
            $stmt_user->execute([
                $demande['nom'],
                $demande['prenom'],
                $demande['email'],
                $pass_par_defaut,
                $demande['telephone'],
                $demande['niveau_etude'], // Récupéré de la table demandes
                $demande['type_stage']    // Récupéré de la table demandes
            ]);

            // 3. Mettre à jour le statut de la demande en 'acceptee' ou 'valide'
            $pdo->prepare("UPDATE demandes SET status = 'valide' WHERE id = ?")->execute([$id_demande]);

            $pdo->commit(); // On valide la transaction

            echo "<script>
                    alert('Compte stagiaire créé avec succès !\\nIdentifiant : " . $demande['email'] . "\\nMot de passe : Resotel2026'); 
                    window.location.href='demandes_gestion.php';
                  </script>";
        } catch (Exception $e) {
            $pdo->rollBack(); // En cas d'erreur, on annule tout
            echo "Erreur lors de la validation : " . $e->getMessage();
        }
    }
} else {
    header('Location: index.php');
    exit();
}
