<?php
require_once 'includes/header.php';
require_once 'includes/admin_only.php';

$id = $_GET['id'] ?? null;

if ($id && $id != $_SESSION['user_id']) {
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$id]);
}

header('Location: users.php');
exit();
