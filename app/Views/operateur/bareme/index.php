<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des barèmes</title>
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
                <h2 class="fw-bold mb-1"><i class="bi bi-table me-2 text-primary"></i>Barèmes et frais</h2>
                <p class="text-muted mb-0">Gestion des tranches de montants et frais associés</p>
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
        <?php if (session()->getFlashdata('error')) : ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-1"></i> <?= session()->getFlashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="baremeTable">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Min (Ar)</th>
                                <th>Max (Ar)</th>
                                <th>Montant frais</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($baremes)) : ?>
                                <?php foreach ($baremes as $bareme) : ?>
                                    <tr>
                                        <td><?= $bareme['id_bareme'] ?></td>
                                        <td class="fw-semibold"><?= number_format($bareme['min_bareme'], 2, ',', ' ') ?></td>
                                        <td class="fw-semibold"><?= number_format($bareme['max_bareme'], 2, ',', ' ') ?></td>
                                        <td>
                                            <?php if ($bareme['montant_frais'] !== null) : ?>
                                                <span class="badge bg-success fs-6"><?= number_format($bareme['montant_frais'], 2, ',', ' ') ?> Ar</span>
                                            <?php else : ?>
                                                <span class="badge bg-secondary">Non défini</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <a href="<?= site_url('bareme/edit/' . $bareme['id_bareme']) ?>" class="btn btn-sm btn-warning">
                                                <i class="bi bi-pencil me-1"></i>Modifier
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <tr><td colspan="5" class="text-center text-muted py-4">Aucun barème trouvé.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
        </div>
    <script src="<?= base_url('assets/js/bootstrap.bundle.min.js') ?>"></script>
    <script>
    function exportCSV() {
        let csv = "ID;Min (Ar);Max (Ar);Montant frais (Ar)\n";
        const rows = document.querySelectorAll('#baremeTable tbody tr');
        rows.forEach(row => {
            const cols = row.querySelectorAll('td');
            if (cols.length >= 4) {
                csv += cols[0].textContent.trim() + ";" 
                    + cols[1].textContent.trim() + ";" 
                    + cols[2].textContent.trim() + ";" 
                    + cols[3].textContent.trim() + "\n";
            }
        });
        const blob = new Blob(["\uFEFF" + csv], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = 'baremes_<?= date('Y-m-d') ?>.csv';
        link.click();
    }
    </script>
</body>
</html>
