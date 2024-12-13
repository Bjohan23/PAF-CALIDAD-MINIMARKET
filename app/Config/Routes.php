<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Ruta principal - Página de inicio de la tienda
$routes->get('/', 'Tienda::index');

// =====================================
// Rutas de Autenticación del Cliente
// =====================================
$routes->get('login', 'Tienda::login');                    // Mostrar formulario de login
$routes->post('login', 'Tienda::login');   // Procesar el login
$routes->get('registro', 'Tienda::registro');              // Mostrar formulario de registro
$routes->post('registro', 'Tienda::registro');             // Procesar el registro
$routes->get('recuperar-password', 'Tienda::recuperarPassword');  // Formulario recuperación contraseña
$routes->post('recuperar-password/procesar', 'Tienda::procesarRecuperacion'); // Procesar recuperación
$routes->get('logout', 'Tienda::logout');                  // Cerrar sesión del cliente
$routes->post('metodo-pago/create', 'MetodoPago::create');

$routes->get('ventas/last-comprobante-numbers', 'Ventas::getLastComprobanteNumbers'); // Obtener números de comprobante
$routes->post('ventas/store', 'Ventas::store');            // Guardar venta
$routes->get('ventas/getDetalle/(:num)', 'Ventas::getDetalle/$1'); // Ver detalle venta
$routes->delete('ventas/delete/(:num)', 'Ventas::delete/$1'); // Eliminar venta
$routes->get('getMetodoPago', 'MetodoPago::getMetodoPago'); // Ver metodos de pagos
// =====================================
// Rutas del Panel Administrativo
// =====================================
$routes->group('admin', function ($routes) {
    // Autenticación y Dashboard Admin
    $routes->get('/', 'Home::index');                           // Panel principal admin
    $routes->get('home', 'Home::index');                        // Alias del panel
    $routes->get('login', 'Login::index');                      // Login admin
    $routes->post('login/authenticate', 'Login::authenticate');  // Procesar login admin
    $routes->post('login/create', 'Login::create');  // Procesar login admin
    $routes->get('logout', 'Login::logout');                    // Logout admin
    $routes->get('account', 'Account::index');                  // Perfil admin

    // Gestión de Productos
    $routes->get('productos', 'Productos::index');              // Listar productos
    $routes->post('productos/store', 'Productos::store');       // Crear producto
    $routes->get('productos/edit/(:num)', 'Productos::edit/$1'); // Editar producto
    $routes->post('productos/update/(:num)', 'Productos::update/$1'); // Guardar edición
    $routes->delete('productos/delete/(:num)', 'Productos::delete/$1'); // Eliminar producto

    // Gestión de Categorías
    $routes->get('categorias', 'Categorias::index');           // Listar categorías
    $routes->post('categorias/store', 'Categorias::store');    // Crear categoría
    $routes->get('categorias/edit/(:num)', 'Categorias::edit/$1'); // Editar categoría
    $routes->post('categorias/update/(:num)', 'Categorias::update/$1'); // Guardar edición
    $routes->get('categorias/delete/(:num)', 'Categorias::delete/$1'); // Eliminar categoría

    // Gestión de Ventas
    $routes->get('ventas', 'Ventas::index');                   // Listar ventas
    $routes->get('ventas/create', 'Ventas::create');           // Nueva venta
    // $routes->post('ventas/store', 'Ventas::store');            // Guardar venta
    $routes->get('ventas/edit/(:num)', 'Ventas::edit/$1');     // Editar venta
    $routes->post('ventas/update/(:num)', 'Ventas::update/$1'); // Guardar edición



    // Gestión de Clientes
    $routes->get('clientes', 'Clientes::index');               // Listar clientes
    $routes->post('clientes/store', 'Clientes::store');        // Crear cliente
    $routes->get('clientes/edit/(:num)', 'Clientes::edit/$1'); // Editar cliente
    $routes->post('clientes/update/(:num)', 'Clientes::update/$1'); // Guardar edición
    $routes->get('clientes/delete/(:num)', 'Clientes::delete/$1'); // Eliminar cliente
    $routes->get('clientes/reactivar/(:num)', 'Clientes::reactivar/$1');

    // Gestión de Usuarios Admin
    $routes->get('administrador', 'Administrador::index');      // Listar admins
    $routes->post('administrador/store', 'Administrador::store'); // Crear admin
    $routes->get('administrador/edit/(:num)', 'Administrador::edit/$1'); // Editar admin
    $routes->post('administrador/update/(:num)', 'Administrador::update/$1'); // Guardar edición
    $routes->get('administrador/delete/(:num)', 'Administrador::delete/$1'); // Eliminar admin
});

// =====================================
// Rutas de la Tienda (Frontend)
// =====================================
$routes->group('tienda', ['namespace' => 'App\Controllers'], function ($routes) {
    // Catálogo y Productos
    $routes->get('productos', 'Tienda::productos');            // Listado de productos
    $routes->get('producto/(:num)', 'Tienda::producto/$1');    // Ver producto individual
    $routes->get('categoria/(:num)', 'Tienda::categoria/$1');  // Productos por categoría
    $routes->get('buscar', 'Tienda::buscar');                  // Búsqueda de productos

    // Carrito de Compras
    $routes->get('carrito', 'Tienda::carrito');                // Ver carrito
    $routes->post('carrito/agregar', 'Tienda::agregarCarrito'); // Agregar producto
    $routes->post('carrito/actualizar', 'Tienda::actualizarCarrito'); // Actualizar cantidad
    $routes->delete('carrito/eliminar/(:num)', 'Tienda::eliminarCarrito/$1'); // Quitar producto

    // Proceso de Compra
    $routes->get('checkout', 'Tienda::checkout');              // Página de checkout
    $routes->post('checkout/procesar', 'Tienda::procesarCheckout'); // Procesar compra
    $routes->get('checkout/confirmar/(:any)', 'Tienda::confirmarCheckout/$1'); // Confirmación
});

// =====================================
// Área Privada del Cliente
// =====================================
$routes->group('mi-cuenta', ['filter' => 'authCliente'], function ($routes) {
    $routes->get('/', 'Tienda::miCuenta');                     // Dashboard cliente
    $routes->get('pedidos', 'Tienda::misPedidos');            // Ver pedidos
    $routes->get('pedido/(:num)', 'Tienda::detallePedido/$1'); // Detalle pedido
    $routes->get('perfil', 'Tienda::perfil');                  // Ver perfil
    $routes->post('perfil/actualizar', 'Tienda::actualizarPerfil'); // Actualizar perfil
    $routes->get('direcciones', 'Tienda::direcciones');        // Ver direcciones
    $routes->post('direcciones/agregar', 'Tienda::agregarDireccion'); // Agregar dirección
    $routes->delete('direcciones/eliminar/(:num)', 'Tienda::eliminarDireccion/$1'); // Eliminar dirección
});
