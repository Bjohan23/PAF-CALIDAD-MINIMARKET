<?php
namespace App\Models;

use CodeIgniter\Model;
class CarritoModel extends Model
{
    protected $table = 'carrito';
    protected $primaryKey = 'id_carrito';
    protected $allowedFields = [
        'id_cliente',
        'fecha_creacion',
        'ultima_modificacion'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'fecha_creacion';
    protected $updatedField = 'ultima_modificacion';
}