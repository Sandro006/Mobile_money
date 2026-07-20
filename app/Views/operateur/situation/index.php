<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Situation des utilisateurs</title>
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
        <h1 class="mb-4">Situation des utilisateurs</h1>

        <a href="<?= site_url('situation/create') ?>" class="btn btn-primary mb-3">Ajouter un utilisateur </a>

        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Numéro</th>
                        <th>Solde </th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($utilisateurs)) : ?>
                        <?php foreach ($utilisateurs as $u) : ?>
                            <tr>
                                <td><?= $u['id_utilisateur'] ?></td>
                                <td><?= esc($u['nom_utilisateur']) ?></td>
                                <td><?= esc($u['numero_utilisateur']) ?></td>
                                <td><?= number_format($u['solde_utilisateur'], 2, ',', ' ') ?></td>
                                <td>
                                    <a href="<?= site_url('situation/detail/' . $u['id_utilisateur']) ?>" class="btn btn-sm btn-info">Voir détails</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr><td colspan="6" class="text-center">Aucun utilisateur trouvé.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <a href="<?= site_url('/') ?>" class="btn btn-secondary">Retour</a>
    </div>
    <script src="<?= base_url('assets/js/bootstrap.bundle.min.js') ?>"></script>
</body>
</html>