document.addEventListener('DOMContentLoaded', () => {
    // Referencias DOM
    const searchInput = document.getElementById('searchInput');
    const clienteList = document.getElementById('clienteList');
    const clienteItems = document.querySelectorAll('.cliente-item');
    const showMoreBtnContainer = document.getElementById('showMoreBtnContainer');
    const showMoreBtn = document.getElementById('showMoreBtn');
    const modalCliente = document.getElementById('modalCliente');
    const clienteSelect = document.getElementById('clienteSelect');
    const closeModalClienteBtn = document.getElementById('closeModalClienteBtn');


    const ITEMS_VISIBLE_AT_START = 5;
    let visibleCount = ITEMS_VISIBLE_AT_START;

    // Función para mostrar el modal
    const showModal = () => {
        if (modalCliente) {
            modalCliente.classList.remove('hidden');
            if (searchInput) searchInput.value = '';
            showInitialClients();
        } else {
            console.error('Modal no encontrado');
        }
    };

    // Función para ocultar el modal
    const hideModal = () => {
        if (modalCliente) {
            modalCliente.classList.add('hidden');
        }
    };

    // Función para mostrar los clientes iniciales
    const showInitialClients = () => {
        clienteItems.forEach((item, index) => {
            // Remover la clase hidden en lugar de usar style.display
            if (index < ITEMS_VISIBLE_AT_START) {
                item.classList.remove('hidden');
            } else {
                item.classList.add('hidden');
            }
        });

        if (showMoreBtnContainer) {
            showMoreBtnContainer.classList.toggle('hidden', 
                clienteItems.length <= ITEMS_VISIBLE_AT_START);
        }
    };

    // Función para seleccionar cliente
    const handleClientSelection = (clienteItem) => {
        const clientData = {
            id: clienteItem.dataset.clienteId,
            nombre: clienteItem.dataset.clienteNombre,
            apellido: clienteItem.dataset.clienteApellido,
            dni: clienteItem.dataset.clienteDni
        };

        // Actualizar el botón de selección
        if (clienteSelect) {
            clienteSelect.innerHTML = `
                <i class="fas fa-user-check mr-2"></i>
                ${clientData.nombre} ${clientData.apellido}
            `;
            clienteSelect.classList.remove('bg-blue-600');
            clienteSelect.classList.add('bg-green-600');
        }

        // Actualizar el carrito
        if (window.cart instanceof ShoppingCart) {
            window.cart.setSelectedClient(clientData);
        } else {
            console.error('El carrito no está inicializado correctamente');
        }

        hideModal();

        // Mostrar notificación
        Swal.fire({
            icon: 'success',
            title: 'Cliente seleccionado',
            text: `${clientData.nombre} ${clientData.apellido}`,
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
    };

    // Event Listeners
    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            const filter = e.target.value.toLowerCase().trim();
            let visibleCount = 0;
            clienteItems.forEach(item => {
                const nombre = item.dataset.clienteNombre?.toLowerCase() || '';
                const apellido = item.dataset.clienteApellido?.toLowerCase() || '';
                const dni = item.dataset.clienteDni?.toLowerCase() || '';
                const shouldShow = nombre.includes(filter) || 
                                 apellido.includes(filter) || 
                                 dni.includes(filter);

                item.classList.toggle('hidden', !shouldShow);
                if (shouldShow) visibleCount++;
            });

            if (showMoreBtnContainer) {
                showMoreBtnContainer.classList.add('hidden');
            }
        });
    }

    if (showMoreBtn) {
        showMoreBtn.addEventListener('click', () => {
            visibleCount += ITEMS_VISIBLE_AT_START;
            clienteItems.forEach((item, index) => {
                if (index < visibleCount) {
                    item.classList.remove('hidden');
                }
            });

            if (showMoreBtnContainer) {
                showMoreBtnContainer.classList.toggle('hidden', 
                    visibleCount >= clienteItems.length);
            }
        });
    }

    // Event listener para el botón de selección de cliente
    if (clienteSelect) {
        clienteSelect.addEventListener('click', (e) => {
            e.preventDefault();
            showModal();
        });
    }

    // Event listener para cerrar el modal
    if (closeModalClienteBtn) {
        closeModalClienteBtn.addEventListener('click', (e) => {
            e.preventDefault();
            hideModal();
        });
    }

    // Cerrar modal al hacer clic fuera
    if (modalCliente) {
        modalCliente.addEventListener('click', (e) => {
            if (e.target === modalCliente) {
                hideModal();
            }
        });
    }

    // Agregar event listeners a cada item de cliente
    clienteItems.forEach(item => {
        item.addEventListener('click', () => handleClientSelection(item));
    });

    // Inicializar
    showInitialClients();
});