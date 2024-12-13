<?= $this->extend('clientes/layout') ?>

<?= $this->section('content') ?>
<div class="container mx-auto px-4 py-8">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl overflow-hidden transition-all duration-300">
        <div class="md:flex">
            <!-- Imagen del producto con galería mejorada -->
            <div class="md:w-1/2 relative group">
                <?php if($producto['imagen_url']): ?>
                    <div class="relative overflow-hidden">
                        <img src="<?= esc($producto['imagen_url']) ?>" 
                             alt="<?= esc($producto['nombre']) ?>"
                             class="w-full h-[600px] object-cover transform transition-transform duration-500 group-hover:scale-110">
                        <?php if($producto['precio_oferta']): ?>
                            <div class="absolute top-4 right-4 bg-gradient-to-r from-red-500 to-pink-500 text-white px-6 py-2 rounded-full transform -rotate-3 shadow-lg">
                                <span class="text-xl font-bold">-<?= round((($producto['precio'] - $producto['precio_oferta']) / $producto['precio']) * 100) ?>%</span>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="w-full h-[600px] bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-600 flex items-center justify-center transition-colors duration-300">
                        <i class="fas fa-image text-6xl text-gray-400 dark:text-gray-500"></i>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Detalles del producto con mejor diseño -->
            <div class="md:w-1/2 p-8 lg:p-12">
                <div class="space-y-8">
                    <div>
                        <h1 class="text-4xl font-bold mb-4 text-gray-900 dark:text-white transition-colors duration-300">
                            <?= esc($producto['nombre']) ?>
                        </h1>
                        
                        <div class="prose dark:prose-invert max-w-none mb-6 text-gray-600 dark:text-gray-300 transition-colors duration-300">
                            <?= nl2br(esc($producto['descripcion'])) ?>
                        </div>
                    </div>

                    <!-- Precio con diseño mejorado -->
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-6 transition-colors duration-300">
                        <?php if($producto['precio_oferta']): ?>
                            <div class="space-y-2">
                                <div class="flex items-center gap-3">
                                    <span class="text-gray-400 dark:text-gray-500 line-through text-2xl transition-colors duration-300">
                                        S/ <?= number_format($producto['precio'], 2) ?>
                                    </span>
                                    <span class="text-4xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                                        S/ <?= number_format($producto['precio_oferta'], 2) ?>
                                    </span>
                                </div>
                                <span class="inline-block bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 text-sm font-medium px-4 py-1.5 rounded-full transition-colors duration-300">
                                    ¡Oferta especial! <?= round((($producto['precio'] - $producto['precio_oferta']) / $producto['precio']) * 100) ?>% de descuento
                                </span>
                            </div>
                        <?php else: ?>
                            <span class="text-4xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                                S/ <?= number_format($producto['precio'], 2) ?>
                            </span>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Stock y Botón de compra mejorados -->
                    <div class="space-y-6">
                        <?php if($producto['stock'] > 0): ?>
                            <div class="flex items-center gap-2">
                                <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
                                <span class="text-green-600 dark:text-green-400 font-medium transition-colors duration-300">
                                    <?= $producto['stock'] ?> unidades disponibles
                                </span>
                            </div>
                            <div class="flex items-center gap-4">
                                <div class="w-32">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 transition-colors duration-300">Cantidad</label>
                                    <input type="number" 
                                           id="cantidad" 
                                           min="1" 
                                           max="<?= $producto['stock'] ?>" 
                                           value="1"
                                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white transition-all duration-300">
                                </div>
                                <button onclick="agregarAlCarrito(<?= $producto['id_producto'] ?>)" 
                                        class="flex-1 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white text-lg font-semibold px-8 py-4 rounded-xl transition-all duration-300 transform hover:scale-[1.02] active:scale-[0.98] shadow-lg hover:shadow-xl flex items-center justify-center gap-3">
                                    <i class="fas fa-cart-plus"></i>
                                    Agregar al carrito
                                </button>
                            </div>
                        <?php else: ?>
                            <div class="bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 px-6 py-4 rounded-xl font-medium flex items-center gap-3 transition-colors duration-300">
                                <i class="fas fa-exclamation-circle"></i>
                                Producto temporalmente agotado
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Productos relacionados con diseño mejorado -->
    <?php if(!empty($relacionados)): ?>
    <section class="mt-16">
        <h2 class="text-3xl font-bold mb-8 text-gray-900 dark:text-white transition-colors duration-300">
            Productos relacionados
        </h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php foreach($relacionados as $relacionado): ?>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden group hover:shadow-2xl transition-all duration-300 transform hover:scale-[1.02]">
                <a href="<?= base_url('tienda/producto/' . $relacionado['id_producto']) ?>">
                    <div class="relative overflow-hidden">
                        <?php if($relacionado['imagen_url']): ?>
                            <img src="<?= esc($relacionado['imagen_url']) ?>" 
                                 alt="<?= esc($relacionado['nombre']) ?>"
                                 class="w-full h-56 object-cover object-center transform transition-transform duration-500 group-hover:scale-110">
                        <?php else: ?>
                            <div class="w-full h-56 bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-600 flex items-center justify-center transition-colors duration-300">
                                <i class="fas fa-image text-4xl text-gray-400 dark:text-gray-500"></i>
                            </div>
                        <?php endif; ?>
                        
                        <?php if($relacionado['precio_oferta']): ?>
                            <div class="absolute top-2 right-2 bg-red-500 text-white px-2 py-1 rounded-lg text-sm font-medium">
                                -<?= round((($relacionado['precio'] - $relacionado['precio_oferta']) / $relacionado['precio']) * 100) ?>%
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-3 text-gray-900 dark:text-white transition-colors duration-300 line-clamp-2">
                            <?= esc($relacionado['nombre']) ?>
                        </h3>
                        <div class="flex justify-between items-end">
                            <?php if($relacionado['precio_oferta']): ?>
                                <div class="space-y-1">
                                    <span class="text-gray-400 dark:text-gray-500 line-through text-sm block transition-colors duration-300">
                                        S/ <?= number_format($relacionado['precio'], 2) ?>
                                    </span>
                                    <span class="text-xl font-bold text-indigo-600 dark:text-indigo-400 transition-colors duration-300">
                                        S/ <?= number_format($relacionado['precio_oferta'], 2) ?>
                                    </span>
                                </div>
                            <?php else: ?>
                                <span class="text-xl font-bold text-indigo-600 dark:text-indigo-400 transition-colors duration-300">
                                    S/ <?= number_format($relacionado['precio'], 2) ?>
                                </span>
                            <?php endif; ?>
                            
                            <?php if($relacionado['stock'] > 0): ?>
                                <span class="text-green-500 dark:text-green-400 text-sm transition-colors duration-300">En stock</span>
                            <?php else: ?>
                                <span class="text-red-500 dark:text-red-400 text-sm transition-colors duration-300">Agotado</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>
</div>

<script>
function agregarAlCarrito(idProducto) {
    const button = event.currentTarget;
    const cantidad = document.getElementById('cantidad').value;
    
    // Deshabilitar botón mientras se procesa
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
            cantidad: parseInt(cantidad)
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
            mostrarNotificacion('¡Producto agregado al carrito!', 'success');
        } else {
            mostrarNotificacion(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        mostrarNotificacion('Error al agregar el producto al carrito', 'error');
    })
    .finally(() => {
        button.disabled = false;
        button.classList.remove('opacity-75');
    });
}

function mostrarNotificacion(mensaje, tipo) {
    const notificacion = document.createElement('div');
    notificacion.className = `fixed bottom-4 right-4 px-6 py-3 rounded-xl shadow-lg transition-all duration-300 transform translate-y-full
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