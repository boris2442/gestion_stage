<?php
session_start();
require_once 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ids_stagiaires'])) {
    $id_encadreur = $_POST['id_encadreur'];
    $id_session = $_POST['id_session']; // On récupère l'ID de session envoyé par le formulaire
    $ids = $_POST['ids_stagiaires'];

    $placeholders = implode(',', array_fill(0, count($ids), '?'));

    // MISE À JOUR : On change l'encadreur ET la session actuelle
    $sql = "UPDATE users 
            SET encadreur_id = ?, id_session_actuelle = ? 
            WHERE id IN ($placeholders) AND role = 'stagiaire'";

    $stmt = $pdo->prepare($sql);

    // On fusionne l'encadreur, la session, puis la liste des IDs
    $params = array_merge([$id_encadreur, $id_session], $ids);

    if ($stmt->execute($params)) {
        $_SESSION['success'] = "Affectation réussie pour " . count($ids) . " stagiaire(s).";
        header("Location: dashboard.php"); // Redirige vers le dashboard pour voir le résultat
        exit();
    } else {
        echo "Une erreur technique est survenue.";
    }
} else {
    header("Location: affectation_masse.php");
    exit();
}
