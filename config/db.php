<?php
// Configuration des paramètres de la base de données
$host = 'localhost';
$db   = 'gestion_stagiaires_resotel';
$user = 'root'; // À modifier selon ton environnement
$pass = '';     // À modifier selon ton environnement
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
     // Si on arrive ici, la connexion est réussie
} catch (\PDOException $e) {
     throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
?>
