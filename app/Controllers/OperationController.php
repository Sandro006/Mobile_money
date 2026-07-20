<?php

namespace App\Controllers;

use App\Models\DepotModel;

class OperationController extends BaseController
{
    public function pageDepot()
    {
        if (empty(session()->get('logged_in'))) {
            return redirect()->to('/login');
        }
        return view('operation/depot');
    }

    public function pageRetrait()
    {
        if (empty(session()->get('logged_in'))) {
            return redirect()->to('/login');
        }
        return view('operation/retrait');
    }

    public function depot()
    {
        $session = session();

        if (empty($session->get('logged_in'))) {
            return redirect()->to('/login');
        }

        $idUtilisateur = $session->get('id_utilisateur');
        $montant = $this->request->getPost('montant');
        $lieu = $this->request->getPost('lieu') ?: 'Guichet';

        // Validation simple du montant reçu
        if (empty($montant) || !is_numeric($montant) || $montant <= 0) {
            $session->setFlashdata('erreur', 'Le montant du dépôt doit être supérieur à 0 Ar.');
            return redirect()->to('/operation/page-depot');
        }

        // Appel de la logique métier déléguée au modèle
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

    public function retrait()
    {
        $session = session();

        if (empty($session->get('logged_in'))) {
            return redirect()->to('/login');
        }

        $idUtilisateur = $session->get('id_utilisateur');
        $montant = $this->request->getPost('montant');
        $lieu = $this->request->getPost('lieu') ?: 'Kiosque';

        // Validation de base
        if (empty($montant) || !is_numeric($montant) || $montant <= 0) {
            $session->setFlashdata('erreur', 'Le montant du retrait doit être supérieur à 0 Ar.');
            return redirect()->to('/operation/page-retrait');
        }

        $retraitModel = new \App\Models\RetraitModel();
        
        // Calcul dynamique des frais via le barème
        $frais = $retraitModel->calculerFrais((float)$montant);

        // Vérification de la provision du solde (Montant retiré + Frais appliqués)
        $utilisateurModel = new \App\Models\UtilisateurModel();
        $utilisateur = $utilisateurModel->find($idUtilisateur);
        $soldeActuel = is_object($utilisateur) ? $utilisateur->solde_utilisateur : $utilisateur['solde_utilisateur'];

        if ($soldeActuel < ($montant + $frais)) {
            $session->setFlashdata('erreur', 'Solde insuffisant. Il vous faut au moins ' . number_format($montant + $frais, 2, ',', ' ') . ' Ar (frais inclus) pour valider cette opération.');
            return redirect()->to('/operation/page-retrait');
        }

        // Exécution de l'opération
        $succes = $retraitModel->executerRetrait($idUtilisateur, (float)$montant, $frais, $lieu);

        if ($succes) {
            $session->setFlashdata('succes', 'Retrait de ' . number_format($montant, 2, ',', ' ') . ' Ar effectué (Frais : ' . number_format($frais, 2, ',', ' ') . ' Ar).');
            return redirect()->to('/client');
        } else {
            $session->setFlashdata('erreur', 'Une erreur technique est survenue lors du retrait.');
            return redirect()->to('/operation/page-retrait');
        }
    }
    

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

        $idEnvoyeur = $session->get('id_utilisateur');
        $numeroDestinataire = $this->request->getPost('numero_destinataire');
        $montant = $this->request->getPost('montant');
        $lieu = $this->request->getPost('lieu') ?: 'Mobile App';

        // 1. Validations de base des entrées
        if (empty($montant) || !is_numeric($montant) || $montant <= 0) {
            $session->setFlashdata('erreur', 'Le montant du transfert doit être supérieur à 0 Ar.');
            return redirect()->to('/operation/page-transfert');
        }

        if (empty($numeroDestinataire)) {
            $session->setFlashdata('erreur', 'Le numéro du destinataire est requis.');
            return redirect()->to('/operation/page-transfert');
        }

        $transfertModel = new \App\Models\TransfertModel();

        // 2. Recherche et validation du destinataire
        $destinataire = $transfertModel->chargerDestinataire($numeroDestinataire);
        if (!$destinataire) {
            $session->setFlashdata('erreur', "Le numéro destinataire n'est pas attribué à un compte e-Money.");
            return redirect()->to('/operation/page-transfert');
        }

        $idRecepteur = $destinataire['id_utilisateur'];

        // Sécurité : Empêcher de s'envoyer de l'argent à soi-même
        if ($idEnvoyeur == $idRecepteur) {
            $session->setFlashdata('erreur', "Vous ne pouvez pas effectuer un transfert vers votre propre numéro.");
            return redirect()->to('/operation/page-transfert');
        }

        // 3. Calcul des frais et vérification du solde de l'envoyeur
        $frais = $transfertModel->calculerFrais((float)$montant);

        $utilisateurModel = new \App\Models\UtilisateurModel();
        $envoyeur = $utilisateurModel->find($idEnvoyeur);
        $soldeEnvoyeur = is_object($envoyeur) ? $envoyeur->solde_utilisateur : $envoyeur['solde_utilisateur'];

        if ($soldeEnvoyeur < ($montant + $frais)) {
            $session->setFlashdata('erreur', 'Solde insuffisant. Il vous faut au moins ' . number_format($montant + $frais, 2, ',', ' ') . ' Ar (frais de ' . number_format($frais, 2, ',', ' ') . ' Ar inclus) pour envoyer ce montant.');
            return redirect()->to('/operation/page-transfert');
        }

        // 4. Exécution globale du transfert
        $succes = $transfertModel->executerTransfert($idEnvoyeur, $idRecepteur, (float)$montant, $frais, $lieu);

        if ($succes) {
            $session->setFlashdata('succes', 'Transfert de ' . number_format($montant, 2, ',', ' ') . ' Ar envoyé avec succès à ' . esc($destinataire['nom_utilisateur']) . ' (Frais : ' . number_format($frais, 2, ',', ' ') . ' Ar).');
            return redirect()->to('/client');
        } else {
            $session->setFlashdata('erreur', 'Une erreur technique est survenue lors de la validation du transfert.');
            return redirect()->to('/operation/page-transfert');
        }
    }


}
