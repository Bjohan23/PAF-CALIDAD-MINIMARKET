<?php

namespace App\Controllers;

use App\Models\ClienteModel;
use App\Models\VentaModel;

class Clientes extends BaseController
{
    public function index()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to(base_url('admin/login'));
        }

        $clienteModel = new ClienteModel();
        $ventaModel = new VentaModel();

        // Obtener clientes con información adicional
        $clientes = $clienteModel->findAll();

        // Agregar información adicional a cada cliente
        foreach ($clientes as &$cliente) {
            // Obtener total de compras
            $compras = $ventaModel->where('id_cliente', $cliente['id_cliente'])
                ->where('estado', 'completada')
                ->findAll();

            $cliente['total_compras'] = count($compras);
            $cliente['monto_total'] = array_sum(array_column($compras, 'total'));
            $cliente['ultima_compra'] = !empty($compras) ? max(array_column($compras, 'fecha_venta')) : null;
            $cliente['estado'] = $cliente['is_active'] ? 'Activo' : 'Inactivo';
        }

        $data['clientes'] = $clientes;
        return view('clientes', $data);
    }

    public function store()
    {
        $model = new ClienteModel();

        // Validación
        $rules = [
            'dni' => 'required|is_unique[cliente.dni]',
            'nombre' => 'required|min_length[3]',
            'apellido' => 'required|min_length[3]',
            'email' => 'required|valid_email|is_unique[cliente.email]',
            'telefono' => 'permit_empty|min_length[6]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Generar contraseña aleatoria si se está creando desde el admin
        $password = bin2hex(random_bytes(4)); // 8 caracteres aleatorios

        $data = [
            'dni' => $this->request->getPost('dni'),
            'nombre' => $this->request->getPost('nombre'),
            'apellido' => $this->request->getPost('apellido'),
            'telefono' => $this->request->getPost('telefono'),
            'email' => $this->request->getPost('email'),
            'direccion' => $this->request->getPost('direccion'),
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'is_active' => 1,
            'fecha_registro' => date('Y-m-d H:i:s')
        ];

        try {
            $model->save($data);
            return redirect()->to(base_url('admin/clientes'))
                ->with('success', 'Cliente creado exitosamente');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()
                ->with('error', 'Error al crear el cliente: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $model = new ClienteModel();
        $cliente = $model->find($id);

        if (!$cliente) {
            return $this->response->setJSON(['success' => false, 'message' => 'Cliente no encontrado']);
        }

        // No enviar el password en la respuesta
        unset($cliente['password']);

        return $this->response->setJSON([
            'success' => true,
            'data' => $cliente
        ]);
    }

    public function update($id)
    {
        $model = new ClienteModel();

        // Validación
        $rules = [
            'dni' => "required|is_unique[cliente.dni,id_cliente,$id]",
            'nombre' => 'required|min_length[3]',
            'apellido' => 'required|min_length[3]',
            'email' => "required|valid_email|is_unique[cliente.email,id_cliente,$id]",
            'telefono' => 'permit_empty|min_length[6]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'dni' => $this->request->getPost('dni'),
            'nombre' => $this->request->getPost('nombre'),
            'apellido' => $this->request->getPost('apellido'),
            'telefono' => $this->request->getPost('telefono'),
            'email' => $this->request->getPost('email'),
            'direccion' => $this->request->getPost('direccion'),
            'is_active' => $this->request->getPost('is_active') ?? 1
        ];

        try {
            if (!$model->update($id, $data)) {
                return redirect()->back()->withInput()
                    ->with('error', 'Error al actualizar el cliente: ' . implode(', ', $model->errors()));
            }

            return redirect()->to(base_url('admin/clientes'))
                ->with('success', 'Cliente actualizado exitosamente');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()
                ->with('error', 'Error al actualizar el cliente: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        $model = new ClienteModel();
        $ventaModel = new VentaModel();

        // Verificar si el cliente tiene ventas asociadas
        $ventasCliente = $ventaModel->where('id_cliente', $id)->countAllResults();

        if ($ventasCliente > 0) {
            // En lugar de eliminar, desactivar el cliente
            try {
                $model->update($id, ['is_active' => 0]);
                return redirect()->to(base_url('admin/clientes'))
                    ->with('warning', 'El cliente tiene ventas asociadas, se ha desactivado en lugar de eliminar');
            } catch (\Exception $e) {
                return redirect()->to(base_url('admin/clientes'))
                    ->with('error', 'Error al desactivar el cliente: ' . $e->getMessage());
            }
        } else {
            // Si no tiene ventas, eliminar completamente
            try {
                $model->delete($id);
                return redirect()->to(base_url('admin/clientes'))
                    ->with('success', 'Cliente eliminado exitosamente');
            } catch (\Exception $e) {
                return redirect()->to(base_url('admin/clientes'))
                    ->with('error', 'Cliente eliminado exitosamente');
            }
        }
    }

    // Método para reactivar clientes desactivados
    public function reactivar($id)
    {
        $model = new ClienteModel();
        try {
            $model->update($id, ['is_active' => 1]);
            return redirect()->to(base_url('admin/clientes'))
                ->with('success', 'Cliente reactivado exitosamente');
        } catch (\Exception $e) {
            return redirect()->to(base_url('admin/clientes'))
                ->with('error', 'Error al reactivar el cliente:' . $e->getMessage());
        }
    }

    // Método para ver el detalle de un cliente
    public function detalle($id)
    {
        $clienteModel = new ClienteModel();
        $ventaModel = new VentaModel();

        $cliente = $clienteModel->find($id);
        if (!$cliente) {
            return redirect()->back()->with('error', 'Cliente no encontrado');
        }

        // Obtener historial de compras
        $ventas = $ventaModel->select('
                venta.*, 
                COUNT(detalle_venta.id_detalle_venta) as total_items,
                seguimiento_pedido.estado as estado_actual
            ')
            ->join('detalle_venta', 'venta.id_venta = detalle_venta.id_venta')
            ->join('seguimiento_pedido', 'venta.id_venta = seguimiento_pedido.id_venta', 'left')
            ->where('venta.id_cliente', $id)
            ->groupBy('venta.id_venta')
            ->orderBy('venta.fecha_venta', 'DESC')
            ->findAll();

        $data = [
            'cliente' => $cliente,
            'ventas' => $ventas,
            'total_compras' => count($ventas),
            'monto_total' => array_sum(array_column($ventas, 'total')),
            'ultima_compra' => !empty($ventas) ? $ventas[0]['fecha_venta'] : null
        ];

        return view('clientes/detalle', $data);
    }
}
