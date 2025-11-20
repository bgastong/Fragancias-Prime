<?php require_once __DIR__ . '/layouts/header.php'; ?>

<div class="container d-flex justify-content-center align-items-center" style="min-height: 70vh;">
    <div class="card shadow p-4" style="max-width: 420px; width: 100%;">

        <h3 class="text-center mb-4 fw-bold">Iniciar Sesión</h3>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger text-center">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form action="?controller=auth&action=login" method="POST">

            <!-- Usuario -->
            <div class="mb-3">
                <label class="form-label">Usuario</label>
                <input 
                    type="text"
                    name="usuario"
                    class="form-control"
                    value="<?= isset($_COOKIE['user_saved']) ? htmlspecialchars($_COOKIE['user_saved']) : '' ?>"
                    required
                >
            </div>

            <!-- Contraseña -->
            <div class="mb-3">
                <label class="form-label">Contraseña</label>
                <input 
                    type="password"
                    name="clave"
                    class="form-control"
                    required
                >
            </div>

            <!-- Recordarme -->
            <div class="form-check mb-3">
                <input 
                    class="form-check-input" 
                    type="checkbox" 
                    name="recordarme" 
                    id="recordarme"
                    <?= isset($_COOKIE['user_saved']) ? 'checked' : '' ?>
                >
                <label class="form-check-label" for="recordarme">
                    Recordarme
                </label>
            </div>

            <!-- Botón -->
            <button type="submit" class="btn btn-dark w-100 mb-3">
                Ingresar
            </button>

            <div class="text-center small">
                ¿No tenes cuenta? <a href="?controller=auth&action=registro" class="text-decoration-none fw-semibold">Registrate aca</a>
            </div>

        </form>
    </div>
</div>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>
