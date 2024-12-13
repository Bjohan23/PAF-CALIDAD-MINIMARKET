<?= $this->extend('clientes/layout') ?>

<?= $this->section('content') ?>
<!-- Hero Carousel Section -->
 
<div class="relative bg-gradient-to-br from-gray-900 to-indigo-900 dark:from-gray-900 dark:to-gray-800 rounded-2xl overflow-hidden mb-16 shadow-2xl">
    <div class="relative" x-data="{ activeSlide: 0 }" x-init="setInterval(() => activeSlide = activeSlide === <?= count($slides) - 1 ?> ? 0 : activeSlide + 1, 5000)">
        <!-- Slides -->
        <div class="relative h-[500px] overflow-hidden">
        <?php foreach ($slides as $index => $slide): ?>
            <div class="absolute inset-0 transition-all duration-700 ease-in-out transform"
                 x-show="activeSlide === <?= $index ?>"
                 x-transition:enter="transition ease-out duration-700"
                 x-transition:enter-start="opacity-0 scale-105"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-500"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95">
                <img src="<?= $slide['image'] ?>" 
                     alt="<?= $slide['title'] ?>"
                     class="object-cover object-center w-full h-full transform scale-100 group-hover:scale-110 transition-transform duration-700">
                <div class="absolute inset-0 bg-gradient-to-r from-black/70 to-black/50"></div>
                <div class="absolute inset-0 flex items-center justify-center">
                    <div class="text-center text-white space-y-6 px-4 max-w-2xl mx-auto">
                        <h2 class="text-5xl font-bold mb-4 transform transition-all duration-500"
                            x-transition:enter="transition delay-300 duration-500"
                            x-transition:enter-start="opacity-0 translate-y-10"
                            x-transition:enter-end="opacity-100 translate-y-0">
                            <?= $slide['title'] ?>
                        </h2>
                        <p class="text-xl text-gray-200 mb-8"
                           x-transition:enter="transition delay-500 duration-500"
                           x-transition:enter-start="opacity-0 translate-y-10"
                           x-transition:enter-end="opacity-100 translate-y-0">
                            <?= $slide['description'] ?>
                        </p>
                        <a href="<?= $slide['buttonLink'] ?>" 
                           class="inline-block bg-white text-gray-900 px-8 py-3 rounded-full font-semibold hover:bg-gray-100 transform hover:scale-105 transition-all duration-200"
                           x-transition:enter="transition delay-700 duration-500"
                           x-transition:enter-start="opacity-0 translate-y-10"
                           x-transition:enter-end="opacity-100 translate-y-0">
                            <?= $slide['buttonText'] ?>
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Navigation arrows -->
        <div class="absolute inset-0 flex items-center justify-between p-4">
            <button class="p-2 rounded-full bg-black/30 text-white hover:bg-black/50 backdrop-blur-sm transition-all duration-200 transform hover:scale-110"
                    @click.prevent="activeSlide = activeSlide === 0 ? <?= count($slides) - 1 ?> : activeSlide - 1">
                <i class="fas fa-chevron-left text-xl"></i>
            </button>
            <button class="p-2 rounded-full bg-black/30 text-white hover:bg-black/50 backdrop-blur-sm transition-all duration-200 transform hover:scale-110"
                    @click.prevent="activeSlide = activeSlide === <?= count($slides) - 1 ?> ? 0 : activeSlide + 1">
                <i class="fas fa-chevron-right text-xl"></i>
            </button>
        </div>

        <!-- Indicators -->
        <div class="absolute bottom-6 left-1/2 transform -translate-x-1/2 flex space-x-3">
            <?php for ($i = 0; $i < count($slides); $i++): ?>
            <button class="w-2.5 h-2.5 rounded-full transition-all duration-300 focus:outline-none"
                    :class="activeSlide === <?= $i ?> ? 'bg-white scale-125' : 'bg-white/50 hover:bg-white/75'"
                    @click="activeSlide = <?= $i ?>"></button>
            <?php endfor; ?>
        </div>
    </div>
</div>

<!-- Categorías Destacadas -->
<section class="mb-16">
    <div class="flex items-center justify-between mb-8">
        <h2 class="text-3xl font-bold text-gray-900 dark:text-white">Categorías Destacadas</h2>
        <a href="<?= base_url('tienda/categorias') ?>" 
           class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 font-medium transition-colors duration-200">
            Ver todas <i class="fas fa-arrow-right ml-2"></i>
        </a>
    </div>
    
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
        <?php foreach ($categorias as $categoria): ?>
        <a href="<?= base_url('tienda/categoria/' . $categoria['id_categoria']) ?>" 
           class="group relative rounded-xl overflow-hidden transform hover:scale-105 transition-all duration-300">
            <div class="aspect-w-1 aspect-h-1">
                <?php if(isset($categoria['imagen_url'])): ?>
                    <img src="<?= esc($categoria['imagen_url']) ?>" 
                         alt="<?= esc($categoria['nombre']) ?>"
                         class="w-full h-48 object-cover object-center transition-transform duration-700 group-hover:scale-110">
                <?php else: ?>
                    <div class="w-full h-48 bg-gradient-to-br from-indigo-500 to-purple-600 group-hover:from-indigo-600 group-hover:to-purple-700 transition-all duration-300"></div>
                <?php endif; ?>
            </div>
            <div class="absolute inset-0 bg-gradient-to-t from-black/75 via-black/30 to-transparent flex items-end p-6">
                <div class="transform transition-all duration-300 group-hover:translate-y-0">
                    <h3 class="text-xl font-bold text-white"><?= esc($categoria['nombre']) ?></h3>
                    <?php if(isset($categoria['total_productos'])): ?>
                        <p class="text-gray-200 text-sm mt-1"><?= $categoria['total_productos'] ?> productos</p>
                    <?php endif; ?>
                </div>
            </div>
        </a>
        <?php endforeach; ?>
    </div>
</section>

<!-- Productos Destacados -->
<section class="mb-16">
    <div class="flex items-center justify-between mb-8">
        <h2 class="text-3xl font-bold text-gray-900 dark:text-white">Productos Destacados</h2>
        <a href="<?= base_url('tienda/productos') ?>" 
           class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 font-medium transition-colors duration-200">
            Ver todos <i class="fas fa-arrow-right ml-2"></i>
        </a>
    </div>
    
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <?php foreach ($productos_destacados as $producto): ?>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden group hover:shadow-2xl transition-all duration-300 transform hover:scale-[1.02]">
            <a href="<?= base_url('tienda/producto/' . $producto['id_producto']) ?>" class="block">
                <div class="relative overflow-hidden">
                    <?php if($producto['imagen_url']): ?>
                        <img src="<?= esc($producto['imagen_url']) ?>" 
                             alt="<?= esc($producto['nombre']) ?>"
                             class="w-full h-52 object-cover object-center transform transition-transform duration-500 group-hover:scale-110">
                    <?php else: ?>
                        <div class="w-full h-52 bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-600 flex items-center justify-center">
                            <i class="fas fa-image text-4xl text-gray-400 dark:text-gray-500"></i>
                        </div>
                    <?php endif; ?>
                    
                    <?php if($producto['precio_oferta']): ?>
                        <div class="absolute top-3 right-3 bg-red-500 text-white px-3 py-1 rounded-full text-sm font-medium transform -rotate-3">
                            -<?= round((($producto['precio'] - $producto['precio_oferta']) / $producto['precio']) * 100) ?>%
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="p-6">
                    <div class="text-sm text-indigo-600 dark:text-indigo-400 font-medium mb-1">
                        <?= isset($producto['categoria_nombre']) ? esc($producto['categoria_nombre']) : 'Categoría' ?>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2 line-clamp-1">
                        <?= esc($producto['nombre']) ?>
                    </h3>
                    <p class="text-gray-600 dark:text-gray-300 text-sm mb-4 line-clamp-2">
                        <?= character_limiter(esc($producto['descripcion']), 100) ?>
                    </p>
                    
                    <div class="flex justify-between items-end">
                        <div class="space-y-1">
                            <?php if($producto['precio_oferta']): ?>
                                <span class="text-sm text-gray-400 dark:text-gray-500 line-through block">
                                    S/ <?= number_format($producto['precio'], 2) ?>
                                </span>
                                <span class="text-2xl font-bold text-red-600 dark:text-red-400">
                                    S/ <?= number_format($producto['precio_oferta'], 2) ?>
                                </span>
                            <?php else: ?>
                                <span class="text-2xl font-bold text-gray-900 dark:text-white">
                                    S/ <?= number_format($producto['precio'], 2) ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        
                        <?php if($producto['stock'] > 0): ?>
                            <span class="flex items-center text-green-500 dark:text-green-400 text-sm font-medium">
                                <i class="fas fa-check-circle mr-1"></i> En stock
                            </span>
                        <?php else: ?>
                            <span class="flex items-center text-red-500 dark:text-red-400 text-sm font-medium">
                                <i class="fas fa-times-circle mr-1"></i> Agotado
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            </a>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- Ofertas Especiales -->
<section class="mb-16">
    <div class="flex items-center justify-between mb-8">
        <h2 class="text-3xl font-bold text-gray-900 dark:text-white">Ofertas Especiales</h2>
        <a href="<?= base_url('tienda/ofertas') ?>" 
           class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 font-medium transition-colors duration-200">
            Ver todas <i class="fas fa-arrow-right ml-2"></i>
        </a>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <?php foreach ($ofertas as $oferta): ?>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden group hover:shadow-2xl transition-all duration-300">
            <div class="md:flex">
                <div class="md:w-1/2 relative overflow-hidden">
                    <?php if($oferta['imagen_url']): ?>
                        <img src="<?= esc($oferta['imagen_url']) ?>" 
                             alt="<?=esc($oferta['nombre']) ?>"
                             class="w-full h-64 md:h-full object-cover transform transition-transform duration-500 group-hover:scale-110">
                    <?php else: ?>
                        <div class="w-full h-64 md:h-full bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-600 flex items-center justify-center">
                            <i class="fas fa-image text-4xl text-gray-400 dark:text-gray-500"></i>
                        </div>
                    <?php endif; ?>
                    <div class="absolute top-4 right-4 bg-red-500 text-white px-4 py-2 rounded-full text-sm font-bold transform rotate-3">
                        -<?= round((($oferta['precio'] - $oferta['precio_oferta']) / $oferta['precio']) * 100) ?>% OFF
                    </div>
                </div>
                <div class="p-8 md:w-1/2">
                    <div class="flex flex-col h-full justify-between">
                        <div>
                            <div class="text-sm text-red-600 dark:text-red-400 font-semibold uppercase tracking-wider mb-2">
                                Oferta Limitada
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
                                <?= esc($oferta['nombre']) ?>
                            </h3>
                            <p class="text-gray-600 dark:text-gray-300 mb-6 line-clamp-3">
                                <?= character_limiter(esc($oferta['descripcion']), 150) ?>
                            </p>
                        </div>
                        
                        <div>
                            <div class="flex items-baseline mb-4">
                                <span class="text-gray-400 dark:text-gray-500 line-through text-lg">
                                    S/ <?= number_format($oferta['precio'], 2) ?>
                                </span>
                                <span class="ml-3 text-3xl font-bold text-red-600 dark:text-red-400">
                                    S/ <?= number_format($oferta['precio_oferta'], 2) ?>
                                </span>
                            </div>
                            
                            <div class="flex space-x-4">
                                <a href="<?= base_url('tienda/producto/' . $oferta['id_producto']) ?>" 
                                   class="flex-1 inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-lg text-white bg-gradient-to-r from-red-600 to-pink-600 hover:from-red-700 hover:to-pink-700 transform hover:scale-105 transition-all duration-200">
                                    <i class="fas fa-shopping-cart mr-2"></i>
                                    Comprar Ahora
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
<?= $this->endSection() ?>