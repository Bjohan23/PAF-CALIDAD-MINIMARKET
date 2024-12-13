<?= $this->extend('base') ?>

<?= $this->section('title') ?>
Clientes
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<!-- Título de la página -->
<h1 class="text-3xl font-extrabold text-gray-900 dark:text-white mb-6">Gestión de Clientes</h1>

<!-- Botón para abrir el modal de agregar/editar cliente -->
<div class="mb-4">
    <button id="openModalBtn" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition-colors">Agregar Cliente</button>
</div>

<!-- Tabla de clientes -->
<div class="overflow-x-auto">
    <table class="w-full bg-white dark:bg-gray-800 rounded-xl shadow-lg transition-colors duration-200">
        <thead class="bg-gray-50 dark:bg-gray-700">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">DNI</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Nombre</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Apellido</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Teléfono</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Email</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($clientes as $cliente): ?>
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-300"><?= $cliente['dni'] ?></td>
                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-300"><?= $cliente['nombre'] ?></td>
                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-300"><?= $cliente['apellido'] ?></td>
                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-300"><?= $cliente['telefono'] ?></td>
                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-300"><?= $cliente['email'] ?></td>
                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-300">
                        <button
                            class="text-blue-500 hover:text-blue-700 transition-colors editBtn"
                            data-id="<?= $cliente['id_cliente'] ?>"
                            data-dni="<?= $cliente['dni'] ?>"
                            data-nombre="<?= $cliente['nombre'] ?>"
                            data-apellido="<?= $cliente['apellido'] ?>"
                            data-telefono="<?= $cliente['telefono'] ?>"
                            data-email="<?= $cliente['email'] ?>">Editar</button>
                        <a href="<?= base_url('admin/clientes/delete/' . $cliente['id_cliente']) ?>"
                            class="text-red-500 hover:text-red-700 transition-colors ml-4">Eliminar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Modal para agregar/editar clientes -->
<div id="clientModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
    <div class="bg-white dark:bg-gray-800 p-8 rounded-xl shadow-lg max-w-lg w-full">
        <h2 id="modalTitle" class="text-xl font-bold text-gray-900 dark:text-white mb-4">Agregar Cliente</h2>
        <form id="clientForm" action="<?= base_url('admin/clientes/store') ?>" method="post" class="space-y-4">
            <input type="hidden" id="clientId" name="id_cliente">

            <div class="space-y-2">
                <label for="dni" class="block text-sm font-medium text-gray-700 dark:text-gray-300">DNI</label>
                <input type="text" id="dni" name="dni" required class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white">
            </div>
            <div class="space-y-2">
                <label for="nombre" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nombre</label>
                <input type="text" id="nombre" name="nombre" required class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white">
            </div>
            <div class="space-y-2">
                <label for="apellido" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Apellido</label>
                <input type="text" id="apellido" name="apellido" required class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white">
            </div>
            <div class="space-y-2">
                <label for="telefono" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Teléfono</label>
                <input type="text" id="telefono" name="telefono" class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white">
            </div>
            <div class="space-y-2">
                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                <input type="email" id="email" name="email" class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white">
            </div>
            <div class="flex justify-end gap-4">
                <button type="button" id="closeModalBtn" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">Cancelar</button>
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">Guardar</button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    const openModalBtn = document.getElementById('openModalBtn');
    const closeModalBtn = document.getElementById('closeModalBtn');
    const clientModal = document.getElementById('clientModal');
    const clientForm = document.getElementById('clientForm');
    const modalTitle = document.getElementById('modalTitle');

    // Abrir modal para agregar cliente
    openModalBtn.addEventListener('click', () => {
        modalTitle.textContent = 'Agregar Cliente';
        clientForm.action = "<?= base_url('admin/clientes/store') ?>";
        clientForm.reset();
        clientModal.classList.remove('hidden');
    });
    // Abrir modal para editar cliente
    document.querySelectorAll('.editBtn').forEach(button => {
        button.addEventListener('click', () => {
            modalTitle.textContent = 'Editar Cliente';
            const id = button.getAttribute('data-id');
            fetch(`<?= base_url('admin/clientes/edit') ?>/${id}`)
                .then(response => response.json())
                .then(response => {
                    // Accedemos a los datos a través de response.data
                    const data = response.data;
                    document.getElementById('clientId').value = data.id_cliente;
                    document.getElementById('dni').value = data.dni;
                    document.getElementById('nombre').value = data.nombre;
                    document.getElementById('apellido').value = data.apellido;
                    document.getElementById('telefono').value = data.telefono;
                    document.getElementById('email').value = data.email;
                    clientForm.action = `<?= base_url('admin/clientes/update') ?>/${id}`;
                    clientModal.classList.remove('hidden');
                })
                .catch(error => {
                    // Agregamos manejo de errores
                    console.error('Error al cargar los datos del cliente:', error);
                    // Opcional: Mostrar un mensaje al usuario
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'No se pudieron cargar los datos del cliente'
                    });
                });
        });
    });
    // Cerrar modal
    closeModalBtn.addEventListener('click', () => {
        clientModal.classList.add('hidden');
    });
</script>
<?= $this->endSection() ?>