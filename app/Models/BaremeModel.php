<?php 

namespace App\Models;

use CodeIgniter\Model;

class BaremeModel extends Model
{
    protected $table = 'bareme';
    protected $primaryKey = 'id_bareme';
    protected $allowedFields = ['min_bareme', 'max_bareme'];

    function getAll(){
        return $this->findAll();
    }

}
