<?php 

namespace App\Models;

use CodeIgniter\Model;

class TransfertModel extends Model
{
    protected $table = 'transfert';
    protected $primaryKey = 'id_transfert';
    protected $allowedFields = [
        'id_operation',
        'envoyeur_transfert',
        'recepteur_transfert',
        'montant_transfert',
        'date_transfert',
        'lieu_transfert'
    ];

    /**
     * Récupère le montant des frais selon le barème
     */
    public function calculerFrais(float $montant): float
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

    /**
     * Valide l'existence d'un destinataire et retourne ses informations
     */
    public function chargerDestinataire(string $numero)
    {
        return $this->db->table('utilisateur')
                        ->where('numero_utilisateur', $numero)
                        ->get()
                        ->getRowArray();
    }

    /**
     * Exécute le transfert d'argent de manière atomique et sécurisée
     */
    public function executerTransfert(int $idEnvoyeur, int $idRecepteur, float $montant, float $frais, string $lieu): bool
    {
        $this->db->transStart();

        $dateActuelle = date('Y-m-d H:i:s');

        // Étape A : Génération manuelle d'un ID unique pour le transfert
        $dernierTransfert = $this->db->table('transfert')->selectMax('id_transfert')->get()->getRowArray();
        $idTransfert = ($dernierTransfert['id_transfert'] ?? 0) + 1;

        // 1. Insertion du transfert
        $this->db->table('transfert')->insert([
            'id_transfert'        => $idTransfert,
            'id_operation'        => 1, // ID correspondant au type 'Transfert'
            'envoyeur_transfert'  => $idEnvoyeur,
            'recepteur_transfert' => $idRecepteur,
            'montant_transfert'   => $montant,
            'date_transfert'      => $dateActuelle,
            'lieu_transfert'      => $lieu
        ]);

        // 2. Insertion dans la table Gain (Frais facturés à l'envoyeur)
        if ($frais > 0) {
            $dernierGain = $this->db->table('gain')->selectMax('id_gain')->get()->getRowArray();
            $idGain = ($dernierGain['id_gain'] ?? 0) + 1;

            $this->db->table('gain')->insert([
                'id_gain'      => $idGain,
                'id_operation' => 1,
                'id_transfert' => $idTransfert,
                'id_retrait'   => null,
                'montant_gain' => $frais,
                'date_gain'    => $dateActuelle
            ]);
        }

        // 3. Mise à jour des soldes des utilisateurs
        $utilisateurModel = new \App\Models\UtilisateurModel();

        // Débit de l'envoyeur (Montant + Frais)
        $envoyeur = $utilisateurModel->find($idEnvoyeur);
        $soldeEnvoyeurActuel = is_object($envoyeur) ? $envoyeur->solde_utilisateur : $envoyeur['solde_utilisateur'];
        $utilisateurModel->update($idEnvoyeur, [
            'solde_utilisateur' => $soldeEnvoyeurActuel - ($montant + $frais)
        ]);

        // Crédit du récepteur (Montant net uniquement)
        $recepteur = $utilisateurModel->find($idRecepteur);
        $soldeRecepteurActuel = is_object($recepteur) ? $recepteur->solde_utilisateur : $recepteur['solde_utilisateur'];
        $utilisateurModel->update($idRecepteur, [
            'solde_utilisateur' => $soldeRecepteurActuel + $montant
        ]);

        $this->db->transComplete();

        return $this->db->transStatus() !== false;
    }
}
