# Ce sera un système qui va simuler un opérateur de mobile money

---

# Version 1 :

### ETAPE 0 : CONFIGURATION DE L'ENVIRONNEMENT (10 min)

- CodeIgniter 4
- Configuration base sqlite
- 
### ETAPE 1 : CONCEPTION DE BASE DE DONNÉES (30 ~ 40 min)

- [x] Comprehension (Binome)
- [x] MCD (IVO)
- [x] Syntaxe (IVO)
- [x] Redation des donneés de test (Sandy)


### ETAPE 2 : Redaction 

- [x] Creation des Entity
- [x] Creation des Services

### ETAPE 3: BACKEND

- [x] Créer les CRUD Opérateurs (prefixe)()
- [x] Créer les CRUD Types d'opérations
- [x] Créer le CRUD Barèmes de frais
- [x] Créer le validateur de numéro
### ETAPE 4: BACKOFFICE
# 📱 Suivi du Projet - Mobile Money (Version 1)

Ce document récapitule l'état d'avancement des fonctionnalités du système de transaction financière, réparti entre la logique Client et l'interface Opérateur.

---

##  Côté Client (Fait par : Sandro)
*Toutes les fonctionnalités requises pour l'espace utilisateur ont été implémentées et configurées avec SQLite et CodeIgniter 4.*

- [x] **Connexion Simplifiée**
  - [x] Login automatique par numéro de téléphone unique.
  - [x] Pas d'inscription préalable obligatoire en front-end.
- [x] **Consultation du Compte**
  - [x] Visualisation en temps réel du solde disponible (formaté en Ariary Ar).
  - [x] Affichage dynamique du profil (Nom et Numéro de l'utilisateur).
- [x] **Gestion des Opérations Métier**
  - [x] **Dépôt automatique** : Alimentation instantanée du solde client via `DepotModel` sans frais appliqués.
  - [x] **Retrait automatique** : Déduction du montant, calcul en temps réel des frais selon la grille SQLite, et alimentation de la table `gain`.
  - [x] **Transfert de compte à compte** : Validation du numéro destinataire, blocage de l'auto-virement, déduction du montant + frais à l'envoyeur, et crédit du montant net au récepteur.
- [x] **Suivi et Visibilité**
  - [x] Intégration d'un tableau des 5 dernières opérations détaillées sur la page d'accueil.
  - [x] Création d'une `VIEW` SQLite globale unifiant les flux de 3 tables (`depot`, `retrait`, `transfert`).
  - [x] Page d'historique complète avec moteur de recherche instantané en JavaScript pour filtrer les transactions.
  - [x] Intégration d'une barre de navigation (Navbar) épurée et d'assets locaux (Bootstrap CSS/JS) pour le routage.

---

##  Côté Opérateur (Fais par : Ivo)
*Ces fonctionnalités représentent le panneau d'administration pour la gestion globale de la plateforme.*

- [x] **Configuration du Réseau Émetteur**
  - [x] Interface de gestion et d'activation des préfixes valides de l'opérateur (ex: `032`, `033`, `034`, `037`).
  - [x] Bloqueur de sécurité bloquant l'accès ou la création de comptes avec un préfixe hors réseau.
- [x] **Gestion de la Politique Tarifaire**
  - [x] Écran de configuration des types d'opérations de base (Dépôt, Retrait, Transfert).
  - [x] Interface d'ajustement dynamique de la matrice des barèmes de frais par tranche de montant (Tranches modifiables en base de données).
- [x] **Suivi Analytique et Reporting**
  - [x] **Situation des Gains** : Tableau de bord affichant le cumul des commissions perçues via les frais de retraits et de transferts.
  - [x] **Situation des Comptes** : Vue globale sur l'ensemble du parc d'utilisateurs, incluant la somme totale des soldes en circulation.

---

## Architecture Technique Validée
* **Base de données** : SQLite 3 avec support des contraintes d'intégrité relationnelle activé (`PRAGMA foreign_keys = ON`).
* **Design** : Intégration locale de Bootstrap 5 dans le répertoire `public/assets/`.
