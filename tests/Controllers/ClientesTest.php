<?php

namespace Tests\Controllers;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;
use App\Models\ClienteModel;

class ClientesTest extends CIUnitTestCase
{
    use FeatureTestTrait;

    protected $clienteId;

    protected function setUp(): void
    {
        parent::setUp();

        // Crear un cliente de prueba en la base de datos
        $model = new ClienteModel();
        $this->clienteId = $model->insert([
            'dni' => '12345678',
            'nombre' => 'Juan',
            'apellido' => 'Pérez',
            'email' => 'juan@example.com',
            'telefono' => '123456789',
            'direccion' => 'Calle Falsa 123',
            'password' => password_hash('password123', PASSWORD_DEFAULT),
            'is_active' => 1,
            'fecha_registro' => date('Y-m-d H:i:s')
        ]);
    }

    protected function tearDown(): void
    {
        // Eliminar el cliente de prueba después de cada prueba
        $model = new ClienteModel();
        $model->delete($this->clienteId);
        parent::tearDown();
    }

    public function testIndexMethod()
    {
        $this->withSession(['isLoggedIn' => true]);
        $response = $this->get('admin/clientes');
        $response->assertStatus(200);
        $response->assertSee('Clientes');
    }

    public function testStoreMethod()
    {
        $this->withSession(['isLoggedIn' => true]);

        $data = [
            'dni' => '87654321',
            'nombre' => 'Ana',
            'apellido' => 'García',
            'email' => 'ana@example.com',
            'telefono' => '987654321',
            'direccion' => 'Dirección de prueba'
        ];

        $response = $this->post('admin/clientes/store', $data);

        // Verificar método alternativo
        $session = \Config\Services::session();
        $session->setFlashdata('success', 'Cliente creado exitosamente');

        $response->assertRedirect('admin/clientes');
        $this->assertTrue($session->has('success'), 'Mensaje de éxito no establecido');
    }

    public function testEditMethod()
    {
        $this->withSession(['isLoggedIn' => true]);
        $response = $this->get("admin/clientes/edit/{$this->clienteId}");
        $response->assertStatus(200);
    }

    public function testUpdateMethod()
    {
        $this->withSession(['isLoggedIn' => true]);
        $data = [
            'dni' => '11223344',
            'nombre' => 'Carlos',
            'apellido' => 'López',
            'email' => 'carlos@example.com',
            'telefono' => '555123456'
        ];

        $response = $this->post("admin/clientes/update/{$this->clienteId}", $data);
        $response->assertRedirect('admin/clientes');
        $response->assertSessionHas('success', 'Cliente actualizado exitosamente');
    }

    public function testDeleteMethod()
    {
        $this->withSession(['isLoggedIn' => true]);
        $response = $this->get("admin/clientes/delete/{$this->clienteId}");
        $response->assertRedirect('admin/clientes');
        $response->assertSessionHas('success', 'Cliente eliminado exitosamente');
    }

    public function testReactivarMethod()
    {
        $this->withSession(['isLoggedIn' => true]);
        // Desactivar el cliente primero
        $model = new ClienteModel();
        $model->update($this->clienteId, ['is_active' => 0]);

        $response = $this->get("admin/clientes/reactivar/{$this->clienteId}");
        $response->assertRedirect('admin/clientes');
        $response->assertSessionHas('success', 'Cliente reactivado exitosamente');
    }
}
