<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Opérateur - e-Money</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap-icons.css') ?>">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { background: #f0f2f5; font-family: 'Segoe UI', Tahoma, sans-serif; }
        .navbar { margin-bottom: 0; }
        .stat-card {
            border-radius: 15px;
            border: none;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
        }
        .chart-container {
            border-radius: 15px;
            border: none;
        }
        .badge-op {
            font-size: 0.85rem;
            padding: 5px 12px;
        }
        .table-card {
            border-radius: 15px;
            border: none;
        }
    </style>
</head>
<body>
    <?= view('layouts/navbar') ?>
    <div class="container-fluid px-4 mt-4">
        <!-- En-tête -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-1">📊 Tableau de bord</h2>
                <p class="text-muted mb-0">Bienvenue, <strong><?= esc($operateur_nom) ?></strong></p>
            </div>
            <div>
                <span class="badge bg-secondary bg-opacity-25 text-dark p-2 px-3">
                    <i class="bi bi-calendar3 me-1"></i> <?= date('d/m/Y') ?>
                </span>
            </div>

        <!-- Cartes statistiques -->
        <div class="row g-3 mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="card stat-card shadow-sm bg-white h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="stat-icon bg-primary bg-opacity-10 text-primary me-3">
                            <i class="bi bi-people-fill"></i>
                        </div>
                        <div>
                            <h3 class="fw-bold mb-0"><?= number_format($totalUsers) ?></h3>
                            <small class="text-muted">Utilisateurs</small>
                        </div>
                </div>
            <div class="col-xl-3 col-md-6">
                <div class="card stat-card shadow-sm bg-white h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="stat-icon bg-success bg-opacity-10 text-success me-3">
                            <i class="bi bi-arrow-left-right"></i>
                        </div>
                        <div>
                            <h3 class="fw-bold mb-0"><?= number_format($todayOps) ?></h3>
                            <small class="text-muted">Transactions aujourd'hui</small>
                        </div>
                </div>
            <div class="col-xl-3 col-md-6">
                <div class="card stat-card shadow-sm bg-white h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="stat-icon bg-warning bg-opacity-10 text-warning me-3">
                            <i class="bi bi-cash-stack"></i>
                        </div>
                        <div>
                            <h3 class="fw-bold mb-0"><?= number_format($totalGains, 0, ',', ' ') ?></h3>
                            <small class="text-muted">Gains totaux (Ar)</small>
                        </div>
                </div>
            <div class="col-xl-3 col-md-6">
                <div class="card stat-card shadow-sm bg-white h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="stat-icon bg-info bg-opacity-10 text-info me-3">
                            <i class="bi bi-wallet2"></i>
                        </div>
                        <div>
                            <h3 class="fw-bold mb-0"><?= number_format($totalSolde, 0, ',', ' ') ?></h3>
                            <small class="text-muted">Solde total (Ar)</small>
                        </div>
                </div>
        </div>

        <!-- Graphiques + Répartition -->
        <div class="row g-3 mb-4">
            <!-- Graphique évolution des gains sur 7 jours -->
            <div class="col-lg-8">
                <div class="card chart-container shadow-sm bg-white h-100">
                    <div class="card-header bg-white border-0 pt-3">
                        <h5 class="fw-bold mb-0"><i class="bi bi-graph-up me-2 text-success"></i>Évolution des gains (7 derniers jours)</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="gainsChart" height="200"></canvas>
                    </div>
            </div>
            <!-- Répartition par type d'opération -->
            <div class="col-lg-4">
                <div class="card chart-container shadow-sm bg-white h-100">
                    <div class="card-header bg-white border-0 pt-3">
                        <h5 class="fw-bold mb-0"><i class="bi bi-pie-chart me-2 text-primary"></i>Répartition</h5>
                    </div>
                    <div class="card-body d-flex align-items-center justify-content-center">
                        <canvas id="pieChart" height="200"></canvas>
                    </div>
            </div>

        <!-- Dernières opérations + Liens rapides -->
        <div class="row g-3 mb-4">
            <div class="col-lg-8">
                <div class="card table-card shadow-sm bg-white">
                    <div class="card-header bg-white border-0 pt-3 d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0"><i class="bi bi-clock-history me-2 text-secondary"></i>Dernières opérations</h5>
                        <a href="<?= site_url('situation') ?>" class="btn btn-sm btn-outline-primary">Voir tout</a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Type</th>
                                        <th>Client</th>
                                        <th>Montant</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($recentOps)) : ?>
                                        <?php foreach ($recentOps as $op) : ?>
                                            <?php 
                                                $badgeClass = $op['type'] === 'Dépôt' ? 'success' : ($op['type'] === 'Retrait' ? 'warning' : 'primary');
                                            ?>
                                            <tr>
                                                <td><span class="badge bg-<?= $badgeClass ?> bg-opacity-75"><?= esc($op['type']) ?></span></td>
                                                <td><?= esc($op['nom']) ?></td>
                                                <td class="fw-semibold"><?= number_format($op['montant'], 0, ',', ' ') ?> Ar</td>
                                                <td><small class="text-muted"><?= date('d/m/Y H:i', strtotime($op['date'])) ?></small></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else : ?>
                                        <tr><td colspan="4" class="text-center text-muted py-3">Aucune opération récente</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                </div>
            <div class="col-lg-4">
                <div class="card table-card shadow-sm bg-white h-100">
                    <div class="card-header bg-white border-0 pt-3">
                        <h5 class="fw-bold mb-0"><i class="bi bi-link-45deg me-2 text-primary"></i>Accès rapide</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="<?= site_url('prefixe') ?>" class="btn btn-outline-secondary text-start p-3">
                                <i class="bi bi-diagram-3 me-2"></i> Gérer les préfixes
                            </a>
                            <a href="<?= site_url('bareme') ?>" class="btn btn-outline-secondary text-start p-3">
                                <i class="bi bi-table me-2"></i> Gérer les barèmes
                            </a>
                            <a href="<?= site_url('gain') ?>" class="btn btn-outline-secondary text-start p-3">
                                <i class="bi bi-graph-up me-2"></i> Voir les gains
                            </a>
                            <a href="<?= site_url('situation') ?>" class="btn btn-outline-secondary text-start p-3">
                                <i class="bi bi-people me-2"></i> Situation utilisateurs
                            </a>
                            <a href="<?= site_url('commission') ?>" class="btn btn-outline-secondary text-start p-3">
                                <i class="bi bi-percent me-2"></i> Configuration commission
                            </a>
                            <a href="<?= site_url('compensation') ?>" class="btn btn-outline-secondary text-start p-3">
                                <i class="bi bi-arrow-left-right me-2"></i> Compensation
                            </a>
                        </div>
                </div>
        </div>

    <script src="<?= base_url('assets/js/bootstrap.bundle.min.js') ?>"></script>
    <script>
        // Graphique d'évolution des gains (7 jours)
        const ctx = document.getElementById('gainsChart').getContext('2d');
        const gainsData = <?= json_encode($gains7j) ?>;
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: gainsData.map(d => d.date),
                datasets: [{
                    label: 'Gains (Ar)',
                    data: gainsData.map(d => d.montant),
                    borderColor: '#198754',
                    backgroundColor: 'rgba(25, 135, 84, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#198754',
                    pointRadius: 4,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { callback: v => v.toLocaleString() + ' Ar' } }
                }
            }
        });

        // Graphique en camembert (répartition)
        const ctx2 = document.getElementById('pieChart').getContext('2d');
        new Chart(ctx2, {
            type: 'doughnut',
            data: {
                labels: ['Dépôts', 'Retraits', 'Transferts'],
                datasets: [{
                    data: [<?= $depotsCount ?>, <?= $retraitsCount ?>, <?= $transfertsCount ?>],
                    backgroundColor: ['#198754', '#ffc107', '#0d6efd'],
                    borderWidth: 2,
                    borderColor: '#fff',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { padding: 15, usePointStyle: true } }
                }
            }
        });
    </script>
</body>
</html>
