<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->renderSection('title') ?></title>
    <!-- Fix: Corregido el orden de carga de CSS y la referencia incorrecta al CSS del sidebar -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <?= $this->renderSection('styles') ?>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    screens: {
                        'xs': '475px'  // Añadido breakpoint para móviles pequeños
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 dark:bg-gray-900 transition-colors duration-200">
    <!-- Header/Navbar mejorado para móviles -->
    <header class="w-full bg-white dark:bg-gray-800 shadow-md fixed top-0 z-50 transition-colors duration-200">
        <nav class="max-w-[1280px] h-[60px] md:h-[70px] mx-auto flex justify-between items-center px-3 md:px-8">
            <!-- Logo con mejor adaptación móvil -->
            <div class="flex items-center gap-2">
                <img src="<?= base_url('img/logo.jpeg') ?>" alt="Logo" class="w-[40px] h-[40px] md:w-[50px] md:h-[50px] rounded-full object-cover">
                <h1 class="text-sm md:text-xl font-bold text-gray-800 dark:text-white hidden xs:block">Minimarket Dashboard</h1>
            </div>

            <!-- Controles optimizados -->
            <div class="flex items-center space-x-2 md:space-x-4">
                <!-- Botón toggle sidebar para móvil -->
                <button id="toggleSidebar" class="p-2 md:hidden rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                    <svg class="w-6 h-6 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M4 6h16M4 12h16M4 18h16" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </button>

                <!-- Modo oscuro -->
                <button id="darkModeToggle" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                    <svg class="w-5 h-5 md:w-6 md:h-6 hidden dark:block text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                    </svg>
                    <svg class="w-5 h-5 md:w-6 md:h-6 block dark:hidden text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                    </svg>
                </button>

                <!-- Buscar - Oculto en móvil -->
                <div class="relative hidden lg:block">
                    <input type="search" 
                           placeholder="Buscar..." 
                           class="w-40 xl:w-64 pl-8 pr-4 py-2 text-sm rounded-lg border dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-orange-500">
                    <svg class="w-4 h-4 absolute left-2.5 top-3 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                    </svg>
                </div>

                <!-- Menú de usuario optimizado -->
                <div class="relative">
                    <button id="userMenuButton" class="flex items-center gap-1 md:gap-2 hover:bg-gray-100 dark:hover:bg-gray-700 px-2 md:px-3 py-2 rounded-lg">
                        <img src="<?= base_url('img/user.svg') ?>" alt="Usuario" class="w-7 h-7 md:w-8 md:h-8 rounded-full">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300 hidden xs:block">Admin</span>
                    </button>
                    <!-- Menú desplegable mejorado -->
                    <div id="userMenu" class="hidden absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden z-50">
                        <ul class="py-1">
                            <li>
                                <button id="viewProfile" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center">
                                    <i class="fas fa-user mr-2"></i>
                                    Ver Perfil
                                </button>
                            </li>
                            <li>
                                <button id="toggleFullscreen" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center">
                                    <i class="fas fa-expand mr-2"></i>
                                    Pantalla Completa
                                </button>
                            </li>
                            <li>
                                <button id="logout" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center">
                                    <i class="fas fa-sign-out-alt mr-2"></i>
                                    Cerrar Sesión
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <div class="flex">
        <!-- Sidebar mejorado -->
        <aside id="sidebar" class="fixed left-0 top-[60px] md:top-[70px] h-[calc(100vh-60px)] md:h-[calc(100vh-70px)] bg-white dark:bg-gray-800 shadow-lg transition-all duration-300 z-40 -translate-x-full md:translate-x-0 w-64">
            <nav class="h-full overflow-y-auto">
                <div class="p-4 space-y-2">
                    <a href="<?= base_url('admin/home') ?>" class="flex items-center gap-3 px-4 py-2 text-orange-600 bg-orange-50 dark:bg-orange-900/50 dark:text-orange-400 rounded-lg">
                        <i class="fas fa-home w-5 h-5"></i>
                        <span class="font-medium">Dashboard</span>
                    </a>
                    <!-- Realizar una venta -->
                    <a href="<?= base_url('admin/ventas/create') ?>" class="flex items-center gap-3 px-4 py-2 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg">
                        <i class="fas fa-box w-5 h-5"></i>
                        <span>Realizar una venta</span>
                    </a>
                    <!-- Resto de los enlaces del menú -->
                    <a href="<?= base_url('admin/productos') ?>" class="flex items-center gap-3 px-4 py-2 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg">
                        <i class="fas fa-box w-5 h-5"></i>
                        <span>Productos</span>
                    </a>
                    <a href="<?= base_url('admin/categorias') ?>" class="flex items-center gap-3 px-4 py-2 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                        </svg>
                        <span>Categorias</span>
                    </a>
                    <a href="<?= base_url('admin/clientes') ?>" class="flex items-center gap-3 px-4 py-2 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                        </svg>
                        <span>Clientes</span>
                    </a>
                    <a href="<?= base_url('admin/ventas') ?>" class="flex items-center gap-3 px-4 py-2 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                        </svg>
                        <span>Ventas</span>
                    </a>
                </div>
            </nav>
        </aside>
         <!-- Overlay para cerrar sidebar en móvil -->
         <div id="sidebarOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-30 hidden md:hidden"></div>
        <!-- Main Content con mejor adaptación -->
        <main class="flex-1 pt-[60px] md:pt-[70px] px-4 md:px-8 lg:px-12 pb-8 md:ml-64 transition-all duration-300">
            <?= $this->renderSection('content') ?> 
        </main>
    </div>
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script src="<?= base_url('js/index.js') ?>"></script>
    <?= $this->renderSection('scripts') ?>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const sidebar = document.getElementById('sidebar');
            const sidebarOverlay = document.getElementById('sidebarOverlay');
            const toggleSidebarBtn = document.getElementById('toggleSidebar');
            const userMenuButton = document.getElementById('userMenuButton');
            const userMenu = document.getElementById('userMenu');

            // Toggle sidebar en móvil
            function toggleSidebar() {
                sidebar.classList.toggle('-translate-x-full');
                sidebarOverlay.classList.toggle('hidden');
                document.body.classList.toggle('overflow-hidden');
            }

            toggleSidebarBtn?.addEventListener('click', toggleSidebar);
            sidebarOverlay?.addEventListener('click', toggleSidebar);

            // Cerrar sidebar al cambiar tamaño de ventana
            window.addEventListener('resize', () => {
                if (window.innerWidth >= 768) {
                    sidebar.classList.remove('-translate-x-full');
                    sidebarOverlay.classList.add('hidden');
                    document.body.classList.remove('overflow-hidden');
                }
            });

        // --------------------------------
        // Event Listener para cerrar sesión
        document.getElementById('logout').addEventListener('click', async () => {
            const result = await Swal.fire({
                title: '¿Cerrar sesión?',
                text: '¿Estás seguro que deseas cerrar la sesión?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, cerrar sesión',
                cancelButtonText: 'Cancelar'
            });

            if (result.isConfirmed) {
                window.location.href = '<?= base_url('admin/logout') ?>';
            }
        });

        // Event Listener para ver perfil
        document.getElementById('viewProfile').addEventListener('click', () => {
            window.location.href = '<?= base_url('admin/account') ?>';
        });

        // Event Listener para pantalla completa
        document.getElementById('toggleFullscreen').addEventListener('click', () => {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen().catch(err => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'No se pudo activar la pantalla completa'
                    });
                });
            } else {
                document.exitFullscreen().catch(err => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'No se pudo salir de pantalla completa'
                    });
                });
            }
        });

        // Mostrar mensajes flash con SweetAlert2
        <?php if (session()->getFlashdata('success')): ?>
            Swal.fire({
                icon: 'success',
                title: 'Éxito',
                text: '<?= session()->getFlashdata('success') ?>',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '<?= session()->getFlashdata('error') ?>',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });
        <?php endif; ?>

        <?php if (session()->getFlashdata('warning')): ?>
            Swal.fire({
                icon: 'warning',
                title: 'Advertencia',
                text: '<?= session()->getFlashdata('warning') ?>',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });
        <?php endif; ?>

        <?php if (session()->getFlashdata('info')): ?>
            Swal.fire({
                icon: 'info',
                title: 'Información',
                text: '<?= session()->getFlashdata('info') ?>',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });
        <?php endif; ?>
        });
    </script>
</body>
</html>