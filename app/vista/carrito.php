<?php require __DIR__ . '/layouts/header.php'; ?>

<link rel="stylesheet" href="/public/css/carrito.css">

<div class="carrito-container">

    <h1 class="carrito-title">Tu carrito</h1>

    <?php if (empty($productos)): ?>
        <div class="carrito-vacio">
            <p>Tu carrito esta vacio.</p>
            <a href="?controller=home&action=index" class="btn-volver">Ver productos</a>
        </div>
    <?php else: ?>

        <div class="carrito-items">

            <?php
            $total = 0;
            foreach ($productos as $p):
                $precio = $p['precio'] ?? 0;
                $subtotal = $precio * $p['cantidad'];
                $total += $subtotal;
            ?>

                <div class="carrito-item">

                    <div class="item-img">
                        <?php
                        // Construir ruta de imagen
                        $rutaImagen = '';
                        if (!empty($p['imagen'])) {
                            $rutaImagen = '/Fragancias Prime/public/upload/productos/' . $p['imagen'];
                        } elseif (!empty($p['pronombre'])) {
                            $rutaImagen = $p['pronombre'];
                        }
                        ?>
                        <img src="<?= htmlspecialchars($rutaImagen) ?>"
                            alt="<?= htmlspecialchars($p['prodetalle']) ?>"
                            onerror="this.src='/Fragancias Prime/public/img/no-image.png'">
                    </div>

                    <div class="item-info">
                        <h2><?php echo htmlspecialchars($p['prodetalle']); ?></h2>
                        <p class="precio-unitario">Precio: $<?php echo number_format($precio, 2, ',', '.'); ?></p>

                        <div class="item-cantidad">
                            <span>Cantidad: <?php echo $p['cantidad']; ?></span>
                        </div>
                    </div>

                    <div class="item-precio">
                        <span>$<?php echo number_format($subtotal, 2, ',', '.'); ?></span>
                        <form method="POST" action="?controller=carrito&action=quitar" style="margin-top: 0.5rem;">
                            <input type="hidden" name="idproducto" value="<?php echo $p['idproducto']; ?>">
                            <button type="submit" class="btn-quitar">Quitar</button>
                        </form>
                    </div>

                </div>

            <?php endforeach; ?>

        </div>

        <div class="carrito-total">
            <h3>Total:</h3>
            <span class="total-valor">$<?php echo number_format($total, 2, ',', '.'); ?></span>
        </div>

        <div class="carrito-acciones">
            <a href="?controller=carrito&action=vaciar" class="btn-vaciar"
                onclick="return confirm('Estas seguro de vaciar el carrito?')">
                Vaciar carrito
            </a>
            <a href="?controller=carrito&action=finalizarCompra" class="btn-finalizar">
                Finalizar compra
            </a>
        </div>

    <?php endif; ?>

</div>

<?php require __DIR__ . '/layouts/footer.php'; ?>