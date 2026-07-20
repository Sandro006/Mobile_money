<?php 

namespace App\Models;

use CodeIgniter\Model;

class GainModel extends Model
{
    protected $table = 'gain';
    protected $primaryKey = 'id_gain';
    protected $allowedFields = [
        'id_operation',
        'id_transfert',
        'id_retrait',
        'montant_gain',
        'date_gain'
    ];
}
