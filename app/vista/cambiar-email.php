<?php require __DIR__ . '/layouts/header.php'; ?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <h3 class="mb-4">Cambiar Email</h3>
            
            <?php if (isset($_SESSION['mensaje_error'])): ?>
                <div class="alert alert-danger">
                    <?= $_SESSION['mensaje_error'] ?>
                </div>
                <?php unset($_SESSION['mensaje_error']); ?>
            <?php endif; ?>

            <p class="text-muted mb-4">Email actual: <strong><?= htmlspecialchars($usuario['usmail']) ?></strong></p>

            <form method="POST" action="?controller=usuario&action=cambiarEmail">
                
                <div class="mb-3">
                    <label class="form-label">Nuevo Email</label>
                    <input type="email" class="form-control" name="email_nuevo" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Contrasena</label>
                    <input type="password" class="form-control" name="password" required>
                </div>

                <button type="submit" class="btn btn-dark w-100 mb-2">Actualizar</button>
                <a href="?controller=home&action=index" class="btn btn-outline-secondary w-100">Cancelar</a>
            </form>
        </div>
    </div>
</div>

<?php require __DIR__ . '/layouts/footer.php'; ?>
