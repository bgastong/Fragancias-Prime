<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fragancias Prime - Home</title>
    <link rel="stylesheet" href="/Fragancias Prime/public/css/style.css">
</head>

<body>

    <?php
    require __DIR__ . '../../vista/layouts/header.php';
    ?>

    <section id="heroCarousel" class="hero-carousel">

        <div class="carousel-inner">

            <div class="carousel-item active">
                <img src="/Fragancias Prime/public/img/Carrousel-Home/scandal.webp"
                    alt="Fragancia Scandal"
                    class="carousel-img">
            </div>

            <div class="carousel-item">
                <img src="/Fragancias Prime/public/img/Carrousel-Home/d&g.webp"
                    alt="Dolce & Gabbana"
                    class="carousel-img">
            </div>

            <div class="carousel-item">
                <img src="/Fragancias Prime/public/img/Carrousel-Home/swy.webp"
                    alt="Fragancia SWY"
                    class="carousel-img">
            </div>

        </div>

        <button class="carousel-control-prev">
            <span class="arrow-icon">❮</span>
        </button>
        <button class="carousel-control-next">
            <span class="arrow-icon">❯</span>
        </button>

        <div class="carousel-indicators">
            <button data-slide="0" class="active"></button>
            <button data-slide="1"></button>
            <button data-slide="2"></button>
        </div>

    </section>

    <!-- Productos destacados -->
    <section class="productos-section">
        <div class="container py-5">
            <h2 class="text-center mb-4">Productos Destacados</h2>

            <div class="row g-4">
                <?php if (!empty($productosSlider)): ?>
                    <?php foreach ($productosSlider as $producto): ?>
                        <div class="col-md-4 col-lg-3">
                            <div class="producto-card">
                                <div class="producto-img-container">
                                    <?php
                                    // Si tiene imagen desde slider_home, usarla
                                    $rutaImagen = '';
                                    if (!empty($producto['imagen'])) {
                                        $rutaImagen = '/Fragancias Prime/public/upload/productos/' . $producto['imagen'];
                                    } elseif (!empty($producto['pronombre'])) {
                                        // Si pronombre tiene ruta completa, usarla
                                        $rutaImagen = $producto['pronombre'];
                                    }
                                    ?>
                                    <img src="<?= htmlspecialchars($rutaImagen) ?>"
                                        alt="<?= htmlspecialchars($producto['prodetalle']) ?>"
                                        class="producto-img"
                                        onerror="this.src='/Fragancias Prime/public/img/no-image.png'">
                                </div>
                                <div class="producto-info">
                                    <?php
                                    // Usar subtitulo de slider_home o prodetalle como nombre
                                    $nombreProducto = '';
                                    if (!empty($producto['subtitulo']) && !is_numeric($producto['subtitulo'])) {
                                        $nombreProducto = $producto['subtitulo'];
                                    } elseif (!empty($producto['prodetalle']) && !is_numeric($producto['prodetalle'])) {
                                        $nombreProducto = $producto['prodetalle'];
                                    } elseif (!empty($producto['descripcion'])) {
                                        $nombreProducto = $producto['descripcion'];
                                    } else {
                                        $nombreProducto = 'Producto sin nombre';
                                    }
                                    ?>
                                    <h5 class="producto-nombre"><?= htmlspecialchars($nombreProducto) ?></h5>
                                    <p class="producto-precio">$<?= number_format($producto['precio'] ?? 0, 2, ',', '.') ?></p>
                                    
                                    <?php if (($producto['procantstock'] ?? 0) <= 0): ?>
                                        <p class="text-danger small mb-2">
                                            <i class="bi bi-exclamation-triangle"></i> Sin stock
                                        </p>
                                    <?php endif; ?>

                                    <div class="producto-actions">
                                        <a href="?controller=producto&action=ver&id=<?= $producto['idproducto'] ?>"
                                            class="btn btn-outline-dark btn-sm">
                                            <i class="bi bi-eye"></i> Ver detalle
                                        </a>
                                        
                                        <?php if (isset($_SESSION['usuario'])): ?>
                                            <?php if (($producto['procantstock'] ?? 0) > 0): ?>
                                                <form method="POST" action="?controller=carrito&action=agregar" class="d-inline">
                                                    <input type="hidden" name="idproducto" value="<?= $producto['idproducto'] ?>">
                                                    <input type="hidden" name="cantidad" value="1">
                                                    <button type="submit" class="btn btn-dark btn-sm">
                                                        <i class="bi bi-cart-plus"></i> Agregar
                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                <button class="btn btn-secondary btn-sm" disabled>
                                                    <i class="bi bi-x-circle"></i> Sin stock
                                                </button>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <a href="?controller=auth&action=login" class="btn btn-dark btn-sm">
                                                <i class="bi bi-box-arrow-in-right"></i> Login para comprar
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12">
                        <p class="text-center text-muted">No hay productos disponibles en este momento.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <script src="/Fragancias Prime/public/js/app.js"></script>

    <?php require_once __DIR__ . '../../vista/layouts/footer.php'; ?>

</body>

</html>