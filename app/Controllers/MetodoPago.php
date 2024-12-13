<?php

namespace App\Controllers;

use App\Models\MetodoPagoModel;

class MetodoPago extends BaseController
{
    protected $metodoPagoModel;

    public function __construct()
    {
        $this->metodoPagoModel = new MetodoPagoModel();
    }

    public function getMetodoPago()
    {
        $metodosPago = $this->metodoPagoModel->where('is_active', 1)->findAll();

        if (empty($metodosPago)) {
            return $this->response->setJSON([]);
        }

        return $this->response->setJSON($metodosPago);
    }
    public function create()
    {
        $data = [
            'nombre' => $this->request->getPost('nombre'),
            'descripcion' => $this->request->getPost('descripcion'),
            'instrucciones' => $this->request->getPost('instrucciones'),
            'is_active' => $this->request->getPost('is_active')
        ];

        $this->metodoPagoModel->save($data);

        return $this->response->setJSON(['success' => true]);
    }
}
