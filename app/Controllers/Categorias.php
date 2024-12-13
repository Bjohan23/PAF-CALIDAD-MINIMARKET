<?php
namespace App\Controllers;

use App\Models\CategoriaModel;

class Categorias extends BaseController
{
    public function index()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }

        $model = new CategoriaModel();
        $data['categorias'] = $model->findAll();

        return view('categorias', $data);
    }

    public function store()
    {
        $model = new CategoriaModel();
        $data = [
            'nombre' => $this->request->getPost('nombre'),
            'descripcion' => $this->request->getPost('descripcion'),
            'created_at' => date('Y-m-d H:i:s'),
        ];
        $model->save($data);
        return redirect()->to(base_url('admin/categorias'));
    }

    public function edit($id)
    {
        $model = new CategoriaModel();
        $data = $model->find($id);
        return $this->response->setJSON($data);
    }

    public function update($id)
    {
        $model = new CategoriaModel();
        $data = [
            'nombre' => $this->request->getPost('nombre'),
            'descripcion' => $this->request->getPost('descripcion'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        $model->update($id, $data);
        return redirect()->to(base_url('admin/categorias'));
    }

    public function delete($id)
    {
        $model = new CategoriaModel();
        $model->delete($id);
        return redirect()->to(base_url('admin/categorias'));
    }
}