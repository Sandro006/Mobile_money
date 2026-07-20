<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier le frais du barème</title>
</head>
<body>
<div class="container mt-5">
    <h1>Modifier le montant des frais</h1>

    <?php if (session()->getFlashdata('errors')) : ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach (session()->getFlashdata('errors') as $error) : ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Barème #<?= $bareme['id_bareme'] ?></h5>
            <p><strong>Min :</strong> <?= number_format($bareme['min_bareme'], 2, ',', ' ') ?></p>
            <p><strong>Max :</strong> <?= number_format($bareme['max_bareme'], 2, ',', ' ') ?></p>
            <p><strong>Montant actuel :</strong>
                <?php if ($bareme['montant_frais'] !== null) : ?>
                    <?= number_format($bareme['montant_frais'], 2, ',', ' ') ?>
                <?php else : ?>
                    <span class="text-muted">Non défini</span>
                <?php endif; ?>
            </p>
        </div>
    </div>

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
</body>
</html>