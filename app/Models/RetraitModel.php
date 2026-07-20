<?php 

namespace App\Models;

use CodeIgniter\Model;

class RetraitModel extends Model
{
    protected $table = 'retrait';
    protected $primaryKey = 'id_retrait';
    protected $allowedFields = [
        'id_operation',
        'id_utilisateur_retrait',
        'montant_retrait',
        'date_retrait',
        'lieu_retrait'
    ];

    public function calculerFrais(float $montant): float
    {
        $db = \Config\Database::connect();
        $resultat = $db->table('bareme b')
                       ->select('f.montant_frais')
                       ->join('frais f', 'f.id_bareme = b.id_bareme')
                       ->where('b.min_bareme <=', $montant)
                       ->where('b.max_bareme >=', $montant)
                       ->get()
                       ->getRowArray();

        // Si le montant dépasse la tranche maximale ou n'est pas trouvé, on peut retourner 0 ou une valeur par défaut
        return $resultat ? (float)$resultat['montant_frais'] : 0.00;
    }

    public function executerRetrait(int $idUtilisateur, float $montant, float $frais, string $lieu): bool
    {
        $this->db->transStart();

        $dateActuelle = date('Y-m-d H:i:s');

        $dernierRetrait = $this->db->table('retrait')->selectMax('id_retrait')->get()->getRowArray();
        $idRetrait = ($dernierRetrait['id_retrait'] ?? 0) + 1;

        // Insertion du retrait
        $this->db->table('retrait')->insert([
            'id_retrait'             => $idRetrait,
            'id_operation'           => 3, 
            'id_utilisateur_retrait' => $idUtilisateur,
            'montant_retrait'        => $montant,
            'date_retrait'           => $dateActuelle,
            'lieu_retrait'           => $lieu
        ]);

        if ($frais > 0) {
            $dernierGain = $this->db->table('gain')->selectMax('id_gain')->get()->getRowArray();
            $idGain = ($dernierGain['id_gain'] ?? 0) + 1;

            $this->db->table('gain')->insert([
                'id_gain'      => $idGain,
                'id_operation' => 3,
                'id_transfert' => null,
                'id_retrait'   => $idRetrait,
                'montant_gain' => $frais,
                'date_gain'    => $dateActuelle
            ]);
        }

        // Mise à jour du solde utilisateur via son propre modèle
        $utilisateurModel = new \App\Models\UtilisateurModel();
        $utilisateur = $utilisateurModel->find($idUtilisateur);
        $soldeActuel = is_object($utilisateur) ? $utilisateur->solde_utilisateur : $utilisateur['solde_utilisateur'];
        
        $nouveauSolde = $soldeActuel - ($montant + $frais);

        $utilisateurModel->update($idUtilisateur, [
            'solde_utilisateur' => $nouveauSolde
        ]);

        $this->db->transComplete();

        return $this->db->transStatus() !== false;
    }


}
