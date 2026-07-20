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
}
