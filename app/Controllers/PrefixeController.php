<?php

namespace App\Controllers;

use App\Models\PrefixeModel;

class PrefixeController extends BaseController
{
    protected $prefixeModel;
    protected $helpers = ['form', 'url'];

    public function __construct()
    {
        $this->prefixeModel = new PrefixeModel();
    }

   
    public function index()
    {
        $data['prefixes'] = $this->prefixeModel->findAll();
        return view('operateur/prefixe/index', $data);
    }

  
    public function create()
    {
        return view('operateur/prefixe/create');
    }

   
    public function store()
    {
        $rules = [
            'num_prefixe' => 'required|is_unique[prefixe.num_prefixe]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->prefixeModel->save([
            'num_prefixe' => $this->request->getPost('num_prefixe'),
        ]);

        return redirect()->to('/prefixe')->with('success', 'Préfixe ajouté avec succès.');
    }

 
    public function show($id)
    {
        $data['prefixe'] = $this->prefixeModel->find($id);
        if (! $data['prefixe']) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Préfixe introuvable');
        }
        return view('operateur/prefixe/show', $data);
    }

   
    public function edit($id)
    {
        $data['prefixe'] = $this->prefixeModel->find($id);
        if (! $data['prefixe']) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Préfixe introuvable');
        }
        return view('operateur/prefixe/edit', $data);
    }

   
    public function update($id)
    {
        $rules = [
            'num_prefixe' => "required|is_unique[prefixe.num_prefixe,id_prefixe,{$id}]",
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->prefixeModel->update($id, [
            'num_prefixe' => $this->request->getPost('num_prefixe'),
        ]);

        return redirect()->to('/prefixe')->with('success', 'Préfixe mis à jour avec succès.');
    }


    public function delete($id)
    {
        $prefixe = $this->prefixeModel->find($id);
        if (! $prefixe) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Préfixe introuvable');
        }

        $this->prefixeModel->delete($id);
        return redirect()->to('/prefixe')->with('success', 'Préfixe supprimé avec succès.');
    }
}