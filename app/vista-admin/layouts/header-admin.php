<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Admin - Fragancias Prime</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/Fragancias Prime/public/css/style.css">
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
</head>
<body>

<div class="container-fluid">
    <div class="row">
        
        <!-- Sidebar -->
        <nav class="col-md-2 d-md-block sidebar">
            <div class="position-sticky">
                
                <div class="text-center py-4 border-bottom border-secondary">
                    <h5 class="text-white mb-0">Panel Admin</h5>
                </div>

                <ul class="nav flex-column mt-3">
                    <li class="nav-item">
                        <a class="nav-link active" href="?controller=admin&action=dashboard">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="?controller=producto&action=listar">
                            <i class="bi bi-box-seam"></i> Productos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="?controller=producto&action=crear">
                            <i class="bi bi-plus-circle"></i> Nuevo Producto
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="?controller=usuario&action=listar">
                            <i class="bi bi-people"></i> Usuarios
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="?controller=pedido&action=pendientes">
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

        <!-- Contenido principal -->
        <main class="col-md-10 ms-sm-auto px-4">
