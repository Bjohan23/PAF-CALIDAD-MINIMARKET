<?php
// Debug de sesión al inicio del archivo
$sessionDebug = [
    'session_id' => session_id(),
    'cliente_id' => session()->get('cliente_id'),
    'isClienteLoggedIn' => session()->get('isClienteLoggedIn')
];
log_message('debug', 'Layout - Estado de sesión: ' . json_encode($sessionDebug));
?>
<!DOCTYPE html>
<html lang="es" class="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'MiniMarket Online' ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            200: '#bae6fd',
                            300: '#7dd3fc',
                            400: '#38bdf8',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                            800: '#075985',
                            900: '#0c4a6e',
                        }
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.3s ease-in-out',
                        'slide-down': 'slideDown 0.3s ease-in-out',
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': {
                                opacity: '0'
                            },
                            '100%': {
                                opacity: '1'
                            },
                        },
                        slideDown: {
                            '0%': {
                                transform: 'translateY(-10px)',
                                opacity: '0'
                            },
                            '100%': {
                                transform: 'translateY(0)',
                                opacity: '1'
                            },
                        }
                    }
                }
            }
        }
    </script>
    <style>
        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }
        }

        .cart-pulse {
            animation: pulse 1s ease-in-out;
        }
    </style>
</head>

<body class="bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800 min-h-screen transition-colors duration-300">
    <!-- Navbar -->
    <nav class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-lg fixed w-full z-50 shadow-lg transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex items-center space-x-4">
                    <a href="<?= base_url('/') ?>" class="flex items-center space-x-2">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center">
                            <i class="fas fa-store text-white text-xl"></i>
                        </div>
                        <span class="text-xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 dark:from-blue-400 dark:to-indigo-400 bg-clip-text text-transparent">
                            MiniMarket
                        </span>
                    </a>
                </div>

                <!-- Navigation Links - Desktop -->
                <div class="hidden md:flex items-center space-x-1">
                    <a href="<?= base_url('/') ?>"
                        class="px-4 py-2 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200">
                        <i class="fas fa-home mr-2"></i>Inicio
                    </a>
                    <a href="<?= base_url('tienda/productos') ?>"
                        class="px-4 py-2 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200">
                        <i class="fas fa-tags mr-2"></i>Productos
                    </a>
                    <?php if (session()->get('isLoggedIn')): ?>
                        <a href="<?= base_url('tienda/mis-pedidos') ?>"
                            class="px-4 py-2 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200">
                            <i class="fas fa-box mr-2"></i>Mis Pedidos
                        </a>
                        <a href="<?= base_url('tienda/perfil') ?>"
                            class="px-4 py-2 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200">
                            <i class="fas fa-user mr-2"></i>Mi Perfil
                        </a>
                    <?php endif; ?>
                </div>

                <!-- Right Side Actions -->
                <div class="flex items-center space-x-4">
                    <!-- Theme Toggle -->
                    <button id="theme-toggle"
                        class="w-10 h-10 rounded-lg border border-gray-200 dark:border-gray-700 flex items-center justify-center hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-moon text-gray-600 dark:hidden"></i>
                        <i class="fas fa-sun hidden dark:block text-yellow-400"></i>
                    </button>

                    <!-- Cart -->
                    <a href="<?= base_url('tienda/carrito') ?>"
                        class="w-10 h-10 rounded-lg border border-gray-200 dark:border-gray-700 flex items-center justify-center hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200 relative">
                        <i class="fas fa-shopping-cart text-gray-600 dark:text-gray-300"></i>
                        <?php if (isset($cart_count) && $cart_count > 0): ?>
                            <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center animate-fade-in">
                                <?= $cart_count ?>
                            </span>
                        <?php endif; ?>
                    </a>

                    <!-- User Menu -->
                    <?php
                    // Obtener el estado de la sesión y los datos del usuario
                    $isLoggedIn = session()->get('isClienteLoggedIn');
                    $clienteNombre = session()->get('cliente_nombre');
                    $clienteEmail = session()->get('cliente_email');
                    ?>
                    <?php if ($isLoggedIn): ?>
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open"
                                class="w-10 h-10 rounded-lg border border-gray-200 dark:border-gray-700 flex items-center justify-center hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200">
                                <div class="flex items-center">
                                    <div class="flex flex-col items-start mr-3">
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                            <?= esc($clienteNombre) ?>
                                        </span>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">
                                            <?= esc($clienteEmail) ?>
                                        </span>
                                    </div>
                                    <i class="fas fa-user-circle text-gray-600 dark:text-gray-300 text-xl"></i>
                                </div>
                            </button>
                            <div x-show="open"
                                @click.away="open = false"
                                class="absolute right-0 mt-2 w-48 rounded-lg shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5 animate-slide-down">
                                <div class="py-1">
                                    <a href="<?= base_url('tienda/perfil') ?>"
                                        class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                        <i class="fas fa-user mr-2"></i>
                                        Mi Perfil
                                    </a>
                                    <a href="<?= base_url('tienda/mis-pedidos') ?>"
                                        class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                        <i class="fas fa-shopping-bag mr-2"></i>
                                        Mis Pedidos
                                    </a>
                                    <hr class="my-1 border-gray-200 dark:border-gray-700">
                                    <a href="<?= base_url('logout') ?>"
                                        class="block px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-700">
                                        <i class="fas fa-sign-out-alt mr-2"></i>
                                        Cerrar Sesión
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="<?= base_url('login') ?>"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 transition-colors duration-200">
                            <i class="fas fa-sign-in-alt mr-2"></i>
                            Iniciar Sesión
                        </a>
                    <?php endif; ?>

                    <!-- Mobile Menu Button -->
                    <button type="button"
                        class="md:hidden inline-flex items-center justify-center p-2 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200"
                        @click="mobileMenuOpen = !mobileMenuOpen">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div class="md:hidden" x-show="mobileMenuOpen" @click.away="mobileMenuOpen = false">
            <div class="px-2 pt-2 pb-3 space-y-1 bg-white dark:bg-gray-800 shadow-lg">
                <a href="<?= base_url('/') ?>"
                    class="block px-3 py-2 rounded-lg text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                    Inicio
                </a>
                <a href="<?= base_url('tienda/productos') ?>"
                    class="block px-3 py-2 rounded-lg text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                    Productos
                </a>
                <?php if (session()->get('isLoggedIn')): ?>
                    <a href="<?= base_url('tienda/mis-pedidos') ?>"
                        class="block px-3 py-2 rounded-lg text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                        Mis Pedidos
                    </a>
                    <a href="<?= base_url('tienda/perfil') ?>"
                        class="block px-3 py-2 rounded-lg text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                        Mi Perfil
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="pt-20 pb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <?= $this->renderSection('content') ?>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white dark:bg-gray-800 shadow-lg transition-colors duration-300">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- About Section -->
                <div class="space-y-4">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Sobre Nosotros</h3>
                    <p class="text-gray-600 dark:text-gray-300 text-sm">
                        Tu minimarket de confianza, ofreciendo productos de calidad a precios justos.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">
                            <i class="fab fa-facebook text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">
                            <i class="fab fa-instagram text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">
                            <i class="fab fa-twitter text-xl"></i>
                        </a>
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="space-y-4">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Enlaces Rápidos</h3>
                    <ul class="space-y-2">
                        <li>
                            <a href="#" class="text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white text-sm">
                                Productos
                            </a>
                        </li>
                        <li>
                            <a href="#" class="text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white text-sm">
                                Ofertas
                            </a>
                        </li>
                        <li>
                            <a href="#" class="text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white text-sm">
                                Categorías
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Contact Info -->
                <div class="space-y-4">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Contáctanos</h3>
                    <ul class="space-y-2">
                        <li class="flex items-start space-x-3 text-gray-600 dark:text-gray-300 text-sm">
                            <i class="fas fa-map-marker-alt mt-1"></i>
                            <span>Av. Principal 123, Lima, Perú</span>
                        </li>
                        <li class="flex items-center space-x-3 text-gray-600 dark:text-gray-300 text-sm">
                            <i class="fas fa-phone"></i>
                            <span>+51 923 456 789</span>
                        </li>
                        <li class="flex items-center space-x-3 text-gray-600 dark:text-gray-300 text-sm">
                            <i class="fas fa-envelope"></i>
                            <span>contacto@minimarket.com</span>
                        </li>
                    </ul>
                </div>

                <!-- Newsletter -->
                <div class="space-y-4">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Suscríbete</h3>
                    <p class="text-gray-600 dark:text-gray-300 text-sm">
                        Recibe nuestras últimas ofertas y novedades
                    </p>
                    <form class="flex flex-col space-y-2">
                        <input type="email"
                            placeholder="Tu correo electrónico"
                            class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        <button type="submit"
                            class="px-4 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-lg transition-colors duration-200">
                            Suscribirse
                        </button>
                    </form>
                </div>
            </div>

            <!-- Bottom Footer -->
            <div class="mt-8 pt-8 border-t border-gray-200 dark:border-gray-700">
                <div class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
                    <p class="text-gray-600 dark:text-gray-400 text-sm">
                        © <?= date('Y') ?> MiniMarket. Todos los derechos reservados.
                    </p>
                    <div class="flex space-x-6">
                        <a href="#" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white text-sm">
                            Términos y Condiciones
                        </a>
                        <a href="#" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white text-sm">
                            Política de Privacidad
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Alpine.js -->
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        // Estado inicial para el menú móvil
        document.addEventListener('alpine:init', () => {
            Alpine.data('layout', () => ({
                mobileMenuOpen: false
            }))
        })

        // Sistema de tema oscuro/claro
        const themeToggle = document.getElementById('theme-toggle');

        // Verificar preferencia guardada o configuración del sistema
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }

        // Manejar cambio de tema
        themeToggle.addEventListener('click', () => {
            document.documentElement.classList.toggle('dark');
            localStorage.theme = document.documentElement.classList.contains('dark') ? 'dark' : 'light';
        });

        // Animación del carrito
        function animateCart() {
            const cart = document.querySelector('.cart-count');
            if (cart) {
                cart.classList.add('cart-pulse');
                setTimeout(() => cart.classList.remove('cart-pulse'), 1000);
            }
        }
    </script>
</body>

</html>