<?php 

namespace App\Models;

use CodeIgniter\Model;

class OperationModel extends Model
{
    protected $table = 'operation';
    protected $primaryKey = 'id_operation';
    protected $allowedFields = ['description_operation'];
}
