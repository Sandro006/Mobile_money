<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Situation des utilisateurs</title>
</head>
<body>
<div class="container mt-5">
    <h1>Situation des utilisateurs</h1>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Numéro</th>
                <th>Préfixe</th>
                <th>Solde (€)</th>
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
                        <td><?= esc($u['id_prefixe']) ?></td>
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

    <a href="<?= site_url('/') ?>" class="btn btn-secondary">Retour</a>
</div>
</body>
</html>