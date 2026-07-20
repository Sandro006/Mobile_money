<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Effectuer un Retrait - e-Money</title>
    <link href="<?= base_url('assets/css/bootstrap.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/bootstrap-icons.css') ?>" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold" href="/client/dashboard">e-Money</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="/client">Accueil</a></li>
                <li class="nav-item"><a class="nav-link" href="/operation/page-depot">Faire un Dépôt</a></li>
                <li class="nav-item"><a class="nav-link active" href="/operation/page-retrait">Faire un Retrait</a></li>
                <li class="nav-item"><a class="nav-link" href="/operation/page-transfert">Faire un Transfert</a></li>
                <li class="nav-item"><a class="nav-link" href="/client/historique">Historique</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            
            <?php if (session()->getFlashdata('erreur')): ?>
                <div class="alert alert-danger mb-3">
                    <?= session()->getFlashdata('erreur') ?>
                </div>
            <?php endif; ?>

            <div class="card p-4 shadow-sm">
                <h4 class="fw-bold mb-3"><i class="bi bi-arrow-up-right-square text-danger me-2"></i>Faire un retrait</h4>
                <p class="text-muted small">Des frais de retrait réglementaires seront automatiquement calculés et déduits selon les grilles de tranches en vigueur.</p>
                <hr>

                <form action="/operation/retrait" method="POST">
                    
                    <div class="mb-3">
                        <label for="montant" class="form-label fw-bold">Montant du retrait (Ar)</label>
                        <div class="input-group">
                            <input type="number" id="montant" name="montant" class="form-control form-control-lg text-center fw-bold" placeholder="Ex: 10000" min="100" required>
                            <span class="input-group-text fw-bold">Ar</span>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="lieu" class="form-label text-muted">Lieu du retrait</label>
                        <input type="text" id="lieu" name="lieu" class="form-control" value="Kiosque" placeholder="Ex: Kiosque Analakely...">
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <a href="/client/dashboard" class="text-decoration-none text-secondary"><i class="bi bi-arrow-left"></i> Retour</a>
                        <button type="submit" class="btn btn-danger px-4 fw-bold">Confirmer le retrait</button>
                    </div>

                </form>
            </div>

        </div>
    </div>
</div>

<script src="<?= base_url('assets/js/bootstrap.bundle.min.js') ?>"></script>
</body>
</html>
