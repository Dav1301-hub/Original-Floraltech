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
    <title>Gestión de Pedidos - FloralTech</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/dashboard-cliente.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --empleado-primary: #28a745;
            --empleado-secondary: #20c997;
            --empleado-accent: #17a2b8;
        }
        
        .navbar {
            background: linear-gradient(135deg, var(--empleado-primary), var(--empleado-secondary)) !important;
        }
        
        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .status-pendiente { background: #fff3cd; color: #856404; }
        .status-proceso { background: #d1ecf1; color: #0c5460; }
        .status-completado { background: #d4edda; color: #155724; }
        .status-cancelado { background: #f8d7da; color: #721c24; }
        
        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }
        
        .card-header {
            background: linear-gradient(135deg, var(--empleado-primary), var(--empleado-secondary));
            color: white;
        }
        
        .table th {
            background-color: #f8f9fa;
            font-weight: 600;
        }
        
        .filter-section {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 10px;
            margin-bottom: 2rem;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Header -->
        <nav class="navbar">
            <div class="navbar-brand">
                <i class="fas fa-seedling"></i>
                FloralTech - Gestión de Pedidos
            </div>
            <div class="navbar-user">
                <div class="user-info">
                    <p class="user-name">Bienvenido, <?= htmlspecialchars($user['nombre_completo']) ?></p>
                    <p class="user-welcome">Panel Empleado</p>
                </div>
                <a href="index.php?ctrl=empleado&action=dashboard" class="btn btn-sm btn-outline-light me-2">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </nav>

        <div class="container-fluid py-4">
            <?php if ($mensaje): ?>
                <div class="alert alert-<?= $tipo_mensaje ?> alert-dismissible fade show" role="alert">
                    <i class="fas fa-<?= $tipo_mensaje === 'success' ? 'check-circle' : ($tipo_mensaje === 'warning' ? 'exclamation-triangle' : 'info-circle') ?>"></i>
                    <?= htmlspecialchars($mensaje) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Filtros -->
            <div class="filter-section">
                <h5><i class="fas fa-filter"></i> Filtros de Búsqueda</h5>
                <form method="GET" class="row g-3">
                    <input type="hidden" name="ctrl" value="empleado">
                    <input type="hidden" name="action" value="gestion_pedidos">
                    
                    <div class="col-md-3">
                        <label class="form-label">Estado del Pedido</label>
                        <select name="estado_pedido" class="form-select">
                            <option value="">Todos los estados</option>
                            <option value="Pendiente" <?= isset($_GET['estado_pedido']) && $_GET['estado_pedido'] === 'Pendiente' ? 'selected' : '' ?>>Pendiente</option>
                            <option value="En proceso" <?= isset($_GET['estado_pedido']) && $_GET['estado_pedido'] === 'En proceso' ? 'selected' : '' ?>>En proceso</option>
                            <option value="Completado" <?= isset($_GET['estado_pedido']) && $_GET['estado_pedido'] === 'Completado' ? 'selected' : '' ?>>Completado</option>
                            <option value="Cancelado" <?= isset($_GET['estado_pedido']) && $_GET['estado_pedido'] === 'Cancelado' ? 'selected' : '' ?>>Cancelado</option>
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label">Estado del Pago</label>
                        <select name="estado_pago" class="form-select">
                            <option value="">Todos los pagos</option>
                            <option value="Pendiente" <?= isset($_GET['estado_pago']) && $_GET['estado_pago'] === 'Pendiente' ? 'selected' : '' ?>>Pendiente</option>
                            <option value="Completado" <?= isset($_GET['estado_pago']) && $_GET['estado_pago'] === 'Completado' ? 'selected' : '' ?>>Completado</option>
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label">Desde</label>
                        <input type="date" name="fecha_desde" class="form-control" value="<?= $_GET['fecha_desde'] ?? '' ?>">
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label">Hasta</label>
                        <input type="date" name="fecha_hasta" class="form-control" value="<?= $_GET['fecha_hasta'] ?? '' ?>">
                    </div>
                    
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Filtrar
                        </button>
                        <a href="index.php?ctrl=empleado&action=gestion_pedidos" class="btn btn-outline-secondary">
                            <i class="fas fa-times"></i> Limpiar
                        </a>
                    </div>
                </form>
            </div>

            <!-- Tabla de Pedidos -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-list-alt"></i> Lista de Pedidos
                        <span class="badge bg-light text-dark ms-2"><?= count($pedidos) ?> pedidos</span>
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($pedidos)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Pedido</th>
                                        <th>Cliente</th>
                                        <th>Fecha</th>
                                        <th>Productos</th>
                                        <th>Monto Total</th>
                                        <th>Estado Pedido</th>
                                        <th>Estado Pago</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pedidos as $pedido): ?>
                                    <tr>
                                        <td>
                                            <strong class="text-primary"><?= htmlspecialchars($pedido['numped']) ?></strong>
                                            <br>
                                            <small class="text-muted">ID: <?= $pedido['idped'] ?></small>
                                        </td>
                                        <td>
                                            <strong><?= htmlspecialchars($pedido['cliente_nombre']) ?></strong>
                                            <br>
                                            <small class="text-muted"><?= htmlspecialchars($pedido['cliente_email']) ?></small>
                                        </td>
                                        <td>
                                            <?= date('d/m/Y', strtotime($pedido['fecha_pedido'])) ?>
                                            <br>
                                            <small class="text-muted"><?= date('H:i', strtotime($pedido['fecha_pedido'])) ?></small>
                                        </td>
                                        <td>
                                            <i class="fas fa-box"></i> <?= $pedido['total_productos'] ?? 1 ?> productos
                                            <br>
                                            <small class="text-muted"><?= $pedido['cantidad'] ?> unidades</small>
                                        </td>
                                        <td>
                                            <strong class="text-success">$<?= number_format($pedido['monto_total'], 2) ?></strong>
                                        </td>
                                        <td>
                                            <span class="status-badge status-<?= strtolower(str_replace(' ', '', $pedido['estado'])) ?>">
                                                <?= htmlspecialchars($pedido['estado']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($pedido['estado_pag']): ?>
                                                <span class="status-badge status-<?= strtolower($pedido['estado_pag']) ?>">
                                                    <?= htmlspecialchars($pedido['estado_pag']) ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Sin pago</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="action-buttons">
                                                <?php if ($pedido['estado'] === 'Pendiente'): ?>
                                                    <button class="btn btn-sm btn-success" onclick="cambiarEstado(<?= $pedido['idped'] ?>, 'En proceso')">
                                                        <i class="fas fa-play"></i>
                                                    </button>
                                                <?php elseif ($pedido['estado'] === 'En proceso'): ?>
                                                    <button class="btn btn-sm btn-primary" onclick="cambiarEstado(<?= $pedido['idped'] ?>, 'Completado')">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                <?php endif; ?>
                                                
                                                <button class="btn btn-sm btn-info" onclick="verDetalle(<?= $pedido['idped'] ?>)">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                
                                                <?php if ($pedido['estado'] !== 'Completado' && $pedido['estado'] !== 'Cancelado'): ?>
                                                    <button class="btn btn-sm btn-danger" onclick="cambiarEstado(<?= $pedido['idped'] ?>, 'Cancelado')">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                            <h4>No hay pedidos</h4>
                            <p class="text-muted">No se encontraron pedidos con los filtros aplicados</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para detalles del pedido -->
    <div class="modal fade" id="detalleModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detalle del Pedido</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="detalleContent">
                    <!-- Contenido se carga dinámicamente -->
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function cambiarEstado(idPedido, nuevoEstado) {
            if (confirm(`¿Está seguro de cambiar el estado a "${nuevoEstado}"?`)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'index.php?ctrl=empleado&action=actualizar_estado_pedido';
                
                const inputId = document.createElement('input');
                inputId.type = 'hidden';
                inputId.name = 'id_pedido';
                inputId.value = idPedido;
                
                const inputEstado = document.createElement('input');
                inputEstado.type = 'hidden';
                inputEstado.name = 'nuevo_estado';
                inputEstado.value = nuevoEstado;
                
                form.appendChild(inputId);
                form.appendChild(inputEstado);
                document.body.appendChild(form);
                form.submit();
            }
        }

        function verDetalle(idPedido) {
            fetch(`index.php?ctrl=empleado&action=detalle_pedido_ajax&id=${idPedido}`)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('detalleContent').innerHTML = data;
                    new bootstrap.Modal(document.getElementById('detalleModal')).show();
                })
                .catch(error => {
                    alert('Error al cargar el detalle del pedido');
                });
        }

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
