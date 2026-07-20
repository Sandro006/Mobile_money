<?php

namespace App\Controllers;

class GainController extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();

        // --- Requête UNION sans limite ---
        $sql = "
            SELECT 
                id_transfert AS id,
                'transfert' AS type,
                date_transfert AS date,
                lieu_transfert AS lieu,
                montant_transfert AS montant,
                envoyeur.nom_utilisateur AS nom_utilisateur,
                envoyeur.numero_utilisateur AS numero_utilisateur,
                recepteur.nom_utilisateur AS autre_nom,
                recepteur.numero_utilisateur AS autre_numero,
                (
                    SELECT montant_frais 
                    FROM frais 
                    WHERE id_bareme = (
                        SELECT id_bareme 
                        FROM bareme 
                        WHERE min_bareme <= transfert.montant_transfert 
                        AND max_bareme >= transfert.montant_transfert 
                        LIMIT 1
                    )
                    ORDER BY date_frais DESC 
                    LIMIT 1
                ) AS frais
            FROM transfert
            JOIN utilisateur envoyeur ON envoyeur.id_utilisateur = transfert.envoyeur_transfert
            JOIN utilisateur recepteur ON recepteur.id_utilisateur = transfert.recepteur_transfert
            WHERE transfert.id_operation = 1

            UNION

            SELECT 
                id_retrait AS id,
                'retrait' AS type,
                date_retrait AS date,
                lieu_retrait AS lieu,
                montant_retrait AS montant,
                utilisateur.nom_utilisateur AS nom_utilisateur,
                utilisateur.numero_utilisateur AS numero_utilisateur,
                NULL AS autre_nom,
                NULL AS autre_numero,
                (
                    SELECT montant_frais 
                    FROM frais 
                    WHERE id_bareme = (
                        SELECT id_bareme 
                        FROM bareme 
                        WHERE min_bareme <= retrait.montant_retrait 
                        AND max_bareme >= retrait.montant_retrait 
                        LIMIT 1
                    )
                    ORDER BY date_frais DESC 
                    LIMIT 1
                ) AS frais
            FROM retrait
            JOIN utilisateur ON utilisateur.id_utilisateur = retrait.id_utilisateur_retrait
            WHERE retrait.id_operation = 3

            UNION

            SELECT 
                id_depot AS id,
                'depot' AS type,
                date_depot AS date,
                lieu_depot AS lieu,
                montant_depot AS montant,
                utilisateur.nom_utilisateur AS nom_utilisateur,
                utilisateur.numero_utilisateur AS numero_utilisateur,
                NULL AS autre_nom,
                NULL AS autre_numero,
                NULL AS frais
            FROM depot
            JOIN utilisateur ON utilisateur.id_utilisateur = depot.id_utilisateur_depot
            WHERE depot.id_operation = 2

            ORDER BY date DESC
        ";

        $query = $db->query($sql);
        $transactions = $query->getResultArray();

        // --- Calcul du total des gains (frais) sur toutes les transactions ---
        $totalGains = 0;
        foreach ($transactions as $t) {
            if ($t['frais'] !== null) {
                $totalGains += (float) $t['frais'];
            }
        }

        $data = [
            'transactions' => $transactions,
            'totalGains'   => $totalGains,
        ];

        return view('operateur/gain/index', $data);
    }
}