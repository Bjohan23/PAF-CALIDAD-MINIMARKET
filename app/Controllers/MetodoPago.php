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
        $metodosPago = $this->metodoPagoModel->findAll();

        return $this->response->setJSON($metodosPago);
    }
}