<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des préfixes</title>
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
                <h2 class="fw-bold mb-1"><i class="bi bi-diagram-3 me-2 text-primary"></i>Préfixes</h2>
                <p class="text-muted mb-0">Opérateur : <strong><?= esc($operateur_nom) ?></strong></p>
            </div>
            <div class="d-flex gap-2">
                <button onclick="exportCSV()" class="btn btn-success">
                    <i class="bi bi-download me-1"></i>Export CSV
                </button>
                <a href="<?= site_url('prefixe/create') ?>" class="btn btn-primary">
                    <i class="bi bi-plus-lg me-1"></i>Ajouter un préfixe
                </a>
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
                    <table class="table table-hover mb-0" id="prefixeTable">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Numéro préfixe</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (! empty($prefixes)) : ?>
                                <?php foreach ($prefixes as $prefixe) : ?>
                                    <tr>
                                        <td><?= $prefixe['id_prefixe'] ?></td>
                                        <td><span class="badge bg-dark fs-6"><?= esc($prefixe['num_prefixe']) ?></span></td>
                                        <td class="text-center">
                                            <a href="<?= site_url('prefixe/edit/'.$prefixe['id_prefixe']) ?>" class="btn btn-sm btn-warning">
                                                <i class="bi bi-pencil"></i> Modifier
                                            </a>
                                            <form action="<?= site_url('prefixe/delete/'.$prefixe['id_prefixe']) ?>" method="post" style="display:inline-block;" onsubmit="return confirm('Voulez-vous vraiment supprimer ce préfixe ?')">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="bi bi-trash"></i> Supprimer
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">
                                        <i class="bi bi-inbox me-2"></i>Aucun préfixe trouvé.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
        </div>
    <script src="<?= base_url('assets/js/bootstrap.bundle.min.js') ?>"></script>
    <script>
    function exportCSV() {
        let csv = "ID;Numéro préfixe\n";
        const rows = document.querySelectorAll('#prefixeTable tbody tr');
        rows.forEach(row => {
            const cols = row.querySelectorAll('td');
            if (cols.length >= 2) {
                csv += cols[0].textContent.trim() + ";" + cols[1].textContent.trim() + "\n";
            }
        });
        const blob = new Blob(["\uFEFF" + csv], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = 'prefixes_<?= date('Y-m-d') ?>.csv';
        link.click();
    }
    </script>
</body>
</html>
