<?php
namespace App\Models;

use CodeIgniter\Model;

class UsuarioModel extends Model
{
    protected $table = 'usuario';
    protected $primaryKey = 'id_usuario';
    protected $allowedFields = ['nombre', 'apellido', 'dni', 'telefono', 'email', 'contraseña', 'tipo_usuario', 'is_active', 'created_at'];
    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];

    protected function hashPassword(array $data)
    {
        if (isset($data['data']['contraseña'])) {
            $data['data']['contraseña'] = password_hash($data['data']['contraseña'], PASSWORD_DEFAULT);
        }
        return $data;
    }
}
