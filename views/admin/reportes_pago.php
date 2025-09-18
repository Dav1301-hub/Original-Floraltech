<div class="container">
    <?php
    // Inicializar variables con valores por defecto
    $tipo = $tipo ?? ($_GET['tipo'] ?? 'ganancias');
    $datos = $datos ?? [];
    ?>
    

    <h2 class="my-4">Reporte de <?= htmlspecialchars(ucfirst($tipo)) ?></h2>

    <!-- Filtros del reporte -->
    <fieldset class="border rounded p-3 mb-4 bg-light">
        <legend class="float-none w-auto px-2 mb-2 fw-bold"><i class="bi bi-funnel"></i> Filtros del reporte</legend>
        <form class="row g-2 align-items-end" method="get" action="">
            <div class="col-md-3">
                <input type="text" name="cliente" class="form-control" placeholder="Cliente" value="<?= htmlspecialchars($_GET['cliente'] ?? '') ?>">
            </div>
            <div class="col-md-3">
                <input type="date" name="fecha_inicio" class="form-control" value="<?= htmlspecialchars($_GET['fecha_inicio'] ?? '') ?>">
            </div>
            <div class="col-md-3">
                <input type="date" name="fecha_fin" class="form-control" value="<?= htmlspecialchars($_GET['fecha_fin'] ?? '') ?>">
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-fill"><i class="bi bi-search"></i> Filtrar</button>
                <a href="?tipo=<?= urlencode($tipo) ?>" class="btn btn-outline-secondary flex-fill"><i class="bi bi-x-circle"></i> Limpiar</a>
            </div>
            <div class="col-12 mt-2">
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-outline-info btn-sm" onclick="setQuickDate('hoy')">Hoy</button>
                    <button type="button" class="btn btn-outline-info btn-sm" onclick="setQuickDate('7dias')">Últimos 7 días</button>
                    <button type="button" class="btn btn-outline-info btn-sm" onclick="setQuickDate('mes')">Este mes</button>
                    <button type="button" class="btn btn-outline-info btn-sm" onclick="setQuickDate('mespasado')">Mes pasado</button>
                </div>
                <button type="button" class="btn btn-outline-dark btn-sm ms-2" onclick="toggleDemo()"><i class="bi bi-eye"></i> Datos de Ejemplo</button>
                <button type="button" class="btn btn-success btn-sm ms-2" onclick="exportar('csv')"><i class="bi bi-file-earmark-spreadsheet"></i> Exportar Excel</button>
                <button type="button" class="btn btn-danger btn-sm ms-2" onclick="exportar('pdf')"><i class="bi bi-file-earmark-pdf"></i> Exportar PDF</button>
            </div>
        </form>
    </fieldset>
    
    <?php if (isset($_SESSION['mensaje'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?= htmlspecialchars($_SESSION['mensaje']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['mensaje']); ?>
    <?php endif; ?>

    <?php
    $mostrarDemo = isset($_GET['demo']) && $_GET['demo'] === '1';
    if ($mostrarDemo) {
        // Datos de ejemplo para modo demo
        $datos = [
            [
                'idpago' => '1001', 'fecha_pago' => date('Y-m-d H:i'), 'numped' => 'PED-001', 'cliente' => 'Juan Pérez', 'metodo_pago' => 'Efectivo', 'monto' => 120.50, 'estado_pag' => 'Completado', 'transaccion_id' => 'TX12345'
            ],
            [
                'idpago' => '1002', 'fecha_pago' => date('Y-m-d H:i', strtotime('-2 days')), 'numped' => 'PED-002', 'cliente' => 'Ana Gómez', 'metodo_pago' => 'Tarjeta', 'monto' => 250.00, 'estado_pag' => 'Pendiente', 'transaccion_id' => 'TX12346'
            ],
            [
                'idpago' => '1003', 'fecha_pago' => date('Y-m-d H:i', strtotime('-7 days')), 'numped' => 'PED-003', 'cliente' => 'Carlos Ruiz', 'metodo_pago' => 'Transferencia', 'monto' => 80.00, 'estado_pag' => 'Completado', 'transaccion_id' => 'TX12347'
            ],
        ];
    }
    ?>
    <?php if (empty($datos)): ?>
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Fecha</th>
                        <th>Cliente</th>
                        <th>Pedido ID</th>
                        <th>Total</th>
                        <th>Método</th>
                        <th>Estado</th>
                        <th>Transacción</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="7" class="text-center text-warning">
                            <i class="bi bi-exclamation-triangle"></i> No se encontraron datos para los filtros seleccionados.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <?php
        // Resumen numérico arriba de la tabla
        if ($tipo === 'ganancias' && !empty($datos['resumen'])) {
            $totalVentas = $datos['resumen']['total_recaudado'] ?? 0;
            $ganancia = $datos['resumen']['ganancia_neta'] ?? ($totalVentas * 0.7);
            $pedidos = $datos['resumen']['pagos_completados'] + $datos['resumen']['pagos_pendientes'];
            $ticketProm = $pedidos ? $totalVentas / $pedidos : 0;
        ?>
        <div class="row mb-3">
            <div class="col-md-3">
                <div class="card bg-success text-white text-center">
                    <div class="card-body">
                        <div class="fw-bold">Total Ventas</div>
                        <div class="h5">$<?= number_format($totalVentas, 2) ?></div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-primary text-white text-center">
                    <div class="card-body">
                        <div class="fw-bold">Ganancia Neta</div>
                        <div class="h5">$<?= number_format($ganancia, 2) ?></div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white text-center">
                    <div class="card-body">
                        <div class="fw-bold">Pedidos</div>
                        <div class="h5"><?= $pedidos ?></div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white text-center">
                    <div class="card-body">
                        <div class="fw-bold">Ticket Promedio</div>
                        <div class="h5">$<?= number_format($ticketProm, 2) ?></div>
                    </div>
                </div>
            </div>
        </div>
        <?php } ?>
            <!-- Reporte de Ganancias -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Estadísticas de Ganancias</h5>
                </div>
                <div class="card-body">
                    <?php if (isset($datos['metodos_pago'])): ?>
                        <div class="row">
                            <div class="col-md-6">
                                <canvas id="metodosPagoChart" height="300"></canvas>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-striped">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Método</th>
                                            <th>Cantidad</th>
                                            <th>Total</th>
                                            <th>Porcentaje</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($datos['metodos_pago'] as $metodo): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($metodo['metodo_pago']) ?></td>
                                            <td><?= $metodo['cantidad'] ?></td>
                                            <td>$<?= number_format($metodo['ingresos_totales'], 2) ?></td>
                                            <td><?= number_format($metodo['porcentaje'], 2) ?>%</td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($datos['resumen'])): ?>
                        <hr>
                        <div class="row mt-4">
                            <div class="col-md-4">
                                <div class="card text-white bg-success mb-3">
                                    <div class="card-body">
                                        <h5 class="card-title">Total Recaudado</h5>
                                        <p class="card-text h4">$<?= number_format($datos['resumen']['total_recaudado'], 2) ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card text-white bg-info mb-3">
                                    <div class="card-body">
                                        <h5 class="card-title">Pagos Completados</h5>
                                        <p class="card-text h4"><?= $datos['resumen']['pagos_completados'] ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card text-white bg-warning mb-3">
                                    <div class="card-body">
                                        <h5 class="card-title">Pagos Pendientes</h5>
                                        <p class="card-text h4"><?= $datos['resumen']['pagos_pendientes'] ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
    <?php endif; ?>
    <?php if ($tipo === 'auditoria'): ?>
            <!-- Reporte de Auditoría -->
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Auditoría de Pagos</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Fecha</th>
                                    <th>Pedido</th>
                                    <th>Cliente</th>
                                    <th>Método</th>
                                    <th>Monto</th>
                                    <th>Estado</th>
                                    <th>Transacción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Filtro por cliente y fechas
                                $clienteFiltro = strtolower(trim($_GET['cliente'] ?? ''));
                                $fechaInicio = $_GET['fecha_inicio'] ?? '';
                                $fechaFin = $_GET['fecha_fin'] ?? '';
                                foreach ($datos as $pago):
                                    if ($clienteFiltro && strpos(strtolower($pago['cliente']), $clienteFiltro) === false) continue;
                                    if ($fechaInicio && strtotime($pago['fecha_pago']) < strtotime($fechaInicio)) continue;
                                    if ($fechaFin && strtotime($pago['fecha_pago']) > strtotime($fechaFin . ' 23:59:59')) continue;
                                ?>
                                <tr>
                                    <td><?= htmlspecialchars($pago['idpago']) ?></td>
                                    <td><?= date('d/m/Y H:i', strtotime($pago['fecha_pago'])) ?></td>
                                    <td><?= htmlspecialchars($pago['numped']) ?></td>
                                    <td><?= htmlspecialchars($pago['cliente']) ?></td>
                                    <td><?= htmlspecialchars($pago['metodo_pago']) ?></td>
                                    <td>$<?= number_format($pago['monto'], 2) ?></td>
                                    <td>
                                        <span class="badge bg-<?= 
                                            $pago['estado_pag'] === 'Completado' ? 'success' : 
                                            ($pago['estado_pag'] === 'Pendiente' ? 'warning' : 'danger') 
                                        ?>">
                                            <?= htmlspecialchars($pago['estado_pag']) ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($pago['transaccion_id'] ?? 'N/A') ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-danger">
                Tipo de reporte no reconocido: <?= htmlspecialchars($tipo) ?>
            </div>
        <?php endif; ?>

    <!-- Navegación secundaria -->
    <nav class="mt-4">
        <ul class="nav nav-pills justify-content-center gap-2">
            <li class="nav-item">
                <a href="/floraltech/views/admin/proyecciones.php" class="nav-link">
                    <i class="bi bi-graph-up"></i> Proyecciones
                </a>
            </li>
            <li class="nav-item">
                <a href="/floraltech/views/admin/auditoria.php" class="nav-link">
                    <i class="bi bi-search"></i> Auditoría
                </a>
            </li>
            <li class="nav-item">
                <a href="/floraltech/views/admin/detalle_pago.php" class="nav-link">
                    <i class="bi bi-clipboard-data"></i> Detalles
                </a>
            </li>
            <li class="nav-item">
                <a href="/floraltech/views/admin/dashboard.php" class="nav-link">
                    <i class="bi bi-house"></i> Dashboard
                </a>
            </li>
        </ul>
    </nav>
</div>

<!-- Incluir Chart.js desde CDN para mejor compatibilidad -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Rango de fechas rápido
function setQuickDate(tipo) {
    const hoy = new Date();
    let inicio = '';
    let fin = '';
    if (tipo === 'hoy') {
        inicio = fin = hoy.toISOString().slice(0,10);
    } else if (tipo === '7dias') {
        const hace7 = new Date(hoy.getTime() - 6*24*60*60*1000);
        inicio = hace7.toISOString().slice(0,10);
        fin = hoy.toISOString().slice(0,10);
    } else if (tipo === 'mes') {
        inicio = new Date(hoy.getFullYear(), hoy.getMonth(), 1).toISOString().slice(0,10);
        fin = new Date(hoy.getFullYear(), hoy.getMonth()+1, 0).toISOString().slice(0,10);
    } else if (tipo === 'mespasado') {
        const mesPasado = new Date(hoy.getFullYear(), hoy.getMonth()-1, 1);
        inicio = mesPasado.toISOString().slice(0,10);
        fin = new Date(hoy.getFullYear(), hoy.getMonth(), 0).toISOString().slice(0,10);
    }
    document.querySelector('input[name="fecha_inicio"]').value = inicio;
    document.querySelector('input[name="fecha_fin"]').value = fin;
}

// Toggle modo demo
function toggleDemo() {
    const url = new URL(window.location.href);
    url.searchParams.set('demo', url.searchParams.get('demo') === '1' ? '0' : '1');
    window.location.href = url.toString();
}

// Exportar CSV/PDF (solo CSV simulado)
function exportar(tipo) {
    if (tipo === 'csv') {
        let csv = '';
        const rows = document.querySelectorAll('table tbody tr');
        rows.forEach(row => {
            let cols = Array.from(row.querySelectorAll('td')).map(td => td.innerText.replace(/\$/g, ''));
            csv += cols.join(',') + '\n';
        });
        const blob = new Blob([csv], {type: 'text/csv'});
        const a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = 'reporte.csv';
        a.click();
    } else if (tipo === 'pdf') {
        alert('Exportar a PDF no implementado en demo.');
    }
}
</script>
<script>
    <?php if ($tipo === 'ganancias' && isset($datos['metodos_pago'])): ?>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('metodosPagoChart').getContext('2d');
        const chartData = {
            labels: <?= json_encode(array_column($datos['metodos_pago'], 'metodo_pago')) ?>,
            datasets: [{
                data: <?= json_encode(array_column($datos['metodos_pago'], 'ingresos_totales')) ?>,
                backgroundColor: [
                    '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'
                ],
                hoverBackgroundColor: [
                    '#2e59d9', '#17a673', '#2c9faf', '#dda20a', '#be2617'
                ],
                hoverBorderColor: "rgba(234, 236, 244, 1)",
            }]
        };
        
        new Chart(ctx, {
            type: 'doughnut',
            data: chartData,
            options: {
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = Math.round((value / total) * 100);
                                return `${label}: $${value.toLocaleString()} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    });
    <?php endif; ?>
</script>

<?php 
// Incluir footer con direccionamiento correcto
require_once __DIR__ . '/../../partials/footer.php';
?>