-- Migration: Création des vues pour le dashboard opérateur
-- Ces vues remplacent les requêtes complexes avec UNION ALL dans le contrôleur

DROP VIEW IF EXISTS vue_dashboard_all_operations;

CREATE VIEW vue_dashboard_all_operations AS
SELECT 
    u.id_operateur,
    'Dépôt' AS type_operation,
    d.id_depot AS id_transaction,
    d.montant_depot AS montant,
    d.date_depot AS date_operation,
    u.nom_utilisateur,
    u.id_utilisateur
FROM depot d
JOIN utilisateur u ON d.id_utilisateur_depot = u.id_utilisateur

UNION ALL

SELECT 
    u.id_operateur,
    'Retrait' AS type_operation,
    r.id_retrait AS id_transaction,
    r.montant_retrait AS montant,
    r.date_retrait AS date_operation,
    u.nom_utilisateur,
    u.id_utilisateur
FROM retrait r
JOIN utilisateur u ON r.id_utilisateur_retrait = u.id_utilisateur

UNION ALL

SELECT 
    u.id_operateur,
    'Transfert' AS type_operation,
    t.id_transfert AS id_transaction,
    t.montant_transfert AS montant,
    t.date_transfert AS date_operation,
    (SELECT nom_utilisateur FROM utilisateur WHERE id_utilisateur = t.envoyeur_transfert) AS nom_utilisateur,
    t.envoyeur_transfert AS id_utilisateur
FROM transfert t
JOIN utilisateur u ON t.envoyeur_transfert = u.id_utilisateur;

DROP VIEW IF EXISTS vue_dashboard_all_gains;

CREATE VIEW vue_dashboard_all_gains AS
-- Gains provenant des retraits
SELECT 
    u.id_operateur,
    g.montant_gain,
    g.date_gain,
    'retrait' AS source_type,
    g.id_retrait AS source_id
FROM gain g
JOIN retrait r ON g.id_retrait = r.id_retrait
JOIN utilisateur u ON r.id_utilisateur_retrait = u.id_utilisateur
WHERE g.id_retrait IS NOT NULL

UNION ALL

-- Gains provenant des transferts (côté envoyeur)
SELECT 
    u.id_operateur,
    g.montant_gain,
    g.date_gain,
    'transfert' AS source_type,
    g.id_transfert AS source_id
FROM gain g
JOIN transfert t ON g.id_transfert = t.id_transfert
JOIN utilisateur u ON t.envoyeur_transfert = u.id_utilisateur
WHERE g.id_transfert IS NOT NULL;

