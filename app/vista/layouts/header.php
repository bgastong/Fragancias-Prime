<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cargar middleware para verificar roles
require_once __DIR__ . '/../../middleware/RoleMiddleware.php';

$usuario = $_SESSION['usuario'] ?? null;
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Fragancias Prime</title>

    <!-- bootstrap -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
        crossorigin="anonymous"
    >

    <!-- carrito y user -->
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
    >

    <link rel="stylesheet" href="/Fragancias Prime/public/css/style.css">
</head>
<body>

<header>
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">

            <!-- logo -->
            <a class="navbar-brand d-flex align-items-center" href="?controller=home&action=index">
                <img src="/Fragancias Prime/public/img/prime.png" alt="PRIME" class="navbar-logo">
            </a>

            <!-- Botón hamburguesa para móvil -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" 
                    aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- cont -->
            <div class="collapse navbar-collapse" id="mainNavbar">

                <!-- Menú dinámico según rol -->
                <ul class="navbar-nav me-auto">
                    
                    <?php if ($usuario && RoleMiddleware::esDeposito()): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="?controller=pedido&action=pendientes">
                                <i class="bi bi-box me-1"></i>Pedidos Pendientes
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="?controller=producto&action=stock">
                                <i class="bi bi-boxes me-1"></i>Stock
                            </a>
                        </li>
                    <?php endif; ?>

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
                            aria-label="Buscar"
                        >
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
                        <li class="nav-item">
                            <span class="nav-link user-welcome">
                                Hola, <strong><?= htmlspecialchars($usuario['usnombre']) ?></strong>
                            </span>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link nav-link-custom logout" href="?controller=auth&action=logout">
                                Salir
                            </a>
                        </li>
                        
                        <!-- Menú admin (solo para admin) -->
                        <?php if (RoleMiddleware::esAdmin()): ?>
                            <li class="nav-item dropdown">
                                <a class="nav-link-icon" href="#" id="adminDropdown" role="button" 
                                   data-bs-toggle="dropdown" aria-expanded="false" title="Menú Admin">
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
                    <?php endif; ?>

                    <!-- Mis Pedidos (solo para clientes) -->
                    <?php if ($usuario && RoleMiddleware::esCliente()): ?>
                        <li class="nav-item">
                            <a class="nav-link-icon" href="?controller=pedido&action=misPedidos" title="Mis Pedidos">
                                <i class="bi bi-bag-check"></i>
                            </a>
                        </li>
                    <?php endif; ?>

                    <!-- Carrito (solo para clientes o no logueados) -->
                    <?php if (!$usuario || RoleMiddleware::esCliente()): ?>
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
