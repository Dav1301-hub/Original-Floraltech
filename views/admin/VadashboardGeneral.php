<?php
// Obtener datos reales del dashboard general mediante el controller
if (!isset($dashboardData) || !is_array($dashboardData)) {
    // Solo ejecutar el controller si no tenemos datos (evitar duplicaci+�n)
    require_once __DIR__ . '/../../controllers/CDashboardGeneral.php';
    
    try {
        $dashboardController = new CDashboardGeneral();
        $dashboardData = $dashboardController->getDashboardData();
    } catch (Exception $e) {
        // Si hay error, usar datos por defecto para mantener funcionalidad
        error_log("Error en Dashboard General: " . $e->getMessage());
        $dashboardData = [
            'totalPagos' => 0,
            'pagosPendientes' => 0,
            'pagosRechazados' => 0,
            'usuariosRegistrados' => 0,
            'nuevosUsuarios' => 0,
            'tendenciaPagos' => 0,
            'tendenciaPendientes' => 0,
            'tendenciaRechazados' => 0,
            'actividadReciente' => [],
            'resumenPedidosMes' => [
                'pedidosMes' => 0,
                'pedidosCompletados' => 0,
                'pedidosPendientes' => 0,
                'pedidosEnProceso' => 0,
                'pedidosCancelados' => 0,
                'mesReferencia' => date('m/Y')
            ],
            'ingresosMes' => 0,
            'tendenciaIngresos' => 0,
            'tasaConversion' => 0,
            'tendenciaConversion' => 0,
            'entregasProximas' => [
                'hoy' => [],
                'manana' => [],
                'cantidadHoy' => 0,
                'cantidadManana' => 0
            ],
            'tendenciaVentas' => [],
            'topProductos' => [],
            'periodos' => [],
            'filtro' => ['mes' => null, 'ano' => null]
        ];
    }
}
?>


<?php
// filepath: c:\xampp\htdocs\Floraltech\views\admin\dashboard_general.php
// Este archivo solo recibe $dashboardData desde el controlador

$totalPagos = $dashboardData['totalPagos'];
$pagosPendientes = $dashboardData['pagosPendientes'];
$pagosRechazados = $dashboardData['pagosRechazados'];
$usuariosRegistrados = $dashboardData['usuariosRegistrados'];
$nuevosUsuarios = $dashboardData['nuevosUsuarios'];
$ingresosMes = $dashboardData['ingresosMes'];
$tasaConversion = $dashboardData['tasaConversion'];
$tendenciaPagos = $dashboardData['tendenciaPagos'];
$tendenciaPendientes = $dashboardData['tendenciaPendientes'];
$tendenciaRechazados = $dashboardData['tendenciaRechazados'];
$tendenciaIngresos = $dashboardData['tendenciaIngresos'];
$tendenciaConversion = $dashboardData['tendenciaConversion'];
$actividadReciente = $dashboardData['actividadReciente'];
$pedidosMes = $dashboardData['resumenPedidosMes']['pedidosMes'];
$pedidosCompletados = $dashboardData['resumenPedidosMes']['pedidosCompletados'];
$pedidosPendientesMes = $dashboardData['resumenPedidosMes']['pedidosPendientes'];
$pedidosEnProcesoMes = $dashboardData['resumenPedidosMes']['pedidosEnProceso'];
$pedidosCanceladosMes = $dashboardData['resumenPedidosMes']['pedidosCancelados'];
$mesReferencia = $dashboardData['resumenPedidosMes']['mesReferencia'] ?? date('m/Y');
$entregasProximas = $dashboardData['entregasProximas'];
$tendenciaVentas = $dashboardData['tendenciaVentas'];
$topProductos = $dashboardData['topProductos'] ?? [];
$periodos = $dashboardData['periodos'] ?? [];
$filtro = $dashboardData['filtro'] ?? ['mes' => null, 'ano' => null];

// Obtener lotes pr+�ximos a caducar
require_once __DIR__ . '/../../models/Mlotes.php';
$lotesModel = new Mlotes();
$lotesProximosCaducar = $lotesModel->getLotesProximosCaducar(7);
$cantidadAlertasLotes = count($lotesProximosCaducar);
?>
<div id="general-dashboard" class="dashboard-main">
    <header class="d-flex flex-wrap align-items-start align-items-md-center justify-content-between mb-4 p-4 rounded-4 shadow-sm text-white" style="background: linear-gradient(120deg, #0d6efd 0%, #5b21b6 50%, #1e1b4b 100%);">
        <div>
            <p class="mb-1 text-uppercase fw-semibold small opacity-75" style="color: #ffff">Panel general</p>
            <h1 class="mb-1 fw-bold" style="color: #ffff">Dashboard General</h1>
            <p class="welcome-text mb-0 opacity-75" style="color: #ffff">Bienvenido al sistema de administracion de FloralTech</p>
        </div>
        <div class="d-flex flex-wrap gap-2 mt-3 mt-md-0 align-items-center">
            <form action="index.php" method="GET" class="d-flex gap-2" id="filter-form">
                <input type="hidden" name="ctrl" value="dashboard">
                <input type="hidden" name="action" value="admin">
                <input type="hidden" name="page" value="general">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-0 opacity-75"><i class="fas fa-filter text-primary"></i></span>
                    <select name="periodo" class="form-select border-0 shadow-sm fw-medium" onchange="this.form.submit()" style="min-width: 160px; cursor: pointer;">
                        <?php 
                        $mesFiltro = $filtro['mes'] ?? null;
                        $anoFiltro = $filtro['ano'] ?? null;
                        foreach ($periodos as $p): 
                            $val = $p['mes'] . '-' . $p['ano'];
                            $sel = ($mesFiltro == $p['mes'] && $anoFiltro == $p['ano']) ? 'selected' : '';
                            $nombreMes = date('F', mktime(0, 0, 0, $p['mes'], 10));
                            $mesesES = ['January'=>'Enero', 'February'=>'Febrero', 'March'=>'Marzo', 'April'=>'Abril', 'May'=>'Mayo', 'June'=>'Junio', 'July'=>'Julio', 'August'=>'Agosto', 'September'=>'Septiembre', 'October'=>'Octubre', 'November'=>'Noviembre', 'December'=>'Diciembre'];
                            $label = ($mesesES[$nombreMes] ?? $nombreMes) . ' ' . $p['ano'];
                        ?>
                            <option value="<?= $val ?>" <?= $sel ?>><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </form>
            <a class="btn btn-light btn-sm text-primary fw-semibold" href="index.php?ctrl=cinventario"><i class="fas fa-boxes me-2"></i>Inventario</a>
            <a class="btn btn-outline-light btn-sm" href="#calendar-pedidos"><i class="fas fa-calendar-alt me-2"></i>Agenda</a>
        </div>
    </header>

    <div class="row mb-4 g-3">
        <div class="col-lg col-md-4 col-sm-6">
            <div class="metric-card metric-purple h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <small>Total Pagos</small>
                        <h3 class="mb-1"><?= $totalPagos ?></h3>
                        <div class="trend <?= $tendenciaPagos >= 0 ? 'up' : 'down' ?>">
                            <i class="fas fa-arrow-<?= $tendenciaPagos >= 0 ? 'up' : 'down' ?>"></i> <?= abs($tendenciaPagos) ?>%
                        </div>
                    </div>
                    <span class="metric-icon"><i class="fas fa-credit-card"></i></span>
                </div>
            </div>
        </div>
        <div class="col-lg col-md-4 col-sm-6">
            <div class="metric-card metric-yellow h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <small>Pagos Pendientes</small>
                        <h3 class="mb-1"><?= $pagosPendientes ?></h3>
                        <div class="trend <?= $tendenciaPendientes >= 0 ? 'up' : 'down' ?>">
                            <i class="fas fa-arrow-<?= $tendenciaPendientes >= 0 ? 'up' : 'down' ?>"></i> <?= abs($tendenciaPendientes) ?>%
                        </div>
                    </div>
                    <span class="metric-icon"><i class="fas fa-clock"></i></span>
                </div>
            </div>
        </div>
        <div class="col-lg col-md-4 col-sm-6">
            <div class="metric-card metric-pink h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <small>Pagos Rechazados</small>
                        <h3 class="mb-1"><?= $pagosRechazados ?></h3>
                        <div class="trend <?= $tendenciaRechazados <= 0 ? 'up' : 'down' ?>">
                            <i class="fas fa-arrow-<?= $tendenciaRechazados >= 0 ? 'up' : 'down' ?>"></i> <?= abs($tendenciaRechazados) ?>%
                        </div>
                    </div>
                    <span class="metric-icon"><i class="fas fa-ban"></i></span>
                </div>
            </div>
        </div>
        <div class="col-lg col-md-4 col-sm-6">
            <div class="metric-card metric-green h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <small>Ingresos del Mes</small>
                        <h3 class="mb-1">$<?= number_format($ingresosMes, 0, ',', '.') ?></h3>
                        <div class="trend <?= $tendenciaIngresos >= 0 ? 'up' : 'down' ?>">
                            <i class="fas fa-arrow-<?= $tendenciaIngresos >= 0 ? 'up' : 'down' ?>"></i> <?= abs($tendenciaIngresos) ?>%
                        </div>
                    </div>
                    <span class="metric-icon"><i class="fas fa-dollar-sign"></i></span>
                </div>
            </div>
        </div>
        <div class="col-lg col-md-4 col-sm-6">
            <div class="metric-card metric-purple h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <small>Usuarios Registrados</small>
                        <h3 class="mb-1"><?= $usuariosRegistrados ?></h3>
                        <div class="trend up"><i class="fas fa-user"></i></div>
                    </div>
                    <span class="metric-icon"><i class="fas fa-users"></i></span>
                </div>
            </div>
        </div>
        <div class="col-lg col-md-4 col-sm-6">
            <div class="metric-card metric-yellow h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <small>Pedidos Exitosos</small>
                        <h3 class="mb-1"><?= $tasaConversion ?>%</h3>
                        <div class="trend <?= $tendenciaConversion >= 0 ? 'up' : 'down' ?>">
                            <i class="fas fa-arrow-<?= $tendenciaConversion >= 0 ? 'up' : 'down' ?>"></i> <?= abs($tendenciaConversion) ?>%
                        </div>
                    </div>
                    <span class="metric-icon"><i class="fas fa-check-circle"></i></span>
                </div>
            </div>
        </div>
    </div>

    <?php if ($cantidadAlertasLotes > 0): ?>
    <div class="dash-card p-3 mb-4 shadow-sm border-0" style="border-left: 6px solid #dc3545;">
        <div class="d-flex align-items-start justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 56px; height: 56px; background: rgba(220,53,69,.12);">
                    <i class="fas fa-exclamation-triangle text-danger fs-4"></i>
                </div>
                <div>
                    <h5 class="mb-1 fw-bold text-danger"><?= $cantidadAlertasLotes ?> <?= $cantidadAlertasLotes === 1 ? 'lote proximo a caducar' : 'lotes proximos a caducar' ?></h5>
                    <p class="mb-0 text-muted">Caducan en los proximos 7 dias. Revisa inventario para evitar perdidas.</p>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-danger-subtle text-danger fw-semibold"><?= $cantidadAlertasLotes ?> alertas</span>
                <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#modal-alertas-caducidad">
                    <i class="fas fa-list-ul me-1"></i>Ver detalles
                </button>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="dash-card p-3 h-100">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <div class="section-title mb-1">Ventas y pedidos</div>
                        <small class="text-muted">Mes <?= $mesReferencia ?></small>
                    </div>
                    <span class="badge bg-light text-dark">Actualizado hoy</span>
                </div>
                <div style="height: 320px;">
                    <canvas id="chartVentas"></canvas>
                </div>
                <div class="row g-3 pt-3">
                    <div class="col-sm-4">
                        <div class="mini-stat">
                            <span class="text-muted">Ingresos mes</span>
                            <span class="fw-bold">$<?= number_format($ingresosMes, 0, ',', '.') ?></span>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="mini-stat">
                            <span class="text-muted">Pedidos mes</span>
                            <span class="fw-bold"><?= $pedidosMes ?></span>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="mini-stat">
                            <span class="text-muted">Conversion</span>
                            <span class="fw-bold"><?= $tasaConversion ?>%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <?php $totalPedidosBase = $pedidosMes > 0 ? $pedidosMes : 1; ?>
            <div class="dash-card p-3 h-100">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="section-title mb-0">Estado de pedidos</div>
                    <span class="badge bg-light text-dark">Mes <?= $mesReferencia ?></span>
                </div>
                <div class="mini-stat">
                    <div><i class="fas fa-check-circle text-success me-2"></i>Completados</div>
                    <div class="fw-bold"><?= $pedidosCompletados ?></div>
                </div>
                <div class="progress-slim mb-2"><div class="progress-slim-bar" style="width: <?= min(100, ($pedidosCompletados / $totalPedidosBase) * 100) ?>%;"></div></div>

                <div class="mini-stat">
                    <div><i class="fas fa-clock text-warning me-2"></i>Pendientes</div>
                    <div class="fw-bold"><?= $pedidosPendientesMes ?></div>
                </div>
                <div class="progress-slim mb-2"><div class="progress-slim-bar" style="width: <?= min(100, ($pedidosPendientesMes / $totalPedidosBase) * 100) ?>%; background: linear-gradient(90deg, #f97316, #f59e0b);"></div></div>

                <div class="mini-stat">
                    <div><i class="fas fa-cog text-info me-2"></i>En proceso</div>
                    <div class="fw-bold"><?= $pedidosEnProcesoMes ?></div>
                </div>
                <div class="progress-slim mb-2"><div class="progress-slim-bar" style="width: <?= min(100, ($pedidosEnProcesoMes / $totalPedidosBase) * 100) ?>%; background: linear-gradient(90deg, #06b6d4, #0ea5e9);"></div></div>

                <div class="mini-stat">
                    <div><i class="fas fa-ban text-danger me-2"></i>Cancelados</div>
                    <div class="fw-bold"><?= $pedidosCanceladosMes ?></div>
                </div>
                <div class="progress-slim"><div class="progress-slim-bar" style="width: <?= min(100, ($pedidosCanceladosMes / $totalPedidosBase) * 100) ?>%; background: linear-gradient(90deg, #ef4444, #b91c1c);"></div></div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="dash-card p-3 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="section-title mb-0">Agenda de pedidos</div>
                    <span class="badge bg-light text-dark d-flex align-items-center gap-2"><i class="fas fa-calendar-week text-primary"></i>Vista mensual</span>
                </div>
                <div id="calendar-pedidos"></div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="dash-card p-3 mb-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="section-title mb-0">Entregas proximas</div>
                    <span class="badge bg-success-subtle text-success"><?= $entregasProximas['cantidadHoy'] + $entregasProximas['cantidadManana'] ?> en cola</span>
                </div>
                <?php if ($entregasProximas['cantidadHoy'] == 0 && $entregasProximas['cantidadManana'] == 0): ?>
                    <p class="text-muted mb-0">No hay entregas programadas.</p>
                <?php endif; ?>

                <?php if ($entregasProximas['cantidadHoy'] > 0): ?>
                    <div class="mb-3">
                        <div class="fw-semibold mb-1"><i class="fas fa-truck text-success me-2"></i>Hoy (<?= date('d/m') ?>)</div>
                        <?php foreach ($entregasProximas['hoy'] as $entrega): ?>
                            <div class="lote-alert mb-2 d-flex justify-content-between align-items-start">
                                <div>
                                    <strong>#<?= $entrega['numped'] ?></strong> - <?= htmlspecialchars($entrega['cliente']) ?>
                                    <div class="small text-muted">Estado: <span class="badge bg-<?= $entrega['estado'] == 'Completado' ? 'success' : ($entrega['estado'] == 'Pendiente' ? 'warning text-dark' : 'info') ?>"><?= $entrega['estado'] ?></span></div>
                                </div>
                                <span class="fw-bold text-success">$<?= number_format($entrega['monto_total'], 0, ',', '.') ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php if ($entregasProximas['cantidadManana'] > 0): ?>
                    <div>
                        <div class="fw-semibold mb-1"><i class="fas fa-clock text-warning me-2"></i>Manana (<?= date('d/m', strtotime('+1 day')) ?>)</div>
                        <?php foreach ($entregasProximas['manana'] as $entrega): ?>
                            <div class="lote-alert mb-2 d-flex justify-content-between align-items-start" style="background: #fffbea; border-color: #facc15;">
                                <div>
                                    <strong>#<?= $entrega['numped'] ?></strong> - <?= htmlspecialchars($entrega['cliente']) ?>
                                    <div class="small text-muted">Estado: <span class="badge bg-<?= $entrega['estado'] == 'Completado' ? 'success' : ($entrega['estado'] == 'Pendiente' ? 'warning text-dark' : 'info') ?>"><?= $entrega['estado'] ?></span></div>
                                </div>
                                <span class="fw-bold text-warning">$<?= number_format($entrega['monto_total'], 0, ',', '.') ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="dash-card p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="section-title mb-0">Lotes por vencer (7 dias)</div>
                    <span class="badge bg-danger-subtle text-danger"><?= $cantidadAlertasLotes ?> alerta<?= $cantidadAlertasLotes == 1 ? '' : 's' ?></span>
                </div>
                <?php if (empty($lotesProximosCaducar)): ?>
                    <p class="text-muted mb-0">Sin lotes proximos a caducar.</p>
                <?php else: ?>
                    <?php foreach (array_slice($lotesProximosCaducar, 0, 3) as $lote): 
                        $fechaCad = new DateTime($lote['fecha_caducidad']);
                        $hoy = new DateTime();
                        $diff = $hoy->diff($fechaCad);
                        $diasRest = $diff->invert ? 0 : $diff->days;
                    ?>
                    <div class="lote-alert">
                        <div class="d-flex justify-content-between">
                            <strong><?= htmlspecialchars($lote['producto'] ?? 'Producto') ?></strong>
                            <span class="badge <?= $diasRest <= 3 ? 'bg-danger' : ($diasRest <= 5 ? 'bg-warning text-dark' : 'bg-info') ?>"><?= $diasRest ?> dia<?= $diasRest == 1 ? '' : 's' ?></span>
                        </div>
                        <div class="small text-muted">Lote <?= htmlspecialchars($lote['numero_lote']) ?> - <?= $fechaCad->format('d/m/Y') ?></div>
                    </div>
                    <?php endforeach; ?>
                    <div class="text-end mt-2">
                        <button class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#modal-alertas-caducidad">
                            <i class="fas fa-eye me-1"></i>Ver todos
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="dash-card p-3 h-100">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="section-title mb-0">Top productos (30 dias)</div>
                    <span class="badge bg-light text-dark"><?= count($topProductos) ?> items</span>
                </div>
                <?php if (empty($topProductos)): ?>
                    <p class="text-muted mb-0 text-center py-3">No hay datos de ventas en este periodo.</p>
                <?php else: ?>
                    <?php foreach ($topProductos as $index => $producto): 
                        $posicion = $index + 1;
                        $rango = "#" . $posicion;
                    ?>
                    <div class="producto-item">
                        <div class="d-flex align-items-start">
                            <span class="producto-ranking"><?= $rango ?></span>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="producto-nombre"><?= htmlspecialchars($producto['nombre']) ?></span>
                                    <span class="producto-porcentaje"><?= $producto['porcentaje'] ?>%</span>
                                </div>
                                <div class="producto-stats">
                                    <i class="fas fa-box me-1"></i><?= $producto['total_vendido'] ?> unidades
                                    <span class="ms-2"><i class="fas fa-shopping-cart me-1"></i><?= $producto['num_pedidos'] ?> pedidos</span>
                                    <span class="ms-2"><i class="fas fa-dollar-sign me-1"></i>$<?= number_format($producto['ingresos_total'], 0, ',', '.') ?></span>
                                </div>
                                <div class="producto-progress">
                                    <div class="producto-progress-bar" style="width: <?= $producto['porcentaje'] ?>%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="dash-card p-3 h-100">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="section-title mb-0">Actividad reciente</div>
                    <span class="badge bg-primary-subtle text-primary"><i class="fas fa-bolt me-1"></i>Tiempo real</span>
                </div>
                <ul class="list-group" id="actividad-reciente-list">
                    <?php foreach ($actividadReciente as $item): ?>
                        <li class="list-group-item d-flex align-items-start gap-2">
                            <span class="text-primary mt-1"><i class="fas fa-history"></i></span>
                            <span><?= date('d/m/Y H:i', strtotime($item['fecha'])) ?> - <?= htmlspecialchars($item['descripcion']) ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalPedidosDia" tabindex="-1" aria-labelledby="modalPedidosDiaLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalPedidosDiaLabel">Pedidos del dia</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body" id="modalPedidosDiaBody"></div>
            </div>
        </div>
    </div>
</div><script>
document.addEventListener('DOMContentLoaded', function() {
    // ========== GR+FICO DE TENDENCIA DE VENTAS ==========
    var ctxVentas = document.getElementById('chartVentas');
    if (ctxVentas) {
        var tendenciaData = <?= json_encode($tendenciaVentas) ?>;
        console.log('Datos de tendencia recibidos:', tendenciaData);

        if (!Array.isArray(tendenciaData) || tendenciaData.length === 0) {
            console.warn('No hay datos para mostrar en la gráfica de ventas.');
            return;
        }

        var labels = tendenciaData.map(item => item.fecha || '');
        var dataPedidos = tendenciaData.map(item => item.pedidos || 0);
        var dataMonto = tendenciaData.map(item => item.monto || 0);

        if (window.myChartVentas) {
            window.myChartVentas.destroy();
        }

        window.myChartVentas = new Chart(ctxVentas, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Cantidad de Pedidos',
                        data: dataPedidos,
                        borderColor: '#1976d2',
                        backgroundColor: 'rgba(25, 118, 210, 0.1)',
                        borderWidth: 2,
                        tension: 0.3,
                        fill: true,
                        yAxisID: 'y'
                    },
                    {
                        label: 'Monto Total ($)',
                        data: dataMonto,
                        borderColor: '#28a745',
                        backgroundColor: 'rgba(40, 167, 69, 0.1)',
                        borderWidth: 2,
                        tension: 0.3,
                        fill: true,
                        yAxisID: 'y1'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                var label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.datasetIndex === 1) {
                                    // Formato de peso colombiano
                                    label += '$' + context.parsed.y.toLocaleString('es-CO', {minimumFractionDigits: 0, maximumFractionDigits: 0});
                                } else {
                                    label += context.parsed.y;
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: {
                            display: true,
                            text: 'Cantidad de Pedidos'
                        },
                        ticks: {
                            stepSize: 1
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: {
                            display: true,
                            text: 'Monto (COP)'
                        },
                        ticks: {
                            callback: function(value) {
                                return '$' + value.toLocaleString('es-CO', {minimumFractionDigits: 0, maximumFractionDigits: 0});
                            }
                        },
                        grid: {
                            drawOnChartArea: false
                        }
                    }
                }
            }
        });
    }
    
    // ========== CALENDARIO DE PEDIDOS ==========
    // Funci+�n helper para formatear fecha sin problemas de zona horaria
    window.formatearFechaSinZona = function(fechaStr) {
        if (!fechaStr) return '<em>No especificada</em>';
        var partes = fechaStr.split('-');
        var fechaObj = new Date(partes[0], partes[1] - 1, partes[2]);
        return fechaObj.toLocaleDateString('es-CO', {day: '2-digit', month: '2-digit', year: 'numeric'});
    };
    var calendarEl = document.getElementById('calendar-pedidos');
    if (calendarEl) {
        function mostrarPedidosDeFecha(fecha) {
            var partes = fecha.split('-');
            var fechaObj = new Date(partes[0], partes[1] - 1, partes[2]);
            var fechaFormateada = fechaObj.toLocaleDateString('es-CO', {weekday: 'long', day: 'numeric', month: 'long', year: 'numeric'});
            var modalLabel = document.getElementById('modalPedidosDiaLabel');
            var modalBody = document.getElementById('modalPedidosDiaBody');
            
            if (modalLabel) {
                modalLabel.textContent = 'Pedidos para: ' + fechaFormateada.charAt(0).toUpperCase() + fechaFormateada.slice(1);
            }
            
            fetch('controllers/ccalendar_api.php?fecha=' + fecha)
                .then(response => response.json())
                .then(data => {
                    var html = '';
                    if (!data || typeof data !== 'object' || data.error) {
                        html = '<p class="text-danger">No se pudieron cargar los pedidos.</p>';
                    } else {
                        html += '<h6>Resumen:</h6>';
                        html += '<ul>';
                        html += '<li>Total: ' + (data.resumen && data.resumen.total !== undefined ? data.resumen.total : 0) + '</li>';
                        html += '<li>Completados: ' + (data.resumen && data.resumen.completados !== undefined ? data.resumen.completados : 0) + '</li>';
                        html += '<li>Pendientes: ' + (data.resumen && data.resumen.pendientes !== undefined ? data.resumen.pendientes : 0) + '</li>';
                        html += '<li>Rechazados: ' + (data.resumen && data.resumen.rechazados !== undefined ? data.resumen.rechazados : 0) + '</li>';
                        html += '</ul>';
                        html += '<h6>Pedidos:</h6>';
                        if (!data.pedidos || !Array.isArray(data.pedidos) || data.pedidos.length === 0) {
                            html += '<p>No hay pedidos para este dia.</p>';
                        } else {
                            html += '<table class="table table-bordered table-sm"><thead><tr><th>Numero</th><th>Cliente</th><th>Estado</th><th>Monto</th><th>Creacion</th><th>Entrega</th></tr></thead><tbody>';
                            data.pedidos.forEach(function(p) {
                                html += '<tr>';
                                html += '<td><strong>' + (p.numped || '-') + '</strong></td>';
                                html += '<td>' + (p.cliente || '-') + '</td>';
                                html += '<td><span class="badge ' + getStatusBadgeClass(p.estado) + '">' + (p.estado || '-') + '</span></td>';
                                html += '<td>$' + (p.monto !== undefined ? parseFloat(p.monto).toLocaleString('es-CO', {minimumFractionDigits: 2, maximumFractionDigits: 2}) : '-') + '</td>';
                                html += '<td><small>' + (p.fecha_pedido ? new Date(p.fecha_pedido.replace(' ', 'T')).toLocaleString('es-CO', {day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit'}) : '-') + '</small></td>';
                                html += '<td><small>' + formatearFechaSinZona(p.fecha_entrega_solicitada) + '</small></td>';
                                html += '</tr>';
                            });
                            html += '</tbody></table>';
                        }
                    }
                    if (modalBody) {
                        modalBody.innerHTML = html;
                    }
                    var modal = new bootstrap.Modal(document.getElementById('modalPedidosDia'));
                    modal.show();
                })
                .catch(() => {
                    if (modalBody) {
                        modalBody.innerHTML = '<p class="text-danger">No se pudieron cargar los pedidos.</p>';
                    }
                    var modal = new bootstrap.Modal(document.getElementById('modalPedidosDia'));
                    modal.show();
                });
        }
        
        var initialDate = '<?= ($filtro['ano'] && $filtro['mes']) ? $filtro['ano']."-".str_pad($filtro['mes'], 2, "0", STR_PAD_LEFT)."-01" : date('Y-m-d') ?>';
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            initialDate: initialDate,
            locale: 'es',
            height: 550,
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth'
            },
            events: function(fetchInfo, successCallback, failureCallback) {
                // Cargar pedidos como eventos para el rango visible
                console.log('Solicitando pedidos del:', fetchInfo.startStr, 'al:', fetchInfo.endStr);
                fetch('controllers/ccalendar_api.php?start=' + fetchInfo.startStr + '&end=' + fetchInfo.endStr)
                    .then(response => response.json())
                    .then(events => {
                        console.log('Eventos recibidos:', events);
                        console.log('Total eventos:', events.length);
                        events.forEach(e => {
                            console.log('- Evento:', e.title, 'Fecha:', e.start, 'Clase:', e.className);
                        });
                        successCallback(events);
                    })
                    .catch(error => {
                        console.error('Error cargando eventos:', error);
                        failureCallback(error);
                    });
            },
            eventDidMount: function(info) {
                console.log('Evento montado:', info.event.title, info.el);
                // Reemplazar el contenido del evento con solo el icono
                if (info.el) {
                    // Buscar el contenedor del titulo
                    var eventMain = info.el.querySelector('.fc-event-main');
                    var eventTitle = info.el.querySelector('.fc-event-title');
                    
                    if (eventMain) {
                        // Limpiar y agregar solo el icono
                        eventMain.innerHTML = '<span style="font-size: 18px; display: block; text-align: center; line-height: 1;">&#128144;</span>';
                    } else if (eventTitle) {
                        eventTitle.innerHTML = '<span style="font-size: 18px;">&#128144;</span>';
                    }
                    
                    // Agregar clase para estilo
                    info.el.classList.add('evento-pedido-con-flores');
                }
            },
            eventClick: function(info) {
                // Mostrar pedidos del dia seleccionado sin crear pedidos nuevos
                mostrarPedidosDeFecha(info.event.startStr);
            },
            dateClick: function(info) {
                // Mostrar pedidos del dia al hacer click en cualquier fecha
                mostrarPedidosDeFecha(info.dateStr);
            }
        });
        calendar.render();
    }
});
// Helper para mostrar badge de estado
function getStatusBadgeClass(estado) {
    switch (estado) {
        case 'Pendiente': return 'bg-warning text-dark';
        case 'Completado': return 'bg-success';
        case 'Cancelado': return 'bg-danger';
        case 'En proceso': return 'bg-info text-dark';
        default: return 'bg-secondary';
    }
}
</script>
<script>
// Actualizaci+�n autom+�tica de Actividad Reciente cada 30 segundos
function actualizarActividadReciente() {
    fetch('controllers/CDashboardGeneral.php?action=actividadReciente')
        .then(res => res.json())
        .then(data => {
            if (Array.isArray(data)) {
                const ul = document.getElementById('actividad-reciente-list');
                if (ul) {
                    ul.innerHTML = data.map(item => {
                        const dateStr = item.fecha ? new Date(item.fecha.replace(' ', 'T')).toLocaleString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' }) : '';
                        const desc = item.descripcion ? item.descripcion.replace(/</g, '&lt;').replace(/>/g, '&gt;') : '';
                        return `<li class="list-group-item d-flex align-items-start gap-2">
                            <span class="text-primary mt-1"><i class="fas fa-history"></i></span>
                            <span>${dateStr} - ${desc}</span>
                        </li>`;
                    }).join('');
                }
            }
        })
        .catch(() => {});
}
setInterval(actualizarActividadReciente, 30000);
document.addEventListener('DOMContentLoaded', actualizarActividadReciente);
</script>

<!-- Modal de Alertas de Caducidad -->
<div class="modal fade" id="modal-alertas-caducidad" tabindex="-1" aria-labelledby="modalAlertasCaducidadLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="modalAlertasCaducidadLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i>Lotes Pr+�ximos a Caducar
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?php if (!empty($lotesProximosCaducar)): ?>
                <div class="alert alert-warning">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Atenci+�n:</strong> Los siguientes lotes <strong>con stock disponible</strong> caducan en los pr+�ximos 7 d+�as. 
                    Se recomienda priorizar su venta o uso inmediato.
                    <br><small class="mt-1 d-block"><i class="fas fa-lightbulb me-1"></i>Los lotes sin stock (cantidad = 0) no se muestran en esta lista.</small>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Estado</th>
                                <th>Producto</th>
                                <th>Lote</th>
                                <th>Cantidad</th>
                                <th>Fecha Caducidad</th>
                                <th>D+�as Restantes</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($lotesProximosCaducar as $lote): 
                                $fechaCaducidad = new DateTime($lote['fecha_caducidad']);
                                $hoy = new DateTime();
                                $diferencia = $hoy->diff($fechaCaducidad);
                                $diasRestantes = $diferencia->invert ? 0 : $diferencia->days;
                                
                                // Determinar color seg+�n d+�as restantes
                                if ($diasRestantes <= 3) {
                                    $colorClase = 'text-danger';
                                    $iconoEstado = '<i class="fas fa-circle text-danger"></i>';
                                    $nivelUrgencia = 'CR+�TICO';
                                } elseif ($diasRestantes <= 5) {
                                    $colorClase = 'text-warning';
                                    $iconoEstado = '<i class="fas fa-circle text-warning"></i>';
                                    $nivelUrgencia = 'URGENTE';
                                } else {
                                    $colorClase = 'text-info';
                                    $iconoEstado = '<i class="fas fa-circle text-info"></i>';
                                    $nivelUrgencia = 'ALERTA';
                                }
                            ?>
                            <tr>
                                <td><?= $iconoEstado ?> <small class="<?= $colorClase ?> fw-bold"><?= $nivelUrgencia ?></small></td>
                                <td><strong><?= htmlspecialchars($lote['producto'] ?? 'N/A') ?></strong></td>
                                <td><code><?= htmlspecialchars($lote['numero_lote']) ?></code></td>
                                <td><span class="badge bg-secondary"><?= number_format($lote['cantidad']) ?> unid.</span></td>
                                <td><?= $fechaCaducidad->format('d/m/Y') ?></td>
                                <td>
                                    <span class="badge <?= $diasRestantes <= 3 ? 'bg-danger' : ($diasRestantes <= 5 ? 'bg-warning text-dark' : 'bg-info') ?>">
                                        <?= $diasRestantes ?> <?= $diasRestantes === 1 ? 'd+�a' : 'd+�as' ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Leyenda -->
                <div class="mt-3">
                    <h6 class="fw-bold">Leyenda de Niveles de Urgencia:</h6>
                    <div class="d-flex gap-3 flex-wrap">
                        <div><i class="fas fa-circle text-danger"></i> <strong>CR+�TICO:</strong> 1-3 d+�as</div>
                        <div><i class="fas fa-circle text-warning"></i> <strong>URGENTE:</strong> 4-5 d+�as</div>
                        <div><i class="fas fa-circle text-info"></i> <strong>ALERTA:</strong> 6-7 d+�as</div>
                    </div>
                </div>
                <?php else: ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle me-2"></i>
                    No hay lotes pr+�ximos a caducar en los pr+�ximos 7 d+�as.
                </div>
                <?php endif; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <a href="index.php?ctrl=cinventario" class="btn btn-primary">
                    <i class="fas fa-boxes me-2"></i>Ir a Inventario
                </a>
            </div>
        </div>
    </div>
</div>

<script>
// Actualizar badge de alertas en sidebar
document.addEventListener('DOMContentLoaded', function() {
    const cantidadAlertas = <?= $cantidadAlertasLotes ?>;
    const badgeSidebar = document.getElementById('badge-alertas-sidebar');
    
    if (badgeSidebar && cantidadAlertas > 0) {
        badgeSidebar.textContent = cantidadAlertas;
        badgeSidebar.style.display = 'inline-block';
    }
});
</script>

</script>
