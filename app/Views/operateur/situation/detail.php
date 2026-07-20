<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détail de l'utilisateur</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap.min.css') ?>">
    <style>
        body { background: #f8f9fa; }
        .navbar { margin-bottom: 20px; }
        .card { border-radius: 10px; }
        .table th { background-color: #e9ecef; }
    </style>
</head>
<body>
    <?= view('layouts/navbar') ?>
    <div class="container mt-4">
        <h1 class="mb-4">Transactions de <?= esc($utilisateur['nom_utilisateur']) ?></h1>

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h5 class="card-title">Informations</h5>
                <p><strong>ID :</strong> <?= $utilisateur['id_utilisateur'] ?></p>
                <p><strong>Nom :</strong> <?= esc($utilisateur['nom_utilisateur']) ?></p>
                <p><strong>Numéro :</strong> <?= esc($utilisateur['numero_utilisateur']) ?></p>
                <p><strong>Solde actuel :</strong> <span class="badge bg-success"><?= number_format($utilisateur['solde_utilisateur'], 2, ',', ' ') ?> Ar</span></p>
            </div>
        </div>

        <?php if (empty($transactions)) : ?>
            <div class="alert alert-info">Aucune transaction trouvée pour cet utilisateur.</div>
        <?php else : ?>
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Type</th>
                            <th>Sens</th>
                            <th>Date</th>
                            <th>Lieu</th>
                            <th>Montant (Ar)</th>
                            <th>Autre partie</th>
                            <th>Frais (Ar)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($transactions as $t) : ?>
                            <tr>
                                <td><?= esc($t['id']) ?></td>
                                <td>
                                    <?php
                                    $badgeClass = $t['type'] === 'transfert' ? 'primary' : ($t['type'] === 'retrait' ? 'warning' : 'success');
                                    ?>
                                    <span class="badge bg-<?= $badgeClass ?>"><?= ucfirst($t['type']) ?></span>
                                </td>
                                <td><?= esc($t['sens']) ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($t['date'])) ?></td>
                                <td><?= esc($t['lieu']) ?></td>
                                <td><?= number_format($t['montant'], 2, ',', ' ') ?></td>
                                <td>
                                    <?php if ($t['autre_nom'] !== null) : ?>
                                        <?= esc($t['autre_nom']) ?><br>
                                        <small class="text-muted"><?= esc($t['autre_numero']) ?></small>
                                    <?php else : ?>
                                        <span class="text-muted">—</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($t['frais'] !== null) : ?>
                                        <span class="badge bg-info"><?= number_format($t['frais'], 2, ',', ' ') ?></span>
                                    <?php else : ?>
                                        <span class="text-muted">—</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="alert alert-success">
                <strong>Total des frais (gains) pour cet utilisateur :</strong> <?= number_format($totalFrais, 2, ',', ' ') ?> Ar
            </div>
        <?php endif; ?>

        <a href="<?= site_url('situation') ?>" class="btn btn-secondary">Retour à la liste</a>
    </div>
    <script src="<?= base_url('assets/js/bootstrap.bundle.min.js') ?>"></script>
</body>
</html>