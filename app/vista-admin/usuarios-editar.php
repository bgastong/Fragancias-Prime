<?php
$esVistaAdmin = true;
$activeMenu = 'usuarios';
require_once __DIR__ . '/../vista/layouts/header.php';

// Obtener todos los roles disponibles
$database = new Database();
$db = $database->getConnection();
$sqlRoles = "SELECT * FROM rol ORDER BY rodescripcion";
$stmtRoles = $db->query($sqlRoles);
$roles = $stmtRoles->fetchAll(PDO::FETCH_ASSOC);

// Obtener roles actuales del usuario
$usuarioModel = new Usuario();
$rolesUsuario = $usuarioModel->obtenerRolesUsuario($usuario['idusuario']);
$rolActual = !empty($rolesUsuario) ? $rolesUsuario[0]['idrol'] : null;
?>

<div class="admin-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Editar Usuario #<?= $usuario['idusuario'] ?></h1>
            <a href="?controller=usuario&action=listar" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-2"></i>Volver al Listado
            </a>
        </div>

        <?php if (isset($_SESSION['mensaje_error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= $_SESSION['mensaje_error'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['mensaje_error']); ?>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Informacion del Usuario</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nombre de Usuario:</label>
                            <p class="form-control-plaintext"><?= htmlspecialchars($usuario['usnombre']) ?></p>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Email:</label>
                            <p class="form-control-plaintext"><?= htmlspecialchars($usuario['usmail']) ?></p>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Estado:</label>
                            <p>
                                <?php if (is_null($usuario['usdeshabilitado'])): ?>
                                    <span class="badge bg-success">Activo</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">
                                        Deshabilitado desde <?= date('d/m/Y H:i', strtotime($usuario['usdeshabilitado'])) ?>
                                    </span>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Cambiar Rol</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="?controller=usuario&action=editar&id=<?= $usuario['idusuario'] ?>">
                            <div class="mb-3">
                                <label for="idrol" class="form-label">Rol del Usuario</label>
                                <select class="form-select" id="idrol" name="idrol" required>
                                    <option value="">Seleccionar rol...</option>
                                    <?php foreach ($roles as $rol): ?>
                                        <option value="<?= $rol['idrol'] ?>" 
                                                <?= $rolActual == $rol['idrol'] ? 'selected' : '' ?>>
                                            <?= ucfirst($rol['rodescripcion']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-save me-2"></i>Actualizar Rol
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/../vista/layouts/footer.php';
?>
