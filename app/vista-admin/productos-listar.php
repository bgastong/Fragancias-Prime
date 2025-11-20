<?php
require_once __DIR__ . '/layouts/header-admin.php';
?>

<div class="admin-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Gestión de Productos</h1>
            <a href="?controller=producto&action=crear" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>Nuevo Producto
            </a>
        </div>

        <?php if (isset($_SESSION['mensaje_exito'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= $_SESSION['mensaje_exito'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['mensaje_exito']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['mensaje_error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= $_SESSION['mensaje_error'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['mensaje_error']); ?>
        <?php endif; ?>

        <?php if (empty($productos)): ?>
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i>
                No hay productos registrados. Crea el primero usando el botón "Nuevo Producto".
            </div>
        <?php else: ?>
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Imagen</th>
                                    <th>Nombre</th>
                                    <th>Detalle</th>
                                    <th>Stock</th>
                                    <th>Precio</th>
                                    <th>Orden</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($productos as $producto): ?>
                                    <tr>
                                        <td><?= $producto['idproducto'] ?></td>
                                        <td>
                                            <?php if (!empty($producto['imagen'])): ?>
                                                <img src="/Fragancias Prime/public/upload/productos/<?= htmlspecialchars($producto['imagen']) ?>" 
                                                     alt="Producto" 
                                                     style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                                            <?php else: ?>
                                                <div style="width: 50px; height: 50px; background: #ddd; border-radius: 4px; display: flex; align-items: center; justify-content: center;">
                                                    <i class="bi bi-image text-muted"></i>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= htmlspecialchars($producto['pronombre']) ?></td>
                                        <td><?= htmlspecialchars(substr($producto['prodetalle'], 0, 50)) ?>...</td>
                                        <td>
                                            <span class="badge <?= $producto['procantstock'] > 0 ? 'bg-success' : 'bg-danger' ?>">
                                                <?= $producto['procantstock'] ?> unidades
                                            </span>
                                        </td>
                                        <td>
                                            <?php if (!empty($producto['precio'])): ?>
                                                $<?= number_format($producto['precio'], 2) ?>
                                            <?php else: ?>
                                                <span class="text-muted">Sin precio</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= $producto['orden'] ?? '-' ?></td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="?controller=producto&action=editar&id=<?= $producto['idproducto'] ?>" 
                                                   class="btn btn-sm btn-outline-primary" 
                                                   title="Editar">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <a href="?controller=producto&action=eliminar&id=<?= $producto['idproducto'] ?>" 
                                                   class="btn btn-sm btn-outline-danger" 
                                                   title="Eliminar"
                                                   onclick="return confirm('¿Estás seguro de eliminar este producto? Esta acción no se puede deshacer.')">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
require_once __DIR__ . '/layouts/footer-admin.php';
?>
