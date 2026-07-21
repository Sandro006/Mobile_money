<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'e-Money Opérateur' ?></title>
    <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap-icons.css') ?>">
    <style>
        body { 
            background: #f0f2f5; 
            font-family: 'Segoe UI', Tahoma, sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .navbar { margin-bottom: 0; }
        .table th { background-color: #e9ecef; }
        .card { border-radius: 12px; border: none; }
        .footer {
            margin-top: auto;
            background: #212529;
            color: #adb5bd;
            padding: 15px 0;
            font-size: 0.85rem;
        }
        .footer a { color: #adb5bd; text-decoration: none; }
        .footer a:hover { color: #fff; }
    </style>
</head>
<body>
    <?= view('layouts/navbar') ?>
    <div class="container-fluid px-4 mt-4" style="flex: 1;">
        <?= $content ?? '' ?>
    </div>
    <!-- Footer -->
    <footer class="footer mt-4">
        <div class="container-fluid px-4">
            <div class="d-flex justify-content-between align-items-center">
                <span>&copy; <?= date('Y') ?> e-Money - Plateforme de transactions monétaires</span>
                <span class="small">Version 1.0 | <a href="<?= site_url('/') ?>">Accueil</a></span>
            </div>
    </footer>
    <script src="<?= base_url('assets/js/bootstrap.bundle.min.js') ?>"></script>
</body>
</html>
