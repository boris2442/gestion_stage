<?php
require_once 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 1. Récupération des données sécurisée
    $nom = htmlspecialchars($_POST['nom']);
    $prenom = htmlspecialchars($_POST['prenom']);
    $email = htmlspecialchars($_POST['email']);
    $telephone = htmlspecialchars($_POST['telephone']);
    $type_stage = $_POST['type_stage'];
    $cni = htmlspecialchars($_POST['cni']); 
$niveau_etude = $_POST['niveau_etude']; // Nouvelle variable
    // 2. Gestion du dossier d'upload
    $target_dir = "uploads/cv/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    // 3. Vérification de l'existence des fichiers dans $_FILES
    if (!isset($_FILES['cv_file']) || !isset($_FILES['lettre_file'])) {
        die("Erreur : Fichiers manquants dans le formulaire.");
    }

    // 4. Sécurité des extensions (PDF uniquement)
    $ext_cv = strtolower(pathinfo($_FILES["cv_file"]["name"], PATHINFO_EXTENSION));
    $ext_lettre = strtolower(pathinfo($_FILES["lettre_file"]["name"], PATHINFO_EXTENSION));

    if ($ext_cv != "pdf" || $ext_lettre != "pdf") {
        die("Erreur : Seuls les fichiers au format PDF sont autorisés.");
    }

    // 5. Noms de fichiers uniques
    $timestamp = time();
    $new_cv_name = "cv_" . strtolower($nom) . "_" . $timestamp . ".pdf";
    $new_lettre_name = "lettre_" . strtolower($nom) . "_" . $timestamp . ".pdf";

    // 6. Déplacement et Insertion
    if (
        move_uploaded_file($_FILES["cv_file"]["tmp_name"], $target_dir . $new_cv_name) &&
        move_uploaded_file($_FILES["lettre_file"]["tmp_name"], $target_dir . $new_lettre_name)
    ) {
        // Respect du diagramme de classe : Insertion en BDD
        $sql = "INSERT INTO demandes (nom, prenom, email, telephone, type_stage, niveau_etude, cv_path, lettre_motivation_path, cni, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'en_attente')";

        $stmt = $pdo->prepare($sql);

        try {
            if ($stmt->execute([$nom, $prenom, $email, $telephone, $type_stage,$niveau_etude, $new_cv_name, $new_lettre_name, $cni])) {
                echo "<script>alert('Candidature envoyée avec succès !'); window.location.href='index.php';</script>";
            }
        } catch (PDOException $e) {
            echo "Erreur SQL : " . $e->getMessage();
        }
    } else {
        echo "Erreur lors du téléchargement des fichiers.";
    }
}
?>
