<?php

namespace App\Controllers;

use App\Models\BaremeModel;
use App\Models\FraisModel;

class BaremeController extends BaseController
{
    protected $baremeModel;
    protected $fraisModel;
    protected $helpers = ['form', 'url'];

    public function __construct()
    {
        $this->baremeModel = new BaremeModel();
        $this->fraisModel = new FraisModel();
    }

    /**
     * Liste des barèmes avec le dernier montant de frais
     */
    public function index()
    {
        // Récupérer tous les barèmes
        $baremes = $this->baremeModel->findAll();

        // Pour chaque barème, récupérer le dernier frais
        foreach ($baremes as &$bareme) {
            $dernierFrais = $this->fraisModel
                ->where('id_bareme', $bareme['id_bareme'])
                ->orderBy('date_frais', 'DESC')
                ->first();
            $bareme['montant_frais'] = $dernierFrais ? $dernierFrais['montant_frais'] : null;
        }

        $data['baremes'] = $baremes;
        return view('operateur/bareme/index', $data);
    }

    /**
     * Formulaire de modification du montant des frais pour un barème
     */
    public function edit($id)
    {
        $bareme = $this->baremeModel->find($id);
        if (!$bareme) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Barème introuvable');
        }

        // Récupérer le dernier montant pour pré-remplir
        $dernierFrais = $this->fraisModel
            ->where('id_bareme', $id)
            ->orderBy('date_frais', 'DESC')
            ->first();
        $bareme['montant_frais'] = $dernierFrais ? $dernierFrais['montant_frais'] : null;

        $data['bareme'] = $bareme;
        return view('operateur/bareme/edit', $data);
    }

    /**
     * Met à jour le montant des frais pour un barème (ajoute un nouvel enregistrement)
     */
    public function update($id)
    {
        $rules = [
            'montant_frais' => 'required|numeric|greater_than[0]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $montant = $this->request->getPost('montant_frais');

        // Vérifier que le barème existe
        $bareme = $this->baremeModel->find($id);
        if (!$bareme) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Barème introuvable');
        }

        // Insérer un nouveau frais avec la date actuelle
        $this->fraisModel->insert([
            'id_bareme'    => $id,
            'montant_frais' => $montant,
            'date_frais'   => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/bareme')->with('success', 'Montant des frais mis à jour avec succès.');
    }
}