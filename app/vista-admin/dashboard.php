<?php
require_once __DIR__ . '/../middleware/RoleMiddleware.php';
RoleMiddleware::requiereAdmin(); // solo admin

require_once __DIR__ . '/../model/Usuario.php';
require_once __DIR__ . '/../model/Producto.php';
require_once __DIR__ . '/../model/Pedido.php';
?>

<?php 
$esVistaAdmin = true;
$activeMenu = 'dashboard';
require_once __DIR__ . '/../vista/layouts/header.php'; 
?>

<div class="admin-header">
    <h2>Dashboard</h2>
    <p class="text-muted">Bienvenido al panel de administracion</p>
</div>

<div class="row g-4">
    
    <!-- Tarjeta Productos -->
    <div class="col-md-4">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0">Productos</h6>
                        <h3 class="mb-0">
                            <?php
                            $producto = new Producto();
                            $productos = $producto->listarTodos();
                            echo count($productos);
                            ?>
                        </h3>
                    </div>
                    <i class="bi bi-box-seam" style="font-size: 3rem; opacity: 0.5;"></i>
                </div>
                <a href="?controller=producto&action=listar" class="btn btn-light btn-sm mt-3">Ver todos</a>
            </div>
        </div>
    </div>

    <!-- Tarjeta Usuarios -->
    <div class="col-md-4">
        <div class="card text-white bg-success">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0">Usuarios</h6>
                        <h3 class="mb-0">
                            <?php
                            $usuario = new Usuario();
                            $usuarios = $usuario->listarTodos();
                            echo count($usuarios);
                            ?>
                        </h3>
                    </div>
                    <i class="bi bi-people" style="font-size: 3rem; opacity: 0.5;"></i>
                </div>
                <a href="?controller=usuario&action=listar" class="btn btn-light btn-sm mt-3">Ver todos</a>
            </div>
        </div>
    </div>

    <!-- Tarjeta Pedidos Pendientes -->
    <div class="col-md-4">
        <div class="card text-white bg-warning">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0">Pedidos Pendientes</h6>
                        <h3 class="mb-0">
                            <?php
                            $pedido = new Pedido();
                            $pedidos = $pedido->obtenerPendientes();
                            echo count($pedidos);
                            ?>
                        </h3>
                    </div>
                    <i class="bi bi-cart-check" style="font-size: 3rem; opacity: 0.5;"></i>
                </div>
                <a href="?controller=pedido&action=pendientes" class="btn btn-light btn-sm mt-3">Ver todos</a>
            </div>
        </div>
    </div>

</div>

<?php require_once __DIR__ . '/../vista/layouts/footer.php'; ?>
