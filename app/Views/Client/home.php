<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Espace Client - Mobile Money</title>
    <link href="<?= base_url('assets/css/bootstrap.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/bootstrap-icons.css') ?>" rel="stylesheet">
</head>
<body class="bg-light">

<!-- Barre de Navigation -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold" href="/client">e-Money</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link active" href="/client">Accueil</a></li>
                <li class="nav-item"><a class="nav-link" href="/operation/page-depot">Faire un Dépôt</a></li>
                <li class="nav-item"><a class="nav-link" href="/operation/page-retrait">Faire un Retrait</a></li>
                <li class="nav-item"><a class="nav-link" href="/operation/page-transfert">Faire un Transfert</a></li>
                <li class="nav-item"><a class="nav-link" href="/client/historique">Historique</a></li>
            </ul>
            <div class="navbar-nav align-items-center">
                <span class="nav-item text-light me-3">
                    <i class="bi bi-person"></i> <?= esc($nom ?? 'Client') ?>
                </span>
                <a class="btn btn-danger btn-sm" href="/logout">Se déconnecter</a>
            </div>
        </div>
    </div>
</nav>

<div class="container">
    <div class="row g-4">
        <!-- Section Informations et Solde -->
        <div class="col-md-4">
            <div class="card p-3 mb-3">
                <h5 class="text-muted small text-uppercase fw-bold">Mon Compte</h5>
                <hr class="my-2">
                <div class="mb-2"><strong>Nom :</strong> <?= esc($nom ?? '-') ?></div>
                <div class="mb-2"><strong>Numéro :</strong> <?= esc($numero ?? '-') ?></div>
                <div class="mt-3">
                    <span class="text-muted small">Solde disponible :</span>
                    <h2 class="text-success fw-bold"><?= number_format($solde ?? 0, 2, ',', ' ') ?> Ar</h2>
                </div>
            </div>
        </div>

        <!-- Section Les 5 Dernières Opérations Detallées -->
        <div class="col-md-8">
            <div class="card p-3">
                <h5 class="fw-bold mb-3"><i class="bi bi-clock-history me-2 text-secondary"></i>Les 5 dernières opérations</h5>
                <div class="table-responsive">
                    <table class="table table-bordered align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Date & Lieu</th>
                                <th>Type</th>
                                <th class="text-end">Frais</th>
                                <th class="text-end">Montant</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($historique) && is_array($historique)): ?>
                                <?php foreach ($historique as $tx): ?>
                                    <tr>
                                        <td>
                                            <div class="fw-bold small"><?= esc($tx['date_operation']) ?></div>
                                            <div class="text-muted small"><i class="bi bi-geo-alt"></i> <?= esc($tx['lieu']) ?></div>
                                        </td>
                                        <td>
                                            <?php if ($tx['type_operation'] === 'Dépôt' || $tx['type_operation'] === 'Transfert Reçu'): ?>
                                                <span class="text-success fw-bold"><?= esc($tx['type_operation']) ?></span>
                                            <?php else: ?>
                                                <span class="text-danger fw-bold"><?= esc($tx['type_operation']) ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end text-muted font-monospace small">
                                            <?= number_format($tx['frais'], 2, ',', ' ') ?> Ar
                                        </td>
                                        <!-- Affichage de la couleur du montant selon le sens du flux financier -->
                                        <?php 
                                            $estEntree = ($tx['type_operation'] === 'Dépôt' || $tx['type_operation'] === 'Transfert Reçu');
                                        ?>
                                        <td class="text-end fw-bold <?= $estEntree ? 'text-success' : 'text-danger' ?>">
                                            <?= $estEntree ? '+' : '-' ?> <?= number_format($tx['montant'], 2, ',', ' ') ?> Ar
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">
                                        <i class="bi bi-info-circle d-block mb-1"></i> Aucune transaction enregistrée.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="text-end mt-2">
                    <a href="/client/historique" class="small text-decoration-none">Voir tout l'historique complet →</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?= base_url('assets/js/bootstrap.bundle.min.js') ?>"></script>
</body>
</html>
