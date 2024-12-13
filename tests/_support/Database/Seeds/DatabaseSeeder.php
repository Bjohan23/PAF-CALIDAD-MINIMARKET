<?php

namespace Tests\Support\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call('Tests\Support\Database\Seeds\CategoriaSeeder');
    }
}
