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
