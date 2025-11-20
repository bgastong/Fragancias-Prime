<?php
$esVistaAdmin = true;
$activeMenu = 'usuarios';
require_once __DIR__ . '/../vista/layouts/header.php';
?>

<div class="admin-content">
    <div class="container-fluid">
        <h1 class="mb-4">Gestion de Usuarios</h1>

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

        <?php if (empty($usuarios)): ?>
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i>
                No hay usuarios registrados.
            </div>
        <?php else: ?>
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Usuario</th>
                                    <th>Email</th>
                                    <th>Rol</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($usuarios as $usuario): ?>
                                    <tr>
                                        <td><?= $usuario['idusuario'] ?></td>
                                        <td><?= htmlspecialchars($usuario['usnombre']) ?></td>
                                        <td><?= htmlspecialchars($usuario['usmail']) ?></td>
                                        <td>
                                            <span class="badge <?=
                                                                match ($usuario['rol_nombre'] ?? '') {
                                                                    'admin' => 'bg-danger',
                                                                    'cliente' => 'bg-primary',
                                                                    default => 'bg-secondary'
                                                                }
                                                                ?>">
                                                <?= ucfirst($usuario['rol_nombre'] ?? 'Sin rol') ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if (is_null($usuario['usdeshabilitado'])): ?>
                                                <span class="badge bg-success">Activo</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Deshabilitado</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="?controller=usuario&action=editar&id=<?= $usuario['idusuario'] ?>"
                                                    class="btn btn-sm btn-outline-primary"
                                                    title="Editar rol">
                                                    <i class="bi bi-pencil"></i>
                                                </a>

                                                <?php if (is_null($usuario['usdeshabilitado'])): ?>
                                                    <a href="?controller=usuario&action=deshabilitar&id=<?= $usuario['idusuario'] ?>"
                                                        class="btn btn-sm btn-outline-warning"
                                                        title="Deshabilitar"
                                                        onclick="return confirm('Deshabilitar este usuario?')">
                                                        <i class="bi bi-lock"></i>
                                                    </a>
                                                <?php else: ?>
                                                    <a href="?controller=usuario&action=habilitar&id=<?= $usuario['idusuario'] ?>"
                                                        class="btn btn-sm btn-outline-success"
                                                        title="Habilitar"
                                                        onclick="return confirm('Habilitar este usuario?')">
                                                        <i class="bi bi-unlock"></i>
                                                    </a>
                                                <?php endif; ?>
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
require_once __DIR__ . '/../vista/layouts/footer.php';
?>