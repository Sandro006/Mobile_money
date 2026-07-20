<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détail de l'utilisateur</title>
</head>
<body>
<div class="container mt-5">
    <h1>Transactions de <?= esc($utilisateur['nom_utilisateur']) ?></h1>

    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Informations</h5>
            <p><strong>ID :</strong> <?= $utilisateur['id_utilisateur'] ?></p>
            <p><strong>Nom :</strong> <?= esc($utilisateur['nom_utilisateur']) ?></p>
            <p><strong>Numéro :</strong> <?= esc($utilisateur['numero_utilisateur']) ?></p>
            <p><strong>Solde actuel :</strong> <?= number_format($utilisateur['solde_utilisateur'], 2, ',', ' ') ?> €</p>
        </div>
    </div>

    <?php if (empty($transactions)) : ?>
        <div class="alert alert-info">Aucune transaction trouvée pour cet utilisateur.</div>
    <?php else : ?>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Type</th>
                    <th>Sens</th>
                    <th>Date</th>
                    <th>Lieu</th>
                    <th>Montant (€)</th>
                    <th>Autre partie</th>
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
                        <td><?= esc($t['sens']) ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($t['date'])) ?></td>
                        <td><?= esc($t['lieu']) ?></td>
                        <td><?= number_format($t['montant'], 2, ',', ' ') ?></td>
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
            <strong>Total des frais (gains) pour cet utilisateur :</strong> <?= number_format($totalFrais, 2, ',', ' ') ?> €
        </div>
    <?php endif; ?>

    <a href="<?= site_url('situation') ?>" class="btn btn-secondary">Retour à la liste</a>
</div>
</body>
</html>