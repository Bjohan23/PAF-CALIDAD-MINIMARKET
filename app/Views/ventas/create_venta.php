<?= $this->extend('base') ?>

<?= $this->section('title') ?>
Lista de Productos
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<!-- Main container -->
<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <div class="flex flex-col lg:flex-row">
        <!-- Product content area -->
        <div class="flex-1 lg:pr-96">
            <div class="container mx-auto px-4 py-8">
                <!-- Header Section -->
                <div class="mb-8">
                    <h1 class="text-4xl font-bold text-gray-900 dark:text-white text-center mb-4">
                        Catálogo de Productos
                    </h1>
                    <div class="flex justify-center">
                        <button id="clienteSelect"
                            class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors shadow-lg">
                            <i class="fas fa-user-plus mr-2"></i>
                            Seleccionar Cliente
                        </button>
                    </div>
                </div>

                <!-- Filters Card -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 mb-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <!-- Search -->
                        <div class="relative">
                            <input type="text"
                                id="search-input"
                                placeholder="Buscar productos..."
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                            <i class="fas fa-search absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        </div>

                        <!-- Category Filter -->
                        <div class="relative">
                            <select id="category-filter"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent appearance-none transition-all">
                                <option value="">Todas las Categorías</option>
                                <?php foreach ($categorias as $categoria): ?>
                                    <option value="<?= $categoria['id_categoria'] ?>"><?= $categoria['nombre'] ?></option>
                                <?php endforeach; ?>
                            </select>
                            <i class="fas fa-chevron-down absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                        </div>

                        <!-- Price Range Inputs -->
                        <div class="relative">
                            <input type="number"
                                id="min-price"
                                placeholder="Precio Mínimo"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                        </div>

                        <div class="relative">
                            <input type="number"
                                id="max-price"
                                placeholder="Precio Máximo"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                        </div>
                    </div>

                    <!-- Reset Filters Button -->
                    <div class="mt-6 text-center">
                        <button id="reset-filters"
                            class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors inline-flex items-center">
                            <i class="fas fa-undo mr-2"></i>
                            Restablecer Filtros
                        </button>
                    </div>
                </div>

                <!-- No Products Message -->
                <div id="no-products-message"
                    class="hidden text-center text-gray-600 dark:text-gray-300 py-8 bg-white dark:bg-gray-800 rounded-lg shadow-md">
                    <i class="fas fa-box-open text-4xl mb-4"></i>
                    <p class="text-xl">No se encontraron productos que coincidan con los filtros seleccionados.</p>
                </div>

                <!-- Product Grid -->
                <div id="product-grid"
                    class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                    <?php foreach ($productos as $producto): ?>
                        <div class="product-card bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden transform transition-all hover:scale-102 hover:shadow-xl"
                            data-category="<?= $producto['id_categoria'] ?>"
                            data-price="<?= $producto['precio'] ?>">
                            <div class="p-6">
                                <h2 class="text-xl font-bold mb-3 text-gray-900 dark:text-white">
                                    <?= $producto['nombre'] ?>
                                </h2>
                                <p class="text-gray-600 dark:text-gray-300 mb-4 line-clamp-2">
                                    <?= $producto['descripcion'] ?>
                                </p>
                                <div class="flex items-center justify-between">
                                    <span class="text-2xl font-bold text-green-600 dark:text-green-400">
                                        S/ <?= number_format($producto['precio'], 2) ?>
                                    </span>
                                    <button data-product-id="<?= $producto['id_producto'] ?>"
                                        data-product-name="<?= $producto['nombre'] ?>"
                                        data-product-price="<?= $producto['precio'] ?>"
                                        class="add-to-cart-btn px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors inline-flex items-center">
                                        <i class="fas fa-shopping-cart mr-2"></i>
                                        Añadir
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <div id="pagination"
                    class="flex justify-center items-center space-x-4 bg-white dark:bg-gray-800 p-4 rounded-lg shadow-md">
                    <button id="prev-page"
                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                        <i class="fas fa-chevron-left mr-2"></i>
                        Anterior
                    </button>
                    <div id="page-numbers" class="flex space-x-2"></div>
                    <button id="next-page"
                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                        Siguiente
                        <i class="fas fa-chevron-right ml-2"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Shopping Cart -->
        <div class="fixed right-0 top-[70px] h-[calc(100vh-70px)] w-96 bg-white dark:bg-gray-800 shadow-xl border-l border-gray-200 dark:border-gray-700 z-30">
            <div class="h-full flex flex-col">
                <!-- Cart Header -->
                <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center">
                            <i class="fas fa-shopping-cart text-blue-600 dark:text-blue-400 mr-3"></i>
                            Carrito de Compras
                        </h2>
                        <span class="px-2 py-1 bg-blue-100 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400 rounded-full text-sm font-medium">
                            <span id="cart-count">0</span> items
                        </span>
                    </div>
                    <div id="selected-client" class="mt-3 px-4 py-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg text-sm border border-gray-200 dark:border-gray-600">
                        <div class="flex items-center text-gray-600 dark:text-gray-300">
                            <i class="fas fa-user mr-2"></i>
                            <span>No hay cliente seleccionado</span>
                        </div>
                    </div>
                </div>

                <!-- Cart Items -->
                <div id="cart-items" class="flex-1 overflow-y-auto p-4">
                    <!-- Los items se agregarán dinámicamente aquí -->
                    <div class="flex flex-col space-y-3">
                    </div>
                </div>

                <!-- Cart Footer -->
                <div class="border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
                    <!-- Comprobante Section -->
                    <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                        <div class="space-y-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Tipo de Comprobante
                                </label>
                                <select id="tipo-comprobante"
                                    class="w-full p-2.5 text-sm border border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="boleta">Boleta</option>
                                    <option value="factura">Factura</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Número de Comprobante
                                </label>
                                <input type="text"
                                    id="numero-comprobante"
                                    class="w-full p-2.5 text-sm border border-gray-300 rounded-lg bg-gray-50 dark:bg-gray-700 dark:border-gray-600 cursor-not-allowed"
                                    readonly>
                            </div>
                        </div>
                    </div>

                    <!-- Totals and Actions -->
                    <div class="p-4">
                        <div class="space-y-2 mb-4">
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-600 dark:text-gray-400">Subtotal:</span>
                                <span id="cart-subtotal" class="font-medium text-gray-900 dark:text-white">S/ 0.00</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-base font-bold text-gray-900 dark:text-white">Total:</span>
                                <span id="cart-total" class="text-xl font-bold text-blue-600 dark:text-blue-400">S/ 0.00</span>
                            </div>
                        </div>
                        <div class="flex space-x-2">
                            <button id="btn-cobrar"
                                class="flex-1 px-4 py-2.5 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 focus:ring-2 focus:ring-green-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-all flex items-center justify-center">
                                <i class="fas fa-cash-register mr-2"></i>
                                Cobrar
                            </button>
                            <button id="btn-imprimir"
                                class="px-4 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed hidden transition-all flex items-center">
                                <i class="fas fa-print mr-2"></i>
                                Imprimir
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Cliente Selection Modal -->
<div id="modalCliente" class="hidden fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm flex items-center justify-center z-50">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl max-w-lg w-full mx-4 transform transition-all">
        <div class="p-6">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 flex items-center">
                <i class="fas fa-user mr-3"></i>
                Seleccionar Cliente
            </h2>

            <!-- Search Input -->
            <div class="relative mb-6">
                <input id="searchInput"
                    type="text"
                    placeholder="Buscar cliente por DNI, nombre o apellido"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 focus:ring-2 focus:ring-blue-500">
                <i class="fas fa-search absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
            </div>

            <!-- Clients List -->
            <div class="max-h-96 overflow-y-auto mb-6">
                <ul id="clienteList" class="space-y-2">
                    <!-- Modifica la estructura de los clientes en el modal -->
                    <?php foreach ($clientes as $cliente): ?>
                        <li class="cliente-item cursor-pointer p-4 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                            data-cliente-id="<?= $cliente['id_cliente'] ?>"
                            data-cliente-nombre="<?= $cliente['nombre'] ?>"
                            data-cliente-apellido="<?= $cliente['apellido'] ?>"
                            data-cliente-dni="<?= $cliente['dni'] ?>">
                            <div class="flex items-center">
                                <i class="fas fa-user-circle text-gray-400 text-2xl mr-3"></i>
                                <div>
                                    <div class="font-medium text-gray-900 dark:text-white">
                                        <?= $cliente['nombre'] ?> <?= $cliente['apellido'] ?>
                                    </div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                        DNI: <?= $cliente['dni'] ?>
                                    </div>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- Show More Button -->
            <div id="showMoreBtnContainer" class="text-center mb-6 hidden">
                <button id="showMoreBtn"
                    class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                    Mostrar más
                </button>
            </div>

            <!-- Close Button -->
            <div class="flex justify-end">
                <button id="closeModalClienteBtn" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors inline-flex items-center">
                    <i class="fas fa-times mr-2"></i>
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url("js/clientes.js") ?>"></script>
<script>
    const BASE_URL = '<?= base_url() ?>';
    const id_usuario = '<?= session()->get('user')['id_usuario'] ?>';
</script>
<script src="<?= base_url("js/create_venta.js") ?>"></script>
<?= $this->endSection() ?>