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
    <title>Gestión de Inventario - FloralTech</title>
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
        
        .inventory-card {
            border: 2px solid #e9ecef;
            border-radius: 15px;
            transition: all 0.3s ease;
            height: 100%;
        }
        
        .inventory-card:hover {
            border-color: var(--empleado-primary);
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.1);
        }
        
        .flower-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 10px;
        }
        
        .stock-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
        }
        
        .stock-high { background: #d4edda; color: #155724; }
        .stock-medium { background: #fff3cd; color: #856404; }
        .stock-low { background: #f8d7da; color: #721c24; }
        
        .price-tag {
            background: var(--empleado-primary);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 0 0 10px 10px;
            font-weight: 600;
            text-align: center;
        }
        
        .quick-actions {
            display: flex;
            gap: 0.5rem;
            margin-top: 1rem;
        }
        
        .btn-stock {
            flex: 1;
            font-size: 0.8rem;
        }
        
        .filter-section {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 10px;
            margin-bottom: 2rem;
        }
        
        .inventory-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
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
                FloralTech - Gestión de Inventario
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

            <!-- Estadísticas de Inventario -->
            <div class="inventory-stats">
                <div class="card text-center">
                    <div class="card-body">
                        <i class="fas fa-boxes fa-2x text-primary mb-2"></i>
                        <h4><?= number_format($stats['total_productos']) ?></h4>
                        <p class="text-muted">Total Productos</p>
                    </div>
                </div>
                <div class="card text-center">
                    <div class="card-body">
                        <i class="fas fa-exclamation-triangle fa-2x text-warning mb-2"></i>
                        <h4><?= number_format($stats['stock_bajo']) ?></h4>
                        <p class="text-muted">Stock Bajo</p>
                    </div>
                </div>
                <div class="card text-center">
                    <div class="card-body">
                        <i class="fas fa-times-circle fa-2x text-danger mb-2"></i>
                        <h4><?= number_format($stats['sin_stock']) ?></h4>
                        <p class="text-muted">Sin Stock</p>
                    </div>
                </div>
                <div class="card text-center">
                    <div class="card-body">
                        <i class="fas fa-dollar-sign fa-2x text-success mb-2"></i>
                        <h4>$<?= number_format($stats['valor_total'], 2) ?></h4>
                        <p class="text-muted">Valor Total</p>
                    </div>
                </div>
            </div>

            <!-- Filtros -->
            <div class="filter-section">
                <h5><i class="fas fa-filter"></i> Filtros de Inventario</h5>
                <form method="GET" class="row g-3">
                    <input type="hidden" name="ctrl" value="empleado">
                    <input type="hidden" name="action" value="inventario">
                    
                    <div class="col-md-3">
                        <label class="form-label">Categoría</label>
                        <select name="categoria" class="form-select">
                            <option value="">Todas las categorías</option>
                            <option value="Rosas" <?= isset($_GET['categoria']) && $_GET['categoria'] === 'Rosas' ? 'selected' : '' ?>>Rosas</option>
                            <option value="Tulipanes" <?= isset($_GET['categoria']) && $_GET['categoria'] === 'Tulipanes' ? 'selected' : '' ?>>Tulipanes</option>
                            <option value="Girasoles" <?= isset($_GET['categoria']) && $_GET['categoria'] === 'Girasoles' ? 'selected' : '' ?>>Girasoles</option>
                            <option value="Lirios" <?= isset($_GET['categoria']) && $_GET['categoria'] === 'Lirios' ? 'selected' : '' ?>>Lirios</option>
                            <option value="Orquídeas" <?= isset($_GET['categoria']) && $_GET['categoria'] === 'Orquídeas' ? 'selected' : '' ?>>Orquídeas</option>
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label">Estado de Stock</label>
                        <select name="stock_estado" class="form-select">
                            <option value="">Todos los estados</option>
                            <option value="alto" <?= isset($_GET['stock_estado']) && $_GET['stock_estado'] === 'alto' ? 'selected' : '' ?>>Stock Alto (>20)</option>
                            <option value="medio" <?= isset($_GET['stock_estado']) && $_GET['stock_estado'] === 'medio' ? 'selected' : '' ?>>Stock Medio (5-20)</option>
                            <option value="bajo" <?= isset($_GET['stock_estado']) && $_GET['stock_estado'] === 'bajo' ? 'selected' : '' ?>>Stock Bajo (<5)</option>
                            <option value="sin_stock" <?= isset($_GET['stock_estado']) && $_GET['stock_estado'] === 'sin_stock' ? 'selected' : '' ?>>Sin Stock</option>
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label">Buscar</label>
                        <input type="text" name="buscar" class="form-control" placeholder="Nombre de la flor..." value="<?= $_GET['buscar'] ?? '' ?>">
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Filtrar
                            </button>
                        </div>
                    </div>
                    
                    <div class="col-12">
                        <a href="index.php?ctrl=empleado&action=inventario" class="btn btn-outline-secondary">
                            <i class="fas fa-times"></i> Limpiar Filtros
                        </a>
                    </div>
                </form>
            </div>

            <!-- Grid de Productos -->
            <div class="row">
                <?php if (!empty($productos)): ?>
                    <?php foreach ($productos as $producto): ?>
                        <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                            <div class="card inventory-card">
                                <div class="position-relative">
                                    <img src="<?= !empty($producto['imagen']) ? htmlspecialchars($producto['imagen']) : 'assets/images/flower-placeholder.jpg' ?>" 
                                         class="flower-image" 
                                         alt="<?= htmlspecialchars($producto['nombre']) ?>"
                                         onerror="this.src='assets/images/flower-placeholder.jpg'">
                                    
                                    <?php
                                    $stock = $producto['cantidad_disponible'];
                                    $stockClass = $stock > 20 ? 'stock-high' : ($stock > 5 ? 'stock-medium' : 'stock-low');
                                    ?>
                                    <span class="stock-badge <?= $stockClass ?>">
                                        Stock: <?= $stock ?>
                                    </span>
                                </div>
                                
                                <div class="card-body">
                                    <h5 class="card-title"><?= htmlspecialchars($producto['nombre']) ?></h5>
                                    <p class="card-text text-muted">
                                        <small><i class="fas fa-tag"></i> <?= htmlspecialchars($producto['naturaleza']) ?></small>
                                    </p>
                                    
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="text-muted">Precio:</span>
                                        <strong class="text-success">$<?= number_format($producto['precio'], 2) ?></strong>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <span class="text-muted">Disponible:</span>
                                        <strong class="<?= $stock > 5 ? 'text-success' : ($stock > 0 ? 'text-warning' : 'text-danger') ?>">
                                            <?= $stock ?> unidades
                                        </strong>
                                    </div>
                                    
                                    <div class="quick-actions">
                                        <button class="btn btn-sm btn-outline-primary btn-stock" 
                                                onclick="ajustarStock(<?= $producto['idtflor'] ?>, '<?= htmlspecialchars($producto['nombre']) ?>', <?= $stock ?>)">
                                            <i class="fas fa-edit"></i> Ajustar
                                        </button>
                                        <button class="btn btn-sm btn-outline-success btn-stock" 
                                                onclick="agregarStock(<?= $producto['idtflor'] ?>, '<?= htmlspecialchars($producto['nombre']) ?>')">
                                            <i class="fas fa-plus"></i> Agregar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body text-center py-5">
                                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                                <h4>No se encontraron productos</h4>
                                <p class="text-muted">No hay productos que coincidan con los filtros aplicados</p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Modal para ajustar stock -->
    <div class="modal fade" id="stockModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ajustar Stock</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="index.php?ctrl=empleado&action=actualizar_stock">
                    <div class="modal-body">
                        <input type="hidden" id="producto_id" name="producto_id">
                        
                        <div class="mb-3">
                            <label class="form-label">Producto:</label>
                            <p class="form-control-plaintext" id="producto_nombre"></p>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Stock Actual:</label>
                            <p class="form-control-plaintext fw-bold text-primary" id="stock_actual"></p>
                        </div>
                        
                        <div class="mb-3">
                            <label for="nuevo_stock" class="form-label">Nuevo Stock:</label>
                            <input type="number" class="form-control" id="nuevo_stock" name="nuevo_stock" min="0" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="motivo" class="form-label">Motivo del Ajuste:</label>
                            <select class="form-select" id="motivo" name="motivo" required>
                                <option value="">Seleccionar motivo...</option>
                                <option value="Recepción de mercancía">Recepción de mercancía</option>
                                <option value="Corrección de inventario">Corrección de inventario</option>
                                <option value="Merma/Deterioro">Merma/Deterioro</option>
                                <option value="Venta directa">Venta directa</option>
                                <option value="Otro">Otro</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="observaciones" class="form-label">Observaciones:</label>
                            <textarea class="form-control" id="observaciones" name="observaciones" rows="3" placeholder="Detalles adicionales..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Actualizar Stock</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function ajustarStock(id, nombre, stockActual) {
            document.getElementById('producto_id').value = id;
            document.getElementById('producto_nombre').textContent = nombre;
            document.getElementById('stock_actual').textContent = stockActual + ' unidades';
            document.getElementById('nuevo_stock').value = stockActual;
            new bootstrap.Modal(document.getElementById('stockModal')).show();
        }

        function agregarStock(id, nombre) {
            ajustarStock(id, nombre, 0);
            document.getElementById('motivo').value = 'Recepción de mercancía';
        }

        // Auto-dismiss alerts
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);

        // Highlight low stock items
        document.addEventListener('DOMContentLoaded', function() {
            const lowStockCards = document.querySelectorAll('.stock-low');
            lowStockCards.forEach(card => {
                card.closest('.inventory-card').style.borderColor = '#dc3545';
                card.closest('.inventory-card').style.backgroundColor = '#fff5f5';
            });
        });

        // Actualización automática de inventario cada 30 segundos
        setInterval(function() {
            fetch('controllers/FlorApiController.php?action=getFlores')
                .then(response => response.json())
                .then(flores => {
                    // Actualizar la grid de productos
                    const grid = document.querySelector('.row');
                    if (!grid) return;
                    let html = '';
                    if (flores.length > 0) {
                        flores.forEach(producto => {
                            let stock = producto.stock;
                            let stockClass = stock > 20 ? 'stock-high' : (stock > 5 ? 'stock-medium' : 'stock-low');
                            html += `<div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                                <div class="card inventory-card">
                                    <div class="position-relative">
                                        <img src="${producto.imagen ? producto.imagen : 'assets/images/flower-placeholder.jpg'}" class="flower-image" alt="${producto.nombre}" onerror="this.src='assets/images/flower-placeholder.jpg'">
                                        <span class="stock-badge ${stockClass}">Stock: ${stock}</span>
                                    </div>
                                    <div class="card-body">
                                        <h5 class="card-title">${producto.nombre}</h5>
                                        <p class="card-text text-muted"><small><i class="fas fa-tag"></i> ${producto.naturaleza}</small></p>
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="text-muted">Precio:</span>
                                            <strong class="text-success">$${parseFloat(producto.precio).toFixed(2)}</strong>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <span class="text-muted">Disponible:</span>
                                            <strong class="${stock > 5 ? 'text-success' : (stock > 0 ? 'text-warning' : 'text-danger')}">${stock} unidades</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>`;
                        });
                    } else {
                        html = `<div class="col-12"><div class="card"><div class="card-body text-center py-5"><i class="fas fa-search fa-3x text-muted mb-3"></i><h4>No se encontraron productos</h4><p class="text-muted">No hay productos que coincidan con los filtros aplicados</p></div></div></div>`;
                    }
                    grid.innerHTML = html;
                    // Notificación visual
                    const notif = document.createElement('div');
                    notif.className = 'alert alert-info position-fixed top-0 end-0 m-3';
                    notif.innerHTML = '<i class="fas fa-sync-alt"></i> Inventario actualizado';
                    document.body.appendChild(notif);
                    setTimeout(() => notif.remove(), 1500);
                });
        }, 30000);
    </script>
</body>
</html>
