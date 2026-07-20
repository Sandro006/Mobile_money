<?php

namespace App\Models;

use CodeIgniter\Model;

class ConfigurationInteropModel extends Model
{
    protected $table            = 'configuration_interop';
    protected $primaryKey       = 'id_config';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'id_config',
        'id_operateur',
        'taux_commission_autre_operateur'
    ];
}

