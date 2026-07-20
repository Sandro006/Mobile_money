<?php

namespace App\Controllers;

use App\Models\TransfertModel;
use App\Models\UtilisateurModel;
use App\Models\PrefixeModel;
use App\Models\OperateurModel;

class CompensationController extends BaseController
{
    protected $transfertModel;
    protected $utilisateurModel;
    protected $prefixeModel;
    protected $operateurModel;

    public function __construct()
    {
        $this->transfertModel   = new TransfertModel();
        $this->utilisateurModel = new UtilisateurModel();
        $this->prefixeModel     = new PrefixeModel();
        $this->operateurModel   = new OperateurModel();
    }

    public function index()
    {
        // Vérifier que l'opérateur est connecté (administrateur)
        $idOperateur = session()->get('operateur_id');
        if (!$idOperateur) {
            return redirect()->to('/operateur/auth')->with('error', 'Veuillez vous connecter.');
        }

        $db = \Config\Database::connect();

        // ---- Requête pour calculer les montants envoyés et reçus par opérateur ----
        $sql = "
            SELECT 
                op.id AS id_operateur,
                op.nom AS nom_operateur,
                op.libelle,
                -- Total des montants envoyés par les utilisateurs de cet opérateur vers d'autres opérateurs
                COALESCE(SUM(
                    CASE 
                        WHEN op.id = op_env.id AND op.id != op_rec.id 
                        THEN t.montant_transfert 
                        ELSE 0 
                    END
                ), 0) AS total_envoye,
                -- Total des montants reçus par les utilisateurs de cet opérateur depuis d'autres opérateurs
                COALESCE(SUM(
                    CASE 
                        WHEN op.id = op_rec.id AND op.id != op_env.id 
                        THEN t.montant_transfert 
                        ELSE 0 
                    END
                ), 0) AS total_recu
            FROM operateur op
            LEFT JOIN prefixe p_env ON p_env.id_operateur = op.id
            LEFT JOIN utilisateur u_env ON u_env.id_prefixe = p_env.id_prefixe
            LEFT JOIN transfert t ON t.envoyeur_transfert = u_env.id_utilisateur
            LEFT JOIN utilisateur u_rec ON u_rec.id_utilisateur = t.recepteur_transfert
            LEFT JOIN prefixe p_rec ON p_rec.id_prefixe = u_rec.id_prefixe
            LEFT JOIN operateur op_rec ON op_rec.id = p_rec.id_operateur
            LEFT JOIN operateur op_env ON op_env.id = p_env.id_operateur
            GROUP BY op.id
            ORDER BY op.nom
        ";

        $resultats = $db->query($sql)->getResultArray();

        // Calcul du solde net pour chaque opérateur
        foreach ($resultats as &$row) {
            $row['solde'] = $row['total_recu'] - $row['total_envoye'];
        }
        unset($row);

        $data['operateurs'] = $resultats;

        return view('operateur/compensation/index', $data);
    }
}