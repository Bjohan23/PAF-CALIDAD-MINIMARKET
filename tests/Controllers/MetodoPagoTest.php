<?php

namespace Tests\Controllers;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;
use App\Models\MetodoPagoModel;
use App\Models\VentaModel;
use Config\Services;
use CodeIgniter\Database\Exceptions\DatabaseException;

class MetodoPagoTest extends CIUnitTestCase
{
    use FeatureTestTrait; // Trait que permite realizar pruebas funcionales simulando solicitudes HTTP

    protected $db; // Conexión a la base de datos
    protected $metodoPagoModel; // Modelo para la tabla `metodo_pago`
    protected $ventaModel; // Modelo para la tabla `venta`
    protected $insertedIds = []; // Almacena los IDs de los datos insertados para su limpieza posterior

    /**
     * Configuración inicial antes de cada prueba.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Conexión a la base de datos del entorno de pruebas
        $this->db = db_connect($this->DBGroup ?? 'tests');
        $this->metodoPagoModel = new MetodoPagoModel();
        $this->ventaModel = new VentaModel();

        $this->cleanup(); // Limpia los datos de pruebas previos
        $this->setupTestData(); // Configura datos necesarios para las pruebas
    }

    /**
     * Inserta datos iniciales en las tablas para realizar pruebas.
     */
    protected function setupTestData()
    {
        try {
            // Crear un método de pago de prueba
            $metodoPago = [
                'nombre' => 'Método Test',
                'descripcion' => 'Método de pago para pruebas',
                'is_active' => 1
            ];
            $this->db->table('metodo_pago')->insert($metodoPago);
            $idMetodoPago = $this->db->insertID();
            $this->insertedIds['metodo_pago'] = $idMetodoPago;

            // Crear un cliente de prueba
            $cliente = [
                'nombre' => 'Cliente Test',
                'apellido' => 'Test',
                'email' => 'cliente@test.com',
                'password' => password_hash('password', PASSWORD_DEFAULT), // Hash de contraseña
                'is_active' => 1,
                'fecha_registro' => date('Y-m-d H:i:s')
            ];
            $this->db->table('cliente')->insert($cliente);
            $this->insertedIds['cliente'] = $this->db->insertID();

            // Crear un usuario de prueba
            $usuario = [
                'nombre' => 'Usuario Test',
                'apellido' => 'Test',
                'dni' => '12345678',
                'email' => 'test@test.com',
                'contraseña' => password_hash('password', PASSWORD_DEFAULT), // Hash de contraseña
                'tipo_usuario' => 'administrador',
                'is_active' => 1
            ];
            $this->db->table('usuario')->insert($usuario);
            $this->insertedIds['usuario'] = $this->db->insertID();

            // Crear una venta de prueba relacionada con los datos anteriores
            $venta = [
                'id_cliente' => $this->insertedIds['cliente'],
                'id_usuario' => $this->insertedIds['usuario'],
                'metodo_pago_id' => $idMetodoPago,
                'tipo_comprobante' => 'boleta',
                'numero_comprobante' => 'B-' . time(),
                'total' => 100.00,
                'estado' => 'completada',
                'estado_pago' => 'pagado',
                'fecha_venta' => date('Y-m-d H:i:s')
            ];
            $this->db->table('venta')->insert($venta);
            $this->insertedIds['venta'] = $this->db->insertID();
        } catch (\Exception $e) {
            log_message('error', 'Error en setupTestData: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Prueba funcional que valida la creación de un nuevo método de pago.
     */
    public function testCreateNewPaymentMethod()
    {
        // Datos para crear un nuevo método de pago
        $data = [
            'nombre' => 'Tarjeta de Débito',
            'descripcion' => 'Pago con tarjeta de débito',
            'instrucciones' => 'Inserte la tarjeta y seleccione la opción de débito',
            'is_active' => 1
        ];

        // Realiza una solicitud POST al controlador correspondiente
        $response = $this->post('metodo-pago/create', $data);

        // Verifica que la respuesta HTTP sea exitosa
        $response->assertStatus(200);

        // Busca el método de pago en la base de datos
        $createdPaymentMethod = $this->metodoPagoModel
            ->where('nombre', 'Tarjeta de Débito')
            ->first();

        // Validaciones sobre los datos creados
        $this->assertNotNull($createdPaymentMethod);
        $this->assertEquals($data['nombre'], $createdPaymentMethod['nombre']);
        $this->assertEquals($data['descripcion'], $createdPaymentMethod['descripcion']);
        $this->assertEquals($data['instrucciones'], $createdPaymentMethod['instrucciones']);
        $this->assertEquals($data['is_active'], $createdPaymentMethod['is_active']);

        if ($createdPaymentMethod) {
            $this->insertedIds['nuevo_metodo'] = $createdPaymentMethod['id_metodo_pago'];
        }
    }

    /**
     * Prueba que valida que no se puede eliminar un método de pago con datos relacionados.
     * @expectedException CodeIgniter\Database\Exceptions\DatabaseException
     */
    public function testCannotDeletePaymentMethodWithRelatedData()
    {
        $metodoPagoId = $this->insertedIds['metodo_pago'];

        // Verifica que el método de pago tiene ventas relacionadas
        $ventasRelacionadas = $this->db->table('venta')
            ->where('metodo_pago_id', $metodoPagoId)
            ->countAllResults();
        $this->assertGreaterThan(0, $ventasRelacionadas);

        // Intentar eliminar el método de pago (esto debería lanzar una excepción)
        $this->expectException(\CodeIgniter\Database\Exceptions\DatabaseException::class);
        $this->db->table('metodo_pago')
            ->where('id_metodo_pago', $metodoPagoId)
            ->delete();

        // Confirma que el método de pago sigue existiendo
        $metodoPago = $this->metodoPagoModel->find($metodoPagoId);
        $this->assertNotNull($metodoPago);
    }

    /**
     * Limpia los datos de prueba creados en la base de datos.
     */
    protected function cleanup()
    {
        try {
            if (isset($this->insertedIds['venta'])) {
                $this->db->table('venta')->where('id_venta', $this->insertedIds['venta'])->delete();
            }
            if (isset($this->insertedIds['nuevo_metodo'])) {
                $this->db->table('metodo_pago')->where('id_metodo_pago', $this->insertedIds['nuevo_metodo'])->delete();
            }
            if (isset($this->insertedIds['cliente'])) {
                $this->db->table('cliente')->where('id_cliente', $this->insertedIds['cliente'])->delete();
            }
            if (isset($this->insertedIds['usuario'])) {
                $this->db->table('usuario')->where('id_usuario', $this->insertedIds['usuario'])->delete();
            }
            if (isset($this->insertedIds['metodo_pago'])) {
                $this->db->table('metodo_pago')->where('id_metodo_pago', $this->insertedIds['metodo_pago'])->delete();
            }
        } catch (\Exception $e) {
            log_message('error', 'Error en cleanup: ' . $e->getMessage());
        }
    }

    /**
     * Realiza las tareas finales después de cada prueba.
     */
    protected function tearDown(): void
    {
        $this->cleanup();
        parent::tearDown();
    }
}
