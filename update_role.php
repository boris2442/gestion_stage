<?php
session_start();
require_once 'config/db.php';

// Sécurité : Vérifier si l'utilisateur est admin (à adapter selon ta logique)
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'administrateur') {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    $new_role = $_POST['role'];

    // Empêcher de changer son propre rôle (pour ne pas se bloquer)
    if ($user_id == $_SESSION['user_id']) {
        header('Location: users.php?error=self_change');
        exit();
    }

    $stmt = $pdo->prepare("UPDATE users SET role = ? WHERE id = ?");
    if ($stmt->execute([$new_role, $user_id])) {
        header('Location: users.php?success=role_updated');
    } else {
        header('Location: users.php?error=update_failed');
    }
    exit();
}
