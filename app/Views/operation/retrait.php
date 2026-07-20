<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Effectuer un Retrait - e-Money</title>
    <link href="<?= base_url('assets/css/bootstrap.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/bootstrap-icons.css') ?>" rel="stylesheet">
</head>
<body class="bg-light">

    <?= view('layouts/navbar_cli') ?>


<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            
            <?php if (session()->getFlashdata('erreur')): ?>
                <div class="alert alert-danger mb-3">
                    <?= session()->getFlashdata('erreur') ?>
                </div>
            <?php endif; ?>

            <div class="card p-4 shadow-sm">
                <h4 class="fw-bold mb-3"><i class="bi bi-arrow-up-right-square text-danger me-2"></i>Faire un retrait</h4>
                <p class="text-muted small">Des frais de retrait réglementaires seront automatiquement calculés et déduits selon les grilles de tranches en vigueur.</p>
                <hr>

                <form action="/operation/retrait" method="POST">
                    <?= csrf_field() ?>

                    <!-- Choix du réseau -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">Réseau du guichet / kiosque</label>
                        <div class="d-flex gap-3">
                            <div class="form-check border rounded p-3 flex-fill text-center bg-white shadow-sm cursor-pointer">
                                <input class="form-check-input ms-0 me-2" type="radio" name="type_operateur" id="meme_op" value="interne" checked>
                                <label class="form-check-label fw-bold text-success" for="meme_op">
                                    <i class="bi bi-check-circle-fill me-1"></i> Même opérateur
                                </label>
                            </div>
                            <div class="form-check border rounded p-3 flex-fill text-center bg-white shadow-sm cursor-pointer">
                                <input class="form-check-input ms-0 me-2" type="radio" name="type_operateur" id="autre_op" value="interop">
                                <label class="form-check-label fw-bold text-primary" for="autre_op">
                                    <i class="bi bi-shuffle me-1"></i> Autre opérateur
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Choix de l'opérateur concerné -->
                    <div class="mb-3 d-none" id="bloc_choix_operateur">
                        <label for="id_operateur_concerne" class="form-label fw-bold text-primary">Sélectionnez l'opérateur du Kiosque</label>
                            <select id="id_operateur_concerne" name="id_operateur_concerne" class="form-select form-select-lg">
                                <option value="" disabled selected>-- Choisir un opérateur --</option>
                                
                                <?php if (!empty($operateurs)): ?>
                                    <?php foreach ($operateurs as $op): ?>
                                        <?php 
                                            // Récupération sécurisée du taux (prend 0 si NULL)
                                            $tauxBrut = (float)($op['taux'] ?? 0);
                                            // Formatage propre pour l'affichage textuel (ex: 30,00)
                                            $tauxAffiche = number_format($tauxBrut, 2, ',', ' '); 
                                        ?>
                                        <option value="<?= esc($op['id']) ?>" data-taux="<?= esc($tauxBrut) ?>">
                                            <?= esc($op['libelle'] ?? $op['nom']) ?> (<?= $tauxAffiche ?>%)
                                        </option>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <option value="" disabled>Aucun opérateur disponible</option>
                                <?php endif; ?>
                                
                            </select>

                    </div>

                    <!-- Champ Montant -->
                    <div class="mb-3">
                        <div class="mb-3">
                            <div class="form-check form-switch p-2 border rounded bg-white shadow-sm">
                                <input class="form-check-input ms-1 me-2" type="checkbox" id="frais_inclus" name="frais_inclus" value="1">
                                <label class="form-check-label fw-bold text-dark" for="frais_inclus">
                                    <i class="bi bi-calculator text-primary me-1"></i> Recevoir le montant exact net (frais à ma charge)
                                </label>
                            </div>
                        </div>
                        <label for="montant" class="form-label fw-bold">Montant du retrait (Ar)</label>
                        <div class="input-group">
                            <input type="number" id="montant" name="montant" class="form-control form-control-lg text-center fw-bold" placeholder="Ex: 10000" min="100" required>
                            <span class="input-group-text fw-bold">Ar</span>
                        </div>
                    </div>

                    <!-- AJOUT : Ticket de Récapitulatif en Temps Réel -->
                    <div id="recap_ticket" class="p-3 bg-light rounded border border-secondary border-opacity-25 mb-3 d-none">
                        <h6 class="fw-bold mb-2 text-dark"><i class="bi bi-receipt me-1"></i> Récapitulatif estimé</h6>
                        <div class="d-flex justify-content-between small text-muted mb-1">
                            <span>Montant net retiré :</span>
                            <span id="recap_net" class="fw-bold">0,00 Ar</span>
                        </div>
                        <div class="d-flex justify-content-between small text-muted mb-1">
                            <span>Frais de base (Barème) :</span>
                            <span id="recap_frais_brut">0,00 Ar</span>
                        </div>
                        <div class="d-flex justify-content-between small text-muted mb-1 d-none" id="recap_ligne_commission">
                            <span>Commission inter-opérateur :</span>
                            <span id="recap_commission" class="text-primary">0,00 Ar</span>
                        </div>
                        <hr class="my-2">
                        <div class="d-flex justify-content-between fw-bold text-danger">
                            <span>Total à débiter :</span>
                            <span id="recap_total">0,00 Ar</span>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="lieu" class="form-label text-muted">Lieu du retrait</label>
                        <input type="text" id="lieu" name="lieu" class="form-control" value="Kiosque" placeholder="Ex: Kiosque Analakely...">
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <a href="/client/dashboard" class="text-decoration-none text-secondary"><i class="bi bi-arrow-left"></i> Retour</a>
                        <button type="submit" class="btn btn-danger px-4 fw-bold">Confirmer le retrait</button>
                    </div>

                </form>
            </div>

        </div>
    </div>
</div>

<script src="<?= base_url('assets/js/bootstrap.bundle.min.js') ?>"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const inputMontant = document.getElementById('montant');
    const selectOperateur = document.getElementById('id_operateur_concerne');
    const checkboxFrais = document.getElementById('frais_inclus');
    const radiosType = document.querySelectorAll('input[name="type_operateur"]');
    const recapTicket = document.getElementById('recap_ticket');

    function formatMoney(value) {
        return value.toFixed(2).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, " ") + " Ar";
    }

    function appelerApiFrais() {
        const montant = parseFloat(inputMontant.value) || 0;
        const typeSelectionne = document.querySelector('input[name="type_operateur"]:checked').value;
        const idOpKiosque = selectOperateur.value;
        const inclureFrais = checkboxFrais.checked ? '1' : '0';

        if (montant < 100 || (typeSelectionne === 'interop' && !idOpKiosque)) {
            recapTicket.classList.add('d-none');
            return;
        }

        const formData = new FormData();
        formData.append('montant', montant);
        formData.append('type_operateur', typeSelectionne);
        formData.append('id_operateur_concerne', idOpKiosque);
        formData.append('frais_inclus', inclureFrais);
        
        const csrfToken = document.querySelector('input[name^="csrf_"]') ? document.querySelector('input[name^="csrf_"]').value : '';
        if (csrfToken) formData.append(document.querySelector('input[name^="csrf_"]').name, csrfToken);

        fetch('/api/calculer-frais-retrait', { method: 'POST', body: formData })
        .then(response => {
            if (!response.ok) throw new Error('Erreur HTTP ' + response.status);
            return response.json();
        })
        .then(data => {
            if (data.succes) {
                recapTicket.classList.remove('d-none');
                document.getElementById('recap_ligne_commission').classList.toggle('d-none', data.commission <= 0);

                // Mise à jour des libellés du ticket selon l'option cochée
                document.getElementById('recap_net').innerText = formatMoney(data.montant_net);
                document.getElementById('recap_frais_brut').innerText = formatMoney(data.frais_brut);
                document.getElementById('recap_commission').innerText = "+ " + formatMoney(data.commission);
                document.getElementById('recap_total').innerText = formatMoney(data.total_debite);
            }
        })
        .catch(error => console.error('Erreur API frais retrait:', error));
    }

    radiosType.forEach(elem => elem.addEventListener("change", function(e) {
        document.getElementById('bloc_choix_operateur').classList.toggle('d-none', e.target.value !== 'interop');
        if (e.target.value !== 'interop') selectOperateur.value = "";
        appelerApiFrais();
    }));

    inputMontant.addEventListener('input', appelerApiFrais);
    selectOperateur.addEventListener('change', appelerApiFrais);
    checkboxFrais.addEventListener('change', appelerApiFrais);
});
</script>

