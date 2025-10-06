<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Pagos - FloralTech</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/dgemp.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid">
        <!-- Header -->
        <div class="header">
            <h1><i class="fas fa-credit-card"></i> Gestión de Pagos</h1>
            <div class="header-actions">
                <a href="index.php?ctrl=empleado&action=index" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver al Dashboard
                </a>
            </div>
        </div>

        <!-- Mensajes -->
        <?php if (isset($_SESSION['mensaje'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= $_SESSION['mensaje'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['mensaje']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= $_SESSION['error'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <!-- Estadísticas rápidas -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="stats-card">
                    <i class="fas fa-clock text-warning"></i>
                    <h3><?= count($pagosPendientes) ?></h3>
                    <p>Pagos Pendientes</p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="stats-card">
                    <i class="fas fa-check-circle text-success"></i>
                    <h3><?= count($pagosCompletados) ?></h3>
                    <p>Pagos Completados</p>
                </div>
            </div>
        </div>

        <!-- Tabs para pagos -->
        <div class="card">
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs" id="pagosTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="pendientes-tab" data-bs-toggle="tab" data-bs-target="#pendientes" type="button" role="tab">
                            <i class="fas fa-clock"></i> Pendientes (<?= count($pagosPendientes) ?>)
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="completados-tab" data-bs-toggle="tab" data-bs-target="#completados" type="button" role="tab">
                            <i class="fas fa-check-circle"></i> Completados (<?= count($pagosCompletados) ?>)
                        </button>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content" id="pagosTabsContent">
                    <!-- Tab de Pagos Pendientes -->
                    <div class="tab-pane fade show active" id="pendientes" role="tabpanel">
                        <?php if (empty($pagosPendientes)): ?>
                            <div class="text-center py-4">
                                <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                                <h5>¡Excelente!</h5>
                                <p class="text-muted">No hay pagos pendientes de procesar.</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID Pago</th>
                                            <th>Pedido</th>
                                            <th>Cliente</th>
                                            <th>Método</th>
                                            <th>Monto</th>
                                            <th>Fecha</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($pagosPendientes as $pago): ?>
                                            <tr>
                                                <td><?= $pago['idpago'] ?></td>
                                                <td><?= $pago['numped'] ?></td>
                                                <td><?= $pago['cliente'] ?></td>
                                                <td>
                                                    <span class="badge bg-info">
                                                        <?= ucfirst($pago['metodo_pago']) ?>
                                                    </span>
                                                </td>
                                                <td>$<?= number_format($pago['monto'], 2) ?></td>
                                                <td><?= date('d/m/Y H:i', strtotime($pago['fecha_pago'])) ?></td>
                                                <td>
                                                    <form method="POST" action="index.php?ctrl=empleadoPagos&action=actualizarEstado" style="display: inline;">
                                                        <input type="hidden" name="id_pago" value="<?= $pago['idpago'] ?>">
                                                        <input type="hidden" name="nuevo_estado" value="Completado">
                                                        <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('¿Confirmar el pago como completado?')">
                                                            <i class="fas fa-check"></i> Completar
                                                        </button>
                                                    </form>
                                                    <form method="POST" action="index.php?ctrl=empleadoPagos&action=actualizarEstado" style="display: inline;">
                                                        <input type="hidden" name="id_pago" value="<?= $pago['idpago'] ?>">
                                                        <input type="hidden" name="nuevo_estado" value="Rechazado">
                                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Rechazar este pago?')">
                                                            <i class="fas fa-times"></i> Rechazar
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Tab de Pagos Completados -->
                    <div class="tab-pane fade" id="completados" role="tabpanel">
                        <?php if (empty($pagosCompletados)): ?>
                            <div class="text-center py-4">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <h5>Sin pagos completados</h5>
                                <p class="text-muted">No se han completado pagos recientemente.</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID Pago</th>
                                            <th>Pedido</th>
                                            <th>Cliente</th>
                                            <th>Método</th>
                                            <th>Monto</th>
                                            <th>Fecha</th>
                                            <th>Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($pagosCompletados as $pago): ?>
                                            <tr>
                                                <td><?= $pago['idpago'] ?></td>
                                                <td><?= $pago['numped'] ?></td>
                                                <td><?= $pago['cliente'] ?></td>
                                                <td>
                                                    <span class="badge bg-info">
                                                        <?= ucfirst($pago['metodo_pago']) ?>
                                                    </span>
                                                </td>
                                                <td>$<?= number_format($pago['monto'], 2) ?></td>
                                                <td><?= date('d/m/Y H:i', strtotime($pago['fecha_pago'])) ?></td>
                                                <td>
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-check"></i> Completado
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-dismiss alerts after 5 seconds
        setTimeout(function() {
            var alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                var bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>
</body>
</html>