<?php
$isAdminView = isset($esVistaAdmin) && $esVistaAdmin === true; // Variable seteada en controlador admin
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

                        <?php
                        // Render admin sidebar dinámico desde $datosHeader['sidebarAdmin'] si esta disponible.
                        $adminSidebar = $datosHeader['sidebarAdmin'] ?? [];
                        if (!empty($adminSidebar)) {
                            echo '<ul class="nav flex-column mt-3">';
                            foreach ($adminSidebar as $entry) {
                                if (isset($entry['divider']) && $entry['divider']) {
                                    echo '<hr class="border-secondary my-3">';
                                    continue;
                                }
                                $key = $entry['key'] ?? '';
                                $label = htmlspecialchars($entry['label'] ?? '');
                                $href = $entry['href'] ?? '#';
                                $icon = isset($entry['icon']) && $entry['icon'] ? '<i class="bi ' . $entry['icon'] . '"></i> ' : '';
                                $class = isset($entry['class']) ? ' ' . $entry['class'] : '';
                                $isActive = isset($activeMenu) && $activeMenu === $key ? 'active' : '';
                                echo '<li class="nav-item">';
                                echo '<a class="nav-link ' . $isActive . $class . '" href="' . $href . '">' . $icon . ' ' . $label . '</a>';
                                echo '</li>';
                            }
                            echo '</ul>';
                        }
                        ?>

                    </div>
                </nav>

                <!-- Contenido principal admin -->
                <main class="col-md-10 ms-sm-auto px-4">

                <?php else: ?>
                    <!-- layout cliente - navbar -->
                    <header>
                        <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
                            <div class="container">

                                <!-- cont -->
                                <div class="collapse navbar-collapse" id="mainNavbar">

                                    <?php
                                    // Logo
                                    $logoUrl = isset($datosHeader['logo']) ? $datosHeader['logo'] : '/Fragancias Prime/public/img/prime.png';
                                    echo '<a class="navbar-brand d-flex align-items-center" href="?controller=home&action=index">';
                                    echo '<img src="' . $logoUrl . '" alt="PRIME" class="navbar-logo">';
                                    echo '</a>';

                                    // Menu izquierdo (ya preparado por el controlador)
                                    $leftMenu = $datosHeader['menuIzquierdo'] ?? [];
                                    echo '<ul class="navbar-nav me-auto">';
                                    foreach ($leftMenu as $item) {
                                        $show = isset($item['condition']) ? (bool)$item['condition'] : true;
                                        if (!$show) continue;
                                        $isActive = isset($activeMenu) && $activeMenu === ($item['key'] ?? '') ? 'active' : '';
                                        $label = htmlspecialchars($item['label'] ?? '');
                                        $href = $item['href'] ?? '#';
                                        $icon = isset($item['icon']) && $item['icon'] ? '<i class="bi ' . $item['icon'] . ' me-1"></i>' : '';
                                        echo "<li class=\"nav-item\"><a class=\"nav-link {$isActive}\" href=\"{$href}\">{$icon}{$label}</a></li>";
                                    }
                                    echo '</ul>';
                                    ?>

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

                                    <!-- login & carrito (dinamico) -->
                                    <ul class="navbar-nav ms-lg-3 align-items-lg-center gap-2">
                                        <?php
                                        $auth = $datosHeader['autenticacion'] ?? ['tipo' => 'invitado', 'links' => []];

                                        if (($auth['tipo'] ?? '') === 'invitado') {
                                            foreach ($auth['links'] as $link) {
                                                echo '<li class="nav-item"><a class="nav-link nav-link-custom" href="' . ($link['href'] ?? '#') . '">' . htmlspecialchars($link['label']) . '</a></li>';
                                            }
                                        } else {
                                            // Usuario logueado: mostrar dropdown con opciones
                                            $user = $auth['usuario'] ?? null;
                                            $userLabel = $user['label'] ?? '';
                                        ?>
                                            <li class="nav-item dropdown">
                                                <a class="nav-link-icon" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" title="Mi Cuenta">
                                                    <i class="bi bi-person-circle"></i>
                                                </a>
                                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                                    <li>
                                                        <span class="dropdown-item-text">Hola, <strong><?= $userLabel ?></strong></span>
                                                    </li>
                                                    <li>
                                                        <hr class="dropdown-divider">
                                                    </li>
                                                    <?php
                                                    foreach ($user['dropdown'] as $d) {
                                                        if (isset($d['divider']) && $d['divider']) {
                                                            echo '<li><hr class="dropdown-divider"></li>';
                                                            continue;
                                                        }
                                                        $class = isset($d['class']) ? ' ' . $d['class'] : '';
                                                        $icon = isset($d['icon']) ? '<i class="bi ' . $d['icon'] . ' me-2"></i>' : '';
                                                        echo '<li><a class="dropdown-item' . $class . '" href="' . ($d['href'] ?? '#') . '">' . $icon . htmlspecialchars($d['label']) . '</a></li>';
                                                    }
                                                    ?>
                                                </ul>
                                            </li>
                                        <?php

                                            // Admin quick menu if present
                                            $adminQuick = $datosHeader['adminRapido'] ?? [];
                                            if (!empty($adminQuick)) { // mostrar menu admin
                                                echo '<li class="nav-item dropdown">';
                                                echo '<a class="nav-link-icon" href="#" id="adminDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" title="Menu Admin">';
                                                echo '<i class="bi bi-three-dots-vertical"></i>';
                                                echo '</a>';
                                                echo '<ul class="dropdown-menu dropdown-menu-end" aria-labelledby="adminDropdown">';
                                                foreach ($adminQuick as $aq) {
                                                    $icon = isset($aq['icon']) ? '<i class="bi ' . $aq['icon'] . ' me-2"></i>' : '';
                                                    echo '<li><a class="dropdown-item" href="' . ($aq['href'] ?? '#') . '">' . $icon . htmlspecialchars($aq['label']) . '</a></li>';
                                                }
                                                echo '</ul></li>';
                                            }

                                            // Mis pedidos
                                            if (!empty($datosHeader['misPedidos'])) {
                                                $mp = $datosHeader['misPedidos'];
                                                echo '<li class="nav-item"><a class="nav-link-icon" href="' . ($mp['href'] ?? '#') . '" title="Mis Pedidos"><i class="bi ' . ($mp['icon'] ?? '') . '"></i></a></li>';
                                            }

                                            // Carrito
                                            $cart = $datosHeader['carrito'] ?? null;
                                            if ($cart) {
                                                echo '<li class="nav-item"><a class="nav-link-icon" href="' . ($cart['href'] ?? '#') . '" title="Carrito"><i class="bi ' . ($cart['icon'] ?? '') . '"></i></a></li>';
                                            }
                                        }
                                        ?>
                                    </ul>

                                </div>
                            </div>
                        </nav>
                    </header>

                    <main class="main-content">
                    <?php endif; ?>