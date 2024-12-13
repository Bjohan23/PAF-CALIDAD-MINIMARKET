<?php

namespace Tests\Controllers;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;
use CodeIgniter\Test\DatabaseTestTrait;
use App\Models\ProductoModel;
use App\Models\CategoriaModel;

class ProductosTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $refresh = false;
    protected $migrate = false;
    protected $productoModel;
    protected $categoriaModel;
    protected $DBGroup = 'tests';
    protected $categoria_id;

    protected function setUp(): void
    {
        parent::setUp();

        $this->productoModel = model(ProductoModel::class);
        $this->categoriaModel = model(CategoriaModel::class);

        // Crear categoría de prueba
        $this->categoria_id = $this->categoriaModel->insert([
            'nombre' => 'Categoría Test',
            'descripcion' => 'Descripción de prueba'
        ]);

        // Insertar producto de prueba
        $this->insertTestData();
    }

    private function insertTestData()
    {
        $data = [
            'nombre' => 'Producto Test',
            'descripcion' => 'Descripción de prueba',
            'precio' => 99.99,
            'stock' => 10,
            'id_categoria' => $this->categoria_id,
            'slug' => 'producto-test',
            'is_active' => 1,
            'destacado' => 0
        ];

        if (!$this->productoModel->where('nombre', $data['nombre'])->first()) {
            $this->productoModel->insert($data);
        }
    }

    public function tearDown(): void
    {
        // Limpiar datos de prueba
        $this->productoModel->where('nombre', 'Producto Test')->delete();
        $this->productoModel->where('nombre', 'Nuevo Producto')->delete();
        $this->productoModel->where('nombre', 'Producto Actualizado')->delete();
        $this->categoriaModel->delete($this->categoria_id);
        parent::tearDown();
    }

    public function testIndexRedirectsIfNotLoggedIn()
    {
        $result = $this->get('admin/productos');
        $result->assertRedirectTo(base_url('admin/login'));
    }

    public function testIndexDisplaysProductsWhenLoggedIn()
    {
        $result = $this->withSession([
            'isLoggedIn' => true,
            'id' => 1,
            'tipo_usuario' => 'administrador'
        ])->get('admin/productos');

        $result->assertOK();
        $result->assertSee('Producto Test');
    }

    public function testStoreValidatesRequiredFields()
    {
        $result = $this->withSession([
            'isLoggedIn' => true,
            'id' => 1,
            'tipo_usuario' => 'administrador'
        ])->post('admin/productos/store', []);

        $result->assertSessionHas('error');
    }

    public function testStoreCreatesNewProduct()
    {
        $data = [
            'nombre' => 'Nuevo Producto',
            'descripcion' => 'Nueva descripción',
            'precio' => 199.99,
            'stock' => 20,
            'id_categoria' => $this->categoria_id,
            'slug' => 'nuevo-producto',
            'is_active' => 1,
            'destacado' => 0
        ];

        $result = $this->withSession([
            'isLoggedIn' => true,
            'id' => 1,
            'tipo_usuario' => 'administrador'
        ])->post('admin/productos/store', $data);

        $this->seeInDatabase('producto', ['nombre' => 'Nuevo Producto']);
        $result->assertSessionHas('success');
    }

    public function testEditReturnsProductDataAsJson()
    {
        $producto = $this->productoModel->where('nombre', 'Producto Test')->first();

        $result = $this->withSession([
            'isLoggedIn' => true,
            'id' => 1,
            'tipo_usuario' => 'administrador'
        ])->get("admin/productos/edit/{$producto['id_producto']}");

        $result->assertOK();
        $resultData = json_decode($result->getJSON(), true);

        // Verificar solo los campos que nos interesan
        $this->assertEquals($producto['nombre'], $resultData['nombre']);
        $this->assertEquals($producto['descripcion'], $resultData['descripcion']);
        $this->assertEquals($producto['precio'], $resultData['precio']);
        $this->assertEquals($producto['stock'], $resultData['stock']);
        $this->assertEquals($producto['id_categoria'], $resultData['id_categoria']);
    }

    public function testUpdateModifiesExistingProduct()
    {
        $producto = $this->productoModel->where('nombre', 'Producto Test')->first();

        $data = [
            'nombre' => 'Producto Actualizado',
            'descripcion' => 'Descripción actualizada',
            'precio' => 299.99,
            'stock' => 30,
            'id_categoria' => $this->categoria_id,
            'slug' => 'producto-actualizado'
        ];

        $result = $this->withSession([
            'isLoggedIn' => true,
            'id' => 1,
            'tipo_usuario' => 'administrador'
        ])->post("admin/productos/update/{$producto['id_producto']}", $data);

        $this->seeInDatabase('producto', ['nombre' => 'Producto Actualizado']);
        $result->assertRedirectTo(base_url('admin/productos'));
        $result->assertSessionHas('success');
    }

    public function testUpdateValidatesRequiredFields()
    {
        $producto = $this->productoModel->where('nombre', 'Producto Test')->first();

        $result = $this->withSession([
            'isLoggedIn' => true,
            'id' => 1,
            'tipo_usuario' => 'administrador'
        ])->post("admin/productos/update/{$producto['id_producto']}", []);

        $result->assertSessionHas('error');
    }

    public function testDeleteRemovesProduct()
    {
        $producto = $this->productoModel->where('nombre', 'Producto Test')->first();

        $result = $this->withSession([
            'isLoggedIn' => true,
            'id' => 1,
            'tipo_usuario' => 'administrador'
        ])->delete("admin/productos/delete/{$producto['id_producto']}");

        $this->dontSeeInDatabase('producto', ['id_producto' => $producto['id_producto']]);
        $result->assertRedirectTo(base_url('admin/productos'));
    }

    public function testDeleteWithInvalidIdShowsError()
    {
        $result = $this->withSession([
            'isLoggedIn' => true,
            'id' => 1,
            'tipo_usuario' => 'administrador'
        ])->delete("admin/productos/delete/99999");

        $result->assertRedirectTo(base_url('admin/productos'));
        $result->assertSessionHas('error');
    }
}
