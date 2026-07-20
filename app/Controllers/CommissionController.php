<?php

namespace App\Controllers;

use App\Models\ConfigurationInteropModel;
use App\Models\OperateurModel;

class CommissionController extends BaseController
{
    protected $configInteropModel;
    protected $operateurModel;

    public function __construct()
    {
        $this->configInteropModel = new ConfigurationInteropModel();
        $this->operateurModel     = new OperateurModel();
    }

    // Afficher la page de configuration de la commission
    public function index()
    {
        $idOperateur = session()->get('operateur_id');
        if (!$idOperateur) {
            return redirect()->to('/operateur/auth')->with('error', 'Veuillez vous connecter.');
        }

        $operateur = $this->operateurModel->find($idOperateur);
        if (!$operateur) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Opérateur introuvable');
        }

        // Récupérer la dernière configuration de commission pour cet opérateur (historique)
        $lastConfig = $this->configInteropModel
            ->where('id_operateur', $idOperateur)
            ->orderBy('id_config', 'DESC')
            ->first();

        $data['operateur']             = $operateur;
        $data['commission_actuelle']   = $lastConfig['taux_commission_autre_operateur'] ?? null;
        $data['historique_commissions'] = $this->configInteropModel
            ->where('id_operateur', $idOperateur)
            ->orderBy('id_config', 'DESC')
            ->findAll();

        return view('operateur/commission/index', $data);
    }

    // Mettre à jour le pourcentage (ajoute une nouvelle ligne dans l'historique)
    public function update()
    {
        $idOperateur = session()->get('operateur_id');
        if (!$idOperateur) {
            return redirect()->to('/operateur/auth')->with('error', 'Veuillez vous connecter.');
        }

        $rules = [
            'commission_pourcent' => 'required|numeric|greater_than_equal_to[0]|less_than_equal_to[100]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $pourcent = $this->request->getPost('commission_pourcent');

        // Ajouter une nouvelle entrée dans configuration_interop (historique conservé)
        $this->configInteropModel->save([
            'id_operateur'                    => $idOperateur,
            'taux_commission_autre_operateur' => $pourcent,
        ]);

        return redirect()->to('/commission')->with('success', 'Pourcentage de commission mis à jour avec succès.');
    }
}
