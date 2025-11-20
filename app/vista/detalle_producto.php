<?php
$esVistaAdmin = false;
require_once __DIR__ . '/layouts/header.php';
?>

<div class="container my-5">
    <div class="row">
        <!-- Imagen del producto -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm">
                <?php
                // Construir ruta de imagen
                $rutaImagen = '';
                if (!empty($producto['imagen'])) {
                    $rutaImagen = '/Fragancias Prime/public/upload/productos/' . $producto['imagen'];
                } elseif (!empty($producto['pronombre'])) {
                    $rutaImagen = $producto['pronombre'];
                } else {
                    $rutaImagen = '/Fragancias Prime/public/img/no-image.png';
                }
                ?>
                <img src="<?php echo htmlspecialchars($rutaImagen); ?>"
                    class="card-img-top"
                    alt="<?php echo htmlspecialchars($producto['prodetalle']); ?>"
                    style="width: 100%; height: auto; object-fit: cover;"
                    onerror="this.src='/Fragancias Prime/public/img/no-image.png'">
            </div>
        </div>

        <!-- Informacion del producto -->
        <div class="col-md-6">
            <h1 class="mb-3"><?php echo htmlspecialchars($producto['prodetalle']); ?></h1>

            <?php if (!empty($producto['subtitulo'])): ?>
                <p class="text-muted fs-5 mb-4"><?php echo htmlspecialchars($producto['subtitulo']); ?></p>
            <?php endif; ?>

            <div class="mb-4">
                <span class="fs-2 fw-bold text-primary">
                    $<?php echo number_format($producto['precio'] ?? 0, 2); ?>
                </span>
            </div>

            <?php if (!empty($producto['descripcion'])): ?>
                <div class="mb-4">
                    <h5>Descripcion</h5>
                    <p class="text-muted"><?php echo nl2br(htmlspecialchars($producto['descripcion'])); ?></p>
                </div>
            <?php endif; ?>

            <!-- Stock disponible -->
            <div class="mb-4">
                <p class="mb-2">
                    <strong>Disponibilidad:</strong>
                    <?php if ($producto['procantstock'] > 0): ?>
                        <span class="badge bg-success">En stock (<?php echo $producto['procantstock']; ?> unidades)</span>
                    <?php else: ?>
                        <span class="badge bg-danger">Sin stock</span>
                    <?php endif; ?>
                </p>
            </div>

            <!-- Formulario para agregar al carrito -->
            <?php if (isset($_SESSION['usuario'])): ?>
                <?php if ($producto['procantstock'] > 0): ?>
                    <form action="?controller=carrito&action=agregar" method="POST" class="mb-3">
                        <input type="hidden" name="idproducto" value="<?php echo $producto['idproducto']; ?>">

                        <div class="row align-items-end">
                            <div class="col-md-4 mb-3 mb-md-0">
                                <label for="cantidad" class="form-label">Cantidad:</label>
                                <input type="number"
                                    class="form-control"
                                    id="cantidad"
                                    name="cantidad"
                                    value="1"
                                    min="1"
                                    max="<?php echo $producto['procantstock']; ?>"
                                    required>
                            </div>
                            <div class="col-md-8">
                                <button type="submit" class="btn btn-primary btn-lg w-100">
                                    <i class="bi bi-cart-plus me-2"></i>Agregar al carrito
                                </button>
                            </div>
                        </div>
                    </form>
                <?php else: ?>
                    <button class="btn btn-secondary btn-lg w-100" disabled>
                        <i class="bi bi-x-circle me-2"></i>Producto no disponible
                    </button>
                <?php endif; ?>
            <?php else: ?>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    Debes <a href="?controller=auth&action=login" class="alert-link">iniciar sesion</a> 
                    para agregar productos al carrito.
                </div>
                <a href="?controller=auth&action=login" class="btn btn-primary btn-lg w-100 mb-2">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Iniciar sesion
                </a>
                <a href="?controller=auth&action=registro" class="btn btn-outline-primary btn-lg w-100">
                    <i class="bi bi-person-plus me-2"></i>Registrarse
                </a>
            <?php endif; ?>

            <!-- Boton volver -->
            <div class="mt-3">
                <a href="?controller=home&action=index" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Volver al catalogo
                </a>
            </div>
        </div>
    </div>

    <!-- Seccion adicional (opcional) -->
    <div class="row mt-5">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Informacion adicional</h5>
                    <p class="card-text text-muted">
                        Este producto es parte de nuestro catalogo exclusivo de fragancias.
                        Ofrecemos garantia de autenticidad en todos nuestros productos.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>