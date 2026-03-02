<?php
// Obtener mensajes de sesión si existen
$mensaje = isset($_SESSION['mensaje']) ? $_SESSION['mensaje'] : '';
$tipo_mensaje = isset($_SESSION['tipo_mensaje']) ? $_SESSION['tipo_mensaje'] : '';
// Limpiar los mensajes después de mostrarlos
unset($_SESSION['mensaje']);
unset($_SESSION['tipo_mensaje']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Empleado - FloralTech</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/dashboard-empleado.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="dashboard-container">
        <!-- Header Mejorado -->
        <nav class="navbar">
            <div class="container-fluid">
                <div class="navbar-brand">
                    <i class="fas fa-seedling me-2"></i>
                    FloralTech - Empleado
                </div>
                <div class="navbar-user">
                    <div class="user-info">
                        <p class="user-name">Bienvenido, <?= htmlspecialchars($user['nombre_completo']) ?></p>
                        <p class="user-welcome">
                            <?php
                            $tipo_empleado = '';
                            switch($user['tpusu_idtpusu']) {
                                case 2: $tipo_empleado = 'Panel Vendedor'; break;
                                case 3: $tipo_empleado = 'Panel Inventario'; break;
                                case 4: $tipo_empleado = 'Panel Repartidor'; break;
                                default: $tipo_empleado = 'Panel Empleado'; break;
                            }
                            echo $tipo_empleado;
                            ?>
                        </p>
                    </div>
                    <a href="index.php?ctrl=login&action=logout" class="logout-btn">
                        <i class="fas fa-sign-out-alt me-1"></i>Cerrar Sesión
                    </a>
                </div>
            </div>
        </nav>

        <!-- Saludo Principal -->
        <div class="main-content">
            <div class="welcome-section">
                <div class="welcome-card card">
                    <div class="card-body">
                        <div class="welcome-header d-flex justify-content-between align-items-center flex-wrap gap-3">
                            <div>
                                <h2><i class="fas fa-chart-line me-2"></i>Dashboard de Empleado</h2>
                                <p class="mb-0">Gestiona pedidos, pagos e inventario desde tu panel de control</p>
                            </div>
                            
                            <!-- Filtro de Periodo -->
                            <div class="period-filter bg-white p-2 rounded shadow-sm border">
                                <form action="index.php" method="GET" class="d-flex align-items-center gap-2">
                                    <input type="hidden" name="ctrl" value="empleado">
                                    <input type="hidden" name="action" value="dashboard">
                                    <label class="small fw-bold text-muted mb-0"><i class="fas fa-filter me-1"></i>Periodo:</label>
                                    <select name="periodo" class="form-select form-select-sm border-0 bg-light" onchange="this.form.submit()" style="min-width: 150px;">
                                        <option value="">Mes Actual</option>
                                        <?php if (!empty($periodos)): ?>
                                            <?php foreach ($periodos as $p): 
                                                $val = $p['mes'] . '-' . $p['ano'];
                                                $selected = (isset($filtro['mes']) && $filtro['mes'] == $p['mes'] && $filtro['ano'] == $p['ano']) ? 'selected' : '';
                                                $nombre_mes = date("F", mktime(0, 0, 0, $p['mes'], 10));
                                                // Traducir mes si es posible o usar formato numérico
                                                $meses_es = [
                                                    1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio',
                                                    7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
                                                ];
                                                $mes_texto = $meses_es[$p['mes']] ?? $nombre_mes;
                                            ?>
                                                <option value="<?= $val ?>" <?= $selected ?>><?= $mes_texto ?> <?= $p['ano'] ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </form>
                            </div>
                        </div>
                        <div class="welcome-stats">
                            <span><i class="fas fa-calendar-day me-1"></i><?= date('d/m/Y') ?></span>
                            <span><i class="fas fa-clock me-1"></i><?= date('H:i') ?></span>
                            <span><i class="fas fa-user-tag me-1"></i>
                                <?php
                                $tipo_empleado = '';
                                switch($user['tpusu_idtpusu']) {
                                    case 2: $tipo_empleado = 'Vendedor'; break;
                                    case 3: $tipo_empleado = 'Inventario'; break;
                                    case 4: $tipo_empleado = 'Repartidor'; break;
                                    default: $tipo_empleado = 'Empleado'; break;
                                }
                                echo $tipo_empleado;
                                ?>
                            </span>
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

            <!-- Estadísticas Principales -->
            <div class="stats-section">
                <div class="stats-grid">
                    <div class="stat-card success">
                        <div class="stat-icon">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?= number_format($stats['pedidos_hoy']) ?></h3>
                            <p><?= (isset($filtro['mes']) && !empty($filtro['mes'])) ? 'Pedidos del Periodo' : 'Pedidos Hoy' ?></p>
                        </div>
                    </div>

                    <div class="stat-card warning">
                        <div class="stat-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?= number_format($stats['pedidos_pendientes']) ?></h3>
                            <p>Pedidos Pendientes</p>
                        </div>
                    </div>

                    <div class="stat-card info">
                        <div class="stat-icon">
                            <i class="fas fa-credit-card"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?= number_format($stats['pagos_pendientes']) ?></h3>
                            <p>Pagos Pendientes</p>
                        </div>
                    </div>

                    <div class="stat-card success">
                        <div class="stat-icon">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                        <div class="stat-info">
                            <h3>$<?= number_format($stats['ventas_mes'], 2) ?></h3>
                            <p>Ventas <?= (isset($filtro['mes']) && !empty($filtro['mes'])) ? 'del Periodo' : 'del Mes' ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Grid de Contenido Principal -->
            <div class="content-grid">
                <!-- Columna Principal - Pedidos -->
                <div class="main-column">
                    <div class="content-card card mb-4">
                        <div class="card-header">
                            <h5><i class="fas fa-list-alt me-2"></i>Ultimos pedidos pendientes</h5>
                            <span class="badge bg-primary"><?= count($pedidos_pendientes) ?> pendientes</span>
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
                                                <td>
                                                    <strong class="text-primary"><?= htmlspecialchars($pedido['numped']) ?></strong>
                                                </td>
                                                <td><?= htmlspecialchars($pedido['cliente_nombre']) ?></td>
                                                <td>
                                                    <small class="text-muted d-block"><?= date('d/m/Y', strtotime($pedido['fecha_pedido'])) ?></small>
                                                    <small class="text-muted"><?= date('H:i', strtotime($pedido['fecha_pedido'])) ?></small>
                                                </td>
                                                <td><strong class="text-success">$<?= number_format($pedido['monto_total'], 2) ?></strong></td>
                                                <td>
                                                    <span class="badge rounded-pill bg-<?= $pedido['estado'] === 'Pendiente' ? 'warning' : 'info' ?> text-dark">
                                                        <?= htmlspecialchars($pedido['estado']) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge rounded-pill bg-<?= $pedido['estado_pag'] === 'Completado' ? 'success' : ($pedido['estado_pag'] === 'Pendiente' ? 'warning' : 'secondary') ?>">
                                                        <?= htmlspecialchars($pedido['estado_pag'] ?? 'Sin pago') ?>
                                                    </span>
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

                <!-- Columna Lateral - Alertas y Acciones -->
                <div class="side-column">
                    <!-- Inventario Crítico -->
                    <div class="content-card card mb-4 border-start border-danger border-4">
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
                                    <a href="index.php?ctrl=empleado&action=inventario" class="btn btn-sm btn-outline-danger w-100">
                                        Ver Inventario Completo
                                    </a>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-3 text-muted">
                                    <i class="fas fa-check-circle text-success mb-2 d-block fs-3"></i>
                                    <p class="mb-0">Todo el stock está bien</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>



                    <!-- Acciones Rápidas -->
                    <div class="content-card card">
                        <div class="card-header">
                            <h5><i class="fas fa-bolt me-2"></i>Acciones Rápidas</h5>
                        </div>
                        <div class="card-body">
                            <div class="quick-actions">
                                <a href="index.php?ctrl=empleado&action=gestion_pedidos" class="action-btn btn btn-primary">
                                    <i class="fas fa-clipboard-list"></i>
                                    Gestionar Pedidos
                                </a>
                                <a href="index.php?ctrl=CempleadoPagos&action=reportes" class="action-btn btn btn-success">
                                    <i class="fas fa-credit-card"></i>
                                    Procesar Pagos
                                </a>
                                <a href="index.php?ctrl=empleado&action=inventario" class="action-btn btn btn-info">
                                    <i class="fas fa-boxes"></i>
                                    <?php if($user['tpusu_idtpusu'] == 3): ?>
                                        Inventario Completo
                                    <?php else: ?>
                                        Inventario
                                    <?php endif; ?>
                                </a>
                                <!-- <a href="index.php?ctrl=CempleadoPagos&action=reportes" class="action-btn btn btn-warning">
                                    <i class="fas fa-chart-bar"></i>
                                    Reportes
                                </a> -->
                            </div>
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
