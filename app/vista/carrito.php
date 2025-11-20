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
                $subtotal = $p['proprecio'] * $p['cantidad'];
                $total += $subtotal;
            ?>

                <div class="carrito-item">

                    <div class="item-img">
                        <img src="/public/img/productos/<?php echo $p['idproducto']; ?>.webp" alt="">
                    </div>

                    <div class="item-info">
                        <h2><?php echo htmlspecialchars($p['pronombre']); ?></h2>
                        <p><?php echo htmlspecialchars($p['prodetalle']); ?></p>

                        <div class="item-cantidad">
                            <span>Cantidad: <?php echo $p['cantidad']; ?></span>
                        </div>
                    </div>

                    <div class="item-precio">
                        <span>$<?php echo number_format($subtotal, 2, ',', '.'); ?></span>
                        <a href="/carrito/quitar/<?php echo $p['idproducto']; ?>" class="btn-quitar">Quitar</a>
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
                onclick="return confirm('¿Estás seguro de vaciar el carrito?')">
                Vaciar carrito
            </a>
            <a href="?controller=carrito&action=finalizarCompra" class="btn-finalizar">
                Finalizar compra
            </a>
        </div>

    <?php endif; ?>

</div>

<?php require __DIR__ . '/layouts/footer.php'; ?>