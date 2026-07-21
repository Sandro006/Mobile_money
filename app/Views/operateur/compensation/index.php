<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compensation inter-opérateurs</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap-icons.css') ?>">
    <style>
        body { background: #f0f2f5; font-family: 'Segoe UI', Tahoma, sans-serif; }
        .navbar { margin-bottom: 0; }
        .table th { background-color: #e9ecef; }
        .card { border-radius: 12px; border: none; }
        .solde-positif { color: #28a745; font-weight: bold; }
        .solde-negatif { color: #dc3545; font-weight: bold; }
    </style>
</head>
<body>
    <?= view('layouts/navbar') ?>
    <div class="container-fluid px-4 mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-1"><i class="bi bi-arrow-left-right me-2 text-primary"></i>Compensation inter-opérateurs</h2>
                <p class="text-muted mb-0">Situation des montants à envoyer/recevoir pour chaque opérateur</p>
            </div>
            <button onclick="exportCSV()" class="btn btn-success">
                <i class="bi bi-download me-1"></i>Export CSV
            </button>
        </div>

        <?php if (empty($operateurs)) : ?>
            <div class="alert alert-info"><i class="bi bi-info-circle me-1"></i>Aucun opérateur trouvé.</div>
        <?php else : ?>
            <div class="card shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="compensationTable">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Nom</th>
                                    <th>Total envoyé (Ar)</th>
                                    <th>Total reçu (Ar)</th>
                                    <th>Solde (Ar)</th>
                                    <th class="text-center">Statut</th>
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
                                        <td><strong><?= esc($op['nom_operateur']) ?></strong></td>
                                        <td class="fw-semibold"><?= number_format($op['total_envoye'], 2, ',', ' ') ?></td>
                                        <td class="fw-semibold"><?= number_format($op['total_recu'], 2, ',', ' ') ?></td>
                                        <td class="<?= $solde > 0 ? 'solde-positif' : ($solde < 0 ? 'solde-negatif' : '') ?>">
                                            <?= number_format($solde, 2, ',', ' ') ?>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-<?= $badgeClass ?> fs-6">
                                                <?php if ($solde > 0) : ?>
                                                    <i class="bi bi-arrow-down-circle me-1"></i>
                                                <?php elseif ($solde < 0) : ?>
                                                    <i class="bi bi-arrow-up-circle me-1"></i>
                                                <?php else : ?>
                                                    <i class="bi bi-check-circle me-1"></i>
                                                <?php endif; ?>
                                                <?= $statut ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
            </div>

            <div class="alert alert-info mt-4 shadow-sm">
                <div class="d-flex align-items-center">
                    <i class="bi bi-info-circle-fill me-3 fs-4"></i>
                    <div>
                        <strong>Interprétation :</strong>
                        <ul class="mb-0 mt-1">
                            <li><span class="badge bg-success">Doit recevoir</span> : cet opérateur doit recevoir de l'argent (ses utilisateurs ont reçu plus qu'ils n'ont envoyé).</li>
                            <li><span class="badge bg-danger">Doit payer</span> : cet opérateur doit payer de l'argent (ses utilisateurs ont envoyé plus qu'ils n'ont reçu).</li>
                            <li><span class="badge bg-secondary">Équilibre</span> : les montants envoyés et reçus sont équilibrés.</li>
                        </ul>
                    </div>
            </div>
        <?php endif; ?>
    </div>
    <script src="<?= base_url('assets/js/bootstrap.bundle.min.js') ?>"></script>
    <script>
    function exportCSV() {
        let csv = "ID;Nom;Total envoyé (Ar);Total reçu (Ar);Solde (Ar);Statut\n";
        const rows = document.querySelectorAll('#compensationTable tbody tr');
        rows.forEach(row => {
            const cols = row.querySelectorAll('td');
            if (cols.length >= 6) {
                csv += cols[0].textContent.trim() + ";" 
                    + cols[1].textContent.trim() + ";" 
                    + cols[2].textContent.trim() + ";" 
                    + cols[3].textContent.trim() + ";" 
                    + cols[4].textContent.trim() + ";" 
                    + cols[5].textContent.trim() + "\n";
            }
        });
        const blob = new Blob(["\uFEFF" + csv], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = 'compensation_<?= date('Y-m-d') ?>.csv';
        link.click();
    }
    </script>
</body>
</html>
