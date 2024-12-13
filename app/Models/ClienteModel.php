<?php
namespace App\Models;

use CodeIgniter\Model;

// Modelo de Cliente actualizado
class ClienteModel extends Model
{
    protected $table = 'cliente';
    protected $primaryKey = 'id_cliente';
    protected $allowedFields = [
        'nombre', 
        'apellido', 
        'dni', 
        'telefono', 
        'email', 
        'direccion',
        'password',
        'fecha_registro',
        'ultimo_login',
        'is_active',
        'token_recuperacion'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'fecha_registro';
    protected $updatedField = 'ultimo_login';
}
