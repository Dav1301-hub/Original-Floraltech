<?php
// Vista para gestionar flores
if (!isset($_SESSION['user']) || $_SESSION['user']['tpusu_idtpusu'] != 1) {
    header('Location: index.php?ctrl=login&action=index');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestionar Flores - FloralTech</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/styles.css">
</head>
<body>
    <div class="container py-4">
        <h2 class="mb-4">Gestión de Flores</h2>
        <a href="index.php?ctrl=Cflor&action=add" class="btn btn-success mb-3">Agregar Flor</a>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Naturaleza</th>
                    <th>Descripción</th>
                    <th>Stock</th>
                    <th>Precio</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($flores as $flor): ?>
                <tr>
                    <td><?= $flor['idtflor'] ?></td>
                    <td><?= htmlspecialchars($flor['nombre']) ?></td>
                    <td><?= htmlspecialchars($flor['naturaleza']) ?></td>
                    <td><?= htmlspecialchars($flor['descripcion']) ?></td>
                    <td><?= $flor['stock'] ?></td>
                    <td>$<?= number_format($flor['precio'], 2) ?></td>
                    <td>
                        <a href="index.php?ctrl=Cflor&action=edit&id=<?= $flor['idtflor'] ?>" class="btn btn-warning btn-sm">Editar</a>
                        <a href="index.php?ctrl=Cflor&action=delete&id=<?= $flor['idtflor'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Seguro que deseas eliminar esta flor?')">Eliminar</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <a href="index.php?ctrl=dashboard&action=admin" class="btn btn-secondary mt-3">Volver al Dashboard</a>
    </div>
</body>
</html>
