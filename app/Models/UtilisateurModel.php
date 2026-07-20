<?php 

namespace App\Models;

use CodeIgniter\Model;

class UtilisateurModel extends Model
{
    protected $table = 'utilisateur';
    protected $primaryKey = 'id_utilisateur';
    protected $allowedFields = [
        'nom_utilisateur',
        'numero_utilisateur',
        'id_prefixe',
        'solde_utilisateur'
    ];
    function getAll(){
        return $this->findAll();
    }
}
