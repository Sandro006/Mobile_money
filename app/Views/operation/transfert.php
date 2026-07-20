<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faire un Transfert - e-Money</title>
    <link href="<?= base_url('assets/css/bootstrap.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/bootstrap-icons.css') ?>" rel="stylesheet">
</head>

<body class="bg-light">

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
                    <li class="nav-item"><a class="nav-link active" href="/operation/page-transfert">Faire un Transfert</a></li>
                    <li class="nav-item"><a class="nav-link" href="/client/historique">Historique</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">

                <?php if (session()->getFlashdata('erreur')): ?>
                    <div class="alert alert-danger mb-3">
                        <?= session()->getFlashdata('erreur') ?>
                    </div>
                <?php endif; ?>
                <div class="card p-4 shadow-sm">
                    <h4 class="fw-bold mb-3"><i class="bi bi-send text-warning me-2"></i>Transférer de l'argent</h4>
                    <p class="text-muted small">Envoyez instantanément des fonds à un ou plusieurs bénéficiaires.</p>
                    <hr>

                    <form action="/operation/transfert" method="POST">
                        <?= csrf_field() ?>

                        <!-- AJOUT : Sélection du mode d'envoi -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Type d'envoi</label>
                            <div class="d-flex gap-3">
                                <div class="form-check border rounded p-3 flex-fill text-center bg-white shadow-sm cursor-pointer">
                                    <input class="form-check-input ms-0 me-2" type="radio" name="mode_transfert" id="mode_unique" value="simple" checked>
                                    <label class="form-check-label fw-bold text-dark" for="mode_unique">
                                        <i class="bi bi-person-fill text-primary me-1"></i> Unique (1 numéro)
                                    </label>
                                </div>
                                <div class="form-check border rounded p-3 flex-fill text-center bg-white shadow-sm cursor-pointer">
                                    <input class="form-check-input ms-0 me-2" type="radio" name="mode_transfert" id="mode_groupe" value="multiple">
                                    <label class="form-check-label fw-bold text-dark" for="mode_groupe">
                                        <i class="bi bi-people-fill text-success me-1"></i> Multiple (Groupé)
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- CHAMP 1 : Numéro unique (Visible par défaut) -->
                        <div class="mb-3" id="bloc_unique">
                            <label for="numero_destinataire" class="form-label fw-bold">Numéro de téléphone du destinataire</label>
                            <input type="text" id="numero_destinataire" name="numero_destinataire" class="form-control form-control-lg font-monospace text-center" placeholder="Ex: 0331234567">
                        </div>

                        <!-- CHAMP 2 : Numéros multiples (Masqué par défaut) -->
                        <div class="mb-3 d-none" id="bloc_multiple">
                            <label for="numeros_destinataires" class="form-label fw-bold">Numéros de téléphone des destinataires</label>
                            <textarea id="numeros_destinataires" name="numeros_destinataires" class="form-control font-monospace" rows="2" placeholder="Ex: 0331234567, 0341234568"></textarea>
                            <small class="text-muted small">Séparez les numéros par des virgules (`,`), points-virgules (`;`) ou des espaces.</small>
                        </div>

                        <!-- Montant à transférer -->
                        <div class="mb-3">
                            <label id="label_montant" for="montant" class="form-label fw-bold">Montant à envoyer (Ar)</label>
                            <div class="input-group">
                                <input type="number" id="montant" name="montant" class="form-control form-control-lg text-center fw-bold" placeholder="0.00" min="100" required>
                                <span class="input-group-text fw-bold">Ar</span>
                            </div>
                        </div>

                        <!-- Ticket récapitulatif dynamique calculé par API AJAX -->
                        <div id="recap_ticket" class="p-3 bg-white rounded border border-secondary border-opacity-25 mb-4 d-none shadow-sm">
                            <h6 class="fw-bold mb-2 text-dark"><i class="bi bi-receipt me-1"></i> Récapitulatif de la distribution</h6>
                            <div class="d-flex justify-content-between small text-muted mb-1">
                                <span>Nombre de bénéficiaires :</span>
                                <span id="recap_nb_personnes" class="fw-bold text-dark">0</span>
                            </div>
                            <div class="d-flex justify-content-between small text-muted mb-1">
                                <span>Part nette par numéro :</span>
                                <span id="recap_part_unitaire" class="fw-bold text-success">0,00 Ar</span>
                            </div>
                            <div class="d-flex justify-content-between small text-muted mb-1">
                                <span>Cumul des frais d'envoi :</span>
                                <span id="recap_total_frais">0,00 Ar</span>
                            </div>
                            <hr class="my-2">
                            <div class="d-flex justify-content-between fw-bold text-dark">
                                <span>Total débité du compte :</span>
                                <span id="recap_total_facture" class="text-danger">0,00 Ar</span>
                            </div>
                        </div>

                        <input type="hidden" name="lieu" value="Mobile App">

                        <div class="d-flex justify-content-between align-items-center">
                            <a href="/client" class="text-decoration-none text-secondary"><i class="bi bi-arrow-left"></i> Retour</a>
                            <button type="submit" class="btn btn-warning px-4 fw-bold text-dark">Confirmer l'envoi</button>
                        </div>
                    </form>
                </div>

                <script src="<?= base_url('assets/js/bootstrap.bundle.min.js') ?>"></script>

                <script>
                    document.addEventListener("DOMContentLoaded", function() {
                        const radiosMode = document.querySelectorAll('input[name="mode_transfert"]');
                        const inputUnique = document.getElementById('numero_destinataire');
                        const txtMultiple = document.getElementById('numeros_destinataires');
                        const inputMontant = document.getElementById('montant');

                        const blocUnique = document.getElementById('bloc_unique');
                        const blocMultiple = document.getElementById('bloc_multiple');
                        const labelMontant = document.getElementById('label_montant');
                        const recapTicket = document.getElementById('recap_ticket');

                        function formatMoney(value) {
                            return value.toFixed(2).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, " ") + " Ar";
                        }

                        // Gestion du basculement visuel des modes
                        radiosMode.forEach((radio) => {
                            radio.addEventListener("change", function(e) {
                                if (e.target.value === 'simple') {
                                    blocUnique.classList.remove('d-none');
                                    inputUnique.setAttribute('required', 'required');

                                    blocMultiple.classList.add('d-none');
                                    txtMultiple.removeAttribute('required');
                                    txtMultiple.value = "";

                                    labelMontant.innerText = "Montant à envoyer (Ar)";
                                } else {
                                    blocMultiple.classList.remove('d-none');
                                    txtMultiple.setAttribute('required', 'required');

                                    blocUnique.classList.add('d-none');
                                    inputUnique.removeAttribute('required');
                                    inputUnique.value = "";

                                    labelMontant.innerText = "Montant global à distribuer (Ar)";
                                }
                                appelerApiCalculTransfert();
                            });
                        });

                        function appelerApiCalculTransfert() {
                            const montantGlobal = parseFloat(inputMontant.value) || 0;
                            const mode = document.querySelector('input[name="mode_transfert"]:checked').value;

                            let listeNumeros = [];
                            if (mode === 'simple') {
                                if (inputUnique.value.trim().length > 0) listeNumeros.push(inputUnique.value.trim());
                            } else {
                                listeNumeros = txtMultiple.value.split(/[\s,;]+/).filter(num => num.trim().length > 0);
                            }

                            const nbDestinataires = listeNumeros.length;
                            if (montantGlobal < 100 || nbDestinataires === 0) {
                                recapTicket.classList.add('d-none');
                                return;
                            }

                            const formData = new FormData();
                            formData.append('montant_global', montantGlobal);
                            listeNumeros.forEach(num => formData.append('numeros[]', num));

                            const csrfToken = document.querySelector('input[name^="csrf_"]');
                            if (csrfToken) formData.append(csrfToken.name, csrfToken.value);

                            fetch('/api/calculer-frais-transfert', {
                                    method: 'POST',
                                    body: formData
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.succes) {
                                        recapTicket.classList.remove('d-none');
                                        document.getElementById('recap_nb_personnes').innerText = data.nb_destinataires;
                                        document.getElementById('recap_part_unitaire').innerText = formatMoney(data.part_unitaire);
                                        document.getElementById('recap_total_frais').innerText = formatMoney(data.frais_globaux);
                                        document.getElementById('recap_total_facture').innerText = formatMoney(data.cout_total_facture);
                                    }
                                });
                        }

                        inputUnique.addEventListener('input', appelerApiCalculTransfert);
                        txtMultiple.addEventListener('input', appelerApiCalculTransfert);
                        inputMontant.addEventListener('input', appelerApiCalculTransfert);

                        // Initialisation
                        inputUnique.setAttribute('required', 'required');
                    });
                </script>
</body>

</html>