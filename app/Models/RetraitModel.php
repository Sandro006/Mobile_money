<?php 

namespace App\Models;

use CodeIgniter\Model;

class RetraitModel extends Model
{
    protected $table = 'retrait';
    protected $primaryKey = 'id_retrait';
    protected $allowedFields = [
        'id_operation', 'id_utilisateur_retrait', 'montant_retrait', 'date_retrait', 'lieu_retrait'
    ];

    // =========================================================================
    // 1. PETITES FONCTIONS DE CALCUL ET DE LECTURE (LECTURE SEULE)
    // =========================================================================

    /**
     * Calcule uniquement les frais bruts fixes à partir du barème de tranches.
     */
    public function calculerFraisBrut(float $montant): float
    {
        $resultat = $this->db->table('bareme b')
                       ->select('f.montant_frais')
                       ->join('frais f', 'f.id_bareme = b.id_bareme')
                       ->where('b.min_bareme <=', $montant)
                       ->where('b.max_bareme >=', $montant)
                       ->get()
                       ->getRowArray();

        return $resultat ? (float)$resultat['montant_frais'] : 0.00;
    }

    /**
     * Récupère le taux de commission enregistré pour un opérateur spécifique.
     */
    public function obtenirTauxInterop(int $idOperateur): float
    {
        $row = $this->db->table('configuration_interop')
                        ->where('id_operateur', $idOperateur)
                        ->get()
                        ->getRowArray();

        return $row ? (float)$row['taux_commission_autre_operateur'] : 0.00;
    }

    /**
     * Calcule le montant précis de la commission d'interopérabilité (Frais Brut * Taux).
     */
    public function calculerCommissionInterop(float $fraisBrut, int $idOpKiosque): float
    {
        if ($idOpKiosque <= 0) return 0.00;
        
        $taux = $this->obtenirTauxInterop($idOpKiosque);
        return $fraisBrut * ($taux / 100);
    }

    // =========================================================================
    // 2. PETITES FONCTIONS D'ÉCRITURE (SOUS-REQUÊTES SÉCURISÉES)
    // =========================================================================

    /**
     * Génère manuellement le prochain identifiant unique incrémenté pour une table donnée.
     */
    private function genererProchainId(string $table, string $clePrimaire): int
    {
        $dernier = $this->db->table($table)->selectMax($clePrimaire)->get()->getRowArray();
        return ($dernier[$clePrimaire] ?? 0) + 1;
    }

    /**
     * Insère un enregistrement dans la table gain de manière totalement isolée et sécurisée.
     */
    private function enregistrerGain(int $idRetrait, float $montant, int $typeGain, ?int $idOperateur, string $dateActuelle): void
    {
        // Sécurité ultime contre l'erreur FOREIGN KEY constraint : 
        // L'opérateur doit obligatoirement exister et être supérieur à 0
        if ($montant <= 0 || empty($idOperateur) || $idOperateur <= 0) {
            return; 
        }

        $idGain = $this->genererProchainId('gain', 'id_gain');

        $this->db->table('gain')->insert([
            'id_gain'               => $idGain,
            'id_operation'          => 3, // Retrait
            'id_transfert'          => null,
            'id_retrait'            => $idRetrait,
            'montant_gain'          => $montant,
            'id_type_gain'          => $typeGain, // 1 pour Interne, 2 pour Inter-Opérateur
            'id_operateur_concerne' => $idOperateur,
            'date_gain'             => $dateActuelle
        ]);
    }

    // =========================================================================
    // 3. FONCTION PRINCIPALE (ORCHESTRATEUR DE TRANSACTION)
    // =========================================================================

    /**
     * Gère l'exécution globale et le débit en compte du retrait.
     */
    public function executerRetrait(int $idUtilisateur, float $montant, float $fraisBrut, float $commission, int $idOpUtilisateur, ?int $idOpKiosque, string $lieu): bool
    {
        $this->db->transStart();

        $dateActuelle = date('Y-m-d H:i:s');
        $totalDebite = $montant + $fraisBrut + $commission;
        $idRetrait = $this->genererProchainId('retrait', 'id_retrait');

        // A. Étape 1 : Enregistrement de la fiche de retrait principale
        $this->db->table('retrait')->insert([
            'id_retrait'             => $idRetrait,
            'id_operation'           => 3, 
            'id_utilisateur_retrait' => $idUtilisateur,
            'montant_retrait'        => $montant,
            'date_retrait'           => $dateActuelle,
            'lieu_retrait'           => $lieu
        ]);

        // B. Étape 2 : Distribution du Gain Brut (Interne -> Reçu par l'opérateur de l'utilisateur)
        $this->enregistrerGain($idRetrait, $fraisBrut, 1, $idOpUtilisateur, $dateActuelle);

        // C. Étape 3 : Distribution de la Commission d'interopérabilité (Inter-Opérateur -> Reçu par l'opérateur du Kiosque)
        if ($commission > 0 && !empty($idOpKiosque)) {
            $this->enregistrerGain($idRetrait, $commission, 2, $idOpKiosque, $dateActuelle);
        }

        // D. Étape 4 : Débit sur le solde de l'utilisateur
        $utilisateurModel = new \App\Models\UtilisateurModel();
        $utilisateur = $utilisateurModel->find($idUtilisateur);
        $soldeActuel = is_object($utilisateur) ? $utilisateur->solde_utilisateur : $utilisateur['solde_utilisateur'];

        $utilisateurModel->update($idUtilisateur, [
            'solde_utilisateur' => $soldeActuel - $totalDebite
        ]);

        $this->db->transComplete();

        return $this->db->transStatus() !== false;
    }
}
