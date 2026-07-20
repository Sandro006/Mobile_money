<?php

namespace App\Controllers;

use App\Models\OperateurModel;
use CodeIgniter\Controller;

class OperateurAuthController extends Controller
{
    public function index()
    {
        if (session()->get('isOperateurLoggedIn')) {
            return redirect()->to('/prefixe');
        }

        return view('operateur/auth');
    }

    public function login()
    {
        $rules = [
            'nom' => 'required',
            'mdp' => 'required',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Veuillez remplir tous les champs.');
        }

        $nom = $this->request->getPost('nom');
        $mdp = $this->request->getPost('mdp');

        $operateurModel = new OperateurModel();
        $operateur = $operateurModel->where('nom', $nom)->first();

        if (! $operateur || $operateur['mdp'] !== $mdp) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Nom ou mot de passe incorrect.');
        }

        session()->set([
            'operateur_id'      => $operateur['id'],
            'operateur_nom'     => $operateur['nom'],
            'operateur_libelle' => $operateur['libelle'],
            'isOperateurLoggedIn' => true,
        ]);

        return redirect()->to('/prefixe')->with('success', 'Connexion réussie.');
    }

    public function logout()
    {
        session()->remove(['operateur_id', 'operateur_nom', 'operateur_libelle', 'isOperateurLoggedIn']);
        session()->destroy();

        return redirect()->to('/operateur/auth')->with('success', 'Vous êtes déconnecté.');
    }
}