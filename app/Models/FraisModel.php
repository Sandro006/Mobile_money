<?php 

namespace App\Models;

use CodeIgniter\Model;

class FraisModel extends Model
{
    protected $table = 'frais';
    protected $primaryKey = 'id_frais';
    protected $allowedFields = ['id_bareme', 'montant_frais', 'date_frais'];
}
