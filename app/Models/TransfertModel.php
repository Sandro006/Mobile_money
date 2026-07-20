<?php

namespace App\Models;

use CodeIgniter\Model;

class TransfertModel extends Model
{
    protected $table = 'transfert';
    protected $primaryKey = 'id_transfert';
    protected $allowedFields = [
        'id_operation', 'envoyeur_transfert', 'recepteur_transfert', 'montant_transfert', 'date_transfert', 'lieu_transfert'
    ];

    public function calculerFraisTransfert(float $montant): float
    {
        $resultat = $this->db->table('bareme b')
                       ->select('f.montant_frais')
                       ->join('frais f', 'f.id_bareme = b.id_bareme')
                       ->where('b.min_bareme <=', $montant)
                       ->where('b.max_bareme >=', $montant)
                       ->get()
                       ->getRowArray();

        return $resultat ? (float)$resultat['montant_frais'] : 0.00;
    }

    public function chargerDestinataire(string $numero)
    {
        return $this->db->table('utilisateur')
                        ->where('numero_utilisateur', $numero)
                        ->get()
                        ->getRowArray();
    }

    /**
     * Effectue un virement unitaire (Ligne transfert, Ligne gain, Ajustement des 2 soldes)
     * Cette fonction sera exécutée au sein d'une transaction parente gérée par le contrôleur
     */
    public function executerVirementUnitaire(int $idEnvoyeur, int $idRecepteur, float $montantUnitaire, float $fraisUnitaire, int $idOpEnvoyeur, int $idOpRecepteur, string $lieu, string $dateActuelle): void
    {
        // 1. Prochain ID de Transfert (MAX + 1)
        $dernierTrans = $this->db->table('transfert')->selectMax('id_transfert')->get()->getRowArray();
        $idTransfert = ($dernierTrans['id_transfert'] ?? 0) + 1;

        // 2. Écriture de la fiche du transfert
        $this->db->table('transfert')->insert([
            'id_transfert'       => $idTransfert,
            'id_operation'       => 1,
            'envoyeur_transfert' => $idEnvoyeur,
            'recepteur_transfert'=> $idRecepteur,
            'montant_transfert'  => $montantUnitaire,
            'date_transfert'     => $dateActuelle,
            'lieu_transfert'     => $lieu
        ]);

        // 3. Écriture de la fiche de gain (Frais d'envoi)
        if ($fraisUnitaire > 0) {
            $typeGainTrans = ($idOpEnvoyeur === $idOpRecepteur) ? 1 : 2; // 1 = Interne, 2 = Inter-Opérateur
            
            $dernierGain = $this->db->table('gain')->selectMax('id_gain')->get()->getRowArray();
            $idGain = ($dernierGain['id_gain'] ?? 0) + 1;

            $this->db->table('gain')->insert([
                'id_gain'               => $idGain,
                'id_operation'          => 1,
                'id_transfert'          => $idTransfert,
                'id_retrait'            => null,
                'montant_gain'          => $fraisUnitaire,
                'id_type_gain'          => $typeGainTrans,
                'id_operateur_concerne' => $idOpEnvoyeur,
                'date_gain'             => $dateActuelle
            ]);
        }

        // 4. Mouvements comptables (Débit Envoyeur / Crédit Récepteur)
        $this->db->table('utilisateur')->where('id_utilisateur', $idEnvoyeur)->decrement('solde_utilisateur', ($montantUnitaire + $fraisUnitaire));
        $this->db->table('utilisateur')->where('id_utilisateur', $idRecepteur)->increment('solde_utilisateur', $montantUnitaire);
    }
}
