<?php 

require __DIR__ . '/layouts/header.php'; ?>

<h2>Catalogo de perfumes</h2>

<?php if (!empty($productos)): ?>
    <ul>
        <?php foreach ($productos as $p): ?>
            <li>
                <strong><?= htmlspecialchars($p['nombre']) ?></strong>
                (<?= htmlspecialchars($p['marca']) ?>) -
                $<?= number_format($p['precio'], 2) ?><br>
                <small><?= htmlspecialchars($p['descripcion']) ?></small>
            </li>
        <?php endforeach; ?>
    </ul>
<?php else: ?>
    <p>No hay perfumes cargados todavía.</p>
<?php endif; ?>

<?php require __DIR__ . '/layouts/footer.php'; ?>
