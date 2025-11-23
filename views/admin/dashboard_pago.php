<?php
$pagoModel = $pagoModel ?? null;
$alertaPago = $alertaPago ?? null;

// Procesar cambio de estado enviado desde la tabla
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_pago'], $_POST['nuevo_estado'])) {
    try {
        require_once __DIR__ . '/../../models/conexion.php';
        require_once __DIR__ . '/../../models/PagoModel.php';

        $conexion = new conexion();
        $db = $conexion->get_conexion();
        $pagoModel = new PagoModel($db);

        $idPago = (int)$_POST['id_pago'];
        $nuevoEstado = trim($_POST['nuevo_estado']);
        $estadosPermitidos = ['Completado', 'Pendiente', 'Cancelado', 'Rechazado'];

        if (!$idPago || !in_array($nuevoEstado, $estadosPermitidos, true)) {
            throw new Exception('Datos de estado no válidos.');
        }

        if ($pagoModel->actualizarEstadoPago($idPago, $nuevoEstado)) {
            $alertaPago = ['tipo' => 'success', 'mensaje' => 'Estado del pago actualizado correctamente.'];
            // Forzar refresco de datos reales
            $estadisticas = [];
            $pagosRecientes = [];
            $resumenMetodosPago = [];
        } else {
            throw new Exception('No se pudo actualizar el estado.');
        }
    } catch (Exception $e) {
        error_log('Actualizar estado pago: ' . $e->getMessage());
        $alertaPago = ['tipo' => 'danger', 'mensaje' => 'No se pudo cambiar el estado del pago.'];
    }
}

// Carga datos reales desde el modelo si la vista se renderiza sin datos previos.
if (
    !isset($estadisticas) || !is_array($estadisticas) ||
    !isset($pagosRecientes) || !is_array($pagosRecientes) ||
    !isset($resumenMetodosPago) || !is_array($resumenMetodosPago)
) {
    try {
        require_once __DIR__ . '/../../models/conexion.php';
        require_once __DIR__ . '/../../models/PagoModel.php';

        $conexion = new conexion();
        $db = $conexion->get_conexion();
        $pagoModel = new PagoModel($db);

        if (!isset($estadisticas) || !is_array($estadisticas) || empty($estadisticas)) {
            $estadisticas = $pagoModel->obtenerEstadisticasPagos();
        }
        if (!isset($resumenMetodosPago) || !is_array($resumenMetodosPago) || empty($resumenMetodosPago)) {
            $resumenMetodosPago = $pagoModel->obtenerResumenMetodosPago();
        }
        if (!isset($pagosRecientes) || !is_array($pagosRecientes) || empty($pagosRecientes)) {
            $pagosRecientes = array_slice($pagoModel->obtenerTodosLosPagos(), 0, 10);
        }
    } catch (Exception $e) {
        error_log('Dashboard de pagos: ' . $e->getMessage());
        $estadisticas = is_array($estadisticas ?? null) ? $estadisticas : [];
        $pagosRecientes = is_array($pagosRecientes ?? null) ? $pagosRecientes : [];
        $resumenMetodosPago = is_array($resumenMetodosPago ?? null) ? $resumenMetodosPago : [];
    }
}

$estadisticas = is_array($estadisticas ?? null) ? $estadisticas : [];
$estadisticas = array_merge([
    'ingresos_totales' => 0,
    'pagos_completados' => 0,
    'pagos_pendientes' => 0,
    'pagos_rechazados' => 0,
    'total_pagos' => 0,
    'metodos_pago' => []
], $estadisticas);

$pagosRecientes = $pagosRecientes ?? [];
$resumenMetodosPago = $resumenMetodosPago ?? [];
?>

<div class="container py-4">
    <?php if ($alertaPago): ?>
        <div class="alert alert-<?= htmlspecialchars($alertaPago['tipo']) ?> alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($alertaPago['mensaje']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    <?php endif; ?>

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h2 class="fw-bold mb-1">Dashboard de Pagos</h2>
            <p class="text-muted mb-0">Visualiza el desempeño financiero con datos reales</p>
        </div>
        <a href="index.php?ctrl=dashboard&action=admin&page=reportes" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Volver a reportes
        </a>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-muted small text-uppercase mb-1">Ingresos totales</div>
                    <div class="display-6 text-primary fw-bold">$<?= number_format($estadisticas['ingresos_totales'], 2) ?></div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-muted small text-uppercase mb-1">Pagos completados</div>
                    <div class="display-6 text-success fw-bold"><?= number_format($estadisticas['pagos_completados']) ?></div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-muted small text-uppercase mb-1">Pagos pendientes</div>
                    <div class="display-6 text-warning fw-bold"><?= number_format($estadisticas['pagos_pendientes']) ?></div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-muted small text-uppercase mb-1">Total de registros</div>
                    <div class="display-6 text-info fw-bold"><?= number_format($estadisticas['total_pagos']) ?></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-white fw-bold">Distribución por método de pago</div>
                <div class="card-body">
                    <?php if (!empty($estadisticas['metodos_pago'])): ?>
                        <canvas id="metodosPagoChart" height="300"></canvas>
                    <?php else: ?>
                        <p class="text-center text-muted mb-0">Aún no hay pagos completados para graficar.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-white fw-bold">Detalle por método</div>
                <div class="card-body">
                    <?php if (!empty($resumenMetodosPago)): ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($resumenMetodosPago as $metodo): ?>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="fw-semibold text-capitalize"><?= htmlspecialchars($metodo['metodo_pago']) ?></div>
                                        <small class="text-muted"><?= number_format($metodo['cantidad']) ?> transacciones</small>
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-bold">$<?= number_format($metodo['total'], 2) ?></div>
                                        <small class="text-muted">Total recaudado</small>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-center text-muted mb-0">No hay datos para los métodos de pago.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white fw-bold d-flex justify-content-between align-items-center">
            <span>Pagos más recientes</span>
            <span class="badge bg-secondary"><?= count($pagosRecientes) ?> registros</span>
        </div>
        <div class="card-body p-0">
            <?php if (!empty($pagosRecientes)): ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Fecha</th>
                                <th>Cliente</th>
                                <th>Pedido</th>
                                <th>Método</th>
                                <th class="text-end">Monto</th>
                                <th>Estado</th>
                                <th class="text-center">Actualizar</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pagosRecientes as $pago): ?>
                                <tr>
                                    <td><?= htmlspecialchars($pago['idpago']) ?></td>
                                    <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($pago['fecha_pago']))) ?></td>
                                    <td><?= htmlspecialchars($pago['cliente']) ?></td>
                                    <td><?= htmlspecialchars($pago['numped']) ?></td>
                                    <td><?= htmlspecialchars($pago['metodo_pago']) ?></td>
                                    <td class="text-end">$<?= number_format($pago['monto'], 2) ?></td>
                                    <td>
                                        <?php
                                            $estado = strtolower($pago['estado_pag']);
                                            $badgeClass = 'bg-secondary';
                                            if ($estado === 'completado') {
                                                $badgeClass = 'bg-success';
                                            } elseif ($estado === 'pendiente') {
                                                $badgeClass = 'bg-warning text-dark';
                                            } elseif (in_array($estado, ['rechazado','reembolsado','cancelado'])) {
                                                $badgeClass = 'bg-danger';
                                            }
                                        ?>
                                        <span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($pago['estado_pag']) ?></span>
                                    </td>
                                    <td class="text-center">
                                        <form method="POST" class="d-flex align-items-center gap-2 justify-content-end">
                                            <input type="hidden" name="id_pago" value="<?= htmlspecialchars($pago['idpago']) ?>">
                                            <select name="nuevo_estado" class="form-select form-select-sm w-auto">
                                                <?php
                                                $opcionesEstado = ['Completado', 'Pendiente', 'Cancelado', 'Rechazado'];
                                                foreach ($opcionesEstado as $opcion):
                                                    $selected = ($pago['estado_pag'] === $opcion) ? 'selected' : '';
                                                ?>
                                                    <option value="<?= $opcion ?>" <?= $selected ?>><?= $opcion ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            <button type="submit" class="btn btn-sm btn-primary">
                                                <i class="fas fa-sync-alt me-1"></i>Guardar
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-center text-muted py-4 mb-0">Todavía no se han registrado pagos.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const dataset = <?= json_encode($estadisticas['metodos_pago'], JSON_UNESCAPED_UNICODE) ?>;
    if (!dataset || !dataset.length) {
        return;
    }

    const ctx = document.getElementById('metodosPagoChart');
    if (!ctx) {
        return;
    }

    const labels = dataset.map(item => item.metodo_pago);
    const data = dataset.map(item => Number(item.cantidad));

    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels,
            datasets: [{
                data,
                backgroundColor: ['#6a5af9','#4ade80','#facc15','#f87171','#60a5fa'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
});
</script>
