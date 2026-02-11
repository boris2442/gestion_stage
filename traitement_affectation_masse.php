<?php
session_start();
require_once 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ids_stagiaires'])) {
    $id_encadreur = $_POST['id_encadreur'];
    $ids = $_POST['ids_stagiaires']; // C'est un tableau d'IDs

    // Création d'une chaîne de points d'interrogation pour le SQL (ex: ?, ?, ?)
    $placeholders = implode(',', array_fill(0, count($ids), '?'));

    // On prépare la requête SQL "UPDATE users SET encadreur_id = X WHERE id IN (1, 2, 3...)"
    $sql = "UPDATE users SET encadreur_id = ? WHERE id IN ($placeholders) AND role = 'stagiaire'";

    $stmt = $pdo->prepare($sql);

    // On fusionne l'ID de l'encadreur avec le tableau des IDs des stagiaires pour l'exécution
    $params = array_merge([$id_encadreur], $ids);

    if ($stmt->execute($params)) {
        header("Location: stagiaires.php?success=bulk_assigned");
    } else {
        echo "Une erreur est survenue.";
    }
}
