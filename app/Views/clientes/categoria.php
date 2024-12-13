<?= $this->extend('clientes/layout') ?>

<?= $this->section('content') ?>
<div class="mb-8">
    <h1 class="text-3xl font-bold mb-2 dark:text-white"><?= esc($categoria['nombre']) ?></h1>
    <?php if($categoria['descripcion']): ?>
        <p class="text-gray-600 dark:text-gray-300"><?= esc($categoria['descripcion']) ?></p>
    <?php endif; ?>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php if(empty($productos)): ?>
        <div class="col-span-full text-center py-8">
            <p class="text-gray-600 dark:text-gray-300">No hay productos disponibles en esta categoría.</p>
        </div>
    <?php else: ?>
        <?php foreach($productos as $producto): ?>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden group hover:shadow-xl transition-shadow duration-300">
            <a href="<?= base_url('tienda/producto/' . $producto['id_producto']) ?>">
                <?php if($producto['imagen_url']): ?>
                    <img src="<?= esc($producto['imagen_url']) ?>" 
                         alt="<?= esc($producto['nombre']) ?>"
                         class="w-full h-48 object-cover object-center group-hover:opacity-75 transition-opacity">
                <?php else: ?>
                    <div class="w-full h-48 bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                        <i class="fas fa-image text-4xl text-gray-400 dark:text-gray-500"></i>
                    </div>
                <?php endif; ?>
                
                <div class="p-4">
                    <h3 class="text-lg font-semibold mb-2 text-gray-900 dark:text-white"><?= esc($producto['nombre']) ?></h3>
                    <p class="text-gray-600 dark:text-gray-300 mb-2 text-sm"><?= character_limiter(esc($producto['descripcion']), 100) ?></p>
                    
                    <div class="flex justify-between items-center">
                        <div class="flex flex-col">
                            <?php if($producto['precio_oferta']): ?>
                                <span class="text-gray-400 dark:text-gray-500 line-through text-sm">
                                    S/ <?= number_format($producto['precio'], 2) ?>
                                </span>
                                <span class="text-lg font-bold text-red-600 dark:text-red-400">
                                    S/ <?= number_format($producto['precio_oferta'], 2) ?>
                                </span>
                            <?php else: ?>
                                <span class="text-lg font-bold text-gray-900 dark:text-white">
                                    S/ <?= number_format($producto['precio'], 2) ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        
                        <?php if($producto['stock'] > 0): ?>
                            <button onclick="agregarAlCarrito(<?= $producto['id_producto'] ?>)" 
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors duration-200">
                                <i class="fas fa-cart-plus"></i>
                            </button>
                        <?php else: ?>
                            <span class="text-red-600 dark:text-red-400 text-sm font-medium">Agotado</span>
                        <?php endif; ?>
                    </div>
                </div>
            </a>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Pagination -->
<?php if ($pager): ?>
<div class="mt-8">
    <?= $pager->links() ?>
</div>
<?php endif; ?>

<script>
function agregarAlCarrito(idProducto) {
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
            // Actualizar contador del carrito
            const cartCount = document.querySelector('.cart-count');
            if (cartCount) {
                cartCount.textContent = data.cart_count;
            }
            // Mostrar mensaje de éxito
            alert('Producto agregado al carrito');
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al agregar el producto al carrito');
    });
}
</script>
<?= $this->endSection() ?>