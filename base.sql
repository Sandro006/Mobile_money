
PRAGMA foreign_keys = ON;

CREATE TABLE prefixe (
    id_prefixe INT PRIMARY KEY,
    num_prefixe VARCHAR(10)
);

CREATE TABLE operation (
    id_operation INT PRIMARY KEY,
    description_operation VARCHAR(100)
);

CREATE TABLE utilisateur (
    id_utilisateur INT PRIMARY KEY,
    nom_utilisateur VARCHAR(50),
    numero_utilisateur VARCHAR(20),
    id_prefixe INT,
    solde_utilisateur DECIMAL(10,2),
    FOREIGN KEY (id_prefixe) REFERENCES prefixe(id_prefixe)
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

CREATE TABLE gain (
    id_gain INT PRIMARY KEY,
    id_operation INT,
    id_transfert INT NULL,
    id_retrait INT NULL,
    montant_gain DECIMAL(10,2),
    date_gain DATETIME,
    FOREIGN KEY (id_operation) REFERENCES operation(id_operation),
    FOREIGN KEY (id_transfert) REFERENCES transfert(id_transfert),
    FOREIGN KEY (id_retrait) REFERENCES retrait(id_retrait)
);


-- =============================== DONNÉES DE TEST ===============================
-- Types d'opérations
INSERT INTO operation (id_operation, description_operation) VALUES
(1, 'Transfert'),
(2, 'Dépôt'),
(3, 'Retrait');

-- Préfixes d'opérateurs
INSERT INTO prefixe (id_prefixe, num_prefixe) VALUES
(1, '033'),
(2, '037');

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

