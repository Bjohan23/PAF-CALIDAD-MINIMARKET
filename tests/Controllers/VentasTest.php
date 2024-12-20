<?php

namespace Tests\Controllers;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;
use App\Models\VentaModel;
use App\Models\ClienteModel;
use App\Models\UsuarioModel;
use App\Models\ProductoModel;
use App\Models\CategoriaModel;
use App\Models\MetodoPagoModel;

/**
 * Clase de pruebas para el controlador de Ventas
 * 
 * Esta clase prueba las funcionalidades principales del controlador Ventas
 * utilizando el framework de pruebas de CodeIgniter 4.
 * 
 * Traits utilizados:
 * - FeatureTestTrait: Proporciona métodos para probar las características HTTP como GET, POST, etc.
 */
class VentasTest extends CIUnitTestCase
{
    use FeatureTestTrait; // Permite realizar pruebas de características HTTP

    /**
     * @var \CodeIgniter\Database\BaseConnection
     * Instancia de la conexión a la base de datos
     */
    protected $db;

    /**
     * @var array
     * Almacena los IDs de los registros insertados durante las pruebas
     */
    protected $insertedIds = [];

    /**
     * @var string
     * URL base para las rutas del admin
     */
    protected $baseUrl = 'admin/';

    /**
     * Configuración inicial antes de cada prueba
     * 
     * Este método se ejecuta antes de cada test y:
     * - Inicializa la conexión a la base de datos de pruebas
     * - Limpia datos existentes
     * - Prepara datos de prueba nuevos
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->db = db_connect($this->DBGroup ?? 'tests');
        $this->cleanup();
        $this->setupTestData();
    }

    /**
     * Prepara los datos necesarios para las pruebas
     * 
     * Crea registros de prueba para:
     * - Categorías
     * - Clientes
     * - Usuarios
     * - Métodos de pago
     * 
     * Utiliza timestamps y números aleatorios para garantizar datos únicos
     */
    protected function setupTestData()
    {
        try {
            // Generar datos únicos para evitar duplicados
            $randomDNI = mt_rand(10000000, 99999999);
            $timestamp = time();

            // Insertar categoría de prueba
            $this->db->table('categoria')->insert([
                'nombre' => 'Categoria Test ' . $timestamp,
                'descripcion' => 'Descripcion test'
            ]);
            $this->insertedIds['categoria'] = $this->db->insertID();

            // Insertar cliente de prueba
            $this->db->table('cliente')->insert([
                'nombre' => 'Cliente Test',
                'apellido' => 'Apellido Test',
                'dni' => (string)$randomDNI,
                'email' => 'cliente_' . $timestamp . '@test.com',
                'password' => password_hash('password123', PASSWORD_DEFAULT),
                'fecha_registro' => date('Y-m-d H:i:s'),
                'is_active' => 1
            ]);
            $this->insertedIds['cliente'] = $this->db->insertID();

            // Insertar usuario de prueba (administrador)
            $this->db->table('usuario')->insert([
                'nombre' => 'Usuario Test',
                'apellido' => 'Apellido Test',
                'dni' => (string)($randomDNI + 1),
                'email' => 'usuario_' . $timestamp . '@test.com',
                'contraseña' => password_hash('password123', PASSWORD_DEFAULT),
                'tipo_usuario' => 'administrador',
                'is_active' => 1
            ]);
            $this->insertedIds['usuario'] = $this->db->insertID();

            // Insertar método de pago de prueba
            $this->db->table('metodo_pago')->insert([
                'nombre' => 'Método Test ' . $timestamp,
                'descripcion' => 'Método de pago para pruebas',
                'is_active' => 1
            ]);
            $this->insertedIds['metodo_pago'] = $this->db->insertID();

        } catch (\Exception $e) {
            log_message('error', 'Error en setupTestData: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Prueba la redirección cuando un usuario no autenticado intenta acceder
     * 
     * Caso de uso: Usuario no autenticado intenta acceder a la lista de ventas
     * Resultado esperado: Redirección a la página de login
     * 
     * Utiliza:
     * - get(): Método de FeatureTestTrait para realizar petición GET
     * - assertRedirectTo(): Verifica la redirección correcta
     */
    public function testIndexRedirectsWhenNotLoggedIn()
    {
        $result = $this->get($this->baseUrl . 'ventas');
        $result->assertRedirectTo(base_url($this->baseUrl . 'login'));
    }

    /**
     * Prueba el acceso al formulario de creación de ventas
     * 
     * Caso de uso: Usuario autenticado accede al formulario de nueva venta
     * Resultado esperado: Visualización correcta del formulario
     * 
     * Utiliza:
     * - withSession(): Simula una sesión autenticada
     * - get(): Realiza la petición GET
     * - assertOK(): Verifica respuesta HTTP 200
     */
    public function testCreateShowsFormWhenLoggedIn()
    {
        $result = $this->withSession([
            'isLoggedIn' => true,
            'id_usuario' => $this->insertedIds['usuario'],
            'tipo_usuario' => 'administrador'
        ])->get($this->baseUrl . 'ventas/create');

        $result->assertOK();
    }

    /**
     * Prueba la obtención de detalles de una venta
     * 
     * Caso de uso: Administrador consulta los detalles de una venta específica
     * Resultado esperado: Retorno exitoso de los datos de la venta
     * 
     * Utiliza:
     * - withSession(): Simula sesión de administrador
     * - get(): Realiza petición GET
     * - assertOK(): Verifica respuesta exitosa
     */
    public function testGetDetalleReturnsCorrectData()
    {
        $result = $this->withSession([
            'isLoggedIn' => true,
            'id_usuario' => $this->insertedIds['usuario'],
            'tipo_usuario' => 'administrador'
        ])->get($this->baseUrl . "ventas/getDetalle/1");

        $result->assertOK();
    }

    /**
     * Prueba la actualización del estado de pago de una venta
     * 
     * Caso de uso: Administrador actualiza el estado de pago de una venta
     * Resultado esperado: Actualización exitosa del estado
     * 
     * Utiliza:
     * - withSession(): Simula sesión de administrador
     * - withBodyFormat(): Especifica formato JSON para la petición
     * - post(): Realiza petición POST
     */
    public function testActualizarEstadoPago()
    {
        $data = [
            'estado_pago' => 'pagado',
            'referencia_pago' => 'REF-' . time()
        ];

        $result = $this->withSession([
            'isLoggedIn' => true,
            'id_usuario' => $this->insertedIds['usuario'],
            'tipo_usuario' => 'administrador'
        ])->withBodyFormat('json')
          ->post($this->baseUrl . "ventas/actualizarEstadoPago/1", $data);

        $result->assertOK();
    }

    /**
     * Limpia los datos de prueba de la base de datos
     * 
     * Este método elimina todos los registros creados durante las pruebas
     * en orden inverso para respetar las restricciones de clave foránea
     */
    protected function cleanup()
    {
        try {
            $tables = [
                'usuario' => 'id_usuario',
                'cliente' => 'id_cliente',
                'categoria' => 'id_categoria',
                'metodo_pago' => 'id_metodo_pago'
            ];

            foreach ($tables as $table => $idColumn) {
                if (isset($this->insertedIds[$table])) {
                    $this->db->table($table)
                         ->where($idColumn, $this->insertedIds[$table])
                         ->delete();
                }
            }
        } catch (\Exception $e) {
            log_message('error', 'Error en cleanup: ' . $e->getMessage());
        }
    }

    /**
     * Método que se ejecuta después de cada prueba
     * 
     * Realiza la limpieza final de datos de prueba
     */
    protected function tearDown(): void
    {
        $this->cleanup();
        parent::tearDown();
    }
}