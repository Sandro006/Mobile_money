# 📋 TODO — Projet e-Money (Mobile Money)

> **Application de transfert financier et gestion d'opérations monétaires**
> CodeIgniter 4 + SQLite 3 + Bootstrap 5

---

## [x] ÉTAPE 0 : Configuration de l'environnement

- [x] Installation de CodeIgniter 4
- [x] Configuration base SQLite
- [x] Configuration des assets Bootstrap 5 locaux (`public/assets/`)

---

## [x] ÉTAPE 1 : Conception de base de données

- [x] Compréhension du cahier des charges
- [x] MCD (Modèle Conceptuel de Données)
- [x] Syntaxe SQL (création des tables)
- [x] Rédaction des données de test
- [x] Création des vues SQL :
  - [x] `vue_historique_operations` — vue unifiée dépôts/retraits/transferts
  - [x] `vue_gains_operations` — vue des gains avec classification interne/inter-opérateur

### 📊 Tables créées

| Table | Statut | Description |
|-------|--------|-------------|
| `Operateur` | [x] | Opérateurs de mobile money (Orange, Telma) |
| `prefixe` | [x] | Préfixes de numéros par opérateur |
| `operation` | [x] | Types d'opérations (Transfert, Dépôt, Retrait) |
| `utilisateur` | [x] | Utilisateurs avec solde et opérateur rattaché |
| `transfert` | [x] | Transactions de transfert |
| `depot` | [x] | Transactions de dépôt |
| `retrait` | [x] | Transactions de retrait |
| `bareme` | [x] | Barème de tranches de montants |
| `frais` | [x] | Montants des frais par barème (avec historique) |
| `type_gain` | [x] | Types de gains (Interne, Inter-Opérateur) |
| `gain` | [x] | Gains perçus par opérateur |
| `configuration_interop` | [x] | Taux de commission inter-opérateur (avec historique) |

---

## [x] ÉTAPE 2 : Rédaction (Couche Métier)

- [x] Création des Models (11 modèles)
- [x] Création des Services (intégrés dans les Controllers/Models)

---

## [x] ÉTAPE 3 : Backend — Controllers & API

### 🔐 Authentification

| Fonctionnalité | Statut | Controller |
|----------------|--------|------------|
| Login client par numéro de téléphone | [x] | `LoginController` |
| Logout client | [x] | `LoginController::logout()` |
| Login opérateur (nom + mot de passe) | [x] | `OperateurAuthController::login()` |
| Logout opérateur | [x] | `OperateurAuthController::logout()` |
| Session opérateur (Prefixes, Barèmes, Gains...) | [x] | Via session `operateur_id` |

### 👤 Espace Client

| Fonctionnalité | Statut | Fichiers clés |
|----------------|--------|---------------|
| Dashboard client (solde + 5 dernières ops) | [x] | `ClientController::index()` |
| Historique complet avec recherche JS | [x] | `ClientController::historique()` |
| Opération de Dépôt | [x] | `OperationController::depot()` |
| Opération de Retrait (avec/sans frais inclus) | [x] | `OperationController::retrait()` |
| Opération de Transfert (simple & groupé) | [x] | `OperationController::transfert()` |
| Calcul frais retrait en temps réel (AJAX) | [x] | `ApiController::calculerFraisRetrait()` |
| Calcul frais transfert en temps réel (AJAX) | [x] | `ApiController::calculerFraisTransfert()` |
| Reçu de transaction (modale) | [x] | Intégré dans `home.php` |

### 💼 Espace Opérateur

| Fonctionnalité | Statut | Controller |
|----------------|--------|------------|
| CRUD Préfixes | [x] | `PrefixeController` |
| Gestion Barèmes de frais | [x] | `BaremeController` |
| Configuration Commission Inter-Opérateur | [x] | `CommissionController` |
| Tableau des Gains (internes + commissions) | [x] | `GainController` |
| Situation des comptes utilisateurs (par opérateur) | [x] | `SituationController` |
| Détail transactions d'un utilisateur | [x] | `SituationController::detail()` |
| Compensation inter-opérateurs | [x] | `CompensationController` |

---

## [x] ÉTAPE 4 : Frontend — Vues

### 🏠 Pages Publiques

| Vue | Statut | Route |
|-----|--------|-------|
| Page d'accueil (choix Client/Opérateur) | [x] | `/` |
| Login client | [x] | `/login` |
| Login opérateur | [x] | `/operateur/auth` |

### 👤 Pages Client

| Vue | Statut | Route |
|-----|--------|-------|
| Dashboard client (solde + profil + 5 ops) | [x] | `/client` |
| Formulaire de dépôt | [x] | `/operation/page-depot` |
| Formulaire de retrait (avec sélecteur opérateur) | [x] | `/operation/page-retrait` |
| Formulaire de transfert (simple/multiple + récap) | [x] | `/operation/page-transfert` |
| Historique complet des transactions | [x] | `/client/historique` |

### 💼 Pages Opérateur

| Vue | Statut | Route |
|-----|--------|-------|
| Liste des préfixes | [x] | `/prefixe` |
| Création d'un préfixe | [x] | `/prefixe/create` |
| Modification d'un préfixe | [x] | `/prefixe/edit/{id}` |
| Liste des barèmes | [x] | `/bareme` |
| Édition d'un barème | [x] | `/bareme/edit/{id}` |
| Configuration commission inter-op | [x] | `/commission` |
| Tableau des gains | [x] | `/gain` |
| Situation des comptes utilisateurs | [x] | `/situation` |
| Détail transactions d'un utilisateur | [x] | `/situation/detail/{id}` |
| Compensation inter-opérateurs | [x] | `/compensation` |

### 🧩 Layouts

| Vue | Statut | Description |
|-----|--------|-------------|
| `layouts/navbar.php` | [x] | Barre de navigation opérateur |
| `layouts/main.php` | [x] | Layout principal opérateur |
| `layouts/navbar_cli.php` | [x] | Barre de navigation client |

---

## [x] ÉTAPE 5 : Fonctionnalités techniques avancées

### 🧮 Calcul dynamique des frais

| Fonctionnalité | Statut |
|----------------|--------|
| Calcul frais bruts par tranche (barème) | [x] |
| Commission inter-opérateur (taux %) | [x] |
| Mode "frais inclus" (montant net reçu) | [x] |
| Mode "frais déduits" (montant brut - frais) | [x] |
| Transfert multiple (groupé) avec calcul par destinataire | [x] |
| Récapitulatif dynamique AJAX | [x] |

### 🔒 Sécurité & Intégrité

| Fonctionnalité | Statut |
|----------------|--------|
| Transactions SQL atomiques (`transStart/transComplete`) | [x] |
| `PRAGMA foreign_keys = ON` | [x] |
| Échappement XSS via `esc()` | [x] |
| Validation des entrées formulaire | [x] |
| Protection auto-virement (transfert) | [x] |
| Contrôle de solde avant opération | [x] |
| Session utilisateur protégée | [x] |

---

## 📦 Structure du projet

```
d:/Mobile_money/
├── app/
│   ├── Config/
│   │   ├── Database.php          # Configuration SQLite
│   │   └── Routes.php            # Toutes les routes
│   ├── Controllers/
│   │   ├── ApiController.php     # API AJAX (calcul frais)
│   │   ├── BaremeController.php  # Gestion barèmes
│   │   ├── ClientController.php  # Espace client
│   │   ├── CommissionController.php # Config commission
│   │   ├── CompensationController.php # Compensation inter-op
│   │   ├── GainController.php    # Tableau des gains
│   │   ├── Home.php              # Page d'accueil
│   │   ├── LoginController.php   # Login client
│   │   ├── OperateurAuthController.php # Login opérateur
│   │   ├── OperationController.php # Opérations financières
│   │   ├── PrefixeController.php # CRUD préfixes
│   │   └── SituationController.php # Situation comptes
│   ├── Models/
│   │   ├── BaremeModel.php
│   │   ├── ConfigurationInteropModel.php
│   │   ├── DepotModel.php
│   │   ├── FraisModel.php
│   │   ├── GainModel.php
│   │   ├── OperateurModel.php
│   │   ├── OperationModel.php
│   │   ├── PrefixeModel.php
│   │   ├── RetraitModel.php
│   │   ├── TransfertModel.php
│   │   ├── TypeGainModel.php
│   │   └── UtilisateurModel.php
│   └── Views/
│       ├── auth/login.php
│       ├── Client/{home,historique,index}.php
│       ├── layouts/{main.php,navbar.php,navbar_cli.php}
│       ├── operateur/{auth.php, bareme/, commission/, compensation/, gain/, prefixe/, situation/}
│       └── operation/{depot.php,retrait.php,transfert.php}
├── base.sql                    # Script complet DB
├── migration_commission_prefixe.sql
├── sup.sql                     # Script de suppression
├── Mobile.db                   # Base SQLite
├── composer.json
└── README.md
```

---

## 🔧 Architecture technique

### 📐 Modèle MVC
- **Contrôleurs légers** : validation + redirection
- **Models métier** : logique financière (transactions SQL, calcul frais)
- **Vues Bootstrap 5** : responsive, formulaires avec récapitulatifs AJAX

### 🗄️ Base de données SQLite
- Base de données fichier unique : `Mobile.db` / `writable/database/Mobile.db`
- Relations avec clés étrangères (`PRAGMA foreign_keys = ON`)
- Vues SQL pour historique unifié et reporting gains
- Historique des modifications (frais, commissions)

### 🔄 Flux opérationnels

1. **Dépôt** → Crédit solde utilisateur → Insertion dans `depot`
2. **Retrait** → Calcul frais bruts → Calcul commission inter-op (si interop) → Débit solde → Insertion `retrait` → Insertion `gain` (interne + commission)
3. **Transfert simple** → Validation destinataire → Calcul frais → Débit envoyeur → Crédit récepteur → Insertion `transfert` + `gain`
4. **Transfert groupé** → Division montant global / n destinataires → Validation de chaque destinataire → Exécution atomique de tous les virements

---

## 🚀 Installation & Démarrage

```bash
# 1. Démarrer le serveur CodeIgniter
php spark serve

# 2. Accéder à l'application
# http://localhost:8080

# Comptes de test :
# Client : 0331234567 (Jean Rabe) / 0341234568 (Rabe Rado)
# Opérateur : Orange / orange123 | Telma / telma123
```

---

## 📌 Routes disponibles

| Méthode | Route | Action |
|---------|-------|--------|
| GET | `/` | Accueil |
| GET/POST | `/login` | Login client |
| GET | `/logout` | Logout client |
| GET | `/client` | Dashboard client |
| GET | `/client/historique` | Historique client |
| GET | `/operation/page-depot` | Formulaire dépôt |
| POST | `/operation/depot` | Traitement dépôt |
| GET | `/operation/page-retrait` | Formulaire retrait |
| POST | `/operation/retrait` | Traitement retrait |
| GET | `/operation/page-transfert` | Formulaire transfert |
| POST | `/operation/transfert` | Traitement transfert |
| GET | `/operateur/auth` | Login opérateur |
| POST | `/operateur/auth` | Traitement login opérateur |
| GET | `/operateur/logout` | Logout opérateur |
| GET | `/prefixe` | Liste préfixes |
| GET/POST | `/prefixe/create` | Créer préfixe |
| GET/POST | `/prefixe/edit/{id}` | Modifier préfixe |
| POST | `/prefixe/delete/{id}` | Supprimer préfixe |
| GET | `/bareme` | Liste barèmes |
| GET/POST | `/bareme/edit/{id}` | Modifier barème |
| GET/POST | `/commission` | Config commission |
| POST | `/commission/update` | Mettre à jour commission |
| GET | `/gain` | Tableau des gains |
| GET | `/situation` | Situation comptes |
| GET | `/situation/detail/{id}` | Détail transactions |
| GET | `/compensation` | Compensation inter-op |
| POST | `/api/calculer-frais-retrait` | API calcul frais retrait |
| POST | `/api/calculer-frais-transfert` | API calcul frais transfert |

---

**Statut global : 100% terminé [x]** — Version 1 du projet e-Money complète et fonctionnelle.



GIT_AUTHOR_DATE="2026-07-20T17:23:00" GIT_COMMITTER_DATE="2026-07-20T17:23:00" git commit -m "commit de la mort"
