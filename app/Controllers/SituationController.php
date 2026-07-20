<?php

namespace App\Controllers;

use App\Models\UtilisateurModel;
use App\Models\TransfertModel;
use App\Models\RetraitModel;
use App\Models\DepotModel;

class SituationController extends BaseController
{
    protected $utilisateurModel;
    protected $transfertModel;
    protected $retraitModel;
    protected $depotModel;

    public function __construct()
    {
        $this->utilisateurModel = new UtilisateurModel();
        $this->transfertModel   = new TransfertModel();
        $this->retraitModel     = new RetraitModel();
        $this->depotModel       = new DepotModel();
    }


    public function index()
    {
        $idOperateur = session()->get('operateur_id');
        if (!$idOperateur) {
            return redirect()->to('/operateur/auth')->with('error', 'Veuillez vous connecter en tant qu\'opérateur.');
        }
    
        $db = \Config\Database::connect();
        $prefixes = $db->table('prefixe')
                       ->where('id_operateur', $idOperateur)
                       ->get()
                       ->getResultArray();
    
        $listeIdsPrefixes = array_column($prefixes, 'id_prefixe');
    
        if (empty($listeIdsPrefixes)) {
            $utilisateurs = [];
        } else {
            $utilisateurs = $this->utilisateurModel
                                 ->whereIn('id_prefixe', $listeIdsPrefixes)
                                 ->findAll();
        }
    
        $data['utilisateurs'] = $utilisateurs;
        return view('operateur/situation/index', $data);
    }
    /**
     * Détail des transactions d'un utilisateur
     */
    public function detail($id)
    {
        // Récupérer l'utilisateur
        $utilisateur = $this->utilisateurModel->find($id);
        if (!$utilisateur) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Utilisateur introuvable');
        }

        $db = \Config\Database::connect();

        // ---- Requête UNION pour toutes les transactions de l'utilisateur ----
        // On va chercher :
        // - Transferts où il est envoyeur ou récepteur
        // - Retraits où il est l'utilisateur
        // - Dépôts où il est l'utilisateur

        $sql = "
        SELECT 
        id_transfert AS id,
        'transfert' AS type,
        date_transfert AS date,
        lieu_transfert AS lieu,
        montant_transfert AS montant,
        'Envoyé' AS sens,
        envoyeur.nom_utilisateur AS autre_nom,
        envoyeur.numero_utilisateur AS autre_numero,
        -- Frais de base
        (SELECT montant_frais FROM frais WHERE id_bareme = (
            SELECT id_bareme FROM bareme 
            WHERE min_bareme <= transfert.montant_transfert AND max_bareme >= transfert.montant_transfert LIMIT 1
        ) ORDER BY date_frais DESC LIMIT 1) AS frais_base,
        -- Commission externe (si opérateurs différents)
        CASE 
            WHEN (SELECT id_operateur FROM prefixe WHERE id_prefixe = envoyeur.id_prefixe) 
                 <> (SELECT id_operateur FROM prefixe WHERE id_prefixe = recepteur.id_prefixe)
            THEN 
                (SELECT commission_pourcent FROM operateur WHERE id = (SELECT id_operateur FROM prefixe WHERE id_prefixe = envoyeur.id_prefixe)) / 100 * transfert.montant_transfert
            ELSE 0
        END AS commission_externe
    FROM transfert
    JOIN utilisateur envoyeur ON envoyeur.id_utilisateur = transfert.envoyeur_transfert
    JOIN utilisateur recepteur ON recepteur.id_utilisateur = transfert.recepteur_transfert
    WHERE transfert.envoyeur_transfert = $id OR transfert.recepteur_transfert = $id
    ";

        $query = $db->query($sql);
        $transactions = $query->getResultArray();

        // Pour les transferts, déterminer le sens (Envoyé ou Reçu)
        foreach ($transactions as &$t) {
            if ($t['type'] === 'transfert') {
                // On doit savoir si l'utilisateur est envoyeur ou récepteur
                // On va re-requêter le transfert spécifique pour obtenir les ids
                // Alternative : ajouter une colonne dans la requête UNION pour distinguer
                // Mais on peut aussi simplement récupérer les infos depuis la table transfert
                // On utilise l'id du transfert pour récupérer les ids
                $transfert = $this->transfertModel->find($t['id']);
                if ($transfert) {
                    if ($transfert['envoyeur_transfert'] == $id) {
                        $t['sens'] = 'Envoyé';
                        // autre_nom et autre_numéro seront ceux du récepteur
                        $recepteur = $this->utilisateurModel->find($transfert['recepteur_transfert']);
                        if ($recepteur) {
                            $t['autre_nom'] = $recepteur['nom_utilisateur'];
                            $t['autre_numero'] = $recepteur['numero_utilisateur'];
                        }
                    } else {
                        $t['sens'] = 'Reçu';
                        // autre_nom et autre_numéro seront ceux de l'envoyeur
                        $envoyeur = $this->utilisateurModel->find($transfert['envoyeur_transfert']);
                        if ($envoyeur) {
                            $t['autre_nom'] = $envoyeur['nom_utilisateur'];
                            $t['autre_numero'] = $envoyeur['numero_utilisateur'];
                        }
                    }
                }
            }
        }
        unset($t);

        // Calcul total des frais pour cet utilisateur
        $totalFrais = 0;
        foreach ($transactions as $t) {
            if ($t['frais'] !== null) {
                $totalFrais += (float) $t['frais'];
            }
        }

        $data = [
            'utilisateur' => $utilisateur,
            'transactions' => $transactions,
            'totalFrais' => $totalFrais,
        ];

        return view('operateur/situation/detail', $data);
    }
}