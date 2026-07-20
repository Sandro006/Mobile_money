<?php 

namespace App\Models;

use CodeIgniter\Model;
use App\Models\UtilisateurModel;

class DepotModel extends Model
{
    protected $table = 'depot';
    protected $primaryKey = 'id_depot';
    protected $allowedFields = [
        'id_operation',
        'id_utilisateur_depot',
        'montant_depot',
        'date_depot',
        'lieu_depot'
    ];


    // Gère l'enregistrement du dépôt et la mise à jour du solde utilisateur

    public function executerDepot(int $idUtilisateur, float $montant, string $lieu): bool
        {
        $this->db->transStart();

        // Enregistrement de la ligne de dépôt avec la date PHP formatée
        $this->insert([
            'id_operation'         => 2,
            'id_utilisateur_depot' => $idUtilisateur,
            'montant_depot'        => $montant,
            'date_depot'           => date('Y-m-d H:i:s'),
            'lieu_depot'           => $lieu
        ]);

        $utilisateurModel = new UtilisateurModel();
        $utilisateur = $utilisateurModel->find($idUtilisateur);

        $soldeActuel = is_object($utilisateur) ? $utilisateur->solde_utilisateur : $utilisateur['solde_utilisateur'];
        $nouveauSolde = $soldeActuel + $montant;

        $utilisateurModel->update($idUtilisateur, [
            'solde_utilisateur' => $nouveauSolde
        ]);

        $this->db->transComplete();

        return $this->db->transStatus() !== false;
        }

}
