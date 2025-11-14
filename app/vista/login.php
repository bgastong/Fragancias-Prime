<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login - Fragancias Prime</title>
</head>
<body>

    <h1>Iniciar sesión</h1>

    <?php if (!empty($error)): ?>
        <p style="color:red;">Usuario o contraseña incorrectos.</p>
    <?php endif; ?>

    <form action="?controller=auth&action=procesarLogin" method="POST">
        
        <label>Usuario:</label>
        <input type="text" name="usuario" 
                value="<?= htmlspecialchars($usuarioRecordado ?? '') ?>" required>
        <br><br>

        <label>Contraseña:</label>
        <input type="password" name="clave" required>
        <br><br>

        <label>
            <input type="checkbox" name="recordar"
                <?= !empty($usuarioRecordado) ? 'checked' : '' ?>>
            Recordarme
        </label>
        <br><br>

        <button type="submit">Ingresar</button>
    </form>

</body>
</html>
