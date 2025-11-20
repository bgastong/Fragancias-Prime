<?php
require_once __DIR__ . '/../middleware/RoleMiddleware.php';

// Verificar que sea admin
RoleMiddleware::requiereAdmin();

require_once __DIR__ . '/../model/Usuario.php';
require_once __DIR__ . '/../model/Producto.php';
?>

<?php require_once __DIR__ . '/../vista-admin/layouts/header-admin.php'; ?>

<div class="admin-header">
    <h2>Dashboard</h2>
    <p class="text-muted">Bienvenido al panel de administración</p>
</div>

<div class="row g-4">
    
    <!-- Tarjeta Productos -->
    <div class="col-md-3">
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
    <div class="col-md-3">
        <div class="card text-white bg-success">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0">Usuarios</h6>
                        <h3 class="mb-0">
                            <?php
                            $usuario = new Usuario();
                            $usuarios = $usuario->listarUsuarios();
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

    <!-- Tarjeta Pedidos -->
    <div class="col-md-3">
        <div class="card text-white bg-warning">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0">Pedidos</h6>
                        <h3 class="mb-0">0</h3>
                    </div>
                    <i class="bi bi-cart-check" style="font-size: 3rem; opacity: 0.5;"></i>
                </div>
                <a href="?controller=pedido&action=listar" class="btn btn-light btn-sm mt-3">Ver todos</a>
            </div>
        </div>
    </div>

    <!-- Tarjeta Ventas -->
    <div class="col-md-3">
        <div class="card text-white bg-info">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0">Ventas</h6>
                        <h3 class="mb-0">$0</h3>
                    </div>
                    <i class="bi bi-cash-stack" style="font-size: 3rem; opacity: 0.5;"></i>
                </div>
                <a href="?controller=admin&action=reportes" class="btn btn-light btn-sm mt-3">Ver reportes</a>
            </div>
        </div>
    </div>

</div>

<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0">Accesos Rápidos</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <a href="?controller=producto&action=crear" class="btn btn-outline-primary w-100 py-3">
                            <i class="bi bi-plus-circle me-2"></i>Nuevo Producto
                        </a>
                    </div>
                    <div class="col-md-4 mb-3">
                        <a href="?controller=producto&action=listar" class="btn btn-outline-success w-100 py-3">
                            <i class="bi bi-list-ul me-2"></i>Gestionar Productos
                        </a>
                    </div>
                    <div class="col-md-4 mb-3">
                        <a href="?controller=usuario&action=listar" class="btn btn-outline-info w-100 py-3">
                            <i class="bi bi-people me-2"></i>Gestionar Usuarios
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../vista-admin/layouts/footer-admin.php'; ?>
