<?php

namespace App\Models;

use CodeIgniter\Model;

// Modelo de Seguimiento de Pedido
class SeguimientoPedidoModel extends Model
{
    protected $table = 'seguimiento_pedido';
    protected $primaryKey = 'id_seguimiento';
    protected $allowedFields = [
        'id_venta',
        'estado',
        'comentario',
        'fecha_actualizacion'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'fecha_actualizacion';
    protected $updatedField = 'fecha_actualizacion';

    // Obtener historial de seguimiento de una venta
    public function getHistorialVenta($id_venta)
    {
        return $this->where('id_venta', $id_venta)
            ->orderBy('fecha_actualizacion', 'DESC')
            ->findAll();
    }

    // Agregar nuevo estado
    public function agregarEstado($id_venta, $estado, $comentario = null)
    {
        return $this->insert([
            'id_venta' => $id_venta,
            'estado' => $estado,
            'comentario' => $comentario
        ]);
    }
}
