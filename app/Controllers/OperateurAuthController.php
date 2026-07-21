<?php

namespace App\Controllers;

use App\Models\OperateurModel;
use CodeIgniter\Controller;

class OperateurAuthController extends Controller
{
    public function index()
    {
        // Si déjà connecté, on redirige directement vers le dashboard
        if (session()->get('isOperateurLoggedIn')) {
            return redirect()->to('/operateur/dashboard');
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

        // NOTE: le mdp est en clair dans vos données de test.
        // En production, stockez un hash (password_hash) et vérifiez avec password_verify().
        if (! $operateur || $operateur['mdp'] !== $mdp) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Nom ou mot de passe incorrect.');
        }

        // Stockage de l'opérateur en session
        session()->set([
            'operateur_id'      => $operateur['id'],
            'operateur_nom'     => $operateur['nom'],
            'operateur_libelle' => $operateur['libelle'],
            'isOperateurLoggedIn' => true,
        ]);

        return redirect()->to('/operateur/dashboard')->with('success', 'Connexion réussie.');
    }

    public function logout()
    {
        session()->remove(['operateur_id', 'operateur_nom', 'operateur_libelle', 'isOperateurLoggedIn']);
        session()->destroy();

        return redirect()->to('/operateur/auth')->with('success', 'Vous êtes déconnecté.');
    }
}
