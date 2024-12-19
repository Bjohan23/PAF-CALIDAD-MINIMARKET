// Primero, creamos una función para manejar el cierre del modal con Escape
function setupModalKeyboardEvents(modalId) {
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            const modal = document.getElementById(modalId);
            if (!modal.classList.contains('hidden')) {
                cerrarModal(modalId);
            }
        }
    });
}

// Función para manejar el focus trap dentro del modal
function setupFocusTrap(modalId) {
    const modal = document.getElementById(modalId);
    const focusableElements = modal.querySelectorAll(
        'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
    );
    const firstFocusableElement = focusableElements[0];
    const lastFocusableElement = focusableElements[focusableElements.length - 1];

    modal.addEventListener('keydown', (e) => {
        if (e.key === 'Tab') {
            if (e.shiftKey) {
                if (document.activeElement === firstFocusableElement) {
                    lastFocusableElement.focus();
                    e.preventDefault();
                }
            } else {
                if (document.activeElement === lastFocusableElement) {
                    firstFocusableElement.focus();
                    e.preventDefault();
                }
            }
        }
    });
}

// Función para cerrar el modal
function cerrarModal(modalId) {
    const modal = document.getElementById(modalId);
    modal.classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
    // Restaurar el foco al elemento que abrió el modal
    if (modal.previousActiveElement) {
        modal.previousActiveElement.focus();
    }
}

async function verDetalleVenta(idVenta) {
    try {
        const response = await fetch(`${BASE_URL}ventas/getDetalle/${idVenta}`);
        const data = await response.json();

        if (!data.success) {
            throw new Error(data.message);
        }

        const { venta, detalles } = data;

        // Creamos un div contenedor dedicado para el modal si no existe
        let modalContainer = document.getElementById('modalContainer');
        if (!modalContainer) {
            modalContainer = document.createElement('div');
            modalContainer.id = 'modalContainer';
            document.body.appendChild(modalContainer);
        }

        const formatValue = (value, defaultValue = 'No registrado') => {
            return value && value !== 'null' ? value : defaultValue;
        };

        let html = `
        <div class="fixed inset-0 z-[60] overflow-hidden"
             role="dialog"
             aria-modal="true"
             aria-labelledby="modal-title">
            <!-- Backdrop con z-index alto -->
            <div class="fixed inset-0 bg-black/70 backdrop-blur-sm transition-opacity"></div>
            
            <!-- Container del modal con z-index más alto -->
            <div class="fixed inset-0 z-[70] overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4">
                    <div class="relative w-full max-w-4xl bg-white dark:bg-gray-800 rounded-xl shadow-2xl transform transition-all
                               mx-auto animate-modal-show">
                        <!-- Header fijo -->
                        <div class="sticky top-0 z-10 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-6 py-4">
                            <div class="flex justify-between items-center">
                                <h2 id="modal-title" class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                                    <span>${venta.tipo_comprobante.toUpperCase()} ${venta.numero_comprobante}</span>
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                        bg-${venta.estado_badge.color}-100 
                                        text-${venta.estado_badge.color}-800
                                        dark:bg-${venta.estado_badge.color}-900/50
                                        dark:text-${venta.estado_badge.color}-400"
                                          role="status">
                                        ${venta.estado_badge.text}
                                    </span>
                                </h2>
                                <button onclick="cerrarModal()"
                                        class="rounded-lg p-2 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        aria-label="Cerrar modal">
                                    <i class="fas fa-times text-xl text-gray-500 dark:text-gray-400" aria-hidden="true"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Contenido principal con scroll interno -->
                        <div class="max-h-[calc(100vh-8rem)] overflow-y-auto">
                            <div class="p-6">
                                <!-- Grid responsivo para información -->
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                                    <!-- Información del Cliente -->
                                    <section aria-labelledby="cliente-title" class="bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600 p-4">
                                        <h3 id="cliente-title" class="text-lg font-semibold mb-4 text-gray-900 dark:text-white flex items-center gap-2">
                                            <i class="fas fa-user text-blue-600 dark:text-blue-400" aria-hidden="true"></i>
                                            Información del Cliente
                                        </h3>
                                        <dl class="space-y-3">
                                            <div class="flex flex-col sm:flex-row sm:items-center gap-1">
                                                <dt class="font-medium text-gray-700 dark:text-gray-300 min-w-[100px]">Nombre:</dt>
                                                <dd class="text-gray-600 dark:text-gray-400">${formatValue(venta.cliente_nombre_completo)}</dd>
                                            </div>
                                            <div class="flex flex-col sm:flex-row sm:items-center gap-1">
                                                <dt class="font-medium text-gray-700 dark:text-gray-300 min-w-[100px]">DNI:</dt>
                                                <dd class="text-gray-600 dark:text-gray-400">${formatValue(venta.cliente_dni)}</dd>
                                            </div>
                                            <div class="flex flex-col sm:flex-row sm:items-center gap-1">
                                                <dt class="font-medium text-gray-700 dark:text-gray-300 min-w-[100px]">Teléfono:</dt>
                                                <dd class="text-gray-600 dark:text-gray-400">${formatValue(venta.cliente_telefono)}</dd>
                                            </div>
                                            <div class="flex flex-col sm:flex-row sm:items-center gap-1">
                                                <dt class="font-medium text-gray-700 dark:text-gray-300 min-w-[100px]">Email:</dt>
                                                <dd class="text-gray-600 dark:text-gray-400">${formatValue(venta.cliente_email)}</dd>
                                            </div>
                                        </dl>
                                    </section>

                                    <!-- Información de la Venta -->
                                    <section aria-labelledby="venta-title" class="bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600 p-4">
                                        <h3 id="venta-title" class="text-lg font-semibold mb-4 text-gray-900 dark:text-white flex items-center gap-2">
                                            <i class="fas fa-file-invoice text-green-600 dark:text-green-400" aria-hidden="true"></i>
                                            Detalles de la Venta
                                        </h3>
                                        <dl class="space-y-3">
                                            <div class="flex flex-col sm:flex-row sm:items-center gap-1">
                                                <dt class="font-medium text-gray-700 dark:text-gray-300 min-w-[100px]">Fecha:</dt>
                                                <dd class="text-gray-600 dark:text-gray-400">${formatValue(venta.fecha_formateada)}</dd>
                                            </div>
                                            <div class="flex flex-col sm:flex-row sm:items-center gap-1">
                                                <dt class="font-medium text-gray-700 dark:text-gray-300 min-w-[100px]">Vendedor:</dt>
                                                <dd class="text-gray-600 dark:text-gray-400">${formatValue(venta.usuario_nombre_completo)}</dd>
                                            </div>
                                            <div class="flex flex-col sm:flex-row sm:items-center gap-1">
                                                <dt class="font-medium text-gray-700 dark:text-gray-300 min-w-[100px]">Total:</dt>
                                                <dd class="text-blue-600 dark:text-blue-400 font-medium">${formatValue(venta.total_formateado)}</dd>
                                            </div>
                                        </dl>
                                    </section>
                                </div>

                                <!-- Tabla de Productos -->
                                <section aria-labelledby="productos-title" class="mt-6">
                                    <h3 id="productos-title" class="text-lg font-semibold mb-4 text-gray-900 dark:text-white flex items-center gap-2">
                                        <i class="fas fa-shopping-cart text-purple-600 dark:text-purple-400" aria-hidden="true"></i>
                                        Productos
                                    </h3>
                                    ${detalles && detalles.length > 0 ? `
                                        <div class="overflow-x-auto -mx-6">
                                            <div class="inline-block min-w-full align-middle px-6">
                                                <div class="overflow-hidden border border-gray-200 dark:border-gray-700 sm:rounded-lg">
                                                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700" role="table">
                                                        <thead class="bg-gray-50 dark:bg-gray-700">
                                                            <tr>
                                                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                                                    Producto
                                                                </th>
                                                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                                                    Categoría
                                                                </th>
                                                                <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                                                    Cantidad
                                                                </th>
                                                                <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                                                    Precio Unit.
                                                                </th>
                                                                <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                                                    Subtotal
                                                                </th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                                            ${detalles.map(detalle => `
                                                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                                                    <td class="px-4 py-3 whitespace-nowrap">
                                                                        <div class="text-sm font-medium text-gray-900 dark:text-gray-300">
                                                                            ${formatValue(detalle.producto_nombre)}
                                                                        </div>
                                                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                                                            ${formatValue(detalle.codigo_barras, 'Sin código')}
                                                                        </div>
                                                                    </td>
                                                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                                                                        ${formatValue(detalle.categoria_nombre)}
                                                                    </td>
                                                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-right text-gray-900 dark:text-gray-300">
                                                                        ${detalle.cantidad}
                                                                    </td>
                                                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-right text-gray-900 dark:text-gray-300">
                                                                        ${detalle.precio_unitario_formateado}
                                                                    </td>
                                                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-right text-gray-900 dark:text-gray-300">
                                                                        ${detalle.subtotal_formateado}
                                                                    </td>
                                                                </tr>
                                                            `).join('')}
                                                        </tbody>
                                                        <tfoot class="bg-gray-50 dark:bg-gray-700">
                                                            <tr>
                                                                <td colspan="4" class="px-4 py-3 text-right text-sm font-medium text-gray-900 dark:text-gray-300">
                                                                    Total:
                                                                </td>
                                                                <td class="px-4 py-3 text-right text-sm font-bold text-blue-600 dark:text-blue-400">
                                                                    ${venta.total_formateado}
                                                                </td>
                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    ` : `
                                        <div class="text-center py-8 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600">
                                            <i class="fas fa-box-open text-4xl text-gray-400 dark:text-gray-500 mb-3" aria-hidden="true"></i>
                                            <p class="text-gray-500 dark:text-gray-400" role="alert">
                                                No hay productos registrados para esta venta
                                            </p>
                                        </div>
                                    `}
                                </section>

                                <!-- Botones de Acción -->
                                <div class="mt-6 flex flex-wrap gap-3 justify-end" role="toolbar" aria-label="Acciones de venta">
                                    <button onclick="imprimirComprobante(${venta.id_venta})" 
                                            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition-colors"
                                            aria-label="Imprimir comprobante">
                                        <i class="fas fa-print mr-2" aria-hidden="true"></i>
                                        Imprimir
                                    </button>
                                    ${venta.estado === 'completada' ? `
                                        <button onclick="anularVenta(${venta.id_venta})" 
                                                class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition-colors"
                                                aria-label="Anular venta">
                                            <i class="fas fa-ban mr-2" aria-hidden="true"></i>
                                            Anular
                                        </button>
                                    ` : ''}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        `;

        // Insertamos el modal en el contenedor dedicado
        modalContainer.innerHTML = html;

        // Agregamos estilos globales si no existen
        if (!document.getElementById('modal-styles')) {
            const styles = document.createElement('style');
            styles.id = 'modal-styles';
            styles.textContent = `
                #modalContainer {
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100vw;
                    height: 100vh;
                    z-index: 9999; /* Z-index muy alto para estar sobre todo */
                    pointer-events: none; /* Permitir clicks en elementos debajo cuando no hay modal */
                }
                
                #modalContainer > div {
                    pointer-events: auto; /* Restaurar interacción para el modal */
                }

                .modal-open {
                    overflow: hidden !important;
                }

                @keyframes modalShow {
                    from {
                        opacity: 0;
                        transform: scale(0.95);
                    }
                    to {
                        opacity: 1;
                        transform: scale(1);
                    }
                }
                
                .animate-modal-show {
                    animation: modalShow 0.3s ease-out;
                }
            `;
            document.head.appendChild(styles);
        }

        // Bloquear scroll del body
        document.body.classList.add('modal-open');

        // Función para cerrar el modal
        window.cerrarModal = () => {
            modalContainer.innerHTML = '';
            document.body.classList.remove('modal-open');
            document.removeEventListener('keydown', handleEscape);
        };

        // Manejador para la tecla Escape
        const handleEscape = (e) => {
            if (e.key === 'Escape') {
                cerrarModal();
            }
        };
        document.addEventListener('keydown', handleEscape);

        // Configurar click fuera del modal para cerrar
        modalContainer.addEventListener('click', (e) => {
            if (e.target === modalContainer.firstElementChild) {
                cerrarModal();
            }
        });

    } catch (error) {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: error.message || 'Error al cargar los detalles de la venta',
            customClass: {
                container: 'swal2-container-high-z-index'
            }
        });
    }
}

// Agregar estilos para Sweet Alert si no existen
if (!document.getElementById('swal-styles')) {
    const swalStyles = document.createElement('style');
    swalStyles.id = 'swal-styles';
    swalStyles.textContent = `
        .swal2-container-high-z-index {
            z-index: 10000 !important;
        }
    `;
    document.head.appendChild(swalStyles);
}

// Agregar estilos de animación si no existen
if (!document.querySelector('#modal-animations')) {
    const style = document.createElement('style');
    style.id = 'modal-animations';
    style.textContent = `
        @keyframes modalShow {
            from {
                opacity: 0;
                transform: scale(0.95);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }
        .animate-modal-show {
            animation: modalShow 0.3s ease-out;
        }
    `;
    document.head.appendChild(style);
}

// Función para imprimir comprobante
async function imprimirComprobante(idVenta) {
    try {
        // Obtener los datos de la venta
        const response = await fetch(`${BASE_URL}ventas/getDetalle/${idVenta}`);
        const data = await response.json();

        if (!data.success) {
            throw new Error(data.message);
        }

        const { venta, detalles } = data;

        // Crear ventana de impresión
        const printWindow = window.open('', '_blank');
        if (!printWindow) {
            throw new Error('El navegador bloqueó la ventana emergente. Por favor, permite las ventanas emergentes para imprimir.');
        }

        // Formatear fecha para el comprobante
        const fechaEmision = new Date(venta.fecha_venta).toLocaleDateString('es-PE', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });

        // Generar el HTML del comprobante
        const html = `
            <!DOCTYPE html>
            <html lang="es">
            <head>
                <meta charset="UTF-8">
                <title>Comprobante de Venta - ${venta.numero_comprobante}</title>
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
                        ${venta.tipo_comprobante.toUpperCase()} DE VENTA
                        <br>
                        ${venta.numero_comprobante}
                    </div>
                </div>

                <div class="info-row">
                    <div><strong>Fecha de Emisión:</strong> ${fechaEmision}</div>
                    ${venta.cliente_nombre ? `
                        <div><strong>Cliente:</strong> ${venta.cliente_nombre} ${venta.cliente_apellido}</div>
                        <div><strong>DNI:</strong> ${venta.cliente_dni || 'Sin DNI'}</div>
                    ` : '<div><strong>Cliente:</strong> Cliente General</div>'}
                    <div><strong>Vendedor:</strong> ${venta.usuario_nombre} ${venta.usuario_apellido}</div>
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
                        ${detalles.map(item => `
                            <tr>
                                <td>${item.producto_nombre}</td>
                                <td class="text-right">${item.cantidad}</td>
                                <td class="text-right">S/ ${parseFloat(item.precio_unitario).toFixed(2)}</td>
                                <td class="text-right">S/ ${parseFloat(item.subtotal).toFixed(2)}</td>
                            </tr>
                        `).join('')}
                        <tr class="total-row">
                            <td colspan="3" class="text-right">Total:</td>
                            <td class="text-right">S/ ${parseFloat(venta.total).toFixed(2)}</td>
                        </tr>
                    </tbody>
                </table>

                <div class="footer">
                    <p>¡Gracias por su compra!</p>
                    <p>Este documento es un comprobante válido de su transacción</p>
                    <p>Conserve este documento para cualquier reclamo</p>
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
        `;

        // Escribir el HTML en la ventana de impresión
        printWindow.document.write(html);
        printWindow.document.close();

    } catch (error) {
        console.error('Error al imprimir:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: error.message || 'Error al generar el comprobante'
        });
    }
}

// Función para anular venta
async function anularVenta(idVenta) {
    try {
        const result = await Swal.fire({
            title: '¿Estás seguro?',
            text: 'Esta acción anulará la venta y no se puede deshacer',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, anular',
            cancelButtonText: 'Cancelar'
        });

        if (result.isConfirmed) {
            try {
                // al confirmar la anulación, se envía la petición al servidor a esta ruta con el método DELETE
                const response = await fetch(`${BASE_URL}ventas/delete/${idVenta}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
        
                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(errorData.message || 'Error al anular la venta');
                }
        
                const data = await response.json();
                if (!data.success) {
                    throw new Error(data.message);
                }
        
                // Manejar la respuesta exitosa, por ejemplo, recargar la página o actualizar la lista de ventas
                Swal.fire('¡Anulado!', 'La venta ha sido anulada.', 'success');
                location.reload(); // Recargar la página para reflejar los cambios
            } catch (error) {
                console.error('Error:', error);
                Swal.fire('Error', error.message, 'error');
            }
        }
    } catch (error) {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Error al anular la venta'
        });
    }
}