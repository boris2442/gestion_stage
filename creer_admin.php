<?php
require_once 'config/db.php';

// Tes informations de connexion
$nom = "Admin";
$prenom = "Resotel";
$email = "admin@resotel.com";
$password_clair = "admin123"; // Ton mot de passe de test

// Hachage sécurisé (obligatoire pour login_process.php)
$password_hashe = password_hash($password_clair, PASSWORD_DEFAULT);
$role = "administrateur";

try {
    // Vérifie bien si c'est 'utilisateur' (singulier) ou 'utilisateurs' (pluriel)
    // Dans ton erreur, PHP dit qu'il ne trouve pas 'utilisateurs'
    $sql = "INSERT INTO users (nom, prenom, email, password, role) VALUES (?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nom, $prenom, $email, $password_hashe, $role]);

    echo "<h2>Succès !</h2>";
    echo "Utilisateur créé avec succès. <br>";
    echo "Identifiants : <b>$email</b> / <b>$password_clair</b><br>";
    echo "<a href='login.php'>Aller à la page de connexion</a>";
} catch (PDOException $e) {
    echo "Erreur lors de la création : " . $e->getMessage();
}
