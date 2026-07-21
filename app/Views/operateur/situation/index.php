<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Situation des utilisateurs</title>
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
                <h2 class="fw-bold mb-1"><i class="bi bi-people me-2 text-primary"></i>Situation des utilisateurs</h2>
                <p class="text-muted mb-0">Gérez les comptes de vos utilisateurs</p>
            </div>
            <div class="d-flex gap-2">
                <button onclick="exportCSV()" class="btn btn-success">
                    <i class="bi bi-download me-1"></i>Export CSV
                </button>
                <a href="<?= site_url('situation/create') ?>" class="btn btn-primary">
                    <i class="bi bi-person-plus me-1"></i>Ajouter un utilisateur
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

        <!-- Recherche -->
        <div class="card shadow-sm mb-4">
            <div class="card-body py-3">
                <form class="row g-2 align-items-center" method="GET" action="<?= site_url('situation') ?>">
                    <div class="col-md-6 col-lg-4">
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control" name="search" placeholder="Rechercher par nom ou numéro..." value="<?= esc($search ?? '') ?>">
                        </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-filter me-1"></i>Filtrer</button>
                        <?php if (!empty($search)) : ?>
                            <a href="<?= site_url('situation') ?>" class="btn btn-outline-secondary"><i class="bi bi-x-circle"></i> Effacer</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="situationTable">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Nom</th>
                                <th>Numéro</th>
                                <th>Solde</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($utilisateurs)) : ?>
                                <?php foreach ($utilisateurs as $u) : ?>
                                    <tr>
                                        <td><?= $u['id_utilisateur'] ?></td>
                                        <td><strong><?= esc($u['nom_utilisateur']) ?></strong></td>
                                        <td><span class="badge bg-info bg-opacity-25 text-dark"><?= esc($u['numero_utilisateur']) ?></span></td>
                                        <td class="fw-semibold <?= $u['solde_utilisateur'] > 0 ? 'text-success' : 'text-muted' ?>">
                                            <?= number_format($u['solde_utilisateur'], 2, ',', ' ') ?> Ar
                                        </td>
                                        <td class="text-center">
                                            <a href="<?= site_url('situation/detail/' . $u['id_utilisateur']) ?>" class="btn btn-sm btn-info">
                                                <i class="bi bi-eye me-1"></i>Voir détails
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        <i class="bi bi-inbox me-2"></i>Aucun utilisateur trouvé.
                                        <?php if (!empty($search)) : ?> Essayez de modifier votre recherche.<?php endif; ?>
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
        let csv = "ID;Nom;Numéro;Solde (Ar)\n";
        const rows = document.querySelectorAll('#situationTable tbody tr');
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
        link.download = 'utilisateurs_<?= date('Y-m-d') ?>.csv';
        link.click();
    }
    </script>
</body>
</html>
