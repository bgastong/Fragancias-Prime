<?php
$esVistaAdmin = true;
$activeMenu = 'productos';
require_once __DIR__ . '/../vista/layouts/header.php';
?>

<div class="admin-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Crear Nuevo Producto</h1>
            <a href="?controller=producto&action=listar" class="btn btn-secondary">
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

        <div class="card">
            <div class="card-body">
                <form method="POST" action="?controller=producto&action=crear" enctype="multipart/form-data">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="prodetalle" class="form-label">Nombre del Producto <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="prodetalle" name="prodetalle"
                                    placeholder="Ej: Perfume Azzaro Chrome" required>
                                <small class="text-muted">Este sera el nombre visible del producto</small>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="procantstock" class="form-label">Stock</label>
                                <input type="number" class="form-control" id="procantstock" name="procantstock" value="0" min="0">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="precio" class="form-label">Precio</label>
                                <input type="number" class="form-control" id="precio" name="precio" step="0.01" min="0" value="0">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripcion del Producto</label>
                        <textarea class="form-control" id="descripcion" name="descripcion" rows="4" 
                            placeholder="Descripcion detallada del producto"></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="subtitulo" class="form-label">Subtitulo (opcional)</label>
                                <input type="text" class="form-control" id="subtitulo" name="subtitulo"
                                    placeholder="Texto secundario para el slider">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="orden" class="form-label">Orden en Slider</label>
                                <input type="number" class="form-control" id="orden" name="orden" value="0" min="0">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="imagen" class="form-label">Imagen del Producto</label>
                        <input type="file" class="form-control" id="imagen" name="imagen" accept="image/*">
                        <small class="text-muted">Formatos aceptados: JPG, PNG, WEBP. Tamano maximo: 5MB</small>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="?controller=producto&action=listar" class="btn btn-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-2"></i>Guardar Producto
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/../vista/layouts/footer.php';
?>