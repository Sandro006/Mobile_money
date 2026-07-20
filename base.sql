PRAGMA foreign_keys = ON;

CREATE TABLE Operateur (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    libelle VARCHAR(50),
    nom VARCHAR(50),
    mdp VARCHAR(100),
    UNIQUE(nom)
);

CREATE TABLE prefixe (
    id_prefixe INTEGER PRIMARY KEY AUTOINCREMENT,
    num_prefixe VARCHAR(10),
    id_operateur INT,
    FOREIGN KEY (id_operateur) REFERENCES Operateur(id)
);

CREATE TABLE operation (
    id_operation INTEGER PRIMARY KEY AUTOINCREMENT,
    description_operation VARCHAR(100)
);

CREATE TABLE utilisateur (
    id_utilisateur INTEGER PRIMARY KEY AUTOINCREMENT,
    nom_utilisateur VARCHAR(50),
    numero_utilisateur VARCHAR(20),
    id_operateur INT, 
    solde_utilisateur DECIMAL(10,2),
    FOREIGN KEY (id_operateur) REFERENCES Operateur(id)
);

CREATE TABLE transfert (
    id_transfert INTEGER PRIMARY KEY AUTOINCREMENT,
    id_operation INT DEFAULT 1,
    envoyeur_transfert INT,
    recepteur_transfert INT,
    montant_transfert DECIMAL(10,2),
    date_transfert DATETIME DEFAULT CURRENT_TIMESTAMP, -- Optionnel : ajout d'une date auto
    lieu_transfert VARCHAR(100),
    FOREIGN KEY (id_operation) REFERENCES operation(id_operation),
    FOREIGN KEY (envoyeur_transfert) REFERENCES utilisateur(id_utilisateur),
    FOREIGN KEY (recepteur_transfert) REFERENCES utilisateur(id_utilisateur)
);

CREATE TABLE depot (
    id_depot INTEGER PRIMARY KEY AUTOINCREMENT,
    id_operation INT DEFAULT 2,
    id_utilisateur_depot INT,
    montant_depot DECIMAL(10,2),
    date_depot DATETIME DEFAULT CURRENT_TIMESTAMP,     -- Optionnel : ajout d'une date auto
    lieu_depot VARCHAR(100),
    FOREIGN KEY (id_operation) REFERENCES operation(id_operation),
    FOREIGN KEY (id_utilisateur_depot) REFERENCES utilisateur(id_utilisateur)
);

CREATE TABLE retrait (
    id_retrait INTEGER PRIMARY KEY AUTOINCREMENT,
    id_operation INT DEFAULT 3,
    id_utilisateur_retrait INT,
    montant_retrait DECIMAL(10,2),
    date_retrait DATETIME DEFAULT CURRENT_TIMESTAMP,   -- Optionnel : ajout d'une date auto
    lieu_retrait VARCHAR(100),
    FOREIGN KEY (id_operation) REFERENCES operation(id_operation),
    FOREIGN KEY (id_utilisateur_retrait) REFERENCES utilisateur(id_utilisateur)
);

CREATE TABLE bareme (
    id_bareme INTEGER PRIMARY KEY AUTOINCREMENT,
    min_bareme DECIMAL(10,2),
    max_bareme DECIMAL(10,2)
);

CREATE TABLE frais (
    id_frais INTEGER PRIMARY KEY AUTOINCREMENT,
    id_bareme INT,
    montant_frais DECIMAL(10,2),
    date_frais DATETIME DEFAULT CURRENT_TIMESTAMP,     -- Optionnel : ajout d'une date auto
    FOREIGN KEY (id_bareme) REFERENCES bareme(id_bareme)
);

CREATE TABLE type_gain (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    libelle VARCHAR(50)  
);

CREATE TABLE gain (
    id_gain INTEGER PRIMARY KEY AUTOINCREMENT,
    id_operation INT,
    id_transfert INT NULL,
    id_retrait INT NULL,
    montant_gain DECIMAL(10,2),
    id_type_gain INT NOT NULL,
    id_operateur_concerne INT NULL, 
    date_gain DATETIME DEFAULT CURRENT_TIMESTAMP,       -- Optionnel : ajout d'une date auto
    FOREIGN KEY (id_operation) REFERENCES operation(id_operation),
    FOREIGN KEY (id_transfert) REFERENCES transfert(id_transfert),
    FOREIGN KEY (id_retrait) REFERENCES retrait(id_retrait),
    FOREIGN KEY (id_type_gain) REFERENCES type_gain(id),
    FOREIGN KEY (id_operateur_concerne) REFERENCES Operateur(id)
);

CREATE TABLE configuration_interop (
    id_config INTEGER PRIMARY KEY AUTOINCREMENT,
    id_operateur INT,
    taux_commission_autre_operateur DECIMAL(5,2) DEFAULT 0.00, 
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP 
);


-- ================================= VUE ========================================
DROP VIEW IF EXISTS vue_historique_operations;

CREATE VIEW vue_historique_operations AS

-- 1. Récupération des Dépôts (Inchangé)
SELECT 
    d.id_depot AS id_transaction,
    d.date_depot AS date_operation,
    'Dépôt' AS type_operation,
    d.id_utilisateur_depot AS id_utilisateur,
    d.montant_depot AS montant,
    0.00 AS frais,
    d.lieu_depot AS lieu
FROM depot d

UNION ALL

-- 2. Récupération des Retraits (CORRIGÉ : Somme des gains par retrait)
SELECT 
    r.id_retrait AS id_transaction,
    r.date_retrait AS date_operation,
    'Retrait' AS type_operation,
    r.id_utilisateur_retrait AS id_utilisateur,
    r.montant_retrait AS montant,
    COALESCE((SELECT SUM(g.montant_gain) FROM gain g WHERE g.id_retrait = r.id_retrait), 0.00) AS frais,
    r.lieu_retrait AS lieu
FROM retrait r

UNION ALL

-- 3. Récupération des Transferts - Côté Envoyeur (CORRIGÉ : Somme des gains par transfert)
SELECT 
    t.id_transfert AS id_transaction,
    t.date_transfert AS date_operation,
    'Transfert Envoyé' AS type_operation,
    t.envoyeur_transfert AS id_utilisateur,
    t.montant_transfert AS montant,
    COALESCE((SELECT SUM(g.montant_gain) FROM gain g WHERE g.id_transfert = t.id_transfert), 0.00) AS frais,
    t.lieu_transfert AS lieu
FROM transfert t

UNION ALL

-- 4. Récupération des Transferts - Côté Récepteur (Inchangé)
SELECT 
    t.id_transfert AS id_transaction,
    t.date_transfert AS date_operation,
    'Transfert Reçu' AS type_operation,
    t.recepteur_transfert AS id_utilisateur,
    t.montant_transfert AS montant,
    0.00 AS frais,
    t.lieu_transfert AS lieu
FROM transfert t;

-- ============================================================
-- VUE : Gains / opérations (transferts, retraits, dépôts)
--       avec classification interne / inter-opérateur et frais
-- ============================================================
DROP VIEW IF EXISTS vue_gains_operations;

CREATE VIEW vue_gains_operations AS

-- Transferts
SELECT 
    t.id_transfert AS id,
    'transfert' AS type,
    t.date_transfert AS date,
    t.lieu_transfert AS lieu,
    t.montant_transfert AS montant,
    ue.id_operateur AS id_operateur_envoyeur,
    ur.id_operateur AS id_operateur_recepteur,
    COALESCE((
        SELECT montant_frais FROM frais
        WHERE id_bareme = (
            SELECT id_bareme FROM bareme
            WHERE min_bareme <= t.montant_transfert AND max_bareme >= t.montant_transfert
            LIMIT 1
        )
        ORDER BY date_frais DESC LIMIT 1
    ), 0) AS frais_base,
    (CASE WHEN ue.id_operateur != ur.id_operateur THEN 1 ELSE 0 END) AS est_externe,
    COALESCE((
        SELECT taux_commission_autre_operateur FROM configuration_interop
        WHERE id_operateur = ue.id_operateur
        ORDER BY id_config DESC LIMIT 1
    ), 0) AS taux_commission
FROM transfert t
JOIN utilisateur ue ON ue.id_utilisateur = t.envoyeur_transfert
JOIN utilisateur ur ON ur.id_utilisateur = t.recepteur_transfert

UNION ALL

-- Retraits
SELECT 
    r.id_retrait AS id,
    'retrait' AS type,
    r.date_retrait AS date,
    r.lieu_retrait AS lieu,
    r.montant_retrait AS montant,
    NULL AS id_operateur_envoyeur,
    NULL AS id_operateur_recepteur,
    COALESCE((
        SELECT montant_frais FROM frais
        WHERE id_bareme = (
            SELECT id_bareme FROM bareme
            WHERE min_bareme <= r.montant_retrait AND max_bareme >= r.montant_retrait
            LIMIT 1
        )
        ORDER BY date_frais DESC LIMIT 1
    ), 0) AS frais_base,
    0 AS est_externe,
    0 AS taux_commission
FROM retrait r

UNION ALL

-- Dépôts
SELECT 
    d.id_depot AS id,
    'depot' AS type,
    d.date_depot AS date,
    d.lieu_depot AS lieu,
    d.montant_depot AS montant,
    NULL AS id_operateur_envoyeur,
    NULL AS id_operateur_recepteur,
    0 AS frais_base,
    0 AS est_externe,
    0 AS taux_commission
FROM depot d;

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

-- Correction : Correction de la double ligne inutile
UPDATE prefixe SET id_operateur = 1 WHERE id_prefixe = 1;
UPDATE prefixe SET id_operateur = 1 WHERE id_prefixe = 2; -- Supposé pour le préfixe 2

INSERT INTO prefixe(id_prefixe, num_prefixe, id_operateur) VALUES
(3, '034', 2),
(4, '038', 2); 

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
INSERT INTO utilisateur (id_utilisateur, nom_utilisateur, numero_utilisateur, id_operateur, solde_utilisateur) VALUES
(1, 'Jean Rabe', '0331234567', 1, 50000.00),
(2, 'Rabe Rado', '0341234568', 2, 50000.00),
(3, 'Jean Princio', '0331234569', 1, 20000.00);

UPDATE utilisateur SET id_operateur = 1 WHERE id_utilisateur = 1; -- Jean Rabe chez Orange
UPDATE utilisateur SET id_operateur = 2 WHERE id_utilisateur = 2; -- Rabe Rado chez Telma


 INSERT INTO utilisateur (id_utilisateur, nom_utilisateur, numero_utilisateur, id_operateur, solde_utilisateur) VALUES (4, 'Jean bae', '0331234570', 1, 20000.00);

ALTER TABLE operateur ADD COLUMN commission_pourcent DECIMAL(5,2) DEFAULT 0.00;

INSERT INTO configuration_interop (id_operateur, taux_commission_autre_operateur, created_at) VALUES
(1, 2.00, '2024-01-01 10:00:00'),
(1, 2.50, '2024-06-15 14:30:00'),
(2, 2.50, '2024-01-01 10:00:00');