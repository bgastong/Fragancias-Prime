<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
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

    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<header>
    <nav class="navbar navbar-expand-lg navbar-light bg-white ">
        <div class="container">

            <!-- logo -->
            <a class="navbar-brand d-flex align-items-center" href="?controller=home&action=index">
                <img src="img/prime.png" alt="PRIME" class="me-2" >
            </a>

            <!-- cont -->
            <div class="collapse navbar-collapse" id="mainNavbar">

                <!-- buscador -->
                <form class="d-flex mx-lg-auto my-2 my-lg-0 w-100 w-lg-50"
                    method="get" action="">

                    <!-- crontrolador de productos -->
                    <input type="hidden" name="controller" value="producto">
                    <input type="hidden" name="action" value="buscar">

                    <input
                        class="form-control rounded-pill me-2"
                        type="search"
                        name="q"
                        placeholder="Buscar fragancia, marca..."
                        aria-label="Buscar"
                    >
                    <button class="btn btn-outline-dark rounded-pill" type="submit">
                        <i class="bi bi-search"></i>
                    </button>
                </form>

                <!-- login & carrito -->
                <ul class="navbar-nav ms-lg-3 align-items-center gap-2">

                    <?php if (!$usuario): ?>
                        <!-- No logueado -->
                        <li class="nav-item">
                            <a class="nav-link text-uppercase fw-semibold" href="?controller=auth&action=login">
                                <i class="bi bi-person me-1"></i> Login
                            </a>
                        </li>
                    <?php else: ?>
                        <!-- Logueado -->
                        <li class="nav-item">
                            <span class="nav-link small">
                                <i class="bi bi-person-check me-1"></i>
                                Hola <?= htmlspecialchars($usuario['usnombre']) ?>
                            </span>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-uppercase fw-semibold" href="?controller=auth&action=logout">
                                Salir
                            </a>
                        </li>
                    <?php endif; ?>

                    <!-- Carrito -->
                    <li class="nav-item position-relative">
                        <a class="nav-link text-uppercase fw-semibold" href="?controller=carrito&action=ver">
                            <i class="bi bi-bag me-1"></i> Carrito
                        </a>
                    </li>
                </ul>

            </div>
        </div>
    </nav>
</header>

<main class="container my-4">
