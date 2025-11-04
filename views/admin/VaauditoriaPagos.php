<?php
// Asegurar que el archivo se procese como PHP y que no haya contenido antes del primer <?php
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auditoría Financiera - FloralTech</title>
    <link rel="stylesheet" href="/assets/dashboard-admin.css">
</head>
<body>
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

    <!-- Filtros funcionales -->
    <div class="card mb-4">
        <div class="card-body pb-2">
            <div class="fw-bold fs-5 mb-2"><i class="fas fa-filter me-2"></i>Filtros de Auditoría</div>
            <form method="GET">
                <div class="row justify-content-center g-2 align-items-end">
                    <div class="col-md-2">
                        <label class="form-label">Fecha Inicio</label>
                        <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" value="<?= htmlspecialchars($_GET['fecha_inicio'] ?? '') ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Fecha Fin</label>
                        <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" value="<?= htmlspecialchars($_GET['fecha_fin'] ?? '') ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Estado</label>
                        <select class="form-select" id="estado" name="estado">
                            <option value="">Todos</option>
                            <option value="Aprobado">Aprobado</option>
                            <option value="Pendiente">Pendiente</option>
                            <option value="Fallido">Fallido</option>
                            <option value="Reembolsado">Reembolsado</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Tipo de Cuenta</label>
                        <select class="form-select" id="tipo_cuenta" name="tipo_cuenta">
                            <option value="">Todas</option>
                            <option value="Ventas">Ventas</option>
                            <option value="Costos">Costos</option>
                            <option value="Existencias">Existencias</option>
                            <option value="Cuentas por cobrar">Cuentas por cobrar</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Método de Pago</label>
                        <select class="form-select" id="metodo_pago" name="metodo_pago">
                            <option value="">Todos</option>
                            <option value="Efectivo">Efectivo</option>
                            <option value="Tarjeta">Tarjeta</option>
                            <option value="Transferencia">Transferencia</option>
                            <option value="PayPal">PayPal</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex gap-2 align-items-end">
                        <button type="submit" class="btn btn-primary px-4">Filtrar</button>
                        <a href="?" class="btn btn-link text-secondary">Limpiar Filtros</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Resumen y gráfico -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card text-center shadow-sm mb-3">
                <div class="card-body">
                    <h5 class="card-title">Total de Transacciones Auditadas</h5>
                    <p class="display-6 fw-bold text-primary mb-0"><?= $totalPagos ?></p>
                </div>
            </div>
            <div class="card text-center shadow-sm mb-3">
                <div class="card-body">
                    <h5 class="card-title">Monto Total Movido</h5>
                    <p class="display-6 fw-bold text-success mb-0">$<?= number_format($totalMonto, 2) ?></p>
                </div>
            </div>
            <div class="card text-center shadow-sm mb-3">
                <div class="card-body">
                    <h5 class="card-title">% Errores/Inconsistencias</h5>
                    <p class="display-6 fw-bold text-danger mb-0">
                        <?= $totalPagos > 0 ? round((($totalFallidos + $totalPendientes) / $totalPagos) * 100, 1) : 0 ?>%
                    </p>
                </div>
            </div>
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Existencias Valorizadas</h5>
                    <p class="display-6 fw-bold text-info mb-0">$<?= number_format(5000, 2) // Simulado ?></p>
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
                                <th>📅 Fecha</th>
                                <th>💲 Monto</th>
                                <th>🛒 Tipo de Cuenta</th>
                                <th>📌 Método de Pago</th>
                                <th>📊 Estado</th>
                                <th>👤 Cliente/Proveedor</th>
                                <th>🏷️ ID Transacción</th>
                                <th>📝 Responsable</th>
                                <th>🔍 Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($registros as $r): ?>
                            <tr class="<?php echo ($r['estado'] === 'Fallido') ? 'table-danger' : (($r['estado'] === 'Reembolsado') ? 'table-warning' : (($r['estado'] === 'Pendiente') ? 'table-info' : '')); ?>">
                                <td><?= $r['fecha'] ?></td>
                                <td>$<?= number_format($r['monto'], 2) ?></td>
                                <td><span class="badge bg-info">Ventas</span></td>
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
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="detalleModalLabel<?= $r['transaccion'] ?>">Detalle de Transacción <?= $r['transaccion'] ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-7">
                                <ul class="list-unstyled">
                                        <li><strong>Fecha:</strong> <?= $r['fecha'] ?></li>
                                        <li><strong>Monto:</strong> $<?= number_format($r['monto'], 2) ?></li>
                                        <li><strong>Método de pago:</strong> <?= $r['metodo'] ?></li>
                                        <li><strong>Estado:</strong> <?= $r['estado'] ?></li>
                                        <li><strong>Cliente/Proveedor:</strong> <?= htmlspecialchars($r['cliente']) ?></li>
                                        <li><strong>Responsable:</strong> <?= htmlspecialchars($r['responsable']) ?></li>
                                        <li><strong>ID Transacción:</strong> <?= $r['transaccion'] ?></li>
                                </ul>
                            </div>
                            <div class="col-md-5">
                                <div class="mb-2">
                                    <strong>Evidencia documental:</strong><br>
                                    <span class="text-muted">(Factura, recibo, registro en inventario)</span>
                                    <div class="border rounded p-2 mt-1 bg-light text-center">
                                        <i class="fas fa-file-invoice fa-2x text-secondary"></i><br>
                                        <span class="small">No disponible (demo)</span>
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <strong>Observaciones del auditor:</strong>
                                    <textarea class="form-control" rows="2" placeholder="Observaciones..."></textarea>
                                </div>
                                <div>
                                    <strong>Recomendación:</strong>
                                    <select class="form-select mt-1">
                                        <option>Ajuste</option>
                                        <option>Aprobación</option>
                                        <option>Seguimiento</option>
                                    </select>
                                </div>
                            </div>
                        </div>
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
// Cambiado a barras verticales
//
document.addEventListener('DOMContentLoaded', function() {
    var ctx = document.getElementById('graficoEstados').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Aprobados', 'Pendientes', 'Fallidos', 'Reembolsados'],
            datasets: [{
                label: 'Cantidad',
                data: [<?= $totalAprobados ?>, <?= $totalPendientes ?>, <?= $totalFallidos ?>, <?= $totalReembolsados ?>],
                backgroundColor: ['#198754', '#0dcaf0', '#dc3545', '#ffc107'],
                borderRadius: 8,
                maxBarThickness: 50
            }]
        },
        options: {
            plugins: {
                legend: { display: false },
                title: {
                    display: true,
                    text: 'Estados de Pagos',
                    font: { size: 18 }
                }
            },
            responsive: true,
            scales: {
                x: {
                    grid: { display: false },
                    title: { display: false }
                },
                y: {
                    beginAtZero: true,
                    grid: { color: '#eee' },
                    title: { display: true, text: 'Cantidad' },
                    ticks: { precision:0 }
                }
            }
        }
    });
});
</script>
