<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historique Complet - e-Money</title>
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
                <li class="nav-item"><a class="nav-link" href="/client">Accueil</a></li>
                <li class="nav-item"><a class="nav-link" href="/operation/page-depot">Faire un Dépôt</a></li>
                <li class="nav-item"><a class="nav-link" href="/operation/page-retrait">Faire un Retrait</a></li>
                <li class="nav-item"><a class="nav-link" href="/operation/page-transfert">Faire un Transfert</a></li>
                <li class="nav-item"><a class="nav-link active" href="/client/historique">Historique</a></li>
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
    <div class="card p-4 shadow-sm mb-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">
            <div>
                <h4 class="fw-bold mb-1"><i class="bi bi-journal-text me-2 text-secondary"></i>Toutes mes transactions</h4>
                <p class="text-muted small mb-0">Liste complète de vos dépôts, retraits et transferts enregistrés.</p>
            </div>
            <!-- Barre de filtre rapide -->
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                    <input type="text" id="recherche" class="form-control" placeholder="Rechercher une opération...">
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Date & Heure</th>
                        <th>Lieu / Canal</th>
                        <th>Type d'opération</th>
                        <th class="text-end">Frais prélevés</th>
                        <th class="text-end">Montant brut</th>
                    </tr>
                </thead>
                <tbody id="table-corps">
                    <?php if (!empty($historique) && is_array($historique)): ?>
                        <?php foreach ($historique as $tx): ?>
                            <tr class="ligne-transaction">
                                <td class="fw-bold small"><?= esc($tx['date_operation']) ?></td>
                                <td class="text-muted small"><?= esc($tx['lieu']) ?></td>
                                <td>
                                    <?php if ($tx['type_operation'] === 'Dépôt' || $tx['type_operation'] === 'Transfert Reçu'): ?>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle"><?= esc($tx['type_operation']) ?></span>
                                    <?php else: ?>
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle"><?= esc($tx['type_operation']) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end text-muted font-monospace small">
                                    <?= number_format($tx['frais'], 2, ',', ' ') ?> Ar
                                </td>
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
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-3 d-block mb-2"></i> Aucune opération enregistrée sur ce compte.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Script local pour le filtrage en temps réel dans le tableau -->
<script>
    document.getElementById('recherche').addEventListener('input', function() {
        const valeur = this.value.toLowerCase();
        const lignes = document.querySelectorAll('.ligne-transaction');
        
        lignes.forEach(ligne => {
            const texte = ligne.textContent.toLowerCase();
            ligne.style.display = texte.includes(valeur) ? '' : 'none';
        });
    });
</script>

<script src="<?= base_url('assets/js/bootstrap.bundle.min.js') ?>"></script>
</body>
</html>
