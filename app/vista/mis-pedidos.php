<?php
require_once __DIR__ . '/layouts/header.php';
?>

<div class="container my-5">
    <h1 class="mb-4">Mis Pedidos</h1>

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

    <?php if (empty($pedidos)): ?>
        <div class="alert alert-info">
            <i class="bi bi-info-circle me-2"></i>
            Aún no has realizado ningún pedido.
        </div>
        <a href="?controller=home&action=index" class="btn btn-primary">
            <i class="bi bi-shop me-2"></i>Ir a la tienda
        </a>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>N° Pedido</th>
                        <th>Fecha</th>
                        <th>Total</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pedidos as $pedido): ?>
                        <tr>
                            <td>#<?= $pedido['idcompra'] ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($pedido['cofecha'])) ?></td>
                            <td>$<?= number_format($pedido['total'], 2) ?></td>
                            <td>
                                <?php
                                $estado = $pedido['estado'] ?? 'sin estado';
                                $badgeClass = match($pedido['idcompraestadotipo'] ?? 0) {
                                    1 => 'bg-warning',      // iniciada
                                    2 => 'bg-info',         // aceptada
                                    3 => 'bg-primary',      // enviada
                                    4 => 'bg-danger',       // cancelada
                                    default => 'bg-secondary'
                                };
                                ?>
                                <span class="badge <?= $badgeClass ?>">
                                    <?= ucfirst($estado) ?>
                                </span>
                            </td>
                            <td>
                                <a href="?controller=pedido&action=detalle&id=<?= $pedido['idcompra'] ?>" 
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye me-1"></i>Ver detalle
                                </a>
                                
                                <?php if ($pedido['idcompraestadotipo'] == 1): ?>
                                    <a href="?controller=pedido&action=cancelar&id=<?= $pedido['idcompra'] ?>" 
                                       class="btn btn-sm btn-outline-danger"
                                       onclick="return confirm('¿Cancelar este pedido?')">
                                        <i class="bi bi-x-circle me-1"></i>Cancelar
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php
require_once __DIR__ . '/layouts/footer.php';
?>
