<?= $this->extend('base') ?>

<?= $this->section('title') ?>
Editar Venta
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<!-- Título de la página -->
<h1 class="text-3xl font-extrabold text-gray-900 dark:text-white mb-6">Editar Venta</h1>

<!-- Formulario para editar una venta -->
<div class="bg-white dark:bg-gray-800 p-8 rounded-xl shadow-lg transition-colors duration-200">
    <form action="<?= base_url('ventas/update/' . $venta['id_venta']) ?>" method="post" class="space-y-6">
        <div class="space-y-2">
            <label for="id_cliente" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Cliente</label>
            <select id="id_cliente" name="id_cliente" required class="w-full px-4 py-3 rounded-lg border border-gray-600 focus:ring-2 focus:ring-blue-500 bg-gray-800 text-white">
                <?php foreach ($clientes as $cliente): ?>
                    <option value="<?= $cliente['id_cliente'] ?>" <?= $cliente['id_cliente'] == $venta['id_cliente'] ? 'selected' : '' ?>><?= $cliente['nombre'] ?> <?= $cliente['apellido'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="space-y-2">
            <label for="id_metodo_pago" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Método de Pago</label>
            <select id="id_metodo_pago" name="id_metodo_pago" required class="w-full px-4 py-3 rounded-lg border border-gray-600 focus:ring-2 focus:ring-blue-500 bg-gray-800 text-white">
                <?php foreach ($metodos_pago as $metodo): ?>
                    <option value="<?= $metodo['id_metodo_pago'] ?>" <?= $metodo['id_metodo_pago'] == $venta['id_metodo_pago'] ? 'selected' : '' ?>><?= $metodo['nombre_metodo'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="space-y-2">
            <label for="total" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Total</label>
            <input type="number" step="0.01" id="total" name="total" value="<?= $venta['total'] ?>" required class="w-full px-4 py-3 rounded-lg border border-gray-600 focus:ring-2 focus:ring-blue-500 bg-gray-800 text-white placeholder-gray-400">
        </div>
        <div class="space-y-2">
            <label for="estado" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Estado</label>
            <select id="estado" name="estado" required class="w-full px-4 py-3 rounded-lg border border-gray-600 focus:ring-2 focus:ring-blue-500 bg-gray-800 text-white">
                <option value="pendiente" <?= $venta['estado'] == 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
                <option value="completada" <?= $venta['estado'] == 'completada' ? 'selected' : '' ?>>Completada</option>
                <option value="cancelada" <?= $venta['estado'] == 'cancelada' ? 'selected' : '' ?>>Cancelada</option>
            </select>
        </div>
        <button type="submit" class="w-full px-4 py-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors">Guardar</button>
    </form>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // Puedes agregar scripts personalizados aquí si es necesario
</script>
<?= $this->endSection() ?>