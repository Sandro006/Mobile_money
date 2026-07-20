<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toutes les transactions</title>
</head>
<body>
<div class="container mt-5">
    <h1>Toutes les transactions</h1>

    <?php if (empty($transactions)) : ?>
        <div class="alert alert-info">Aucune transaction trouvée.</div>
    <?php else : ?>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Type</th>
                    <th>Date</th>
                    <th>Lieu</th>
                    <th>Montant (€)</th>
                    <th>Utilisateur 1</th>
                    <th>Utilisateur 2</th>
                    <th>Frais (€)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($transactions as $t) : ?>
                    <tr>
                        <td><?= esc($t['id']) ?></td>
                        <td><span class="badge bg-<?= $t['type'] === 'transfert' ? 'primary' : ($t['type'] === 'retrait' ? 'warning' : 'success') ?>">
                            <?= ucfirst($t['type']) ?>
                        </span></td>
                        <td><?= date('d/m/Y H:i', strtotime($t['date'])) ?></td>
                        <td><?= esc($t['lieu']) ?></td>
                        <td><?= number_format($t['montant'], 2, ',', ' ') ?></td>
                        <td>
                            <?= esc($t['nom_utilisateur']) ?><br>
                            <small><?= esc($t['numero_utilisateur']) ?></small>
                        </td>
                        <td>
                            <?php if ($t['autre_nom'] !== null) : ?>
                                <?= esc($t['autre_nom']) ?><br>
                                <small><?= esc($t['autre_numero']) ?></small>
                            <?php else : ?>
                                <span class="text-muted">—</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($t['frais'] !== null) : ?>
                                <?= number_format($t['frais'], 2, ',', ' ') ?>
                            <?php else : ?>
                                <span class="text-muted">—</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="alert alert-success">
            <strong>Total des gains (frais perçus) :</strong> <?= number_format($totalGains, 2, ',', ' ') ?> €
        </div>
    <?php endif; ?>

    <a href="<?= site_url('/') ?>" class="btn btn-secondary">Retour</a>
</div>
</body>
</html>