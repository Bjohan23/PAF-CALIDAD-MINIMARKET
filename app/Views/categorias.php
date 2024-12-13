<?= $this->extend('base') ?>

<?= $this->section('title') ?>Gestión de Categorías<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .animate-fadeIn {
        animation: fadeIn 0.3s ease-out;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<!-- Encabezado de la página -->
<div class="mb-6">
    <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white">
        Gestión de Categorías
    </h1>
    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
        Administra las categorías de productos de tu negocio
    </p>
</div>

<!-- Mensajes de alerta -->
<?php if (session()->getFlashdata('success')): ?>
    <div id="alertSuccess" class="mb-4 p-4 border-l-4 border-green-500 bg-green-100 dark:bg-green-800/30 text-green-700 dark:text-green-400 animate-fadeIn">
        <div class="flex items-center">
            <i class="fas fa-check-circle mr-2"></i>
            <?= session()->getFlashdata('success') ?>
        </div>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div id="alertError" class="mb-4 p-4 border-l-4 border-red-500 bg-red-100 dark:bg-red-800/30 text-red-700 dark:text-red-400 animate-fadeIn">
        <div class="flex items-center">
            <i class="fas fa-exclamation-circle mr-2"></i>
            <?= session()->getFlashdata('error') ?>
        </div>
    </div>
<?php endif; ?>

<!-- Botón para agregar categoría -->
<div class="mb-6">
    <button id="openModalBtn" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition-colors">
        <i class="fas fa-plus mr-2"></i>
        Nueva Categoría
    </button>
</div>

<!-- Tabla de categorías -->
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">ID</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nombre</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Descripción</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                <?php if (empty($categorias)): ?>
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                            <i class="fas fa-folder-open text-4xl mb-2"></i>
                            <p>No hay categorías registradas</p>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($categorias as $categoria): ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-300">
                                <?= $categoria['id_categoria'] ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-300">
                                <?= esc($categoria['nombre']) ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-300">
                                <?= esc($categoria['descripcion']) ?>
                            </td>
                            <td class="px-6 py-4 text-sm space-x-3 whitespace-nowrap">
                                <button 
                                    class="inline-flex items-center text-blue-600 hover:text-blue-700 transition-colors editBtn" 
                                    data-id="<?= $categoria['id_categoria'] ?>"
                                    data-nombre="<?= esc($categoria['nombre']) ?>"
                                    data-descripcion="<?= esc($categoria['descripcion']) ?>"
                                >
                                    <i class="fas fa-edit mr-1"></i>
                                    Editar
                                </button>
                                <button 
                                    class="inline-flex items-center text-red-600 hover:text-red-700 transition-colors deleteBtn"
                                    data-id="<?= $categoria['id_categoria'] ?>"
                                    data-nombre="<?= esc($categoria['nombre']) ?>"
                                >
                                    <i class="fas fa-trash-alt mr-1"></i>
                                    Eliminar
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal para agregar/editar categorías -->
<div id="categoryModal" class="hidden fixed inset-0 z-[60] overflow-y-auto" role="dialog" aria-modal="true">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black/70 backdrop-blur-sm transition-opacity"></div>
    
    <!-- Modal Container -->
    <div class="relative min-h-screen flex items-center justify-center p-4">
        <div class="relative w-full max-w-lg bg-white dark:bg-gray-800 rounded-xl shadow-2xl transform transition-all mx-auto">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex justify-between items-center">
                    <h3 id="modalTitle" class="text-xl font-semibold text-gray-900 dark:text-white">
                        Agregar Categoría
                    </h3>
                    <button type="button" class="closeModalBtn text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 rounded-lg">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>

            <!-- Formulario -->
            <form id="categoryForm" action="<?= base_url('categorias/store') ?>" method="post" class="p-6">
                <input type="hidden" id="categoryId" name="id_categoria">
                
                <div class="space-y-4">
                    <div>
                        <label for="nombre" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Nombre <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="nombre" 
                               name="nombre" 
                               required 
                               class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Ingrese el nombre de la categoría">
                        <div id="nombreError" class="mt-1 text-sm text-red-600 dark:text-red-400 hidden"></div>
                    </div>

                    <div>
                        <label for="descripcion" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Descripción
                        </label>
                        <textarea id="descripcion" 
                                  name="descripcion" 
                                  rows="3"
                                  class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Ingrese una descripción (opcional)"></textarea>
                    </div>
                </div>

                <!-- Botones de acción -->
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" 
                            class="closeModalBtn px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                        Cancelar
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition-colors">
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
document.addEventListener('DOMContentLoaded', function() {
    // Referencias a elementos del DOM
    const categoryModal = document.getElementById('categoryModal');
    const categoryForm = document.getElementById('categoryForm');
    const modalTitle = document.getElementById('modalTitle');
    const nombreInput = document.getElementById('nombre');
    const nombreError = document.getElementById('nombreError');

    // Función para mostrar el modal
    function showModal() {
        categoryModal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
        nombreInput.focus();
    }

    // Función para ocultar el modal
    function hideModal() {
        categoryModal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
        categoryForm.reset();
        nombreError.classList.add('hidden');
    }

    // Abrir modal para nueva categoría
    document.getElementById('openModalBtn').addEventListener('click', () => {
        modalTitle.textContent = 'Agregar Categoría';
        categoryForm.action = "<?= base_url('admin/categorias/store') ?>";
        showModal();
    });

    // Editar categoría
    document.querySelectorAll('.editBtn').forEach(button => {
        button.addEventListener('click', () => {
            modalTitle.textContent = 'Editar Categoría';
            const id = button.dataset.id;
            document.getElementById('categoryId').value = id;
            document.getElementById('nombre').value = button.dataset.nombre;
            document.getElementById('descripcion').value = button.dataset.descripcion;
            categoryForm.action = `<?= base_url('admin/categorias/update') ?>/${id}`;
            showModal();
        });
    });

    // Eliminar categoría
    document.querySelectorAll('.deleteBtn').forEach(button => {
        button.addEventListener('click', () => {
            const id = button.dataset.id;
            const nombre = button.dataset.nombre;
            
            Swal.fire({
                title: '¿Estás seguro?',
                html: `¿Deseas eliminar la categoría <strong>${nombre}</strong>?<br>Esta acción no se puede deshacer.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `<?= base_url('admin/categorias/delete') ?>/${id}`;
                }
            });
        });
    });

    // Cerrar modal
    document.querySelectorAll('.closeModalBtn').forEach(button => {
        button.addEventListener('click', hideModal);
    });

    // Cerrar modal con Escape
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !categoryModal.classList.contains('hidden')) {
            hideModal();
        }
    });

    // Cerrar modal al hacer clic fuera
    categoryModal.addEventListener('click', (e) => {
        if (e.target === categoryModal) {
            hideModal();
        }
    });

    // Validación del formulario
    categoryForm.addEventListener('submit', (e) => {
        let isValid = true;
        nombreError.classList.add('hidden');

        if (nombreInput.value.trim() === '') {
            isValid = false;
            nombreError.textContent = 'El nombre es obligatorio';
            nombreError.classList.remove('hidden');
        }

        if (!isValid) {
            e.preventDefault();
        }
    });

    // Auto-ocultar alertas después de 5 segundos
    const alerts = document.querySelectorAll('#alertSuccess, #alertError');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.classList.add('opacity-0', 'transition-opacity', 'duration-300');
            setTimeout(() => alert.remove(), 300);
        }, 5000);
    });
});
</script>
<?= $this->endSection() ?>