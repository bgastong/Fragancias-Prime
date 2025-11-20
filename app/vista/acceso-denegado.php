<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso Denegado - Fragancias Prime</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
</head>
<body class="bg-light">

<div class="container">
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-md-6 text-center">
            
            <div class="mb-4">
                <i class="bi bi-shield-exclamation text-danger" style="font-size: 6rem;"></i>
            </div>

            <h1 class="display-4 fw-bold text-danger mb-3">Acceso Denegado</h1>
            
            <p class="lead text-muted mb-4">
                No tenes permisos para acceder a esta seccion.
            </p>

            <div class="d-flex gap-3 justify-content-center">
                <a href="?controller=home&action=index" class="btn btn-dark btn-lg">
                    <i class="bi bi-house-door me-2"></i>Volver al Inicio
                </a>
                
                <?php if (!isset($_SESSION['usuario'])): ?>
                    <a href="?controller=auth&action=login" class="btn btn-outline-dark btn-lg">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Iniciar Sesion
                    </a>
                <?php endif; ?>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
