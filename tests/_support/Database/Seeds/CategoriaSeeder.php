<?php

namespace Tests\Support\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CategoriaSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'nombre' => 'Electrónica',
                'descripcion' => 'Productos electrónicos'
            ],
            [
                'nombre' => 'Ropa',
                'descripcion' => 'Categoría de ropa'
            ]
        ];

        $this->db->table('categoria')->insertBatch($data);
    }
}
