<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuration de la commission inter-opérateur</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap-icons.css') ?>">
    <style>
        body { background: #f0f2f5; font-family: 'Segoe UI', Tahoma, sans-serif; }
        .navbar { margin-bottom: 0; }
        .card { border-radius: 12px; border: none; }
    </style>
</head>
<body>
    <?= view('layouts/navbar') ?>
    <div class="container-fluid px-4 mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-1"><i class="bi bi-percent me-2 text-primary"></i>Configuration de la commission</h2>
                <p class="text-muted mb-0">Définissez le taux de commission pour les transferts inter-opérateur</p>
            </div>
            <button onclick="exportCSV()" class="btn btn-success">
                <i class="bi bi-download me-1"></i>Export CSV
            </button>
        </div>

        <?php if (session()->getFlashdata('success')) : ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-1"></i> <?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('errors')) : ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    <?php foreach (session()->getFlashdata('errors') as $error) : ?>
                        <li><i class="bi bi-dot me-1"></i><?= esc($error) ?></li>
                    <?php endforeach; ?>
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form action="<?= site_url('commission/update') ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="row align-items-end">
                        <div class="col-md-6 col-lg-4">
                            <label for="commission_pourcent" class="form-label fw-bold">Pourcentage (%)</label>
                            <div class="input-group">
                                <input type="number" step="0.01" min="0" max="100" class="form-control form-control-lg" id="commission_pourcent" name="commission_pourcent" value="<?= old('commission_pourcent', $commission_actuelle ?? 0) ?>" required>
                                <span class="input-group-text">%</span>
                            </div>
                        <div class="col-auto mt-3 mt-md-0">
                            <button type="submit" class="btn btn-primary btn-lg"><i class="bi bi-save me-1"></i>Enregistrer</button>
                            <a href="<?= site_url('operateur/dashboard') ?>" class="btn btn-secondary btn-lg"><i class="bi bi-arrow-left me-1"></i>Retour</a>
                        </div>
                </form>
            </div>

        <!-- Historique des modifications -->
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Historique des modifications</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="historiqueTable">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Taux commission (%)</th>
                                <th>Date modification</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (! empty($historique_commissions)) : ?>
                                <?php foreach ($historique_commissions as $index => $entry) : ?>
                                    <tr>
                                        <td><?= count($historique_commissions) - $index ?></td>
                                        <td><span class="badge bg-info fs-6"><?= esc($entry['taux_commission_autre_operateur']) ?> %</span></td>
                                        <td><?= esc($entry['created_at'] ?? 'N/A') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">Aucun historique disponible.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
        </div>
    <script src="<?= base_url('assets/js/bootstrap.bundle.min.js') ?>"></script>
    <script>
    function exportCSV() {
        let csv = "#;Taux commission (%);Date modification\n";
        const rows = document.querySelectorAll('#historiqueTable tbody tr');
        rows.forEach(row => {
            const cols = row.querySelectorAll('td');
            if (cols.length >= 3) {
                csv += cols[0].textContent.trim() + ";" + cols[1].textContent.trim() + ";" + cols[2].textContent.trim() + "\n";
            }
        });
        const blob = new Blob(["\uFEFF" + csv], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = 'historique_commission_<?= date('Y-m-d') ?>.csv';
        link.click();
    }
    </script>
</body>
</html>
