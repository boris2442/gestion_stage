<?php
require_once 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom = htmlspecialchars($_POST['nom']);
    $prenom = htmlspecialchars($_POST['prenom']);
    $email = htmlspecialchars($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    // $role = $_POST['role'];

    try {
        $sql = "INSERT INTO users (nom, prenom, email, password) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        
        if ($stmt->execute([$nom, $prenom, $email, $password])) {
            echo "<script>alert('Compte créé ! Connectez-vous.'); window.location.href='login.php';</script>";
        }
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
    }
}
