

 // ----------------------------------------------
// SECCIÓN DEL CARRITO DE COMPRAS
// ----------------------------------------------
class ShoppingCart {
    constructor() {
        this.items = new Map();
        this.total = 0;
        this.selectedClient = null;
        this.lastComprobanteNumbers = {
            boleta: 0,
            factura: 0
        };
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.fetchLastComprobanteNumbers();
        this.updateBtnCobrarState();
    }

    async fetchLastComprobanteNumbers() {
        try {
            
            const response = await fetch(`${BASE_URL}ventas/last-comprobante-numbers`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) {
                const error = await response.json();
                throw new Error(error.message || 'Error al obtener números de comprobante');
            }
            
            const data = await response.json();
            this.lastComprobanteNumbers = data;
            this.updateComprobanteNumber();
        } catch (error) {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error al obtener números de comprobante: ' + error.message,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });
        }
    }


    setupEventListeners() {
        // Evento para procesar venta
        document.getElementById('btn-cobrar')?.addEventListener('click', () => this.procesarVenta());
        
        // Evento para cambio de tipo de comprobante
        document.getElementById('tipo-comprobante')?.addEventListener('change', () => this.updateComprobanteNumber());
        
        // Evento para imprimir comprobante
        document.getElementById('btn-imprimir')?.addEventListener('click', () => this.imprimirBoleta());
    }

    setSelectedClient(clientData) {
        this.selectedClient = clientData;
        const clienteDisplay = document.getElementById('selected-client');
        
        if (clienteDisplay) {
            if (clientData) {
                clienteDisplay.innerHTML = `
                    <div class="flex items-center">
                        <i class="fas fa-user-check text-green-500 mr-2"></i>
                        <div>
                            <div class="font-medium text-gray-900 dark:text-white">
                                ${clientData.nombre} ${clientData.apellido}
                            </div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                DNI: ${clientData.dni}
                            </div>
                        </div>
                    </div>
                `;
                clienteDisplay.className = 'mt-3 px-4 py-2 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-900/30';
            } else {
                clienteDisplay.innerHTML = `
                    <div class="flex items-center text-gray-500">
                        <i class="fas fa-user-slash mr-2"></i>
                        No hay cliente seleccionado
                    </div>
                `;
                clienteDisplay.className = 'mt-3 px-4 py-2 bg-gray-100 dark:bg-gray-700 rounded-lg';
            }
        }
        
        this.updateBtnCobrarState();
    }

    addItem(productId, productName, price) {
        if (!this.selectedClient) {
            this.showError('Por favor, seleccione un cliente antes de agregar productos al carrito.');
            return false;
        }

        const parsedPrice = parseFloat(price);
        if (isNaN(parsedPrice) || parsedPrice <= 0) {
            this.showError('Precio inválido');
            return false;
        }

        let item = this.items.get(productId);
        
        if (item) {
            item.quantity++;
            item.subtotal = Number((item.quantity * item.price).toFixed(2));
        } else {
            item = {
                id: productId,
                name: productName,
                price: parsedPrice,
                quantity: 1,
                subtotal: parsedPrice
            };
            this.items.set(productId, item);
        }
        
        this.updateCart();
        this.updateBtnCobrarState();
        this.showSuccess('Producto agregado al carrito');
        return true;
    }

    removeItem(productId) {
        if (this.items.delete(productId)) {
            this.updateCart();
            this.updateBtnCobrarState();
            this.showSuccess('Producto eliminado del carrito');
        }
    }

    updateQuantity(productId, newQuantity) {
        const item = this.items.get(productId);
        if (!item) return;

        const quantity = Math.max(1, Math.min(99, newQuantity));
        if (quantity !== item.quantity) {
            item.quantity = quantity;
            item.subtotal = Number((item.quantity * item.price).toFixed(2));
            this.updateCart();
        }
    }

    updateCart() {
        const cartItems = document.getElementById('cart-items');
        if (!cartItems) return;

        cartItems.innerHTML = '';
        this.total = 0;

        if (this.items.size === 0) {
            cartItems.innerHTML = `
                <div class="text-center text-gray-500 dark:text-gray-400 py-8">
                    <i class="fas fa-shopping-cart text-4xl mb-4"></i>
                    <p>El carrito está vacío</p>
                </div>
            `;
            this.updateTotals();
            return;
        }

        this.items.forEach(item => {
            const itemElement = document.createElement('div');
            itemElement.className = 'flex flex-col p-4 mb-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg';
            itemElement.innerHTML = `
                <div class="flex justify-between items-start mb-3">
                    <div class="flex-1">
                        <h3 class="font-medium text-gray-900 dark:text-white mb-1">${item.name}</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">S/ ${item.price.toFixed(2)} c/u</p>
                    </div>
                    <button class="remove-item text-red-500 hover:text-red-700 p-1"
                            data-product-id="${item.id}">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <button class="quantity-btn minus p-2 bg-gray-200 dark:bg-gray-600 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-500"
                                data-product-id="${item.id}">
                            <i class="fas fa-minus"></i>
                        </button>
                        <span class="quantity w-12 text-center font-medium">${item.quantity}</span>
                        <button class="quantity-btn plus p-2 bg-gray-200 dark:bg-gray-600 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-500"
                                data-product-id="${item.id}">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                    <div class="font-medium text-gray-900 dark:text-white">
                        S/ ${item.subtotal.toFixed(2)}
                    </div>
                </div>
            `;

            const minusBtn = itemElement.querySelector('.quantity-btn.minus');
            const plusBtn = itemElement.querySelector('.quantity-btn.plus');
            const removeBtn = itemElement.querySelector('.remove-item');

            minusBtn.addEventListener('click', () => this.updateQuantity(item.id, item.quantity - 1));
            plusBtn.addEventListener('click', () => this.updateQuantity(item.id, item.quantity + 1));
            removeBtn.addEventListener('click', () => this.removeItem(item.id));

            cartItems.appendChild(itemElement);
            this.total += item.subtotal;
        });

        this.updateTotals();
    }

    updateTotals() {
        const subtotalElement = document.getElementById('cart-subtotal');
        const totalElement = document.getElementById('cart-total');
        
        if (subtotalElement) subtotalElement.textContent = `S/ ${this.total.toFixed(2)}`;
        if (totalElement) totalElement.textContent = `S/ ${this.total.toFixed(2)}`;
    }

    updateComprobanteNumber() {
        const tipoComprobante = document.getElementById('tipo-comprobante')?.value;
        const numeroComprobante = document.getElementById('numero-comprobante');
        
        if (!tipoComprobante || !numeroComprobante) return;
        
        const nextNumber = (this.lastComprobanteNumbers[tipoComprobante] || 0) + 1;
        const prefix = tipoComprobante === 'boleta' ? 'B' : 'F';
        numeroComprobante.value = `${prefix}-${String(nextNumber).padStart(8, '0')}`;
    }
    updateBtnCobrarState() {
        const btnCobrar = document.getElementById('btn-cobrar');
        if (btnCobrar) {
            const disabled = !this.selectedClient || this.items.size === 0;
            btnCobrar.disabled = disabled;
            btnCobrar.className = `flex-1 py-3 rounded-lg transition-colors inline-flex items-center justify-center ${
                disabled 
                ? 'bg-gray-400 cursor-not-allowed' 
                : 'bg-green-600 hover:bg-green-700'
            } text-white`;
        }
    }

    async procesarVenta() {
        try {
            // 1. Validaciones iniciales
            if (!this.selectedClient || this.items.size === 0) {
                this.showError('Seleccione un cliente y agregue productos al carrito');
                return;
            }
    
            if (!id_usuario) {
                this.showError('Usuario no autenticado');
                return;
            }
    
            const tipoComprobante = document.getElementById('tipo-comprobante')?.value;
            const numeroComprobante = document.getElementById('numero-comprobante')?.value;
    
            if (!tipoComprobante || !numeroComprobante) {
                this.showError('Datos de comprobante inválidos');
                return;
            }
    
            // 2. Obtener métodos de pago desde la API
            const metodoPagoResponse = await fetch(`${BASE_URL}getMetodoPago`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
    
            if (!metodoPagoResponse.ok) {
                throw new Error('Error al obtener métodos de pago');
            }
    
            const metodosPago = await metodoPagoResponse.json();
            
            // 3. Generar opciones para el select de métodos de pago
            const metodoPagoOptions = metodosPago
                .filter(metodo => metodo.is_active === '1')
                .map(metodo => `
                    <option value="${metodo.id_metodo_pago}">
                        ${metodo.nombre}
                    </option>
                `).join('');
    
            // 4. Mostrar modal de pago
            const { value: formValues, isConfirmed } = await Swal.fire({
                title: 'Procesar Pago',
                html: `
                    <div class="space-y-4">
                        <div class="text-left">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Método de Pago</label>
                            <select id="metodo_pago_id" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                ${metodoPagoOptions}
                            </select>
                            <p id="instrucciones" class="mt-1 text-sm text-gray-500 italic"></p>
                        </div>
                        <div class="text-left">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Total a Pagar</label>
                            <input type="text" id="total_pagar" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-100" 
                                   value="S/ ${this.total.toFixed(2)}" readonly>
                        </div>
                        <div id="efectivo-fields" style="display: none">
                            <div class="text-left">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Monto Recibido</label>
                                <input type="number" id="monto_recibido" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                                       step="0.10" min="${this.total}">
                            </div>
                            <div class="text-left">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Vuelto</label>
                                <input type="text" id="vuelto" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-100" 
                                       value="S/ 0.00" readonly>
                            </div>
                        </div>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Procesar Pago',
                cancelButtonText: 'Cancelar',
                allowOutsideClick: false,
                didOpen: () => {
                    const montoRecibido = document.getElementById('monto_recibido');
                    const vuelto = document.getElementById('vuelto');
                    const metodoPago = document.getElementById('metodo_pago_id');
                    const efectivoFields = document.getElementById('efectivo-fields');
                    const instrucciones = document.getElementById('instrucciones');
    
                    const updateVuelto = () => {
                        const monto = parseFloat(montoRecibido.value) || 0;
                        const cambio = monto - this.total;
                        vuelto.value = `S/ ${Math.max(0, cambio).toFixed(2)}`;
                    };
    
                    const updateInstrucciones = () => {
                        const metodoPagoSeleccionado = metodosPago.find(m => m.id_metodo_pago === metodoPago.value);
                        instrucciones.textContent = metodoPagoSeleccionado?.instrucciones || '';
                        instrucciones.style.display = metodoPagoSeleccionado?.instrucciones ? 'block' : 'none';
                    };
    
                    montoRecibido.addEventListener('input', updateVuelto);
    
                    metodoPago.addEventListener('change', (e) => {
                        const metodoPagoSeleccionado = metodosPago.find(m => m.id_metodo_pago === e.target.value);
                        const esEfectivo = metodoPagoSeleccionado?.nombre.toLowerCase().includes('efectivo');
                        efectivoFields.style.display = esEfectivo ? 'block' : 'none';
                        
                        if (!esEfectivo) {
                            montoRecibido.value = this.total;
                            vuelto.value = 'S/ 0.00';
                        }
                        
                        updateInstrucciones();
                    });
    
                    // Inicializar valores
                    updateVuelto();
                    updateInstrucciones();
                },
                preConfirm: () => {
                    const metodoPagoId = document.getElementById('metodo_pago_id').value;
                    const montoRecibido = parseFloat(document.getElementById('monto_recibido').value);
    
                    if (isNaN(montoRecibido) || montoRecibido < this.total) {
                        Swal.showValidationMessage('El monto recibido debe ser mayor o igual al total');
                        return false;
                    }
    
                    return {
                        metodo_pago_id: metodoPagoId,
                        monto_recibido: montoRecibido
                    };
                }
            });
    
            // 5. Verificar si el usuario canceló el modal
            if (!isConfirmed) {
                return;
            }
    
            // 6. Preparar datos de la venta
            const ventaData = {
                id_cliente: parseInt(this.selectedClient.id),
                id_usuario: parseInt(id_usuario),
                metodo_pago_id: parseInt(formValues.metodo_pago_id),
                tipo_comprobante: tipoComprobante,
                numero_comprobante: numeroComprobante,
                total: parseFloat(this.total),
                monto_recibido: formValues.monto_recibido,
                detalles: Array.from(this.items.values()).map(item => ({
                    id_producto: parseInt(item.id),
                    cantidad: parseInt(item.quantity),
                    precio_unitario: parseFloat(item.price),
                    subtotal: parseFloat(item.subtotal)
                }))
            };
    
            // 7. Enviar venta al servidor
            const response = await fetch(`${BASE_URL}ventas/store`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(ventaData)
            });
    
            const result = await response.json();
    
            if (!response.ok) {
                throw new Error(result.message || 'Error al procesar la venta');
            }
    
            // 8. Actualizar UI después de venta exitosa
            this.lastComprobanteNumbers[tipoComprobante]++;
    
            // 9. Preguntar por impresión
            const printConfirm = await Swal.fire({
                icon: 'success',
                title: '¡Venta exitosa!',
                text: '¿Desea imprimir el comprobante?',
                showCancelButton: true,
                confirmButtonText: 'Sí, imprimir',
                cancelButtonText: 'No, gracias'
            });
    
            if (printConfirm.isConfirmed) {
                this.imprimirBoleta();
            }
    
            // 10. Limpiar el carrito y actualizar la UI
            this.items.clear();
            this.updateCart();
            this.updateBtnCobrarState();
            this.updateComprobanteNumber();
            this.selectedClient = null;
            document.getElementById('selected-client').innerHTML = `
                <div class="flex items-center text-gray-600 dark:text-gray-300">
                    <i class="fas fa-user mr-2"></i>
                    <span>No hay cliente seleccionado</span>
                </div>
            `;
    
        } catch (error) {
            console.error('Error:', error);
            this.showError(error.message || 'Error al procesar la venta');
        }
    }

    imprimirBoleta() {
        try {
            const ventaData = {
                comprobante: document.getElementById('numero-comprobante')?.value,
                fecha: new Date().toLocaleString('es-PE', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                }),
                cliente: this.selectedClient,
                id_usuario: `${id_usuario}`,
                items: Array.from(this.items.values()),
                total: this.total
            };
    
            const printWindow = window.open('', '_blank');
            if (!printWindow) {
                this.showError('Error al abrir ventana de impresión');
                return;
            }
    
            printWindow.document.write(`
               <!DOCTYPE html>
            <html lang="es">
            <head>
                <meta charset="UTF-8">
                    <title>Comprobante de Venta - ${ventaData.comprobante}</title>
                <style>
                    @page { 
                        size: 80mm 297mm;
                        margin: 0;
                    }
                    @media print {
                        body {
                            width: 80mm;
                        }
                    }
                    body {
                        font-family: Arial, sans-serif;
                        margin: 0;
                        padding: 10px;
                        font-size: 12px;
                        line-height: 1.4;
                    }
                    .header {
                        text-align: center;
                        margin-bottom: 20px;
                        padding-bottom: 10px;
                        border-bottom: 1px dashed #000;
                    }
                    .logo {
                        max-width: 150px;
                        margin-bottom: 10px;
                    }
                    .company-info {
                        margin-bottom: 10px;
                    }
                    .title {
                        font-size: 14px;
                        font-weight: bold;
                        margin: 10px 0;
                    }
                    .info-row {
                        margin: 5px 0;
                    }
                    .table {
                        width: 100%;
                        margin: 10px 0;
                        border-collapse: collapse;
                    }
                    .table th, .table td {
                        text-align: left;
                        padding: 5px;
                    }
                    .table th {
                        border-top: 1px solid #000;
                        border-bottom: 1px solid #000;
                    }
                    .total-row {
                        font-weight: bold;
                        border-top: 1px solid #000;
                    }
                    .footer {
                        text-align: center;
                        margin-top: 20px;
                        padding-top: 10px;
                        border-top: 1px dashed #000;
                    }
                    .text-right {
                        text-align: right;
                    }
                    .text-center {
                        text-align: center;
                    }
                    .bold {
                        font-weight: bold;
                    }
                </style>
            </head>
                <body>
                    <div class="header">
                        <img src="${BASE_URL}img/logo.jpeg" alt="Logo" class="logo">
                        <div class="company-info">
                            <div class="bold">MINIMARKET SISTEMA</div>
                            <div>RUC: 20123456789</div>
                            <div>Dirección: Av. Example 123</div>
                            <div>Teléfono: (01) 123-4567</div>
                        </div>
                        <div class="title">
                            COMPROBANTE DE VENTA
                            <br>
                            ${ventaData.comprobante}
                        </div>
                    </div>
    
                    <div class="info-row">
                        <div><strong>Fecha de Emisión:</strong> ${ventaData.fecha}</div>
                        ${ventaData.cliente ? `
                            <div><strong>Cliente:</strong> ${ventaData.cliente.nombre} ${ventaData.cliente.apellido}</div>
                            <div><strong>DNI:</strong> ${ventaData.cliente.dni || 'Sin DNI'}</div>
                        ` : '<div><strong>Cliente:</strong> Cliente General</div>'}
                    </div>
    
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Descripción</th>
                                <th class="text-right">Cant.</th>
                                <th class="text-right">P.Unit</th>
                                <th class="text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${ventaData.items.map(item => `
                                <tr>
                                    <td>${item.name}</td>
                                    <td class="text-right">${item.quantity}</td>
                                    <td class="text-right">S/ ${item.price.toFixed(2)}</td>
                                    <td class="text-right">S/ ${item.subtotal.toFixed(2)}</td>
                                </tr>
                            `).join('')}
                            <tr class="total-row">
                                <td colspan="3" class="text-right bold">Total:</td>
                                <td class="text-right bold">S/ ${ventaData.total.toFixed(2)}</td>
                            </tr>
                        </tbody>
                    </table>
    
                    <div class="footer">
                        <p>¡Gracias por su compra!</p>
                        <p>Este documento es un comprobante válido de su transacción</p>
                        <p>Conserve este documento para cualquier reclamo</p>
                        <br>
                        <p>${new Date().toLocaleString()}</p>
                    </div>
    
                    <script>
                        window.onload = function() {
                            window.print();
                            // Opcional: Cerrar la ventana después de imprimir
                            // window.onafterprint = function() {
                            //     window.close();
                            // };
                        }
                    </script>
                </body>
                </html>
            `);
            printWindow.document.close();
    
        } catch (error) {
            console.error('Error al imprimir:', error);
            this.showError('Error al generar el comprobante: ' + error.message);
        }
    }

    showSuccess(message) {
        Swal.fire({
            icon: 'success',
            title: 'Éxito',
            text: message,
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
    }

    showError(message) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: message,
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
    }
}


// ----------------------------------------------
// SECCIÓN DE PRODUCTOS: Filtrado y Paginación
// ----------------------------------------------
const productGrid = document.getElementById('product-grid');
const searchInput = document.getElementById('search-input');
const categoryFilter = document.getElementById('category-filter');
const minPriceInput = document.getElementById('min-price');
const maxPriceInput = document.getElementById('max-price');
const noProductsMessage = document.getElementById('no-products-message');
const resetFiltersBtn = document.getElementById('reset-filters');
const prevPageBtn = document.getElementById('prev-page');
const nextPageBtn = document.getElementById('next-page');
const pageNumbersContainer = document.getElementById('page-numbers');
const itemsPerPage = 10;
let currentPage = 1;

// Obtener todos los productos inicialmente y guardarlos
const allProductCards = Array.from(document.querySelectorAll('.product-card'));
const originalProducts = allProductCards.map(card => ({
    element: card,
    name: card.querySelector('h2').textContent.toLowerCase(),
    description: card.querySelector('p').textContent.toLowerCase(),
    price: parseFloat(card.dataset.price),
    category: card.dataset.category
}));

// Función de filtrado de productos
const filterProducts = () => {
    const searchTerm = searchInput.value.toLowerCase();
    const selectedCategory = categoryFilter.value;
    const minPrice = parseFloat(minPriceInput.value) || 0;
    const maxPrice = parseFloat(maxPriceInput.value) || Infinity;

    const filteredProducts = originalProducts.filter(product => {
        return (
            (searchTerm === '' || 
             product.name.includes(searchTerm) || 
             product.description.includes(searchTerm)) &&
            (selectedCategory === '' || product.category === selectedCategory) &&
            (product.price >= minPrice && product.price <= maxPrice)
        );
    });

    // Resetear página actual solo si los filtros han cambiado
    const totalPages = Math.ceil(filteredProducts.length / itemsPerPage);
    if (currentPage > totalPages) {
        currentPage = 1;
    }

    paginateProducts(filteredProducts);
    return filteredProducts;
};

// Paginación de productos
const paginateProducts = (products) => {
    // Mostrar/ocultar mensaje de no productos
    if (noProductsMessage) {
        noProductsMessage.classList.toggle('hidden', products.length > 0);
    }

    // Limpiar grid
    productGrid.innerHTML = '';

    // Calcular índices para la página actual
    const startIndex = (currentPage - 1) * itemsPerPage;
    const endIndex = Math.min(startIndex + itemsPerPage, products.length);

    // Mostrar productos de la página actual
    for (let i = startIndex; i < endIndex; i++) {
        productGrid.appendChild(products[i].element.cloneNode(true));
    }

    // Actualizar controles de paginación
    updatePaginationControls(products.length);

    // Reactivar los botones de "Añadir al carrito" para los productos clonados
    reactivateAddToCartButtons();
};

// Actualizar controles de paginación
const updatePaginationControls = (totalProducts) => {
    const totalPages = Math.ceil(totalProducts / itemsPerPage);
    pageNumbersContainer.innerHTML = '';

    // Determinar rango de páginas a mostrar
    let startPage = Math.max(1, currentPage - 2);
    let endPage = Math.min(totalPages, startPage + 4);
    
    // Ajustar el rango si estamos cerca del final
    if (endPage - startPage < 4) {
        startPage = Math.max(1, endPage - 4);
    }

    // Añadir primera página si no está en el rango
    if (startPage > 1) {
        addPageButton(1);
        if (startPage > 2) {
            pageNumbersContainer.appendChild(createEllipsis());
        }
    }

    // Añadir páginas numeradas
    for (let i = startPage; i <= endPage; i++) {
        addPageButton(i);
    }

    // Añadir última página si no está en el rango
    if (endPage < totalPages) {
        if (endPage < totalPages - 1) {
            pageNumbersContainer.appendChild(createEllipsis());
        }
        addPageButton(totalPages);
    }

    // Actualizar estado de botones de navegación
    prevPageBtn.disabled = currentPage === 1;
    nextPageBtn.disabled = currentPage === totalPages;
};

// Función auxiliar para crear botón de página
const addPageButton = (pageNum) => {
    const button = document.createElement('button');
    button.textContent = pageNum;
    button.classList.add('px-3', 'py-1', 'rounded-lg');
    
    if (pageNum === currentPage) {
        button.classList.add('bg-blue-500', 'text-white');
    } else {
        button.classList.add('bg-gray-200', 'text-gray-700', 'hover:bg-gray-300');
    }

    button.addEventListener('click', () => {
        currentPage = pageNum;
        filterProducts();
    });
    
    pageNumbersContainer.appendChild(button);
};

// Función para crear ellipsis
const createEllipsis = () => {
    const span = document.createElement('span');
    span.textContent = '...';
    span.classList.add('px-2');
    return span;
};

// Modificar la función reactivateAddToCartButtons para usar el carrito
const reactivateAddToCartButtons = () => {
    document.querySelectorAll('.add-to-cart-btn').forEach(button => {
        button.addEventListener('click', (e) => {
            const target = e.target.closest('.add-to-cart-btn');
            const productId = target.dataset.productId;
            const productName = target.dataset.productName;
            const productPrice = target.dataset.productPrice;
            
            cart.addItem(productId, productName, productPrice);
        });
    });
};
// Eventos de navegación
prevPageBtn.addEventListener('click', () => {
    if (currentPage > 1) {
        currentPage--;
        filterProducts();
    }
});

nextPageBtn.addEventListener('click', () => {
    const totalPages = Math.ceil(originalProducts.length / itemsPerPage);
    if (currentPage < totalPages) {
        currentPage++;
        filterProducts();
    }
});

// Restablecer filtros
resetFiltersBtn.addEventListener('click', () => {
    searchInput.value = '';
    categoryFilter.value = '';
    minPriceInput.value = '';
    maxPriceInput.value = '';
    currentPage = 1;
    filterProducts();
});

// Eventos de filtrado
searchInput.addEventListener('input', () => {
    currentPage = 1;
    filterProducts();
});

categoryFilter.addEventListener('change', () => {
    currentPage = 1;
    filterProducts();
});

minPriceInput.addEventListener('input', () => {
    currentPage = 1;
    filterProducts();
});

maxPriceInput.addEventListener('input', () => {
    currentPage = 1;
    filterProducts();
});

// Inicializar paginación
filterProducts();
// Inicializar el carrito
window.cart = new ShoppingCart();