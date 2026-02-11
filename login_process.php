<?php
session_start();
require_once 'config/db.php';

// Si déjà connecté, on file au dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit();
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        // ✅ Sécurité : on régénère l'ID
        session_regenerate_id();

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['nom'] = $user['nom'];
        $_SESSION['role'] = $user['role'];
        // On stocke la session actuelle pour filtrer les données plus tard
        $_SESSION['id_session_actuelle'] = $user['id_session_actuelle'];

        header('Location: dashboard.php');
        exit();
    } else {
        // On redirige vers le template avec un paramètre d'erreur
        header('Location: login.php?error=auth_failed');
        exit();
    }
}
