<?php

namespace Tests\Support\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCategoriasTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_categoria' => [
                'type'           => 'INT',
                'constraint'     => 5,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nombre' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => false,
            ],
            'descripcion' => [
                'type' => 'TEXT',
                'null' => true,
            ]
        ]);
        
        $this->forge->addKey('id_categoria', true);
        $this->forge->addUniqueKey('nombre', 'uk_categoria_nombre');
        $this->forge->createTable('tests_categoria');
    }

    public function down()
    {
        $this->forge->dropTable('tests_categoria');
    }
}