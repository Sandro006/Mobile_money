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

        <!-- Filtres avancés -->
        <div class="row g-2 mb-3 align-items-end">
            <div class="col-md-2">
                <label class="form-label small fw-semibold">Type d'opération</label>
                <select id="filtre-type" class="form-select form-select-sm">
                    <option value="">Tous</option>
                    <option value="Dépôt">Dépôt</option>
                    <option value="Retrait">Retrait</option>
                    <option value="Transfert">Transfert</option>
                    <option value="Transfert Reçu">Transfert Reçu</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold">Date début</label>
                <input type="date" id="filtre-date-debut" class="form-control form-control-sm">
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold">Date fin</label>
                <input type="date" id="filtre-date-fin" class="form-control form-control-sm">
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold">Montant min</label>
                <input type="number" id="filtre-montant-min" class="form-control form-control-sm" placeholder="0" min="0">
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold">Montant max</label>
                <input type="number" id="filtre-montant-max" class="form-control form-control-sm" placeholder="999999" min="0">
            </div>
            <div class="col-md-2 d-grid">
                <button id="btn-reinitialiser" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-counterclockwise"></i> Réinitialiser
                </button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered align-middle mb-0" id="table-historique">
                <thead class="table-light">
                    <tr>
                        <th class="tri-colonne" data-col="date" style="cursor:pointer">Date & Heure <i class="bi bi-arrow-down-up ms-1 small"></i></th>
                        <th class="tri-colonne" data-col="lieu" style="cursor:pointer">Lieu / Canal <i class="bi bi-arrow-down-up ms-1 small"></i></th>
                        <th class="tri-colonne" data-col="type" style="cursor:pointer">Type d'opération <i class="bi bi-arrow-down-up ms-1 small"></i></th>
                        <th class="text-end tri-colonne" data-col="frais" style="cursor:pointer">Frais prélevés <i class="bi bi-arrow-down-up ms-1 small"></i></th>
                        <th class="text-end tri-colonne" data-col="montant" style="cursor:pointer">Montant brut <i class="bi bi-arrow-down-up ms-1 small"></i></th>
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

<!-- Script local pour le filtrage, tri et réinitialisation -->
<script>
(function() {
    const recherche = document.getElementById('recherche');
    const filtreType = document.getElementById('filtre-type');
    const filtreDateDebut = document.getElementById('filtre-date-debut');
    const filtreDateFin = document.getElementById('filtre-date-fin');
    const filtreMontantMin = document.getElementById('filtre-montant-min');
    const filtreMontantMax = document.getElementById('filtre-montant-max');
    const btnReset = document.getElementById('btn-reinitialiser');
    const tableCorps = document.getElementById('table-corps');

    // État du tri
    let triColonne = null;
    let triOrdre = 'asc';

    // Fonction pour parser une date au format "YYYY-MM-DD HH:MM:SS" ou similaire
    function parserDate(dateStr) {
        if (!dateStr) return null;
        // Supprime les espaces insécables et nettoie
        const propre = dateStr.trim().replace(/\u00A0/g, ' ');
        const d = new Date(propre.replace(' ', 'T'));
        return isNaN(d.getTime()) ? null : d;
    }

    // Fonction pour extraire le montant numérique d'une cellule (ex: "+ 1 000,00 Ar" -> 1000.00)
    function extraireMontant(cellule) {
        const texte = cellule.textContent.trim();
        const match = texte.replace(/\s/g, '').match(/([+-]?)[\d.,]+/);
        if (!match) return 0;
        const signe = match[1] === '-' ? -1 : 1;
        const nombre = parseFloat(match[0].replace(/[^0-9,]/g, '').replace(',', '.'));
        return signe * (isNaN(nombre) ? 0 : nombre);
    }

    // Fonction pour extraire les frais numériques
    function extraireFrais(cellule) {
        const texte = cellule.textContent.trim();
        const match = texte.replace(/\s/g, '').match(/[\d.,]+/);
        if (!match) return 0;
        return parseFloat(match[0].replace(',', '.')) || 0;
    }

    // Fonction principale de filtrage
    function filtrer() {
        const rechercheVal = recherche.value.toLowerCase().trim();
        const typeVal = filtreType.value;
        const dateDebutVal = filtreDateDebut.value; // format YYYY-MM-DD
        const dateFinVal = filtreDateFin.value;
        const montantMin = parseFloat(filtreMontantMin.value) || 0;
        const montantMax = parseFloat(filtreMontantMax.value) || Infinity;

        const lignes = tableCorps.querySelectorAll('.ligne-transaction');

        lignes.forEach(ligne => {
            const cellules = ligne.querySelectorAll('td');
            
            // 1. Filtre texte (recherche générale)
            const texteComplet = ligne.textContent.toLowerCase();
            if (rechercheVal && !texteComplet.includes(rechercheVal)) {
                ligne.style.display = 'none';
                return;
            }

            // 2. Filtre par type d'opération (colonne 2)
            if (typeVal) {
                const typeCell = cellules[2]?.textContent.trim();
                if (typeCell !== typeVal) {
                    ligne.style.display = 'none';
                    return;
                }
            }

            // 3. Filtre par date (colonne 0)
            const dateCell = cellules[0]?.textContent.trim();
            const dateOp = parserDate(dateCell);
            if (dateOp) {
                if (dateDebutVal) {
                    const debut = new Date(dateDebutVal + 'T00:00:00');
                    if (dateOp < debut) {
                        ligne.style.display = 'none';
                        return;
                    }
                }
                if (dateFinVal) {
                    const fin = new Date(dateFinVal + 'T23:59:59');
                    if (dateOp > fin) {
                        ligne.style.display = 'none';
                        return;
                    }
                }
            }

            // 4. Filtre par montant (colonne 4)
            const montantReel = extraireMontant(cellules[4]);
            if (Math.abs(montantReel) < montantMin || Math.abs(montantReel) > montantMax) {
                ligne.style.display = 'none';
                return;
            }

            ligne.style.display = '';
        });
    }

    // Fonction de tri
    function trier(colonne, ordre) {
        const lignes = Array.from(tableCorps.querySelectorAll('.ligne-transaction'));

        lignes.sort((a, b) => {
            const cellA = a.querySelectorAll('td')[colonne];
            const cellB = b.querySelectorAll('td')[colonne];
            if (!cellA || !cellB) return 0;

            let valA, valB;

            if (colonne === 0) {
                // Date
                valA = parserDate(cellA.textContent) || new Date(0);
                valB = parserDate(cellB.textContent) || new Date(0);
                return ordre === 'asc' ? valA - valB : valB - valA;
            } else if (colonne === 1) {
                // Lieu / Canal (texte)
                valA = cellA.textContent.trim().toLowerCase();
                valB = cellB.textContent.trim().toLowerCase();
                return ordre === 'asc' ? valA.localeCompare(valB) : valB.localeCompare(valA);
            } else if (colonne === 2) {
                // Type (texte)
                valA = cellA.textContent.trim().toLowerCase();
                valB = cellB.textContent.trim().toLowerCase();
                return ordre === 'asc' ? valA.localeCompare(valB) : valB.localeCompare(valA);
            } else if (colonne === 3) {
                // Frais (nombre)
                valA = extraireFrais(cellA);
                valB = extraireFrais(cellB);
                return ordre === 'asc' ? valA - valB : valB - valA;
            } else if (colonne === 4) {
                // Montant (nombre avec signe)
                valA = extraireMontant(cellA);
                valB = extraireMontant(cellB);
                return ordre === 'asc' ? valA - valB : valB - valA;
            }
            return 0;
        });

        // Réinsérer les lignes triées
        lignes.forEach(ligne => tableCorps.appendChild(ligne));
    }

    // Fonction de réinitialisation
    function reinitialiser() {
        recherche.value = '';
        filtreType.value = '';
        filtreDateDebut.value = '';
        filtreDateFin.value = '';
        filtreMontantMin.value = '';
        filtreMontantMax.value = '';
        triColonne = null;
        triOrdre = 'asc';
        // Remettre les icônes de tri
        document.querySelectorAll('.tri-colonne i').forEach(icone => {
            icone.className = 'bi bi-arrow-down-up ms-1 small';
        });
        filtrer();
    }

    // Attacher les événements de filtrage
    recherche.addEventListener('input', filtrer);
    filtreType.addEventListener('change', filtrer);
    filtreDateDebut.addEventListener('change', filtrer);
    filtreDateFin.addEventListener('change', filtrer);
    filtreMontantMin.addEventListener('input', filtrer);
    filtreMontantMax.addEventListener('input', filtrer);
    btnReset.addEventListener('click', reinitialiser);

    // Attacher les événements de tri sur les en-têtes
    document.querySelectorAll('.tri-colonne').forEach(th => {
        th.addEventListener('click', function() {
            const colonneMap = { 'date': 0, 'lieu': 1, 'type': 2, 'frais': 3, 'montant': 4 };
            const col = colonneMap[this.dataset.col];
            if (col === undefined) return;

            // Basculer l'ordre
            if (triColonne === col) {
                triOrdre = triOrdre === 'asc' ? 'desc' : 'asc';
            } else {
                triColonne = col;
                triOrdre = 'asc';
            }

            // Mettre à jour les icônes
            document.querySelectorAll('.tri-colonne i').forEach(icone => {
                icone.className = 'bi bi-arrow-down-up ms-1 small';
            });
            const icone = this.querySelector('i');
            icone.className = triOrdre === 'asc' ? 'bi bi-sort-up ms-1 small' : 'bi bi-sort-down ms-1 small';

            trier(col, triOrdre);
        });
    });

    // Appliquer le filtre initial
    filtrer();
})();
</script>

<script src="<?= base_url('assets/js/bootstrap.bundle.min.js') ?>"></script>
</body>
</html>
