<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des barèmes</title>
</head>
<body>
<div class="container mt-5">
    <h1>Liste des barèmes et frais</h1>

    <?php if (session()->getFlashdata('success')) : ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Min</th>
                <th>Max</th>
                <th>Montant frais (dernier)</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($baremes)) : ?>
                <?php foreach ($baremes as $bareme) : ?>
                    <tr>
                        <td><?= $bareme['id_bareme'] ?></td>
                        <td><?= number_format($bareme['min_bareme'], 2, ',', ' ') ?></td>
                        <td><?= number_format($bareme['max_bareme'], 2, ',', ' ') ?></td>
                        <td>
                            <?php if ($bareme['montant_frais'] !== null) : ?>
                                <?= number_format($bareme['montant_frais'], 2, ',', ' ') ?>
                            <?php else : ?>
                                <span class="text-muted">Non défini</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="<?= site_url('bareme/edit/' . $bareme['id_bareme']) ?>" class="btn btn-sm btn-warning">Modifier</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr><td colspan="5" class="text-center">Aucun barème trouvé.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>