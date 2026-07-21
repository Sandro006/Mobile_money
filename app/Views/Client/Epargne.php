<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier l'epargne - e-Money</title>
    <!-- Liens vers vos assets locaux dans public/assets/ -->
    <link href="<?= base_url('assets/css/bootstrap.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/bootstrap-icons.css') ?>" rel="stylesheet">
</head>
<body class="bg-light">

<?= view('layouts/navbar_cli') ?>

<div class="container">
                <form action="/poucentagesauver" method="POST">
                    <?= csrf_field() ?>

                    <!-- Choix du réseau -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">Changer le % de l'epargne </label>
                        <div class="d-flex gap-3">
                        </div>
                    </div>

                    <!-- Champ Montant -->
                    <div class="mb-3">
                        <label for="montant" class="form-label fw-bold">Pourcentage De l'epargne</label>
                        <div class="input-group">
                            <input type="number" id="pourcentage" name="pourcentage" class="form-control form-control-lg text-center fw-bold" placeholder="Ex: 10" max="100" required>
                            <span class="input-group-text fw-bold">%</span>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="/client/dashboard" class="text-decoration-none text-secondary"><i class="bi bi-arrow-left"></i> Retour</a>
                        <button type="submit" class="btn btn-danger px-4 fw-bold">Confirmer le retrait</button>
                    </div>

                </form>
</div>