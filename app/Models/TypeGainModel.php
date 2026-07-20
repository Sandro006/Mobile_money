<?php

namespace App\Models;

use CodeIgniter\Model;

class TypeGainModel extends Model
{
    protected $table            = 'type_gain';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['id', 'libelle'];
}

