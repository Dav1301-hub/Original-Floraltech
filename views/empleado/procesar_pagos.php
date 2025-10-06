<?php
// Vista para procesar pagos de pedidos
require_once __DIR__ . '/../../controllers/ProcesarPagosController.php';
$controller = new ProcesarPagosController();

// Procesar pago si se envía el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pago_id'])) {
    $pagoId = $_POST['pago_id'];
    $resultado = $controller->procesarPago($pagoId);
}

// Obtener pagos pendientes
$pagos = $controller->obtenerPagosPendientes();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Procesar Pagos - FloralTech</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/dashboard-general.css">
    <link rel="stylesheet" href="../../assets/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container py-4">
        <h2 class="mb-4 text-success">Procesar Pagos</h2>
        <?php if (isset($resultado)): ?>
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <?php echo $resultado ? 'Pago procesado correctamente.' : 'Error al procesar el pago.'; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <div class="card">
            <div class="card-header bg-success text-white">Todos los Pagos</div>
            <div class="card-body">
                <?php if (empty($pagos)): ?>
                    <div class="alert alert-warning">No hay pagos registrados.</div>
                <?php else: ?>
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID Pago</th>
                                <th>Cliente</th>
                                <th>Monto</th>
                                <th>Método</th>
                                <th>Estado</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pagos as $pago): ?>
                                <tr>
                                    <td><?php echo $pago['idpago']; ?></td>
                                    <td><?php echo $pago['cliente']; ?></td>
                                    <td>$<?php echo number_format($pago['monto'], 2); ?></td>
                                    <td><?php echo htmlspecialchars($pago['metodo_pago']); ?></td>
                                    <td>
                                        <?php if ($pago['estado_pag'] === 'Pendiente'): ?>
                                            <span class="badge bg-warning">Pendiente</span>
                                        <?php elseif ($pago['estado_pag'] === 'Procesando'): ?>
                                            <span class="badge bg-info text-dark">Procesando</span>
                                        <?php elseif ($pago['estado_pag'] === 'Completado'): ?>
                                            <span class="badge bg-success">Completado</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary"><?php echo htmlspecialchars($pago['estado_pag']); ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($pago['estado_pag'] !== 'Completado'): ?>
                                            <form method="POST">
                                                <input type="hidden" name="pago_id" value="<?php echo $pago['idpago']; ?>">
                                                <button type="submit" class="btn btn-success btn-sm">Procesar</button>
                                            </form>
                                        <?php else: ?>
                                            <span class="text-muted">Sin acción</span>
                                        <?php endif; ?>
                                    </td>
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
