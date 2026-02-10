<?php
session_start();
require_once 'config/db.php';

// 1. Vérification de sécurité
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

try {
    // 2. Suppression de l'utilisateur dans la base de données
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$user_id]);

    // 3. Destruction propre de la session
    $_SESSION = array(); // Vide les variables
    session_destroy();   // Détruit le fichier de session sur le serveur

    // 4. Redirection vers le login avec un message de confirmation
    // On passe par une variable d'URL pour que le login puisse afficher un message
    header("Location: login.php?msg=account_deleted");
    exit();

} catch (PDOException $e) {
    // En cas d'erreur (ex: contrainte de clé étrangère), on renvoie vers le profil
    header("Location: editer_profil.php?error=delete_failed");
    exit();
}
