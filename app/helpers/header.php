<?php
// Helper para construir los elementos del header dinámicamente
// Devuelve un array con: logo, leftMenu (items), auth (login/register o user dropdown), admin (links), cart, extra
// menuIzquierdo - autenticacion - adminRapido - sidebarAdmin - misPedidos y carrito
require_once __DIR__ . '/../middleware/RoleMiddleware.php';

function getHeaderData($usuario = null, $menuItems = [])
{
    $data = [];

    // Logo
    $data['logo'] = 'img/prime.png';

    // Menu a la izquierda
    $data['leftMenu'] = $menuItems;

    // Auth area
    if (!$usuario) { // invitado
        $data['auth'] = [
            'type' => 'guest', //registrar / login
            'links' => [
                ['label' => 'Login', 'href' => '?controller=auth&action=login'],
                ['label' => 'Registrarse', 'href' => '?controller=auth&action=registro'],
            ]
        ];
    } else {
        $displayName = htmlspecialchars(ucfirst(strtolower($usuario['usnombre'])));
        $data['auth'] = [
            'type' => 'user',
            'user' => [
                'label' => $displayName,
                'dropdown' => [
                    ['label' => 'Cambiar Contrasena', 'href' => '?controller=usuario&action=cambiarContrasena', 'icon' => 'bi-key'],
                    ['label' => 'Cambiar Email', 'href' => '?controller=usuario&action=cambiarEmail', 'icon' => 'bi-envelope'],
                    ['divider' => true],
                    ['label' => 'Salir', 'href' => '?controller=auth&action=logout', 'icon' => 'bi-box-arrow-right', 'class' => 'text-danger'],
                ]
            ]
        ];
    }

    // Admin
    $data['adminQuick'] = [];
    if ($usuario && class_exists('RoleMiddleware') && RoleMiddleware::esAdmin()) {
        $data['adminQuick'][] = ['label' => 'Dashboard', 'href' => '?controller=admin&action=dashboard', 'icon' => 'bi-speedometer2'];
        $data['adminQuick'][] = ['label' => 'Usuarios', 'href' => '?controller=usuario&action=listar', 'icon' => 'bi-people'];

        // Sidebar admin (estructura para layout admin)
        $data['adminSidebar'] = [
            ['key' => 'dashboard', 'label' => 'Dashboard', 'href' => '?controller=admin&action=dashboard', 'icon' => 'bi-speedometer2'],
            ['key' => 'productos', 'label' => 'Productos', 'href' => '?controller=producto&action=listar', 'icon' => 'bi-box-seam'],
            ['key' => 'nuevo_producto', 'label' => 'Nuevo Producto', 'href' => '?controller=producto&action=crear', 'icon' => 'bi-plus-circle'],
            ['key' => 'usuarios', 'label' => 'Usuarios', 'href' => '?controller=usuario&action=listar', 'icon' => 'bi-people'],
            ['key' => 'pedidos', 'label' => 'Pedidos Pendientes', 'href' => '?controller=pedido&action=pendientes', 'icon' => 'bi-cart-check'],
            ['divider' => true],
            ['key' => 'ver_sitio', 'label' => 'Ver Sitio', 'href' => '?controller=home&action=index', 'icon' => 'bi-house'],
            ['key' => 'logout', 'label' => 'Salir', 'href' => '?controller=auth&action=logout', 'icon' => 'bi-box-arrow-right', 'class' => 'text-danger'],
        ];
    }

    // mis pedidos si es cliente
    $data['misPedidos'] = null;
    if ($usuario && class_exists('RoleMiddleware') && RoleMiddleware::esCliente()) {
        $data['misPedidos'] = ['label' => 'Mis Pedidos', 'href' => '?controller=pedido&action=misPedidos', 'icon' => 'bi-bag-check'];
    }

    // carrito (visible para usuarios también, mantengo disponible para invitados por si se desea)
    $data['cart'] = ['label' => 'Carrito', 'href' => '?controller=carrito&action=ver', 'icon' => 'bi-cart3'];

    return $data;
}

// Funcion obtener menu x defecto
function obtenerMenuPorDefecto($usuario = null)
{
    $menu = [
        ['key' => 'inicio', 'label' => 'Inicio', 'href' => '?controller=home&action=index', 'icon' => 'bi-house'],
        ['key' => 'buscar', 'label' => 'Buscar', 'href' => '?controller=producto&action=buscar', 'icon' => 'bi-search'],
    ];

    // Autenticados.
    if ($usuario) {
        $menu[] = ['key' => 'carrito', 'label' => 'Carrito', 'href' => '?controller=carrito&action=index', 'icon' => 'bi-cart', 'condition' => true];
        $menu[] = ['key' => 'mispedidos', 'label' => 'Mis Pedidos', 'href' => '?controller=pedido&action=mispedidos', 'icon' => 'bi-bag', 'condition' => true];
    }

    return $menu;
}

function obtenerDatosHeaderDesdeControlador($usuario = null, $menuIzquierdo = null)
{
    // Si no se pasan items, usar un conjunto por defecto mínimo
    if ($menuIzquierdo === null) {
        $menuIzquierdo = [
            ['key' => 'inicio', 'label' => 'Inicio', 'href' => '?controller=home&action=index', 'icon' => 'bi-house'],
        ];
    }

    $datos = [];
    $datos['logo'] = 'img/prime.png';
    $datos['menuIzquierdo'] = $menuIzquierdo;

    // autenticacion
    if (!$usuario) {
        $datos['autenticacion'] = [
            'tipo' => 'invitado',
            'links' => [
                ['label' => 'Login', 'href' => '?controller=auth&action=login'],
                ['label' => 'Registrarse', 'href' => '?controller=auth&action=registro'],
            ]
        ];
    } else {
        $nombre = htmlspecialchars(ucfirst(strtolower($usuario['usnombre'])));
        $datos['autenticacion'] = [
            'tipo' => 'usuario',
            'usuario' => [
                'label' => $nombre,
                'dropdown' => [
                    ['label' => 'Cambiar Contrasena', 'href' => '?controller=usuario&action=cambiarContrasena', 'icon' => 'bi-key'],
                    ['label' => 'Cambiar Email', 'href' => '?controller=usuario&action=cambiarEmail', 'icon' => 'bi-envelope'],
                    ['divider' => true],
                    ['label' => 'Salir', 'href' => '?controller=auth&action=logout', 'icon' => 'bi-box-arrow-right', 'class' => 'text-danger'],
                ]
            ]
        ];
    }

    $datos['adminRapido'] = [];
    if ($usuario && class_exists('RoleMiddleware') && RoleMiddleware::esAdmin()) {
        $datos['adminRapido'][] = ['label' => 'Dashboard', 'href' => '?controller=admin&action=dashboard', 'icon' => 'bi-speedometer2'];
        $datos['adminRapido'][] = ['label' => 'Usuarios', 'href' => '?controller=usuario&action=listar', 'icon' => 'bi-people'];

        $datos['sidebarAdmin'] = [
            ['key' => 'dashboard', 'label' => 'Dashboard', 'href' => '?controller=admin&action=dashboard', 'icon' => 'bi-speedometer2'],
            ['key' => 'productos', 'label' => 'Productos', 'href' => '?controller=producto&action=listar', 'icon' => 'bi-box-seam'],
            ['key' => 'nuevo_producto', 'label' => 'Nuevo Producto', 'href' => '?controller=producto&action=crear', 'icon' => 'bi-plus-circle'],
            ['key' => 'usuarios', 'label' => 'Usuarios', 'href' => '?controller=usuario&action=listar', 'icon' => 'bi-people'],
            ['key' => 'pedidos', 'label' => 'Pedidos Pendientes', 'href' => '?controller=pedido&action=pendientes', 'icon' => 'bi-cart-check'],
            ['divider' => true],
            ['key' => 'ver_sitio', 'label' => 'Ver Sitio', 'href' => '?controller=home&action=index', 'icon' => 'bi-house'],
            ['key' => 'logout', 'label' => 'Salir', 'href' => '?controller=auth&action=logout', 'icon' => 'bi-box-arrow-right', 'class' => 'text-danger'],
        ];
    }

    $datos['misPedidos'] = null;
    if ($usuario && class_exists('RoleMiddleware') && RoleMiddleware::esCliente()) {
        $datos['misPedidos'] = ['label' => 'Mis Pedidos', 'href' => '?controller=pedido&action=misPedidos', 'icon' => 'bi-bag-check'];
    }

    $datos['carrito'] = ['label' => 'Carrito', 'href' => '?controller=carrito&action=ver', 'icon' => 'bi-cart3'];

    return $datos;
}


