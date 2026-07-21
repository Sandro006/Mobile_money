<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gains - Interne et Commissions</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap-icons.css') ?>">
    <style>
        body { background: #f0f2f5; font-family: 'Segoe UI', Tahoma, sans-serif; }
        .navbar { margin-bottom: 0; }
        .table th { background-color: #e9ecef; }
        .card { border-radius: 12px; border: none; }
    </style>
</head>
<body>
    <?= view('layouts/navbar') ?>
    <div class="container-fluid px-4 mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-1"><i class="bi bi-graph-up me-2 text-success"></i>Situation des gains</h2>
                <p class="text-muted mb-0">Suivi des gains internes et commissions inter-opérateur</p>
            </div>
            <button onclick="exportCSV()" class="btn btn-success">
                <i class="bi bi-download me-1"></i>Export CSV
            </button>
        </div>

        <!-- ============================================================ -->
        <!-- TABLEAU 1 : GAINS INTERNES (frais de base de toutes les ops) -->
        <!-- ============================================================ -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                <h4 class="mb-0"><i class="bi bi-cash me-2"></i>Gains internes (frais de base)</h4>
                <span class="badge bg-light text-dark">Total : <?= number_format($totalGainsInternes, 2, ',', ' ') ?> Ar</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="gainsInternesTable">
                        <thead class="table-light">
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
                                        <td class="fw-semibold"><?= number_format($t['montant'], 2, ',', ' ') ?></td>
                                        <td>
                                            <?php if ($t['frais_calcules'] > 0) : ?>
                                                <span class="badge bg-success"><?= number_format($t['frais_calcules'], 2, ',', ' ') ?></span>
                                            <?php else : ?>
                                                <span class="text-muted">0,00</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">Aucun gain interne.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
        </div>

        <!-- ============================================================ -->
        <!-- TABLEAU 2 : COMMISSIONS INTER-OPÉRATEUR -->
        <!-- ============================================================ -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                <h4 class="mb-0"><i class="bi bi-arrow-left-right me-2"></i>Commissions inter-opérateur</h4>
                <span class="badge bg-light text-dark">Total : <?= number_format($totalCommissions, 2, ',', ' ') ?> Ar</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="commissionsTable">
                        <thead class="table-light">
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
                                        <td class="fw-semibold"><?= number_format($t['montant'], 2, ',', ' ') ?></td>
                                        <td><span class="badge bg-info"><?= number_format($t['taux_commission'], 2, ',', ' ') ?> %</span></td>
                                        <td class="fw-semibold text-danger"><?= number_format($t['commission_calculee'], 2, ',', ' ') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">Aucune commission inter-opérateur.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
        </div>

        <!-- ============================================================ -->
        <!-- RÉCAPITULATIF GÉNÉRAL -->
        <!-- ============================================================ -->
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card text-white bg-success shadow-sm h-100">
                    <div class="card-body text-center">
                        <h6 class="card-subtitle mb-2 opacity-75">Gains internes</h6>
                        <h3 class="card-title fw-bold"><?= number_format($totalGainsInternes, 2, ',', ' ') ?> Ar</h3>
                        <p class="card-text small">Frais de base perçus sur toutes les opérations</p>
                    </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-danger shadow-sm h-100">
                    <div class="card-body text-center">
                        <h6 class="card-subtitle mb-2 opacity-75">Commissions inter-opérateur</h6>
                        <h3 class="card-title fw-bold"><?= number_format($totalCommissions, 2, ',', ' ') ?> Ar</h3>
                        <p class="card-text small">Commissions sur transferts vers autres opérateurs</p>
                    </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-info shadow-sm h-100">
                    <div class="card-body text-center">
                        <h6 class="card-subtitle mb-2 opacity-75">Total général</h6>
                        <h3 class="card-title fw-bold"><?= number_format($totalGeneral, 2, ',', ' ') ?> Ar</h3>
                        <p class="card-text small">Somme des gains internes et commissions</p>
                    </div>
            </div>
    </div>

    <script src="<?= base_url('assets/js/bootstrap.bundle.min.js') ?>"></script>
    <script>
    function exportCSV() {
        let csv = "=== GAINS INTERNES ===\nID;Type;Date;Lieu;Montant (Ar);Frais perçus (Ar)\n";
        const rows1 = document.querySelectorAll('#gainsInternesTable tbody tr');
        rows1.forEach(row => {
            const cols = row.querySelectorAll('td');
            if (cols.length >= 6) {
                csv += cols[0].textContent.trim() + ";" + cols[1].textContent.trim() + ";" 
                    + cols[2].textContent.trim() + ";" + cols[3].textContent.trim() + ";" 
                    + cols[4].textContent.trim() + ";" + cols[5].textContent.trim() + "\n";
            }
        });
        csv += "\n=== COMMISSIONS INTER-OPÉRATEUR ===\nID Transfert;Date;Lieu;Montant transféré (Ar);Taux commission;Commission perçue (Ar)\n";
        const rows2 = document.querySelectorAll('#commissionsTable tbody tr');
        rows2.forEach(row => {
            const cols = row.querySelectorAll('td');
            if (cols.length >= 6) {
                csv += cols[0].textContent.trim() + ";" + cols[1].textContent.trim() + ";" 
                    + cols[2].textContent.trim() + ";" + cols[3].textContent.trim() + ";" 
                    + cols[4].textContent.trim() + ";" + cols[5].textContent.trim() + "\n";
            }
        });
        const blob = new Blob(["\uFEFF" + csv], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = 'gains_<?= date('Y-m-d') ?>.csv';
        link.click();
    }
    </script>
</body>
</html>
