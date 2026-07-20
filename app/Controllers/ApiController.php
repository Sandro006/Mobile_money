<?php

namespace App\Controllers;

use App\Models\RetraitModel;
use CodeIgniter\API\ResponseTrait;

class ApiController extends BaseController
{
    use ResponseTrait;

    /**
     * Endpoint API pour calculer dynamiquement les frais de retrait
     * URL : /api/calculer-frais-retrait
     */
    public function calculerFraisRetrait()
    {
        // 1. Récupération sécurisée des données de la requête AJAX
        $montant       = (float) $this->request->getPost('montant');
        $typeOperateur = $this->request->getPost('type_operateur');
        $idOpKiosque   = (int) $this->request->getPost('id_operateur_concerne');

        if ($montant <= 0) {
            return $this->respond(['erreur' => 'Montant invalide'], 400);
        }

        $retraitModel = new RetraitModel();

        // 2. Calcul des frais via le modèle
        $fraisBrut = $retraitModel->calculerFraisBrut($montant);
        $commission = 0.00;

        if ($typeOperateur === 'interop' && $idOpKiosque > 0) {
            $commission = $retraitModel->calculerCommissionInterop($fraisBrut, $idOpKiosque);
        }

        $totalFrais = $fraisBrut + $commission;
        $totalDebite = $montant + $totalFrais;

        // 3. Renvoi de la réponse JSON structurée
        return $this->respond([
            'succes'       => true,
            'montant_net'  => $montant,
            'frais_brut'   => $fraisBrut,
            'commission'   => $commission,
            'total_frais'  => $totalFrais,
            'total_debite' => $totalDebite
        ]);
    }

    /**
 * Endpoint API pour analyser les numéros et simuler la division du transfert groupé
 * URL : /api/calculer-frais-transfert
 */
public function calculerFraisTransfert()
{
    $montantGlobal = (float)$this->request->getPost('montant_global');
    $numeros       = $this->request->getPost('numeros') ?: [];

    $nbDestinataires = count($numeros);
    if ($montantGlobal <= 0 || $nbDestinataires === 0) {
        return $this->respond(['erreur' => 'Données invalides'], 400);
    }

    // Calcul de la part par personne
    $partUnitaire = $montantGlobal / $nbDestinataires;

    $transfertModel = new \App\Models\TransfertModel();
    $fraisGlobaux = 0.00;

    // Simulation du coût de chaque numéro selon sa tranche et sa nature
    foreach ($numeros as $numero) {
        $fraisGlobaux += $transfertModel->calculerFraisTransfert($partUnitaire);
    }

    return $this->respond([
        'succes'             => true,
        'nb_destinataires'   => $nbDestinataires,
        'part_unitaire'      => $partUnitaire,
        'frais_globaux'      => $fraisGlobaux,
        'cout_total_facture' => $montantGlobal + $fraisGlobaux
    ]);
}

}
