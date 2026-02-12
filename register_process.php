<?php
session_start();
require_once 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom = htmlspecialchars($_POST['nom']);
    $prenom = htmlspecialchars($_POST['prenom']);
    $email = htmlspecialchars($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    // $role = $_POST['role'];

    try {

        // 1. RÉCUPÉRATION DE LA SESSION ACTIVE
        // On cherche la session qui a le badge 'is_active' à 1
        $stmt_sess = $pdo->query("SELECT id FROM sessions WHERE is_active = 1 LIMIT 1");
        $session = $stmt_sess->fetch();
        $id_session = $session['id'] ?? null;
        // 2. INSERTION AVEC SESSION ET RÔLE STAGIAIRE
        // Note : j'utilise 'id_session_actuelle' car c'est le nom dans ta table (capture précédente)
        $sql = "INSERT INTO users (nom, prenom, email, password, role, id_session_actuelle) 
                VALUES (?, ?, ?, ?, 'stagiaire', ?)";

        $stmt = $pdo->prepare($sql);

        if ($stmt->execute([$nom, $prenom, $email, $password, $id_session])) {
            echo "<script>alert('Compte stagiaire créé avec succès ! Connectez-vous.'); window.location.href='login.php';</script>";
        }
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
    }
}
