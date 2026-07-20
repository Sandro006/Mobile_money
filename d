<![CDATA[<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gains - Interne et Commissions</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap.min.css') ?>">
    <style>
        body { background: #f8f9fa; }
        .navbar { margin-bottom: 20px; }
        .table th { background-color: #e9ecef; }
    </style>
</head>
<body>
    <?= view('layouts/navbar') ?>
    <div class="container mt-4">
        <h1 class="mb-4">Situation des gains</h1>

        <!-- ============================================================ -->
        <!-- TABLEAU 1 : GAINS INTERNES (frais de base de toutes les ops) -->
        <!-- ============================================================ -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-success text-white">
                <h4 class="mb-0">Gains internes (frais de base)</h4>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover mb-0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Type</th>
                                <th>Date</th>
                                <th>Lieu</th>
                                <th>Montant (Ar)</th>
                                <th>Frais perçus (Ar)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (! empty($gainsInternes)) : ?>
                                <?php foreach ($gainsInternes as $t) : ?>
                                    <?php 
                                        $badgeType = $t['type'] === 'transfert' ? 'primary' : ($t['type'] === 'retrait' ? 'warning' : 'success');
                                    ?>
                                    <tr>
                                        <td><?= esc($t['id']) ?></td>
                                        <td><span class="badge bg-<?= $badgeType ?>"><?= ucfirst($t['type']) ?></span></td>
                                        <td><?= date('d/m/Y H:i', strtotime($t['date'])) ?></td>
                                        <td><?= esc($t['lieu']) ?></td>
                                        <td><?= number_format($t['montant'], 2, ',', ' ') ?></td>
                                        <td>
                                            <?php if ($t['frais_calcules'] > 0) : ?>
                                                <?= number_format($t['frais_calcules'], 2, ',', ' ') ?>
                                            <?php else : ?>
                                                <span class="text-muted">0,00</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted">Aucun gain interne.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                        <tfoot class="table-success">
                            <tr>
                                <th colspan="5" class="text-end">Total gains internes :</th>
                                <th><?= number_format($totalGainsInternes, 2, ',', ' ') ?> Ar</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
        </div>

        <!-- ============================================================ -->
        <!-- TABLEAU 2 : COMMISSIONS INTER-OPÉRATEUR -->
        <!-- ============================================================ -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-danger text-white">
                <h4 class="mb-0">Commissions inter-opérateur</h4>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover mb-0">
                        <thead>
                            <tr>
                                <th>ID Transfert</th>
                                <th>Date</th>
                                <th>Lieu</th>
                                <th>Montant transféré (Ar)</th>
                                <th>Taux commission</th>
                                <th>Commission perçue (Ar)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (! empty($commissions)) : ?>
                                <?php foreach ($commissions as $t) : ?>
                                    <tr>
                                        <td><?= esc($t['id']) ?></td>
                                        <td><?= date('d/m/Y H:i', strtotime($t['date'])) ?></td>
                                        <td><?= esc($t['lieu']) ?></td>
                                        <td><?= number_format($t['montant'], 2, ',', ' ') ?></td>
                                        <td><?= number_format($t['taux_commission'], 2, ',', ' ') ?> %</td>
                                        <td>
                                            <?= number_format($t['commission_calculee'], 2, ',', ' ') ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted">Aucune commission inter-opérateur.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                        <tfoot class="table-danger">
                            <tr>
                                <th colspan="5" class="text-end">Total commissions :</th>
                                <th><?= number_format($totalCommissions, 2, ',', ' ') ?> Ar</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
        </div>

        <!-- ============================================================ -->
        <!-- RÉCAPITULATIF GÉNÉRAL -->
        <!-- ============================================================ -->
        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card text-white bg-success mb-3">
                    <div class="card-header">Gains internes</div>
                    <div class="card-body">
                        <h5 class="card-title"><?= number_format($totalGainsInternes, 2, ',', ' ') ?> Ar</h5>
                        <p class="card-text">Frais de base perçus sur toutes les opérations (dépôts, retraits, transferts).</p>
                    </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-danger mb-3">
                    <div class="card-header">Commissions inter-opérateur</div>
                    <div class="card-body">
                        <h5 class="card-title"><?= number_format($totalCommissions, 2, ',', ' ') ?> Ar</h5>
                        <p class="card-text">Commissions perçues sur les transferts vers d'autres opérateurs.</p>
                    </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-info mb-3">
                    <div class="card-header">Total général</div>
                    <div class="card-body">
                        <h5 class="card-title"><?= number_format($totalGeneral, 2, ',', ' ') ?> Ar</h5>
                        <p class="card-text">Somme des gains internes et commissions inter-opérateur.</p>
                    </div>
            </div>

        <a href="<?= site_url('/') ?>" class="btn btn-secondary">Retour</a>
    </div>
    <script src="<?= base_url('assets/js/bootstrap.bundle.min.js') ?>"></script>
</body>
</html>
]]>
