<?php

namespace App\Controllers;

use App\Models\UtilisateurModel;
use App\Models\TransfertModel;
use App\Models\RetraitModel;
use App\Models\DepotModel;
use App\Models\PrefixeModel;
use App\Models\OperateurModel;

class GainController extends BaseController
{
    protected $utilisateurModel;
    protected $transfertModel;
    protected $retraitModel;
    protected $depotModel;
    protected $prefixeModel;
    protected $operateurModel;

    public function __construct()
    {
        $this->utilisateurModel = new UtilisateurModel();
        $this->transfertModel   = new TransfertModel();
        $this->retraitModel     = new RetraitModel();
        $this->depotModel       = new DepotModel();
        $this->prefixeModel     = new PrefixeModel();
        $this->operateurModel   = new OperateurModel();
    }

    public function index()
    {
        // Vérifier que l'opérateur est connecté
        $idOperateur = session()->get('operateur_id');
        if (!$idOperateur) {
            return redirect()->to('/operateur/auth')->with('error', 'Veuillez vous connecter.');
        }

        $db = \Config\Database::connect();

        // ---- Récupérer tous les transferts avec les opérateurs de l'envoyeur et du récepteur ----
        $sqlTransferts = "
        SELECT 
        t.id_transfert AS id,
        'transfert' AS type,
        t.date_transfert AS date,
        t.lieu_transfert AS lieu,
        t.montant_transfert AS montant,
        oe.id AS id_operateur_envoyeur,
        op_rec.id AS id_operateur_recepteur,
        (
            SELECT montant_frais 
            FROM frais 
            WHERE id_bareme = (
                SELECT id_bareme 
                FROM bareme 
                WHERE min_bareme <= t.montant_transfert 
                AND max_bareme >= t.montant_transfert 
                LIMIT 1
            )
            ORDER BY date_frais DESC 
            LIMIT 1
        ) AS frais_base
    FROM transfert t
    JOIN utilisateur ue ON ue.id_utilisateur = t.envoyeur_transfert
    JOIN prefixe pe ON pe.id_prefixe = ue.id_prefixe
    JOIN operateur oe ON oe.id = pe.id_operateur
    JOIN utilisateur ur ON ur.id_utilisateur = t.recepteur_transfert
    JOIN prefixe pr ON pr.id_prefixe = ur.id_prefixe
    JOIN operateur op_rec ON op_rec.id = pr.id_operateur
";
        

        $transferts = $db->query($sqlTransferts)->getResultArray();

        // ---- Récupérer tous les retraits ----
        $sqlRetraits = "
            SELECT 
                r.id_retrait AS id,
                'retrait' AS type,
                r.date_retrait AS date,
                r.lieu_retrait AS lieu,
                r.montant_retrait AS montant,
                NULL AS id_operateur_envoyeur,
                NULL AS id_operateur_recepteur,
                (
                    SELECT montant_frais 
                    FROM frais 
                    WHERE id_bareme = (
                        SELECT id_bareme 
                        FROM bareme 
                        WHERE min_bareme <= r.montant_retrait 
                        AND max_bareme >= r.montant_retrait 
                        LIMIT 1
                    )
                    ORDER BY date_frais DESC 
                    LIMIT 1
                ) AS frais_base
            FROM retrait r
        ";
        $retraits = $db->query($sqlRetraits)->getResultArray();

        // ---- Récupérer tous les dépôts ----
        $sqlDepots = "
            SELECT 
                d.id_depot AS id,
                'depot' AS type,
                d.date_depot AS date,
                d.lieu_depot AS lieu,
                d.montant_depot AS montant,
                NULL AS id_operateur_envoyeur,
                NULL AS id_operateur_recepteur,
                NULL AS frais_base   -- Les dépôts n'ont pas de frais (ou bien 0)
            FROM depot d
        ";
        $depots = $db->query($sqlDepots)->getResultArray();

        // Fusionner toutes les transactions
        $transactions = array_merge($transferts, $retraits, $depots);

        // Trier par date décroissante
        usort($transactions, function($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });

        // Initialiser les totaux
        $totalGainsInternes = 0;
        $totalGainsExternes = 0;

        // Parcourir chaque transaction pour classer et calculer les frais effectifs
        foreach ($transactions as &$t) {
            $frais = 0;
            $estExterne = false;

            if ($t['type'] === 'transfert') {
                // Récupérer le montant des frais de base
                $frais = (float) $t['frais_base'];

                // Déterminer si le transfert est externe
                if ($t['id_operateur_envoyeur'] != $t['id_operateur_recepteur']) {
                    $estExterne = true;
                    // Ajouter la commission inter-opérateur si elle est configurée
                    // On récupère le pourcentage de l'opérateur de l'envoyeur
                    $operateurEnvoyeur = $this->operateurModel->find($t['id_operateur_envoyeur']);
                    if ($operateurEnvoyeur && isset($operateurEnvoyeur['commission_pourcent'])) {
                        $commission = ($operateurEnvoyeur['commission_pourcent'] / 100) * $t['montant'];
                        $frais += $commission;
                    }
                }
            } else {
                // Dépôts et retraits sont toujours internes
                $frais = (float) ($t['frais_base'] ?? 0);
                $estExterne = false;
            }

            // Stocker le frais calculé et le statut dans la transaction
            $t['frais_calcules'] = $frais;
            $t['est_externe'] = $estExterne;

            // Ajouter au total correspondant
            if ($estExterne) {
                $totalGainsExternes += $frais;
            } else {
                $totalGainsInternes += $frais;
            }
        }
        unset($t); // pour éviter les références

        // Préparer les données pour la vue
        $data = [
            'transactions' => $transactions,
            'totalGainsInternes' => $totalGainsInternes,
            'totalGainsExternes' => $totalGainsExternes,
            'totalGeneral' => $totalGainsInternes + $totalGainsExternes,
        ];

        return view('operateur/gain/index', $data);
    }
}