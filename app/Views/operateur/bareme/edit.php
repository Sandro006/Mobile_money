<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier le frais du barème</title>
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
        <h1 class="mb-4">Modifier le montant des frais</h1>

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

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h5 class="card-title">Barème #<?= $bareme['id_bareme'] ?></h5>
                <p><strong>Min :</strong> <?= number_format($bareme['min_bareme'], 2, ',', ' ') ?> €</p>
                <p><strong>Max :</strong> <?= number_format($bareme['max_bareme'], 2, ',', ' ') ?> €</p>
                <p><strong>Montant actuel :</strong>
                    <?php if ($bareme['montant_frais'] !== null) : ?>
                        <span class="badge bg-success"><?= number_format($bareme['montant_frais'], 2, ',', ' ') ?> €</span>
                    <?php else : ?>
                        <span class="badge bg-secondary">Non défini</span>
                    <?php endif; ?>
                </p>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <form action="<?= site_url('bareme/update/' . $bareme['id_bareme']) ?>" method="post">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label for="montant_frais" class="form-label">Nouveau montant des frais (€)</label>
                        <input type="number" step="0.01" min="0" class="form-control" id="montant_frais" name="montant_frais" value="<?= old('montant_frais', $bareme['montant_frais']) ?>" required>
                    </div>

                    <button type="submit" class="btn btn-primary">Mettre à jour</button>
                    <a href="<?= site_url('bareme') ?>" class="btn btn-secondary">Annuler</a>
                </form>
            </div>
        </div>
    </div>
    <script src="<?= base_url('assets/js/bootstrap.bundle.min.js') ?>"></script>
</body>
</html>