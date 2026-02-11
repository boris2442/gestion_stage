<?php
// 1. Démarrage de la session et connexion à la base de données
session_start();
require_once 'config/db.php';

/**
 * LOGIQUE DE GÉNIE LOGICIEL :
 * On vérifie trois conditions avant d'autoriser la mise à jour :
 * 1. L'ID de la tâche est présent dans l'URL (?id=...)
 * 2. L'utilisateur est bien connecté
 * 3. L'utilisateur a bien le rôle 'stagiaire'
 */
if (!isset($_GET['id']) || !isset($_SESSION['user_id']) || $_SESSION['role'] !== 'stagiaire') {
    header('Location: mes_taches.php');
    exit();
}

$id_tache = $_GET['id'];
$id_stagiaire = $_SESSION['user_id'];

/**
 * SÉCURITÉ CRITIQUE :
 * On ne met pas seulement "WHERE id = ?". 
 * On ajoute "AND id_stagiaire = ?" pour empêcher un stagiaire malveillant 
 * de deviner l'ID d'une tâche d'un autre stagiaire et de la valider à sa place.
 */
$sql = "UPDATE taches SET status = 'termine' WHERE id = ? AND id_stagiaire = ?";
$stmt = $pdo->prepare($sql);

if ($stmt->execute([$id_tache, $id_stagiaire])) {
    // Succès : on redirige vers la liste avec un message positif
    header('Location: mes_taches.php?msg=done');
} else {
    // Échec : on redirige avec un message d'erreur
    header('Location: mes_taches.php?msg=error');
}
exit();
