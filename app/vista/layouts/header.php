<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cargar middleware para verificar roles
require_once __DIR__ . '/../../middleware/RoleMiddleware.php';

$usuario = $_SESSION['usuario'] ?? null; // Obtener usuario logueado

// Detectar si estamos en vista admin
$isAdminView = isset($esVistaAdmin) && $esVistaAdmin === true;
?>
<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $isAdminView ? 'Panel Admin - ' : '' ?>Fragancias Prime</title>

    <!-- bootstrap -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
        crossorigin="anonymous">

    <!-- carrito y user -->
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <link rel="stylesheet" href="/Fragancias Prime/public/css/style.css">

    <?php if ($isAdminView): ?>
        <style>
            .sidebar {
                min-height: 100vh;
                background: #2c3e50;
                padding: 0;
            }

            .sidebar .nav-link {
                color: #ecf0f1;
                padding: 15px 20px;
                border-left: 3px solid transparent;
            }

            .sidebar .nav-link:hover,
            .sidebar .nav-link.active {
                background: #34495e;
                border-left-color: #3498db;
            }

            .sidebar .nav-link i {
                width: 25px;
            }

            .admin-header {
                background: white;
                border-bottom: 1px solid #ddd;
                padding: 15px 0;
                margin-bottom: 30px;
            }
        </style>
    <?php endif; ?>
</head>

<body>

    <?php if ($isAdminView): ?>
        <!-- layout admin -->
        <div class="container-fluid">
            <div class="row">

                <!-- sidebar -->
                <nav class="col-md-2 d-md-block sidebar">
                    <div class="position-sticky">

                        <div class="text-center py-4 border-bottom border-secondary">
                            <h5 class="text-white mb-0">Panel Admin</h5>
                        </div>

                        <ul class="nav flex-column mt-3">
                            <li class="nav-item">
                                
                                <a class="nav-link <?= isset($activeMenu) && $activeMenu === 'dashboard' ? 'active' : '' ?>"  
                                    href="?controller=admin&action=dashboard">
                                    <i class="bi bi-speedometer2"></i> Dashboard
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= isset($activeMenu) && $activeMenu === 'productos' ? 'active' : '' ?>"
                                    href="?controller=producto&action=listar">
                                    <i class="bi bi-box-seam"></i> Productos
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="?controller=producto&action=crear">
                                    <i class="bi bi-plus-circle"></i> Nuevo Producto
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= isset($activeMenu) && $activeMenu === 'usuarios' ? 'active' : '' ?>"
                                    href="?controller=usuario&action=listar">
                                    <i class="bi bi-people"></i> Usuarios
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= isset($activeMenu) && $activeMenu === 'pedidos' ? 'active' : '' ?>"
                                    href="?controller=pedido&action=pendientes">
                                    <i class="bi bi-cart-check"></i> Pedidos Pendientes
                                </a>
                            </li>

                            <hr class="border-secondary my-3">

                            <li class="nav-item">
                                <a class="nav-link" href="?controller=home&action=index">
                                    <i class="bi bi-house"></i> Ver Sitio
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-danger" href="?controller=auth&action=logout">
                                    <i class="bi bi-box-arrow-right"></i> Salir
                                </a>
                            </li>
                        </ul>

                    </div>
                </nav>

                <!-- Contenido principal admin -->
                <main class="col-md-10 ms-sm-auto px-4">

                <?php else: ?>
                    <!-- layout cliente - navbar -->
                    <header>
                        <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
                            <div class="container">

                                <!-- logo -->
                                <a class="navbar-brand d-flex align-items-center" href="?controller=home&action=index">
                                    <img src="/Fragancias Prime/public/img/prime.png" alt="PRIME" class="navbar-logo">
                                </a>

                                <!-- cont -->
                                <div class="collapse navbar-collapse" id="mainNavbar">

                                    <!-- Menu dinamico -->
                                    <ul class="navbar-nav me-auto">
                                    </ul>

                                    <!-- buscador -->
                                    <form class="d-flex mx-lg-auto my-2 my-lg-0 flex-grow-1 search-form"
                                        method="get" action="">

                                        <!-- controlador de productos -->
                                        <input type="hidden" name="controller" value="producto">
                                        <input type="hidden" name="action" value="buscar">

                                        <div class="search-wrapper">
                                            <i class="bi bi-search search-icon"></i>
                                            <input
                                                class="form-control search-input"
                                                type="search"
                                                name="q"
                                                placeholder="Buscar fragancia, marca..."
                                                aria-label="Buscar">
                                        </div>
                                    </form>

                                    <!-- login & carrito -->
                                    <ul class="navbar-nav ms-lg-3 align-items-lg-center gap-2">

                                        <?php if (!$usuario): ?>
                                            <!-- No logueado -->
                                            <li class="nav-item">
                                                <a class="nav-link nav-link-custom" href="?controller=auth&action=login">
                                                    Login
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link nav-link-custom" href="?controller=auth&action=registro">
                                                    Registrarse
                                                </a>
                                            </li>
                                        <?php else: ?>
                                            <!-- Logueado -->
                                            <li class="nav-item dropdown">
                                                <a class="nav-link-icon" href="#" id="userDropdown" role="button"
                                                    data-bs-toggle="dropdown" aria-expanded="false" title="Mi Cuenta">
                                                    <i class="bi bi-person-circle"></i>
                                                </a>
                                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                                    <li>
                                                        <span class="dropdown-item-text">
                                                            Hola, <strong><?= htmlspecialchars(ucfirst(strtolower($usuario['usnombre']))) ?></strong>
                                                        </span>
                                                    </li>
                                                    <li>
                                                        <hr class="dropdown-divider">
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" href="?controller=usuario&action=cambiarContrasena">
                                                            <i class="bi bi-key me-2"></i>Cambiar Contrasena
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" href="?controller=usuario&action=cambiarEmail">
                                                            <i class="bi bi-envelope me-2"></i>Cambiar Email
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <hr class="dropdown-divider">
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item text-danger" href="?controller=auth&action=logout">
                                                            <i class="bi bi-box-arrow-right me-2"></i>Salir
                                                        </a>
                                                    </li>
                                                </ul>
                                            </li>

                                            <!-- Menu admin (solo para admin) -->
                                            <?php if (RoleMiddleware::esAdmin()): ?>
                                                <li class="nav-item dropdown">
                                                    <a class="nav-link-icon" href="#" id="adminDropdown" role="button"
                                                        data-bs-toggle="dropdown" aria-expanded="false" title="Menu Admin">
                                                        <i class="bi bi-three-dots-vertical"></i>
                                                    </a>
                                                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="adminDropdown">
                                                        <li>
                                                            <a class="dropdown-item" href="?controller=admin&action=dashboard">
                                                                <i class="bi bi-speedometer2 me-2"></i>Dashboard
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item" href="?controller=producto&action=listar">
                                                                <i class="bi bi-box-seam me-2"></i>Productos
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item" href="?controller=usuario&action=listar">
                                                                <i class="bi bi-people me-2"></i>Usuarios
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </li>
                                            <?php endif; ?>

                                            <!-- Mis Pedidos (solo para clientes) -->
                                            <?php if (RoleMiddleware::esCliente()): ?>
                                                <li class="nav-item">
                                                    <a class="nav-link-icon" href="?controller=pedido&action=misPedidos" title="Mis Pedidos">
                                                        <i class="bi bi-bag-check"></i>
                                                    </a>
                                                </li>
                                            <?php endif; ?>

                                            <!-- Carrito (solo para usuarios logueados) -->
                                            <li class="nav-item">
                                                <a class="nav-link-icon" href="?controller=carrito&action=ver" title="Carrito">
                                                    <i class="bi bi-cart3"></i>
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                    </ul>

                                </div>
                            </div>
                        </nav>
                    </header>

                    <main class="main-content">
                    <?php endif; ?>