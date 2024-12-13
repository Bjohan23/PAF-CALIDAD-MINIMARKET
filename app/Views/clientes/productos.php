<?= $this->extend('clientes/layout') ?>

<?= $this->section('content') ?>
<div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-gray-900 dark:to-gray-800 min-h-screen p-6 transition-colors duration-300">
    <div class="flex flex-col md:flex-row gap-6">
        <!-- Sidebar Filters -->
        <div class="w-full md:w-72 flex-shrink-0">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 sticky top-6 transition-all duration-300 hover:shadow-xl">
                <h3 class="font-bold text-xl mb-6 text-indigo-800 dark:text-indigo-400 border-b-2 border-indigo-100 dark:border-gray-700 pb-2">Filtros de Búsqueda</h3>

                <!-- Categories -->
                <div class="mb-8">
                    <h4 class="font-semibold text-lg mb-3 text-gray-700 dark:text-gray-300">Categorías</h4>
                    <div class="space-y-2">
                        <?php foreach ($categorias as $categoria): ?>
                            <label class="flex items-center space-x-3 p-2 rounded-lg hover:bg-indigo-50 dark:hover:bg-gray-700 transition-colors duration-200">
                                <input type="checkbox"
                                    name="categoria"
                                    value="<?= $categoria['id_categoria'] ?>"
                                    class="w-4 h-4 rounded border-gray-300 dark:border-gray-600 text-indigo-600 focus:ring-indigo-500 dark:bg-gray-700">
                                <span class="text-gray-700 dark:text-gray-300 select-none"><?= $categoria['nombre'] ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Price Range -->
                <div class="mb-8">
                    <h4 class="font-semibold text-lg mb-3 text-gray-700 dark:text-gray-300">Rango de Precio</h4>
                    <div class="space-y-4">
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 dark:text-gray-400">S/</span>
                            <input type="number"
                                name="precio_min"
                                min="0"
                                placeholder="Mínimo"
                                class="pl-8 w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring focus:ring-indigo-200">
                        </div>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 dark:text-gray-400">S/</span>
                            <input type="number"
                                name="precio_max"
                                min="0"
                                placeholder="Máximo"
                                class="pl-8 w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring focus:ring-indigo-200">
                        </div>
                    </div>
                </div>

                <!-- Stock Filter -->
                <div class="mb-8">
                    <h4 class="font-semibold text-lg mb-3 text-gray-700 dark:text-gray-300">Disponibilidad</h4>
                    <label class="flex items-center p-2 rounded-lg hover:bg-indigo-50 dark:hover:bg-gray-700 transition-colors duration-200 cursor-pointer">
                        <input type="checkbox"
                            name="en_stock"
                            class="w-4 h-4 rounded border-gray-300 dark:border-gray-600 text-indigo-600 focus:ring-indigo-500 dark:bg-gray-700">
                        <span class="ml-3 text-gray-700 dark:text-gray-300">Productos en Stock</span>
                    </label>
                </div>

                <!-- Apply Filters Button -->
                <button type="button"
                    id="aplicarFiltros"
                    class="w-full bg-gradient-to-r from-indigo-600 to-blue-500 text-white px-6 py-3 rounded-lg font-semibold
                    transform transition duration-200 hover:scale-[1.02] hover:shadow-lg active:scale-[0.98]">
                    Aplicar Filtros
                </button>
            </div>
        </div>

        <!-- Product Grid Section -->
        <div class="flex-1">
            <!-- Sort Options -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-4 mb-6">
                <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                    <div class="text-gray-600 dark:text-gray-300 font-medium">
                        <span class="text-indigo-600 dark:text-indigo-400"><?= count($productos) ?></span> productos encontrados
                    </div>
                    <div class="flex items-center space-x-3">
                        <label class="text-gray-600 dark:text-gray-300 font-medium">Ordenar por:</label>
                        <select id="ordenar"
                            class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring focus:ring-indigo-200">
                            <option value="recientes">Más recientes</option>
                            <option value="precio_asc">Precio: Menor a Mayor</option>
                            <option value="precio_desc">Precio: Mayor a Menor</option>
                            <option value="nombre_asc">Nombre: A-Z</option>
                            <option value="nombre_desc">Nombre: Z-A</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Products Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($productos as $producto): ?>
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden transform transition duration-300 hover:scale-[1.02] hover:shadow-xl">
                        <a href="<?= base_url('tienda/producto/' . $producto['id_producto']) ?>" class="block">
                            <div class="relative overflow-hidden group">
                                <?php if ($producto['imagen_url']): ?>
                                    <img src="<?= $producto['imagen_url'] ?>"
                                        alt="<?= $producto['nombre'] ?>"
                                        class="w-full h-56 object-cover object-center transform transition duration-300 group-hover:scale-110">
                                <?php else: ?>
                                    <div class="w-full h-56 bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-600 flex items-center justify-center">
                                        <i class="fas fa-image text-4xl text-gray-400 dark:text-gray-500"></i>
                                    </div>
                                <?php endif; ?>

                                <?php if ($producto['precio_oferta']): ?>
                                    <div class="absolute top-3 right-3 bg-gradient-to-r from-red-500 to-pink-500 text-white px-3 py-1 rounded-full
                                        font-semibold text-sm transform -rotate-3 shadow-lg">
                                        -<?= round((($producto['precio'] - $producto['precio_oferta']) / $producto['precio']) * 100) ?>%
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="p-6">
                                <?php if (isset($producto['categoria_nombre'])): ?>
                                    <div class="text-sm text-indigo-600 dark:text-indigo-400 font-medium mb-2"><?= esc($producto['categoria_nombre']) ?></div>
                                <?php endif; ?>
                                <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-2 line-clamp-2"><?= esc($producto['nombre']) ?></h3>
                                <p class="text-gray-600 dark:text-gray-300 mb-4 text-sm line-clamp-2"><?= character_limiter(esc($producto['descripcion']), 100) ?></p>

                                <div class="flex justify-between items-center">
                                    <div class="space-y-1">
                                        <?php if ($producto['precio_oferta']): ?>
                                            <span class="text-sm text-gray-400 dark:text-gray-500 line-through block">S/ <?= number_format($producto['precio'], 2) ?></span>
                                            <span class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">S/ <?= number_format($producto['precio_oferta'], 2) ?></span>
                                        <?php else: ?>
                                            <span class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">S/ <?= number_format($producto['precio'], 2) ?></span>
                                        <?php endif; ?>
                                    </div>

                                    <?php if ($producto['stock'] > 0): ?>
                                        <button type="button"
                                            onclick="agregarAlCarrito(<?= $producto['id_producto'] ?>)"
                                            class="bg-gradient-to-r from-indigo-600 to-blue-500 text-white px-4 py-2 rounded-lg
                                            transform transition duration-200 hover:scale-105 hover:shadow-lg active:scale-95">
                                            <i class="fas fa-cart-plus"></i>
                                        </button>
                                    <?php else: ?>
                                        <span class="px-3 py-1 bg-red-100 dark:bg-red-900 text-red-600 dark:text-red-300 rounded-lg text-sm font-medium">Agotado</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <?php if ($pager): ?>
                <div class="mt-12">
                    <?= $pager->links() ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Animación suave al cargar la página
        document.querySelectorAll('.grid > div').forEach((el, i) => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(20px)';
            setTimeout(() => {
                el.style.transition = 'all 0.5s ease';
                el.style.opacity = '1';
                el.style.transform = 'translateY(0)';
            }, i * 100);
        });

        // Función para aplicar filtros
        document.getElementById('aplicarFiltros').addEventListener('click', function() {
            aplicarFiltros();
        });

        // Función para ordenar productos
        document.getElementById('ordenar').addEventListener('change', function() {
            aplicarFiltros();
        });

        function aplicarFiltros() {
            const categorias = Array.from(document.querySelectorAll('input[name="categoria"]:checked'))
                .map(input => input.value);

            const precioMin = document.querySelector('input[name="precio_min"]').value;
            const precioMax = document.querySelector('input[name="precio_max"]').value;
            const enStock = document.querySelector('input[name="en_stock"]').checked;
            const ordenar = document.getElementById('ordenar').value;

            const params = new URLSearchParams();

            if (categorias.length) params.append('categorias', categorias.join(','));
            if (precioMin) params.append('precio_min', precioMin);
            if (precioMax) params.append('precio_max', precioMax);
            if (enStock) params.append('en_stock', '1');
            params.append('ordenar', ordenar);

            window.location.href = '<?= base_url('tienda/productos') ?>?' + params.toString();
        }
    });

    function agregarAlCarrito(idProducto) {
        <?php if (!$isLoggedIn): ?>
            Swal.fire({
                title: 'Iniciar sesión requerido',
                text: 'Necesitas iniciar sesión para agregar productos al carrito',
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#4F46E5',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Ir a login',
                cancelButtonText: 'Cancelar',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Guardar el estado actual de la página en sessionStorage
                    sessionStorage.setItem('lastPage', window.location.href);
                    window.location.href = '<?= base_url('login') ?>';
                }
            });
            return;
        <?php endif; ?>

        const button = event.currentTarget;
        button.disabled = true;
        button.classList.add('opacity-75');

        fetch('<?= base_url('tienda/carrito/agregar') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    id_producto: idProducto,
                    cantidad: 1
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Actualizar contador del carrito con animación
                    const cartCount = document.querySelector('.cart-count');
                    if (cartCount) {
                        cartCount.textContent = data.cart_count;
                        cartCount.classList.add('scale-125');
                        setTimeout(() => cartCount.classList.remove('scale-125'), 200);
                    }

                    // Mostrar notificación de éxito
                    Swal.fire({
                        title: '¡Éxito!',
                        text: 'Producto agregado al carrito',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false,
                        position: 'bottom-end',
                        toast: true
                    });
                } else {
                    if (data.requireLogin) {
                        Swal.fire({
                            title: 'Iniciar sesión requerido',
                            text: data.message,
                            icon: 'info',
                            showCancelButton: true,
                            confirmButtonColor: '#4F46E5',
                            cancelButtonColor: '#6B7280',
                            confirmButtonText: 'Ir a login',
                            cancelButtonText: 'Cancelar'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = '<?= base_url('login') ?>';
                            }
                        });
                    } else {
                        Swal.fire({
                            title: 'Error',
                            text: data.message,
                            icon: 'error'
                        });
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    title: 'Error',
                    text: 'Error al agregar el producto al carrito',
                    icon: 'error'
                });
            })
            .finally(() => {
                button.disabled = false;
                button.classList.remove('opacity-75');
            });
    }

    function mostrarNotificacion(mensaje, tipo) {
        const notificacion = document.createElement('div');
        notificacion.className = `fixed bottom-4 right-4 px-6 py-3 rounded-lg shadow-lg transition-all duration-300 transform translate-y-full
            ${tipo === 'success' ? 'bg-green-500' : 'bg-red-500'} text-white font-medium z-50`;
        notificacion.textContent = mensaje;
        document.body.appendChild(notificacion);

        setTimeout(() => {
            notificacion.style.transform = 'translate(0)';
        }, 100);

        setTimeout(() => {
            notificacion.style.transform = 'translateY(full)';
            setTimeout(() => notificacion.remove(), 300);
        }, 3000);
    }
</script>
<?= $this->endSection() ?>