<?php
require_once __DIR__ . '/../../controllers/Cinventario.php';
$controller = new Cinventario();

$inventario = $controller->obtenerInventario();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventario - FloralTech</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/inventario.css">
    <link rel="stylesheet" href="../../assets/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container py-4">
        <h2 class="mb-4 text-primary">Inventario de Flores</h2>
        <div class="card">
            <div class="card-header bg-primary text-white">Listado de Inventario</div>
            <div class="card-body">
                <?php if (empty($inventario)): ?>
                    <div class="alert alert-warning">No hay productos en inventario.</div>
                <?php else: ?>
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Flor</th>
                                <th>Stock</th>
                                <th>Precio</th>
                                <th>Última actualización</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($inventario as $item): ?>
                                <tr>
                                    <td><?php echo $item['idinv']; ?></td>
                                    <td><?php echo $item['nombre_flor']; ?></td>
                                    <td><?php echo $item['stock']; ?></td>
                                    <td>$<?php echo number_format($item['precio'], 2); ?></td>
                                    <td><?php echo $item['fecha_actualizacion']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
