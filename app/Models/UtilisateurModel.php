<?php 

namespace App\Models;

use CodeIgniter\Model;

class UtilisateurModel extends Model
{
    protected $table = 'utilisateur';
    protected $primaryKey = 'id_utilisateur';
    protected $allowedFields = [
        'nom_utilisateur',
        'numero_utilisateur',
        'id_prefixe',
        'solde_utilisateur'
    ];


    function getAll(){
        return $this->findAll();
    }


public function getHistoriqueUtilisateur(int $idUtilisateur, ?int $limit = null): array
        {
            $builder = $this->db->table('vue_historique_operations')
                                ->where('id_utilisateur', $idUtilisateur)
                                ->orderBy('date_operation', 'DESC');

            // Si une limite est précisée, on l'applique à la requête
            if ($limit !== null) {
                $builder->limit($limit);
            }

            return $builder->get()->getResultArray();
        }


}
