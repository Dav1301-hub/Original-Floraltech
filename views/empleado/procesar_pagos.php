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
    <title>Procesar Pagos - FloralTech</title>
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
        
        .payment-card {
            border: 2px solid #e9ecef;
            border-radius: 15px;
            transition: all 0.3s ease;
            margin-bottom: 1rem;
        }
        
        .payment-card:hover {
            border-color: var(--empleado-primary);
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.1);
        }
        
        .payment-header {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            padding: 1rem;
            border-radius: 13px 13px 0 0;
            border-bottom: 1px solid #dee2e6;
        }
        
        .method-badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
        }
        
        .method-transferencia { background: #e3f2fd; color: #1565c0; }
        .method-efectivo { background: #f3e5f5; color: #7b1fa2; }
        .method-tarjeta { background: #fff3e0; color: #ef6c00; }
        
        .amount-display {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--empleado-primary);
        }
        
        .verification-section {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 10px;
            margin-top: 1rem;
        }
        
        .btn-verify {
            background: var(--empleado-primary);
            border-color: var(--empleado-primary);
            color: white;
        }
        
        .btn-verify:hover {
            background: #218838;
            border-color: #218838;
            color: white;
        }
        
        .btn-reject {
            background: #dc3545;
            border-color: #dc3545;
            color: white;
        }
        
        .btn-reject:hover {
            background: #c82333;
            border-color: #c82333;
            color: white;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Header -->
        <nav class="navbar">
            <div class="navbar-brand">
                <i class="fas fa-seedling"></i>
                FloralTech - Procesar Pagos
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

            <!-- Estadísticas de Pagos -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-clock fa-2x text-warning mb-2"></i>
                            <h4><?= number_format($stats['pendientes']) ?></h4>
                            <p class="text-muted">Pagos Pendientes</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-dollar-sign fa-2x text-success mb-2"></i>
                            <h4>$<?= number_format($stats['monto_pendiente'], 2) ?></h4>
                            <p class="text-muted">Monto Pendiente</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-check-circle fa-2x text-primary mb-2"></i>
                            <h4><?= number_format($stats['verificados_hoy']) ?></h4>
                            <p class="text-muted">Verificados Hoy</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-chart-line fa-2x text-info mb-2"></i>
                            <h4>$<?= number_format($stats['verificados_monto'], 2) ?></h4>
                            <p class="text-muted">Monto Verificado</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lista de Pagos Pendientes -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-credit-card"></i> Pagos Pendientes de Verificación
                        <span class="badge bg-light text-dark ms-2"><?= count($pagos_pendientes) ?> pagos</span>
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($pagos_pendientes)): ?>
                        <?php foreach ($pagos_pendientes as $pago): ?>
                            <div class="payment-card">
                                <div class="payment-header">
                                    <div class="row align-items-center">
                                        <div class="col-md-6">
                                            <h6 class="mb-1">
                                                <strong>Pedido: <?= htmlspecialchars($pago['numped']) ?></strong>
                                            </h6>
                                            <p class="mb-0 text-muted">
                                                Cliente: <?= htmlspecialchars($pago['cliente_nombre']) ?>
                                            </p>
                                        </div>
                                        <div class="col-md-3 text-center">
                                            <span class="method-badge method-<?= strtolower($pago['metodo_pago']) ?>">
                                                <i class="fas fa-<?= $pago['metodo_pago'] === 'Transferencia' ? 'exchange-alt' : ($pago['metodo_pago'] === 'Efectivo' ? 'money-bill' : 'credit-card') ?>"></i>
                                                <?= htmlspecialchars($pago['metodo_pago']) ?>
                                            </span>
                                        </div>
                                        <div class="col-md-3 text-end">
                                            <div class="amount-display">
                                                $<?= number_format($pago['monto'], 2) ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <p><strong>Fecha del Pago:</strong> <?= date('d/m/Y H:i', strtotime($pago['fecha_pago'])) ?></p>
                                            
                                            <?php if (isset($pago['referencia_pago']) && $pago['referencia_pago']): ?>
                                                <p><strong>Referencia:</strong> <?= htmlspecialchars($pago['referencia_pago']) ?></p>
                                            <?php endif; ?>
                                            
                                            <?php if (isset($pago['observaciones']) && $pago['observaciones']): ?>
                                                <p><strong>Observaciones:</strong> <?= htmlspecialchars($pago['observaciones']) ?></p>
                                            <?php endif; ?>
                                            
                                            <?php if (isset($pago['archivo_comprobante']) && $pago['archivo_comprobante']): ?>
                                                <p>
                                                    <strong>Comprobante:</strong>
                                                    <a href="<?= htmlspecialchars($pago['archivo_comprobante']) ?>" target="_blank" class="btn btn-sm btn-outline-info">
                                                        <i class="fas fa-file-download"></i> Ver archivo
                                                    </a>
                                                </p>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <div class="col-md-4">
                                            <div class="verification-section">
                                                <h6><i class="fas fa-tasks"></i> Acciones de Verificación</h6>
                                                
                                                <form method="POST" action="index.php?ctrl=empleado&action=verificar_pago" class="mt-3">
                                                    <input type="hidden" name="id_pago" value="<?= $pago['idpago'] ?>">
                                                    
                                                    <div class="mb-3">
                                                        <label class="form-label">Observaciones de verificación:</label>
                                                        <textarea name="observaciones_empleado" class="form-control" rows="3" placeholder="Agregar comentarios sobre la verificación..."></textarea>
                                                    </div>
                                                    
                                                    <div class="d-grid gap-2">
                                                        <button type="submit" name="accion" value="aprobar" class="btn btn-verify">
                                                            <i class="fas fa-check"></i> Aprobar Pago
                                                        </button>
                                                        <button type="submit" name="accion" value="rechazar" class="btn btn-reject">
                                                            <i class="fas fa-times"></i> Rechazar Pago
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                            <h4>¡Excelente trabajo!</h4>
                            <p class="text-muted">No hay pagos pendientes de verificación</p>
                            <a href="index.php?ctrl=empleado&action=dashboard" class="btn btn-primary">
                                <i class="fas fa-arrow-left"></i> Volver al Dashboard
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Historial de Pagos Verificados (últimos 10) -->
            <?php if (!empty($pagos_verificados)): ?>
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-history"></i> Últimos Pagos Verificados
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead>
                                    <tr>
                                        <th>Pedido</th>
                                        <th>Cliente</th>
                                        <th>Método</th>
                                        <th>Monto</th>
                                        <th>Verificado por</th>
                                        <th>Fecha Verificación</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pagos_verificados as $pago): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($pago['numped']) ?></td>
                                        <td><?= htmlspecialchars($pago['cliente_nombre']) ?></td>
                                        <td>
                                            <span class="badge bg-info">
                                                <?= htmlspecialchars($pago['metodo_pago']) ?>
                                            </span>
                                        </td>
                                        <td>$<?= number_format($pago['monto'], 2) ?></td>
                                        <td><?= htmlspecialchars($pago['verificado_por']) ?></td>
                                        <td><?= date('d/m H:i', strtotime($pago['fecha_verificacion'])) ?></td>
                                        <td>
                                            <span class="badge bg-<?= $pago['estado_pag'] === 'Completado' ? 'success' : 'danger' ?>">
                                                <?= htmlspecialchars($pago['estado_pag']) ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
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

        // Confirmación antes de aprobar/rechazar
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function(e) {
                const accion = e.submitter.value;
                const accionTexto = accion === 'aprobar' ? 'aprobar' : 'rechazar';
                
                if (!confirm(`¿Está seguro de ${accionTexto} este pago?`)) {
                    e.preventDefault();
                }
            });
        });

        // Auto-refresh cada 2 minutos para capturar nuevos pagos
        setInterval(function() {
            if (document.querySelectorAll('.payment-card').length === 0) {
                location.reload();
            }
        }, 120000);
    </script>
</body>
</html>
