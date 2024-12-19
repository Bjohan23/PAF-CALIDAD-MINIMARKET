<?= $this->extend('base') ?>

<?= $this->section('title') ?>Productos<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container mx-auto px-4 py-8">
    <!-- Encabezado -->
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-900 dark:text-white">
            Gestión de Productos
        </h1>
        <p class="mt-2 text-gray-600 dark:text-gray-400">
            Administra tu catálogo de productos de manera eficiente
        </p>
    </div>

    <!-- Controles superiores -->
    <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
        <button id="openModalBtn"
            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all transform hover:scale-105 shadow-lg">
            <i class="fas fa-plus mr-2"></i>
            Agregar Producto
        </button>

        <div class="relative w-full sm:w-96">
            <input type="text"
                id="searchInput"
                placeholder="Buscar productos..."
                class="w-full px-4 py-2 pl-10 pr-4 border border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
        </div>
    </div>

    <!-- Tabla de productos -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="group px-6 py-4 text-left">
                            <div class="flex items-center gap-2 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                ID
                                <button class="opacity-0 group-hover:opacity-100 transition-opacity">
                                    <i class="fas fa-sort text-gray-400"></i>
                                </button>
                            </div>
                        </th>
                        <th class="group px-6 py-4 text-left">
                            <div class="flex items-center gap-2 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Imagen
                            </div>
                        </th>
                        <th class="group px-6 py-4 text-left">
                            <div class="flex items-center gap-2 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Nombre
                                <button class="opacity-0 group-hover:opacity-100 transition-opacity">
                                    <i class="fas fa-sort text-gray-400"></i>
                                </button>
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Descripción
                        </th>
                        <th class="group px-6 py-4 text-left">
                            <div class="flex items-center gap-2 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Precio
                                <button class="opacity-0 group-hover:opacity-100 transition-opacity">
                                    <i class="fas fa-sort text-gray-400"></i>
                                </button>
                            </div>
                        </th>
                        <th class="group px-6 py-4 text-left">
                            <div class="flex items-center gap-2 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Stock
                                <button class="opacity-0 group-hover:opacity-100 transition-opacity">
                                    <i class="fas fa-sort text-gray-400"></i>
                                </button>
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Categoría
                        </th>
                        <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <?php foreach ($productos as $producto): ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                <?= $producto['id_producto'] ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="h-16 w-16 rounded-lg overflow-hidden bg-gray-100 dark:bg-gray-700">
                                    <?php if ($producto['imagen_url']): ?>
                                        <img src="<?= $producto['imagen_url'] ?>"
                                            alt="<?= $producto['nombre'] ?>"
                                            class="h-full w-full object-cover">
                                    <?php else: ?>
                                        <div class="h-full w-full flex items-center justify-center">
                                            <i class="fas fa-image text-gray-400 text-2xl"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-medium text-gray-900 dark:text-white">
                                    <?= $producto['nombre'] ?>
                                </div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    <?= $producto['slug'] ?>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 dark:text-gray-300 max-w-xs truncate">
                                    <?= $producto['descripcion'] ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-green-600 dark:text-green-400">
                                    S/ <?= number_format($producto['precio'], 2) ?>
                                </div>
                                <?php if ($producto['precio_oferta']): ?>
                                    <div class="text-xs text-red-500 line-through">
                                        S/ <?= number_format($producto['precio_oferta'], 2) ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php
                                $stockClass = $producto['stock'] > 10
                                    ? 'text-green-800 bg-green-100 dark:text-green-400 dark:bg-green-900/30'
                                    : ($producto['stock'] > 0
                                        ? 'text-yellow-800 bg-yellow-100 dark:text-yellow-400 dark:bg-yellow-900/30'
                                        : 'text-red-800 bg-red-100 dark:text-red-400 dark:bg-red-900/30');
                                ?>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $stockClass ?>">
                                    <?= $producto['stock'] ?> unidades
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-sm text-blue-800 bg-blue-100 dark:text-blue-400 dark:bg-blue-900/30 rounded-lg">
                                    <?= $producto['categoria'] ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end gap-2">
                                    <button class="editBtn m-1 p-1 text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 transition-colors"
                                        data-id="<?= $producto['id_producto'] ?>">
                                        <i class="fas fa-edit text-lg"></i>
                                    </button>
                                    <button class="deleteBtn text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 transition-colors"
                                        data-id="<?= $producto['id_producto'] ?>">
                                        <i class="fas fa-trash text-lg"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/50 border-t dark:border-gray-700">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <div>
                    <p class="text-sm text-gray-700 dark:text-gray-300 pagination-info"></p>
                </div>
                <div>
                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px pagination-buttons" aria-label="Pagination"></nav>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para agregar/editar productos -->
<div id="productModal" class="hidden fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm flex items-center justify-center z-50">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl max-w-4xl w-full mx-4 transform transition-all">
        <div class="p-6">
            <h2 id="modalTitle" class="text-2xl font-bold text-gray-900 dark:text-white mb-6 flex items-center">
                <i class="fas fa-box mr-3"></i>
                <span>Agregar Producto</span>
            </h2>

            <form id="productForm" action="<?= base_url('admin/productos/store') ?>" method="post" enctype="multipart/form-data" class="space-y-6">
                <input type="hidden" id="productId" name="id_producto">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Columna izquierda -->
                    <div class="space-y-6">
                        <div class="space-y-2">
                            <label for="nombre" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Nombre del Producto *
                            </label>
                            <input type="text"
                                id="nombre"
                                name="nombre"
                                required
                                class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                        </div>

                        <div class="space-y-2">
                            <label for="slug" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Slug URL *
                            </label>
                            <input type="text"
                                id="slug"
                                name="slug"
                                required
                                class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                        </div>

                        <div class="space-y-2">
                            <label for="descripcion" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Descripción
                            </label>
                            <textarea id="descripcion"
                                name="descripcion"
                                rows="4"
                                class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"></textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label for="precio" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Precio *
                                </label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">S/</span>
                                    <input type="number"
                                        step="0.01"
                                        id="precio"
                                        name="precio"
                                        required
                                        class="w-full pl-8 pr-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label for="precio_oferta" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Precio Oferta
                                </label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">S/</span>
                                    <input type="number"
                                        step="0.01"
                                        id="precio_oferta"
                                        name="precio_oferta"
                                        class="w-full pl-8 pr-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Columna derecha -->
                    <div class="space-y-6">

                        <div class="space-y-2">
                            <label for="stock" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Stock *
                            </label>
                            <input type="number"
                                id="stock"
                                name="stock"
                                required
                                min="0"
                                class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                        </div>

                        <div class="space-y-2">
                            <label for="codigo_barras" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Código de Barras
                            </label>
                            <input type="text"
                                id="codigo_barras"
                                name="codigo_barras"
                                class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                        </div>

                        <div class="space-y-2">
                            <label for="id_categoria" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Categoría *
                            </label>
                            <select id="id_categoria"
                                name="id_categoria"
                                required
                                class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                                <option value="">Seleccione una categoría</option>
                                <?php foreach ($categorias as $categoria): ?>
                                    <option value="<?= $categoria['id_categoria'] ?>">
                                        <?= $categoria['nombre'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="flex items-center gap-3">
                            <div class="flex items-center">
                                <input type="checkbox"
                                    id="destacado"
                                    name="destacado"
                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <label for="destacado" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                                    Producto Destacado
                                </label>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox"
                                    id="is_active"
                                    name="is_active"
                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <label for="is_active" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                                    Activo
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 border-t dark:border-gray-700 pt-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">SEO</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label for="meta_descripcion" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Meta Descripción
                            </label>
                            <textarea id="meta_descripcion"
                                name="meta_descripcion"
                                rows="2"
                                maxlength="160"
                                class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"></textarea>
                        </div>
                        <div class="space-y-2">
                            <label for="meta_keywords" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Meta Keywords
                            </label>
                            <input type="text"
                                id="meta_keywords"
                                name="meta_keywords"
                                class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                                placeholder="Palabras clave separadas por comas">
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-4 mt-8">
                    <button type="button"
                        id="closeModalBtn"
                        class="px-6 py-3 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors inline-flex items-center">
                        <i class="fas fa-times mr-2"></i>
                        Cancelar
                    </button>
                    <button type="submit"
                        class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors inline-flex items-center">
                        <i class="fas fa-save mr-2"></i>
                        Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para agregar/editar productos -->
<div id="productModal"
    class="hidden fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm flex items-center justify-center z-50">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl max-w-lg w-full mx-4 transform transition-all">
        <div class="p-6">
            <h2 id="modalTitle" class="text-2xl font-bold text-gray-900 dark:text-white mb-6 flex items-center">
                <i class="fas fa-box mr-3"></i>
                <span>Agregar Producto</span>
            </h2>

            <form id="productForm" action="<?= base_url('admin/productos/store') ?>" method="post" class="space-y-6">
                <input type="hidden" id="productId" name="id_producto">

                <div class="space-y-2">
                    <label for="nombre" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Nombre del Producto
                    </label>
                    <input type="text"
                        id="nombre"
                        name="nombre"
                        required
                        class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                </div>

                <div class="space-y-2">
                    <label for="descripcion" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Descripción
                    </label>
                    <textarea id="descripcion"
                        name="descripcion"
                        rows="3"
                        class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"></textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label for="precio" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Precio
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">
                                S/
                            </span>
                            <input type="number"
                                step="0.01"
                                id="precio"
                                name="precio"
                                required
                                class="w-full pl-8 pr-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label for="stock" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Stock
                        </label>
                        <input type="number"
                            id="stock"
                            name="stock"
                            required
                            min="0"
                            class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                    </div>
                </div>

                <div class="space-y-2">
                    <label for="id_categoria" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Categoría
                    </label>
                    <select id="id_categoria"
                        name="id_categoria"
                        required
                        class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                        <option value="">Seleccione una categoría</option>
                        <?php foreach ($categorias as $categoria): ?>
                            <option value="<?= $categoria['id_categoria'] ?>">
                                <?= $categoria['nombre'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="flex justify-end gap-4 mt-8">
                    <button type="button"
                        id="closeModalBtn"
                        class="px-6 py-3 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors inline-flex items-center">
                        <i class="fas fa-times mr-2"></i>
                        Cancelar
                    </button>
                    <button type="submit"
                        class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors inline-flex items-center">
                        <i class="fas fa-save mr-2"></i>
                        Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // Variables y funciones globales de paginación
    window.currentPage = 1;
    window.filteredRows = [];
    const itemsPerPage = 10;

    window.changePage = function(newPage) {
        const totalPages = Math.ceil(window.filteredRows.length / itemsPerPage);
        if (newPage >= 1 && newPage <= totalPages) {
            window.currentPage = newPage;
            updatePagination();
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Referencias DOM
        const openModalBtn = document.getElementById('openModalBtn');
        const closeModalBtn = document.getElementById('closeModalBtn');
        const productModal = document.getElementById('productModal');
        const productForm = document.getElementById('productForm');
        const modalTitle = document.getElementById('modalTitle');
        const searchInput = document.getElementById('searchInput');

        // Estado de la aplicación
        let sortColumn = null;
        let sortDirection = 'asc';

        // Funciones de paginación
        function updatePagination() {
            const rows = Array.from(document.querySelectorAll('tbody tr'));
            window.filteredRows = rows.filter(row => !row.classList.contains('hidden'));
            const totalPages = Math.ceil(window.filteredRows.length / itemsPerPage);

            // Actualizar la información de paginación
            const paginationInfo = document.querySelector('.pagination-info');
            const start = (window.currentPage - 1) * itemsPerPage + 1;
            const end = Math.min(start + itemsPerPage - 1, window.filteredRows.length);

            if (paginationInfo) {
                paginationInfo.innerHTML = `
                Mostrando
                <span class="font-medium">${start}</span>
                a
                <span class="font-medium">${end}</span>
                de
                <span class="font-medium">${window.filteredRows.length}</span>
                resultados
            `;
            }

            // Mostrar/ocultar filas según la página actual
            window.filteredRows.forEach((row, index) => {
                if (index >= (window.currentPage - 1) * itemsPerPage && index < window.currentPage * itemsPerPage) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });

            updatePaginationButtons(totalPages);
        }

        function updatePaginationButtons(totalPages) {
            const paginationContainer = document.querySelector('.pagination-buttons');
            if (!paginationContainer) return;

            let buttons = '';

            // Botón Anterior
            buttons += `
            <button class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm font-medium text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-700 ${window.currentPage === 1 ? 'opacity-50 cursor-not-allowed' : ''}"
                    onclick="changePage(${window.currentPage - 1})" 
                    ${window.currentPage === 1 ? 'disabled' : ''}>
                <span class="sr-only">Anterior</span>
                <i class="fas fa-chevron-left"></i>
            </button>
        `;

            // Botones de página
            let startPage = Math.max(1, window.currentPage - 2);
            let endPage = Math.min(totalPages, startPage + 4);

            if (startPage > 1) {
                buttons += `
                <button onclick="changePage(1)" class="relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">1</button>
            `;
                if (startPage > 2) {
                    buttons += `<span class="relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm font-medium text-gray-700 dark:text-gray-300">...</span>`;
                }
            }

            for (let i = startPage; i <= endPage; i++) {
                buttons += `
                <button onclick="changePage(${i})" 
                        class="relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 ${window.currentPage === i ? 'bg-blue-600 text-white' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300'} text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-700">
                    ${i}
                </button>
            `;
            }

            if (endPage < totalPages) {
                if (endPage < totalPages - 1) {
                    buttons += `<span class="relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm font-medium text-gray-700 dark:text-gray-300">...</span>`;
                }
                buttons += `
                <button onclick="changePage(${totalPages})" class="relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">${totalPages}</button>
            `;
            }

            // Botón Siguiente
            buttons += `
            <button class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm font-medium text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-700 ${window.currentPage === totalPages ? 'opacity-50 cursor-not-allowed' : ''}"
                    onclick="changePage(${window.currentPage + 1})"
                    ${window.currentPage === totalPages ? 'disabled' : ''}>
                <span class="sr-only">Siguiente</span>
                <i class="fas fa-chevron-right"></i>
            </button>
        `;

            paginationContainer.innerHTML = buttons;
        }

        // Función para mostrar el modal
        const showModal = (title = 'Agregar Producto') => {
            modalTitle.innerHTML = `<i class="fas fa-box mr-3"></i><span>${title}</span>`;
            productModal.classList.remove('hidden');
            // Animar entrada
            const modalContent = productModal.querySelector('.transform');
            modalContent.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                modalContent.classList.remove('scale-95', 'opacity-0');
                modalContent.classList.add('scale-100', 'opacity-100');
            }, 10);
        };

        // Función para ocultar el modal
        const hideModal = () => {
            const modalContent = productModal.querySelector('.transform');
            modalContent.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                productModal.classList.add('hidden');
                modalContent.classList.remove('scale-95', 'opacity-0');
            }, 200);
        };

        // Event Listeners
        openModalBtn.addEventListener('click', () => {
            productForm.reset();
            productForm.action = "<?= base_url('admin/productos/store') ?>";
            showModal('Agregar Producto');
        });

        closeModalBtn.addEventListener('click', hideModal);

        // Cerrar modal al hacer clic fuera
        productModal.addEventListener('click', (e) => {
            if (e.target === productModal) {
                hideModal();
            }
        });

        // Event listeners para editar producto
        document.querySelectorAll('.editBtn').forEach(button => {
            button.addEventListener('click', async (e) => {
                e.preventDefault();
                const id = button.getAttribute('data-id');

                try {
                    const response = await fetch(`<?= base_url('admin/productos/edit') ?>/${id}`);
                    if (!response.ok) throw new Error('Error al obtener datos del producto');

                    const data = await response.json();

                    document.getElementById('productId').value = data.id_producto;
                    document.getElementById('nombre').value = data.nombre;
                    document.getElementById('descripcion').value = data.descripcion;
                    document.getElementById('precio').value = data.precio;
                    document.getElementById('stock').value = data.stock;
                    document.getElementById('id_categoria').value = data.id_categoria;

                    productForm.action = `<?= base_url('admin/productos/update') ?>/${id}`;
                    showModal('Editar Producto');
                } catch (error) {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'No se pudo cargar la información del producto'
                    });
                }
            });
        });

        // Event listeners para eliminar producto
        document.querySelectorAll('.deleteBtn').forEach(button => {
            button.addEventListener('click', async (e) => {
                e.preventDefault();
                const id = button.getAttribute('data-id');
                // console.log("ID DEL PRODUCTO A ELIMINAR ",id);

                const result = await Swal.fire({
                    title: '¿Estás seguro?',
                    text: "Esta acción no se puede deshacer",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                });

                if (result.isConfirmed) {
                    try {
                        const response = await fetch(`<?= base_url('admin/productos/delete') ?>/${id}`, {
                            method: 'DELETE'
                        });

                        if (!response.ok) throw new Error('Error al eliminar el producto');

                        Swal.fire(
                            '¡Eliminado!',
                            'El producto ha sido eliminado.',
                            'success'
                        ).then(() => {
                            window.location.reload();
                        });
                    } catch (error) {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'No se pudo eliminar el producto'
                        });
                    }
                }
            });
        });

        // Búsqueda en tiempo real
        let searchTimeout;
        searchInput.addEventListener('input', (e) => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                const searchTerm = e.target.value.toLowerCase();
                const rows = document.querySelectorAll('tbody tr');

                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    row.classList.toggle('hidden', !text.includes(searchTerm));
                });

                window.currentPage = 1; // Resetear a la primera página
                updatePagination();
            }, 300);
        });

        // Validación del formulario
        productForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const formData = new FormData(productForm);
            try {
                const response = await fetch(productForm.action, {
                    method: 'POST',
                    body: formData
                });

                if (!response.ok) throw new Error('Error al guardar el producto');

                Swal.fire({
                    icon: 'success',
                    title: 'Éxito',
                    text: 'Producto guardado correctamente'
                }).then(() => {
                    window.location.reload();
                });
            } catch (error) {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudo guardar el producto'
                });
            }
        });

        // Inicializar paginación
        updatePagination();
    });
</script>
<?= $this->endSection() ?>