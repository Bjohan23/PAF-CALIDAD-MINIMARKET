<?php

namespace Tests;

use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\CIUnitTestCase;

class DatabaseSeeder extends CIUnitTestCase
{
    use DatabaseTestTrait;

    public function setUp(): void
    {
        parent::setUp();

        // Limpia las tablas antes de cada test
        $this->db->query('DELETE FROM cliente');
        $this->db->query('DELETE FROM categoria');
        $this->db->query('DELETE FROM producto');

        // Inserta datos iniciales
        $this->db->query("
            INSERT INTO cliente (nombre, apellido, email, password) 
            VALUES ('Juan', 'Perez', 'juan@example.com', '1234')
        ");

        $this->db->query("
            INSERT INTO categoria (nombre, descripcion) 
            VALUES ('Electrónica', 'Categoría de productos electrónicos')
        ");

        $this->db->query("
            INSERT INTO producto (id_categoria, nombre, slug, precio) 
            VALUES (1, 'Laptop', 'laptop-123', 1500.00)
        ");
    }
}
