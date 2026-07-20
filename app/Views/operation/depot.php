<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Effectuer un Dépôt - e-Money</title>
    <!-- Liens vers vos assets locaux dans public/assets/ -->
    <link href="<?= base_url('assets/css/bootstrap.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/bootstrap-icons.css') ?>" rel="stylesheet">
</head>
<body class="bg-light">

<!-- Barre de Navigation identique à l'accueil -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold" href="/client/dashboard">e-Money</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="/client">Accueil</a></li>
                <li class="nav-item"><a class="nav-link active" href="/operation/page-depot">Faire un Dépôt</a></li>
                <li class="nav-item"><a class="nav-link" href="/operation/page-retrait">Faire un Retrait</a></li>
                <li class="nav-item"><a class="nav-link" href="/operation/page-transfert">Faire un Transfert</a></li>
                <li class="nav-item"><a class="nav-link" href="/client/historique">Historique</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            
            <!-- Affichage des messages Flash de CodeIgniter -->
            <?php if (session()->getFlashdata('succes')): ?>
                <div class="alert alert-success mb-3">
                    <?= session()->getFlashdata('succes') ?>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('erreur')): ?>
                <div class="alert alert-danger mb-3">
                    <?= session()->getFlashdata('erreur') ?>
                </div>
            <?php endif; ?>

            <!-- Formulaire de Dépôt -->
            <div class="card p-4 shadow-sm">
                <h4 class="fw-bold mb-3"><i class="bi bi-arrow-down-left-square text-success me-2"></i>Faire un dépôt</h4>
                <p class="text-muted small">L'argent sera directement ajouté à votre solde disponible de manière automatique.</p>
                <hr>

                <form action="/operation/depot" method="POST">
                    
                    <!-- Champ Montant -->
                    <div class="mb-3">
                        <label for="montant" class="form-label fw-bold">Montant du dépôt (Ar)</label>
                        <div class="input-group">
                            <input type="number" id="montant" name="montant" class="form-control form-control-lg text-center fw-bold" placeholder="Ex: 5000" min="100" required>
                            <span class="input-group-text fw-bold">Ar</span>
                        </div>
                    </div>

                    <!-- Champ Lieu avec valeur par défaut modifiable -->
                    <div class="mb-4">
                        <label for="lieu" class="form-label text-muted">Lieu de l'opération</label>
                        <input type="text" id="lieu" name="lieu" class="form-control" value="Guichet" placeholder="Ex: Agence Analakely, Kiosque...">
                        <div class="form-text text-muted">Laissez "Guichet" ou modifiez-le si nécessaire.</div>
                    </div>

                    <!-- Boutons d'action -->
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="/client/dashboard" class="text-decoration-none text-secondary"><i class="bi bi-arrow-left"></i> Retour</a>
                        <button type="submit" class="btn btn-success px-4 fw-bold">Confirmer le dépôt</button>
                    </div>

                </form>
            </div>

        </div>
    </div>
</div>

<script src="<?= base_url('assets/js/bootstrap.bundle.min.js') ?>"></script>
</body>
</html>
