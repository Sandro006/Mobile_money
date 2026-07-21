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

        // Support de la recherche
        $search = $this->request->getGet('search');
        
        if ($search) {
            $utilisateurs = $this->utilisateurModel
                                 ->where('id_operateur', $idOperateur)
                                 ->groupStart()
                                     ->like('nom_utilisateur', $search)
                                     ->orLike('numero_utilisateur', $search)
                                 ->groupEnd()
                                 ->findAll();
        } else {
            $utilisateurs = $this->utilisateurModel
                                 ->where('id_operateur', $idOperateur)
                                 ->findAll();
        }

        $data['utilisateurs'] = $utilisateurs;
        $data['search'] = $search;
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
            t.id_transfert AS id,
            'transfert' AS type,
            t.date_transfert AS date,
            t.lieu_transfert AS lieu,
            t.montant_transfert AS montant,
            CASE WHEN t.envoyeur_transfert = $id THEN 'Envoyé' ELSE 'Reçu' END AS sens,
            CASE WHEN t.envoyeur_transfert = $id 
                THEN recepteur.nom_utilisateur 
                ELSE envoyeur.nom_utilisateur 
            END AS autre_nom,
            CASE WHEN t.envoyeur_transfert = $id 
                THEN recepteur.numero_utilisateur 
                ELSE envoyeur.numero_utilisateur 
            END AS autre_numero,
            COALESCE((
                SELECT montant_frais FROM frais 
                WHERE id_bareme = (
                    SELECT id_bareme FROM bareme 
                    WHERE min_bareme <= t.montant_transfert AND max_bareme >= t.montant_transfert LIMIT 1
                ) ORDER BY date_frais DESC LIMIT 1
            ), 0) AS frais_base,
            CASE 
                WHEN envoyeur.id_operateur <> recepteur.id_operateur
                THEN COALESCE((
                    SELECT taux_commission_autre_operateur FROM configuration_interop 
                    WHERE id_operateur = envoyeur.id_operateur 
                    ORDER BY id_config DESC LIMIT 1
                ), 0) / 100.0 * t.montant_transfert
                ELSE 0
            END AS commission_externe,
            CASE WHEN t.envoyeur_transfert = $id THEN 'Envoyé' ELSE 'Reçu' END AS sens_filtre
        FROM transfert t
        JOIN utilisateur envoyeur ON envoyeur.id_utilisateur = t.envoyeur_transfert
        JOIN utilisateur recepteur ON recepteur.id_utilisateur = t.recepteur_transfert
        WHERE t.envoyeur_transfert = $id OR t.recepteur_transfert = $id
        ";

        $query = $db->query($sql);
        $transactions = $query->getResultArray();

        $totalFrais = 0;
        foreach ($transactions as &$t) {
            // Calcul frais effectifs = frais_base + commission_externe
            $t['frais'] = (float) $t['frais_base'] + (float) $t['commission_externe'];
            $totalFrais += $t['frais'];
        }
        unset($t);

        $data = [
            'utilisateur' => $utilisateur,
            'transactions' => $transactions,
            'totalFrais' => $totalFrais,
        ];

        return view('operateur/situation/detail', $data);
    }

    public function pageinserer()
    {
       $idOperateur = session()->get('operateur_id');
        if (!$idOperateur) {
            return redirect()->to('/operateur/auth')->with('error', 'Veuillez vous connecter en tant qu\'opérateur.');
        }
        return view('operateur/situation/create');
    }

    // =========================================================================
    // MÉTHODE DE SAUVEGARDE DE L'UTILISATEUR
    // =========================================================================
    public function sauvegarder()
    {
        $session = session();
        
        // 1. Vérification de la session opérateur
        $idOperateur = $session->get('operateur_id');
        if (!$idOperateur) {
            return redirect()->to('/operateur/auth')->with('error', 'Veuillez vous connecter en tant qu\'opérateur.');
        }

        // 2. Récupération des données du formulaire POST
        $nom           = $this->request->getPost('nom_utilisateur');
        $numero        = $this->request->getPost('numero_utilisateur');
        $soldeInitial  = $this->request->getPost('solde_utilisateur');

        // 3. Validation stricte des données côté serveur
        if (empty($nom) || empty($numero)) {
            $session->setFlashdata('erreur', 'Le nom et le numéro de téléphone sont obligatoires.');
            return redirect()->to('situation/create');
        }

        if (!is_numeric($soldeInitial) || $soldeInitial < 0) {
            $session->setFlashdata('erreur', 'Le solde initial doit être un nombre positif ou égal à zéro.');
            return redirect()->to('situation/create');
        }

        // 4. Vérification d'unicité du numéro (Optionnel mais fortement recommandé)
        $dejaExistant = $this->utilisateurModel->where('numero_utilisateur', $numero)->first();
        if ($dejaExistant) {
            $session->setFlashdata('erreur', 'Ce numéro de téléphone est déjà attribué à un autre compte.');
            return redirect()->to('situation/create');
        }

        // 5. Préparation des données d'insertion
        $donneesUtilisateur = [
            // ID omis : SQLite l'ajoute automatiquement via AUTOINCREMENT
            'nom_utilisateur'    => trim($nom),
            'numero_utilisateur' => trim($numero),
            'id_operateur'       => (int) $idOperateur, // Sécurisé : provient de la session, pas du formulaire
            'solde_utilisateur'  => (float) $soldeInitial
        ];

        // 6. Insertion en Base de Données
        if ($this->utilisateurModel->insert($donneesUtilisateur)) {
            return redirect()->to('situation')
                             ->with('success', 'L\'utilisateur ' . esc($nom) . ' a été créé avec succès.');
        } else {
            $session->setFlashdata('erreur', 'Une erreur est survenue lors de l\'enregistrement en base de données.');
            return redirect()->to('situation/create');
        }
    }



}
