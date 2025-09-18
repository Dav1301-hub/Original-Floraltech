<?php
if (!isset($_SESSION['user']) || $_SESSION['user']['tpusu_idtpusu'] != 1) {
    header('Location: index.php?ctrl=login&action=index');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Flor - FloralTech</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/styles.css">
</head>
<body>
    <div class="container py-4">
        <h2 class="mb-4">Editar Flor</h2>
        <form method="post">
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre</label>
                <input type="text" class="form-control" id="nombre" name="nombre" value="<?= htmlspecialchars($flor['nombre']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="naturaleza" class="form-label">Naturaleza</label>
                <input type="text" class="form-control" id="naturaleza" name="naturaleza" value="<?= htmlspecialchars($flor['naturaleza']) ?>">
            </div>
            <div class="mb-3">
                <label for="descripcion" class="form-label">Descripción</label>
                <textarea class="form-control" id="descripcion" name="descripcion"><?= htmlspecialchars($flor['descripcion']) ?></textarea>
            </div>
            <div class="mb-3">
                <label for="stock" class="form-label">Stock</label>
                <input type="number" class="form-control" id="stock" name="stock" min="0" value="<?= $flor['stock'] ?>" required>
            </div>
            <div class="mb-3">
                <label for="precio" class="form-label">Precio</label>
                <input type="number" step="0.01" class="form-control" id="precio" name="precio" min="0" value="<?= $flor['precio'] ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
            <a href="index.php?ctrl=FlorController&action=index" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</body>
</html>
