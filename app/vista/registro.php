<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Fragancias Prime</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/Fragancias Prime/public/css/style.css">
</head>
<body>

<?php require_once __DIR__ . '/layouts/header.php'; ?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            
            <h2 class="text-center mb-1">Crear cuenta</h2>
            <p class="text-center text-muted mb-4">Completa los datos para registrarte</p>

            <div class="card">
                <div class="card-body">
                    
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger">
                            <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($exito)): ?>
                        <div class="alert alert-success">
                            <?= htmlspecialchars($exito) ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="?controller=auth&action=registro">
                        
                        <div class="mb-3">
                            <label for="usuario" class="form-label">Usuario</label>
                            <input 
                                type="text" 
                                class="form-control" 
                                id="usuario" 
                                name="usuario" 
                                value="<?= htmlspecialchars($_POST['usuario'] ?? '') ?>"
                                required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input 
                                type="email" 
                                class="form-control" 
                                id="email" 
                                name="email" 
                                value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                                required>
                        </div>

                        <div class="mb-3">
                            <label for="clave" class="form-label">Contrasena</label>
                            <input 
                                type="password" 
                                class="form-control" 
                                id="clave" 
                                name="clave" 
                                required>
                            <small class="text-muted">Minimo 4 caracteres</small>
                        </div>

                        <div class="mb-3">
                            <label for="clave2" class="form-label">Confirmar contrasena</label>
                            <input 
                                type="password" 
                                class="form-control" 
                                id="clave2" 
                                name="clave2" 
                                required>
                        </div>

                        <button type="submit" class="btn btn-dark w-100 mb-3">
                            Registrarse
                        </button>

                        <div class="text-center small">
                            ¿Ya tenes cuenta? <a href="?controller=auth&action=login" class="text-decoration-none">Inicia sesion</a>
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>
</div>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>

</body>
</html>
