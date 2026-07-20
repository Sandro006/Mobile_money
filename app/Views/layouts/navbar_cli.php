<!-- Barre de Navigation -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold" href="/client">e-Money</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link active" href="/client">Accueil</a></li>
                <li class="nav-item"><a class="nav-link" href="/operation/page-depot">Faire un Dépôt</a></li>
                <li class="nav-item"><a class="nav-link" href="/operation/page-retrait">Faire un Retrait</a></li>
                <li class="nav-item"><a class="nav-link" href="/operation/page-transfert">Faire un Transfert</a></li>
                <li class="nav-item"><a class="nav-link" href="/client/historique">Historique</a></li>
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