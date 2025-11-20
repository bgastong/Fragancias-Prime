<?php
require_once __DIR__ . '/layouts/header.php';
?>

<div class="container my-5">
    <h1 class="mb-4">Pedidos Pendientes</h1>

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
            No hay pedidos pendientes en este momento.
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>N° Pedido</th>
                        <th>Cliente</th>
                        <th>Email</th>
                        <th>Fecha</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pedidos as $pedido): ?>
                        <tr>
                            <td>#<?= $pedido['idcompra'] ?></td>
                            <td><?= htmlspecialchars($pedido['usnombre']) ?></td>
                            <td><?= htmlspecialchars($pedido['usmail']) ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($pedido['cofecha'])) ?></td>
                            <td>
                                <?php
                                $estado = $pedido['estado'] ?? 'sin estado';
                                $estadoId = $pedido['idcompraestadotipo'] ?? 0;
                                $badgeClass = match ($estadoId) {
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
                                <div class="btn-group" role="group">
                                    <a href="?controller=pedido&action=detalle&id=<?= $pedido['idcompra'] ?>"
                                        class="btn btn-sm btn-outline-primary" title="Ver detalle">
                                        <i class="bi bi-eye"></i>
                                    </a>

                                    <?php if ($estadoId == 1): ?>
                                        <!-- iniciada - puede aceptar -->
                                        <a href="?controller=pedido&action=aceptar&id=<?= $pedido['idcompra'] ?>"
                                            class="btn btn-sm btn-success" title="Aceptar pedido"
                                            onclick="return confirm('Aceptar este pedido?')">
                                            <i class="bi bi-check-circle"></i>
                                        </a>
                                    <?php endif; ?>

                                    <?php if ($estadoId == 2): ?>
                                        <!-- Estado aceptada: puede enviar -->
                                        <a href="?controller=pedido&action=enviar&id=<?= $pedido['idcompra'] ?>"
                                            class="btn btn-sm btn-primary" title="Marcar como enviado"
                                            onclick="return confirm('Marcar este pedido como enviado?')">
                                            <i class="bi bi-truck"></i>
                                        </a>
                                    <?php endif; ?>

                                    <?php if ($estadoId != 4): ?>
                                        <!-- si no esta cancelado - cancelar -->
                                        <a href="?controller=pedido&action=cancelar&id=<?= $pedido['idcompra'] ?>"
                                            class="btn btn-sm btn-danger" title="Cancelar pedido"
                                            onclick="return confirm('Estas seguro de cancelar este pedido?')">
                                            <i class="bi bi-x-circle"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
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