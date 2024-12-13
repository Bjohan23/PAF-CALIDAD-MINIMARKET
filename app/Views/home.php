<?= $this->extend('base') ?>

<?= $this->section('title') ?>Dashboard<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container mx-auto px-3 sm:px-4 lg:px-6 py-4 sm:py-6 lg:py-8">
    <?php if (isset($error)): ?>
    <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
        <span class="block sm:inline"><?= $error ?></span>
    </div>
    <?php endif; ?>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 lg:gap-6 mb-6">
        <!-- Ventas Totales -->
        <div class="bg-white dark:bg-gray-800 p-4 sm:p-6 rounded-xl shadow-sm transition-colors duration-200">
            <div class="flex items-center justify-between mb-3 sm:mb-4">
                <h3 class="text-gray-500 dark:text-gray-400 text-xs sm:text-sm">Ventas Totales</h3>
                <span class="p-1.5 sm:p-2 bg-green-100 dark:bg-green-900/50 rounded-lg">
                    <i class="fas fa-dollar-sign w-5 h-5 sm:w-6 sm:h-6 text-green-600 dark:text-green-400"></i>
                </span>
            </div>
            <p class="text-xl sm:text-2xl font-semibold text-gray-900 dark:text-white">
                S/ <?= number_format($estadisticas['ventas_totales']['valor'], 2) ?>
            </p>
            <p class="<?= $estadisticas['ventas_totales']['tendencia'] === 'up' ? 'text-green-500 dark:text-green-400' : 'text-red-500 dark:text-red-400' ?> text-xs sm:text-sm mt-2">
                <?= $estadisticas['ventas_totales']['porcentaje'] ?>% vs mes anterior
            </p>
        </div>

        <!-- Productos Vendidos -->
        <div class="bg-white dark:bg-gray-800 p-4 sm:p-6 rounded-xl shadow-sm transition-colors duration-200">
            <div class="flex items-center justify-between mb-3 sm:mb-4">
                <h3 class="text-gray-500 dark:text-gray-400 text-xs sm:text-sm">Productos Vendidos</h3>
                <span class="p-1.5 sm:p-2 bg-blue-100 dark:bg-blue-900/50 rounded-lg">
                    <i class="fas fa-box w-5 h-5 sm:w-6 sm:h-6 text-blue-600 dark:text-blue-400"></i>
                </span>
            </div>
            <p class="text-xl sm:text-2xl font-semibold text-gray-900 dark:text-white">
                <?= number_format($estadisticas['productos_vendidos']['valor']) ?>
            </p>
            <p class="<?= $estadisticas['productos_vendidos']['tendencia'] === 'up' ? 'text-blue-500 dark:text-blue-400' : 'text-red-500 dark:text-red-400' ?> text-xs sm:text-sm mt-2">
                <?= $estadisticas['productos_vendidos']['porcentaje'] ?>% vs mes anterior
            </p>
        </div>

        <!-- Clientes Nuevos -->
        <div class="bg-white dark:bg-gray-800 p-4 sm:p-6 rounded-xl shadow-sm transition-colors duration-200">
            <div class="flex items-center justify-between mb-3 sm:mb-4">
                <h3 class="text-gray-500 dark:text-gray-400 text-xs sm:text-sm">Clientes Nuevos</h3>
                <span class="p-1.5 sm:p-2 bg-purple-100 dark:bg-purple-900/50 rounded-lg">
                    <i class="fas fa-users w-5 h-5 sm:w-6 sm:h-6 text-purple-600 dark:text-purple-400"></i>
                </span>
            </div>
            <p class="text-xl sm:text-2xl font-semibold text-gray-900 dark:text-white">
                <?= number_format($estadisticas['clientes_nuevos']['valor']) ?>
            </p>
            <p class="<?= $estadisticas['clientes_nuevos']['tendencia'] === 'up' ? 'text-purple-500 dark:text-purple-400' : 'text-red-500 dark:text-red-400' ?> text-xs sm:text-sm mt-2">
                <?= $estadisticas['clientes_nuevos']['porcentaje'] ?>% vs mes anterior
            </p>
        </div>

        <!-- Stock Bajo -->
        <div class="bg-white dark:bg-gray-800 p-4 sm:p-6 rounded-xl shadow-sm transition-colors duration-200">
            <div class="flex items-center justify-between mb-3 sm:mb-4">
                <h3 class="text-gray-500 dark:text-gray-400 text-xs sm:text-sm">Stock Bajo</h3>
                <span class="p-1.5 sm:p-2 bg-red-100 dark:bg-red-900/50 rounded-lg">
                    <i class="fas fa-exclamation-triangle w-5 h-5 sm:w-6 sm:h-6 text-red-600 dark:text-red-400"></i>
                </span>
            </div>
            <p class="text-xl sm:text-2xl font-semibold text-gray-900 dark:text-white">
                <?= number_format($estadisticas['stock_bajo']['valor']) ?>
            </p>
            <p class="text-red-500 dark:text-red-400 text-xs sm:text-sm mt-2">
                Productos por reabastecer
            </p>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 lg:gap-8 mb-6">
        <!-- Ventas por Categoría -->
        <div class="bg-white dark:bg-gray-800 p-4 sm:p-6 rounded-xl shadow-sm transition-colors duration-200">
            <h3 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white mb-4">
                <i class="fas fa-chart-pie mr-2 text-blue-600 dark:text-blue-400"></i>
                Ventas por Categoría
            </h3>
            <div class="relative h-48 sm:h-64">
                <canvas id="salesChart"></canvas>
            </div>
        </div>

        <!-- Productos más Vendidos -->
        <div class="bg-white dark:bg-gray-800 p-4 sm:p-6 rounded-xl shadow-sm transition-colors duration-200">
            <h3 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white mb-4">
                <i class="fas fa-crown mr-2 text-yellow-600 dark:text-yellow-400"></i>
                Productos más Vendidos
            </h3>
            <div class="relative h-48 sm:h-64">
                <canvas id="productsChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Recent Orders Table -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm transition-colors duration-200">
        <div class="p-4 sm:p-6 border-b border-gray-100 dark:border-gray-700">
            <h3 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                <i class="fas fa-shopping-cart mr-2 text-blue-600 dark:text-blue-400"></i>
                Órdenes Recientes
            </h3>
        </div>

        <!-- Tabla responsiva con scroll horizontal -->
        <div class="overflow-x-auto">
            <div class="inline-block min-w-full align-middle">
                <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Comprobante
                            </th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Cliente
                            </th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Productos
                            </th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Total
                            </th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Estado
                            </th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider hidden sm:table-cell">
                                Fecha
                            </th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700">
                        <?php foreach ($ordenes_recientes as $orden): ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                                <?= strtoupper($orden['tipo_comprobante']) ?> <?= $orden['numero_comprobante'] ?>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                                <?= $orden['cliente_nombre'] ?> <?= $orden['cliente_apellido'] ?>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                                <?= $orden['total_items'] ?> items
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                                S/ <?= number_format($orden['total'], 2) ?>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                    <?= $orden['estado'] === 'completada' 
                                        ? 'text-green-600 dark:text-green-400 bg-green-100 dark:bg-green-900/50'
                                        : 'text-red-600 dark:text-red-400 bg-red-100 dark:bg-red-900/50' ?>">
                                    <?= ucfirst($orden['estado']) ?>
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300 hidden sm:table-cell">
                                <?= date('d M Y H:i', strtotime($orden['fecha_venta'])) ?>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm">
                                <button onclick="verDetalleVenta(<?= $orden['id_venta'] ?>)"
                                        class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300">
                                    <span class="hidden sm:inline">Ver Detalles</span>
                                    <span class="sm:hidden">Ver</span>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="px-4 sm:px-6 py-3 sm:py-4 border-t border-gray-100 dark:border-gray-700">
            <a href="<?= base_url('admin/ventas') ?>" class="text-xs sm:text-sm font-medium text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300">
                Ver todas las órdenes →
            </a>
        </div>
    </div>
</div>

<!-- Modal Container -->
<div id="modalContainer"></div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Configuración común para los gráficos
    const chartConfig = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    padding: 20,
                    color: document.documentElement.classList.contains('dark') ? '#9ca3af' : '#4b5563',
                    font: {
                        size: window.innerWidth < 768 ? 10 : 12
                    }
                }
            }
        }
    };

    // Gráfico de Ventas por Categoría
const salesCtx = document.getElementById('salesChart').getContext('2d');
new Chart(salesCtx, {
    type: 'doughnut',
    data: {
        labels: <?= json_encode(array_column($ventas_categoria, 'categoria')) ?>,
        datasets: [{
            data: <?= json_encode(array_column($ventas_categoria, 'total')) ?>,
            backgroundColor: [
                'rgba(59, 130, 246, 0.8)',   // blue-500
                'rgba(16, 185, 129, 0.8)',   // green-500
                'rgba(249, 115, 22, 0.8)',   // orange-500
                'rgba(139, 92, 246, 0.8)',   // purple-500
                'rgba(236, 72, 153, 0.8)'    // pink-500
            ],
            borderColor: [
                'rgba(59, 130, 246, 1)',
                'rgba(16, 185, 129, 1)',
                'rgba(249, 115, 22, 1)',
                'rgba(139, 92, 246, 1)',
                'rgba(236, 72, 153, 1)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        ...chartConfig,
        cutout: '65%',
        plugins: {
            ...chartConfig.plugins,
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const label = context.label || '';
                        const value = context.raw || 0;
                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                        const percentage = ((value * 100) / total).toFixed(1);
                        return `${label}: S/ ${value.toFixed(2)} (${percentage}%)`;
                    }
                }
            }
        }
    }
});

// Gráfico de Productos más Vendidos
const productsCtx = document.getElementById('productsChart').getContext('2d');
new Chart(productsCtx, {
    type: 'bar',
    data: {
        labels: <?= json_encode(array_column($productos_populares, 'nombre')) ?>,
        datasets: [{
            label: 'Unidades Vendidas',
            data: <?= json_encode(array_column($productos_populares, 'cantidad_vendida')) ?>,
            backgroundColor: 'rgba(59, 130, 246, 0.8)',
            borderColor: 'rgba(59, 130, 246, 1)',
            borderWidth: 1,
            borderRadius: 4
        },
        {
            label: 'Total Ventas (S/)',
            data: <?= json_encode(array_column($productos_populares, 'total_ventas')) ?>,
            backgroundColor: 'rgba(16, 185, 129, 0.8)',
            borderColor: 'rgba(16, 185, 129, 1)',
            borderWidth: 1,
            borderRadius: 4
        }]
    },
    options: {
        ...chartConfig,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    color: document.documentElement.classList.contains('dark') ? '#9ca3af' : '#4b5563',
                    callback: function(value) {
                        return value.toLocaleString();
                    }
                },
                grid: {
                    color: document.documentElement.classList.contains('dark') ? 'rgba(243, 244, 246, 0.1)' : 'rgba(243, 244, 246, 0.6)'
                }
            },
            x: {
                ticks: {
                    color: document.documentElement.classList.contains('dark') ? '#9ca3af' : '#4b5563',
                    maxRotation: 45,
                    minRotation: 45
                },
                grid: {
                    display: false
                }
            }
        },
        plugins: {
            ...chartConfig.plugins,
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const label = context.dataset.label || '';
                        const value = context.raw || 0;
                        if (context.datasetIndex === 0) {
                            return `${label}: ${value.toLocaleString()} unidades`;
                        }
                        return `${label}: S/ ${value.toFixed(2)}`;
                    }
                }
            }
        }
    }
});

// Actualizar gráficos cuando cambia el tema
document.addEventListener('dark-mode', function(e) {
    const isDark = e.detail;
    const textColor = isDark ? '#9ca3af' : '#4b5563';
    const gridColor = isDark ? 'rgba(243, 244, 246, 0.1)' : 'rgba(243, 244, 246, 0.6)';
    
    [salesChart, productsChart].forEach(chart => {
        if (chart) {
            // Actualizar colores de leyendas
            chart.options.plugins.legend.labels.color = textColor;
            
            // Actualizar colores de ejes si es un gráfico de barras
            if (chart.config.type === 'bar') {
                chart.options.scales.y.ticks.color = textColor;
                chart.options.scales.x.ticks.color = textColor;
                chart.options.scales.y.grid.color = gridColor;
            }
            
            chart.update();
        }
    });
});

});
</script>
<?= $this->endSection() ?>