<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inserer nouveau utilisateur e-money</title>
        <link href="<?= base_url('assets/css/bootstrap.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/bootstrap-icons.css') ?>" rel="stylesheet">
</head>
<body>
    <body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                
                <!-- Gestion des messages flash d'erreur ou de succès -->
                <?php if (session()->getFlashdata('erreur')): ?>
                    <div class="alert alert-danger shadow-sm mb-3">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i><?= session()->getFlashdata('erreur') ?>
                    </div>
                <?php endif; ?>

                <div class="card shadow-sm border-0">
                    <div class="card-header bg-primary text-white p-3">
                        <h4 class="mb-0 fw-bold">
                            <i class="bi bi-person-plus-fill me-2"></i>Créer un nouvel utilisateur
                        </h4>
                    </div>
                    <div class="card-body p-4">
                        <p class="text-muted small">
                            L'utilisateur créé sera automatiquement rattaché à votre réseau opérateur actuel.
                        </p>
                        <hr>

                        <!-- Le formulaire pointe vers la méthode de sauvegarde dans votre contrôleur -->
                        <form action="<?= site_url('situation/sauvegarder') ?>" method="POST">
                            <?= csrf_field() ?>

                            <!-- Champ Nom Complet -->
                            <div class="mb-3">
                                <label for="nom_utilisateur" class="form-label fw-bold">Nom complet</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="bi bi-person"></i></span>
                                    <input type="text" 
                                           id="nom_utilisateur" 
                                           name="nom_utilisateur" 
                                           class="form-control form-control-lg" 
                                           placeholder="Ex: Jean Dupont" 
                                           required>
                                </div>
                            </div>

                            <!-- Champ Numéro de téléphone -->
                            <div class="mb-3">
                                <label for="numero_utilisateur" class="form-label fw-bold">Numéro de téléphone</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="bi bi-phone"></i></span>
                                    <input type="tel" 
                                           id="numero_utilisateur" 
                                           name="numero_utilisateur" 
                                           class="form-control form-control-lg" 
                                           placeholder="Ex: 0340000000" 
                                           required>
                                </div>
                            </div>

                            <!-- Champ Solde Initial -->
                            <div class="mb-4">
                                <label for="solde_utilisateur" class="form-label fw-bold">Solde initial (Ar)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light fw-bold">Ar</span>
                                    <input type="number" 
                                           id="solde_utilisateur" 
                                           name="solde_utilisateur" 
                                           class="form-control form-control-lg" 
                                           placeholder="0.00" 
                                           step="0.01" 
                                           min="0" 
                                           value="0.00" 
                                           required>
                                </div>
                            </div>

                            <!-- Boutons d'action -->
                            <div class="d-flex justify-content-between align-items-center">
                                <a href="<?= site_url('operateur/situation') ?>" class="btn btn-light border px-3">
                                    <i class="bi bi-arrow-left me-1"></i> Annuler
                                </a>
                                <button type="submit" class="btn btn-primary px-4 fw-bold">
                                    <i class="bi bi-check-circle me-1"></i> Enregistrer l'utilisateur
                                </button>
                            </div>

                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</body>

</body>
</html>