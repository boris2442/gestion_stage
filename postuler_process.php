<?php
require_once 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 1. Récupération et nettoyage
    $nom = strip_tags(trim($_POST['nom']));
    $prenom = strip_tags(trim($_POST['prenom']));
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $telephone = htmlspecialchars($_POST['telephone']);
    $type_stage = $_POST['type_stage'];
    $cni = htmlspecialchars($_POST['cni']);
    $niveau_etude = $_POST['niveau_etude'];

    if (!$email) {
        die("Email invalide.");
    }

    // 2. RÉCUPÉRATION DE LA SESSION ACTIVE
    // On veut que la candidature soit liée à la session actuelle de recrutement
    $stmt_sess = $pdo->query("SELECT id FROM sessions WHERE is_active = 1 LIMIT 1");
    $session_active = $stmt_sess->fetch();
    $id_session = $session_active['id'] ?? null;

    // 3. VÉRIFICATION DOUBLON (Email déjà utilisé pour cette session ?)
    $check = $pdo->prepare("SELECT id FROM demandes WHERE email = ? AND status = 'en_attente'");
    $check->execute([$email]);
    if ($check->rowCount() > 0) {
        die("Vous avez déjà une candidature en cours de traitement.");
    }

    // 4. Gestion des fichiers
    $target_dir = "uploads/cv/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    // Validation PDF plus stricte (MIME Type)
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime_cv = $finfo->file($_FILES["cv_file"]["tmp_name"]);
    $mime_lettre = $finfo->file($_FILES["lettre_file"]["tmp_name"]);

    if ($mime_cv !== 'application/pdf' || $mime_lettre !== 'application/pdf') {
        die("Erreur : Seuls les vrais documents PDF sont acceptés.");
    }

    $timestamp = time();
    $new_cv_name = "cv_" . substr(md5($email), 0, 8) . "_" . $timestamp . ".pdf";
    $new_lettre_name = "lettre_" . substr(md5($email), 0, 8) . "_" . $timestamp . ".pdf";

    if (
        move_uploaded_file($_FILES["cv_file"]["tmp_name"], $target_dir . $new_cv_name) &&
        move_uploaded_file($_FILES["lettre_file"]["tmp_name"], $target_dir . $new_lettre_name)
    ) {

        // 5. INSERTION (Ajout de id_session)
        $sql = "INSERT INTO demandes (nom, prenom, email, telephone, type_stage, niveau_etude, cv_path, lettre_motivation_path, cni, id_session, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'en_attente')";

        $stmt = $pdo->prepare($sql);
        try {
            if ($stmt->execute([$nom, $prenom, $email, $telephone, $type_stage, $niveau_etude, $new_cv_name, $new_lettre_name, $cni, $id_session])) {
                echo "<script>alert('Candidature reçue ! Nous reviendrons vers vous par email.'); window.location.href='index.php';</script>";
            }
        } catch (PDOException $e) {
            error_log($e->getMessage()); // On log l'erreur discrètement
            die("Une erreur technique est survenue.");
        }
    }
}
