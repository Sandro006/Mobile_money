<?php
namespace App\Models;

use CodeIgniter\Model;

class EpargneModel extends Model
{
    protected $table = 'Epargne';
    protected $primaryKey = 'id';
    protected $allowedFields = ['id_utilisateur', 'Solde', 'Update_at'];

    public function getPourcentageUtilisateur(int $idUtilisateur){
            {
                $builder = $this->db->table('porcentage_epargne')
                                    ->where('id_utilisateur', $idUtilisateur)
                                    ->orderBy('date_operation', 'DESC');
                return $builder->get()->getResultArray();
            }
    }

    public function insertPourcentage(INT $idUtilisateur,float $pourcentage){
        $this->db->table('pourcentage_epargne')->insert([
                'id_utilisateur' => $idUtilisateur,
                'pourcentage' => $pourcentage
            ]);
    }

    
}