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
}
