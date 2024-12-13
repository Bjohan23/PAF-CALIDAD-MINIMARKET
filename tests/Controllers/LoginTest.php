<?php

namespace Tests\Controllers;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;
use App\Models\UsuarioModel;
use Config\Services;

class LoginTest extends CIUnitTestCase
{
    use FeatureTestTrait;

    protected function setUp(): void
    {
        parent::setUp();

        // Crear un usuario de prueba en la base de datos
        $model = new UsuarioModel();
        $model->insert([
            'nombre' => 'John Doe',
            'apellido' => 'Doe',
            'email' => 'john.doe@example.com',
            'telefono' => '123456789',
            'dni' => '12345678',
            'contraseña' => password_hash('password123', PASSWORD_DEFAULT),
            'tipo_usuario' => 'cliente',
        ]);
    }

    protected function tearDown(): void
    {
        // Eliminar el usuario de prueba después de cada prueba
        $model = new UsuarioModel();
        $model->where('email', 'john.doe@example.com')->delete();
        parent::tearDown();
    }

    /** @test */
    // public function it_shows_the_login_page()
    // {
    //     $response = $this->get('admin/login');
    //     $response->assertStatus(200)
    //         ->assertSee('Iniciar sesión');
    // }

    /** @test */
    // public function it_authenticates_valid_user()
    // {
    //     $data = [
    //         'username' => 'john.doe@example.com',
    //         'password' => 'password123',
    //     ];

    //     $response = $this->post('admin/login/authenticate', $data);
    //     $response->assertRedirect('admin/home');

    //     $session = \Config\Services::session();
    //     $this->assertTrue($session->get('isLoggedIn'));
    //     $this->assertNotEmpty($session->get('id_usuario'));
    //     $this->assertNotEmpty($session->get('user'));
    // }
    /** @test */
    public function it_fails_to_authenticate_invalid_user()
    {
        $data = [
            'username' => 'invalid@example.com',
            'password' => 'wrongpassword',
        ];

        $response = $this->post('admin/login/authenticate', $data);
        $response->assertRedirect('admin/login');
        $response->assertSessionHas('error', 'Usuario o contraseña incorrectos');
    }
    /** @test */
    // public function it_logs_out_user()
    // {
    //     // Simular que el usuario está logueado
    //     $session = \Config\Services::session();
    //     $session->set([
    //         'isLoggedIn' => true,
    //         'id_usuario' => 1,
    //         'user' => [
    //             'id_usuario' => 1,
    //             'nombre' => 'John Doe',
    //             'email' => 'john.doe@example.com',
    //         ],
    //     ]);

    //     $response = $this->get('admin/logout');
    //     $response->assertRedirect('admin/login');

    //     $this->assertFalse($session->get('isLoggedIn'));
    //     $this->assertNull($session->get('id_usuario'));
    //     $this->assertNull($session->get('user'));
    // }

    /** @test */
    public function it_creates_a_new_user()
    {
        $data = [
            'nombre' => 'Jane Doe',
            'apellido' => 'Doe',
            'email' => 'jane.doe@example.com',
            'telefono' => '987654321',
            'dni' => '87654321',
            'contraseña' => 'password456',
        ];

        try {
            $response = $this->post('admin/login/create', $data);
            $response->assertRedirect('admin/login');

            $model = new UsuarioModel();
            $user = $model->where('email', 'jane.doe@example.com')->first();
            $this->assertNotNull($user);
            $this->assertEquals('Jane Doe', $user['nombre']);
            $this->assertEquals('Doe', $user['apellido']);
            $this->assertEquals('jane.doe@example.com', $user['email']);
            $this->assertEquals('987654321', $user['telefono']);
            $this->assertEquals('87654321', $user['dni']);
            $this->assertTrue(password_verify('password456', $user['contraseña']));
            $this->assertEquals('cliente', $user['tipo_usuario']);
        } catch (\CodeIgniter\Database\Exceptions\DatabaseException $e) {
            // Verificar si el error es por clave única
            $this->assertStringContainsString('Duplicate entry', $e->getMessage());
        }
    }
}
