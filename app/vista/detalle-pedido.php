<?php
require_once __DIR__ . '/layouts/header.php';
?>

<div class="container my-5">
    <div class="row">
        <div class="col-md-8">
            <h1 class="mb-4">Detalle del Pedido #<?= $pedido['idcompra'] ?></h1>

            <!-- Información del pedido -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Información del Pedido</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Cliente:</strong> <?= htmlspecialchars($pedido['usnombre']) ?></p>
                            <p><strong>Email:</strong> <?= htmlspecialchars($pedido['usmail']) ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Fecha:</strong> <?= date('d/m/Y H:i', strtotime($pedido['cofecha'])) ?></p>
                            <p>
                                <strong>Estado:</strong>
                                <?php
                                $estado = $pedido['estado'] ?? 'sin estado';
                                $estadoId = $pedido['idcompraestadotipo'] ?? 0;
                                $badgeClass = match ($estadoId) {
                                    1 => 'bg-warning',
                                    2 => 'bg-info',
                                    3 => 'bg-primary',
                                    4 => 'bg-danger',
                                    default => 'bg-secondary'
                                };
                                ?>
                                <span class="badge <?= $badgeClass ?>">
                                    <?= ucfirst($estado) ?>
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Items del pedido -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Productos</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($items)): ?>
                        <p class="text-muted">No hay productos en este pedido.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Producto</th>
                                        <th>Descripcion</th>
                                        <th class="text-center">Cantidad</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($items as $item): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($item['pronombre']) ?></td>
                                            <td><?= htmlspecialchars($item['prodetalle']) ?></td>
                                            <td class="text-center"><?= $item['cicantidad'] ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Historial -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Historial de Estados</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($historialEstados)): ?>
                        <p class="text-muted">No hay historial disponible.</p>
                    <?php else: ?>
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Estado</th>
                                    <th>Fecha Inicio</th>
                                    <th>Fecha Fin</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($historialEstados as $estado): ?>
                                    <tr>
                                        <td>
                                            <?php
                                            $badgeClass = match ($estado['idcompraestadotipo']) {
                                                1 => 'bg-warning',
                                                2 => 'bg-info',
                                                3 => 'bg-primary',
                                                4 => 'bg-danger',
                                                default => 'bg-secondary'
                                            };
                                            ?>
                                            <span class="badge <?= $badgeClass ?>">
                                                <?= ucfirst($estado['cetdescripcion']) ?>
                                            </span>
                                        </td>
                                        <td><?= date('d/m/Y H:i', strtotime($estado['cefechaini'])) ?></td>
                                        <td>
                                            <?php if ($estado['cefechafin']): ?>
                                                <?= date('d/m/Y H:i', strtotime($estado['cefechafin'])) ?>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Acciones laterales -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Acciones</h5>
                </div>
                <div class="card-body">
                    <?php
                    $usuarioId = AuthMiddleware::usuarioId();
                    $esAdmin = RoleMiddleware::esAdmin();
                    $esDueno = $pedido['idusuario'] == $usuarioId;
                    ?>

                    <?php if ($esAdmin && $estadoId == 1): ?>
                        <a href="?controller=pedido&action=aceptar&id=<?= $pedido['idcompra'] ?>"
                            class="btn btn-success w-100 mb-2"
                            onclick="return confirm('Aceptar este pedido?')">
                            <i class="bi bi-check-circle me-2"></i>Aceptar Pedido
                        </a>
                    <?php endif; ?>

                    <?php if ($esAdmin && $estadoId == 2): ?>
                        <a href="?controller=pedido&action=enviar&id=<?= $pedido['idcompra'] ?>"
                            class="btn btn-primary w-100 mb-2"
                            onclick="return confirm('Marcar como enviado?')">
                            <i class="bi bi-truck me-2"></i>Marcar Enviado
                        </a>
                    <?php endif; ?>

                    <?php if ($estadoId != 4): ?>
                        <?php if ($esAdmin || ($esDueno && $estadoId == 1)): ?>
                            <a href="?controller=pedido&action=cancelar&id=<?= $pedido['idcompra'] ?>"
                                class="btn btn-danger w-100 mb-2"
                                onclick="return confirm('Estas seguro de cancelar este pedido?')">
                                <i class="bi bi-x-circle me-2"></i>Cancelar Pedido
                            </a>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if ($esAdmin): ?>
                        <a href="?controller=pedido&action=pendientes" class="btn btn-outline-secondary w-100">
                            <i class="bi bi-arrow-left me-2"></i>Volver a Pendientes
                        </a>
                    <?php else: ?>
                        <a href="?controller=pedido&action=misPedidos" class="btn btn-outline-secondary w-100">
                            <i class="bi bi-arrow-left me-2"></i>Volver a Mis Pedidos
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/layouts/footer.php';
?>