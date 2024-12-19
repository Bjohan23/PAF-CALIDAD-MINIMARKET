<?php

namespace App\Models;

use CodeIgniter\Model;

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
        'is_active',
        'token_recuperacion'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'fecha_registro';
    protected $updatedField = false;

    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];

    // Modificar las reglas de validación
    protected $validationRules = [
        'nombre' => [
            'rules' => 'required|min_length[2]|max_length[100]',
            'errors' => [
                'required' => 'El nombre es requerido',
                'min_length' => 'El nombre debe tener al menos 2 caracteres',
                'max_length' => 'El nombre no puede exceder los 100 caracteres'
            ]
        ],
        'apellido' => [
            'rules' => 'required|min_length[2]|max_length[100]',
            'errors' => [
                'required' => 'El apellido es requerido',
                'min_length' => 'El apellido debe tener al menos 2 caracteres',
                'max_length' => 'El apellido no puede exceder los 100 caracteres'
            ]
        ],
        'email' => [
            'rules' => 'required|valid_email|is_unique[cliente.email,id_cliente,{id_cliente}]',
            'errors' => [
                'required' => 'El email es requerido',
                'valid_email' => 'Debe ingresar un email válido',
                'is_unique' => 'Este email ya está registrado'
            ]
        ],
        'dni' => [
            'rules' => 'required|min_length[8]|max_length[20]|is_unique[cliente.dni,id_cliente,{id_cliente}]',
            'errors' => [
                'required' => 'El DNI es requerido',
                'min_length' => 'El DNI debe tener al menos 8 caracteres',
                'max_length' => 'El DNI no puede exceder los 20 caracteres',
                'is_unique' => 'Este DNI ya está registrado'
            ]
        ],
        'password' => [
            'rules' => 'required|min_length[6]',
            'errors' => [
                'required' => 'La contraseña es requerida',
                'min_length' => 'La contraseña debe tener al menos 6 caracteres'
            ]
        ]
    ];

    protected function hashPassword(array $data)
    {
        if (!empty($data['data']['password'])) {
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        }
        return $data;
    }

    // Método para verificar si existe un email
    public function emailExists($email, $excludeId = null)
    {
        $builder = $this->builder();
        $builder->where('email', $email);
        if ($excludeId) {
            $builder->where('id_cliente !=', $excludeId);
        }
        return $builder->countAllResults() > 0;
    }

    // Método para verificar si existe un DNI
    public function dniExists($dni, $excludeId = null)
    {
        $builder = $this->builder();
        $builder->where('dni', $dni);
        if ($excludeId) {
            $builder->where('id_cliente !=', $excludeId);
        }
        return $builder->countAllResults() > 0;
    }
}