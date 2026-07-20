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

<?= view('layouts/navbar_cli') ?>


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

<!-- ========== MODALE DE REÇU / FACTURE ========== -->
<?php $recu = session()->getFlashdata('recu'); ?>
<?php if ($recu): ?>
<div class="modal fade" id="modalRecu" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg">
            <!-- En-tête du ticket -->
            <div class="modal-header bg-<?= esc($recu['couleur'] ?? 'primary') ?> text-white border-0 py-3">
                <div class="text-center w-100">
                    <i class="bi <?= esc($recu['icone'] ?? 'bi-receipt') ?> fs-1 d-block mb-1"></i>
                    <h5 class="fw-bold mb-0"><?= esc($recu['type']) ?></h5>
                    <small class="opacity-75">Reçu de transaction</small>
                </div>
                <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 mt-2 me-2" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-4 bg-white">
                <!-- Informations du client -->
                <div class="text-center mb-3 pb-2 border-bottom border-2 border-light">
                    <strong class="small text-uppercase text-muted">e-Money</strong>
                    <div class="fw-bold"><?= esc($recu['nom'] ?? '') ?></div>
                    <small class="text-muted"><?= esc($recu['numero'] ?? '') ?></small>
                </div>

                <!-- Détails de l'opération -->
                <div class="small">
                    <!-- Ligne : Date -->
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">Date</span>
                        <span><?= esc($recu['date']) ?></span>
                    </div>
                    <!-- Ligne : Lieu -->
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">Lieu</span>
                        <span><?= esc($recu['lieu'] ?? '-') ?></span>
                    </div>

                    <?php if ($recu['type'] === 'Dépôt'): ?>
                        <!-- ===== DEPOT ===== -->
                        <hr class="my-2">
                        <div class="d-flex justify-content-between fw-bold text-success">
                            <span>Montant déposé</span>
                            <span>+ <?= number_format($recu['montant'], 2, ',', ' ') ?> Ar</span>
                        </div>

                    <?php elseif (strpos($recu['type'], 'Retrait') !== false): ?>
                        <!-- ===== RETRAIT ===== -->
                        <hr class="my-2">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Montant demandé</span>
                            <span><?= number_format($recu['montant_demande'], 2, ',', ' ') ?> Ar</span>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Frais de barème</span>
                            <span class="text-danger">- <?= number_format($recu['frais_brut'], 2, ',', ' ') ?> Ar</span>
                        </div>
                        <?php if (!empty($recu['commission']) && $recu['commission'] > 0): ?>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Commission inter-opérateur</span>
                            <span class="text-danger">- <?= number_format($recu['commission'], 2, ',', ' ') ?> Ar</span>
                        </div>
                        <?php endif; ?>
                        <hr class="my-2">
                        <div class="d-flex justify-content-between fw-bold <?= $recu['frais_inclus'] ? 'text-danger' : '' ?>">
                            <span><?= $recu['frais_inclus'] ? 'Total débité' : 'Montant net reçu' ?></span>
                            <span><?= number_format($recu['montant'], 2, ',', ' ') ?> Ar</span>
                        </div>
                        <?php if ($recu['frais_inclus']): ?>
                        <div class="d-flex justify-content-between fw-bold text-success mt-1">
                            <span>Montant net reçu</span>
                            <span><?= number_format($recu['montant_demande'], 2, ',', ' ') ?> Ar</span>
                        </div>
                        <?php endif; ?>

                    <?php elseif (strpos($recu['type'], 'Transfert') !== false): ?>
                        <!-- ===== TRANSFERT ===== -->
                        <hr class="my-2">
                        <?php if ($recu['sous_type'] === 'simple'): ?>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Destinataire</span>
                            <span class="fw-bold"><?= esc($recu['destinataire_nom'] ?? '') ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Téléphone</span>
                            <span><?= esc($recu['destinataire_tel'] ?? '') ?></span>
                        </div>
                        <?php else: ?>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Nombre de bénéficiaires</span>
                            <span class="fw-bold"><?= (int)$recu['nb_destinataires'] ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Part par personne</span>
                            <span><?= number_format($recu['montant_unitaire'], 2, ',', ' ') ?> Ar</span>
                        </div>
                        <?php endif; ?>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Montant total envoyé</span>
                            <span><?= number_format($recu['montant'], 2, ',', ' ') ?> Ar</span>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Frais d'envoi</span>
                            <span class="text-danger">- <?= number_format($recu['frais_totaux'], 2, ',', ' ') ?> Ar</span>
                        </div>
                        <hr class="my-2">
                        <div class="d-flex justify-content-between fw-bold text-danger">
                            <span>Total débité</span>
                            <span><?= number_format($recu['montant'] + $recu['frais_totaux'], 2, ',', ' ') ?> Ar</span>
                        </div>
                    <?php endif; ?>

                    <!-- Nouveau solde -->
                    <hr class="my-2">
                    <div class="d-flex justify-content-between small">
                        <span class="text-muted">Nouveau solde disponible</span>
                        <span class="fw-bold text-<?= esc($recu['couleur'] ?? 'primary') ?>"><?= number_format($recu['nouveau_solde'] ?? 0, 2, ',', ' ') ?> Ar</span>
                    </div>
                </div>
            </div>

            <!-- Pied de la modale -->
            <div class="modal-footer border-0 justify-content-center bg-light py-3">
                <button type="button" class="btn btn-<?= esc($recu['couleur'] ?? 'primary') ?> px-4 fw-bold" data-bs-dismiss="modal">
                    <i class="bi bi-check-circle me-1"></i> Fermer
                </button>
                <button type="button" class="btn btn-outline-secondary" onclick="window.print()">
                    <i class="bi bi-printer me-1"></i> Télécharger
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const modalRecu = new bootstrap.Modal(document.getElementById('modalRecu'));
    modalRecu.show();
});
</script>
<?php endif; ?>

<script src="<?= base_url('assets/js/bootstrap.bundle.min.js') ?>"></script>
</body>
</html>
