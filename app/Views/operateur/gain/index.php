<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gains par frais</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap.min.css') ?>">
    <style>
        body { background: #f8f9fa; }
        .navbar { margin-bottom: 20px; }
        .table th { background-color: #e9ecef; }
        .badge-interne { background-color: #28a745; }
        .badge-externe { background-color: #dc3545; }
    </style>
</head>
<body>
    <?= view('layouts/navbar') ?>
    <div class="container mt-4">
        <h1 class="mb-4">Situation des gains via les frais</h1>

        <?php if (empty($transactions)) : ?>
            <div class="alert alert-info">Aucune transaction trouvée.</div>
        <?php else : ?>
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Type</th>
                            <th>Date</th>
                            <th>Lieu</th>
                            <th>Montant (€)</th>
                            <th>Type de transfert</th>
                            <th>Frais perçus (€)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($transactions as $t) : ?>
                            <?php 
                                $badgeType = $t['type'] === 'transfert' ? 'primary' : ($t['type'] === 'retrait' ? 'warning' : 'success');
                                $badgeExterne = $t['est_externe'] ? 'badge-externe' : 'badge-interne';
                                $labelExterne = $t['est_externe'] ? 'Externe' : 'Interne';
                            ?>
                            <tr>
                                <td><?= esc($t['id']) ?></td>
                                <td><span class="badge bg-<?= $badgeType ?>"><?= ucfirst($t['type']) ?></span></td>
                                <td><?= date('d/m/Y H:i', strtotime($t['date'])) ?></td>
                                <td><?= esc($t['lieu']) ?></td>
                                <td><?= number_format($t['montant'], 2, ',', ' ') ?></td>
                                <td>
                                    <?php if ($t['type'] === 'transfert') : ?>
                                        <span class="badge <?= $badgeExterne ?>"><?= $labelExterne ?></span>
                                    <?php else : ?>
                                        <span class="badge badge-interne">Interne</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($t['frais_calcules'] > 0) : ?>
                                        <?= number_format($t['frais_calcules'], 2, ',', ' ') ?>
                                    <?php else : ?>
                                        <span class="text-muted">0,00</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Totaux séparés -->
            <div class="row mt-4">
                <div class="col-md-4">
                    <div class="card text-white bg-success mb-3">
                        <div class="card-header">Gains internes</div>
                        <div class="card-body">
                            <h5 class="card-title"><?= number_format($totalGainsInternes, 2, ',', ' ') ?> €</h5>
                            <p class="card-text">Frais perçus sur les opérations entre utilisateurs du même opérateur.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-white bg-danger mb-3">
                        <div class="card-header">Gains externes</div>
                        <div class="card-body">
                            <h5 class="card-title"><?= number_format($totalGainsExternes, 2, ',', ' ') ?> €</h5>
                            <p class="card-text">Frais perçus sur les transferts vers d'autres opérateurs (incluant la commission inter-opérateur).</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-white bg-info mb-3">
                        <div class="card-header">Total général</div>
                        <div class="card-body">
                            <h5 class="card-title"><?= number_format($totalGeneral, 2, ',', ' ') ?> €</h5>
                            <p class="card-text">Somme des gains internes et externes.</p>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <a href="<?= site_url('/') ?>" class="btn btn-secondary">Retour</a>
    </div>
    <script src="<?= base_url('assets/js/bootstrap.bundle.min.js') ?>"></script>
</body>
</html>