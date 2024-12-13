<?php

namespace App\Controllers;

use App\Models\CategoriaModel;
use App\Models\VentaModel;
use App\Models\ClienteModel;
use App\Models\DetalleVentaModel;
use App\Models\ProductoModel;
use App\Models\MetodoPagoModel;
use App\Models\SeguimientoPedidoModel;
use App\Models\CarritoModel;
use App\Models\CarritoItemModel;

class Ventas extends BaseController
{

    public function index()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }

        $db = \Config\Database::connect();

        // Consulta principal para obtener ventas con información del cliente y usuario
        $builder = $db->table('venta');
        $builder->select('
            venta.*,
            cliente.nombre as cliente_nombre,
            cliente.apellido as cliente_apellido,
            cliente.dni as cliente_dni,
            cliente.telefono as cliente_telefono,
            cliente.email as cliente_email,
            cliente.direccion as cliente_direccion,
            usuario.nombre as usuario_nombre,
            usuario.apellido as usuario_apellido,
            usuario.email as usuario_email,
            metodo_pago.nombre as metodo_pago_nombre
        ');
        $builder->join('cliente', 'venta.id_cliente = cliente.id_cliente', 'left');
        $builder->join('usuario', 'venta.id_usuario = usuario.id_usuario', 'left');
        $builder->join('metodo_pago', 'venta.metodo_pago_id = metodo_pago.id_metodo_pago', 'left');
        $builder->orderBy('venta.fecha_venta', 'DESC');

        $ventas = $builder->get()->getResultArray();

        // Obtener los detalles de cada venta
        foreach ($ventas as &$venta) {
            // Consulta para obtener los productos de cada venta
            $detallesBuilder = $db->table('detalle_venta');
            $detallesBuilder->select('
                detalle_venta.*,
                producto.nombre as producto_nombre,
                producto.codigo_barras,
                producto.descripcion as producto_descripcion,
                categoria.nombre as categoria_nombre
            ');
            $detallesBuilder->join('producto', 'detalle_venta.id_producto = producto.id_producto');
            $detallesBuilder->join('categoria', 'producto.id_categoria = categoria.id_categoria');
            $detallesBuilder->where('detalle_venta.id_venta', $venta['id_venta']);

            $venta['detalles'] = $detallesBuilder->get()->getResultArray();

            // Obtener seguimiento del pedido
            $seguimientoBuilder = $db->table('seguimiento_pedido');
            $seguimientoBuilder->where('id_venta', $venta['id_venta']);
            $seguimientoBuilder->orderBy('fecha_actualizacion', 'DESC');
            $venta['seguimiento'] = $seguimientoBuilder->get()->getResultArray();

            // Calcular información adicional
            $venta['cantidad_productos'] = array_sum(array_column($venta['detalles'], 'cantidad'));
            $venta['cliente_nombre_completo'] = trim($venta['cliente_nombre'] . ' ' . $venta['cliente_apellido']);
            $venta['usuario_nombre_completo'] = trim($venta['usuario_nombre'] . ' ' . $venta['usuario_apellido']);
            $venta['fecha_formateada'] = date('d/m/Y H:i:s', strtotime($venta['fecha_venta']));

            // Estado de la venta y pago
            $venta['estado_badge'] = $this->getBadgeInfo($venta['estado']);
            $venta['estado_pago_badge'] = $this->getBadgeInfo($venta['estado_pago']);

            // Formatear montos
            $venta['total_formateado'] = 'S/ ' . number_format($venta['total'], 2);
            $venta['costo_envio_formateado'] = 'S/ ' . number_format($venta['costo_envio'], 2);
            $venta['total_final_formateado'] = 'S/ ' . number_format($venta['total'] + $venta['costo_envio'], 2);
        }

        $data = [
            'ventas' => $ventas,
            'total_ventas' => count($ventas),
            'total_monto' => array_sum(array_column($ventas, 'total')),
            'ventas_completadas' => count(array_filter($ventas, fn($v) => $v['estado'] === 'completada')),
            'ventas_anuladas' => count(array_filter($ventas, fn($v) => $v['estado'] === 'anulada')),
            'ventas_pendientes_pago' => count(array_filter($ventas, fn($v) => $v['estado_pago'] === 'pendiente'))
        ];

        return view('ventas', $data);
    }

    public function create()
    {
        // Verificar la sesión de administrador
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to(base_url('admin/login'));
        }

        try {
            $clienteModel = new ClienteModel();
            $metodoPagoModel = new MetodoPagoModel();
            $productoModel = new ProductoModel();
            $categoriaModel = new CategoriaModel();

            $data = [
                'clientes' => $clienteModel->findAll(),
                'metodos_pago' => $metodoPagoModel->findAll(),
                'productos' => $productoModel->findAll(),
                'categorias' => $categoriaModel->findAll()
            ];

            return view('ventas/create_venta', $data);
        } catch (\Exception $e) {
            log_message('error', 'Error en create venta: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al cargar el formulario de venta');
        }
    }

    public function getBadgeInfo($estado)
    {
        $badges = [
            'completada' => ['text' => 'Completada', 'color' => 'green'],
            'anulada' => ['text' => 'Anulada', 'color' => 'red'],
            'pendiente' => ['text' => 'Pendiente', 'color' => 'yellow'],
            'pagado' => ['text' => 'Pagado', 'color' => 'green'],
            'rechazado' => ['text' => 'Rechazado', 'color' => 'red']
        ];

        return $badges[$estado] ?? ['text' => ucfirst($estado), 'color' => 'gray'];
    }

    public function store()
    {
        try {
            $jsonData = $this->request->getJSON(true);
            log_message('debug', 'Datos recibidos en store: ' . json_encode($jsonData));

            // Validación detallada de los datos recibidos
            $requiredFields = [
                'id_cliente',
                'id_usuario',
                'metodo_pago_id',
                'tipo_comprobante',
                'numero_comprobante',
                'total',
                'detalles'
            ];

            $missingFields = [];
            foreach ($requiredFields as $field) {
                if (!isset($jsonData[$field]) || empty($jsonData[$field])) {
                    $missingFields[] = $field;
                }
            }

            if (!empty($missingFields)) {
                throw new \Exception('Campos requeridos faltantes: ' . implode(', ', $missingFields));
            }

            $db = \Config\Database::connect();
            $db->transStart();

            $ventaModel = new VentaModel();

            // Preparar datos de la venta
            $data = [
                'id_cliente' => $jsonData['id_cliente'],
                'id_usuario' => $jsonData['id_usuario'],
                'metodo_pago_id' => $jsonData['metodo_pago_id'],
                'tipo_comprobante' => $jsonData['tipo_comprobante'],
                'numero_comprobante' => $jsonData['numero_comprobante'],
                'total' => $jsonData['total'],
                'estado' => 'completada',
                'estado_pago' => 'pagado',
                'referencia_pago' => isset($jsonData['monto_recibido']) ? 'EFECTIVO-' . $jsonData['monto_recibido'] : null
            ];
            
            // Remover campos nulos
            $data = array_filter($data, function($value) {
                return $value !== null;
            });

            log_message('debug', 'Datos preparados para inserción: ' . json_encode($data));

            // Validar la existencia de registros relacionados
            $clienteModel = new ClienteModel();
            $metodoPagoModel = new MetodoPagoModel();

            if (!$clienteModel->find($data['id_cliente'])) {
                throw new \Exception('Cliente no encontrado');
            }

            if (!$metodoPagoModel->find($data['metodo_pago_id'])) {
                throw new \Exception('Método de pago no válido');
            }

            // Insertar venta
            $id_venta = $ventaModel->insert($data);

            if (!$id_venta) {
                throw new \Exception('Error al insertar venta: ' . json_encode($ventaModel->errors()));
            }

            log_message('debug', 'Venta creada con ID: ' . $id_venta);

            // Procesar detalles
            if (!empty($jsonData['detalles'])) {
                $detalleVentaModel = new DetalleVentaModel();
                $productoModel = new ProductoModel();

                foreach ($jsonData['detalles'] as $detalle) {
                    $detalleData = [
                        'id_venta' => $id_venta,
                        'id_producto' => $detalle['id_producto'],
                        'cantidad' => $detalle['cantidad'],
                        'precio_unitario' => $detalle['precio_unitario'],
                        'subtotal' => $detalle['subtotal']
                    ];

                    if (!$detalleVentaModel->insert($detalleData)) {
                        throw new \Exception('Error al insertar detalle de venta: ' . json_encode($detalleVentaModel->errors()));
                    }

                    // Actualizar stock
                    $producto = $productoModel->find($detalle['id_producto']);
                    if (!$producto) {
                        throw new \Exception('Producto no encontrado: ' . $detalle['id_producto']);
                    }

                    if (!$productoModel->update($detalle['id_producto'], [
                        'stock' => $producto['stock'] - $detalle['cantidad']
                    ])) {
                        throw new \Exception('Error al actualizar stock: ' . json_encode($productoModel->errors()));
                    }
                }
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Error en la transacción de la base de datos');
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Venta registrada correctamente',
                'id_venta' => $id_venta
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error en store: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Error al procesar la venta: ' . $e->getMessage()
            ]);
        }
    }

    public function actualizarEstadoPago($id_venta)
    {
        try {
            $jsonData = $this->request->getJSON(true);

            if (!isset($jsonData['estado_pago'])) {
                return $this->response->setStatusCode(400)->setJSON([
                    'success' => false,
                    'message' => 'Estado de pago no especificado'
                ]);
            }

            $ventaModel = new VentaModel();
            $seguimientoModel = new SeguimientoPedidoModel();

            $db = \Config\Database::connect();
            $db->transStart();

            // Actualizar estado de pago
            $ventaModel->update($id_venta, [
                'estado_pago' => $jsonData['estado_pago'],
                'referencia_pago' => $jsonData['referencia_pago'] ?? null
            ]);

            // Agregar seguimiento
            $seguimientoModel->insert([
                'id_venta' => $id_venta,
                'estado' => $jsonData['estado_pago'] === 'pagado' ? 'confirmado' : 'pendiente',
                'comentario' => "Pago {$jsonData['estado_pago']}. " . ($jsonData['comentario'] ?? '')
            ]);

            $db->transComplete();

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Estado de pago actualizado correctamente'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error al actualizar estado de pago: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Error al actualizar estado de pago: ' . $e->getMessage()
            ]);
        }
    }

    public function actualizarSeguimiento($id_venta)
    {
        try {
            $jsonData = $this->request->getJSON(true);

            if (!isset($jsonData['estado'])) {
                return $this->response->setStatusCode(400)->setJSON([
                    'success' => false,
                    'message' => 'Estado no especificado'
                ]);
            }

            $seguimientoModel = new SeguimientoPedidoModel();

            $seguimientoModel->insert([
                'id_venta' => $id_venta,
                'estado' => $jsonData['estado'],
                'comentario' => $jsonData['comentario'] ?? null
            ]);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Seguimiento actualizado correctamente'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error al actualizar seguimiento: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Error al actualizar seguimiento: ' . $e->getMessage()
            ]);
        }
    }


    public function getDetalle($id_venta = null)
    {
        if (!$id_venta) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'ID de venta no proporcionado'
            ]);
        }

        try {
            $db = \Config\Database::connect();

            // Consulta principal con LEFT JOIN para obtener todos los datos necesarios
            $query = $db->query("
                SELECT 
                    v.*,
                    c.nombre as cliente_nombre,
                    c.apellido as cliente_apellido,
                    c.dni as cliente_dni,
                    c.telefono as cliente_telefono,
                    c.email as cliente_email,
                    c.direccion as cliente_direccion,
                    u.nombre as usuario_nombre,
                    u.apellido as usuario_apellido,
                    u.email as usuario_email
                FROM venta v
                LEFT JOIN cliente c ON v.id_cliente = c.id_cliente
                LEFT JOIN usuario u ON v.id_usuario = u.id_usuario
                WHERE v.id_venta = ?
            ", [$id_venta]);

            $venta = $query->getRowArray();

            if (!$venta) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Venta no encontrada'
                ]);
            }

            // Consulta para obtener los detalles de productos
            $queryDetalles = $db->query("
                SELECT 
                    dv.*,
                    p.nombre as producto_nombre,
                    p.codigo_barras,
                    p.descripcion as producto_descripcion,
                    c.nombre as categoria_nombre
                FROM detalle_venta dv
                JOIN producto p ON dv.id_producto = p.id_producto
                JOIN categoria c ON p.id_categoria = c.id_categoria
                WHERE dv.id_venta = ?
            ", [$id_venta]);

            $detalles = $queryDetalles->getResultArray();

            // Debug: Imprimir las consultas SQL y sus resultados
            log_message('debug', 'SQL Venta: ' . $db->getLastQuery());
            log_message('debug', 'Datos Venta: ' . json_encode($venta));
            log_message('debug', 'SQL Detalles: ' . $db->getLastQuery());
            log_message('debug', 'Datos Detalles: ' . json_encode($detalles));

            // Formatear los datos
            $venta['cliente_nombre_completo'] = trim($venta['cliente_nombre'] . ' ' . $venta['cliente_apellido']);
            $venta['usuario_nombre_completo'] = trim($venta['usuario_nombre'] . ' ' . $venta['usuario_apellido']);
            $venta['fecha_formateada'] = date('d/m/Y H:i:s', strtotime($venta['fecha_venta']));
            $venta['total_formateado'] = 'S/ ' . number_format($venta['total'], 2);
            $venta['estado_badge'] = [
                'text' => ucfirst($venta['estado']),
                'color' => $venta['estado'] === 'completada' ? 'green' : 'red'
            ];

            // Formatear los detalles
            foreach ($detalles as &$detalle) {
                $detalle['subtotal_formateado'] = 'S/ ' . number_format($detalle['subtotal'], 2);
                $detalle['precio_unitario_formateado'] = 'S/ ' . number_format($detalle['precio_unitario'], 2);
            }

            // Verificar si hay datos del cliente
            $venta['tiene_cliente'] = !empty($venta['cliente_nombre']);

            return $this->response->setJSON([
                'success' => true,
                'venta' => $venta,
                'detalles' => $detalles,
                'debug' => [
                    'sql_venta' => $db->getLastQuery(),
                    'sql_detalles' => $db->getLastQuery()
                ]
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error en getDetalle: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Error al obtener los detalles de la venta: ' . $e->getMessage()
            ]);
        }
    }

    public function edit($id)
    {
        $ventaModel = new VentaModel();
        $data['venta'] = $ventaModel->find($id);

        $clienteModel = new ClienteModel();

        $data['clientes'] = $clienteModel->findAll();

        return view('ventas/edit_venta', $data);
    }

    public function update($id)
    {
        $ventaModel = new VentaModel();
        $data = [
            'id_cliente' => $this->request->getPost('id_cliente'),
            'id_metodo_pago' => $this->request->getPost('id_metodo_pago'),
            'total' => $this->request->getPost('total'),
            'estado' => $this->request->getPost('estado'),
        ];
        $ventaModel->update($id, $data);
        return redirect()->to(base_url('ventas'));
    }

    public function getLastComprobanteNumbers()
    {
        try {
            $ventaModel = new VentaModel();

            // Obtener último número de boleta
            $lastBoleta = $ventaModel->select('numero_comprobante')
                ->where('tipo_comprobante', 'boleta')
                ->orderBy('id_venta', 'DESC')
                ->limit(1)
                ->get()
                ->getRowArray();

            // Obtener último número de factura
            $lastFactura = $ventaModel->select('numero_comprobante')
                ->where('tipo_comprobante', 'factura')
                ->orderBy('id_venta', 'DESC')
                ->limit(1)
                ->get()
                ->getRowArray();

            // Extraer los números de los comprobantes
            $ultimaBoleta = 0;
            if ($lastBoleta) {
                // Extraer el número después del guión (B-00000001 → 1)
                preg_match('/B-(\d+)/', $lastBoleta['numero_comprobante'], $matches);
                $ultimaBoleta = isset($matches[1]) ? (int)$matches[1] : 0;
            }

            $ultimaFactura = 0;
            if ($lastFactura) {
                // Extraer el número después del guión (F-00000001 → 1)
                preg_match('/F-(\d+)/', $lastFactura['numero_comprobante'], $matches);
                $ultimaFactura = isset($matches[1]) ? (int)$matches[1] : 0;
            }

            return $this->response->setJSON([
                'boleta' => $ultimaBoleta,
                'factura' => $ultimaFactura
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error al obtener números de comprobante: ' . $e->getMessage());
            return $this->response->setStatusCode(500)
                ->setJSON([
                    'error' => 'Error al obtener números de comprobante',
                    'message' => $e->getMessage()
                ]);
        }
    }

    public function delete($id)
    {
        $db = \Config\Database::connect();
        $detalleVentaModel = new DetalleVentaModel();
        $ventaModel = new VentaModel();

        // Iniciar una transacción para asegurar la integridad de los datos
        $db->transStart();

        // Eliminar los detalles de la venta
        $detalleVentaModel->where('id_venta', $id)->delete();

        // Eliminar la venta
        $ventaModel->delete($id);

        // Completar la transacción
        $db->transComplete();

        if ($db->transStatus() === FALSE) {
            // Si hubo un error, deshacer la transacción
            $db->transRollback();
            return $this->response->setJSON(['success' => false, 'message' => 'Error al eliminar la venta']);
        } else {
            // Si todo salió bien, confirmar la transacción
            $db->transCommit();
            return $this->response->setJSON(['success' => true, 'message' => 'Venta eliminada correctamente']);
        }
    }
}
