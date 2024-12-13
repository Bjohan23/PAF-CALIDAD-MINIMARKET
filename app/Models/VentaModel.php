<?php
namespace App\Models;

use CodeIgniter\Model;

class VentaModel extends Model
{
    protected $table = 'venta';
    protected $primaryKey = 'id_venta';
    protected $useTimestamps = false; // Cambiar a false
    protected $allowedFields = [
        'id_cliente',
        'id_usuario',
        'metodo_pago_id',
        'fecha_venta',
        'tipo_comprobante',
        'numero_comprobante',
        'total',
        'estado',
        'estado_pago',
        'referencia_pago',
        'direccion_envio',
        'costo_envio',
        'notas'
    ];

    // Define los tipos de datos para cada campo
    protected $validationRules = [
        'id_cliente' => 'required|numeric',
        'id_usuario' => 'required|numeric',
        'metodo_pago_id' => 'required|numeric',
        'tipo_comprobante' => 'required',
        'numero_comprobante' => 'required',
        'total' => 'required|numeric'
    ];
}