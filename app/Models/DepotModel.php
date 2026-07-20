<?php 

namespace App\Models;

use CodeIgniter\Model;

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
}
