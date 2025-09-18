
<div class="container py-4">
    <h2 class="mb-4 text-center">Auditoría Financiera</h2>
    <?php
    // Inicializar la variable $pagos si no está definida
    $pagos = $pagos ?? [];
    // Datos simulados de auditoría
    $registros = [
        [
            'fecha' => '2025-08-28',
            'monto' => 120.50,
            'metodo' => 'Tarjeta',
            'estado' => 'Aprobado',
            'cliente' => 'Juan Pérez',
            'transaccion' => 'TX1001',
            'responsable' => 'Ana López'
        ],
        [
            'fecha' => '2025-08-28',
            'monto' => 75.00,
            'metodo' => 'Efectivo',
            'estado' => 'Fallido',
            'cliente' => 'Ana López',
            'transaccion' => 'TX1002',
            'responsable' => 'Juan Pérez'
        ],
        [
            'fecha' => '2025-08-27',
            'monto' => 200.00,
            'metodo' => 'PayPal',
            'estado' => 'Reembolsado',
            'cliente' => 'Carlos Ruiz',
            'transaccion' => 'TX1003',
            'responsable' => 'Ana López'
        ],
        [
            'fecha' => '2025-08-26',
            'monto' => 50.00,
            'metodo' => 'Tarjeta',
            'estado' => 'Pendiente',
            'cliente' => 'Lucía Gómez',
            'transaccion' => 'TX1004',
            'responsable' => 'Juan Pérez'
        ]
    ];
    $totalAprobados = count(array_filter($registros, fn($r) => $r['estado'] === 'Aprobado'));
    $totalFallidos = count(array_filter($registros, fn($r) => $r['estado'] === 'Fallido'));
    $totalReembolsados = count(array_filter($registros, fn($r) => $r['estado'] === 'Reembolsado'));
    $totalPendientes = count(array_filter($registros, fn($r) => $r['estado'] === 'Pendiente'));
    $totalPagos = count($registros);
    $totalMonto = array_sum(array_column($registros, 'monto'));
    ?>

    <!-- Filtros -->
    <div class="row mb-4">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">Filtros de Auditoría</div>
                <div class="card-body">
                    <form method="GET" class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label for="fecha_inicio" class="form-label">Fecha Inicio</label>
                            <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" value="<?= htmlspecialchars($_GET['fecha_inicio'] ?? '') ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="fecha_fin" class="form-label">Fecha Fin</label>
                            <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" value="<?= htmlspecialchars($_GET['fecha_fin'] ?? '') ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="estado" class="form-label">Estado</label>
                            <select class="form-select" id="estado" name="estado">
                                <option value="">Todos</option>
                                <option value="Completado" <?= (isset($_GET['estado']) && $_GET['estado'] === 'Completado') ? 'selected' : '' ?>>Completado</option>
                                <option value="Pendiente" <?= (isset($_GET['estado']) && $_GET['estado'] === 'Pendiente') ? 'selected' : '' ?>>Pendiente</option>
                                <option value="Cancelado" <?= (isset($_GET['estado']) && $_GET['estado'] === 'Cancelado') ? 'selected' : '' ?>>Cancelado</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary w-100">Filtrar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Resumen y gráfico -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card text-center shadow-sm mb-3">
                <div class="card-body">
                    <h5 class="card-title">Total Pagos</h5>
                    <p class="display-6 fw-bold text-primary mb-0"><?= $totalPagos ?></p>
                </div>
            </div>
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Monto Total</h5>
                    <p class="display-6 fw-bold text-success mb-0">$<?= number_format($totalMonto, 2) ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-8 d-flex align-items-center">
            <div class="w-100">
                <canvas id="graficoEstados" height="120"></canvas>
            </div>
        </div>
    </div>

    <!-- Tabla de registros -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-secondary text-white">Registros de Auditoría</div>
        <div class="card-body p-0">
            <?php if (empty($registros)): ?>
                <div class="alert alert-info m-4">
                    No se encontraron registros de pagos con los filtros seleccionados.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Fecha</th>
                                <th>Monto</th>
                                <th>Método</th>
                                <th>Estado</th>
                                <th>Cliente</th>
                                <th>ID Transacción</th>
                                <th>Responsable</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($registros as $r): ?>
                            <tr class="<?php echo ($r['estado'] === 'Fallido') ? 'table-danger' : (($r['estado'] === 'Reembolsado') ? 'table-warning' : (($r['estado'] === 'Pendiente') ? 'table-info' : '')); ?>">
                                <td><?= $r['fecha'] ?></td>
                                <td>$<?= number_format($r['monto'], 2) ?></td>
                                <td><span class="badge bg-dark"><?= $r['metodo'] ?></span></td>
                                <td><span class="badge bg-<?= $r['estado'] === 'Aprobado' ? 'success' : ($r['estado'] === 'Pendiente' ? 'info' : ($r['estado'] === 'Fallido' ? 'danger' : 'warning')) ?>"><?= $r['estado'] ?></span></td>
                                <td><?= htmlspecialchars($r['cliente']) ?></td>
                                <td><?= $r['transaccion'] ?></td>
                                <td><?= htmlspecialchars($r['responsable']) ?></td>
                                <td>
                                    <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#detalleModal<?= $r['transaccion'] ?>">Ver Detalle</button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modales de detalle -->
    <?php foreach ($registros as $r): ?>
    <div class="modal fade" id="detalleModal<?= $r['transaccion'] ?>" tabindex="-1" aria-labelledby="detalleModalLabel<?= $r['transaccion'] ?>" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="detalleModalLabel<?= $r['transaccion'] ?>">Detalle de Transacción <?= $r['transaccion'] ?></h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <ul class="list-unstyled">
                <li><strong>Fecha:</strong> <?= $r['fecha'] ?></li>
                <li><strong>Monto:</strong> $<?= number_format($r['monto'], 2) ?></li>
                <li><strong>Método de pago:</strong> <?= $r['metodo'] ?></li>
                <li><strong>Estado:</strong> <?= $r['estado'] ?></li>
                <li><strong>Cliente:</strong> <?= htmlspecialchars($r['cliente']) ?></li>
                <li><strong>Responsable:</strong> <?= htmlspecialchars($r['responsable']) ?></li>
                <li><strong>ID Transacción:</strong> <?= $r['transaccion'] ?></li>
            </ul>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
          </div>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Gráfico de estados de pagos
document.addEventListener('DOMContentLoaded', function() {
    var ctx = document.getElementById('graficoEstados').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Aprobados', 'Pendientes', 'Fallidos', 'Reembolsados'],
            datasets: [{
                data: [<?= $totalAprobados ?>, <?= $totalPendientes ?>, <?= $totalFallidos ?>, <?= $totalReembolsados ?>],
                backgroundColor: ['#198754', '#0dcaf0', '#dc3545', '#ffc107']
            }]
        },
        options: {
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
});
</script>
