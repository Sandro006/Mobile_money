<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des barèmes</title>
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
        <h1 class="mb-4">Liste des barèmes et frais</h1>

        <?php if (session()->getFlashdata('success')) : ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')) : ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= session()->getFlashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Min (€)</th>
                        <th>Max (€)</th>
                        <th>Montant frais (dernier)</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($baremes)) : ?>
                        <?php foreach ($baremes as $bareme) : ?>
                            <tr>
                                <td><?= $bareme['id_bareme'] ?></td>
                                <td><?= number_format($bareme['min_bareme'], 2, ',', ' ') ?></td>
                                <td><?= number_format($bareme['max_bareme'], 2, ',', ' ') ?></td>
                                <td>
                                    <?php if ($bareme['montant_frais'] !== null) : ?>
                                        <span class="badge bg-success"><?= number_format($bareme['montant_frais'], 2, ',', ' ') ?></span>
                                    <?php else : ?>
                                        <span class="badge bg-secondary">Non défini</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="<?= site_url('bareme/edit/' . $bareme['id_bareme']) ?>" class="btn btn-sm btn-warning">Modifier</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr><td colspan="5" class="text-center">Aucun barème trouvé.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <a href="<?= site_url('/') ?>" class="btn btn-secondary">Retour</a>
    </div>
    <script src="<?= base_url('assets/js/bootstrap.bundle.min.js') ?>"></script>
</body>
</html>