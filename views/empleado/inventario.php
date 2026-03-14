<?php
$navbar_volver_url = 'index.php?ctrl=empleado&action=dashboard';
$navbar_volver_text = 'Volver al Dashboard';
$mensaje = isset($_SESSION['mensaje']) ? $_SESSION['mensaje'] : '';
$tipo_mensaje = isset($_SESSION['tipo_mensaje']) ? $_SESSION['tipo_mensaje'] : '';
unset($_SESSION['mensaje']);
unset($_SESSION['tipo_mensaje']);

$stats = $stats ?? [];
$inventario_perecederos = $inventario_perecederos ?? [];
$inventario_no_perecederos = $inventario_no_perecederos ?? [];
$proveedores = $proveedores ?? [];
$total_elementos_perecederos = (int)($total_elementos_perecederos ?? 0);
$total_elementos_no_perecederos = (int)($total_elementos_no_perecederos ?? 0);
$total_elementos_proveedores = (int)($total_elementos_proveedores ?? 0);
$pagina_actual_perecederos = (int)($pagina_actual_perecederos ?? 1);
$pagina_actual_no_perecederos = (int)($pagina_actual_no_perecederos ?? 1);
$pagina_actual_proveedores = (int)($pagina_actual_proveedores ?? 1);
$total_paginas_perecederos = (int)($total_paginas_perecederos ?? 1);
$total_paginas_no_perecederos = (int)($total_paginas_no_perecederos ?? 1);
$total_paginas_proveedores = (int)($total_paginas_proveedores ?? 1);
$offset_perecederos = (int)($offset_perecederos ?? 0);
$offset_no_perecederos = (int)($offset_no_perecederos ?? 0);
$offset_proveedores = (int)($offset_proveedores ?? 0);
$elementos_por_pagina = (int)($elementos_por_pagina ?? 10);
$categorias = $categorias ?? [];
$filter_buscar = isset($_GET['buscar']) ? htmlspecialchars((string)$_GET['buscar'], ENT_QUOTES, 'UTF-8') : '';
$filter_categoria = isset($_GET['categoria']) ? (string)$_GET['categoria'] : '';
$filter_estado = isset($_GET['estado_stock']) ? (string)$_GET['estado_stock'] : '';

function urlPaginaEmpleado($param, $pagina) {
    $params = array_merge($_GET, [$param => $pagina]);
    $params['ctrl'] = 'empleado';
    $params['action'] = 'inventario';
    return 'index.php?' . http_build_query($params);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventario - FloralTech</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/dashboard-empleado.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="empleado-theme">
    <div class="dashboard-container">
        <?php include __DIR__ . '/partials/navbar_empleado.php'; ?>
        <div class="main-content">
            <div class="content-wrapper inv-page">
                <?php if ($mensaje): ?>
                    <div class="alert alert-<?= $tipo_mensaje ?> alert-dismissible fade show" role="alert">
                        <i class="fas fa-<?= $tipo_mensaje === 'success' ? 'check-circle' : ($tipo_mensaje === 'warning' ? 'exclamation-triangle' : 'info-circle') ?> me-2"></i>
                        <?= htmlspecialchars($mensaje) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                <?php if (isset($_SESSION['error_seguridad'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-shield-alt me-2"></i>
                        <strong>ALERTA:</strong> <?= htmlspecialchars($_SESSION['error_seguridad']) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['error_seguridad']); ?>
                <?php endif; ?>

                <!-- Cabecera compacta: título + resumen en un solo bloque -->
                <div class="inv-top-block">
                    <div class="inv-top-row">
                        <h1 class="inv-hero-title"><i class="fas fa-boxes-stacked me-2"></i>Inventario</h1>
                        <p class="inv-hero-desc">Productos por tipo y proveedores. Mismos datos que en admin.</p>
                    </div>
                    <p class="inv-resumen-label">Resumen</p>
                    <div class="inv-resumen-cards">
                        <div class="inv-stat-card inv-stat-total">
                            <span class="inv-stat-icon"><i class="fas fa-boxes"></i></span>
                            <div class="inv-stat-body">
                                <span class="inv-stat-value"><?= number_format($stats['total_productos'] ?? 0) ?></span>
                                <span class="inv-stat-label">Total productos</span>
                            </div>
                        </div>
                        <div class="inv-stat-card inv-stat-bajo">
                            <span class="inv-stat-icon"><i class="fas fa-exclamation-triangle"></i></span>
                            <div class="inv-stat-body">
                                <span class="inv-stat-value"><?= number_format($stats['stock_bajo'] ?? 0) ?></span>
                                <span class="inv-stat-label">Stock bajo</span>
                                <span class="inv-stat-hint">10-19 uds</span>
                            </div>
                        </div>
                        <div class="inv-stat-card inv-stat-critico">
                            <span class="inv-stat-icon"><i class="fas fa-exclamation-circle"></i></span>
                            <div class="inv-stat-body">
                                <span class="inv-stat-value"><?= number_format($stats['stock_critico'] ?? 0) ?></span>
                                <span class="inv-stat-label">Stock crítico</span>
                                <span class="inv-stat-hint">1-9 uds</span>
                            </div>
                        </div>
                        <div class="inv-stat-card inv-stat-sin">
                            <span class="inv-stat-icon"><i class="fas fa-times-circle"></i></span>
                            <div class="inv-stat-body">
                                <span class="inv-stat-value"><?= number_format($stats['sin_stock'] ?? 0) ?></span>
                                <span class="inv-stat-label">Sin stock</span>
                            </div>
                        </div>
                        <div class="inv-stat-card inv-stat-valor">
                            <span class="inv-stat-icon"><i class="fas fa-dollar-sign"></i></span>
                            <div class="inv-stat-body">
                                <span class="inv-stat-value">$<?= number_format($stats['valor_total'] ?? 0, 2) ?></span>
                                <span class="inv-stat-label">Valor total</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Navegación rápida entre secciones -->
                <nav class="inv-nav-bar" aria-label="Ir a sección">
                    <a href="#perecederos" class="inv-nav-link inv-nav-perecederos"><i class="fas fa-seedling me-1"></i>Perecederos</a>
                    <a href="#no-perecederos" class="inv-nav-link inv-nav-no-perecederos"><i class="fas fa-box me-1"></i>No perecederos</a>
                    <a href="#proveedores" class="inv-nav-link inv-nav-proveedores"><i class="fas fa-truck me-1"></i>Proveedores</a>
                </nav>

                <!-- Filtros (aplican a Perecederos y No perecederos) -->
                <div class="inv-filters-card content-card">
                    <div class="card-header inv-filters-header">
                        <h3 class="inv-filters-title"><i class="fas fa-filter me-2"></i>Filtros</h3>
                    </div>
                    <div class="card-body">
                        <form method="get" action="index.php" class="inv-filters-form row g-2 align-items-end">
                            <input type="hidden" name="ctrl" value="empleado">
                            <input type="hidden" name="action" value="inventario">
                            <div class="col-12 col-md-4 col-lg-3">
                                <label for="inv-buscar" class="form-label small mb-0">Buscar</label>
                                <input type="text" class="form-control form-control-sm" id="inv-buscar" name="buscar" value="<?= $filter_buscar ?>" placeholder="Nombre o producto...">
                            </div>
                            <div class="col-12 col-md-4 col-lg-3">
                                <label for="inv-categoria" class="form-label small mb-0">Naturaleza / Categoría</label>
                                <select class="form-select form-select-sm" id="inv-categoria" name="categoria">
                                    <option value="">Todas</option>
                                    <?php foreach ($categorias as $cat): ?>
                                    <option value="<?= htmlspecialchars($cat['tipo'] ?? '', ENT_QUOTES) ?>" <?= ($filter_categoria === ($cat['tipo'] ?? '')) ? 'selected' : '' ?>><?= htmlspecialchars($cat['tipo'] ?? '') ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-12 col-md-4 col-lg-3">
                                <label for="inv-estado" class="form-label small mb-0">Estado de stock</label>
                                <select class="form-select form-select-sm" id="inv-estado" name="estado_stock">
                                    <option value="">Todos</option>
                                    <option value="normal" <?= $filter_estado === 'normal' ? 'selected' : '' ?>>Normal</option>
                                    <option value="bajo" <?= $filter_estado === 'bajo' ? 'selected' : '' ?>>Bajo</option>
                                    <option value="critico" <?= $filter_estado === 'critico' ? 'selected' : '' ?>>Crítico</option>
                                    <option value="sin_stock" <?= $filter_estado === 'sin_stock' ? 'selected' : '' ?>>Sin stock</option>
                                </select>
                            </div>
                            <div class="col-12 col-md-12 col-lg-3 d-flex gap-2 flex-wrap">
                                <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search me-1"></i>Aplicar</button>
                                <a href="index.php?ctrl=empleado&action=inventario" class="btn btn-outline-secondary btn-sm">Limpiar</a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Sección: Productos Perecederos (como admin) -->
                <section id="perecederos" class="inv-section">
                    <div class="content-card inv-card">
                        <div class="card-header inv-card-header inv-card-perecederos">
                            <h2 class="inv-card-title"><i class="fas fa-seedling me-2"></i>Productos Perecederos (Flores Naturales)</h2>
                            <span class="badge inv-card-badge"><?= $total_elementos_perecederos ?> producto<?= $total_elementos_perecederos !== 1 ? 's' : '' ?></span>
                        </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0 align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Producto</th>
                                        <th>Naturaleza</th>
                                        <th>Stock</th>
                                        <th>P. Venta</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($inventario_perecederos)): ?>
                                        <?php foreach ($inventario_perecederos as $item): ?>
                                        <tr>
                                            <td class="fw-bold"><?= htmlspecialchars($item['producto'] ?? '') ?></td>
                                            <td><span class="badge bg-success"><?= htmlspecialchars($item['naturaleza'] ?? '') ?></span></td>
                                            <td><strong class="text-<?= ($item['stock'] ?? 0) < 10 ? 'danger' : 'dark' ?>"><?= (int)($item['stock'] ?? 0) ?></strong></td>
                                            <td class="text-success">$<?= number_format((float)($item['precio'] ?? 0), 2) ?></td>
                                            <td><span class="badge <?= ($item['estado_stock'] ?? '') === 'Sin Stock' ? 'stock-sin' : (($item['estado_stock'] ?? '') === 'Critico' ? 'stock-bajo' : (($item['estado_stock'] ?? '') === 'Bajo' ? 'stock-medio' : 'stock-alto')) ?>"><?= htmlspecialchars($item['estado_stock'] ?? '') ?></span></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr><td colspan="5" class="text-center text-muted py-4"><i class="fas fa-seedling me-1"></i>No hay productos perecederos</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer inv-pagination-footer bg-light d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <small class="text-muted">Mostrando <?= $total_elementos_perecederos ? $offset_perecederos + 1 : 0 ?>-<?= min($offset_perecederos + count($inventario_perecederos), $total_elementos_perecederos) ?> de <?= $total_elementos_perecederos ?></small>
                            <nav aria-label="Paginación Perecederos">
                                <ul class="pagination pagination-sm mb-0">
                                    <li class="page-item <?= $pagina_actual_perecederos <= 1 ? 'disabled' : '' ?>"><a class="page-link" href="<?= $pagina_actual_perecederos <= 1 ? '#' : urlPaginaEmpleado('pagina_perecederos', $pagina_actual_perecederos - 1) ?>">&laquo;</a></li>
                                    <?php for ($i = max(1, $pagina_actual_perecederos - 1); $i <= min($total_paginas_perecederos, $pagina_actual_perecederos + 1); $i++): ?>
                                    <li class="page-item <?= $i === $pagina_actual_perecederos ? 'active' : '' ?>"><a class="page-link" href="<?= urlPaginaEmpleado('pagina_perecederos', $i) ?>"><?= $i ?></a></li>
                                    <?php endfor; ?>
                                    <li class="page-item <?= $pagina_actual_perecederos >= $total_paginas_perecederos ? 'disabled' : '' ?>"><a class="page-link" href="<?= $pagina_actual_perecederos >= $total_paginas_perecederos ? '#' : urlPaginaEmpleado('pagina_perecederos', $pagina_actual_perecederos + 1) ?>">&raquo;</a></li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                    </div>
                </section>

                <!-- Sección: Productos No Perecederos (como admin) -->
                <section id="no-perecederos" class="inv-section">
                    <div class="content-card inv-card">
                        <div class="card-header inv-card-header inv-card-no-perecederos">
                            <h2 class="inv-card-title"><i class="fas fa-box me-2"></i>Productos No Perecederos (Duraderos)</h2>
                            <span class="badge inv-card-badge"><?= $total_elementos_no_perecederos ?> producto<?= $total_elementos_no_perecederos !== 1 ? 's' : '' ?></span>
                        </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0 align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Producto</th>
                                        <th>Naturaleza</th>
                                        <th>Stock</th>
                                        <th>P. Venta</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($inventario_no_perecederos)): ?>
                                        <?php foreach ($inventario_no_perecederos as $item): ?>
                                        <tr>
                                            <td class="fw-bold"><?= htmlspecialchars($item['producto'] ?? '') ?></td>
                                            <td><span class="badge bg-secondary"><?= htmlspecialchars($item['naturaleza'] ?? '') ?></span></td>
                                            <td><strong class="text-<?= ($item['stock'] ?? 0) < 10 ? 'danger' : 'dark' ?>"><?= (int)($item['stock'] ?? 0) ?></strong></td>
                                            <td class="text-success">$<?= number_format((float)($item['precio'] ?? 0), 2) ?></td>
                                            <td><span class="badge <?= ($item['estado_stock'] ?? '') === 'Sin Stock' ? 'stock-sin' : (($item['estado_stock'] ?? '') === 'Critico' ? 'stock-bajo' : (($item['estado_stock'] ?? '') === 'Bajo' ? 'stock-medio' : 'stock-alto')) ?>"><?= htmlspecialchars($item['estado_stock'] ?? '') ?></span></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr><td colspan="5" class="text-center text-muted py-4"><i class="fas fa-box me-1"></i>No hay productos no perecederos</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer inv-pagination-footer bg-light d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <small class="text-muted">Mostrando <?= $total_elementos_no_perecederos ? $offset_no_perecederos + 1 : 0 ?>-<?= min($offset_no_perecederos + count($inventario_no_perecederos), $total_elementos_no_perecederos) ?> de <?= $total_elementos_no_perecederos ?></small>
                            <nav aria-label="Paginación No perecederos">
                                <ul class="pagination pagination-sm mb-0">
                                    <li class="page-item <?= $pagina_actual_no_perecederos <= 1 ? 'disabled' : '' ?>"><a class="page-link" href="<?= $pagina_actual_no_perecederos <= 1 ? '#' : urlPaginaEmpleado('pagina_no_perecederos', $pagina_actual_no_perecederos - 1) ?>">&laquo;</a></li>
                                    <?php for ($i = max(1, $pagina_actual_no_perecederos - 1); $i <= min($total_paginas_no_perecederos, $pagina_actual_no_perecederos + 1); $i++): ?>
                                    <li class="page-item <?= $i === $pagina_actual_no_perecederos ? 'active' : '' ?>"><a class="page-link" href="<?= urlPaginaEmpleado('pagina_no_perecederos', $i) ?>"><?= $i ?></a></li>
                                    <?php endfor; ?>
                                    <li class="page-item <?= $pagina_actual_no_perecederos >= $total_paginas_no_perecederos ? 'disabled' : '' ?>"><a class="page-link" href="<?= $pagina_actual_no_perecederos >= $total_paginas_no_perecederos ? '#' : urlPaginaEmpleado('pagina_no_perecederos', $pagina_actual_no_perecederos + 1) ?>">&raquo;</a></li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                    </div>
                </section>

                <!-- Sección: Proveedores (como admin) -->
                <section id="proveedores" class="inv-section">
                    <div class="content-card inv-card">
                        <div class="card-header inv-card-header inv-card-proveedores">
                            <h2 class="inv-card-title"><i class="fas fa-truck me-2"></i>Proveedores Registrados</h2>
                            <span class="badge inv-card-badge"><?= $total_elementos_proveedores ?> proveedor<?= $total_elementos_proveedores !== 1 ? 'es' : '' ?></span>
                        </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0 align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Categoría</th>
                                        <th class="d-none d-md-table-cell">Teléfono</th>
                                        <th class="d-none d-lg-table-cell">Email</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($proveedores)): ?>
                                        <?php foreach ($proveedores as $prov): ?>
                                        <tr>
                                            <td class="fw-bold"><?= htmlspecialchars($prov['nombre'] ?? '') ?></td>
                                            <td><span class="badge bg-info"><?= htmlspecialchars($prov['categoria'] ?? '') ?></span></td>
                                            <td class="d-none d-md-table-cell"><?= htmlspecialchars($prov['telefono'] ?? '') ?></td>
                                            <td class="d-none d-lg-table-cell small"><?= htmlspecialchars($prov['email'] ?? '') ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr><td colspan="4" class="text-center text-muted py-4"><i class="fas fa-truck me-1"></i>No hay proveedores registrados</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer inv-pagination-footer bg-light d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <small class="text-muted">Mostrando <?= $total_elementos_proveedores ? $offset_proveedores + 1 : 0 ?>-<?= min($offset_proveedores + count($proveedores), $total_elementos_proveedores) ?> de <?= $total_elementos_proveedores ?></small>
                            <nav aria-label="Paginación Proveedores">
                                <ul class="pagination pagination-sm mb-0">
                                    <li class="page-item <?= $pagina_actual_proveedores <= 1 ? 'disabled' : '' ?>"><a class="page-link" href="<?= $pagina_actual_proveedores <= 1 ? '#' : urlPaginaEmpleado('pagina_proveedores', $pagina_actual_proveedores - 1) ?>">&laquo;</a></li>
                                    <?php for ($i = max(1, $pagina_actual_proveedores - 1); $i <= min($total_paginas_proveedores, $pagina_actual_proveedores + 1); $i++): ?>
                                    <li class="page-item <?= $i === $pagina_actual_proveedores ? 'active' : '' ?>"><a class="page-link" href="<?= urlPaginaEmpleado('pagina_proveedores', $i) ?>"><?= $i ?></a></li>
                                    <?php endfor; ?>
                                    <li class="page-item <?= $pagina_actual_proveedores >= $total_paginas_proveedores ? 'disabled' : '' ?>"><a class="page-link" href="<?= $pagina_actual_proveedores >= $total_paginas_proveedores ? '#' : urlPaginaEmpleado('pagina_proveedores', $pagina_actual_proveedores + 1) ?>">&raquo;</a></li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                    </div>
                </section>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.querySelectorAll('.inv-nav-link').forEach(function(link) {
            link.addEventListener('click', function(e) {
                var id = this.getAttribute('href').slice(1);
                var el = document.getElementById(id);
                if (el) {
                    e.preventDefault();
                    el.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });
        setTimeout(function() {
            document.querySelectorAll('.alert').forEach(function(alert) {
                try { new bootstrap.Alert(alert).close(); } catch (_) {}
            });
        }, 5000);
    </script>
</body>
</html>
