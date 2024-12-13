<?php
namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;
use App\Models\VentaModel;
use App\Models\ProductoModel;
use App\Models\ClienteModel;
use App\Models\CategoriaModel;
use App\Models\DetalleVentaModel;

class Home extends BaseController
{
    public function index(): ResponseInterface|string
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to(base_url('admin/login'));
        }

        try {
            $db = \Config\Database::connect();
            
            // 1. Ventas Totales
            $ventaModel = new VentaModel();
            $totalVentas = $ventaModel->selectSum('total')
                                    ->where('estado', 'completada')
                                    ->first();
            
            // Calcular porcentaje vs mes anterior
            $currentMonth = date('Y-m');
            $lastMonth = date('Y-m', strtotime('-1 month'));
            
            $ventasActuales = $ventaModel->selectSum('total')
                                        ->where('estado', 'completada')
                                        ->where('DATE_FORMAT(fecha_venta, "%Y-%m")', $currentMonth)
                                        ->first();
            
            $ventasAnteriores = $ventaModel->selectSum('total')
                                          ->where('estado', 'completada')
                                          ->where('DATE_FORMAT(fecha_venta, "%Y-%m")', $lastMonth)
                                          ->first();
            
            $porcentajeVentas = 0;
            if ($ventasAnteriores && $ventasAnteriores['total'] > 0) {
                $porcentajeVentas = (($ventasActuales['total'] - $ventasAnteriores['total']) / $ventasAnteriores['total']) * 100;
            }

            // 2. Productos Vendidos
            $detalleVentaModel = new DetalleVentaModel();
            $totalProductos = $detalleVentaModel->selectSum('cantidad')
                                               ->join('venta', 'venta.id_venta = detalle_venta.id_venta')
                                               ->where('venta.estado', 'completada')
                                               ->first();
            
            // Porcentaje vs mes anterior
            $productosActuales = $detalleVentaModel->selectSum('cantidad')
                                                  ->join('venta', 'venta.id_venta = detalle_venta.id_venta')
                                                  ->where('venta.estado', 'completada')
                                                  ->where('DATE_FORMAT(venta.fecha_venta, "%Y-%m")', $currentMonth)
                                                  ->first();
            
            $productosAnteriores = $detalleVentaModel->selectSum('cantidad')
                                                    ->join('venta', 'venta.id_venta = detalle_venta.id_venta')
                                                    ->where('venta.estado', 'completada')
                                                    ->where('DATE_FORMAT(venta.fecha_venta, "%Y-%m")', $lastMonth)
                                                    ->first();
            
            $porcentajeProductos = 0;
            if ($productosAnteriores && $productosAnteriores['cantidad'] > 0) {
                $porcentajeProductos = (($productosActuales['cantidad'] - $productosAnteriores['cantidad']) / $productosAnteriores['cantidad']) * 100;
            }

            // 3. Clientes Nuevos - Usando fecha_registro de la tabla cliente
            $clienteModel = new ClienteModel();
            $totalClientes = $clienteModel->countAllResults();
            
            $clientesNuevos = $clienteModel->where('DATE_FORMAT(fecha_registro, "%Y-%m")', $currentMonth)
                                         ->countAllResults();
            
            $clientesAnteriores = $clienteModel->where('DATE_FORMAT(fecha_registro, "%Y-%m")', $lastMonth)
                                             ->countAllResults();
            
            $porcentajeClientes = 0;
            if ($clientesAnteriores > 0) {
                $porcentajeClientes = (($clientesNuevos - $clientesAnteriores) / $clientesAnteriores) * 100;
            }

            // 4. Stock Bajo
            $productoModel = new ProductoModel();
            $stockBajo = $productoModel->where('stock <=', 10)
                                     ->where('is_active', 1)
                                     ->countAllResults();

            // 5. Ventas por Categoría
            $ventasPorCategoria = $db->query("
                SELECT 
                    c.nombre as categoria,
                    SUM(dv.cantidad * dv.precio_unitario) as total
                FROM detalle_venta dv
                JOIN producto p ON p.id_producto = dv.id_producto
                JOIN categoria c ON c.id_categoria = p.id_categoria
                JOIN venta v ON v.id_venta = dv.id_venta
                WHERE v.estado = 'completada'
                GROUP BY c.id_categoria
                ORDER BY total DESC
            ")->getResult();

            // 6. Productos más Vendidos con nuevos campos
            $productosMasVendidos = $db->query("
                SELECT 
                    p.nombre,
                    p.slug,
                    p.precio,
                    p.precio_oferta,
                    SUM(dv.cantidad) as cantidad_vendida,
                    SUM(dv.cantidad * dv.precio_unitario) as total_ventas
                FROM detalle_venta dv
                JOIN producto p ON p.id_producto = dv.id_producto
                JOIN venta v ON v.id_venta = dv.id_venta
                WHERE v.estado = 'completada'
                AND p.is_active = 1
                GROUP BY p.id_producto
                ORDER BY cantidad_vendida DESC
                LIMIT 5
            ")->getResult();

            // 7. Órdenes Recientes con estado de pago
            $ordenesRecientes = $ventaModel->select('
                    venta.*, 
                    cliente.nombre as cliente_nombre, 
                    cliente.apellido as cliente_apellido,
                    cliente.dni as cliente_dni,
                    metodo_pago.nombre as metodo_pago_nombre,
                    (SELECT SUM(cantidad) FROM detalle_venta WHERE id_venta = venta.id_venta) as total_items
                ')
                ->join('cliente', 'cliente.id_cliente = venta.id_cliente', 'left')
                ->join('metodo_pago', 'metodo_pago.id_metodo_pago = venta.metodo_pago_id', 'left')
                ->orderBy('fecha_venta', 'DESC')
                ->limit(5)
                ->find();

            // Preparar datos para la vista
            $data = [
                'estadisticas' => [
                    'ventas_totales' => [
                        'valor' => $totalVentas['total'] ?? 0,
                        'porcentaje' => round($porcentajeVentas, 1),
                        'tendencia' => $porcentajeVentas >= 0 ? 'up' : 'down'
                    ],
                    'productos_vendidos' => [
                        'valor' => $totalProductos['cantidad'] ?? 0,
                        'porcentaje' => round($porcentajeProductos, 1),
                        'tendencia' => $porcentajeProductos >= 0 ? 'up' : 'down'
                    ],
                    'clientes_nuevos' => [
                        'valor' => $clientesNuevos,
                        'porcentaje' => round($porcentajeClientes, 1),
                        'tendencia' => $porcentajeClientes >= 0 ? 'up' : 'down'
                    ],
                    'stock_bajo' => [
                        'valor' => $stockBajo
                    ]
                ],
                'ventas_categoria' => $ventasPorCategoria,
                'productos_populares' => $productosMasVendidos,
                'ordenes_recientes' => $ordenesRecientes
            ];

                // echo "<pre>";
                // print_r($data);
                // echo "</pre>";
                // exit();
            return view('home', $data);

        } catch (\Exception $e) {
            log_message('error', '[Home::index] Error: ' . $e->getMessage());
            return view('home', [
                'error' => 'Hubo un error al cargar los datos del dashboard'. $e->getMessage()
            ]);
        }
    }
}