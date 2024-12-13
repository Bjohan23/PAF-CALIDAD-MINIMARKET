<?php

namespace Tests\Controllers;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;
use App\Models\MetodoPagoModel;
use Config\Services;

class MetodoPagoTest extends CIUnitTestCase
{
    use FeatureTestTrait;

    protected function setUp(): void
    {
        parent::setUp();

        // Crear registros de prueba en la tabla de métodos de pago
        $metodoPagoModel = new MetodoPagoModel();
        $metodoPagoModel->insertBatch([
            [
                'nombre' => 'Tarjeta de Crédito',
                'descripcion' => 'Pago con tarjeta de crédito',
                'is_active' => 1
            ],
            [
                'nombre' => 'Transferencia Bancaria',
                'descripcion' => 'Pago mediante transferencia bancaria',
                'is_active' => 1
            ],
            [
                'nombre' => 'Efectivo',
                'descripcion' => 'Pago en efectivo',
                'is_active' => 1
            ]
        ]);
    }

    protected function tearDown(): void
    {
        // Eliminar los registros de prueba de la tabla de métodos de pago
        $metodoPagoModel = new MetodoPagoModel();
        $metodoPagoModel->emptyTable();
        parent::tearDown();
    }

    /** @test */
    public function it_creates_a_new_payment_method()
    {
        $data = [
            'nombre' => 'Tarjeta de Débito',
            'descripcion' => 'Pago con tarjeta de débito',
            'instrucciones' => 'Inserte la tarjeta y seleccione la opción de débito',
            'is_active' => 1
        ];

        $response = $this->post('metodo-pago/create', $data);
        $response->assertStatus(200);

        $model = new MetodoPagoModel();
        $createdPaymentMethod = $model->where('nombre', 'Tarjeta de Débito')->first();

        $this->assertNotNull($createdPaymentMethod);
        $this->assertEquals($data['nombre'], $createdPaymentMethod['nombre']);
        $this->assertEquals($data['descripcion'], $createdPaymentMethod['descripcion']);
        $this->assertEquals($data['instrucciones'], $createdPaymentMethod['instrucciones']);
        $this->assertEquals($data['is_active'], $createdPaymentMethod['is_active']);
    }
    /** @test */
    // public function it_gets_all_active_payment_methods()
    // {
    //     $response = $this->get('getMetodoPago');

    //     $response->assertStatus(200)
    //         ->assertJsonCount(3)
    //         ->assertJsonStructure([
    //             '*' => [
    //                 'id_metodo_pago',
    //                 'nombre',
    //                 'descripcion',
    //                 'instrucciones',
    //                 'is_active'
    //             ]
    //         ]);

    //     $data = json_decode($response->getBody(), true);
    //     $this->assertNotEmpty($data);

    //     foreach ($data as $item) {
    //         $this->assertArrayHasKey('id_metodo_pago', $item);
    //         $this->assertArrayHasKey('nombre', $item);
    //         $this->assertArrayHasKey('descripcion', $item);
    //         $this->assertArrayHasKey('instrucciones', $item);
    //         $this->assertArrayHasKey('is_active', $item);
    //         $this->assertTrue($item['is_active']);
    //     }
    // }
}
