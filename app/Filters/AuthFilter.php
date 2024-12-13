<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Debug: Log del estado de la sesión en cada petición
        log_message('debug', 'AuthFilter - Estado de sesión: ' . json_encode([
            'session_id' => session_id(),
            'cliente_id' => session()->get('cliente_id'),
            'isClienteLoggedIn' => session()->get('isClienteLoggedIn')
        ]));

        if (!session()->get('isClienteLoggedIn')) {
            return redirect()->to('/login');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No necesitamos hacer nada aquí
    }
}