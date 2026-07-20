<?php

namespace App\Controllers;

use App\Models\UtilisateurModel;
use App\Models\BaremeModel;

class Home extends BaseController
{
    public function index(): string
    {
        return view('index'); 
    }
}
