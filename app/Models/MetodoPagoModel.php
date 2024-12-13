<?php
namespace App\Models;

use CodeIgniter\Model;

// Modelo de Método de Pago
class MetodoPagoModel extends Model
{
    protected $table = 'metodo_pago';
    protected $primaryKey = 'id_metodo_pago';
    protected $allowedFields = [
        'nombre',
        'descripcion',
        'instrucciones',
        'is_active'
    ];

    // Obtener métodos de pago activos
    public function getActiveMethods()
    {
        return $this->where('is_active', 1)->findAll();
    }
}
