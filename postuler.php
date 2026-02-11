<?php include 'includes/header.php'; ?>

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

                    <div class="mb-3">
                        <label class="form-label">Niveau d'études actuel</label>
                        <select name="niveau_etude" class="form-select" required>
                            <option value="" disabled selected>Choisissez votre niveau...</option>
                            <?php foreach ($niveaux as $n): ?>
                                <option value="<?= $n ?>"><?= htmlspecialchars($n) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>


                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nom</label>
                            <input type="text" name="nom" class="form-control" required placeholder="Ex: DOE">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Prénom</label>
                            <input type="text" name="prenom" class="form-control" required placeholder="Ex: John">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Téléphone</label>
                            <input type="text" name="telephone" class="form-control" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Type de Stage Sollicité</label>
                        <select name="type_stage" class="form-select" required>
                            <option value="academique">Stage Académique</option>
                            <option value="professionnel">Stage Professionnel</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Lettre de Motivation (PDF)</label>
                        <input type="file" name="lettre_file" class="form-control" accept=".pdf" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Numéro de CNI</label>
                        <input type="text" name="cni" class="form-control" required placeholder="Numéro de la carte d'identité">
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Votre CV (Format PDF uniquement)</label>
                        <input type="file" name="cv_file" class="form-control" accept=".pdf" required>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">Envoyer ma candidature</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
