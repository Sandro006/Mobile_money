

PRAGMA foreign_keys = ON;

CREATE TABLE Operateur(
    id INT PRIMARY KEY,
    libelle VARCHAR(50),
    nom VARCHAR(50),
    mdp VARCHAR(100),
    UNIQUE(nom)
);

CREATE TABLE prefixe (
    id_prefixe INT PRIMARY KEY,
    num_prefixe VARCHAR(10),
    id_operateur INT,
    FOREIGN KEY (id_operateur) REFERENCES Operateur(id)
);

CREATE TABLE operation (
    id_operation INT PRIMARY KEY,
    description_operation VARCHAR(100)
);

-- Si vous recréez la table, ajoutez la clé étrangère id_operateur :
CREATE TABLE utilisateur (
    id_utilisateur INT PRIMARY KEY,
    nom_utilisateur VARCHAR(50),
    numero_utilisateur VARCHAR(20),
    id_prefixe INT,
    id_operateur INT, -- Ajout essentiel pour identifier l'opérateur du client
    solde_utilisateur DECIMAL(10,2),
    FOREIGN KEY (id_prefixe) REFERENCES prefixe(id_prefixe),
    FOREIGN KEY (id_operateur) REFERENCES Operateur(id)
);


CREATE TABLE transfert (
    id_transfert INT PRIMARY KEY,
    id_operation INT DEFAULT 1,
    envoyeur_transfert INT,
    recepteur_transfert INT,
    montant_transfert DECIMAL(10,2),
    date_transfert DATETIME,
    lieu_transfert VARCHAR(100),
    FOREIGN KEY (id_operation) REFERENCES operation(id_operation),
    FOREIGN KEY (envoyeur_transfert) REFERENCES utilisateur(id_utilisateur),
    FOREIGN KEY (recepteur_transfert) REFERENCES utilisateur(id_utilisateur)
);

CREATE TABLE depot (
    id_depot INT PRIMARY KEY,
    id_operation INT DEFAULT 2,
    id_utilisateur_depot INT,
    montant_depot DECIMAL(10,2),
    date_depot DATETIME,
    lieu_depot VARCHAR(100),
    FOREIGN KEY (id_operation) REFERENCES operation(id_operation),
    FOREIGN KEY (id_utilisateur_depot) REFERENCES utilisateur(id_utilisateur)
);

CREATE TABLE retrait (
    id_retrait INT PRIMARY KEY,
    id_operation INT DEFAULT 3,
    id_utilisateur_retrait INT,
    montant_retrait DECIMAL(10,2),
    date_retrait DATETIME,
    lieu_retrait VARCHAR(100),
    FOREIGN KEY (id_operation) REFERENCES operation(id_operation),
    FOREIGN KEY (id_utilisateur_retrait) REFERENCES utilisateur(id_utilisateur)
);

CREATE TABLE bareme (
    id_bareme INT PRIMARY KEY,
    min_bareme DECIMAL(10,2),
    max_bareme DECIMAL(10,2)
);

CREATE TABLE frais (
    id_frais INT PRIMARY KEY,
    id_bareme INT,
    montant_frais DECIMAL(10,2),
    date_frais DATETIME,
    FOREIGN KEY (id_bareme) REFERENCES bareme(id_bareme)
);

CREATE TABLE type_gain(
    id INT PRIMARY KEY,
    libelle VARCHAR(50)  -- 'Interne' ou 'Inter-Operateur'
);
CREATE TABLE gain (
    id_gain INT PRIMARY KEY,
    id_operation INT,
    id_transfert INT NULL,
    id_retrait INT NULL,
    montant_gain DECIMAL(10,2),
    id_type_gain INT NOT NULL,
    id_operateur_concerne INT NULL, -- Utile pour savoir quel opérateur a généré le gain
    date_gain DATETIME,
    FOREIGN KEY (id_operation) REFERENCES operation(id_operation),
    FOREIGN KEY (id_transfert) REFERENCES transfert(id_transfert),
    FOREIGN KEY (id_retrait) REFERENCES retrait(id_retrait),
    FOREIGN KEY (id_type_gain) REFERENCES type_gain(id),
    FOREIGN KEY (id_operateur_concerne) REFERENCES Operateur(id)
);

CREATE TABLE configuration_interop (
    id_config INT PRIMARY KEY,
    id_operateur INT,
    taux_commission_autre_operateur DECIMAL(5,2) DEFAULT 0.00 -- Stocke le %, ex: 2.50 pour 2.5%
);


-- ================================= VUE ========================================
CREATE VIEW vue_historique_operations AS

-- 1. Récupération des Dépôts
SELECT 
    d.id_depot AS id_transaction,
    d.date_depot AS date_operation,
    'Dépôt' AS type_operation,
    d.id_utilisateur_depot AS id_utilisateur,
    d.montant_depot AS montant,
    0.00 AS frais, -- Pas de frais sur les dépôts d'après le barème
    d.lieu_depot AS lieu
FROM depot d

UNION ALL

-- 2. Récupération des Retraits
SELECT 
    r.id_retrait AS id_transaction,
    r.date_retrait AS date_operation,
    'Retrait' AS type_operation,
    r.id_utilisateur_retrait AS id_utilisateur,
    r.montant_retrait AS montant,
    COALESCE(g.montant_gain, 0.00) AS frais, -- Récupération des frais via la table gain
    r.lieu_retrait AS lieu
FROM retrait r
LEFT JOIN gain g ON r.id_retrait = g.id_retrait

UNION ALL

-- 3. Récupération des Transferts (Côté Envoyeur : Sortie d'argent)
SELECT 
    t.id_transfert AS id_transaction,
    t.date_transfert AS date_operation,
    'Transfert Envoyé' AS type_operation,
    t.envoyeur_transfert AS id_utilisateur,
    t.montant_transfert AS montant,
    COALESCE(g.montant_gain, 0.00) AS frais, -- L'envoyeur paye les frais du transfert
    t.lieu_transfert AS lieu
FROM transfert t
LEFT JOIN gain g ON t.id_transfert = g.id_transfert

UNION ALL

-- 4. Récupération des Transferts (Côté Récepteur : Entrée d'argent)
SELECT 
    t.id_transfert AS id_transaction,
    t.date_transfert AS date_operation,
    'Transfert Reçu' AS type_operation,
    t.recepteur_transfert AS id_utilisateur,
    t.montant_transfert AS montant,
    0.00 AS frais, -- Le récepteur ne paye aucun frais
    t.lieu_transfert AS lieu
FROM transfert t;

-- =============================== DONNÉES DE TEST ===============================
INSERT INTO Operateur (id, libelle, nom, mdp) VALUES
(1, 'Orange Madagascar', 'Orange', 'orange123'),
(2, 'Telma Madagascar', 'Telma', 'telma123');

INSERT INTO operation (id_operation, description_operation) VALUES
(1, 'Transfert'),
(2, 'Dépôt'),
(3, 'Retrait');

-- Préfixes d'opérateurs
INSERT INTO prefixe (id_prefixe, num_prefixe) VALUES
(1, '033'),
(2, '037');
UPDATE prefixe SET id_operateur = 1 WHERE id_prefixe = 1;
UPDATE prefixe SET id_operateur = 1 WHERE id_prefixe = 1;

INSERT INTO prefixe(id_prefixe, num_prefixe, id_operateur) VALUES
(3, '034',2),
(4,'038',2)
-- Insertion des types de gains
INSERT INTO type_gain(id, libelle) VALUES 
(1, 'Interne'),
(2, 'Inter-Operateur');


INSERT INTO configuration_interop (id_config, id_operateur, taux_commission_autre_operateur) VALUES
(1, 1, 2.00),
(2, 2, 2.50);

-- Insertion du barème (Table bareme)
INSERT INTO bareme (id_bareme, min_bareme, max_bareme) VALUES
(1, 100.00, 1000.00),
(2, 1001.00, 5000.00),
(3, 5001.00, 10000.00),
(4, 10001.00, 25000.00),
(5, 25001.00, 50000.00),
(6, 50001.00, 100000.00),
(7, 100001.00, 250000.00),
(8, 250001.00, 500000.00),
(9, 500001.00, 1000000.00),
(10, 1000001.00, 2000000.00);

-- Insertion des frais associés (Table frais avec DATETIME('now'))
INSERT INTO frais (id_frais, id_bareme, montant_frais, date_frais) VALUES
(1, 1, 50.00, DATETIME('now')),
(2, 2, 50.00, DATETIME('now')),
(3, 3, 100.00, DATETIME('now')),
(4, 4, 200.00, DATETIME('now')),
(5, 5, 400.00, DATETIME('now')),
(6, 6, 800.00, DATETIME('now')),
(7, 7, 1500.00, DATETIME('now')),
(8, 8, 1500.00, DATETIME('now')),
(9, 9, 2500.00, DATETIME('now')),
(10, 10, 3000.00, DATETIME('now'));

-- Utilisateurs de test
INSERT INTO utilisateur (id_utilisateur, nom_utilisateur, numero_utilisateur, id_prefixe, solde_utilisateur) VALUES
(1, 'Jean Rabe', '0331234567', 1, 50000.00),
(2, 'Rabe Rado', '0331234568', 2, 50000.00);

-- Exemple de dépôt

