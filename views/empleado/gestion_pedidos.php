<?php
// views/empleado/gestion_pedidos.php
require_once __DIR__ . '/../../controllers/Cpedido.php';
$controller = new Cpedido();

// Filtros
$estadoPedido = $_GET['estado_pedido'] ?? '';
$estadoPago = $_GET['estado_pago'] ?? '';
$fechaDesde = $_GET['fecha_desde'] ?? '';
$fechaHasta = $_GET['fecha_hasta'] ?? '';

// Procesar actualización de estado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pedido_id'], $_POST['nuevo_estado'])) {
    $pedidoId = $_POST['pedido_id'];
    $nuevoEstado = $_POST['nuevo_estado'];
    $resultado = $controller->actualizarEstadoPedido($pedidoId, $nuevoEstado);
}

// Obtener pedidos filtrados
$pedidos = $controller->obtenerPedidosFiltrados($estadoPedido, $estadoPago, $fechaDesde, $fechaHasta);
?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Gestión de Pedidos - FloralTech</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="../../assets/dashboard-cliente.css">
        <link rel="stylesheet" href="../../assets/dashboard-general.css">
        <link rel="stylesheet" href="../../assets/styles.css">
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

            .filter-card {
                background: white;
                border-radius: var(--border-radius);
                box-shadow: var(--shadow);
                padding: 1rem;
                margin-bottom: 1rem;
                border: 1px solid #e9ecef;
            }

            .filter-title {
                color: var(--empleado-primary);
                font-size: 1.1rem;
                font-weight: 600;
                margin-bottom: 0.75rem;
                border-bottom: 2px solid var(--empleado-primary);
                padding-bottom: 0.5rem;
            }

            .content-card {
                background: white;
                border-radius: var(--border-radius);
                box-shadow: var(--shadow);
                border: 1px solid #e9ecef;
                margin-bottom: 1rem;
            }

            .pedido-list-header {
                background: linear-gradient(135deg, var(--empleado-primary), var(--empleado-secondary));
                color: white;
                padding: 0.75rem 1rem;
                border-radius: var(--border-radius) var(--border-radius) 0 0;
                display: flex;
                align-items: center;
                justify-content: space-between;
                font-weight: 600;
            }

            .pedido-list-header .badge {
                background: rgba(255,255,255,0.2);
                color: white;
                border: 1px solid rgba(255,255,255,0.3);
                padding: 0.25rem 0.5rem;
                border-radius: 6px;
                font-size: 0.85rem;
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

            .form-control, .form-select {
                border-radius: 6px;
                border: 1px solid #ced4da;
                padding: 0.5rem 0.75rem;
                font-size: 0.85rem;
                transition: var(--transition);
            }

            .form-control:focus, .form-select:focus {
                border-color: var(--empleado-primary);
                box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
            }

            .form-label {
                font-weight: 500;
                margin-bottom: 0.25rem;
                font-size: 0.85rem;
                color: #495057;
            }

            .empty-state-pedidos {
                text-align: center;
                padding: 2rem 1rem;
                color: #6c757d;
            }

            .empty-state-pedidos i {
                font-size: 2.5rem;
                margin-bottom: 0.75rem;
                opacity: 0.5;
                color: var(--empleado-primary);
            }

            .empty-state-pedidos h4 {
                font-size: 1.2rem;
                margin-bottom: 0.5rem;
                color: #495057;
                font-weight: 600;
            }

            .alert {
                border-radius: 6px;
                padding: 0.75rem 1rem;
                margin-bottom: 1rem;
                border: none;
                font-size: 0.9rem;
            }

            .badge {
                font-size: 0.75rem;
                padding: 0.35rem 0.6rem;
                border-radius: 6px;
            }

            /* Responsive Design */
            @media (max-width: 768px) {
                .main-content {
                    padding: 0.5rem;
                }
                
                .filter-card {
                    padding: 0.75rem;
                }
                
                .table-responsive {
                    font-size: 0.8rem;
                }
                
                .navbar-user {
                    flex-direction: column;
                    align-items: flex-end;
                    gap: 0.5rem;
                }
                
                .user-info {
                    font-size: 0.8rem;
                }
            }

            @media (max-width: 576px) {
                .pedido-list-header {
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
                        <i class="fas fa-seedling me-2"></i>FloralTech - Gestión de Pedidos
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
                    <!-- Filtros compactos -->
                    <div class="filter-card">
                        <div class="filter-title">
                            <i class="fas fa-filter me-2"></i>Filtros de Búsqueda
                        </div>
                        <form method="GET" action="gestion_pedidos.php" class="row g-2 align-items-end">
                            <div class="col-md-3">
                                <label class="form-label">Estado del Pedido</label>
                                <select name="estado_pedido" class="form-select">
                                    <option value="">Todos los estados</option>
                                    <option value="Pendiente" <?= $estadoPedido=='Pendiente'?'selected':'' ?>>Pendiente</option>
                                    <option value="En Proceso" <?= $estadoPedido=='En Proceso'?'selected':'' ?>>En Proceso</option>
                                    <option value="En Preparación" <?= $estadoPedido=='En Preparación'?'selected':'' ?>>En Preparación</option>
                                    <option value="Completado" <?= $estadoPedido=='Completado'?'selected':'' ?>>Completado</option>
                                    <option value="Cancelado" <?= $estadoPedido=='Cancelado'?'selected':'' ?>>Cancelado</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Estado del Pago</label>
                                <select name="estado_pago" class="form-select">
                                    <option value="">Todos los pagos</option>
                                    <option value="Pendiente" <?= $estadoPago=='Pendiente'?'selected':'' ?>>Pendiente</option>
                                    <option value="Completado" <?= $estadoPago=='Completado'?'selected':'' ?>>Completado</option>
                                    <option value="Cancelado" <?= $estadoPago=='Cancelado'?'selected':'' ?>>Cancelado</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Desde</label>
                                <input type="date" name="fecha_desde" class="form-control" value="<?= htmlspecialchars($fechaDesde) ?>">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Hasta</label>
                                <input type="date" name="fecha_hasta" class="form-control" value="<?= htmlspecialchars($fechaHasta) ?>">
                            </div>
                            <div class="col-md-2 d-flex gap-1">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-search"></i> Filtrar
                                </button>
                                <a href="gestion_pedidos.php" class="btn btn-outline-secondary btn-sm">
                                    <i class="fas fa-times"></i>
                                </a>
                            </div>
                        </form>
                    </div>

                    <!-- Lista de pedidos -->
                    <div class="content-card">
                        <div class="pedido-list-header">
                            <span><i class="fas fa-list me-2"></i>Lista de Pedidos</span>
                            <span class="badge"><?= count($pedidos) ?> pedidos</span>
                        </div>
                        <div class="card-body p-0">
                            <?php if (isset($resultado)): ?>
                                <div class="alert alert-info alert-dismissible fade show m-3" role="alert">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <?php echo $resultado ? 'Estado actualizado correctamente.' : 'Error al actualizar el estado.'; ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (empty($pedidos)): ?>
                                <div class="empty-state-pedidos">
                                    <i class="fas fa-clipboard-list"></i>
                                    <h4>No hay pedidos</h4>
                                    <p>No se encontraron pedidos con los filtros aplicados</p>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th>Pedido</th>
                                                <th>Cliente</th>
                                                <th>Estado Actual</th>
                                                <th>Actualizar Estado</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($pedidos as $pedido): ?>
                                                <tr>
                                                    <td class="fw-bold text-primary">PED-<?php echo $pedido['id']; ?></td>
                                                    <td><?php echo $pedido['cliente']; ?></td>
                                                    <td>
                                                        <span class="badge bg-<?php echo ($pedido['estado'] === 'Pendiente') ? 'warning' : (($pedido['estado'] === 'Completado') ? 'success' : (($pedido['estado'] === 'Cancelado') ? 'danger' : 'info')); ?>">
                                                            <?php echo $pedido['estado']; ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <form method="POST" class="d-flex align-items-center gap-1">
                                                            <select name="nuevo_estado" class="form-select form-select-sm" style="width: auto;">
                                                                <option value="Pendiente" <?php echo ($pedido['estado'] === 'Pendiente') ? 'selected' : ''; ?>>Pendiente</option>
                                                                <option value="En Proceso" <?php echo ($pedido['estado'] === 'En Proceso' || $pedido['estado'] === 'En proceso') ? 'selected' : ''; ?>>En Proceso</option>
                                                                <option value="En Preparación" <?php echo ($pedido['estado'] === 'En Preparación') ? 'selected' : ''; ?>>En Preparación</option>
                                                                <option value="Completado" <?php echo ($pedido['estado'] === 'Completado') ? 'selected' : ''; ?>>Completado</option>
                                                                <option value="Cancelado" <?php echo ($pedido['estado'] === 'Cancelado') ? 'selected' : ''; ?>>Cancelado</option>
                                                            </select>
                                                            <input type="hidden" name="pedido_id" value="<?php echo $pedido['id']; ?>">
                                                            <button type="submit" class="btn btn-success btn-sm">
                                                                <i class="fas fa-sync-alt"></i>
                                                            </button>
                                                        </form>
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
