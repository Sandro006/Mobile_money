<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des préfixes</title>
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
        <h1 class="mb-4">Préfixes de l'opérateur : <?= esc($operateur_nom) ?></h1>

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

        <a href="<?= site_url('prefixe/create') ?>" class="btn btn-primary mb-3">Ajouter un préfixe</a>

        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Numéro préfixe</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (! empty($prefixes)) : ?>
                        <?php foreach ($prefixes as $prefixe) : ?>
                            <tr>
                                <td><?= $prefixe['id_prefixe'] ?></td>
                                <td><?= esc($prefixe['num_prefixe']) ?></td>
                                <td>
                                    <a href="<?= site_url('prefixe/edit/'.$prefixe['id_prefixe']) ?>" class="btn btn-sm btn-warning">Modifier</a>
                                    <form action="<?= site_url('prefixe/delete/'.$prefixe['id_prefixe']) ?>" method="post" style="display:inline-block;" onsubmit="return confirm('Voulez-vous vraiment supprimer ce préfixe ?')">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-sm btn-danger">Supprimer</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="3" class="text-center">Aucun préfixe trouvé.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <a href="<?= site_url('/') ?>" class="btn btn-secondary">Retour</a>
    </div>
    <script src="<?= base_url('assets/js/bootstrap.bundle.min.js') ?>"></script>
</body>
</html>