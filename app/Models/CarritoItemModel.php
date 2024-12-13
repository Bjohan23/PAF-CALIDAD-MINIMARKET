<?php
namespace App\Models;

use CodeIgniter\Model;
// Modelo de Items del Carrito
class CarritoItemModel extends Model
{
    protected $table = 'carrito_item';
    protected $primaryKey = 'id_carrito_item';
    protected $allowedFields = [
        'id_carrito',
        'id_producto',
        'cantidad',
        'precio_unitario',
        'subtotal'
    ];

    // Método para calcular subtotal
    protected function calculateSubtotal($cantidad, $precio)
    {
        return $cantidad * $precio;
    }

    // Método para actualizar item
    public function actualizarItem($id_carrito_item, $cantidad, $precio)
    {
        $subtotal = $this->calculateSubtotal($cantidad, $precio);
        return $this->update($id_carrito_item, [
            'cantidad' => $cantidad,
            'precio_unitario' => $precio,
            'subtotal' => $subtotal
        ]);
    }
}
