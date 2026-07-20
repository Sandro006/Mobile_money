<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenue sur e-Money</title>
    <!-- Liens vers vos assets locaux dans public/assets/ -->
    <link href="<?= base_url('assets/css/bootstrap.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/bootstrap-icons.css') ?>" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center" style="min-height: 100vh;">

<div class="container text-center">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-5">
            
            <!-- Logo / Icône de l'application -->
            <div class="mb-4">
                <i class="bi bi-wallet2 text-primary" style="font-size: 4rem;"></i>
                <h1 class="fw-bold mt-2">e-Money</h1>
                <p class="text-muted">Plateforme de transactions et gestion monétaire mobile</p>
            </div>

            <div class="card p-4 shadow-sm bg-white mb-4">
                <h5 class="fw-bold mb-4 text-secondary">Choisissez votre espace pour commencer</h5>
                
                <div class="d-grid gap-3">
                    <!-- Bouton Espace Client -->
                    <a href="/login" class="btn btn-primary btn-lg p-3 fw-bold text-start d-flex align-items-center justify-content-between">
                        <div>
                            <i class="bi bi-person-circle me-3 fs-4"></i>
                            <span>Espace Client</span>
                        </div>
                        <i class="bi bi-chevron-right"></i>
                    </a>
                    
                    <!-- Bouton Espace Opérateur -->
                    <a href="/operateur/auth" class="btn btn-dark btn-lg p-3 fw-bold text-start d-flex align-items-center justify-content-between">
                        <div>
                            <i class="bi bi-gear-fill me-3 fs-4 text-warning"></i>
                            <span>Configuration Opérateur</span>
                        </div>
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?= base_url('assets/js/bootstrap.bundle.min.js') ?>"></script>
</body>
</html>
