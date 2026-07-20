<?php

namespace App\Models;

use CodeIgniter\Model;

class PrefixeModel extends Model
{
    protected $table            = 'prefixe';
    protected $primaryKey       = 'id_prefixe';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['id_prefixe', 'num_prefixe', 'id_operateur'];

    /**
     * Récupère tous les préfixes avec le nom de leur opérateur associé
     */
    public function getPrefixesAvecOperateur(): array
    {
        return $this->select('prefixe.*, Operateur.nom AS nom_operateur')
                    ->join('Operateur', 'Operateur.id = prefixe.id_operateur', 'left')
                    ->findAll();
    }
}
