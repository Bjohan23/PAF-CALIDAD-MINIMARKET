<?php

namespace Tests\Controllers;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;
use App\Models\VentaModel;
use App\Models\ProductoModel;
use App\Models\ClienteModel;

/**
 * Clase de pruebas para el controlador Home
 * Extiende CIUnitTestCase que proporciona la funcionalidad base para pruebas en CodeIgniter
 */
class HomeTest extends CIUnitTestCase
{
    // FeatureTestTrait proporciona métodos auxiliares para pruebas de características HTTP
    use FeatureTestTrait;

    // Propiedades para mantener las instancias de la base de datos y modelos
    protected $db;                 // Conexión a la base de datos
    protected $ventaModel;         // Modelo de ventas
    protected $productoModel;      // Modelo de productos  
    protected $clienteModel;       // Modelo de clientes
    protected $insertedIds = [];   // Almacena IDs de registros insertados para limpieza

    /**
     * Método que se ejecuta antes de cada prueba
     * Configura el entorno necesario para las pruebas
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Conecta a la base de datos de pruebas
        $this->db = db_connect($this->DBGroup ?? 'tests');

        // Limpia datos anteriores
        $this->cleanup();

        // Inicializa los modelos
        $this->ventaModel = new VentaModel();
        $this->productoModel = new ProductoModel();
        $this->clienteModel = new ClienteModel();

        // Configura datos de prueba
        $this->setupTestData();
    }

    /**
     * Configura datos de prueba en la base de datos
     * Crea registros de ejemplo para categorías, clientes, usuarios, productos y ventas
     */
    protected function setupTestData()
    {
        try {
            // Genera DNI aleatorio para evitar duplicados en pruebas
            $randomDNI = mt_rand(10000000, 99999999);

            // Inserta categoría de prueba
            $categoryId = $this->db->table('categoria')->insert([
                'nombre' => 'Categoria Test',
                'descripcion' => 'Descripcion test'
            ]);
            $this->insertedIds['categoria'] = $this->db->insertID();

            // Inserta cliente de prueba con email único usando timestamp
            $clientId = $this->db->table('cliente')->insert([/* ... */]);
            $this->insertedIds['cliente'] = $this->db->insertID();

            // Inserta usuario administrador de prueba
            $userId = $this->db->table('usuario')->insert([/* ... */]);
            $this->insertedIds['usuario'] = $this->db->insertID();

            // Inserta producto de prueba con nombre y slug únicos
            $productId = $this->db->table('producto')->insert([/* ... */]);
            $this->insertedIds['producto'] = $this->db->insertID();

            // Inserta venta de prueba con número de comprobante único
            $ventaId = $this->db->table('venta')->insert([/* ... */]);
            $this->insertedIds['venta'] = $this->db->insertID();
        } catch (\Exception $e) {
            log_message('error', 'Error en setupTestData: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Prueba que verifica la redirección al login cuando un usuario no autenticado 
     * intenta acceder al dashboard
     */
    public function testIndexRedirectsWhenNotLoggedIn()
    {
        // Simula petición GET a /admin/home
        $result = $this->get('admin/home');
        // Verifica que redireccione a la página de login
        $result->assertRedirectTo(base_url('admin/login'));
    }

    /**
     * Prueba que verifica que un usuario autenticado puede ver el dashboard
     */
    public function testIndexShowsDashboardWhenLoggedIn()
    {
        // Simula sesión de usuario autenticado
        $result = $this->withSession([
            'isLoggedIn' => true,
            'id_usuario' => $this->insertedIds['usuario']
        ])->get('admin/home');

        // Verifica respuesta HTTP 200
        $result->assertOK();
        // Verifica que se muestre la palabra "Dashboard" en la página
        $result->assertSee('Dashboard');
    }

    /**
     * Prueba que verifica que el dashboard muestra las estadísticas correctas
     */
    public function testDashboardShowsCorrectStatistics()
    {
        $result = $this->withSession([
            'isLoggedIn' => true,
            'id_usuario' => $this->insertedIds['usuario']
        ])->get('admin/home');

        $result->assertOK();
        // Verifica que se muestren los elementos estadísticos esperados
        $result->assertSee('Ventas Totales');
        $result->assertSee('Productos Vendidos');
        $result->assertSee('Clientes Nuevos');
        $result->assertSee('Stock Bajo');
    }

    /**
     * Prueba que verifica que el dashboard muestra las órdenes recientes
     */
    public function testDashboardShowsRecentOrders()
    {
        $result = $this->withSession([
            'isLoggedIn' => true,
            'id_usuario' => $this->insertedIds['usuario']
        ])->get('admin/home');

        $result->assertOK();
        $result->assertSee('Órdenes Recientes');
    }

    /**
     * Método que se ejecuta después de cada prueba
     * Limpia los datos de prueba creados
     */
    protected function tearDown(): void
    {
        $this->cleanup();
        parent::tearDown();
    }

    /**
     * Limpia los datos de prueba de la base de datos
     * Elimina todos los registros creados durante las pruebas
     */
    protected function cleanup()
    {
        try {
            // Elimina registros en orden inverso a su creación para mantener integridad referencial
            if (isset($this->insertedIds['venta'])) {
                $this->db->table('venta')->where('id_venta', $this->insertedIds['venta'])->delete();
            }
            // Continúa eliminando el resto de registros...
        } catch (\Exception $e) {
            log_message('error', 'Error en cleanup: ' . $e->getMessage());
        }
    }
}
