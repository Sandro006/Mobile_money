<?php

namespace App\Controllers;

use App\Models\UtilisateurModel;
use App\Models\BaremeModel;

class Home extends BaseController
{
    public function index(): string
    {
        $utilisateurModel = new UtilisateurModel();

        $utilisateurs = $utilisateurModel->getAll();

        return view('index', ['utilisateurs' => $utilisateurs]);

    }
}
