

<?php
// Inicializar variables con valores por defecto si no están definidas
$estadisticas = $estadisticas ?? [['ingresos_totales' => 0, 'pagos_completados' => 0, 'pagos_pendientes' => 0, 'total_pagos' => 0, 'metodos_pago' => []]];
$pagosRecientes = $pagosRecientes ?? [];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <link rel="stylesheet" href="/assets/dashboard-admin.css">

<div class="container">
    <h2 class="my-4">Dashboard de Pagos</h2>
    <?php // ...existing code... ?>

    <!-- Tarjetas de métricas -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card metric-card">
                <div class="card-body text-center">
                    <div class="stat-icon primary mb-2"><i class="fas fa-dollar-sign"></i></div>
                    <h5 class="card-title">Ingresos Totales</h5>
                    <div class="stat-number">$<?= number_format($estadisticas[0]['ingresos_totales'], 2) ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card metric-card">
                <div class="card-body text-center">
                    <div class="stat-icon success mb-2"><i class="fas fa-check-circle"></i></div>
                    <h5 class="card-title">Pagos Completados</h5>
                    <div class="stat-number text-success"><?= $estadisticas[0]['pagos_completados'] ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card metric-card">
                <div class="card-body text-center">
                    <div class="stat-icon warning mb-2"><i class="fas fa-clock"></i></div>
                    <h5 class="card-title">Pagos Pendientes</h5>
                    <div class="stat-number text-warning"><?= $estadisticas[0]['pagos_pendientes'] ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card metric-card">
                <div class="card-body text-center">
                    <div class="stat-icon info mb-2"><i class="fas fa-list-alt"></i></div>
                    <h5 class="card-title">Total Pagos</h5>
                    <div class="stat-number text-info"><?= $estadisticas[0]['total_pagos'] ?></div>
                </div>
            </div>
        </div>
    </div>
<body>

        <div class="navbar-user">
            <div class="user-info">
                <p class="user-name">Panel de Pagos</p>
                <p class="user-welcome">Resumen y gestión de pagos</p>

            </div>
            <a href="index.php?ctrl=login&action=logout" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
            </a>
        </div>
    </nav>

    <!-- Gráfica de métodos de pago -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Métodos de Pago Utilizados</h5>
=======
      <!-- Últimos pagos -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Últimos Pagos Registrados</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($pagosRecientes)): ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Fecha</th>
                                        <th>Cliente</th>
                                        <th>Pedido</th>
                                        <th>Método</th>
                                        <th>Monto</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pagosRecientes as $pago): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($pago['idpago']) ?></td>
                                        <td><?= date('d/m/Y H:i', strtotime($pago['fecha_pago'])) ?></td>
                                        <td><?= htmlspecialchars($pago['cliente']) ?></td>
                                        <td><?= htmlspecialchars($pago['numped']) ?></td>
                                        <td><?= htmlspecialchars($pago['metodo_pago']) ?></td>
                                        <td>$<?= number_format($pago['monto'], 2) ?></td>
                                        <td>
                                            <span class="badge badge-<?= 
                                                $pago['estado_pag'] === 'Completado' ? 'success' : 
                                                ($pago['estado_pag'] === 'Pendiente' ? 'warning' : 'danger')
                                            ?>">
                                                <?= htmlspecialchars($pago['estado_pag']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="/admin/pagos/detalle/<?= $pago['idpago'] ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i> Detalle
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
=======
            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="welcome-card card mb-4">
                    <div class="card-body">
                        <div class="welcome-header">
                            <h2>Gestión de Pagos</h2>
                            <p>Panel de administración de pagos y pedidos</p>

                        </div>
                    </div>
                </div>

            </div>

            <div class="mt-3">
                <a href="/floraltech/views/admin/reportes.php" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Volver a reportes
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Script para gráficos -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    <?php if (!empty($estadisticas[0]['metodos_pago'])): ?>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('metodosPagoChart');
        const chartData = {
            labels: <?= json_encode(array_column($estadisticas[0]['metodos_pago'], 'metodo_pago')) ?>,
            datasets: [{
                data: <?= json_encode(array_column($estadisticas[0]['metodos_pago'], 'cantidad')) ?>,
                backgroundColor: [
                    '#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#36b9cc'
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
                    }
                }
            }
        });
    });
    <?php endif; ?>
</script>
            <!-- Estadísticas -->
                <div class="stats-grid mb-4">
                    <div class="stat-card">
                        <div class="stat-icon"><i class="fas fa-dollar-sign"></i></div>
                        <div class="stat-number">$<?= number_format($estadisticas[0]['ingresos_totales'], 2) ?></div>
                        <div class="stat-label">Ingresos Totales</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                        <div class="stat-number"><?= $estadisticas[0]['pagos_completados'] ?></div>
                        <div class="stat-label">Pagos Completados</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon"><i class="fas fa-clock"></i></div>
                        <div class="stat-number"><?= $estadisticas[0]['pagos_pendientes'] ?></div>
                        <div class="stat-label">Pagos Pendientes</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon"><i class="fas fa-list"></i></div>
                        <div class="stat-number"><?= $estadisticas[0]['total_pagos'] ?></div>
                        <div class="stat-label">Total Pagos</div>
                    </div>
                </div>

                <!-- Métodos de pago -->
                <div class="card mb-4" style="background: #f8f9fa; border-radius: 16px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); border: none;">
                    <div class="card-header" style="background: #6c757d; color: #fff; border-top-left-radius: 16px; border-top-right-radius: 16px; border: none;">
                        <h5 class="mb-0">Métodos de Pago Utilizados</h5>
                    </div>
                    <div class="card-body" style="background: #f8f9fa;">
                        <?php
                        $metodos = [
                            'nequi' => ['label' => 'Nequi', 'color' => '#00c4a7', 'icon' => 'fa-mobile-alt'],
                            'efectivo' => ['label' => 'Efectivo', 'color' => '#ffc107', 'icon' => 'fa-money-bill-wave']
                        ];
                        $totalPagos = array_sum(array_map(function($m){ return $m['cantidad']; }, $resumenMetodosPago));
                        ?>
                        <div class="d-flex flex-wrap gap-4 justify-content-between align-items-stretch w-100">
                            <?php foreach ($metodos as $metodo => $info):
                                $data = array_filter($resumenMetodosPago, function($m) use ($metodo) {
                                    // Asegura que 'efectivo' coincida con el valor exacto en la base de datos
                                    return trim(strtolower($m['metodo_pago'])) === trim(strtolower($metodo));
                                });
                                $data = array_values($data);
                                $cantidad = !empty($data) ? $data[0]['cantidad'] : 0;
                                $total = !empty($data) ? $data[0]['total'] : 0;
                                $porcentaje = ($totalPagos > 0) ? round(($cantidad / $totalPagos) * 100) : 0;
                            ?>
                            <div class="flex-fill" style="min-width:280px; max-width:48%;">
                                <div class="p-4 h-100 d-flex flex-column justify-content-between" style="background:<?= $info['color'] ?>10; border-radius:16px; min-height:150px; box-shadow:0 1px 4px rgba(0,0,0,0.04);">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas <?= $info['icon'] ?>" style="font-size:2rem; color:<?= $info['color'] ?>; margin-right:10px;"></i>
                                        <h5 class="mb-0" style="color:<?= $info['color'] ?>; font-weight:bold;"><?= $info['label'] ?></h5>
                                    </div>
                                    <div class="mb-2">Cantidad de pagos: <strong><?= $cantidad ?></strong></div>
                                    <div class="mb-2">Total pagado: <strong>$<?= number_format($total, 2) ?></strong></div>
                                    <div class="progress" style="height:8px; background:#e9ecef; border-radius:4px;">
                                        <div class="progress-bar" role="progressbar" style="width: <?= $porcentaje ?>%; background:<?= $info['color'] ?>;" aria-valuenow="<?= $porcentaje ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <small class="text-muted">Porcentaje de pagos: <?= $porcentaje ?>%</small>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php if ($totalPagos == 0): ?>
                            <div class="alert mb-0 mt-3" style="background: #d1f3fc; color: #217a8b; border: none; border-radius: 12px;">No hay datos de métodos de pago disponibles.</div>
                        <?php endif; ?>
                    </div>
                </div>
                <style>
                    .card.mb-4 .card-body .alert.mb-0:hover,
                    .card.mb-4 .card-body .alert.mb-0:active,
                    .card.mb-4 .card-body .alert.mb-0:focus {
                        background: #d1f3fc !important;
                        color: #217a8b !important;
                        border: none !important;
                    }
                    .progress-bar {
                        transition: width 0.6s ease;
                    }
                </style>

                <!-- Filtros de búsqueda mejorados -->
                <div class="card mb-4" style="border-radius: 12px; box-shadow: 0 1px 4px rgba(0,0,0,0.04); border: none;">
                    <div class="card-body p-3">
                        <form id="form-filtros-pagos" class="row g-2 align-items-center" autocomplete="off">
                            <div class="col-md-5 col-12">
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i class="fas fa-hashtag"></i></span>
                                    <input type="text" name="pedido" id="filtro-pedido" class="form-control" placeholder="Filtrar por pedido" value="<?= htmlspecialchars($_GET['pedido'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="col-md-5 col-12">
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i class="fas fa-calendar-alt"></i></span>
                                    <input type="date" name="fecha" id="filtro-fecha" class="form-control" value="<?= htmlspecialchars($_GET['fecha'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="col-md-2 col-12 d-grid">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Filtrar</button>
                            </div>
                        </form>
                    </div>
                </div>

                <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const form = document.getElementById('form-filtros-pagos');
                    form.addEventListener('submit', function(e) {
                        e.preventDefault();
                        const pedido = document.getElementById('filtro-pedido').value;
                        const fecha = document.getElementById('filtro-fecha').value;
                        const params = new URLSearchParams({ pedido, fecha });
                        fetch('assets/ajax/filtrar_pagos.php?' + params.toString())
                            .then(res => res.text())
                            .then(html => {
                                document.getElementById('tabla-pagos-admin').innerHTML = html;
                            });
                    });
                });
                </script>

                <!-- Últimos pagos con paginación -->
                <div class="card mb-4">
                    <div class="card-header bg-dark text-white" style="background:#212529 !important; color:#fff !important;">
                        <h5 class="mb-0">Últimos Pagos Registrados</h5>
                    </div>
                    <style>
                        .card-header.bg-dark.text-white:hover,
                        .card-header.bg-dark.text-white:active,
                        .card-header.bg-dark.text-white:focus {
                            background: #2b75c0ff !important;
                            color: #fff !important;
                        }
                    </style>
                    <div class="card-body">
                        <?php
                        $pagosFiltrados = $pagosRecientes;
                        if (!empty($_GET['pedido'])) {
                            $pagosFiltrados = array_filter($pagosFiltrados, function($p) {
                                return stripos($p['numped'], $_GET['pedido']) !== false;
                            });
                        }
                        if (!empty($_GET['fecha'])) {
                            $pagosFiltrados = array_filter($pagosFiltrados, function($p) {
                                // Permite filtrar por fecha exacta o por día
                                $fechaFiltro = $_GET['fecha'];
                                $fechaPago = date('Y-m-d', strtotime($p['fecha_pago']));
                                return $fechaPago === $fechaFiltro;
                            });
                        }
                        usort($pagosFiltrados, function($a, $b) {
                            return strtotime($b['fecha_pago']) - strtotime($a['fecha_pago']);
                        });
                        $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
                        $perPage = 5;
                        $total = count($pagosFiltrados);
                        $start = ($page - 1) * $perPage;
                        $pagosPagina = array_slice($pagosFiltrados, $start, $perPage);
                        ?>
                        <?php if (!empty($pagosPagina)): ?>
                            <div id="tabla-pagos-admin">
                                <div class="table-responsive">
                                    <table class="table pagos-admin-table">
                                        <thead>
                                            <tr style="background:#212529; color:#fff;">
                                                <th>ID</th>
                                                <th>Fecha</th>
                                                <th>Cliente</th>
                                                <th>Pedido</th>
                                                <th>Método</th>
                                                <th>Monto</th>
                                                <th>Estado</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($pagosPagina as $pago): ?>
                                            <tr style="background:#f8f9fa; color:#212529;">
                                                <td><?= htmlspecialchars($pago['idpago']) ?></td>
                                                <td><?= date('d/m/Y H:i', strtotime($pago['fecha_pago'])) ?></td>
                                                <td><?= htmlspecialchars($pago['cliente']) ?></td>
                                                <td><?= htmlspecialchars($pago['numped']) ?></td>
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
                                                <td>
                                                    <button class="btn btn-sm btn-info btn-detalle-pago" data-idpago="<?= $pago['idpago'] ?>">
                                                        <i class="bi bi-eye-fill"></i> Detalle
                                                    </button>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <style>
                                .pagos-admin-table tbody tr:hover,
                                .pagos-admin-table tbody tr:active,
                                .pagos-admin-table tbody tr:focus {
                                    background: #f8f9fa !important;
                                    color: #212529 !important;
                                }
                                .pagos-admin-table thead tr {
                                    background: #212529 !important;
                                    color: #fff !important;
                                }
                            </style>
                            <nav>
                                <ul class="pagination">
                                    <?php for ($i = 1; $i <= ceil($total / $perPage); $i++): ?>
                                        <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                            <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>">Página <?= $i ?></a>
                                        </li>
                                    <?php endfor; ?>
                                </ul>
                            </nav>
                        <?php else: ?>
                            <div class="alert alert-info mb-0">No hay pagos recientes para mostrar.</div>
                        <?php endif; ?>
                    </div>
                </div>



