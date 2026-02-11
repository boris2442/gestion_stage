<?php
require_once 'config/db.php';
session_start();

// Sécurité : On vérifie l'ID et le rôle admin
if (isset($_GET['id']) && isset($_SESSION['role']) && $_SESSION['role'] === 'administrateur') {
    $id_demande = $_GET['id'];

    // 1. Récupérer la demande ET l'ID de la session active
    $stmt_demande = $pdo->prepare("SELECT * FROM demandes WHERE id = ?");
    $stmt_demande->execute([$id_demande]);
    $demande = $stmt_demande->fetch();

    // On récupère la session active pour lier le stagiaire immédiatement
    $stmt_sess = $pdo->query("SELECT id FROM sessions WHERE is_active = 1 LIMIT 1");
    $active_session = $stmt_sess->fetch();
    $id_session_active = $active_session['id'] ?? null;

    if ($demande && $id_session_active) {
        try {
            $pdo->beginTransaction();

            // 2. Création du compte 
            // AJOUT : id_session_actuelle dans l'INSERT
            $pass_par_defaut = password_hash("Resotel2026", PASSWORD_DEFAULT);

            $sql_user = "INSERT INTO users (nom, prenom, email, password, telephone, role, niveau_etude, type_stage, id_session_actuelle) 
                         VALUES (?, ?, ?, ?, ?, 'stagiaire', ?, ?, ?)";

            $stmt_user = $pdo->prepare($sql_user);
            $stmt_user->execute([
                $demande['nom'],
                $demande['prenom'],
                $demande['email'],
                $pass_par_defaut,
                $demande['telephone'],
                $demande['niveau_etude'],
                $demande['type_stage'],
                $id_session_active // Le stagiaire est maintenant lié à la session en cours !
            ]);

            // 3. Mettre à jour le statut de la demande
            $pdo->prepare("UPDATE demandes SET status = 'valide' WHERE id = ?")->execute([$id_demande]);

            $pdo->commit();

            echo "<script>
                    alert('Compte créé et rattaché à la session active !\\nIdentifiant : " . $demande['email'] . "\\nMot de passe : Resotel2026'); 
                    window.location.href='demandes_gestion.php';
                  </script>";
        } catch (Exception $e) {
            $pdo->rollBack();
            die("Erreur lors de la validation : " . $e->getMessage());
        }
    } else {
        echo "<script>alert('Erreur : Aucune session active trouvée. Créez une session avant de valider.'); window.history.back();</script>";
    }
} else {
    header('Location: index.php');
    exit();
}
