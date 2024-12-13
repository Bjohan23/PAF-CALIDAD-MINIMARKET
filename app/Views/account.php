<?= $this->extend('base') ?>

<?= $this->section('title') ?>Mi Cuenta<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container mx-auto px-4 py-8">
    <!-- Header con breadcrumb -->
    <div class="mb-8">
        <nav class="flex mb-4" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 text-sm font-medium">
                <li>
                    <a href="<?= base_url('admin/home') ?>" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">
                        <i class="fas fa-home"></i>
                        <span class="ml-1">Inicio</span>
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400 mx-2 text-xs"></i>
                        <span class="text-gray-900 dark:text-white">Mi Cuenta</span>
                    </div>
                </li>
            </ol>
        </nav>
        <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white">
            Mi Cuenta
        </h1>
    </div>

    <!-- Grid de contenido -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Perfil Card -->
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Información Personal</h2>
                        <button onclick="editProfile()" 
                                class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-500 focus:ring-opacity-50 transition-colors">
                            <i class="fas fa-edit mr-2"></i>
                            Editar Perfil
                        </button>
                    </div>
                </div>

                <div class="p-6">
                    <!-- Avatar y nombre -->
                    <div class="flex items-center mb-8">
                        <div class="relative">
                            <div class="w-20 h-20 rounded-full bg-gradient-to-r from-blue-500 to-blue-600 flex items-center justify-center text-white text-2xl font-bold">
                                <?= strtoupper(substr(session()->get('user')['nombre'], 0, 1)) ?>
                            </div>
                            <div class="absolute bottom-0 right-0 w-6 h-6 bg-green-500 rounded-full border-2 border-white dark:border-gray-800"></div>
                        </div>
                        <div class="ml-6">
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white">
                                <?= session()->get('user')['nombre'] ?> <?= session()->get('user')['apellido'] ?>
                            </h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                <?= ucfirst(session()->get('user')['tipo_usuario']) ?>
                            </p>
                        </div>
                    </div>

                    <!-- Información detallada -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <div class="flex items-center p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                <div class="flex-shrink-0 w-10 h-10 flex items-center justify-center rounded-lg bg-blue-500/10 text-blue-600 dark:text-blue-400">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Email</p>
                                    <p class="text-gray-900 dark:text-white"><?= session()->get('user')['email'] ?></p>
                                </div>
                            </div>

                            <div class="flex items-center p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                <div class="flex-shrink-0 w-10 h-10 flex items-center justify-center rounded-lg bg-green-500/10 text-green-600 dark:text-green-400">
                                    <i class="fas fa-id-card"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">DNI</p>
                                    <p class="text-gray-900 dark:text-white"><?= session()->get('user')['dni'] ?></p>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div class="flex items-center p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                <div class="flex-shrink-0 w-10 h-10 flex items-center justify-center rounded-lg bg-purple-500/10 text-purple-600 dark:text-purple-400">
                                    <i class="fas fa-phone"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Teléfono</p>
                                    <p class="text-gray-900 dark:text-white"><?= session()->get('user')['telefono'] ?? 'No registrado' ?></p>
                                </div>
                            </div>

                            <div class="flex items-center p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                <div class="flex-shrink-0 w-10 h-10 flex items-center justify-center rounded-lg bg-orange-500/10 text-orange-600 dark:text-orange-400">
                                    <i class="fas fa-calendar"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Miembro desde</p>
                                    <p class="text-gray-900 dark:text-white"><?= date('d M, Y', strtotime(session()->get('user')['created_at'])) ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Cards -->
        <div class="space-y-6">
            <!-- Cambiar Contraseña Card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Seguridad</h3>
                        <span class="w-10 h-10 flex items-center justify-center rounded-lg bg-blue-500/10 text-blue-600 dark:text-blue-400">
                            <i class="fas fa-shield-alt"></i>
                        </span>
                    </div>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                        Mantén tu cuenta segura actualizando tu contraseña regularmente
                    </p>
                    <button onclick="changePassword()" 
                            class="w-full px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-500 focus:ring-opacity-50 transition-colors">
                        Cambiar Contraseña
                    </button>
                </div>
            </div>

            <!-- Actividad Reciente Card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Actividad Reciente</h3>
                        <span class="w-10 h-10 flex items-center justify-center rounded-lg bg-green-500/10 text-green-600 dark:text-green-400">
                            <i class="fas fa-clock"></i>
                        </span>
                    </div>
                    <div class="space-y-4">
                        <?php for ($i = 0; $i < 3; $i++): ?>
                        <div class="flex items-center text-sm">
                            <div class="w-2 h-2 rounded-full bg-blue-500 mr-3"></div>
                            <span class="text-gray-500 dark:text-gray-400">Inicio de sesión exitoso</span>
                            <span class="ml-auto text-xs text-gray-400">Hace <?= $i + 1 ?> días</span>
                        </div>
                        <?php endfor; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function editProfile() {
    // Implementar lógica de edición
    Swal.fire({
        title: 'Próximamente',
        text: 'La función de editar perfil estará disponible pronto',
        icon: 'info'
    });
}

function changePassword() {
    // Implementar cambio de contraseña
    Swal.fire({
        title: 'Cambiar Contraseña',
        html: `
            <input type="password" id="currentPassword" class="swal2-input" placeholder="Contraseña actual">
            <input type="password" id="newPassword" class="swal2-input" placeholder="Nueva contraseña">
            <input type="password" id="confirmPassword" class="swal2-input" placeholder="Confirmar contraseña">
        `,
        showCancelButton: true,
        confirmButtonText: 'Cambiar',
        cancelButtonText: 'Cancelar',
        showLoaderOnConfirm: true,
        preConfirm: () => {
            // Implementar la lógica de cambio de contraseña
        }
    });
}
</script>
<?= $this->endSection() ?>