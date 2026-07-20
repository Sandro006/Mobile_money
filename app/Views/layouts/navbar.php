<nav class="navbar navbar-expand-lg navbar-dark bg-secondary">
    <div class="container">
        <a class="navbar-brand" href="<?= site_url('/') ?>">e-Money</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="<?= site_url('prefixe') ?>">Préfixes</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= site_url('bareme') ?>">Barèmes</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= site_url('gain') ?>">Gains</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= site_url('situation') ?>">Situation</a>
                </li>
                <li class="nav-item">
            <a class="nav-link" href="<?= site_url('commission') ?>">Commission </a>
                </li>
                <li class="nav-item">
    <a class="nav-link" href="<?= site_url('compensation') ?>">Compensation</a>
</li>
            </ul>
        </div>
    </div>
</nav>