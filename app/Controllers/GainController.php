<?php

namespace App\Controllers;

class GainController extends BaseController
{
    public function index()
    {
        // Vérifier que l'opérateur est connecté
        $idOperateur = session()->get('operateur_id');
        if (!$idOperateur) {
            return redirect()->to('/operateur/auth')->with('error', 'Veuillez vous connecter.');
        }

        $db = \Config\Database::connect();

        // Toutes les opérations via la vue SQL centralisée
        $rows = $db->query("SELECT * FROM vue_gains_operations ORDER BY date DESC")->getResultArray();

        // Initialiser les tableaux et totaux
        $gainsInternes = [];
        $commissions = [];
        $totalGainsInternes = 0;
        $totalCommissions = 0;

        foreach ($rows as $row) {
            $fraisBase = (float) $row['frais_base'];

            // Cas 1 : Transfert externe → séparer frais_base et commission
            if ($row['est_externe']) {
                // La partie "frais de base" va dans gains internes
                $row['frais_calcules'] = $fraisBase;
                $gainsInternes[] = $row;
                $totalGainsInternes += $fraisBase;

                // La commission inter-opérateur va dans un tableau dédié
                $commissionMontant = 0;
                if ($row['taux_commission'] > 0) {
                    $commissionMontant = ($row['taux_commission'] / 100) * $row['montant'];
                }
                if ($commissionMontant > 0) {
                    $row['commission_calculee'] = $commissionMontant;
                    $commissions[] = $row;
                    $totalCommissions += $commissionMontant;
                }
            } else {
                // Cas 2 : Opération interne → tout va dans gains internes
                $row['frais_calcules'] = $fraisBase;
                $gainsInternes[] = $row;
                $totalGainsInternes += $fraisBase;
            }
        }

        $data = [
            'gainsInternes'      => $gainsInternes,
            'commissions'        => $commissions,
            'totalGainsInternes' => $totalGainsInternes,
            'totalCommissions'   => $totalCommissions,
            'totalGeneral'       => $totalGainsInternes + $totalCommissions,
        ];

        return view('operateur/gain/index', $data);
    }
}
