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
            ]
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
$tendenciaPagos = $dashboardData['tendenciaPagos'];
$tendenciaPendientes = $dashboardData['tendenciaPendientes'];
$tendenciaRechazados = $dashboardData['tendenciaRechazados'];
$actividadReciente = $dashboardData['actividadReciente'];
$pedidosMes = $dashboardData['resumenPedidosMes']['pedidosMes'];
$pedidosCompletados = $dashboardData['resumenPedidosMes']['pedidosCompletados'];
$pedidosPendientesMes = $dashboardData['resumenPedidosMes']['pedidosPendientes'];
$pedidosEnProcesoMes = $dashboardData['resumenPedidosMes']['pedidosEnProceso'];
$pedidosCanceladosMes = $dashboardData['resumenPedidosMes']['pedidosCancelados'];
$mesReferencia = $dashboardData['resumenPedidosMes']['mesReferencia'] ?? date('m/Y');
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard General - FloralTech</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/assets/dashboard-admin.css">
    <link rel="stylesheet" href="/assets/styles.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
<div id="general-dashboard" class="dashboard-main" style="max-width: 1100px; margin: 0 auto; padding: 32px 24px;">
    <header>
        <h1>Dashboard General</h1>
        <p class="welcome-text">Bienvenido al sistema de administración de FloralTech</p>
    </header>

    <!-- KPIs -->
    <div class="dashboard-cards mb-4" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 18px;">
        <div class="kpi-card">
            <div class="kpi-icon" style="background: #007bff; color: #fff;"><i class="fas fa-credit-card"></i></div>
            <div class="kpi-value"><?= $totalPagos ?></div>
            <div class="kpi-label">Total Pagos</div>
            <div class="kpi-trend text-success">
                <i class="fas fa-arrow-up"></i> <?= $tendenciaPagos ?>% semana
            </div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon" style="background: #ffc107; color: #fff;"><i class="fas fa-clock"></i></div>
            <div class="kpi-value"><?= $pagosPendientes ?></div>
            <div class="kpi-label">Pagos Pendientes</div>
            <div class="kpi-trend text-info">
                <i class="fas fa-arrow-up"></i> <?= $tendenciaPendientes ?>% semana
            </div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon" style="background: #dc3545; color: #fff;"><i class="fas fa-ban"></i></div>
            <div class="kpi-value"><?= $pagosRechazados ?></div>
            <div class="kpi-label">Pagos Rechazados</div>
            <div class="kpi-trend text-danger">
                <i class="fas fa-arrow-down"></i> <?= $tendenciaRechazados ?>% semana
            </div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon" style="background: #28a745; color: #fff;"><i class="fas fa-users"></i></div>
            <div class="kpi-value"><?= $usuariosRegistrados ?></div>
            <div class="kpi-label">Usuarios Registrados</div>
            <div class="kpi-trend text-success">
                <i class="fas fa-user-plus"></i> <?= $nuevosUsuarios ?> nuevos esta semana
            </div>
        </div>
    </div>

    <!-- Actividad reciente y calendario -->
    <div class="row mb-4">
        <div class="col-lg-6 col-md-7">
            <div class="card card-noti mb-3">
                <div class="card-header bg-info text-white"><i class="fas fa-bell me-2"></i>Actividad Reciente</div>
                <div class="card-body">
                    <ul class="list-group">
                        <?php foreach ($actividadReciente as $item): ?>
                            <li class="list-group-item">
                                <span class="icon"><i class="fas fa-history"></i></span>
                                <span><?= date('d/m/Y H:i', strtotime($item['fecha'])) ?> - <?= htmlspecialchars($item['descripcion']) ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
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
        <div class="col-lg-6 col-md-5">
            <div class="card card-calendario mb-3">
                <div class="card-header bg-success text-white"><i class="fas fa-calendar me-2"></i>Calendario de Pedidos</div>
                <div class="card-body">
                    <div id="calendar-pedidos"></div>
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
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
<!-- FullCalendar -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.9/index.global.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.9/index.global.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar-pedidos');
    if (calendarEl) {
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'es',
            height: 400,
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            dateClick: function(info) {
                var fecha = info.dateStr;
                fetch('/Original-Floraltech/controllers/ccalendar_api.php?fecha=' + fecha)
                    .then(response => response.json())
                    .then(data => {
                        var html = '';
                        html += '<h6>Resumen:</h6>';
                        html += '<ul>';
                        html += '<li>Total: ' + data.resumen.total + '</li>';
                        html += '<li>Completados: ' + data.resumen.completados + '</li>';
                        html += '<li>Pendientes: ' + data.resumen.pendientes + '</li>';
                        html += '<li>Rechazados: ' + data.resumen.rechazados + '</li>';
                        html += '</ul>';
                        html += '<button class="btn btn-success mb-3" id="btnNuevoPedido" data-fecha="' + info.dateStr + '">Crear nuevo pedido</button>';
                        html += '<h6>Pedidos:</h6>';
                        if (data.pedidos.length === 0) {
                            html += '<p>No hay pedidos para este día.</p>';
                        } else {
                            html += '<table class="table table-bordered"><thead><tr><th>ID</th><th>Cliente</th><th>Estado</th><th>Monto</th><th>Hora</th></tr></thead><tbody>';
                            data.pedidos.forEach(function(p) {
                                html += '<tr>';
                                html += '<td>' + p.id + '</td>';
                                html += '<td>' + (p.cliente || '-') + '</td>';
                                html += '<td><span class="badge ' + getStatusBadgeClass(p.estado) + '">' + p.estado + '</span></td>';
                                html += '<td>' + p.monto + '</td>';
                                html += '<td>' + (p.fecha_pedido ? p.fecha_pedido.substr(11,5) : '-') + '</td>';
                                html += '</tr>';
                            });
                            html += '</tbody></table>';
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
                    .catch(() => {
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
</body>
</html>