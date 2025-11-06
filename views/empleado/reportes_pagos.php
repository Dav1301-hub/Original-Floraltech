<?php
// Obtener mensajes de sesión si existen
$mensaje = isset($_SESSION['mensaje']) ? $_SESSION['mensaje'] : '';
$tipo_mensaje = isset($_SESSION['tipo_mensaje']) ? $_SESSION['tipo_mensaje'] : '';
// Limpiar los mensajes después de mostrarlos
unset($_SESSION['mensaje']);
unset($_SESSION['tipo_mensaje']);

// Obtener filtros actuales
$filtros = [
    'fecha_inicio' => $_GET['fecha_inicio'] ?? '',
    'fecha_fin' => $_GET['fecha_fin'] ?? '',
    'estado' => $_GET['estado'] ?? '',
    'cliente' => $_GET['cliente'] ?? '',
    'metodo_pago' => $_GET['metodo_pago'] ?? '',
    'monto_min' => $_GET['monto_min'] ?? '',
    'monto_max' => $_GET['monto_max'] ?? '',
    'limite' => $_GET['limite'] ?? '50'
];

// Limitar pagos completados a solo los 5 más recientes
$pagosCompletadosRecientes = isset($pagosCompletados) ? array_slice($pagosCompletados, 0, 5) : [];
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
        
        .export-buttons {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }
        
        .btn-export {
            border-radius: 8px;
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
            transition: all 0.3s ease;
        }
        
        .btn-export:hover {
            transform: translateY(-1px);
            box-shadow: 0 0.25rem 0.5rem rgba(0,0,0,0.15);
        }

        .filters-card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            border-left: 4px solid var(--empleado-primary);
        }

        .badge.bg-primary {
            background: var(--empleado-primary) !important;
        }

        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.5rem;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--empleado-primary);
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
        }
        
        @media (max-width: 992px) {
            .content-grid {
                grid-template-columns: 1fr;
            }
            
            .export-buttons {
                justify-content: center;
            }
        }
        
        @media (max-width: 576px) {
            .main-content {
                padding: 0 0.5rem 2rem;
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

        <!-- Filtros de Búsqueda para Exportación -->
        <div class="content-card mb-4">
            <div class="card-header">
                <h5><i class="fas fa-filter me-2"></i>Filtros para Reportes</h5>
                <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#filtrosCollapse">
                    <i class="fas fa-chevron-down"></i>
                </button>
            </div>
            <div class="collapse" id="filtrosCollapse">
                <div class="card-body">
                    <form method="GET" action="" id="filtrosForm">
                        <input type="hidden" name="ctrl" value="empleado">
                        <input type="hidden" name="action" value="reportes">
                        
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label for="fecha_inicio" class="form-label">Fecha Inicio</label>
                                <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" value="<?= $filtros['fecha_inicio'] ?>">
                            </div>
                            <div class="col-md-3">
                                <label for="fecha_fin" class="form-label">Fecha Fin</label>
                                <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" value="<?= $filtros['fecha_fin'] ?>">
                            </div>
                            <div class="col-md-3">
                                <label for="estado" class="form-label">Estado</label>
                                <select class="form-select" id="estado" name="estado">
                                    <option value="">Todos los Estados</option>
                                    <option value="Pendiente" <?= $filtros['estado'] === 'Pendiente' ? 'selected' : '' ?>>Pendiente</option>
                                    <option value="Completado" <?= $filtros['estado'] === 'Completado' ? 'selected' : '' ?>>Completado</option>
                                    <option value="Procesando" <?= $filtros['estado'] === 'Procesando' ? 'selected' : '' ?>>Procesando</option>
                                    <option value="Cancelado" <?= $filtros['estado'] === 'Cancelado' ? 'selected' : '' ?>>Cancelado</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="cliente" class="form-label">Cliente</label>
                                <input type="text" class="form-control" id="cliente" name="cliente" value="<?= htmlspecialchars($filtros['cliente']) ?>" placeholder="Buscar cliente...">
                            </div>
                        </div>
                        
                        <div class="row g-3 mt-2">
                            <div class="col-md-3">
                                <label for="metodo_pago" class="form-label">Método de Pago</label>
                                <select class="form-select" id="metodo_pago" name="metodo_pago">
                                    <option value="">Todos los Métodos</option>
                                    <option value="efectivo" <?= $filtros['metodo_pago'] === 'efectivo' ? 'selected' : '' ?>>Efectivo</option>
                                    <option value="transferencia" <?= $filtros['metodo_pago'] === 'transferencia' ? 'selected' : '' ?>>Transferencia</option>
                                    <option value="tarjeta_credito" <?= $filtros['metodo_pago'] === 'tarjeta_credito' ? 'selected' : '' ?>>Tarjeta de Crédito</option>
                                    <option value="nequi" <?= $filtros['metodo_pago'] === 'nequi' ? 'selected' : '' ?>>Nequi</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="monto_min" class="form-label">Monto Mínimo</label>
                                <input type="number" class="form-control" id="monto_min" name="monto_min" value="<?= $filtros['monto_min'] ?? '' ?>" placeholder="0.00" step="0.01">
                            </div>
                            <div class="col-md-3">
                                <label for="monto_max" class="form-label">Monto Máximo</label>
                                <input type="number" class="form-control" id="monto_max" name="monto_max" value="<?= $filtros['monto_max'] ?? '' ?>" placeholder="999999.99" step="0.01">
                            </div>
                            <div class="col-md-3">
                                <label for="limite" class="form-label">Límite de Resultados</label>
                                <select class="form-select" id="limite" name="limite">
                                    <option value="50" <?= $filtros['limite'] === '50' ? 'selected' : '' ?>>50</option>
                                    <option value="100" <?= $filtros['limite'] === '100' ? 'selected' : '' ?>>100</option>
                                    <option value="200" <?= $filtros['limite'] === '200' ? 'selected' : '' ?>>200</option>
                                    <option value="500" <?= $filtros['limite'] === '500' ? 'selected' : '' ?>>500</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="mt-4 d-flex gap-2 flex-wrap align-items-center">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-1"></i>Aplicar Filtros
                            </button>
                            <a href="index.php?ctrl=empleado&action=reportes" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i>Limpiar Filtros
                            </a>
                            
                            <div class="ms-auto d-flex gap-2">
                                <button type="button" class="btn btn-danger" onclick="exportarPDF()">
                                    <i class="fas fa-file-pdf me-1"></i>Exportar PDF
                                </button>
                                <button type="button" class="btn btn-success" onclick="exportarExcel()">
                                    <i class="fas fa-file-excel me-1"></i>Exportar Excel
                                </button>
                            </div>
                        </div>
                        
                        <!-- Filtros activos -->
                        <?php 
                        $filtrosActivos = array_filter($filtros, function($value) { 
                            return !empty($value) && $value !== '20' && $value !== '50'; 
                        });
                        ?>
                        <?php if (!empty($filtrosActivos)): ?>
                        <div class="mt-3">
                            <small class="text-muted fw-bold">Filtros activos:</small>
                            <div class="mt-2">
                                <?php foreach ($filtrosActivos as $key => $value): ?>
                                    <span class="badge bg-primary me-1 mb-1">
                                        <i class="fas fa-filter me-1"></i>
                                        <?= ucfirst(str_replace('_', ' ', $key)) ?>: <?= htmlspecialchars($value) ?>
                                    </span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </form>
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
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pagosPendientes as $pago): ?>
                                    <tr>
                                        <td><strong class="text-primary"><?= htmlspecialchars($pago['numped']) ?></strong></td>
                                        <td><?= htmlspecialchars($pago['cliente_nombre']) ?></td>
                                        <td><strong class="text-warning">$<?= number_format($pago['monto'], 2) ?></strong></td>
                                        <td><small><?= date('d/m H:i', strtotime($pago['fecha_pago'])) ?></small></td>
                                        <td><span class="badge bg-secondary"><?= htmlspecialchars($pago['metodo_pago']) ?></span></td>
                                        <td>
                                            <form method="POST" action="index.php?ctrl=CempleadoPagos&action=actualizarEstadoPago" style="display: inline;">
                                                <input type="hidden" name="id_pago" value="<?= $pago['idpago'] ?>">
                                                <input type="hidden" name="nuevo_estado" value="Completado">
                                                <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('¿Marcar como completado?')">
                                                    <i class="fas fa-check"></i>
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

            <!-- Pagos Completados Recientes -->
            <div class="content-card">
                <div class="card-header">
                    <h5><i class="fas fa-check-circle me-2"></i>Pagos Completados Recientes</h5>
                    <small class="text-muted"><?= count($pagosCompletadosRecientes) ?> recientes</small>
                </div>
                <div class="card-body">
                    <?php if (empty($pagosCompletadosRecientes)): ?>
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
                                    <?php foreach ($pagosCompletadosRecientes as $pago): ?>
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

        // Función para exportar PDF con filtros
        function exportarPDF() {
            const filtros = obtenerFiltrosActuales();
            const url = `index.php?ctrl=CempleadoPagos&action=generarPDF&${filtros}`;
            window.open(url, '_blank');
        }

        // Función para exportar Excel con filtros
        function exportarExcel() {
            const filtros = obtenerFiltrosActuales();
            const url = `index.php?ctrl=CempleadoPagos&action=exportarExcel&${filtros}`;
            window.open(url, '_blank');
        }

        // Función para obtener los filtros actuales del formulario
        function obtenerFiltrosActuales() {
            const form = document.getElementById('filtrosForm');
            const formData = new FormData(form);
            const params = new URLSearchParams();
            
            for (let [key, value] of formData.entries()) {
                if (value && value.trim() !== '' && key !== 'ctrl' && key !== 'action') {
                    params.append(key, value);
                }
            }
            
            return params.toString();
        }

        // Auto submit en cambio de filtros importantes
        document.addEventListener('DOMContentLoaded', function() {
            const autoSubmitInputs = ['fecha_inicio', 'fecha_fin', 'estado'];
            
            autoSubmitInputs.forEach(inputId => {
                const input = document.getElementById(inputId);
                if (input) {
                    input.addEventListener('change', function() {
                        setTimeout(() => {
                            document.getElementById('filtrosForm').submit();
                        }, 300);
                    });
                }
            });
        });

        // Confirmar acciones de estado
        function confirmarAccion(mensaje) {
            return confirm(mensaje);
        }
    </script>
</body>
</html>