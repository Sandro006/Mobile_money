<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Opérateur - e-Money</title>
    <link href="<?= base_url('assets/css/bootstrap.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/bootstrap-icons.css') ?>" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center" style="min-height: 100vh;">

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">

            <div class="text-center mb-4">
                <i class="bi bi-gear-fill text-warning" style="font-size: 3rem;"></i>
                <h2 class="fw-bold mt-2">Espace Opérateur</h2>
                <p class="text-muted">Connectez-vous pour gérer votre configuration</p>
            </div>

            <div class="card p-4 shadow-sm bg-white">

                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger">
                        <?= esc(session()->getFlashdata('error')) ?>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success">
                        <?= esc(session()->getFlashdata('success')) ?>
                    </div>
                <?php endif; ?>

                <form action="<?= base_url('operateur/auth') ?>" method="post">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label for="nom" class="form-label">Nom de l'opérateur</label>
                        <input type="text" class="form-control" id="nom" name="nom"
                               value="<?= old('nom') ?>" required autofocus>
                    </div>

                    <div class="mb-3">
                        <label for="mdp" class="form-label">Mot de passe</label>
                        <input type="password" class="form-control" id="mdp" name="mdp" required>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-dark btn-lg fw-bold">
                            Se connecter
                        </button>
                    </div>
                </form>

                <div class="text-center mt-3">
                    <a href="/" class="text-muted small">&larr; Retour à l'accueil</a>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="<?= base_url('assets/js/bootstrap.bundle.min.js') ?>"></script>
</body>
</html>