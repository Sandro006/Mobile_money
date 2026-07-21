<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
    <div class="container-fluid px-4">
        <a class="navbar-brand fw-bold" href="<?= site_url('operateur/dashboard') ?>">
            <i class="bi bi-gear-fill text-warning me-2"></i>e-Money
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link <?= (service('uri')->getSegment(1) === 'operateur' && service('uri')->getSegment(2) === 'dashboard') ? 'active' : '' ?>" href="<?= site_url('operateur/dashboard') ?>">
                        <i class="bi bi-speedometer2 me-1"></i>Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= service('uri')->getSegment(1) === 'prefixe' ? 'active' : '' ?>" href="<?= site_url('prefixe') ?>">
                        <i class="bi bi-diagram-3 me-1"></i>Préfixes
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= service('uri')->getSegment(1) === 'bareme' ? 'active' : '' ?>" href="<?= site_url('bareme') ?>">
                        <i class="bi bi-table me-1"></i>Barèmes
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= service('uri')->getSegment(1) === 'gain' ? 'active' : '' ?>" href="<?= site_url('gain') ?>">
                        <i class="bi bi-graph-up me-1"></i>Gains
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= service('uri')->getSegment(1) === 'situation' ? 'active' : '' ?>" href="<?= site_url('situation') ?>">
                        <i class="bi bi-people me-1"></i>Situation
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= service('uri')->getSegment(1) === 'commission' ? 'active' : '' ?>" href="<?= site_url('commission') ?>">
                        <i class="bi bi-percent me-1"></i>Commission
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= service('uri')->getSegment(1) === 'compensation' ? 'active' : '' ?>" href="<?= site_url('compensation') ?>">
                        <i class="bi bi-arrow-left-right me-1"></i>Compensation
                    </a>
                </li>
            </ul>
            
            <!-- Barre de recherche globale -->
            <form class="d-flex me-3" action="<?= site_url('situation') ?>" method="GET">
                <div class="input-group input-group-sm">
                    <input type="text" class="form-control form-control-sm bg-dark border-secondary text-light" name="search" placeholder="Rechercher utilisateur..." aria-label="Rechercher">
                    <button class="btn btn-outline-light btn-sm" type="submit">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </form>
            
            <!-- Info opérateur + Déconnexion -->
            <div class="navbar-nav align-items-center">
                <span class="nav-item text-light me-3 small">
                    <i class="bi bi-person-circle me-1"></i> <?= esc(session()->get('operateur_nom') ?? 'Opérateur') ?>
                </span>
                <a class="btn btn-outline-danger btn-sm" href="<?= site_url('deconnexion') ?>">
                    <i class="bi bi-box-arrow-right me-1"></i>Déconnexion
                </a>
            </div>
    </div>
</nav>
