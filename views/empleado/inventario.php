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
    <title>Inventario - FloralTech</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/inventario.css">
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
            background: linear-gradient(135deg, var(--empleado-primary), var(--empleado-secondary)) !important;
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
            gap: 0.5rem;
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

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .stat-card {
            background: white;
            border-radius: var(--border-radius);
            padding: 1rem;
            display: flex;
            align-items: center;
            box-shadow: var(--shadow);
            border-left: 4px solid #dee2e6;
            transition: var(--transition);
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .stat-card.success { border-left-color: var(--empleado-primary); }
        .stat-card.warning { border-left-color: #ffc107; }
        .stat-card.danger { border-left-color: #dc3545; }
        .stat-card.info { border-left-color: var(--empleado-accent); }

        .stat-icon {
            background: var(--bg-light);
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 0.75rem;
            font-size: 1.2rem;
        }

        .stat-card.success .stat-icon { color: var(--empleado-primary); }
        .stat-card.warning .stat-icon { color: #ffc107; }
        .stat-card.danger .stat-icon { color: #dc3545; }
        .stat-card.info .stat-icon { color: var(--empleado-accent); }

        .stat-info h3 {
            margin: 0;
            font-size: 1.3rem;
            font-weight: 600;
            color: #343a40;
        }

        .stat-info p {
            margin: 0.2rem 0 0 0;
            color: #6c757d;
            font-size: 0.85rem;
        }

        .search-filters {
            background: white;
            border-radius: var(--border-radius);
            padding: 1rem;
            margin-bottom: 1rem;
            box-shadow: var(--shadow);
            border: 1px solid #e9ecef;
        }

        .content-card {
            background: white;
            border: 1px solid #e9ecef;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            margin-bottom: 1rem;
        }

        .card-header {
            background: linear-gradient(135deg, var(--empleado-primary), var(--empleado-secondary));
            color: white;
            border-radius: var(--border-radius) var(--border-radius) 0 0 !important;
            padding: 0.75rem 1rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-weight: 600;
            border-bottom: none;
        }

        .card-header h5 {
            margin: 0;
            font-size: 1rem;
        }

        .card-header small {
            color: rgba(255,255,255,0.8);
            font-size: 0.8rem;
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

        .btn {
            border-radius: 6px;
            font-size: 0.85rem;
            padding: 0.4rem 0.8rem;
            transition: var(--transition);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--empleado-primary), var(--empleado-secondary));
            border: none;
        }

        .btn-primary:hover {
            background: var(--empleado-primary);
            transform: translateY(-1px);
        }

        .btn-outline-light {
            border: 1px solid rgba(255,255,255,0.3);
            color: white;
        }

        .btn-outline-light:hover {
            background: rgba(255,255,255,0.2);
            color: white;
        }

        .badge-stock {
            font-size: 0.75rem;
            padding: 0.35rem 0.6rem;
            border-radius: 6px;
        }

        .stock-alto { background-color: #d1e7dd; color: #0f5132; }
        .stock-medio { background-color: #fff3cd; color: #664d03; }
        .stock-bajo { background-color: #f8d7da; color: #721c24; }
        .stock-sin { background-color: #e2e3e5; color: #41464b; }

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
            
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 0.75rem;
            }
            
            .stat-card {
                padding: 0.75rem;
            }
            
            .stat-icon {
                width: 40px;
                height: 40px;
                font-size: 1rem;
                margin-right: 0.5rem;
            }
            
            .stat-info h3 {
                font-size: 1.1rem;
            }
            
            .search-filters {
                padding: 0.75rem;
            }
            
            .table-responsive {
                font-size: 0.8rem;
            }
            
            .navbar-user {
                flex-direction: column;
                gap: 0.25rem;
            }
        }

        @media (max-width: 576px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .card-header {
                flex-direction: column;
                gap: 0.25rem;
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
                    <i class="fas fa-seedling me-2"></i>FloralTech - Inventario
                </div>
                <div class="navbar-user">
                    <a href="index.php?ctrl=empleado&action=dashboard" class="btn btn-outline-light btn-sm me-2">
                        <i class="fas fa-arrow-left me-1"></i>Volver
                    </a>
                    <a href="index.php?ctrl=login&action=logout" class="btn btn-outline-light btn-sm">
                        <i class="fas fa-sign-out-alt me-1"></i>Salir
                    </a>
                </div>
            </div>
        </nav>

        <div class="main-content">
            <div class="content-wrapper">
                <?php if ($mensaje): ?>
                    <div class="alert alert-<?= $tipo_mensaje ?> alert-dismissible fade show" role="alert">
                        <i class="fas fa-<?= $tipo_mensaje === 'success' ? 'check-circle' : ($tipo_mensaje === 'warning' ? 'exclamation-triangle' : 'info-circle') ?> me-2"></i>
                        <?= htmlspecialchars($mensaje) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Estadísticas del Inventario -->
                <div class="stats-grid">
                    <div class="stat-card success">
                        <div class="stat-icon">
                            <i class="fas fa-box"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?= number_format($stats['total_productos'] ?? 0) ?></h3>
                            <p>Total Productos</p>
                        </div>
                    </div>

                    <div class="stat-card warning">
                        <div class="stat-icon">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?= number_format($stats['stock_bajo'] ?? 0) ?></h3>
                            <p>Stock Bajo</p>
                        </div>
                    </div>

                    <div class="stat-card danger">
                        <div class="stat-icon">
                            <i class="fas fa-times-circle"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?= number_format($stats['sin_stock'] ?? 0) ?></h3>
                            <p>Sin Stock</p>
                        </div>
                    </div>

                    <div class="stat-card info">
                        <div class="stat-icon">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                        <div class="stat-info">
                            <h3>$<?= number_format($stats['valor_total'] ?? 0, 2) ?></h3>
                            <p>Valor Total</p>
                        </div>
                    </div>
                </div>

                <!-- Filtros de Búsqueda compactos -->
                <div class="search-filters">
                    <form method="GET" action="" class="row g-2">
                        <input type="hidden" name="ctrl" value="empleado">
                        <input type="hidden" name="action" value="inventario">
                        
                        <div class="col-md-4">
                            <label for="buscar" class="form-label">Buscar Producto</label>
                            <input type="text" class="form-control" id="buscar" name="buscar" value="<?= htmlspecialchars($_GET['buscar'] ?? '') ?>" placeholder="Nombre del producto...">
                        </div>
                        
                        <div class="col-md-3">
                            <label for="categoria" class="form-label">Naturaleza</label>
                            <select class="form-select" id="categoria" name="categoria">
                                <option value="">Todas las naturalezas</option>
                                <option value="Natural" <?= ($_GET['categoria'] ?? '') === 'Natural' ? 'selected' : '' ?>>Natural</option>
                                <option value="Artificial" <?= ($_GET['categoria'] ?? '') === 'Artificial' ? 'selected' : '' ?>>Artificial</option>
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <label for="stock_estado" class="form-label">Estado de Stock</label>
                            <select class="form-select" id="stock_estado" name="stock_estado">
                                <option value="">Todos los estados</option>
                                <option value="alto" <?= ($_GET['stock_estado'] ?? '') === 'alto' ? 'selected' : '' ?>>Stock Alto (>20)</option>
                                <option value="medio" <?= ($_GET['stock_estado'] ?? '') === 'medio' ? 'selected' : '' ?>>Stock Medio (5-20)</option>
                                <option value="bajo" <?= ($_GET['stock_estado'] ?? '') === 'bajo' ? 'selected' : '' ?>>Stock Bajo (1-4)</option>
                                <option value="sin_stock" <?= ($_GET['stock_estado'] ?? '') === 'sin_stock' ? 'selected' : '' ?>>Sin Stock</option>
                            </select>
                        </div>
                        
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search me-1"></i>Filtrar
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Tabla de Inventario -->
                <div class="content-card">
                    <div class="card-header">
                        <h5><i class="fas fa-boxes me-2"></i>Inventario de Productos</h5>
                        <small><?= count($productos ?? []) ?> productos encontrados</small>
                    </div>
                    <div class="card-body p-0">
                        <?php if (empty($productos)): ?>
                            <div class="empty-state">
                                <i class="fas fa-box-open"></i>
                                <h4>No hay productos en inventario</h4>
                                <p>No se encontraron productos que coincidan con los filtros aplicados</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Producto</th>
                                            <th>Naturaleza</th>
                                            <th>Stock</th>
                                            <th>Precio</th>
                                            <th>Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($productos as $producto): ?>
                                        <tr>
                                            <td><strong class="text-primary"><?= htmlspecialchars($producto['idtflor']) ?></strong></td>
                                            <td><?= htmlspecialchars($producto['nombre']) ?></td>
                                            <td>
                                                <span class="badge bg-secondary">
                                                    <?= htmlspecialchars($producto['naturaleza']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <strong><?= number_format($producto['cantidad_disponible']) ?></strong>
                                            </td>
                                            <td><strong class="text-success">$<?= number_format($producto['precio'], 2) ?></strong></td>
                                            <td>
                                                <?php
                                                $stock = $producto['cantidad_disponible'];
                                                if ($stock == 0): ?>
                                                    <span class="badge stock-sin">Sin Stock</span>
                                                <?php elseif ($stock <= 4): ?>
                                                    <span class="badge stock-bajo">Stock Bajo</span>
                                                <?php elseif ($stock <= 20): ?>
                                                    <span class="badge stock-medio">Stock Medio</span>
                                                <?php else: ?>
                                                    <span class="badge stock-alto">Stock Alto</span>
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
