<?php

namespace Tests\Controllers;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;

class AccountTest extends CIUnitTestCase
{
    use FeatureTestTrait;

    protected $sessionData;

    protected function setUp(): void
    {
        parent::setUp();

        // Datos de sesión simulados para un usuario autenticado
        $this->sessionData = [
            'isLoggedIn' => true,
            'user' => [
                'nombre' => 'John',
                'apellido' => 'Doe',
                'email' => 'john.doe@example.com',
                'dni' => '12345678',
                'telefono' => '123456789',
                'tipo_usuario' => 'admin',
                'created_at' => '2024-01-01 10:00:00',
            ],
        ];
    }

    /** @test */
    // public function it_shows_account_page_for_logged_in_user()
    // {
    //     // Simular una sesión activa
    //     $session = \Config\Services::session();
    //     $session->set($this->sessionData);

    //     $response = $this->get('admin/account');

    //     // Depuración: mostrar detalles de la respuesta
    //     $this->assertSame(200, $response->getStatusCode());
    //     $this->assertStringContainsString('Mi Cuenta', $response->getBody());
    //     $this->assertStringContainsString('John Doe', $response->getBody());
    //     $this->assertStringContainsString('john.doe@example.com', $response->getBody());
    // }

    /** @test */
    public function it_redirects_to_login_when_user_is_not_logged_in()
    {
        // Simular sin sesión de login
        $response = $this->get('admin/account');
        $response->assertRedirectTo(base_url('admin/login'));
    }
}
