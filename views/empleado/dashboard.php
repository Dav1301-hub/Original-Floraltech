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
    <link rel="stylesheet" href="assets/dashboard-cliente.css">
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
        
        .dashboard-container {
            min-height: 100vh;
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
        
        /* Tarjeta de Bienvenida */
        .welcome-section {
            margin-bottom: 2rem;
        }
        
        .welcome-card {
            background: linear-gradient(135deg, var(--empleado-primary), var(--empleado-secondary));
            border: none;
            border-radius: var(--border-radius);
            color: white;
            box-shadow: var(--shadow-hover);
        }
        
        .welcome-header h2 {
            margin-bottom: 0.5rem;
        }
        
        .welcome-stats {
            margin-top: 1rem;
            display: flex;
            gap: 2rem;
        }
        
        .welcome-stats span {
            opacity: 0.9;
        }
        
        /* Grid de Estadísticas */
        .stats-section {
            margin-bottom: 2rem;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
        }
        
        .stat-card {
            background: white;
            border-radius: var(--border-radius);
            padding: 1.5rem;
            display: flex;
            align-items: center;
            box-shadow: var(--shadow);
            transition: all 0.3s ease;
            border-left: 4px solid #dee2e6;
        }
        
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-hover);
        }
        
        .stat-card.success { border-left-color: var(--empleado-primary); }
        .stat-card.info { border-left-color: var(--empleado-accent); }
        .stat-card.warning { border-left-color: #ffc107; }
        .stat-card.danger { border-left-color: #dc3545; }
        
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
        .stat-card.info .stat-icon { color: var(--empleado-accent); }
        .stat-card.warning .stat-icon { color: #ffc107; }
        .stat-card.danger .stat-icon { color: #dc3545; }
        
        .stat-info h3 {
            margin: 0;
            font-size: 2rem;
            font-weight: 700;
            color: #333;
        }
        
        .stat-info p {
            margin: 0;
            color: #6c757d;
            font-weight: 500;
        }
        
        /* Grid Principal de Contenido */
        .content-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 2rem;
            margin-bottom: 2rem;
        }
        
        @media (max-width: 992px) {
            .content-grid {
                grid-template-columns: 1fr;
            }
        }
        
        /* Tarjetas de Contenido */
        .content-card {
            background: white;
            border: none;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            transition: all 0.3s ease;
        }
        
        .content-card:hover {
            box-shadow: var(--shadow-hover);
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
        
        .card-header h5 {
            margin: 0;
            font-weight: 600;
            color: #333;
        }
        
        .card-body {
            padding: 1.5rem;
        }
        
        /* Estados Vacíos */
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
        
        .empty-state h4 {
            margin-bottom: 0.5rem;
            color: #495057;
        }
        
        /* Acciones Rápidas */
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }
        
        .quick-actions .btn {
            padding: 1rem;
            border-radius: var(--border-radius);
            font-weight: 500;
            transition: all 0.3s ease;
            text-align: center;
        }
        
        .quick-actions .btn i {
            display: block;
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }
        
        .quick-actions .btn-primary {
            background: var(--empleado-primary);
            border-color: var(--empleado-primary);
        }
        
        .quick-actions .btn-primary:hover {
            background: #218838;
            border-color: #218838;
            transform: translateY(-2px);
        }
        
        .quick-actions .btn-outline {
            background: white;
            color: var(--empleado-primary);
            border: 2px solid var(--empleado-primary);
        }
        
        .quick-actions .btn-outline:hover {
            background: var(--empleado-primary);
            color: white;
            transform: translateY(-2px);
        }
        
        /* Tablas */
        .table-responsive {
            border-radius: var(--border-radius);
        }
        
        .table {
            margin-bottom: 0;
        }
        
        .table th {
            background-color: var(--bg-light);
            border-top: none;
            font-weight: 600;
            color: #495057;
        }
        
        .table td {
            vertical-align: middle;
        }
        
        /* Lista de Pagos en Sidebar */
        .payment-list {
            max-height: 400px;
            overflow-y: auto;
        }
        
        .payment-item {
            transition: all 0.2s ease;
        }
        
        .payment-item:hover {
            background-color: var(--bg-light);
            border-radius: 8px;
            padding: 0.75rem !important;
            margin: 0 -0.75rem 0.75rem -0.75rem !important;
        }
        
        .payment-item:last-child {
            border-bottom: none !important;
            margin-bottom: 0 !important;
        }
        
        /* Alertas */
        .alert {
            border: none;
            border-radius: var(--border-radius);
            margin-bottom: 2rem;
        }
        
        /* Navbar mejorado */
        .navbar-brand {
            font-weight: 600;
            font-size: 1.25rem;
        }
        
        .navbar-user {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .user-info {
            text-align: right;
        }
        
        .user-name {
            margin: 0;
            font-weight: 600;
            color: white;
        }
        
        .user-welcome {
            margin: 0;
            font-size: 0.9rem;
            opacity: 0.9;
            color: white;
        }
        
        .logout-btn {
            background: rgba(255, 255, 255, 0.2) !important;
            border: 1px solid rgba(255, 255, 255, 0.3) !important;
            color: white !important;
            border-radius: 8px;
            padding: 0.5rem 1rem;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .logout-btn:hover {
            background: rgba(255, 255, 255, 0.3) !important;
            color: white !important;
            transform: translateY(-1px);
        }
        
        /* Responsivo */
        @media (max-width: 768px) {
            .main-content {
                padding: 0 1rem 2rem;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .welcome-stats {
                flex-direction: column;
                gap: 1rem;
            }
            
            .quick-actions {
                grid-template-columns: 1fr;
            }
        }
    </style>
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
                        <div class="welcome-header">
                            <h2><i class="fas fa-chart-line me-2"></i>Dashboard de Empleado</h2>
                            <p class="mb-0">Gestiona pedidos, pagos e inventario desde tu panel de control</p>
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
                            <p>Pedidos Hoy</p>
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
                            <p>Ventas del Mes</p>
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
                        </div>
                        <div class="card-body">
                            <?php if (!empty($pedidos_pendientes)): ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
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
                                            <?php foreach (array_slice($pedidos_pendientes, 0, 3) as $pedido): ?>

                                            <tr>
                                                <td>
                                                    <strong class="text-primary"><?= htmlspecialchars($pedido['numped']) ?></strong>
                                                </td>
                                                <td><?= htmlspecialchars($pedido['cliente_nombre']) ?></td>
                                                <td>
                                                    <small class="text-muted"><?= date('d/m H:i', strtotime($pedido['fecha_pedido'])) ?></small>
                                                </td>
                                                <td><strong class="text-success">$<?= number_format($pedido['monto_total'], 2) ?></strong></td>
                                                <td>
                                                    <span class="badge bg-<?= $pedido['estado'] === 'Pendiente' ? 'warning' : 'info' ?>">
                                                        <?= htmlspecialchars($pedido['estado']) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-<?= $pedido['estado_pag'] === 'Completado' ? 'success' : ($pedido['estado_pag'] === 'Pendiente' ? 'warning' : 'secondary') ?>">
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



                    <!-- Acciones Rápidas -->
                    <div class="content-card card">
                        <div class="card-header">
                            <h5><i class="fas fa-bolt me-2"></i>Acciones Rápidas</h5>
                        </div>
                        <div class="card-body">
                            <div class="quick-actions">
                                <a href="index.php?ctrl=empleado&action=gestion_pedidos" class="btn btn-primary">
                                    <i class="fas fa-clipboard-list"></i>
                                    Gestionar Pedidos
                                </a>
                                <a href="index.php?ctrl=empleado&action=procesar_pagos" class="btn btn-outline">
                                    <i class="fas fa-credit-card"></i>
                                    Procesar Pagos
                                </a>
                                <a href="index.php?ctrl=empleado&action=inventario" class="btn btn-outline">
                                    <i class="fas fa-boxes"></i>
                                    <?php if($user['tpusu_idtpusu'] == 3): ?>
                                        Inventario Completo
                                    <?php else: ?>
                                        Inventario
                                    <?php endif; ?>
                                </a>
                                <a href="index.php?ctrl=CempleadoPagos&action=index" class="btn btn-outline">

                                    <i class="fas fa-chart-bar"></i>
                                    Reportes
                                </a>
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
