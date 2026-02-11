<?php include 'includes/header.php'; ?>
<style>
    .file-drop-area {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        width: 100%;
        padding: 40px;
        border: 2px dashed #cbd5e0;
        border-radius: 15px;
        background-color: #f8fafc;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .file-drop-area:hover {
        border-color: #4a90e2;
        background-color: #edf2f7;
    }

    .file-input {
        position: absolute;
        left: 0;
        top: 0;
        height: 100%;
        width: 100%;
        cursor: pointer;
        opacity: 0;
    }

    .file-msg {
        font-weight: 600;
        color: #4a5568;
        margin-top: 10px;
    }

    .file-icon {
        font-size: 50px;
        color: #4a90e2;
    }

    .file-hint {
        font-size: 0.85rem;
        color: #718096;
    }
</style>
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h3 class="card-title text-primary"><i class="fas fa-user-graduate me-2"></i> Formulaire de Candidature</h3>
                <p class="text-muted mb-0">Rejoignez l'équipe de RESOTEL SARL en déposant votre dossier.</p>
            </div>
            <div class="card-body p-4">
                <?php
                include 'config/db.php';
                // Récupération dynamique des valeurs de l'ENUM niveau_etude
                $stmtNiveau = $pdo->query("SHOW COLUMNS FROM demandes LIKE 'niveau_etude'");
                $rowNiveau = $stmtNiveau->fetch();
                preg_match("/^enum\((.*)\)$/", $rowNiveau['Type'], $matches);
                $niveaux = str_getcsv($matches[1], ',', "'");
                ?>
                <form action="postuler_process.php" method="POST" enctype="multipart/form-data">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small">Nom</label>
                            <input type="text" name="nom" class="form-control shadow-sm" required placeholder="Ex: DOE">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small">Prénom</label>
                            <input type="text" name="prenom" class="form-control shadow-sm" required placeholder="Ex: John">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small">Email</label>
                            <input type="email" name="email" class="form-control shadow-sm" required placeholder="john@example.com">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small">Numéro de CNI</label>
                            <input type="text" name="cni" class="form-control shadow-sm" required placeholder="Numéro de carte d'identité">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-bold small">Téléphone</label>
                            <input type="text" name="telephone" class="form-control shadow-sm" required placeholder="+237 ...">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small">Niveau d'études actuel</label>
                            <select name="niveau_etude" class="form-select shadow-sm" required>
                                <option value="" disabled selected>Choisissez votre niveau...</option>
                                <?php foreach ($niveaux as $n): ?>
                                    <option value="<?= $n ?>"><?= htmlspecialchars($n) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small">Type de Stage Sollicité</label>
                            <select name="type_stage" class="form-select shadow-sm" required>
                                <option value="academique">Stage Académique</option>
                                <option value="professionnel">Stage Professionnel</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold text-primary">Lettre de Motivation (PDF)</label>
                            <div class="file-drop-area">
                                <i class="fas fa-file-signature file-icon"></i>
                                <span class="file-msg">Cliquez ou glissez la lettre</span>
                                <span class="file-hint">Format PDF (max. 2Mo)</span>
                                <input type="file" name="lettre_file" class="file-input" accept=".pdf" required onchange="updateFileName(this)">
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold text-primary">Curriculum Vitae (PDF)</label>
                            <div class="file-drop-area">
                                <i class="fas fa-user-tie file-icon"></i>
                                <span class="file-msg">Cliquez ou glissez le CV</span>
                                <span class="file-hint">Format PDF (max. 2Mo)</span>
                                <input type="file" name="cv_file" class="file-input" accept=".pdf" required onchange="updateFileName(this)">
                            </div>
                        </div>
                    </div>

                    <div class="d-grid mt-3">
                        <button type="submit" class="btn btn-primary btn-lg shadow-lg">
                            <i class="fas fa-paper-plane me-2"></i> Envoyer ma candidature
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    function updateFileName(input) {
        let fileName = input.files[0].name;
        let msgElement = input.parentElement.querySelector('.file-msg');
        let hintElement = input.parentElement.querySelector('.file-hint');
        let iconElement = input.parentElement.querySelector('.file-icon');

        // On change le texte et l'icône pour confirmer le succès
        msgElement.innerText = "Fichier sélectionné !";
        hintElement.innerText = fileName;
        input.parentElement.style.borderColor = "#28a745"; // Bordure verte
        iconElement.className = "fas fa-check-circle file-icon text-success";
    }
</script>
<?php include 'includes/footer.php'; ?>
