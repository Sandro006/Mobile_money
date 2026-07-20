<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des préfixes</title>
</head>
<body>
<div class="container mt-5">
    <h1>Liste des préfixes</h1>

    <?php if (session()->getFlashdata('success')) : ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>

    <a href="<?= site_url('prefixe/create') ?>" class="btn btn-primary mb-3">Ajouter un préfixe</a>

    <table class="table table-bordered table-striped">
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
       
    </form>
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
</body>
</html>