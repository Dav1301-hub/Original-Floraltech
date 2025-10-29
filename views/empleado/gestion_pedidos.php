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
                --empleado-secondary: #28a745;
                --empleado-accent: #28a745;
                --bg-light: #f8f9fa;
                --border-radius: 16px;
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
            .main-content {
                padding: 0 1.5rem 2rem;
                max-width: 1400px;
                margin: 0 auto;
            }
            .header-card {
                background: linear-gradient(135deg, var(--empleado-primary), var(--empleado-secondary));
                border-radius: var(--border-radius);
                box-shadow: var(--shadow);
                padding: 1.5rem 2rem;
                margin-bottom: 2rem;
                display: flex;
                align-items: center;
                justify-content: space-between;
            }
            .header-title {
                font-size: 2rem;
                font-weight: 700;
                color: white;
                letter-spacing: 1px;
            }
            .header-user {
                text-align: right;
            }
            .header-user .user-name {
                font-weight: 600;
                color: white;
                margin-bottom: 0;
            }
            .header-user .user-welcome {
                font-size: 1rem;
                color: white;
                opacity: 0.9;
            }
            .header-user .btn {
                margin-top: 0.5rem;
            }
            .filter-card {
                background: white;
                border-radius: var(--border-radius);
                box-shadow: var(--shadow);
                padding: 2rem 1.5rem;
                margin-bottom: 2rem;
            }
            .filter-title {
                font-weight: 600;
                font-size: 1.3rem;
                color: var(--empleado-accent);
                margin-bottom: 1.2rem;
                letter-spacing: 0.5px;
            }
            .pedido-list-header {
                background: linear-gradient(135deg, var(--empleado-accent), var(--empleado-primary));
                color: white;
                border-radius: var(--border-radius) var(--border-radius) 0 0;
                padding: 1rem 1.5rem;
                display: flex;
                align-items: center;
                justify-content: space-between;
                font-size: 1.2rem;
                font-weight: 600;
            }
            .pedido-list-header .badge {
                background: white;
                color: var(--empleado-accent);
                font-weight: 600;
                font-size: 1rem;
                border-radius: 8px;
            }
            .content-card {
                border-radius: var(--border-radius);
                box-shadow: var(--shadow);
                border: none;
            }
            .table th {
                background-color: var(--bg-light);
                font-weight: 600;
                color: #495057;
                border-top: none;
            }
            .table td {
                vertical-align: middle;
            }
            .btn-success, .btn-outline-success {
                border-radius: 8px;
                font-weight: 500;
            }
            .btn-success {
                background: linear-gradient(135deg, var(--empleado-primary), var(--empleado-secondary));
                border: none;
            }
            .btn-success:hover {
                background: var(--empleado-primary);
            }
            .btn-outline-success {
                border: 2px solid var(--empleado-primary);
                color: var(--empleado-primary);
            }
            .btn-outline-success:hover {
                background: var(--empleado-primary);
                color: white;
            }
            .empty-state-pedidos {
                text-align: center;
                padding: 4rem 1rem;
                color: #444;
            }
            .empty-state-pedidos i {
                font-size: 3rem;
                margin-bottom: 1rem;
                opacity: 0.5;
            }
            .empty-state-pedidos h4 {
                margin-bottom: 0.5rem;
                color: #495057;
                font-weight: 600;
            }
        </style>
    </head>
    <body>
        <div class="dashboard-container">
            <div class="main-content">
                <div class="header-card mb-4">
                    <div class="header-title">
                        <i class="fas fa-seedling me-2"></i> FloralTech - Gestión de Pedidos
                    </div>
                    <div class="header-user">
                        <div class="user-name">Bienvenido, <?= htmlspecialchars($_SESSION['nombre_completo'] ?? 'Usuario') ?></div>
                        <div class="user-welcome">Panel Empleado</div>
                        <a href="index.php?ctrl=login&action=logout" class="btn btn-light logout-btn mt-2">Volver</a>
                    </div>
                </div>
                <div class="filter-card mb-4">
                    <div class="filter-title mb-3"><i class="fas fa-filter me-2"></i>Filtros de Búsqueda</div>
                    <form method="GET" action="gestion_pedidos.php" class="row g-3 align-items-end">
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
                        <div class="col-md-2 d-flex gap-2">
                            <button type="submit" class="btn btn-success"><i class="fas fa-search"></i> Filtrar</button>
                            <a href="gestion_pedidos.php" class="btn btn-link">&times; Limpiar</a>
                        </div>
                    </form>
                </div>
                <div class="content-card card mb-4">
                    <div class="pedido-list-header">
                        <span><i class="fas fa-list me-2"></i>Lista de Pedidos</span>
                        <span class="badge"><?= count($pedidos) ?> pedidos</span>
                    </div>
                    <div class="card-body">
                        <?php if (isset($resultado)): ?>
                            <div class="alert alert-info alert-dismissible fade show" role="alert">
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
                                <table class="table table-hover align-middle">
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
                                                    <form method="POST" class="d-flex align-items-center gap-2">
                                                        <select name="nuevo_estado" class="form-select form-select-sm w-auto">
                                                            <option value="Pendiente" <?php echo ($pedido['estado'] === 'Pendiente') ? 'selected' : ''; ?>>Pendiente</option>
                                                            <option value="En Proceso" <?php echo ($pedido['estado'] === 'En Proceso' || $pedido['estado'] === 'En proceso') ? 'selected' : ''; ?>>En Proceso</option>
                                                            <option value="En Preparación" <?php echo ($pedido['estado'] === 'En Preparación') ? 'selected' : ''; ?>>En Preparación</option>
                                                            <option value="Completado" <?php echo ($pedido['estado'] === 'Completado') ? 'selected' : ''; ?>>Completado</option>
                                                            <option value="Cancelado" <?php echo ($pedido['estado'] === 'Cancelado') ? 'selected' : ''; ?>>Cancelado</option>
                                                        </select>
                                                        <input type="hidden" name="pedido_id" value="<?php echo $pedido['id']; ?>">
                                                        <button type="submit" class="btn btn-success btn-sm"><i class="fas fa-sync-alt"></i> Actualizar</button>
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
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    </body>
    </html>
