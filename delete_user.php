<?php
session_start();
require_once 'config/db.php';

// 1. Sécurité stricte : Vérifier si c'est un Admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'administrateur') {
    header('Location: index.php');
    exit();
}

$id = $_GET['id'] ?? null;

// 2. Vérifications de sécurité avant suppression
if ($id) {
    // Empêcher l'admin de se supprimer lui-même par erreur
    if ($id == $_SESSION['user_id']) {
        header('Location: users.php?error=SelfDelete');
        exit();
    }

    try {
        // Début d'une transaction (Optionnel mais recommandé pour l'intégrité)
        $pdo->beginTransaction();

        // 3. LOGIQUE INGÉNIEUR : Nettoyage des dépendances
        // Avant de supprimer l'utilisateur, on nettoie ses liens dans les autres tables
        // (Sinon le SQL peut bloquer à cause des clés étrangères)
        $pdo->prepare("DELETE FROM taches WHERE id_stagiaire = ?")->execute([$id]);

        // 4. Suppression finale de l'utilisateur
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);

        $pdo->commit();
        $msg = "Utilisateur supprimé avec succès.";
    } catch (Exception $e) {
        $pdo->rollBack();
        header('Location: users.php?error=DeleteFailed');
        exit();
    }
}

header('Location: users.php?success=Deleted');
exit();
