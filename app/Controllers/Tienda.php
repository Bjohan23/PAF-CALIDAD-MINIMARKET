<?php

namespace App\Controllers;

use App\Models\ProductoModel;
use App\Models\CategoriaModel;
use App\Models\ClienteModel;
use App\Models\CarritoModel;
use App\Models\CarritoItemModel;
use App\Models\VentaModel;
use App\Models\MetodoPagoModel;
use App\Models\SeguimientoPedidoModel;
use App\Models\DetalleVentaModel;

class Tienda extends BaseController
{
    protected $session;

    public function __construct()
    {
        $this->session = session();
        helper('text');
    }

    public function index()
    {
        $productoModel = new ProductoModel();
        $categoriaModel = new CategoriaModel();

        $data = [
            'title' => 'Inicio',
            'productos_destacados' => $productoModel->where('destacado', 1)
                ->where('is_active', 1)
                ->findAll(8),
            'categorias' => $categoriaModel->findAll(),
            'ofertas' => $productoModel->where('precio_oferta IS NOT NULL')
                ->where('is_active', 1)
                ->findAll(4),
            'cart_count' => $this->getCartCount(),
            'slides' => [
                [
                    'image' => 'https://plus.unsplash.com/premium_photo-1681488262364-8aeb1b6aac56?q=80&w=2670&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
                    'title' => 'Nuevos Productos',
                    'description' => 'Descubre nuestra nueva colección',
                    'buttonText' => 'Ver Productos',
                    'buttonLink' => base_url('tienda/productos')
                ],
                [
                    'image' => 'https://images.pexels.com/photos/5650026/pexels-photo-5650026.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1',
                    'title' => 'Ofertas Especiales',
                    'description' => 'Hasta 50% de descuento',
                    'buttonText' => 'Ver Ofertas',
                    'buttonLink' => base_url('tienda/ofertas')
                ],
                [
                    'image' => 'https://images.pexels.com/photos/7776101/pexels-photo-7776101.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1',
                    'title' => 'Envío Gratis',
                    'description' => 'En compras mayores a S/ 200',
                    'buttonText' => 'Comprar Ahora',
                    'buttonLink' => base_url('tienda/productos')
                ]
            ]
        ];

        return view('clientes/home', $data);
    }

    public function categoria($id)
    {
        $productoModel = new ProductoModel();
        $categoriaModel = new CategoriaModel();

        // Obtener la categoría
        $categoria = $categoriaModel->find($id);

        if (!$categoria) {
            return redirect()->to('/')->with('error', 'Categoría no encontrada');
        }

        // Obtener productos de la categoría
        $productos = $productoModel->where('id_categoria', $id)
            ->where('is_active', 1)
            ->paginate(12);

        $data = [
            'title' => $categoria['nombre'],
            'categoria' => $categoria,
            'productos' => $productos,
            'pager' => $productoModel->pager,
            'cart_count' => $this->getCartCount()
        ];

        return view('clientes/categoria', $data);
    }

    public function productos()
    {
        $productoModel = new ProductoModel();
        $categoriaModel = new CategoriaModel();

        // Procesar filtros
        $categorias = $this->request->getGet('categorias');
        $precioMin = $this->request->getGet('precio_min');
        $precioMax = $this->request->getGet('precio_max');
        $enStock = $this->request->getGet('en_stock');
        $ordenar = $this->request->getGet('ordenar');

        // Iniciamos el builder con el join a categorías
        $builder = $productoModel->select('producto.*, categoria.nombre as categoria_nombre')
            ->join('categoria', 'categoria.id_categoria = producto.id_categoria')
            ->where('producto.is_active', 1);

        // Aplicar filtros
        if ($categorias) {
            $builder->whereIn('producto.id_categoria', explode(',', $categorias));
        }
        if ($precioMin) {
            $builder->where('producto.precio >=', $precioMin);
        }
        if ($precioMax) {
            $builder->where('producto.precio <=', $precioMax);
        }
        if ($enStock) {
            $builder->where('producto.stock >', 0);
        }

        // Aplicar ordenamiento
        switch ($ordenar) {
            case 'precio_asc':
                $builder->orderBy('producto.precio', 'ASC');
                break;
            case 'precio_desc':
                $builder->orderBy('producto.precio', 'DESC');
                break;
            case 'nombre_asc':
                $builder->orderBy('producto.nombre', 'ASC');
                break;
            case 'nombre_desc':
                $builder->orderBy('producto.nombre', 'DESC');
                break;
            default:
                $builder->orderBy('producto.id_producto', 'DESC');
        }

        $data = [
            'title' => 'Productos',
            'productos' => $builder->paginate(12),
            'pager' => $productoModel->pager,
            'categorias' => $categoriaModel->findAll(),
            'cart_count' => $this->getCartCount(),
            'isLoggedIn' => (bool)$this->session->get('cliente_id') // esta línea es para mostrar el botón de agregar al carrito
        ];

        return view('clientes/productos', $data);
    }

    public function producto($id)
    {
        $productoModel = new ProductoModel();
        $producto = $productoModel->find($id);

        if (!$producto) {
            return redirect()->to('/productos')->with('error', 'Producto no encontrado');
        }

        // Obtener productos relacionados
        $relacionados = $productoModel->where('id_categoria', $producto['id_categoria'])
            ->where('id_producto !=', $id)
            ->where('is_active', 1)
            ->findAll(4);

        $data = [
            'title' => $producto['nombre'],
            'producto' => $producto,
            'relacionados' => $relacionados,
            'cart_count' => $this->getCartCount()
        ];

        return view('clientes/producto_detalle', $data);
    }

    // Métodos del carrito
    public function agregarCarrito()
    {
        // Verificar si es una petición AJAX
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(404);
        }

        // Verificar autenticación del cliente
        if (!$this->session->get('cliente_id')) {
            return $this->response->setJSON([
                'success' => false,
                'requireLogin' => true,
                'message' => 'Debe iniciar sesión para agregar productos al carrito'
            ]);
        }

        // Obtener y validar datos de entrada
        $json = $this->request->getJSON();
        $id_producto = isset($json->id_producto) ? intval($json->id_producto) : 0;
        $cantidad = isset($json->cantidad) ? intval($json->cantidad) : 1;

        if ($id_producto <= 0 || $cantidad <= 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Datos inválidos'
            ]);
        }

        // Verificar producto y stock
        $productoModel = new ProductoModel();
        $producto = $productoModel->find($id_producto);

        if (!$producto) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Producto no encontrado'
            ]);
        }

        if ($producto['stock'] < $cantidad) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Stock insuficiente. Solo quedan ' . $producto['stock'] . ' unidades'
            ]);
        }

        try {
            // Obtener o crear carrito
            $carritoModel = new CarritoModel();
            $carritoItemModel = new CarritoItemModel();

            $id_cliente = $this->session->get('cliente_id');
            $carrito = $carritoModel->where('id_cliente', $id_cliente)->first();

            if (!$carrito) {
                $id_carrito = $carritoModel->insert([
                    'id_cliente' => $id_cliente,
                    'fecha_creacion' => date('Y-m-d H:i:s')
                ]);
                $carrito = $carritoModel->find($id_carrito);
            }

            // Verificar si el producto ya está en el carrito
            $item = $carritoItemModel->where('id_carrito', $carrito['id_carrito'])
                ->where('id_producto', $id_producto)
                ->first();

            $precio_actual = $producto['precio_oferta'] ?? $producto['precio'];

            if ($item) {
                // Actualizar cantidad si ya existe
                $nueva_cantidad = $item['cantidad'] + $cantidad;
                if ($nueva_cantidad > $producto['stock']) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'La cantidad total superaría el stock disponible'
                    ]);
                }

                $carritoItemModel->actualizarItem(
                    $item['id_carrito_item'],
                    $nueva_cantidad,
                    $precio_actual
                );
            } else {
                // Agregar nuevo item
                $carritoItemModel->insert([
                    'id_carrito' => $carrito['id_carrito'],
                    'id_producto' => $id_producto,
                    'cantidad' => $cantidad,
                    'precio_unitario' => $precio_actual,
                    'subtotal' => $precio_actual * $cantidad
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Producto agregado al carrito',
                'cart_count' => $this->getCartCount()
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error al agregar al carrito: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al agregar el producto al carrito'
            ]);
        }
    }

    protected function getCartCount()
    {
        if (!$this->session->get('cliente_id')) {
            return 0;
        }

        $carritoModel = new CarritoModel();
        $carritoItemModel = new CarritoItemModel();

        $carrito = $carritoModel->where('id_cliente', $this->session->get('cliente_id'))->first();

        if (!$carrito) {
            return 0;
        }

        return $carritoItemModel->where('id_carrito', $carrito['id_carrito'])
            ->selectSum('cantidad')
            ->first()['cantidad'] ?? 0;
    }

    public function carrito()
    {
        if (!$this->session->get('cliente_id')) {
            return redirect()->to('/login')->with('error', 'Debe iniciar sesión para ver el carrito');
        }

        $carritoModel = new CarritoModel();
        $carritoItemModel = new CarritoItemModel();
        $productoModel = new ProductoModel();

        $carrito = $carritoModel->where('id_cliente', $this->session->get('cliente_id'))->first();

        if (!$carrito) {
            $data = [
                'title' => 'Carrito de Compras',
                'items' => [],
                'total' => 0,
                'cart_count' => 0
            ];
            return view('clientes/carrito', $data);
        }

        // Obtener items con información de productos
        $items = $carritoItemModel->select('
            carrito_item.*,
            producto.nombre as producto_nombre,
            producto.imagen_url,
            producto.stock,
            producto.precio as precio_actual,
            producto.precio_oferta as precio_oferta_actual
        ')
            ->join('producto', 'carrito_item.id_producto = producto.id_producto')
            ->where('id_carrito', $carrito['id_carrito'])
            ->findAll();

        // Calcular total
        $total = array_reduce($items, function ($carry, $item) {
            return $carry + $item['subtotal'];
        }, 0);

        $data = [
            'title' => 'Carrito de Compras',
            'items' => $items,
            'total' => $total,
            'cart_count' => $this->getCartCount()
        ];

        return view('clientes/carrito', $data);
    }

    public function actualizarCarrito()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(404);
        }

        $id_item = $this->request->getPost('id_item');
        $cantidad = $this->request->getPost('cantidad');

        $carritoItemModel = new CarritoItemModel();
        $productoModel = new ProductoModel();

        $item = $carritoItemModel->find($id_item);
        if (!$item) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Item no encontrado'
            ]);
        }

        // Verificar stock
        $producto = $productoModel->find($item['id_producto']);
        if ($producto['stock'] < $cantidad) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Stock insuficiente'
            ]);
        }

        // Actualizar cantidad y subtotal
        $precio = $producto['precio_oferta'] ?? $producto['precio'];
        $carritoItemModel->actualizarItem($id_item, $cantidad, $precio);

        // Obtener nuevo total del carrito
        $total = $carritoItemModel->select('SUM(subtotal) as total')
            ->where('id_carrito', $item['id_carrito'])
            ->first()['total'];

        return $this->response->setJSON([
            'success' => true,
            'subtotal' => number_format($cantidad * $precio, 2),
            'total' => number_format($total, 2),
            'cart_count' => $this->getCartCount()
        ]);
    }

    public function eliminarCarrito($id_item)
    {
        $carritoItemModel = new CarritoItemModel();
        $carritoItemModel->delete($id_item);

        return $this->response->setJSON([
            'success' => true,
            'cart_count' => $this->getCartCount()
        ]);
    }

    public function checkout()
    {
        if (!$this->session->get('cliente_id')) {
            return redirect()->to('/login')->with('error', 'Debe iniciar sesión para continuar');
        }

        $carritoModel = new CarritoModel();
        $carritoItemModel = new CarritoItemModel();
        $metodoPagoModel = new MetodoPagoModel();

        $carrito = $carritoModel->where('id_cliente', $this->session->get('cliente_id'))->first();

        if (!$carrito || $this->getCartCount() == 0) {
            return redirect()->to('/carrito')->with('error', 'El carrito está vacío');
        }

        // Obtener items con información de productos
        $items = $carritoItemModel->select('
            carrito_item.*,
            producto.nombre as producto_nombre,
            producto.imagen_url,
            producto.stock
        ')
            ->join('producto', 'carrito_item.id_producto = producto.id_producto')
            ->where('id_carrito', $carrito['id_carrito'])
            ->findAll();

        // Verificar stock antes de continuar
        foreach ($items as $item) {
            if ($item['stock'] < $item['cantidad']) {
                return redirect()->to('/carrito')->with(
                    'error',
                    "Stock insuficiente para {$item['producto_nombre']}"
                );
            }
        }

        $data = [
            'title' => 'Checkout',
            'items' => $items,
            'total' => array_sum(array_column($items, 'subtotal')),
            'metodos_pago' => $metodoPagoModel->getActiveMethods(),
            'cart_count' => $this->getCartCount()
        ];

        return view('clientes/checkout', $data);
    }

    public function procesarCheckout()
    {
        if (!$this->session->get('cliente_id')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Sesión expirada'
            ]);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $carritoModel = new CarritoModel();
            $carritoItemModel = new CarritoItemModel();
            $ventaModel = new VentaModel();
            $detalleVentaModel = new DetalleVentaModel();
            $productoModel = new ProductoModel();
            $seguimientoModel = new SeguimientoPedidoModel();

            // Obtener datos del carrito
            $carrito = $carritoModel->where('id_cliente', $this->session->get('cliente_id'))->first();
            $items = $carritoItemModel->where('id_carrito', $carrito['id_carrito'])->findAll();

            // Crear venta
            $datosVenta = [
                'id_cliente' => $this->session->get('cliente_id'),
                'metodo_pago_id' => $this->request->getPost('metodo_pago'),
                'tipo_comprobante' => $this->request->getPost('tipo_comprobante'),
                'numero_comprobante' => $this->generarNumeroComprobante($this->request->getPost('tipo_comprobante')),
                'total' => array_sum(array_column($items, 'subtotal')),
                'direccion_envio' => $this->request->getPost('direccion_envio'),
                'estado' => 'completada',
                'estado_pago' => 'pendiente'
            ];

            $id_venta = $ventaModel->insert($datosVenta);

            // Crear detalles y actualizar stock
            foreach ($items as $item) {
                $detalleVentaModel->insert([
                    'id_venta' => $id_venta,
                    'id_producto' => $item['id_producto'],
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $item['precio_unitario'],
                    'subtotal' => $item['subtotal']
                ]);

                // Actualizar stock
                $producto = $productoModel->find($item['id_producto']);
                $productoModel->update($item['id_producto'], [
                    'stock' => $producto['stock'] - $item['cantidad']
                ]);
            }

            // Crear seguimiento inicial
            $seguimientoModel->insert([
                'id_venta' => $id_venta,
                'estado' => 'pendiente',
                'comentario' => 'Pedido registrado'
            ]);

            // Limpiar carrito
            $carritoItemModel->where('id_carrito', $carrito['id_carrito'])->delete();

            $db->transComplete();

            if ($db->transStatus() === false) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Error al procesar el pedido'
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Pedido procesado correctamente',
                'redirect' => base_url("checkout/confirmar/{$id_venta}")
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error en procesarCheckout: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al procesar el pedido: ' . $e->getMessage()
            ]);
        }
    }

    public function login()
    {
        log_message('debug', 'Estado inicial de sesión: ' . json_encode([
            'session_id' => session_id(),
            'cliente_id' => $this->session->get('cliente_id'),
            'isClienteLoggedIn' => $this->session->get('isClienteLoggedIn')
        ]));

        if ($this->request->getMethod() === 'post') {
            $email = $this->request->getPost('email');
            $password = $this->request->getPost('password');

            log_message('debug', 'Intento de login con email: ' . $email);

            $clienteModel = new ClienteModel();
            $cliente = $clienteModel->where('email', $email)
                ->where('is_active', 1)
                ->first();

            if ($cliente) {
                log_message('debug', 'Cliente encontrado en la base de datos');

                if (password_verify($password, $cliente['password'])) {
                    log_message('debug', 'Contraseña verificada correctamente');

                    // Limpiar la sesión actual
                    $this->session->destroy();

                    // Iniciar una nueva sesión
                    session_start();

                    // Establecer los datos de sesión
                    $this->session->set([
                        'cliente_id' => $cliente['id_cliente'],
                        'cliente_nombre' => $cliente['nombre'],
                        'cliente_email' => $cliente['email'],
                        'isClienteLoggedIn' => true
                    ]);

                    // Forzar la escritura de la sesión
                    session_write_close();

                    log_message('debug', 'Datos de sesión establecidos: ' . json_encode([
                        'cliente_id' => $this->session->get('cliente_id'),
                        'cliente_nombre' => $this->session->get('cliente_nombre'),
                        'isClienteLoggedIn' => $this->session->get('isClienteLoggedIn')
                    ]));

                    // Actualizar último login
                    $clienteModel->update($cliente['id_cliente'], [
                        'ultimo_login' => date('Y-m-d H:i:s')
                    ]);

                    // Establecer mensaje flash y redirigir
                    $this->session->setFlashdata('success', '¡Bienvenido/a ' . $cliente['nombre'] . '!');

                    return redirect()->to('/');
                } else {
                    log_message('debug', 'Contraseña incorrecta');
                }
            } else {
                log_message('debug', 'Cliente no encontrado');
            }

            return redirect()->back()
                ->with('error', 'Email o contraseña incorrectos')
                ->withInput();
        }

        return view('clientes/login');
    }

    public function registro()
    {
        if ($this->session->get('cliente_id')) {
            return redirect()->to('/');
        }

        if ($this->request->getMethod() === 'post') {
            $clienteModel = new ClienteModel();

            $rules = [
                'nombre' => [
                    'rules' => 'required|min_length[3]',
                    'errors' => [
                        'required' => 'El nombre es requerido',
                        'min_length' => 'El nombre debe tener al menos 3 caracteres'
                    ]
                ],
                'apellido' => [
                    'rules' => 'required|min_length[3]',
                    'errors' => [
                        'required' => 'El apellido es requerido',
                        'min_length' => 'El apellido debe tener al menos 3 caracteres'
                    ]
                ],
                'email' => [
                    'rules' => 'required|valid_email|is_unique[cliente.email]',
                    'errors' => [
                        'required' => 'El email es requerido',
                        'valid_email' => 'Por favor ingrese un email válido',
                        'is_unique' => 'Este email ya está registrado'
                    ]
                ],
                'password' => [
                    'rules' => 'required|min_length[6]',
                    'errors' => [
                        'required' => 'La contraseña es requerida',
                        'min_length' => 'La contraseña debe tener al menos 6 caracteres'
                    ]
                ],
                'confirm_password' => [
                    'rules' => 'required|matches[password]',
                    'errors' => [
                        'required' => 'Debe confirmar la contraseña',
                        'matches' => 'Las contraseñas no coinciden'
                    ]
                ],
                'dni' => [
                    'rules' => 'required|min_length[8]|is_unique[cliente.dni]',
                    'errors' => [
                        'required' => 'El DNI es requerido',
                        'min_length' => 'El DNI debe tener al menos 8 caracteres',
                        'is_unique' => 'Este DNI ya está registrado'
                    ]
                ]
            ];

            if (!$this->validate($rules)) {
                return redirect()->back()
                    ->withInput()
                    ->with('errors', $this->validator->getErrors());
            }

            $data = [
                'nombre' => $this->request->getPost('nombre'),
                'apellido' => $this->request->getPost('apellido'),
                'email' => $this->request->getPost('email'),
                'dni' => $this->request->getPost('dni'),
                'telefono' => $this->request->getPost('telefono'),
                'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
                'is_active' => 1,
                'fecha_registro' => date('Y-m-d H:i:s')
            ];

            try {
                $clienteModel->insert($data);
                return redirect()->to('/login')
                    ->with('success', 'Registro exitoso. Por favor, inicie sesión.');
            } catch (\Exception $e) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Error al crear la cuenta. Por favor, intente nuevamente.');
            }
        }

        return view('clientes/registro', [
            'title' => 'Registro'
        ]);
    }

    public function logout()
    {
        $this->session->destroy();
        return redirect()->to('/')->with('success', 'Sesión cerrada correctamente');
    }

    public function miCuenta()
    {
        if (!$this->session->get('cliente_id')) {
            return redirect()->to('/login');
        }

        $clienteModel = new ClienteModel();
        $ventaModel = new VentaModel();

        $cliente = $clienteModel->find($this->session->get('cliente_id'));
        $ultimosPedidos = $ventaModel->where('id_cliente', $cliente['id_cliente'])
            ->orderBy('fecha_venta', 'DESC')
            ->limit(5)
            ->findAll();

        $data = [
            'title' => 'Mi Cuenta',
            'cliente' => $cliente,
            'ultimos_pedidos' => $ultimosPedidos,
            'cart_count' => $this->getCartCount()
        ];

        return view('clientes/mi_cuenta', $data);
    }

    protected function generarNumeroComprobante($tipo)
    {
        $ventaModel = new VentaModel();

        $ultimoNumero = $ventaModel->select('numero_comprobante')
            ->where('tipo_comprobante', $tipo)
            ->orderBy('id_venta', 'DESC')
            ->first();

        if ($ultimoNumero) {
            $numero = intval(substr($ultimoNumero['numero_comprobante'], 2)) + 1;
        } else {
            $numero = 1;
        }

        $prefijo = ($tipo == 'boleta') ? 'B-' : 'F-';
        return $prefijo . str_pad($numero, 8, '0', STR_PAD_LEFT);
    }
}
