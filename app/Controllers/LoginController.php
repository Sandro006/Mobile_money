<?php

namespace App\Controllers;

use App\Models\UtilisateurModel;
use CodeIgniter\HTTP\RedirectResponse;

class LoginController extends BaseController
{
    public function index()
    {
        return view('auth/login');
    }

    public function submit()
    {
        $numero = trim((string) $this->request->getPost('numero_utilisateur'));

        // Validation minimale
        if ($numero === '') {
            return view('auth/login', ['error' => 'Veuillez saisir votre numéro de téléphone.']);
        }

        $utilisateurModel = new UtilisateurModel();
        $utilisateur = $utilisateurModel->where('numero_utilisateur', $numero)->first();

        if (!$utilisateur) {
            return view('auth/login', ['error' => 'Numéro introuvable.']);
        }

        // Session
        $session = session();
        $session->set([
            'logged_in' => true,
            'id_utilisateur' => $utilisateur['id_utilisateur'],
            'numero_utilisateur' => $utilisateur['numero_utilisateur'],
            'nom_utilisateur' => $utilisateur['nom_utilisateur'],
        ]);

        return redirect()->to('/client');
    }

    public function logout(): RedirectResponse
    {
        $session = session();
        $session->destroy();

        return redirect()->to('/login');
    }
}


