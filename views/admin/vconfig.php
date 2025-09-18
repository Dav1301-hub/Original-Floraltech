<?php
// Vista de configuración general para administrador
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Configuración General - Administrador</title>
    <link rel="stylesheet" href="../../assets/styles.css">
</head>
<body>
    <div class="container">
        <h2>Configuración General del Sistema</h2>
        <?php if (isset($mensaje) && $mensaje): ?>
            <div class="alert alert-<?= $tipo_mensaje ?>"> <?= htmlspecialchars($mensaje) ?> </div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label for="nombre_empresa" class="form-label">Nombre de la empresa</label>
                <input type="text" class="form-control" id="nombre_empresa" name="nombre_empresa" value="<?= htmlspecialchars($config['nombre_empresa'] ?? '') ?>" required>
            </div>
            <div class="mb-3">
                <label for="telefono" class="form-label">Teléfono</label>
                <input type="text" class="form-control" id="telefono" name="telefono" value="<?= htmlspecialchars($config['telefono'] ?? '') ?>">
            </div>
            <div class="mb-3">
                <label for="direccion" class="form-label">Dirección</label>
                <input type="text" class="form-control" id="direccion" name="direccion" value="<?= htmlspecialchars($config['direccion'] ?? '') ?>">
            </div>
            <div class="mb-3">
                <label for="email_contacto" class="form-label">Email de contacto</label>
                <input type="email" class="form-control" id="email_contacto" name="email_contacto" value="<?= htmlspecialchars($config['email_contacto'] ?? '') ?>">
            </div>
            <button type="submit" class="btn btn-primary">Guardar cambios</button>
        </form>
    </div>
</body>
</html>
