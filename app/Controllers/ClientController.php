<?php

namespace App\Controllers;

use App\Models\UtilisateurModel;

class ClientController extends BaseController
{
    public function index()
    {
        $session = session();

        if (empty($session->get('logged_in'))) {
            return redirect()->to('/login');
        }

        $id = $session->get('id_utilisateur');

        $utilisateurModel = new UtilisateurModel();
        $utilisateur = $utilisateurModel->find($id);

        // Récupération des informations de l'utilisateur
        $nom = is_object($utilisateur) ? ($utilisateur->nom_utilisateur ?? null) : ($utilisateur['nom_utilisateur'] ?? null);
        $numero = is_object($utilisateur) ? ($utilisateur->numero_utilisateur ?? null) : ($utilisateur['numero_utilisateur'] ?? null);
        $solde = is_object($utilisateur) ? ($utilisateur->solde_utilisateur ?? null) : ($utilisateur['solde_utilisateur'] ?? null);

        // Appel direct à la méthode du modèle avec une limite de 5 résultats
        $historique = $utilisateurModel->getHistoriqueUtilisateur($id, 5);


        return view('Client/home', [
            'nom' => $nom,
            'numero' => $numero,
            'solde' => $solde,
            'historique' => $historique // Injection des transactions dans la vue
        ]);
    }

    public function historique()
    {
        $session = session();

        if (empty($session->get('logged_in'))) {
            return redirect()->to('/login');
        }

        $id = $session->get('id_utilisateur');

        $utilisateurModel = new \App\Models\UtilisateurModel();
        $utilisateur = $utilisateurModel->find($id);

        $nom = is_object($utilisateur) ? ($utilisateur->nom_utilisateur ?? null) : ($utilisateur['nom_utilisateur'] ?? null);

        // Appel de la méthode du modèle sans paramètre de limite pour tout récupérer
        $historiqueComplet = $utilisateurModel->getHistoriqueUtilisateur($id);

        return view('Client/historique', [
            'nom' => $nom,
            'historique' => $historiqueComplet
        ]);
    }

}
