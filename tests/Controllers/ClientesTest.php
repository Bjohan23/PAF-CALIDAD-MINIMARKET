<?php

namespace Tests\Controllers;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;
use CodeIgniter\Test\DatabaseTestTrait;
use App\Models\ClienteModel;
use App\Models\VentaModel;

class ClientesTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $refresh = false;
    protected $migrate = false;
    protected $clienteModel;
    protected $ventaModel;
    protected $DBGroup = 'default';
    protected $cliente_id;

    protected function setUp(): void
    {
        parent::setUp();

        $this->clienteModel = new ClienteModel();
        $this->ventaModel = new VentaModel();

        // Limpiar datos existentes
        $this->cleanTestData();

        // Insertar cliente de prueba
        $this->insertTestData();
    }

    private function cleanTestData()
    {
        $this->clienteModel->where('email', 'cliente.test@example.com')->delete();
        $this->clienteModel->where('email', 'nuevo.cliente@example.com')->delete();
        $this->clienteModel->where('email', 'cliente.actualizado@example.com')->delete();
    }
    private function insertTestData()
    {
        $data = [
            'dni' => '12345678',
            'nombre' => 'Cliente',
            'apellido' => 'Test',
            'email' => 'cliente.test@example.com',
            'telefono' => '123456789',
            'direccion' => 'Dirección de prueba',
            'password' => password_hash('password123', PASSWORD_DEFAULT),
            'is_active' => 1,
            'fecha_registro' => date('Y-m-d H:i:s')
        ];

        // Verificar si ya existe antes de insertar
        if (!$this->clienteModel->where('email', $data['email'])->first()) {
            $this->cliente_id = $this->clienteModel->insert($data);
        } else {
            $existingClient = $this->clienteModel->where('email', $data['email'])->first();
            $this->cliente_id = $existingClient['id_cliente'];
        }
    }
    public function tearDown(): void
    {
        $this->cleanTestData();
        parent::tearDown();
    }

    public function testIndexRedirectsIfNotLoggedIn()
    {
        $result = $this->get('admin/clientes');
        $result->assertRedirectTo(base_url('admin/login'));
    }
    public function testIndexDisplaysClientsWhenLoggedIn()
    {
        $result = $this->withSession([
            'isLoggedIn' => true,
            'id' => 1,
            'tipo_usuario' => 'administrador'
        ])->get('admin/clientes');

        $result->assertOK();
        $result->assertSee('Cliente'); // Busca solo el nombre
        $result->assertSee('Test');    // Busca solo el apellido
    }

    public function testStoreCreatesNewClient()
    {
        $data = [
            'dni' => '87654321',
            'nombre' => 'Nuevo',
            'apellido' => 'Cliente',
            'email' => 'nuevo.cliente@example.com',
            'telefono' => '987654321',
            'direccion' => 'Nueva dirección de prueba'
        ];

        $result = $this->withSession([
            'isLoggedIn' => true,
            'id' => 1,
            'tipo_usuario' => 'administrador'
        ])->post('admin/clientes/store', $data);

        // Verificar que el cliente existe
        $newClient = $this->clienteModel->where('email', 'nuevo.cliente@example.com')->first();
        $this->assertNotNull($newClient, 'El cliente no fue creado');

        $result->assertRedirectTo(base_url('admin/clientes'));
    }
    public function testDeleteWithSalesDeactivatesClient()
    {
        $cliente = $this->clienteModel->where('email', 'cliente.test@example.com')->first();

        // Crear una venta asociada
        $ventaData = [
            'id_cliente' => $cliente['id_cliente'],
            'id_usuario' => 1,
            'fecha_venta' => date('Y-m-d H:i:s'),
            'tipo_comprobante' => 'boleta',
            'numero_comprobante' => 'B001-00001',
            'total' => 100.00,
            'estado' => 'completada',
            'estado_pago' => 'pagado'
        ];
        $this->ventaModel->insert($ventaData);

        $result = $this->withSession([
            'isLoggedIn' => true,
            'id' => 1,
            'tipo_usuario' => 'administrador'
        ])->get("admin/clientes/delete/{$cliente['id_cliente']}");

        // Verificar que el cliente fue desactivado
        $deactivatedClient = $this->clienteModel->find($cliente['id_cliente']);
        $this->assertEquals(0, $deactivatedClient['is_active'], 'El cliente no fue desactivado');

        $result->assertRedirectTo(base_url('admin/clientes'));
    }


    public function testEditReturnsClientDataAsJson()
    {
        $cliente = $this->clienteModel->where('email', 'cliente.test@example.com')->first();

        $result = $this->withSession([
            'isLoggedIn' => true,
            'id' => 1,
            'tipo_usuario' => 'administrador'
        ])->get("admin/clientes/edit/{$cliente['id_cliente']}");

        $result->assertOK();
        $jsonResponse = json_decode($result->getJSON(), true);

        $this->assertTrue($jsonResponse['success']);
        $this->assertEquals($cliente['nombre'], $jsonResponse['data']['nombre']);
        $this->assertEquals($cliente['email'], $jsonResponse['data']['email']);
        $this->assertArrayNotHasKey('password', $jsonResponse['data']);
    }

    public function testUpdateModifiesExistingClient()
    {
        $cliente = $this->clienteModel->where('email', 'cliente.test@example.com')->first();

        $data = [
            'dni' => '87654321',
            'nombre' => 'Cliente',
            'apellido' => 'Actualizado',
            'email' => 'cliente.actualizado@example.com',
            'telefono' => '987654321',
            'direccion' => 'Dirección actualizada',
            'is_active' => 1
        ];

        $result = $this->withSession([
            'isLoggedIn' => true,
            'id' => 1,
            'tipo_usuario' => 'administrador'
        ])->post("admin/clientes/update/{$cliente['id_cliente']}", $data);

        $this->seeInDatabase('cliente', ['email' => 'cliente.actualizado@example.com']);
        $result->assertRedirectTo(base_url('admin/clientes'));
        $result->assertSessionHas('success');
    }
    public function testDeleteWithNoSalesRemovesClient()
    {
        $cliente = $this->clienteModel->where('email', 'cliente.test@example.com')->first();

        $result = $this->withSession([
            'isLoggedIn' => true,
            'id' => 1,
            'tipo_usuario' => 'administrador'
        ])->get("admin/clientes/delete/{$cliente['id_cliente']}");

        $this->dontSeeInDatabase('cliente', ['id_cliente' => $cliente['id_cliente']]);
        $result->assertRedirectTo(base_url('admin/clientes'));
        $result->assertSessionHas('success');
    }

    public function testReactivateClient()
    {
        $cliente = $this->clienteModel->where('email', 'cliente.test@example.com')->first();

        // Primero desactivar
        $this->clienteModel->update($cliente['id_cliente'], ['is_active' => 0]);

        $result = $this->withSession([
            'isLoggedIn' => true,
            'id' => 1,
            'tipo_usuario' => 'administrador'
        ])->get("admin/clientes/reactivar/{$cliente['id_cliente']}");

        $this->seeInDatabase('cliente', [
            'id_cliente' => $cliente['id_cliente'],
            'is_active' => 1
        ]);
        $result->assertRedirectTo(base_url('admin/clientes'));
        $result->assertSessionHas('success');
    }
}
