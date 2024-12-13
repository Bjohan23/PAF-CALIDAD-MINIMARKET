<?= $this->extend('clientes/layout') ?>

<?= $this->section('content') ?>
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-gray-900 dark:to-gray-800 flex flex-col justify-center py-12 sm:px-6 lg:px-8 transition-colors duration-300">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <!-- Logo o Icono -->
        <div class="mx-auto w-24 h-24 bg-white dark:bg-gray-800 rounded-full shadow-lg flex items-center justify-center transition-colors duration-300">
            <i class="fas fa-user-plus text-4xl text-indigo-600 dark:text-indigo-400"></i>
        </div>
        
        <h2 class="mt-6 text-center text-4xl font-extrabold bg-gradient-to-r from-indigo-600 to-blue-500 dark:from-indigo-400 dark:to-blue-300 bg-clip-text text-transparent">
            Únete a nosotros
        </h2>
        <p class="mt-2 text-center text-sm text-gray-600 dark:text-gray-400 transition-colors duration-300">
            ¿Ya tienes una cuenta?
            <a href="<?= base_url('login') ?>" class="font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 dark:hover:text-indigo-300 transition-colors duration-200">
                Inicia sesión aquí
            </a>
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white dark:bg-gray-800 py-8 px-4 shadow-2xl sm:rounded-xl sm:px-10 relative overflow-hidden transition-colors duration-300">
            <!-- Decorative Element -->
            <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-gradient-to-br from-indigo-600 to-blue-500 dark:from-indigo-400 dark:to-blue-300 opacity-10 transform rotate-45"></div>

            <!-- Mensajes de error con animación -->
            <?php if (session()->has('errors')): ?>
                <div class="bg-red-50 dark:bg-red-900/50 border-l-4 border-red-400 p-4 mb-6 rounded-lg animate-fade-in">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-circle text-red-400 text-lg"></i>
                        </div>
                        <div class="ml-3">
                            <ul class="text-sm text-red-700 dark:text-red-400 list-disc list-inside">
                            <?php foreach (session('errors') as $error): ?>
                                <li><?= esc($error) ?></li>
                            <?php endforeach ?>
                            </ul>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <form class="space-y-6" action="<?= base_url('admin/clientes/store') ?>" method="POST" id="registerForm">
                <?= csrf_field() ?>

                <!-- Sección de información personal -->
                <div class="space-y-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white transition-colors duration-300">
                        Información Personal
                    </h3>
                    
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div class="relative group">
                            <label for="nombre" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Nombre
                            </label>
                            <input id="nombre" 
                                   name="nombre" 
                                   type="text" 
                                   required
                                   class="block w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-transparent transition-all duration-200"
                                   value="<?= old('nombre') ?>">
                            <div class="hidden group-focus-within:block absolute -bottom-5 left-0 text-xs text-gray-500 dark:text-gray-400">
                                Ingresa tu nombre como aparece en tu documento
                            </div>
                        </div>

                        <!-- Campo Apellido -->
                        <div class="relative group">
                            <label for="apellido" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Apellido
                            </label>
                            <input id="apellido" 
                                   name="apellido" 
                                   type="text" 
                                   required
                                   class="block w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-transparent transition-all duration-200"
                                   value="<?= old('apellido') ?>">
                        </div>
                    </div>
                </div>

                <!-- Sección de contacto -->
                <div class="space-y-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white transition-colors duration-300">
                        Información de Contacto
                    </h3>
                    
                    <!-- Campos de Email y Teléfono con íconos -->
                    <div class="space-y-6">
                        <div class="relative group">
                            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Correo electrónico
                            </label>
                            <div class="mt-1 relative rounded-lg shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-envelope text-gray-400 dark:text-gray-500"></i>
                                </div>
                                <input id="email" 
                                       name="email" 
                                       type="email" 
                                       autocomplete="email" 
                                       required
                                       class="block w-full pl-10 px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-transparent transition-all duration-200"
                                       value="<?= old('email') ?>">
                            </div>
                        </div>
                        <div class="relative group">
                            <label for="dni" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                dni
                            </label>
                            <div class="mt-1 relative rounded-lg shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-envelope text-gray-400 dark:text-gray-500"></i>
                                </div>
                                <input id="dni" 
                                       name="dni" 
                                       type="dni" 
                                       autocomplete="dni" 
                                       required
                                       class="block w-full pl-10 px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-transparent transition-all duration-200"
                                       value="<?= old('dni') ?>">
                            </div>
                        </div>

                        <div class="relative group">
                            <label for="telefono" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Teléfono (opcional)
                            </label>
                            <div class="mt-1 relative rounded-lg shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-phone text-gray-400 dark:text-gray-500"></i>
                                </div>
                                <input id="telefono" 
                                       name="telefono" 
                                       type="tel"
                                       class="block w-full pl-10 px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-transparent transition-all duration-200"
                                       value="<?= old('telefono') ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sección de seguridad -->
                <div class="space-y-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white transition-colors duration-300">
                        Seguridad
                    </h3>
                    
                    <div class="space-y-6">
                        <!-- Campo Contraseña -->
                        <div class="relative group">
                            <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Contraseña
                            </label>
                            <div class="mt-1 relative">
                                <input id="password" 
                                       name="password" 
                                       type="password"
                                       required
                                       class="block w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-transparent transition-all duration-200"
                                       minlength="6">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                    <button type="button" onclick="togglePassword('password')" class="text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="mt-2">
                                <div class="text-xs text-gray-500 dark:text-gray-400">La contraseña debe contener:</div>
                                <ul class="mt-1 text-xs space-y-1">
                                    <li id="length" class="text-gray-500 dark:text-gray-400">
                                        <i class="fas fa-check-circle mr-1"></i>
                                        Mínimo 6 caracteres
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <!-- Campo Confirmar Contraseña -->
                        <div class="relative group">
                            <label for="confirm_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Confirmar Contraseña
                            </label>
                            <div class="mt-1 relative">
                                <input id="confirm_password" 
                                       name="confirm_password" 
                                       type="password"
                                       required
                                       class="block w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-transparent transition-all duration-200">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                    <button type="button" onclick="togglePassword('confirm_password')" class="text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Términos y condiciones -->
                <div class="flex items-start space-x-3 pt-6">
                    <div class="flex items-center h-5">
                        <input id="terminos" 
                               name="terminos" 
                               type="checkbox"
                               required
                               class="h-4 w-4 text-indigo-600 dark:text-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 border-gray-300 dark:border-gray-600 rounded transition-all duration-200">
                    </div>
                    <div class="text-sm">
                        <label for="terminos" class="font-medium text-gray-700 dark:text-gray-300">
                            Acepto los términos y condiciones
                        </label>
                        <p class="text-gray-500 dark:text-gray-400">
                            Al crear una cuenta, aceptas nuestros
                            <a href="<?= base_url('tienda/terminos-y-condiciones') ?>" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 dark:hover:text-indigo-300">
                                términos y condiciones
                            </a>
                            y nuestra
                            <a href="<?= base_url('tienda/politica-privacidad') ?>" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 dark:hover:text-indigo-300">
                                política de privacidad
                            </a>
                        </p>
                    </div>
                </div>

                <!-- Botón de registro -->
                <div>
                    <button type="submit" 
                            class="w-full flex justify-center items-center px-6 py-3 border border-transparent text-base font-medium rounded-lg text-white bg-gradient-to-r from-indigo-600 to-blue-500 dark:from-indigo-500 dark:to-blue-400 hover:from-indigo-700 hover:to-blue-600 dark:hover:from-indigo-600 dark:hover:to-blue-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 transform transition-all duration-200 hover:scale-[1.02] active:scale-[0.98] shadow-lg hover:shadow-xl">
                        <i class="fas fa-user-plus mr-2"></i>
                        Crear cuenta
                    </button>
                </div>
            </form>

<!-- Separador -->
<div class="mt-8">
    <div class="relative">
        <div class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-gray-200 dark:border-gray-700"></div>
        </div>
        <div class="relative flex justify-center text-sm">
            <span class="px-4 bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400">
                O regístrate con
            </span>
        </div>
    </div>

    <div class="mt-6 grid grid-cols-2 gap-4">
        <button type="button" class="w-full flex items-center justify-center px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-all duration-200 transform hover:scale-[1.02] active:scale-[0.98]">
            <img src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Google" class="h-5 w-5 mr-2">
            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Google</span>
        </button>

        <button type="button" class="w-full flex items-center justify-center px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-all duration-200 transform hover:scale-[1.02] active:scale-[0.98]">
            <img src="https://www.svgrepo.com/show/475647/facebook-color.svg" alt="Facebook" class="h-5 w-5 mr-2">
            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Facebook</span>
        </button>
    </div>
</div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('registerForm');
        const password = document.getElementById('password');
        const confirmPassword = document.getElementById('confirm_password');
        const lengthCheck = document.getElementById('length');

        // Validación de contraseña en tiempo real
        password.addEventListener('input', function() {
            const isLongEnough = this.value.length >= 6;
            lengthCheck.classList.toggle('text-green-500', isLongEnough);
            lengthCheck.classList.toggle('text-gray-500 dark:text-gray-400', !isLongEnough);
        });

        // Validación de coincidencia de contraseñas
        function validatePasswordMatch() {
            const match = password.value === confirmPassword.value;
            const commonClasses = 'transition-colors duration-200';
            if (confirmPassword.value) {
                if (match) {
                    confirmPassword.className = `block w-full px-4 py-3 rounded-lg border border-green-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-green-500 focus:border-transparent ${commonClasses}`;
                } else {
                    confirmPassword.className = `block w-full px-4 py-3 rounded-lg border border-red-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-red-500 focus:border-transparent ${commonClasses}`;
                }
            }
        }

        confirmPassword.addEventListener('input', validatePasswordMatch);
        password.addEventListener('input', validatePasswordMatch);

        // Función para mostrar/ocultar contraseña
        window.togglePassword = function(inputId) {
            const input = document.getElementById(inputId);
            const icon = input.nextElementSibling.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        };

        // Validación del formulario antes de enviar
        form.addEventListener('submit', function(event) {
            if (password.value !== confirmPassword.value) {
                event.preventDefault();
                showError('Las contraseñas no coinciden');
            }
        });

        // Función para mostrar errores
        function showError(message) {
            const errorDiv = document.createElement('div');
            errorDiv.className = 'bg-red-50 dark:bg-red-900/50 border-l-4 border-red-400 p-4 mb-6 rounded-lg animate-fade-in';
            errorDiv.innerHTML = `
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-red-400 text-lg"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-red-700 dark:text-red-400">${message}</p>
                    </div>
                </div>
            `;
            
            const existingError = form.querySelector('.bg-red-50');
            if (existingError) {
                existingError.remove();
            }
            
            form.insertBefore(errorDiv, form.firstChild);
            
            // Animación suave para el error
            errorDiv.style.opacity = '0';
            errorDiv.style.transform = 'translateY(-10px)';
            requestAnimationFrame(() => {
                errorDiv.style.transition = 'all 0.3s ease';
                errorDiv.style.opacity = '1';
                errorDiv.style.transform = 'translateY(0)';
            });
        }
    });
</script>
<?= $this->endSection() ?>