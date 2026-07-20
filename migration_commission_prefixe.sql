-- ============================================================
-- MIGRATION : Ajout des colonnes pour l'historique des commissions
-- ============================================================

-- 1. Ajouter la colonne created_at à configuration_interop pour dater l'historique
ALTER TABLE configuration_interop ADD COLUMN created_at DATETIME DEFAULT CURRENT_TIMESTAMP;

-- 2. Pour que l'auto-incrément fonctionne en SQLite, il faut que la colonne
--    soit déclarée comme INTEGER PRIMARY KEY AUTOINCREMENT.
--    Si votre table actuelle utilise INT PRIMARY KEY, vous devez la recréer :
--
--    a) Créer une nouvelle table avec le bon type
--    b) Copier les données
--    c) Supprimer l'ancienne table
--    d) Renommer la nouvelle table
--
--    Exécutez ce bloc si id_config retourne NULL lors des INSERT :

DROP TABLE IF EXISTS configuration_interop_new;
CREATE TABLE configuration_interop_new (
    id_config INTEGER PRIMARY KEY AUTOINCREMENT,
    id_operateur INT,
    taux_commission_autre_operateur DECIMAL(5,2) DEFAULT 0.00,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Copier les données existantes (sans se soucier des anciens id_config)
INSERT INTO configuration_interop_new (id_operateur, taux_commission_autre_operateur, created_at)
SELECT id_operateur, taux_commission_autre_operateur, COALESCE(created_at, CURRENT_TIMESTAMP)
FROM configuration_interop;

-- Supprimer l'ancienne table
DROP TABLE configuration_interop;

-- Renommer la nouvelle table
ALTER TABLE configuration_interop_new RENAME TO configuration_interop;

-- 3. Insérer des données de test initiales (si besoin)
INSERT INTO configuration_interop (id_operateur, taux_commission_autre_operateur, created_at) VALUES
(1, 2.00, '2024-01-01 10:00:00'),
(1, 2.50, '2024-06-15 14:30:00'),
(2, 2.50, '2024-01-01 10:00:00');

