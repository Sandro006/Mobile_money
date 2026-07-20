<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuration de la commission inter-opérateur</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap.min.css') ?>">
    <style>
        body { background: #f8f9fa; }
        .navbar { margin-bottom: 20px; }
        .card { border-radius: 10px; }
    </style>
</head>
<body>
    <?= view('layouts/navbar') ?>
    <div class="container mt-4">
        <h1 class="mb-4">Configuration de la commission</h1>

        <?php if (session()->getFlashdata('success')) : ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('errors')) : ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    <?php foreach (session()->getFlashdata('errors') as $error) : ?>
                        <li><?= esc($error) ?></li>
                    <?php endforeach; ?>
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="card shadow-sm">
            <div class="card-body">
                <form action="<?= site_url('commission/update') ?>" method="post">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label for="commission_pourcent" class="form-label">Pourcentage (%)</label>
                        <input type="number" step="0.01" min="0" max="100" class="form-control" id="commission_pourcent" name="commission_pourcent" value="<?= old('commission_pourcent', $operateur['commission_pourcent'] ?? 0) ?>" required>
                    </div>

                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                    <a href="<?= site_url('/') ?>" class="btn btn-secondary">Annuler</a>
                </form>
            </div>
        </div>
    </div>
    <script src="<?= base_url('assets/js/bootstrap.bundle.min.js') ?>"></script>
</body>
</html>