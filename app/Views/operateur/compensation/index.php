<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compensation inter-opérateurs</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap.min.css') ?>">
    <style>
        body { background: #f8f9fa; }
        .navbar { margin-bottom: 20px; }
        .table th { background-color: #e9ecef; }
        .solde-positif { color: #28a745; font-weight: bold; }
        .solde-negatif { color: #dc3545; font-weight: bold; }
    </style>
</head>
<body>
    <?= view('layouts/navbar') ?>
    <div class="container mt-4">
        <h1 class="mb-4">Situation des montants à envoyer à chaque opérateur</h1>

        <?php if (empty($operateurs)) : ?>
            <div class="alert alert-info">Aucun opérateur trouvé.</div>
        <?php else : ?>
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom</th>
                            <th>Total envoyé (€)</th>
                            <th>Total reçu (€)</th>
                            <th>Solde (€)</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($operateurs as $op) : ?>
                            <?php 
                                $solde = $op['solde'];
                                $statut = $solde > 0 ? 'Doit recevoir' : ($solde < 0 ? 'Doit payer' : 'Équilibre');
                                $badgeClass = $solde > 0 ? 'success' : ($solde < 0 ? 'danger' : 'secondary');
                            ?>
                            <tr>
                                <td><?= $op['id_operateur'] ?></td>
                                <td><?= esc($op['nom_operateur']) ?></td>
                                <td><?= number_format($op['total_envoye'], 2, ',', ' ') ?></td>
                                <td><?= number_format($op['total_recu'], 2, ',', ' ') ?></td>
                                <td class="<?= $solde > 0 ? 'solde-positif' : ($solde < 0 ? 'solde-negatif' : '') ?>">
                                    <?= number_format($solde, 2, ',', ' ') ?>
                                </td>
                                <td>
                                    <span class="badge bg-<?= $badgeClass ?>">
                                        <?= $statut ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="alert alert-info mt-3">
                <strong>Interprétation :</strong>
                <ul>
                    <li><span class="badge bg-success">Doit recevoir</span> : cet opérateur doit recevoir de l'argent (ses utilisateurs ont reçu plus qu'ils n'ont envoyé).</li>
                    <li><span class="badge bg-danger">Doit payer</span> : cet opérateur doit payer de l'argent (ses utilisateurs ont envoyé plus qu'ils n'ont reçu).</li>
                    <li><span class="badge bg-secondary">Équilibre</span> : les montants envoyés et reçus sont équilibrés.</li>
                </ul>
            </div>
        <?php endif; ?>

        <a href="<?= site_url('/') ?>" class="btn btn-secondary">Retour</a>
    </div>
    <script src="<?= base_url('assets/js/bootstrap.bundle.min.js') ?>"></script>
</body>
</html>