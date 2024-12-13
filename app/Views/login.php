<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer>
        // Función para alternar entre login y registro
        function toggleForms() {
            const loginForm = document.getElementById("login-form");
            const registerForm = document.getElementById("register-form");
            loginForm.classList.toggle("hidden");
            registerForm.classList.toggle("hidden");
        }
    </script>
    <link rel="stylesheet" href="<?= base_url('css/login.css') ?>">
</head>
<body class="min-h-screen animate-gradient">
    <!-- Círculos animados de fondo -->
    <div class="fixed inset-0 overflow-hidden -z-10">
        <div class="absolute -top-40 -left-40 w-80 h-80 bg-purple-300 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob"></div>
        <div class="absolute top-0 -right-40 w-80 h-80 bg-yellow-300 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-2000"></div>
        <div class="absolute -bottom-40 left-20 w-80 h-80 bg-pink-300 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-4000"></div>
    </div>

    <!-- Contenedor principal -->
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="w-full max-w-md p-8 rounded-2xl shadow-2xl bg-black/70 backdrop-blur-md animate__animated animate__fadeIn">
            <!-- Formulario de Login -->
            <div id="login-form">
                <h2 class="text-3xl font-bold text-white mb-2 text-center">Bienvenido</h2>
                <p class="text-gray-300 text-center mb-6">Por favor, inicia sesión para continuar</p>
            <!-- Mostrar mensaje de error -->
            <?php if (session()->getFlashdata('error')): ?>
                <div class="bg-red-500 text-white text-center py-2 rounded mb-4 error-message">
                    <?= session()->getFlashdata('error') ?>
                    <span class="close-btn">&times;</span>
                    <div class="progress-bar"></div>
                </div>
            <?php endif; ?>
                <form action="<?= base_url('admin/login/authenticate') ?>" method="post" class="space-y-6">
                    <div class="space-y-2">
                        <label for="username" class="block text-sm font-medium text-white">Usuario</label>
                        <input type="text" id="username" name="username" required class="w-full px-4 py-3 rounded-lg border border-gray-600 focus:ring-2 focus:ring-yellow-500 bg-gray-800 text-white placeholder-gray-400" placeholder="Ingresa tu usuario">
                    </div>
                    <div class="space-y-2">
                        <label for="password" class="block text-sm font-medium text-white">Contraseña</label>
                        <input type="password" id="password" name="password" required class="w-full px-4 py-3 rounded-lg border border-gray-600 focus:ring-2 focus:ring-yellow-500 bg-gray-800 text-white placeholder-gray-400" placeholder="Ingresa tu contraseña">
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input type="checkbox" id="remember" class="h-4 w-4 rounded border-gray-600 text-yellow-500 focus:ring-yellow-500">
                            <label for="remember" class="ml-2 block text-sm text-gray-300">Recordarme</label>
                        </div>
                        <a href="#" class="text-sm text-yellow-400 hover:text-yellow-300">¿Olvidaste tu contraseña?</a>
                    </div>
                    <button type="submit" class="w-full py-3 bg-yellow-400 text-black rounded-lg hover:bg-yellow-500 font-medium transition-colors">Iniciar Sesión</button>
                    <div class="text-center mt-4">
                        <span class="text-gray-300">¿No tienes cuenta? </span>
                        <button type="button" onclick="toggleForms()" class="text-yellow-400 hover:text-yellow-300 font-medium transition-colors">Regístrate</button>
                    </div>
                </form>
            </div>

            <!-- Formulario de Registro -->
            <div id="register-form" class="hidden">
                <h2 class="text-3xl font-bold text-white mb-2 text-center">Registro</h2>
                <p class="text-gray-300 text-center mb-6">Crea una cuenta para empezar</p>
                <form action="<?= base_url('admin/login/create') ?>" method="post" class="space-y-6">
                    <div class="space-y-2">
                        <label for="nombre" class="block text-sm font-medium text-white">Nombre</label>
                        <input type="text" id="nombre" name="nombre" required class="w-full px-4 py-3 rounded-lg border border-gray-600 focus:ring-2 focus:ring-yellow-500 bg-gray-800 text-white placeholder-gray-400">
                    </div>
                    <div class="space-y-2">
                        <label for="apellido" class="block text-sm font-medium text-white">Apellido</label>
                        <input type="text" id="apellido" name="apellido" required class="w-full px-4 py-3 rounded-lg border border-gray-600 focus:ring-2 focus:ring-yellow-500 bg-gray-800 text-white placeholder-gray-400">
                    </div>
                    <div class="space-y-2">
                        <label for="email" class="block text-sm font-medium text-white">Email</label>
                        <input type="email" id="email" name="email" required class="w-full px-4 py-3 rounded-lg border border-gray-600 focus:ring-2 focus:ring-yellow-500 bg-gray-800 text-white placeholder-gray-400">
                    </div>
                    <div class="space-y-2">
                        <label for="telefono" class="block text-sm font-medium text-white">Teléfono</label>
                        <input type="text" id="telefono" name="telefono" class="w-full px-4 py-3 rounded-lg border border-gray-600 focus:ring-2 focus:ring-yellow-500 bg-gray-800 text-white placeholder-gray-400">
                    </div>
                    <div class="space-y-2">
                        <label for="dni" class="block text-sm font-medium text-white">DNI</label>
                        <input type="text" id="dni" name="dni" required class="w-full px-4 py-3 rounded-lg border border-gray-600 focus:ring-2 focus:ring-yellow-500 bg-gray-800 text-white placeholder-gray-400">
                    </div>
                    <div class="space-y-2">
                        <label for="contraseña" class="block text-sm font-medium text-white">Contraseña</label>
                        <input type="password" id="contraseña" name="contraseña" required class="w-full px-4 py-3 rounded-lg border border-gray-600 focus:ring-2 focus:ring-yellow-500 bg-gray-800 text-white placeholder-gray-400">
                    </div>
                    <button type="submit" class="w-full py-3 bg-yellow-400 text-black rounded-lg hover:bg-yellow-500 font-medium transition-colors">Registrar</button>
                    <div class="text-center mt-4">
                        <span class="text-gray-300">¿Ya tienes cuenta? </span>
                        <button type="button" onclick="toggleForms()" class="text-yellow-400 hover:text-yellow-300 font-medium transition-colors">Inicia Sesión</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
<script src="<?=base_url('js/login.js') ?>"></script>