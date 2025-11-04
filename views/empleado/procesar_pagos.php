<?php
// Vista para procesar pagos de pedidos
require_once __DIR__ . '/../../controllers/CprocesarPagos.php';
$controller = new CprocesarPagos();

// Procesar pago si se envía el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pago_id'])) {
    $pagoId = $_POST['pago_id'];
    $resultado = $controller->procesarPago($pagoId);
}

// Obtener pagos pendientes
$pagos = $controller->obtenerPagosPendientes();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Procesar Pagos - FloralTech</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --empleado-primary: #28a745;
            --empleado-secondary: #20c997;
            --empleado-accent: #17a2b8;
            --bg-light: #f8f9fa;
            --border-radius: 12px;
            --shadow: 0 2px 8px rgba(0,0,0,0.1);
            --transition: all 0.3s ease;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            margin: 0;
            padding: 0;
            min-height: 100vh;
        }

        .dashboard-container {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            max-width: 100vw;
            overflow-x: hidden;
        }

        .navbar {
            background: linear-gradient(135deg, var(--empleado-primary), var(--empleado-secondary));
            padding: 0.5rem 1rem;
            box-shadow: var(--shadow);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .navbar-brand {
            color: white !important;
            font-weight: 600;
            font-size: 1.2rem;
        }

        .navbar-user {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-info {
            text-align: right;
            margin: 0;
        }

        .user-name {
            color: white;
            margin: 0;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .user-welcome {
            color: rgba(255,255,255,0.8);
            margin: 0;
            font-size: 0.8rem;
        }

        .logout-btn {
            background: rgba(255,255,255,0.2);
            border: 1px solid rgba(255,255,255,0.3);
            color: white;
            padding: 0.4rem 0.8rem;
            border-radius: 6px;
            text-decoration: none;
            font-size: 0.85rem;
            transition: var(--transition);
        }

        .logout-btn:hover {
            background: rgba(255,255,255,0.3);
            color: white;
            text-decoration: none;
        }

        .main-content {
            flex: 1;
            padding: 1rem;
            max-width: 100%;
            box-sizing: border-box;
        }

        .content-wrapper {
            max-width: 1400px;
            margin: 0 auto;
        }

        .stats-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .stat-card {
            background: white;
            padding: 1rem;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            border-left: 4px solid var(--empleado-primary);
            transition: var(--transition);
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .stat-card h3 {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--empleado-primary);
        }

        .stat-card p {
            margin: 0.25rem 0 0 0;
            color: #6c757d;
            font-size: 0.9rem;
        }

        .content-card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            border: 1px solid #e9ecef;
            margin-bottom: 1rem;
        }

        .card-header-custom {
            background: linear-gradient(135deg, var(--empleado-primary), var(--empleado-secondary));
            color: white;
            padding: 0.75rem 1rem;
            border-radius: var(--border-radius) var(--border-radius) 0 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-weight: 600;
        }

        .table {
            margin: 0;
            font-size: 0.9rem;
        }

        .table th {
            background: var(--bg-light);
            border-top: none;
            padding: 0.75rem 0.5rem;
            font-weight: 600;
            color: #495057;
            font-size: 0.85rem;
        }

        .table td {
            padding: 0.75rem 0.5rem;
            vertical-align: middle;
            border-top: 1px solid #dee2e6;
        }

        .table-hover tbody tr:hover {
            background-color: #f8f9fa;
        }

        .btn {
            border-radius: 6px;
            font-size: 0.85rem;
            padding: 0.4rem 0.8rem;
            transition: var(--transition);
        }

        .btn-success {
            background: linear-gradient(135deg, var(--empleado-primary), var(--empleado-secondary));
            border: none;
        }

        .btn-success:hover {
            background: var(--empleado-primary);
            transform: translateY(-1px);
        }

        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.8rem;
        }

        .badge {
            font-size: 0.75rem;
            padding: 0.35rem 0.6rem;
            border-radius: 6px;
        }

        .alert {
            border-radius: 6px;
            padding: 0.75rem 1rem;
            margin-bottom: 1rem;
            border: none;
            font-size: 0.9rem;
        }

        .empty-state {
            text-align: center;
            padding: 2rem 1rem;
            color: #6c757d;
        }

        .empty-state i {
            font-size: 2.5rem;
            margin-bottom: 0.75rem;
            opacity: 0.5;
            color: var(--empleado-primary);
        }

        .empty-state h4 {
            font-size: 1.2rem;
            margin-bottom: 0.5rem;
            color: #495057;
            font-weight: 600;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .main-content {
                padding: 0.5rem;
            }
            
            .stats-cards {
                grid-template-columns: 1fr;
            }
            
            .table-responsive {
                font-size: 0.8rem;
            }
            
            .navbar-user {
                flex-direction: column;
                align-items: flex-end;
                gap: 0.5rem;
            }
        }

        @media (max-width: 576px) {
            .card-header-custom {
                flex-direction: column;
                gap: 0.5rem;
                text-align: center;
            }
            
            .table th, .table td {
                padding: 0.5rem 0.25rem;
                font-size: 0.8rem;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Navbar compacta -->
        <nav class="navbar">
            <div class="container-fluid">
                <div class="navbar-brand">
                    <i class="fas fa-credit-card me-2"></i>FloralTech - Procesar Pagos
                </div>
                <div class="navbar-user">
                    <div class="user-info">
                        <p class="user-name">Bienvenido, <?= htmlspecialchars($_SESSION['nombre_completo'] ?? 'Usuario') ?></p>
                        <p class="user-welcome">Panel Empleado</p>
                    </div>
                    <a href="index.php?ctrl=empleado&action=dashboard" class="logout-btn">
                        <i class="fas fa-arrow-left me-1"></i>Volver
                    </a>
                </div>
            </div>
        </nav>

        <div class="main-content">
            <div class="content-wrapper">
                <!-- Estadísticas rápidas -->
                <div class="stats-cards">
                    <?php 
                    $totalPagos = count($pagos);
                    $pagosPendientes = count(array_filter($pagos, function($p) { return $p['estado_pag'] === 'Pendiente'; }));
                    $pagosCompletados = count(array_filter($pagos, function($p) { return $p['estado_pag'] === 'Completado'; }));
                    $montoTotal = array_sum(array_column($pagos, 'monto'));
                    ?>
                    <div class="stat-card">
                        <h3><?= $totalPagos ?></h3>
                        <p><i class="fas fa-file-invoice-dollar me-1"></i>Total Pagos</p>
                    </div>
                    <div class="stat-card">
                        <h3><?= $pagosPendientes ?></h3>
                        <p><i class="fas fa-clock me-1"></i>Pendientes</p>
                    </div>
                    <div class="stat-card">
                        <h3><?= $pagosCompletados ?></h3>
                        <p><i class="fas fa-check-circle me-1"></i>Completados</p>
                    </div>
                    <div class="stat-card">
                        <h3>$<?= number_format($montoTotal, 2) ?></h3>
                        <p><i class="fas fa-dollar-sign me-1"></i>Monto Total</p>
                    </div>
                </div>

                <?php if (isset($resultado)): ?>
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        <i class="fas fa-info-circle me-2"></i>
                        <?php echo $resultado ? 'Pago procesado correctamente.' : 'Error al procesar el pago.'; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Lista de pagos -->
                <div class="content-card">
                    <div class="card-header-custom">
                        <span><i class="fas fa-list me-2"></i>Todos los Pagos</span>
                        <span class="badge bg-light text-dark"><?= count($pagos) ?> pagos</span>
                    </div>
                    <div class="card-body p-0">
                        <?php if (empty($pagos)): ?>
                            <div class="empty-state">
                                <i class="fas fa-credit-card"></i>
                                <h4>No hay pagos registrados</h4>
                                <p>No se encontraron pagos en el sistema</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th>ID Pago</th>
                                            <th>Cliente</th>
                                            <th>Monto</th>
                                            <th>Método</th>
                                            <th>Estado</th>
                                            <th>Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($pagos as $pago): ?>
                                            <tr>
                                                <td class="fw-bold text-primary">PAG-<?php echo $pago['idpago']; ?></td>
                                                <td><?php echo htmlspecialchars($pago['cliente']); ?></td>
                                                <td class="fw-bold">$<?php echo number_format($pago['monto'], 2); ?></td>
                                                <td>
                                                    <span class="badge bg-secondary">
                                                        <?php echo htmlspecialchars($pago['metodo_pago']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php if ($pago['estado_pag'] === 'Pendiente'): ?>
                                                        <span class="badge bg-warning">
                                                            <i class="fas fa-clock me-1"></i>Pendiente
                                                        </span>
                                                    <?php elseif ($pago['estado_pag'] === 'Procesando'): ?>
                                                        <span class="badge bg-info">
                                                            <i class="fas fa-spinner me-1"></i>Procesando
                                                        </span>
                                                    <?php elseif ($pago['estado_pag'] === 'Completado'): ?>
                                                        <span class="badge bg-success">
                                                            <i class="fas fa-check me-1"></i>Completado
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="badge bg-secondary">
                                                            <?php echo htmlspecialchars($pago['estado_pag']); ?>
                                                        </span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if ($pago['estado_pag'] !== 'Completado'): ?>
                                                        <form method="POST" class="d-inline">
                                                            <input type="hidden" name="pago_id" value="<?php echo $pago['idpago']; ?>">
                                                            <button type="submit" class="btn btn-success btn-sm">
                                                                <i class="fas fa-credit-card me-1"></i>Procesar
                                                            </button>
                                                        </form>
                                                    <?php else: ?>
                                                        <span class="text-muted">
                                                            <i class="fas fa-check-circle me-1"></i>Procesado
                                                        </span>
                                                    <?php endif; ?>
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
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
