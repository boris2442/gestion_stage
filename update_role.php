<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'administrateur') {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    $new_role = $_POST['role'];

    // 1. Liste blanche des rôles autorisés
    $roles_autorises = ['administrateur', 'encadreur', 'stagiaire'];
    if (!in_array($new_role, $roles_autorises)) {
        header('Location: users.php?error=invalid_role');
        exit();
    }

    if ($user_id == $_SESSION['user_id']) {
        header('Location: users.php?error=self_change');
        exit();
    }

    try {
        // 2. Si on devient admin ou encadreur, on nettoie les infos de "stagiaire"
        if ($new_role !== 'stagiaire') {
            $stmt = $pdo->prepare("UPDATE users SET role = ?, encadreur_id = NULL, id_session_actuelle = NULL WHERE id = ?");
        } else {
            $stmt = $pdo->prepare("UPDATE users SET role = ? WHERE id = ?");
        }

        if ($stmt->execute([$new_role, $user_id])) {
            header('Location: users.php?success=role_updated');
        }
    } catch (PDOException $e) {
        header('Location: users.php?error=db_error');
    }
    exit();
}
