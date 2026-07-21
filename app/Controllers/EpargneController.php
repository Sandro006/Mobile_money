<?php

namespace App\Controllers;

use App\Models\EpargneModel;

class EpargneController extends BaseController{

    public function index()
    {
        // Si déjà connecté, on redirige directement vers le dashboard
        if (session()->get('isOperateurLoggedIn')) {
            return redirect()->to('/operateur/dashboard');
        }

        return view('Client/Epargne');
    }
    public function Sauver(){

        $session = session();
        $EpargneModel = new EpargneModel();

        if(session()->get('isOperateurLoggedIn')) {
            return redirect()->to('/operateur/dashboard');
        }

        $idUtilisateur = $session->get('id_utilisateur');
        $pourcentage = $this->request->getPost('pourcentage');

        $succes = $EpargneModel->insertPourcentage($idUtilisateur, $pourcentage);

         return view('client/Epargne');


    }

}
