<?php

namespace Tests\Controllers;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;
use CodeIgniter\Test\DatabaseTestTrait;
use App\Models\CategoriaModel;

class CategoriasTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $refresh = false;
    protected $migrate = false;
    protected $model;
    protected $DBGroup = 'tests';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->model = model(CategoriaModel::class);
        $this->insertTestData();
    }

    private function insertTestData()
    {
        $data = [
            'nombre' => 'Categoría Test',
            'descripcion' => 'Descripción de prueba'
        ];
        
        if (!$this->model->where('nombre', $data['nombre'])->first()) {
            $this->model->insert($data);
        }
    }

    public function tearDown(): void
    {
        // Limpiamos los datos de prueba
        $this->model->where('nombre', 'Categoría Test')->delete();
        $this->model->where('nombre', 'Nueva Categoría')->delete();
        $this->model->where('nombre', 'Categoría Actualizada')->delete();
        parent::tearDown();
    }

    public function testIndexRedirectsIfNotLoggedIn()
    {
        $result = $this->get('admin/categorias');
        $result->assertRedirectTo(base_url('login'));
    }

    public function testIndexDisplaysCategoriesWhenLoggedIn()
    {
        $result = $this->withSession([
            'isLoggedIn' => true,
            'id' => 1,
            'tipo_usuario' => 'administrador'
        ])->get('admin/categorias');
        
        $result->assertOK();
        $result->assertSee('Categoría Test');
    }

    public function testStoreCreatesNewCategory()
    {
        $data = [
            'nombre' => 'Nueva Categoría',
            'descripcion' => 'Nueva descripción'
        ];

        $result = $this->withSession([
            'isLoggedIn' => true,
            'id' => 1,
            'tipo_usuario' => 'administrador'
        ])->post('admin/categorias/store', $data);

        $this->seeInDatabase('categoria', ['nombre' => 'Nueva Categoría']);
        $result->assertRedirectTo(base_url('admin/categorias'));
    }

    public function testEditReturnsCategoryDataAsJson()
    {
        $categoria = $this->model->where('nombre', 'Categoría Test')->first();
        
        $result = $this->withSession([
            'isLoggedIn' => true,
            'id' => 1,
            'tipo_usuario' => 'administrador'
        ])->get("admin/categorias/edit/{$categoria['id_categoria']}");

        $result->assertOK();
        $expectedData = [
            'id_categoria' => $categoria['id_categoria'],
            'nombre' => 'Categoría Test',
            'descripcion' => 'Descripción de prueba'
        ];
        $this->assertEquals($expectedData, json_decode($result->getJSON(), true));
    }

    public function testUpdateModifiesExistingCategory()
    {
        $categoria = $this->model->where('nombre', 'Categoría Test')->first();
        
        $data = [
            'nombre' => 'Categoría Actualizada',
            'descripcion' => 'Descripción actualizada'
        ];

        $result = $this->withSession([
            'isLoggedIn' => true,
            'id' => 1,
            'tipo_usuario' => 'administrador'
        ])->post("admin/categorias/update/{$categoria['id_categoria']}", $data);

        $this->seeInDatabase('categoria', ['nombre' => 'Categoría Actualizada']);
        $result->assertRedirectTo(base_url('admin/categorias'));
    }

    public function testDeleteRemovesCategory()
    {
        $categoria = $this->model->where('nombre', 'Categoría Test')->first();

        $result = $this->withSession([
            'isLoggedIn' => true,
            'id' => 1,
            'tipo_usuario' => 'administrador'
        ])->get("admin/categorias/delete/{$categoria['id_categoria']}");

        $this->dontSeeInDatabase('categoria', ['id_categoria' => $categoria['id_categoria']]);
        $result->assertRedirectTo(base_url('admin/categorias'));
    }
}