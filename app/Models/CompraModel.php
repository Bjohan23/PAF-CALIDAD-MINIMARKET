<?php
namespace App\Models;

use CodeIgniter\Model;

class CompraModel extends Model
{
    protected $table = 'compra';
    protected $primaryKey = 'id_compra';
    protected $allowedFields = ['id_proveedor', 'id_usuario', 'fecha_compra', 'total', 'estado'];
}
