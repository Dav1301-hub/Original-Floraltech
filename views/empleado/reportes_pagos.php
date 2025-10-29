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
    <title>Reportes de Pagos - FloralTech</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/dashboard-general.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --empleado-primary: #28a745;
            --empleado-secondary: #20c997;
            --empleado-accent: #17a2b8;
            --bg-light: #f8f9fa;
            --border-radius: 12px;
            --shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            --shadow-hover: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        
        body {
            background-color: var(--bg-light);
            font-family: 'Poppins', sans-serif;
        }
        
        .navbar {
            background: linear-gradient(135deg, var(--empleado-primary), var(--empleado-secondary)) !important;
            box-shadow: var(--shadow);
            margin-bottom: 2rem;
        }
        
        .main-content {
            padding: 0 1.5rem 2rem;
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .content-card {
            background: white;
            border: none;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            transition: all 0.3s ease;
            margin-bottom: 1.5rem;
        }
        
        .card-header {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-bottom: 1px solid #dee2e6;
            border-radius: var(--border-radius) var(--border-radius) 0 0 !important;
            padding: 1rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: white;
            border-radius: var(--border-radius);
            padding: 1.5rem;
            display: flex;
            align-items: center;
            box-shadow: var(--shadow);
            border-left: 4px solid #dee2e6;
        }
        
        .stat-card.success { border-left-color: var(--empleado-primary); }
        .stat-card.warning { border-left-color: #ffc107; }
        .stat-card.info { border-left-color: var(--empleado-accent); }
        
        .stat-icon {
            background: var(--bg-light);
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            font-size: 1.5rem;
        }
        
        .stat-card.success .stat-icon { color: var(--empleado-primary); }
        .stat-card.warning .stat-icon { color: #ffc107; }
        .stat-card.info .stat-icon { color: var(--empleado-accent); }
        
        .table-responsive {
            border-radius: var(--border-radius);
        }
        
        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: #6c757d;
        }
        
        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }
        
        .content-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        @media (max-width: 992px) {
            .content-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <nav class="navbar">
        <div class="container-fluid">
            <div class="navbar-brand">
                <i class="fas fa-seedling me-2"></i>
                FloralTech - Reportes de Pagos
            </div>
            <div class="navbar-user">
                <a href="index.php?ctrl=empleado&action=dashboard" class="btn btn-outline-light btn-sm me-2">
                    <i class="fas fa-arrow-left me-1"></i>Volver al Dashboard
                </a>
                <a href="index.php?ctrl=login&action=logout" class="btn btn-outline-light btn-sm">
                    <i class="fas fa-sign-out-alt me-1"></i>Cerrar Sesión
                </a>
            </div>
        </div>
    </nav>

    <div class="main-content">
        <?php if ($mensaje): ?>
            <div class="alert alert-<?= $tipo_mensaje ?> alert-dismissible fade show" role="alert">
                <i class="fas fa-<?= $tipo_mensaje === 'success' ? 'check-circle' : ($tipo_mensaje === 'warning' ? 'exclamation-triangle' : 'info-circle') ?> me-2"></i>
                <?= htmlspecialchars($mensaje) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Estadísticas de Pagos -->
        <div class="stats-grid">
            <div class="stat-card warning">
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-info">
                    <h3><?= number_format($stats['pendientes'] ?? 0) ?></h3>
                    <p>Pagos Pendientes</p>
                    <small class="text-muted">$<?= number_format($stats['monto_pendiente'] ?? 0, 2) ?></small>
                </div>
            </div>

            <div class="stat-card success">
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-info">
                    <h3><?= number_format($stats['completados'] ?? 0) ?></h3>
                    <p>Pagos Completados</p>
                    <small class="text-muted">$<?= number_format($stats['monto_completado'] ?? 0, 2) ?></small>
                </div>
            </div>

            <div class="stat-card info">
                <div class="stat-icon">
                    <i class="fas fa-calendar-day"></i>
                </div>
                <div class="stat-info">
                    <h3><?= number_format($stats['completados_hoy'] ?? 0) ?></h3>
                    <p>Completados Hoy</p>
                    <small class="text-muted">$<?= number_format($stats['monto_hoy'] ?? 0, 2) ?></small>
                </div>
            </div>
        </div>

        <!-- Grid de Contenido -->
        <div class="content-grid">
            <!-- Pagos Pendientes -->
            <div class="content-card">
                <div class="card-header">
                    <h5><i class="fas fa-clock me-2"></i>Pagos Pendientes</h5>
                    <small class="text-muted"><?= count($pagosPendientes ?? []) ?> pendientes</small>
                </div>
                <div class="card-body">
                    <?php if (empty($pagosPendientes)): ?>
                        <div class="empty-state">
                            <i class="fas fa-check-circle"></i>
                            <h4>¡Excelente!</h4>
                            <p>No hay pagos pendientes</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover table-sm">
                                <thead>
                                    <tr>
                                        <th>Pedido</th>
                                        <th>Cliente</th>
                                        <th>Monto</th>
                                        <th>Fecha</th>
                                        <th>Método</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach (array_slice($pagosPendientes ?? [], 0, 10) as $pago): ?>
                                    <tr>
                                        <td><strong class="text-primary"><?= htmlspecialchars($pago['numped']) ?></strong></td>
                                        <td><?= htmlspecialchars($pago['cliente_nombre']) ?></td>
                                        <td><strong class="text-warning">$<?= number_format($pago['monto'], 2) ?></strong></td>
                                        <td><small><?= date('d/m H:i', strtotime($pago['fecha_pago'])) ?></small></td>
                                        <td><span class="badge bg-secondary"><?= htmlspecialchars($pago['metodo_pago']) ?></span></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Pagos Completados Recientes -->
            <div class="content-card">
                <div class="card-header">
                    <h5><i class="fas fa-check-circle me-2"></i>Pagos Completados Recientes</h5>
                    <small class="text-muted"><?= count($pagosCompletados ?? []) ?> recientes</small>
                </div>
                <div class="card-body">
                    <?php if (empty($pagosCompletados)): ?>
                        <div class="empty-state">
                            <i class="fas fa-info-circle"></i>
                            <h4>Sin pagos completados</h4>
                            <p>No hay pagos completados recientes</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover table-sm">
                                <thead>
                                    <tr>
                                        <th>Pedido</th>
                                        <th>Cliente</th>
                                        <th>Monto</th>
                                        <th>Fecha</th>
                                        <th>Método</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach (array_slice($pagosCompletados ?? [], 0, 10) as $pago): ?>
                                    <tr>
                                        <td><strong class="text-primary"><?= htmlspecialchars($pago['numped']) ?></strong></td>
                                        <td><?= htmlspecialchars($pago['cliente_nombre']) ?></td>
                                        <td><strong class="text-success">$<?= number_format($pago['monto'], 2) ?></strong></td>
                                        <td><small><?= date('d/m H:i', strtotime($pago['fecha_pago'])) ?></small></td>
                                        <td><span class="badge bg-success"><?= htmlspecialchars($pago['metodo_pago']) ?></span></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Reporte Mensual -->
        <div class="content-card">
            <div class="card-header">
                <h5><i class="fas fa-chart-line me-2"></i>Reporte Mensual por Día</h5>
                <small class="text-muted">Últimos <?= count($reporteMensual ?? []) ?> días con actividad</small>
            </div>
            <div class="card-body">
                <?php if (empty($reporteMensual)): ?>
                    <div class="empty-state">
                        <i class="fas fa-chart-line"></i>
                        <h4>Sin datos del mes</h4>
                        <p>No hay actividad de pagos en el mes actual</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Total Pagos</th>
                                    <th>Monto Total</th>
                                    <th>Completados</th>
                                    <th>Pendientes</th>
                                    <th>Efectividad</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($reporteMensual ?? [] as $dia): ?>
                                <tr>
                                    <td><strong><?= date('d/m/Y', strtotime($dia['fecha'])) ?></strong></td>
                                    <td><?= number_format($dia['total_pagos']) ?></td>
                                    <td><strong class="text-primary">$<?= number_format($dia['monto_total'], 2) ?></strong></td>
                                    <td><span class="badge bg-success"><?= number_format($dia['completados']) ?></span></td>
                                    <td><span class="badge bg-warning"><?= number_format($dia['pendientes']) ?></span></td>
                                    <td>
                                        <?php 
                                        $efectividad = $dia['total_pagos'] > 0 ? ($dia['completados'] / $dia['total_pagos']) * 100 : 0;
                                        $clase = $efectividad >= 80 ? 'success' : ($efectividad >= 60 ? 'warning' : 'danger');
                                        ?>
                                        <span class="badge bg-<?= $clase ?>"><?= number_format($efectividad, 1) ?>%</span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-dismiss alerts
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>
</body>
</html>