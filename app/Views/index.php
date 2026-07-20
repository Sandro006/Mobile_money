<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Utilisateurs</title>
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-people-fill"></i> Liste des Utilisateurs</h2>
        <span class="badge bg-primary fs-6">
            Total : <?= count($utilisateurs) ?>
        </span>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th scope="col" class="ps-4">ID</th>
                            <th scope="col">Nom complet</th>
                            <th scope="col">Numéro de téléphone</th>
                            <th scope="col" class="text-end pe-4">Solde actuel</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($utilisateurs) && is_array($utilisateurs)): ?>
                            <?php foreach ($utilisateurs as $user): ?>
                                <tr>
                                    <!-- Adaptation automatique selon que vos données soient des objets ou des tableaux -->
                                    <td class="ps-4 text-muted">
                                        <?= is_object($user) ? $user->id_utilisateur : $user['id_utilisateur'] ?>
                                    </td>
                                    <td class="fw-bold">
                                        <?= esc(is_object($user) ? $user->nom_utilisateur : $user['nom_utilisateur']) ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary font-monospace">
                                            <?= esc(is_object($user) ? $user->numero_utilisateur : $user['numero_utilisateur']) ?>
                                        </span>
                                    </td>
                                    <td class="text-end pe-4 fw-bold text-success">
                                        <!-- Formatage monétaire en Ariary (Ar) -->
                                        <?= number_format(is_object($user) ? $user->solde_utilisateur : $user['solde_utilisateur'], 2, ',', ' ') ?> Ar
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">
                                    Aucun utilisateur trouvé dans la base de données.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

</body>
</html>
