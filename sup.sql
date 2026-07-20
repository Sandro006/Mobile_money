-- 1. Désactiver temporairement la vérification des clés étrangères
PRAGMA foreign_keys = OFF;

-- 2. Suppression de la VUE
DROP VIEW IF EXISTS vue_historique_operations;

-- 3. Suppression de TOUTES les tables (l'ordre n'a plus d'importance ici)
DROP TABLE IF EXISTS configuration_interop;
DROP TABLE IF EXISTS gain;
DROP TABLE IF EXISTS type_gain;
DROP TABLE IF EXISTS retrait;
DROP TABLE IF EXISTS depot;
DROP TABLE IF EXISTS transfert;
DROP TABLE IF EXISTS utilisateur;
DROP TABLE IF EXISTS prefixe;
DROP TABLE IF EXISTS bareme;
DROP TABLE IF EXISTS frais;
DROP TABLE IF EXISTS operation;
DROP TABLE IF EXISTS Operateur;

-- 4. Réactiver la sécurité des clés étrangères pour la suite
PRAGMA foreign_keys = ON;
