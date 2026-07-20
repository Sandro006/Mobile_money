<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier un préfixe</title>
</head>
<body>
<div class="container mt-5">
    <h1>Modifier le préfixe</h1>

    <?php if (session()->getFlashdata('errors')) : ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach (session()->getFlashdata('errors') as $error) : ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="<?= site_url('prefixe/update/'.$prefixe['id_prefixe']) ?>" method="post">
        <?= csrf_field() ?>
        

        <div class="mb-3">
            <label for="num_prefixe" class="form-label">Numéro préfixe</label>
            <input type="text" class="form-control" id="num_prefixe" name="num_prefixe" value="<?= old('num_prefixe', $prefixe['num_prefixe']) ?>" required>
        </div>

        <button type="submit" class="btn btn-primary">Mettre à jour</button>
        <a href="<?= site_url('prefixe') ?>" class="btn btn-secondary">Annuler</a>
    </form>
</div>
</body>
</html>