<?php
$mensaje = isset($_SESSION['mensaje']) ? $_SESSION['mensaje'] : '';
$tipo_mensaje = isset($_SESSION['tipo_mensaje']) ? $_SESSION['tipo_mensaje'] : '';
unset($_SESSION['mensaje']);
unset($_SESSION['tipo_mensaje']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Empleado</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/dashboard-empleado.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="empleado-theme">
    <div class="dashboard-container">
        <?php include __DIR__ . '/partials/navbar_empleado.php'; ?>

        <!-- Saludo Principal -->
        <div class="main-content">
            <div class="welcome-section">
                <div class="welcome-card card">
                    <div class="card-body">
                        <div class="welcome-header d-flex justify-content-between align-items-center flex-wrap gap-3">
                            <div>
                                <h2><i class="fas fa-chart-line me-2"></i>Dashboard</h2>
                                <p class="mb-0 text-muted">Pedidos, pagos e inventario en un solo lugar</p>
                            </div>
                            <!-- Filtro: cambia los números de "Pedidos" y "Ventas" según el mes elegido -->
                            <div class="period-filter-wrapper">
                                <label class="period-filter-label">Ver estadísticas de</label>
                                <form action="index.php" method="GET" class="d-inline">
                                    <input type="hidden" name="ctrl" value="empleado">
                                    <input type="hidden" name="action" value="dashboard">
                                    <select name="periodo" class="form-select form-select-sm period-filter-select" onchange="this.form.submit()" title="Elige el mes para ver pedidos y ventas de ese periodo">
                                        <option value="">Mes actual</option>
                                        <?php if (!empty($periodos)): ?>
                                            <?php foreach ($periodos as $p):
                                                $val = $p['mes'] . '-' . $p['ano'];
                                                $selected = (isset($filtro['mes']) && $filtro['mes'] == $p['mes'] && $filtro['ano'] == $p['ano']) ? 'selected' : '';
                                                $meses_es = [1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'];
                                                $mes_texto = $meses_es[$p['mes']] ?? '';
                                            ?>
                                                <option value="<?= $val ?>" <?= $selected ?>><?= $mes_texto ?> <?= $p['ano'] ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </form>
                                <small class="text-muted d-block mt-1">Pedidos y ventas del mes elegido</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php if ($mensaje): ?>
                <div class="alert alert-<?= $tipo_mensaje ?> alert-dismissible fade show" role="alert">
                    <i class="fas fa-<?= $tipo_mensaje === 'success' ? 'check-circle' : ($tipo_mensaje === 'warning' ? 'exclamation-triangle' : 'info-circle') ?> me-2"></i>
                    <?= htmlspecialchars($mensaje) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Resumen: estas son las estadísticas según el periodo elegido arriba -->
            <div class="stats-section">
                <h3 class="stats-section-title">Resumen</h3>
                <p class="stats-section-desc">Números según el mes seleccionado</p>
                <div class="stats-grid">
                    <div class="stat-card success">
                        <div class="stat-icon"><i class="fas fa-shopping-cart"></i></div>
                        <div class="stat-info">
                            <h3><?= number_format($stats['pedidos_hoy']) ?></h3>
                            <p><?= (isset($filtro['mes']) && !empty($filtro['mes'])) ? 'Pedidos del periodo' : 'Pedidos hoy' ?></p>
                        </div>
                    </div>
                    <div class="stat-card warning">
                        <div class="stat-icon"><i class="fas fa-clock"></i></div>
                        <div class="stat-info">
                            <h3><?= number_format($stats['pedidos_pendientes']) ?></h3>
                            <p>Pedidos pendientes</p>
                        </div>
                    </div>
                    <div class="stat-card info">
                        <div class="stat-icon"><i class="fas fa-credit-card"></i></div>
                        <div class="stat-info">
                            <h3><?= number_format($stats['pagos_pendientes']) ?></h3>
                            <p>Pagos pendientes</p>
                        </div>
                    </div>
                    <div class="stat-card success">
                        <div class="stat-icon"><i class="fas fa-dollar-sign"></i></div>
                        <div class="stat-info">
                            <h3>$<?= number_format($stats['ventas_mes'], 2) ?></h3>
                            <p>Ventas <?= (isset($filtro['mes']) && !empty($filtro['mes'])) ? 'del periodo' : 'del mes' ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Acciones rápidas: siempre visibles arriba -->
            <div class="quick-actions-bar">
                <a href="index.php?ctrl=empleado&action=gestion_pedidos" class="quick-action-link quick-action-pedidos">
                    <i class="fas fa-clipboard-list"></i>
                    <span>Gestionar Pedidos</span>
                </a>
                <a href="index.php?ctrl=empleado&action=procesar_pagos" class="quick-action-link quick-action-pagos">
                    <i class="fas fa-credit-card"></i>
                    <span>Procesar Pagos</span>
                </a>
                <a href="index.php?ctrl=empleado&action=inventario" class="quick-action-link quick-action-inventario">
                    <i class="fas fa-boxes"></i>
                    <span><?= $user['tpusu_idtpusu'] == 3 ? 'Inventario completo' : 'Inventario' ?></span>
                </a>
                <a href="index.php?ctrl=empleado&action=configuracion" class="quick-action-link quick-action-config">
                    <i class="fas fa-cog"></i>
                    <span>Configuración</span>
                </a>
            </div>

            <!-- Contenido: pedidos y alerta de stock -->
            <div class="content-grid">
                <div class="main-column">
                    <div class="content-card card mb-4">
                        <div class="card-header">
                            <h5><i class="fas fa-list-alt me-2"></i>Últimos pedidos pendientes</h5>
                            <span class="badge bg-primary"><span class="badge-num"><?= count($pedidos_pendientes) ?></span> pendientes</span>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($pedidos_pendientes)): ?>
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle">
                                        <thead>
                                            <tr>
                                                <th>Pedido</th>
                                                <th>Cliente</th>
                                                <th>Fecha</th>
                                                <th>Monto</th>
                                                <th>Estado</th>
                                                <th>Pago</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach (array_slice($pedidos_pendientes, 0, 10) as $pedido): ?>
                                            <tr>
                                                <td><strong class="text-primary"><?= htmlspecialchars($pedido['numped']) ?></strong></td>
                                                <td><?= htmlspecialchars($pedido['cliente_nombre']) ?></td>
                                                <td>
                                                    <small class="text-muted d-block"><?= date('d/m/Y', strtotime($pedido['fecha_pedido'])) ?></small>
                                                    <small class="text-muted"><?= date('H:i', strtotime($pedido['fecha_pedido'])) ?></small>
                                                </td>
                                                <td><strong class="text-success">$<?= number_format($pedido['monto_total'], 2) ?></strong></td>
                                                <td>
                                                    <span class="badge rounded-pill bg-<?= $pedido['estado'] === 'Pendiente' ? 'warning' : 'info' ?> text-dark"><?= htmlspecialchars($pedido['estado']) ?></span>
                                                </td>
                                                <td>
                                                    <span class="badge rounded-pill bg-<?= $pedido['estado_pag'] === 'Completado' ? 'success' : ($pedido['estado_pag'] === 'Pendiente' ? 'warning' : 'secondary') ?>"><?= htmlspecialchars($pedido['estado_pag'] ?? 'Sin pago') ?></span>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="empty-state">
                                    <i class="fas fa-clipboard-check"></i>
                                    <h4>No hay pedidos pendientes</h4>
                                    <p>Todos los pedidos están al día</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="side-column">
                    <div class="content-card card border-start border-danger border-4">
                        <div class="card-header bg-white">
                            <h5 class="text-danger"><i class="fas fa-exclamation-triangle me-2"></i>Stock Crítico</h5>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($stock_critico)): ?>
                                <div class="critical-list">
                                    <?php foreach ($stock_critico as $item): ?>
                                        <div class="d-flex justify-content-between align-items-center mb-2 border-bottom pb-2">
                                            <div>
                                                <h6 class="mb-0 fs-6"><?= htmlspecialchars($item['nombre']) ?></h6>
                                                <small class="text-muted"><?= htmlspecialchars($item['naturaleza']) ?></small>
                                            </div>
                                            <span class="badge bg-danger fs-6"><?= $item['stock'] ?></span>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <div class="text-center mt-3">
                                    <a href="index.php?ctrl=empleado&action=inventario" class="btn btn-sm btn-outline-danger w-100">Ver Inventario</a>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-3 text-muted">
                                    <i class="fas fa-check-circle text-success mb-2 d-block fs-3"></i>
                                    <p class="mb-0">Todo el stock está bien</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/dashboard-cliente.js"></script>
    <script>
        // Auto-dismiss alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);

        // Actualizar estadísticas cada 30 segundos
        setInterval(function() {
            // Aquí podrías agregar una llamada AJAX para actualizar las estadísticas
            console.log('Actualizando estadísticas...');
        }, 30000);
    </script>
</body>
</html>
