# 📱 e-Money - Application de Mobile Money (Version 1)

Une application web de transfert financier et de gestion des opérations monétaires (Dépôt, Retrait, Transfert) développée avec **CodeIgniter 4** et **SQLite 3**. L'application intègre un moteur de calcul dynamique des frais basé sur un barème de tranches modifiable.

---

## 🚀 Fonctionnalités Clés

### 👤 Espace Client (Développé par Sandro)
* **Connexion instantanée** : Authentification rapide via le numéro de téléphone (pas d'inscription préalable).
* **Tableau de bord épuré** : Visualisation du solde en temps réel (Ariary Ar) et affichage des 5 dernières opérations.
* **Dépôt automatisé** : Crédit immédiat du solde utilisateur sans frais.
* **Retrait avec frais** : Calcul automatique des frais réglementaires selon la tranche du montant, prélèvement sur le solde et enregistrement du gain opérateur.
* **Transfert de compte à compte** : Validation du numéro destinataire, blocage de l'auto-virement, débit de l'envoyeur (montant + frais) et crédit net du récepteur.
* **Historique centralisé** : Page dédiée affichant l'intégralité des flux grâce à une `VIEW` SQLite, incluant un filtre de recherche instantané en JavaScript.

### 💼 Espace Opérateur / Admin (Développé par Ivo)
* **Gestion des préfixes** : Configuration et filtrage des préfixes valides sur le réseau (ex: 032, 033, 034, 037).
* **Matrice des frais** : Ajustement dynamique des tranches de montants et des frais associés.
* **Suivi analytique** : Vue d'ensemble sur les gains générés (commissions) et la situation des soldes globaux des clients.

---

## 🛠️ Spécifications Techniques

* **Framework Backend** : CodeIgniter 4.x
* **Base de données** : SQLite 3 (Légère, embarquée et sans configuration de serveur).
* **Interface Utilisateur** : Bootstrap 5 + Bootstrap Icons (Intégrés localement pour un fonctionnement hors-ligne).
* **Architecture** : MVC respectant le principe *"Fat Models, Skinny Controllers"*.

---

## 📦 Installation et Lancement

### 1. Prérequis
* PHP 8.1 ou supérieur (avec les extensions `php-sqlite3`, `php-intl` et `php-mbstring` activées dans votre `php.ini`).
* SQLite 3.

### 2. Clonage et Configuration
Déplacez le projet dans votre répertoire de travail.

Ouvrez le fichier de configuration de la base de données **`app/Config/Database.php`** et assurez-vous que le chemin pointe vers votre fichier SQLite (à la racine ou dans `writable/`) :

```php
public array $default = [
    'DSN'      => '',
    'hostname' => '',
    'username' => '',
    'password' => '',
    'database' => 'Mobile.db', // Nom de votre fichier SQLite à la racine
    'DBDriver' => 'SQLite3',
    'DBPrefix' => '',
    // ...
];
```

### 3. Initialisation de la Base de Données
Lancez votre terminal SQLite sur le fichier de votre base de données :
```bash
sqlite3 Mobile.db
```
Exécutez la commande d'activation des clés étrangères, puis injectez la structure des tables et le barème des frais :
```sql
PRAGMA foreign_keys = ON;

-- (Copiez-collez ici le script SQL de création des tables et des données de test)
```

### 4. Structure des Dossiers pour les Assets
Vérifiez que vos fichiers CSS et JS de Bootstrap sont correctement placés pour le chargement local :
```text
public/
└── assets/
    ├── css/
    │   ├── bootstrap.min.css
    │   └── bootstrap-icons.css
    └── js/
        └── bootstrap.bundle.min.js
```

### 5. Démarrage du Serveur Local
À la racine de votre projet, exécutez la commande native de CodeIgniter :
```bash
php spark serve
```
L'application est maintenant accessible sur **`http://localhost:8080`**.

---

## 🔒 Sécurité et Intégrité des Données
* **Transactions SQL** : Toutes les opérations financières complexes (Retrait et Transfert) utilisent `transStart()` et `transComplete()` pour garantir qu'aucune modification de solde n'ait lieu si une étape de l'écriture échoue.
* **Contraintes SQLite** : Validation stricte via `PRAGMA foreign_keys = ON` pour interdire l'insertion de données orphelines.
* **Protection XSS** : Échappement systématique des données dynamiques affichées à l'écran via la fonction `esc()`.
