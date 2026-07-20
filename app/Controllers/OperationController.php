<?php

namespace App\Controllers;

use App\Models\DepotModel;

class OperationController extends BaseController
{
    /**
     * Affiche la page de formulaire pour un dépôt
     */
    public function pageDepot()
    {
        if (empty(session()->get('logged_in'))) {
            return redirect()->to('/login');
        }
        return view('operation/depot');
    }

    /**
     * Affiche la page de formulaire pour un retrait
     */
    public function pageRetrait()
    {
        if (empty(session()->get('logged_in'))) {
            return redirect()->to('/login');
        }
        return view('operation/retrait');
    }

    /**
     * Traite la soumission du formulaire de dépôt
     */
    public function depot()
    {
        $session = session();

        if (empty($session->get('logged_in'))) {
            return redirect()->to('/login');
        }

        $idUtilisateur = $session->get('id_utilisateur');
        $montant = $this->request->getPost('montant');
        $lieu = $this->request->getPost('lieu') ?: 'Guichet';

        if (empty($montant) || !is_numeric($montant) || $montant <= 0) {
            $session->setFlashdata('erreur', 'Le montant du dépôt doit être supérieur à 0 Ar.');
            return redirect()->to('/operation/page-depot');
        }

        $depotModel = new DepotModel();
        $succes = $depotModel->executerDepot($idUtilisateur, (float)$montant, $lieu);

        if ($succes) {
            $session->setFlashdata('succes', 'Le dépôt de ' . number_format($montant, 2, ',', ' ') . ' Ar a été effectué avec succès !');
            return redirect()->to('/client');
        } else {
            $session->setFlashdata('erreur', 'Une erreur technique est survenue sur la base de données.');
            return redirect()->to('/operation/page-depot');
        }
    }

    /**
     * Traite la soumission du formulaire de retrait (Frais inclus ou déduits)
     */
    public function retrait()
    {
        $session = session();

        if (empty($session->get('logged_in'))) {
            return redirect()->to('/login');
        }

        $idUtilisateur = $session->get('id_utilisateur');
        $montant = $this->request->getPost('montant');
        $lieu = $this->request->getPost('lieu') ?: 'Kiosque';

        // Récupération des paramètres de gestion du Kiosque
        $typeOperateur = $this->request->getPost('type_operateur'); // 'interne' ou 'interop'
        $idOpKiosque = $this->request->getPost('id_operateur_concerne'); // ID ou null
        $fraisInclus = $this->request->getPost('frais_inclus') === '1'; // Case cochée ou non

        // 1. Validations de base des entrées du formulaire
        if (empty($montant) || !is_numeric($montant) || $montant <= 0) {
            $session->setFlashdata('erreur', 'Le montant du retrait doit être supérieur à 0 Ar.');
            return redirect()->to('/operation/page-retrait');
        }

        if ($typeOperateur === 'interop' && empty($idOpKiosque)) {
            $session->setFlashdata('erreur', "Veuillez sélectionner l'opérateur du kiosque pour un retrait inter-opérateur.");
            return redirect()->to('/operation/page-retrait');
        }

        // 2. Chargement des données de l'utilisateur (Solde et Opérateur d'origine)
        $utilisateurModel = new \App\Models\UtilisateurModel();
        $utilisateur = $utilisateurModel->find($idUtilisateur);

        if (!$utilisateur) {
            $session->setFlashdata('erreur', 'Compte utilisateur introuvable.');
            return redirect()->to('/operation/page-retrait');
        }

        $soldeActuel = is_object($utilisateur) ? $utilisateur->solde_utilisateur : $utilisateur['solde_utilisateur'];
        $idOpUtilisateur = is_object($utilisateur) ? $utilisateur->id_operateur : $utilisateur['id_operateur'];

        if (empty($idOpUtilisateur) || $idOpUtilisateur <= 0) {
            $session->setFlashdata('erreur', "Erreur : Votre compte n'est rattaché à aucun opérateur e-Money de référence.");
            return redirect()->to('/operation/page-retrait');
        }

        $retraitModel = new \App\Models\RetraitModel();

        // 3. Calculs des frais fixes et des commissions d'interopérabilité
        $fraisBrut = $retraitModel->calculerFraisBrut((float)$montant);
        $commission = 0.00;

        if ($typeOperateur === 'interop') {
            $commission = $retraitModel->calculerCommissionInterop($fraisBrut, (int)$idOpKiosque);
        }

        $totalFrais = $fraisBrut + $commission;

        // 4. Application des scénarios mathématiques (Frais inclus ou déduits)
        if ($fraisInclus) {
            // Le montant saisi est le NET reçu. Les frais se rajoutent au débit global
            $montantRetraitFinal = $montant;
            $totalRequisDeSolde = $montant + $totalFrais;
        } else {
            // Le montant saisi est le BRUT. Les frais sont soustraits de ce qui est donné en main propre
            $montantRetraitFinal = $montant - $totalFrais;
            $totalRequisDeSolde = $montant;

            // Sécurité : Bloquer l'opération si les frais consument tout le montant saisi
            if ($montantRetraitFinal <= 0) {
                $session->setFlashdata('erreur', "Opération impossible. Le montant saisi est trop faible pour couvrir les frais de barème (" . number_format($totalFrais, 2, ',', ' ') . " Ar).");
                return redirect()->to('/operation/page-retrait');
            }
        }

        // 5. Contrôle de provision du solde client
        if ($soldeActuel < $totalRequisDeSolde) {
            $msgErreur = 'Solde insuffisant. Il vous faut au moins ' . number_format($totalRequisDeSolde, 2, ',', ' ') . ' Ar. ';
            $msgErreur .= '(Montant demandé : ' . number_format($montantRetraitFinal, 2, ',', ' ') . ' Ar + Frais totaux : ' . number_format($totalFrais, 2, ',', ' ') . ' Ar).';

            $session->setFlashdata('erreur', $msgErreur);
            return redirect()->to('/operation/page-retrait');
        }

        // 6. Persistance en Base de données via la transaction du modèle découpé
        $succes = $retraitModel->executerRetrait(
            (int)$idUtilisateur,
            (float)$montantRetraitFinal,
            (float)$fraisBrut,
            (float)$commission,
            (int)$idOpUtilisateur,
            $idOpKiosque ? (int)$idOpKiosque : null,
            $lieu
        );

        if ($succes) {
            $msgSucces = 'Retrait validé avec succès ! ';
            $msgSucces .= 'Montant net remis au guichet : ' . number_format($montantRetraitFinal, 2, ',', ' ') . ' Ar. ';
            $msgSucces .= 'Frais totaux appliqués : ' . number_format($totalFrais, 2, ',', ' ') . ' Ar.';

            $session->setFlashdata('succes', $msgSucces);
            return redirect()->to('/client');
        } else {
            $session->setFlashdata('erreur', "Une erreur SQL d'écriture est survenue lors de la finalisation.");
            return redirect()->to('/operation/page-retrait');
        }
    }

    /**
     * Affiche la page de formulaire pour un transfert
     */
    public function pageTransfert()
    {
        if (empty(session()->get('logged_in'))) {
            return redirect()->to('/login');
        }
        return view('operation/transfert');
    }

public function transfert()
{
    $session = session();

    if (empty($session->get('logged_in'))) {
        return redirect()->to('/login');
    }

    $idEnvoyeur    = $session->get('id_utilisateur');
    $modeTransfert = $this->request->getPost('mode_transfert'); // 'simple' ou 'multiple'
    $montantGlobal = $this->request->getPost('montant');
    $lieu          = $this->request->getPost('lieu') ?: 'Mobile App';

    // 1. Validation de base des entrées monétaires
    if (empty($montantGlobal) || !is_numeric($montantGlobal) || $montantGlobal <= 0) {
        $session->setFlashdata('erreur', 'Le montant du transfert doit être supérieur à 0 Ar.');
        return redirect()->to('/operation/page-transfert');
    }

    // 2. Extraction adaptative des numéros selon le mode choisi
    $listeNumeros = [];
    if ($modeTransfert === 'simple') {
        $numUnique = $this->request->getPost('numero_destinataire');
        if (!empty($numUnique)) {
            $listeNumeros[] = trim($numUnique);
        }
    } else {
        $chaineNumeros = $this->request->getPost('numeros_destinataires');
        if (!empty($chaineNumeros)) {
            $listeNumeros = preg_split("/[\s,;]+/", $chaineNumeros);
        }
    }

    // Nettoyage des chaînes vides et élimination des doublons accidentels
    $listeNumeros = array_unique(array_filter(array_map('trim', $listeNumeros)));
    $nbDestinataires = count($listeNumeros);

    if ($nbDestinataires === 0) {
        $session->setFlashdata('erreur', 'Veuillez renseigner au moins un numéro de téléphone valide.');
        return redirect()->to('/operation/page-transfert');
    }

    // 3. Calcul de la part unitaire reçue par bénéficiaire
    $montantUnitaire = (float)($montantGlobal / $nbDestinataires);
    if ($montantUnitaire < 100) {
        $session->setFlashdata('erreur', 'Opération refusée : Le montant par destinataire (' . number_format($montantUnitaire, 2, ',', ' ') . ' Ar) est inférieur au minimum requis de 100 Ar.');
        return redirect()->to('/operation/page-transfert');
    }

    // 4. Chargement des profils d'origine émetteur
    $utilisateurModel = new \App\Models\UtilisateurModel();
    $envoyeur = $utilisateurModel->find($idEnvoyeur);
    $soldeEnvoyeur = is_object($envoyeur) ? $envoyeur->solde_utilisateur : $envoyeur['solde_utilisateur'];
    $idOpEnvoyeur  = is_object($envoyeur) ? $envoyeur->id_operateur : $envoyeur['id_operateur'];

    $transfertModel = new \App\Models\TransfertModel();

    // 5. Phase d'analyse de contrôle (Dry-Run)
    $destinatairesValides = [];
    $coutTotalFraisGlobal = 0.00;

    foreach ($listeNumeros as $numero) {
        $destinataire = $transfertModel->chargerDestinataire($numero);
        
        if (!$destinataire) {
            $session->setFlashdata('erreur', "Erreur d'analyse : Le numéro '{$numero}' n'existe pas dans le système. Transaction avortée.");
            return redirect()->to('/operation/page-transfert');
        }

        if ($destinataire['id_utilisateur'] == $idEnvoyeur) {
            $session->setFlashdata('erreur', "Sécurité : Vous ne pouvez pas vous inclure ({$numero}) parmi les destinataires. Transaction avortée.");
            return redirect()->to('/operation/page-transfert');
        }

        // Calcul des frais unitaires appliqués à la tranche
        $fraisUnitaire = $transfertModel->calculerFraisTransfert($montantUnitaire);
        $coutTotalFraisGlobal += $fraisUnitaire;

        $destinatairesValides[] = [
            'data'  => $destinataire,
            'frais' => $fraisUnitaire
        ];
    }

    $totalFactureEnvoyeur = $montantGlobal + $coutTotalFraisGlobal;

    // 6. Contrôle de provision financière globale
    if ($soldeEnvoyeur < $totalFactureEnvoyeur) {
        $msg = 'Solde insuffisant. Il vous faut ' . number_format($totalFactureEnvoyeur, 2, ',', ' ') . ' Ar. ';
        $msg .= '(Montant : ' . number_format($montantGlobal, 2, ',', ' ') . ' Ar + Frais cumulés : ' . number_format($coutTotalFraisGlobal, 2, ',', ' ') . ' Ar inclus).';
        
        $session->setFlashdata('erreur', $msg);
        return redirect()->to('/operation/page-transfert');
    }

    // 7. Persistance atomique unifiée (Tout ou rien)
    $db = \Config\Database::connect();
    $db->transStart();

    $dateActuelle = date('Y-m-d H:i:s');

    foreach ($destinatairesValides as $item) {
        $transfertModel->executerVirementUnitaire(
            (int)$idEnvoyeur,
            (int)$item['data']['id_utilisateur'],
            (float)$montantUnitaire,
            (float)$item['frais'],
            (int)$idOpEnvoyeur,
            (int)$item['data']['id_operateur'],
            $lieu,
            $dateActuelle
        );
    }

    $db->transComplete();

    // 8. Traitement du message de confirmation de sortie
    if ($db->transStatus() !== false) {
        if ($modeTransfert === 'simple') {
            $msgSucces = 'Transfert de ' . number_format($montantGlobal, 2, ',', ' ') . ' Ar envoyé avec succès à ' . esc($destinatairesValides[0]['data']['nom_utilisateur']) . ' (Frais : ' . number_format($coutTotalFraisGlobal, 2, ',', ' ') . ' Ar).';
        } else {
            $msgSucces = "Envoi multiple exécuté avec succès vers {$nbDestinataires} numéros !<br>";
            $msgSucces .= "Chacun a reçu : " . number_format($montantUnitaire, 2, ',', ' ') . " Ar. Frais totaux : " . number_format($coutTotalFraisGlobal, 2, ',', ' ') . " Ar.";
        }
        
        $session->setFlashdata('succes', $msgSucces);
        return redirect()->to('/client');
    } else {
        $session->setFlashdata('erreur', "Une erreur inattendue est survenue lors de l'enregistrement des virements.");
        return redirect()->to('/operation/page-transfert');
    }
}



}
