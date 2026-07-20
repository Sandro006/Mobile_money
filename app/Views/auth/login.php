<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <style>
        body { font-family: Arial, sans-serif; background:#f5f5f5; }
        .container { max-width: 420px; margin: 60px auto; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,.08); }
        h2 { margin-top: 0; }
        .error { background:#ffe5e5; color:#b00020; padding:10px; border-radius:6px; margin-bottom: 15px; }
        label { display:block; margin: 10px 0 6px; }
        input { width:100%; padding: 10px; border:1px solid #ddd; border-radius:6px; }
        button { width:100%; margin-top: 15px; padding: 10px; border:none; border-radius:6px; background:#0d6efd; color:#fff; font-weight:700; cursor:pointer; }
        a { display:block; margin-top: 12px; text-align:center; color:#0d6efd; text-decoration:none; }
    </style>
</head>
<body>
<div class="container">
    <h2>Connexion</h2>

    <?php if (!empty($error)): ?>
        <div class="error"><?= esc($error) ?></div>
    <?php endif; ?>

    <form method="post" action="/login">
        <label for="numero_utilisateur">Numéro de téléphone</label>
        <input
            id="numero_utilisateur"
            name="numero_utilisateur"
            type="text"
            inputmode="tel"
            placeholder="ex: 0331234567"
            required
        />

        <button type="submit">Se connecter</button>
    </form>

    <a href="/">Voir la liste des utilisateurs</a>
</div>
</body>
</html>

