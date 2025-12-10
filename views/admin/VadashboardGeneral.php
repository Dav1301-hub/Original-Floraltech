<?php
// Obtener datos reales del dashboard general mediante el controller
if (!isset($dashboardData) || !is_array($dashboardData)) {
    // Solo ejecutar el controller si no tenemos datos (evitar duplicación)
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
            'topProductos' => []
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

// Obtener lotes próximos a caducar
require_once __DIR__ . '/../../models/Mlotes.php';
$lotesModel = new Mlotes();
$lotesProximosCaducar = $lotesModel->getLotesProximosCaducar(7);
$cantidadAlertasLotes = count($lotesProximosCaducar);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard General - FloralTech</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/assets/dashboard-admin.css">
    <link rel="stylesheet" href="/assets/styles.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        /* Responsive fixes para Dashboard General */
        .dashboard-main {
            width: 100%;
            margin: 0;
            padding: 32px 24px;
        }
        
        /* Eliminar el grid de cards, ahora se muestran en columna */
        .stats-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.08);
            padding: 1.25rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            transition: transform 0.2s, box-shadow 0.2s;
            height: 100%;
            min-height: 140px;
        }
        
        .stats-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        
        .stats-card i {
            font-size: 2rem;
            margin-bottom: 0.5rem;
            color: #1976d2;
        }
        
        .stats-card h3 {
            font-size: 2.25rem;
            margin: 0.25rem 0;
            font-weight: bold;
            color: #333;
        }
        
        .stats-card p {
            margin: 0.25rem 0;
            color: #666;
            font-size: 0.875rem;
            font-weight: 500;
        }
        
        .trend {
            margin-top: 0.35rem;
            font-size: 0.75rem;
            font-weight: 500;
        }
        
        .trend.up {
            color: #4caf50;
        }
        
        .trend.down {
            color: #f44336;
        }
        
        /* Tablet */
        @media (max-width: 992px) {
            .dashboard-main {
                padding: 24px 16px;
            }
            
            .stats-card {
                padding: 1rem;
            }
            
            .stats-card h3 {
                font-size: 1.75rem;
            }
        }
        
        /* Mobile */
        @media (max-width: 576px) {
            
            .dashboard-main {
                padding: 16px 12px;
            }
            
            .dashboard-main h1 {
                font-size: 1.5rem;
            }
            
            .stats-card {
                padding: 1rem;
            }
            
            .stats-card h3 {
                font-size: 1.75rem;
            }
            
            .card-body {
                padding: 0.75rem;
            }
            
            .list-group-item {
                padding: 0.5rem 0.75rem;
                font-size: 0.85rem;
            }
        }
        
        /* Extra small devices */
        @media (max-width: 380px) {
            .dashboard-main {
                padding: 12px 8px;
            }
            
            .dashboard-main h1 {
                font-size: 1.25rem;
            }
            
            .welcome-text {
                font-size: 0.85rem;
            }
        }
        
        /* Estilos para eventos del calendario con pedidos */
        .fc-event.evento-pedido-con-flores {
            background-color: #f8bbd0 !important;
            border-color: #f48fb1 !important;
            color: #880e4f !important;
            font-weight: 500;
            font-size: 0.75rem !important;
            padding: 2px 4px !important;
        }
        
        .fc-event.evento-pedido-con-flores:hover {
            background-color: #f48fb1 !important;
            border-color: #ec407a !important;
        }
        
        .fc-daygrid-event.evento-pedido-con-flores {
            cursor: pointer;
            white-space: normal !important;
            overflow: visible !important;
        }
        
        .fc-event.evento-pedido-con-flores .fc-event-title {
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
        }
        
        /* Estilos para alertas de entregas */
        .alert-entregas {
            border-left: 4px solid #28a745;
            background: linear-gradient(135deg, #e8f5e9 0%, #f1f8f4 100%);
        }
        
        .alert-entregas.alert-warning {
            border-left-color: #ffc107;
            background: linear-gradient(135deg, #fff8e1 0%, #fffbf0 100%);
        }
        
        /* Responsive: Alerta de caducidad en móviles */
        @media (max-width: 768px) {
            .alert.alert-danger {
                flex-direction: column !important;
                text-align: center;
            }
            
            .alert.alert-danger > div:first-child {
                margin-bottom: 1rem;
            }
            
            .alert.alert-danger button {
                width: 100%;
                margin-top: 0.5rem;
            }
        }
        
        .entrega-item {
            border-left: 3px solid #28a745;
            padding-left: 0.75rem;
            margin-bottom: 0.5rem;
            transition: all 0.2s;
        }
        
        .entrega-item:hover {
            background-color: rgba(40, 167, 69, 0.05);
            border-left-width: 4px;
        }
        
        /* Estilos para Top Productos */
        .producto-item {
            padding: 0.75rem 0;
            border-bottom: 1px solid #f0f0f0;
            transition: background-color 0.2s;
        }
        
        .producto-item:last-child {
            border-bottom: none;
        }
        
        .producto-item:hover {
            background-color: #f8f9fa;
        }
        
        .producto-ranking {
            font-size: 1.5rem;
            width: 40px;
            text-align: center;
            display: inline-block;
        }
        
        .producto-nombre {
            font-weight: 600;
            color: #333;
            flex-grow: 1;
        }
        
        .producto-stats {
            font-size: 0.85rem;
            color: #666;
            margin-top: 0.25rem;
        }
        
        .producto-progress {
            height: 6px;
            background-color: #e9ecef;
            border-radius: 3px;
            overflow: hidden;
            margin-top: 0.5rem;
        }
        
        .producto-progress-bar {
            height: 100%;
            background: linear-gradient(90deg, #1976d2, #42a5f5);
            border-radius: 3px;
            transition: width 0.3s ease;
        }
        
        .producto-porcentaje {
            font-weight: 600;
            color: #1976d2;
            min-width: 50px;
            text-align: right;
        }
    </style>
</head>
<body>
<div id="general-dashboard" class="dashboard-main">
    <header>
        <h1>Dashboard General</h1>
        <p class="welcome-text">Bienvenido al sistema de administración de FloralTech</p>
    </header>

    <!-- Métricas visuales modernas -->
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

    <!-- Alerta Compacta de Caducidad -->
    <?php if ($cantidadAlertasLotes > 0): ?>
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-danger d-flex align-items-center justify-content-between shadow-sm border-0" style="border-left: 5px solid #dc3545 !important;">
                <div class="d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; background: rgba(220, 53, 69, 0.1); border-radius: 50%;">
                        <i class="fas fa-exclamation-triangle text-danger" style="font-size: 1.8rem;"></i>
                    </div>
                    <div>
                        <h5 class="mb-1 fw-bold text-danger">
                            <i class="fas fa-box-open me-2"></i><?= $cantidadAlertasLotes ?> 
                            <?= $cantidadAlertasLotes === 1 ? 'Lote próximo a caducar' : 'Lotes próximos a caducar' ?>
                        </h5>
                        <p class="mb-0 text-dark" style="font-size: 0.95rem;">
                            <?= $cantidadAlertasLotes === 1 
                                ? 'Caduca en los próximos 7 días' 
                                : "Caducan en los próximos 7 días" 
                            ?> • Revisa el inventario para evitar pérdidas
                        </p>
                    </div>
                </div>
                <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#modal-alertas-caducidad" style="white-space: nowrap;">
                    <i class="fas fa-list-ul me-1"></i>Ver Detalles
                </button>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Sección media: Calendario + Gráfico -->
    <div class="row mb-4">
        <!-- Calendario de Pedidos (Izquierda) -->
        <div class="col-lg-8">
            <div class="card card-calendario mb-3">
                <div class="card-header bg-success text-white"><i class="fas fa-calendar me-2"></i>Calendario de Pedidos</div>
                <div class="card-body">
                    <div id="calendar-pedidos"></div>
                </div>
            </div>
        </div>

        <!-- Gráfico de Tendencia (Derecha) -->
        <div class="col-lg-4">
            <div class="card mb-3">
                <div class="card-header bg-dark text-white"><i class="fas fa-chart-line me-2"></i>Tendencia de Ventas</div>
                <div class="card-body">
                    <canvas id="chartVentas" height="400"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Sección de Alertas: Entregas Próximas -->
    <?php if ($entregasProximas['cantidadHoy'] > 0 || $entregasProximas['cantidadManana'] > 0): ?>
    <div class="row mb-4">
        <div class="col-12">
            <?php if ($entregasProximas['cantidadHoy'] > 0): ?>
            <div class="alert alert-entregas alert-success border-0 shadow-sm mb-3">
                <div class="d-flex align-items-center mb-2">
                    <i class="fas fa-truck fa-2x me-3"></i>
                    <div>
                        <h5 class="mb-0"><strong><?= $entregasProximas['cantidadHoy'] ?></strong> Entrega<?= $entregasProximas['cantidadHoy'] > 1 ? 's' : '' ?> para HOY</h5>
                        <small class="text-muted"><?= date('d/m/Y') ?></small>
                    </div>
                </div>
                <div class="mt-2">
                    <?php foreach ($entregasProximas['hoy'] as $entrega): ?>
                    <div class="entrega-item">
                        <strong>Pedido #<?= $entrega['numped'] ?></strong> - <?= htmlspecialchars($entrega['cliente']) ?>
                        <span class="badge bg-<?= $entrega['estado'] == 'Completado' ? 'success' : ($entrega['estado'] == 'Pendiente' ? 'warning' : 'info') ?> ms-2"><?= $entrega['estado'] ?></span>
                        <span class="text-muted ms-2">$<?= number_format($entrega['monto_total'], 2) ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if ($entregasProximas['cantidadManana'] > 0): ?>
            <div class="alert alert-entregas alert-warning border-0 shadow-sm mb-3">
                <div class="d-flex align-items-center mb-2">
                    <i class="fas fa-clock fa-2x me-3"></i>
                    <div>
                        <h5 class="mb-0"><strong><?= $entregasProximas['cantidadManana'] ?></strong> Entrega<?= $entregasProximas['cantidadManana'] > 1 ? 's' : '' ?> para MAÑANA</h5>
                        <small class="text-muted"><?= date('d/m/Y', strtotime('+1 day')) ?></small>
                    </div>
                </div>
                <div class="mt-2">
                    <?php foreach ($entregasProximas['manana'] as $entrega): ?>
                    <div class="entrega-item" style="border-left-color: #ffc107;">
                        <strong>Pedido #<?= $entrega['numped'] ?></strong> - <?= htmlspecialchars($entrega['cliente']) ?>
                        <span class="badge bg-<?= $entrega['estado'] == 'Completado' ? 'success' : ($entrega['estado'] == 'Pendiente' ? 'warning' : 'info') ?> ms-2"><?= $entrega['estado'] ?></span>
                        <span class="text-muted ms-2">$<?= number_format($entrega['monto_total'], 2) ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Sección inferior: Resumen + Top Productos + Actividad Reciente -->
    <div class="row">
        <!-- Resumen Mensual -->
        <div class="col-lg-4">
            <div class="card card-resumen mb-3">
                <div class="card-header bg-primary text-white"><i class="fas fa-calendar-alt me-2"></i>Resumen Mensual de Pedidos (<?= $mesReferencia ?>)</div>
                <div class="card-body">
                    <ul class="list-group">
                        <li class="list-group-item"><span class="icon text-primary"><i class="fas fa-shopping-bag"></i></span>Pedidos este mes: <strong><?= $pedidosMes ?></strong></li>
                        <li class="list-group-item"><span class="icon text-success"><i class="fas fa-check"></i></span>Completados: <strong><?= $pedidosCompletados ?></strong></li>
                        <li class="list-group-item"><span class="icon text-warning"><i class="fas fa-clock"></i></span>Pendientes: <strong><?= $pedidosPendientesMes ?></strong></li>
                        <li class="list-group-item"><span class="icon text-info"><i class="fas fa-cog"></i></span>En proceso: <strong><?= $pedidosEnProcesoMes ?></strong></li>
                        <li class="list-group-item"><span class="icon text-danger"><i class="fas fa-ban"></i></span>Cancelados: <strong><?= $pedidosCanceladosMes ?></strong></li>
                    </ul>
                </div>
            </div>
        </div>
        
        <!-- Top 5 Productos Más Vendidos -->
        <div class="col-lg-4">
            <div class="card mb-3">
                <div class="card-header bg-warning text-dark"><i class="fas fa-trophy me-2"></i>Top 5 Productos (Últimos 30 días)</div>
                <div class="card-body">
                    <?php if (empty($topProductos)): ?>
                        <p class="text-muted text-center mb-0">No hay datos de ventas en este período</p>
                    <?php else: ?>
                        <?php foreach ($topProductos as $index => $producto): 
                            $medallas = ['🥇', '🥈', '🥉'];
                            $emoji = $index < 3 ? $medallas[$index] : ($index + 1);
                        ?>
                        <div class="producto-item">
                            <div class="d-flex align-items-start">
                                <span class="producto-ranking"><?= $emoji ?></span>
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
        </div>
        
        <!-- Actividad Reciente -->
        <div class="col-lg-4">
            <div class="card card-noti mb-3">
                <div class="card-header bg-info text-white"><i class="fas fa-bell me-2"></i>Pedidos Recientes</div>
                <div class="card-body" id="actividad-reciente-body">
                    <ul class="list-group" id="actividad-reciente-list">
                        <?php foreach ($actividadReciente as $item): ?>
                            <li class="list-group-item">
                                <span class="icon"><i class="fas fa-history"></i></span>
                                <span><?= date('d/m/Y H:i', strtotime($item['fecha'])) ?> - <?= htmlspecialchars($item['descripcion']) ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para mostrar pedidos del día -->
    <div class="modal fade" id="modalPedidosDia" tabindex="-1" aria-labelledby="modalPedidosDiaLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalPedidosDiaLabel">Pedidos del día</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body" id="modalPedidosDiaBody">
                    <!-- Aquí se cargan los pedidos -->
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<!-- FullCalendar -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.9/index.global.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.9/index.global.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ========== GRÁFICO DE TENDENCIA DE VENTAS ==========
    var ctxVentas = document.getElementById('chartVentas');
    if (ctxVentas) {
        var tendenciaData = <?= json_encode($tendenciaVentas) ?>;
        
        var labels = tendenciaData.map(item => item.fecha);
        var dataPedidos = tendenciaData.map(item => item.pedidos);
        var dataMonto = tendenciaData.map(item => item.monto);
        
        new Chart(ctxVentas, {
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
    // Función helper para formatear fecha sin problemas de zona horaria
    window.formatearFechaSinZona = function(fechaStr) {
        if (!fechaStr) return '<em>No especificada</em>';
        var partes = fechaStr.split('-');
        var fechaObj = new Date(partes[0], partes[1] - 1, partes[2]);
        return fechaObj.toLocaleDateString('es-CO', {day: '2-digit', month: '2-digit', year: 'numeric'});
    };
    
    var calendarEl = document.getElementById('calendar-pedidos');
    if (calendarEl) {
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
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
                fetch('/Original-Floraltech/controllers/ccalendar_api.php?start=' + fetchInfo.startStr + '&end=' + fetchInfo.endStr)
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
                // Ícono de flor para pedidos
                if (info.el) {
                    var eventMain = info.el.querySelector('.fc-event-main');
                    var eventTitle = info.el.querySelector('.fc-event-title');
                    if (eventMain) {
                        eventMain.innerHTML = '<span style="font-size: 18px; display: block; text-align: center; line-height: 1;">💐</span>';
                    } else if (eventTitle) {
                        eventTitle.innerHTML = '<span style="font-size: 18px;">💐</span>';
                    }
                    info.el.classList.add('evento-pedido-con-flores');
                }
            },
            eventClick: function(info) {
                // Mostrar modal con detalle del pedido al hacer click en un evento
                var fecha = info.event.startStr;
                // Usar la fecha directamente sin conversión de zona horaria
                var partes = fecha.split('-');
                var fechaObj = new Date(partes[0], partes[1] - 1, partes[2]);
                var fechaFormateada = fechaObj.toLocaleDateString('es-CO', {weekday: 'long', day: 'numeric', month: 'long', year: 'numeric'});
                document.getElementById('modalPedidosDiaLabel').textContent = 'Pedidos para: ' + fechaFormateada.charAt(0).toUpperCase() + fechaFormateada.slice(1);
                
                fetch('/Original-Floraltech/controllers/ccalendar_api.php?fecha=' + fecha)
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
                            html += '<button class="btn btn-success mb-3" id="btnNuevoPedido" data-fecha="' + fecha + '">Crear nuevo pedido</button>';
                            html += '<h6>Pedidos:</h6>';
                            if (!data.pedidos || !Array.isArray(data.pedidos) || data.pedidos.length === 0) {
                                html += '<p>No hay pedidos para este día.</p>';
                            } else {
                                html += '<table class="table table-bordered table-sm"><thead><tr><th>Número</th><th>Cliente</th><th>Estado</th><th>Monto</th><th>Creación</th><th>Entrega</th></tr></thead><tbody>';
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
                        document.getElementById('modalPedidosDiaBody').innerHTML = html;
                        var modal = new bootstrap.Modal(document.getElementById('modalPedidosDia'));
                        modal.show();
                        setTimeout(function() {
                            var btn = document.getElementById('btnNuevoPedido');
                            if (btn) {
                                btn.onclick = function() {
                                    var fecha = btn.getAttribute('data-fecha');
                                    document.getElementById('modalPedidosDiaBody').innerHTML = '<div class="text-center"><div class="spinner-border text-success" role="status"></div><p>Cargando formulario...</p></div>';
                                    fetch('/Original-Floraltech/controllers/ajax_nuevo_pedido.php?fecha=' + fecha)
                                        .then(resp => resp.text())
                                        .then(formHtml => {
                                            document.getElementById('modalPedidosDiaBody').innerHTML = formHtml;
                                        })
                                        .catch(() => {
                                            document.getElementById('modalPedidosDiaBody').innerHTML = '<p class="text-danger">No se pudo cargar el formulario.</p>';
                                        });
                                };
                            }
                        }, 300);
                    })
                    .catch((err) => {
                        document.getElementById('modalPedidosDiaBody').innerHTML = '<p class="text-danger">No se pudieron cargar los pedidos.</p>';
                        var modal = new bootstrap.Modal(document.getElementById('modalPedidosDia'));
                        modal.show();
                    });
            },
            dateClick: function(info) {
                // También permitir crear pedido desde un día vacío
                var fecha = info.dateStr;
                // Usar la fecha directamente sin conversión de zona horaria
                var partes = fecha.split('-');
                var fechaObj = new Date(partes[0], partes[1] - 1, partes[2]);
                var fechaFormateada = fechaObj.toLocaleDateString('es-CO', {weekday: 'long', day: 'numeric', month: 'long', year: 'numeric'});
                document.getElementById('modalPedidosDiaLabel').textContent = 'Pedidos para: ' + fechaFormateada.charAt(0).toUpperCase() + fechaFormateada.slice(1);
                
                fetch('/Original-Floraltech/controllers/ccalendar_api.php?fecha=' + fecha)
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
                            html += '<button class="btn btn-success mb-3" id="btnNuevoPedido" data-fecha="' + info.dateStr + '">Crear nuevo pedido</button>';
                            html += '<h6>Pedidos:</h6>';
                            if (!data.pedidos || !Array.isArray(data.pedidos) || data.pedidos.length === 0) {
                                html += '<p>No hay pedidos para este día.</p>';
                            } else {
                                html += '<table class="table table-bordered table-sm"><thead><tr><th>Número</th><th>Cliente</th><th>Estado</th><th>Monto</th><th>Creación</th><th>Entrega</th></tr></thead><tbody>';
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
                        document.getElementById('modalPedidosDiaBody').innerHTML = html;
                        var modal = new bootstrap.Modal(document.getElementById('modalPedidosDia'));
                        modal.show();
                        setTimeout(function() {
                            var btn = document.getElementById('btnNuevoPedido');
                            if (btn) {
                                btn.onclick = function() {
                                    var fecha = btn.getAttribute('data-fecha');
                                    document.getElementById('modalPedidosDiaBody').innerHTML = '<div class="text-center"><div class="spinner-border text-success" role="status"></div><p>Cargando formulario...</p></div>';
                                    fetch('/Original-Floraltech/controllers/ajax_nuevo_pedido.php?fecha=' + fecha)
                                        .then(resp => resp.text())
                                        .then(formHtml => {
                                            document.getElementById('modalPedidosDiaBody').innerHTML = formHtml;
                                        })
                                        .catch(() => {
                                            document.getElementById('modalPedidosDiaBody').innerHTML = '<p class="text-danger">No se pudo cargar el formulario.</p>';
                                        });
                                };
                            }
                        }, 300);
                    })
                    .catch((err) => {
                        document.getElementById('modalPedidosDiaBody').innerHTML = '<p class="text-danger">No se pudieron cargar los pedidos.</p>';
                        var modal = new bootstrap.Modal(document.getElementById('modalPedidosDia'));
                        modal.show();
                    });
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
// Actualización automática de Actividad Reciente cada 30 segundos
function actualizarActividadReciente() {
    fetch('/Original-Floraltech/controllers/CDashboardGeneral.php?action=actividadReciente')
        .then(res => res.json())
        .then(data => {
            if (Array.isArray(data)) {
                const ul = document.getElementById('actividad-reciente-list');
                if (ul) {
                    ul.innerHTML = data.map(item =>
                        `<li class="list-group-item">
                            <span class='icon'><i class='fas fa-history'></i></span>
                            <span>${item.fecha ? new Date(item.fecha).toLocaleString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' }) : ''} - ${item.descripcion ? item.descripcion.replace(/</g, '&lt;').replace(/>/g, '&gt;') : ''}</span>
                        </li>`
                    ).join('');
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
                    <i class="fas fa-exclamation-triangle me-2"></i>Lotes Próximos a Caducar
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?php if (!empty($lotesProximosCaducar)): ?>
                <div class="alert alert-warning">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Atención:</strong> Los siguientes lotes <strong>con stock disponible</strong> caducan en los próximos 7 días. 
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
                                <th>Días Restantes</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($lotesProximosCaducar as $lote): 
                                $fechaCaducidad = new DateTime($lote['fecha_caducidad']);
                                $hoy = new DateTime();
                                $diferencia = $hoy->diff($fechaCaducidad);
                                $diasRestantes = $diferencia->invert ? 0 : $diferencia->days;
                                
                                // Determinar color según días restantes
                                if ($diasRestantes <= 3) {
                                    $colorClase = 'text-danger';
                                    $iconoEstado = '<i class="fas fa-circle text-danger"></i>';
                                    $nivelUrgencia = 'CRÍTICO';
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
                                        <?= $diasRestantes ?> <?= $diasRestantes === 1 ? 'día' : 'días' ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <body>
                        <div id="general-dashboard" class="dashboard-main">
                            <header class="d-flex flex-wrap align-items-start align-items-md-center justify-content-between mb-4 p-4 rounded-4 shadow-sm text-white" style="background: linear-gradient(120deg, #0d6efd 0%, #5b21b6 50%, #1e1b4b 100%);">
                                <div>
                                    <p class="mb-1 text-uppercase fw-semibold small opacity-75">Panel general</p>
                                    <h1 class="mb-1 fw-bold">Dashboard General</h1>
                                    <p class="welcome-text mb-0 opacity-75">Bienvenido al sistema de administración de FloralTech</p>
                                </div>
                                <div class="d-flex gap-2 mt-3 mt-md-0">
                                    <a class="btn btn-light btn-sm text-primary fw-semibold" href="index.php?ctrl=cinventario"><i class="fas fa-boxes me-2"></i>Inventario</a>
                                    <a class="btn btn-outline-light btn-sm" href="#calendar-pedidos"><i class="fas fa-calendar-alt me-2"></i>Agenda</a>
                                </div>
                            </header>
                    <h6 class="fw-bold">Leyenda de Niveles de Urgencia:</h6>
                    <div class="d-flex gap-3 flex-wrap">
                        <div><i class="fas fa-circle text-danger"></i> <strong>CRÍTICO:</strong> 1-3 días</div>
                        <div><i class="fas fa-circle text-warning"></i> <strong>URGENTE:</strong> 4-5 días</div>
                        <div><i class="fas fa-circle text-info"></i> <strong>ALERTA:</strong> 6-7 días</div>
                    </div>
                </div>
                <?php else: ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle me-2"></i>
                    No hay lotes próximos a caducar en los próximos 7 días.
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

</body>
</html>
