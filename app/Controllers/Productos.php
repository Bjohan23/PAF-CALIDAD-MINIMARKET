<?php

namespace App\Controllers;

use App\Models\ProductoModel;
use App\Models\CategoriaModel;
use CodeIgniter\API\ResponseTrait;

class Productos extends BaseController
{
    use ResponseTrait;

    protected $validation;

    public function __construct()
    {
        $this->validation = \Config\Services::validation();
    }

    public function index()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            $session->setFlashdata('error', 'Debe iniciar sesión para acceder a esta página');
            return redirect()->to(base_url('admin/login'));
        }

        try {
            $productoModel = new ProductoModel();
            $categoriaModel = new CategoriaModel();

            $productos = $productoModel->findAll();
            $categorias = $categoriaModel->findAll();

            // Añadir la categoría a cada producto
            foreach ($productos as &$producto) {
                $categoria = $categoriaModel->find($producto['id_categoria']);
                $producto['categoria'] = $categoria ? $categoria['nombre'] : 'Sin categoría';
            }

            $data['productos'] = $productos;
            $data['categorias'] = $categorias;

            return view('productos', $data);
        } catch (\Exception $e) {
            $session->setFlashdata('error', 'Error al cargar los productos: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function store()
    {
        $session = session();

        // Reglas de validación
        $rules = [
            'nombre' => [
                'rules' => 'required|min_length[3]|max_length[100]|is_unique[producto.nombre]',
                'errors' => [
                    'required' => 'El nombre del producto es obligatorio',
                    'min_length' => 'El nombre debe tener al menos 3 caracteres',
                    'max_length' => 'El nombre no puede exceder los 100 caracteres',
                    'is_unique' => 'Ya existe un producto con este nombre'
                ]
            ],
            'precio' => [
                'rules' => 'required|numeric|greater_than[0]',
                'errors' => [
                    'required' => 'El precio es obligatorio',
                    'numeric' => 'El precio debe ser un número',
                    'greater_than' => 'El precio debe ser mayor que 0'
                ]
            ],
            'stock' => [
                'rules' => 'required|integer|greater_than_equal_to[0]',
                'errors' => [
                    'required' => 'El stock es obligatorio',
                    'integer' => 'El stock debe ser un número entero',
                    'greater_than_equal_to' => 'El stock no puede ser negativo'
                ]
            ],
            'id_categoria' => [
                'rules' => 'required|integer|is_not_unique[categoria.id_categoria]',
                'errors' => [
                    'required' => 'La categoría es obligatoria',
                    'integer' => 'Categoría inválida',
                    'is_not_unique' => 'La categoría seleccionada no existe'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            $session->setFlashdata('error', $this->validation->listErrors());
            return redirect()->back()->withInput();
        }

        try {
            $model = new ProductoModel();
            $data = [
                'nombre' => trim($this->request->getPost('nombre')),
                'descripcion' => trim($this->request->getPost('descripcion')),
                'precio' => number_format((float)$this->request->getPost('precio'), 2, '.', ''),
                'stock' => (int)$this->request->getPost('stock'),
                'id_categoria' => (int)$this->request->getPost('id_categoria'),
            ];

            if ($model->save($data)) {
                $session->setFlashdata('success', 'Producto agregado exitosamente');
            } else {
                $session->setFlashdata('error', 'Error al guardar el producto');
            }

            // return redirect()->to(base_url('admin/productos'));
        } catch (\Exception $e) {
            $session->setFlashdata('error', 'Error al guardar el producto: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function edit($id = null)
    {
        if (!$id) {
            return $this->failNotFound('No se especificó el ID del producto');
        }

        try {
            $model = new ProductoModel();
            $producto = $model->find($id);

            if (!$producto) {
                return $this->failNotFound('Producto no encontrado');
            }

            return $this->response->setJSON($producto);
        } catch (\Exception $e) {
            return $this->fail('Error al obtener el producto: ' . $e->getMessage());
        }
    }

    public function update($id = null)
    {
        $session = session();

        if (!$id) {
            $session->setFlashdata('error', 'No se especificó el ID del producto');
            return redirect()->back();
        }

        // Reglas de validación para actualización
        $rules = [
            'nombre' => [
                'rules' => "required|min_length[3]|max_length[100]|is_unique[producto.nombre,id_producto,$id]",
                'errors' => [
                    'required' => 'El nombre del producto es obligatorio',
                    'min_length' => 'El nombre debe tener al menos 3 caracteres',
                    'max_length' => 'El nombre no puede exceder los 100 caracteres',
                    'is_unique' => 'Ya existe otro producto con este nombre'
                ]
            ],
            'precio' => [
                'rules' => 'required|numeric|greater_than[0]',
                'errors' => [
                    'required' => 'El precio es obligatorio',
                    'numeric' => 'El precio debe ser un número',
                    'greater_than' => 'El precio debe ser mayor que 0'
                ]
            ],
            'stock' => [
                'rules' => 'required|integer|greater_than_equal_to[0]',
                'errors' => [
                    'required' => 'El stock es obligatorio',
                    'integer' => 'El stock debe ser un número entero',
                    'greater_than_equal_to' => 'El stock no puede ser negativo'
                ]
            ],
            'id_categoria' => [
                'rules' => 'required|integer|is_not_unique[categoria.id_categoria]',
                'errors' => [
                    'required' => 'La categoría es obligatoria',
                    'integer' => 'Categoría inválida',
                    'is_not_unique' => 'La categoría seleccionada no existe'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            $session->setFlashdata('error', $this->validation->listErrors());
            return redirect()->back()->withInput();
        }

        try {
            $model = new ProductoModel();
            $data = [
                'nombre' => trim($this->request->getPost('nombre')),
                'descripcion' => trim($this->request->getPost('descripcion')),
                'precio' => number_format((float)$this->request->getPost('precio'), 2, '.', ''),
                'stock' => (int)$this->request->getPost('stock'),
                'id_categoria' => (int)$this->request->getPost('id_categoria'),
            ];

            if ($model->update($id, $data)) {
                $session->setFlashdata('success', 'Producto actualizado exitosamente');
            } else {
                $session->setFlashdata('error', 'Error al actualizar el producto');
            }

            return redirect()->to(base_url('admin/productos'));
        } catch (\Exception $e) {
            $session->setFlashdata('error', 'Error al actualizar el producto: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function delete($id = null)
    {
        $session = session();

        if (!$id) {
            $session->setFlashdata('error', 'No se especificó el ID del producto');
            return redirect()->back();
        }

        try {
            $model = new ProductoModel();

            // Verificar si el producto existe
            $producto = $model->find($id);
            if (!$producto) {
                $session->setFlashdata('error', 'Producto no encontrado');
                return redirect()->to(base_url('admin/productos'));
            }

            // Intentar eliminar el producto
            if ($model->delete($id)) {
                $session->setFlashdata('success', 'Producto eliminado exitosamente');
            } else {
                $session->setFlashdata('error', 'Error al eliminar el producto');
            }

            return redirect()->to(base_url('admin/productos'));
        } catch (\Exception $e) {
            $session->setFlashdata('error', 'Error al eliminar el producto: ' . $e->getMessage());
            return redirect()->back();
        }
    }
}
