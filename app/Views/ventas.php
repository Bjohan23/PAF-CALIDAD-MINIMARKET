<?= $this->extend('base') ?>

<?= $this->section('title') ?>Ventas<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container mx-auto px-4 py-8">
    <!-- Resumen de Ventas -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-gray-500 dark:text-gray-400 text-sm">Total Ventas</h3>
                <span class="p-2 bg-blue-100 dark:bg-blue-900/50 rounded-lg">
                    <i class="fas fa-shopping-cart text-blue-600 dark:text-blue-400"></i>
                </span>
            </div>
            <p class="text-2xl font-semibold text-gray-900 dark:text-white"><?= $total_ventas ?></p>
        </div>

        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-gray-500 dark:text-gray-400 text-sm">Monto Total</h3>
                <span class="p-2 bg-green-100 dark:bg-green-900/50 rounded-lg">
                    <i class="fas fa-dollar-sign text-green-600 dark:text-green-400"></i>
                </span>
            </div>
            <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                S/ <?= number_format($total_monto, 2) ?>
            </p>
        </div>

        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-gray-500 dark:text-gray-400 text-sm">Ventas Completadas</h3>
                <span class="p-2 bg-green-100 dark:bg-green-900/50 rounded-lg">
                    <i class="fas fa-check text-green-600 dark:text-green-400"></i>
                </span>
            </div>
            <p class="text-2xl font-semibold text-gray-900 dark:text-white"><?= $ventas_completadas ?></p>
        </div>

        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-gray-500 dark:text-gray-400 text-sm">Ventas Anuladas</h3>
                <span class="p-2 bg-red-100 dark:bg-red-900/50 rounded-lg">
                    <i class="fas fa-times text-red-600 dark:text-red-400"></i>
                </span>
            </div>
            <p class="text-2xl font-semibold text-gray-900 dark:text-white"><?= $ventas_anuladas ?></p>
        </div>
    </div>

    <!-- Tabla de Ventas -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
        <div class="p-6 border-b border-gray-100 dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Lista de Ventas</h2>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Comprobante
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Cliente
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Vendedor
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Fecha
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Total
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Estado
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    <?php foreach ($ventas as $venta): ?>
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900 dark:text-gray-300">
                                <?= strtoupper($venta['tipo_comprobante']) ?> <?= $venta['numero_comprobante'] ?>
                            </div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                <?= $venta['cantidad_productos'] ?> productos
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900 dark:text-gray-300">
                                <?= $venta['cliente_nombre_completo'] ?>
                            </div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                DNI: <?= $venta['cliente_dni'] ?>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900 dark:text-gray-300">
                                <?= $venta['usuario_nombre_completo'] ?>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900 dark:text-gray-300">
                                <?= $venta['fecha_formateada'] ?>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900 dark:text-gray-300">
                                <?= $venta['total_formateado'] ?>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                       bg-<?= $venta['estado_badge']['color'] ?>-100 
                                       text-<?= $venta['estado_badge']['color'] ?>-800
                                       dark:bg-<?= $venta['estado_badge']['color'] ?>-900/50
                                       dark:text-<?= $venta['estado_badge']['color'] ?>-400">
                                <?= $venta['estado_badge']['text'] ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button onclick="verDetalleVenta(<?= $venta['id_venta'] ?>)" 
                                    class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">
                                Ver Detalle
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal para ver detalles -->
<div id="detalleVentaModal" class="hidden fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-4/5 lg:w-3/4 shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div id="detalleVentaContent">
            <!-- El contenido del detalle se cargará dinámicamente -->
        </div>
    </div>
</div>
<?= $this->endSection() ?>
<style>
    @keyframes modalShow {
        from {
            opacity: 0;
            transform: scale(0.95);
        }
        to {
            opacity: 1;
            transform: scale(1);
        }
    }

    .modal-animation {
        animation: modalShow 0.2s ease-out;
    }
</style>
<?= $this->section('scripts') ?>
<script>
    const BASE_URL = '<?= base_url() ?>';
    const id_usuario = '<?= session()->get('user')['id_usuario'] ?>';
</script>

<script src="<?= base_url("js/ventas.js") ?>"></script>
<?= $this->endSection() ?>