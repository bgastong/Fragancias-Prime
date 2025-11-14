<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Fragancias Prime - Home</title>
    <style>
        body { font-family: Arial; background: #f7f7f7; padding: 30px; }
        .card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            max-width: 600px;
            margin: auto;
            box-shadow: 0 0 10px #bbb;
        }
        .menu a {
            margin-right: 15px;
            text-decoration: none;
            font-weight: bold;
            color: #333;
        }
        .menu a:hover { color: #007bff; }
    </style>
</head>
<body>

<div class="card">

    <h1>Fragancias Prime</h1>

    <!-- MENÚ DE NAVEGACIÓN -->
    <div class="menu">
        <a href="?controller=home&action=index">Inicio</a>
        <a href="?controller=producto&action=listar">Perfumes</a>

        <?php if (!$usuario): ?>
            <!-- Si NO está logueado -->
            <a href="?controller=auth&action=login">Iniciar sesión</a>
            <a href="?controller=auth&action=registrarse">Registrarse</a>
        <?php else: ?>
            <!-- Si está logueado -->
            <a href="?controller=carrito&action=ver">Mi carrito</a>
            <a href="?controller=compra&action=misCompras">Mis compras</a>
            <a href="?controller=auth&action=logout">Cerrar sesión</a>
        <?php endif; ?>
    </div>

    <hr>

    <!-- CONTENIDO PRINCIPAL -->
    <?php if ($usuario): ?>
        <h2>Bienvenido, <?= htmlspecialchars($usuario['usnombre']) ?> 👋</h2>
        <p>Explorá nuestros productos y encontrá tu fragancia ideal.</p>
    <?php else: ?>
        <h2>Bienvenido a Fragancias Prime 👑</h2>
        <p>Iniciá sesión para ver tus compras y tu carrito.</p>
    <?php endif; ?>

</div>

</body>
</html>
