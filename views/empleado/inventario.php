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
$parametros_inventario = $parametros_inventario ?? ['stock_minimo' => 20, 'dias_vencimiento' => 7];
$todos_proveedores = $todos_proveedores ?? [];
$colores = ['Rojo' => '#dc3545', 'Rosa' => '#ff69b4', 'Blanco' => '#f8f9fa', 'Amarillo' => '#ffc107', 'Naranja' => '#fd7e14', 'Morado' => '#6f42c1', 'Azul' => '#0d6efd', 'Verde' => '#198754', 'Multicolor' => '#6c757d'];

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
    <link rel="icon" href="favicon.php">
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
                                        <th><i class="fas fa-tag me-1"></i>Producto</th>
                                        <th><i class="fas fa-seedling me-1"></i>Naturaleza</th>
                                        <th><i class="fas fa-palette me-1"></i>Color</th>
                                        <th><i class="fas fa-boxes me-1"></i>Stock</th>
                                        <th>P. Compra</th>
                                        <th>P. Venta</th>
                                        <th><i class="fas fa-calendar-times me-1"></i>F. Caducidad</th>
                                        <th><i class="fas fa-clock me-1"></i>Días rest.</th>
                                        <th><i class="fas fa-layer-group me-1"></i>Lotes</th>
                                        <th class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($inventario_perecederos)): ?>
                                        <?php foreach ($inventario_perecederos as $item):
                                            $umbral = (int)($parametros_inventario['stock_minimo'] ?? 20);
                                            $critico = min(5, max(1, (int)($umbral / 2)));
                                            $es_critico = ($item['stock'] ?? 0) > 0 && ($item['stock'] ?? 0) < $critico;
                                            $es_bajo = ($item['stock'] ?? 0) >= $critico && ($item['stock'] ?? 0) < $umbral;
                                            $badge_stock = ($item['stock'] ?? 0) == 0 ? 'bg-danger' : ($es_critico ? 'bg-danger' : ($es_bajo ? 'bg-warning text-dark' : 'bg-success'));
                                            $dias_limite = (int)($parametros_inventario['dias_vencimiento'] ?? 7);
                                            $dias = $item['dias_hasta_caducidad'] ?? null;
                                            $color_badge = $colores[$item['color'] ?? ''] ?? '#6c757d';
                                            $text_color = in_array($item['color'] ?? '', ['Blanco', 'Amarillo']) ? '#000' : '#fff';
                                        ?>
                                        <tr>
                                            <td class="fw-bold"><?= htmlspecialchars($item['producto'] ?? '') ?></td>
                                            <td><span class="badge bg-success"><?= htmlspecialchars($item['naturaleza'] ?? '') ?></span></td>
                                            <td><span class="badge" style="background-color:<?= $color_badge ?>;color:<?= $text_color ?>"><?= htmlspecialchars($item['color'] ?? '') ?></span></td>
                                            <td><span class="badge <?= $badge_stock ?>"><i class="fas fa-box me-1"></i><?= (int)($item['stock'] ?? 0) ?></span></td>
                                            <td class="text-muted small">$<?= number_format((float)($item['precio_compra'] ?? 0), 2) ?></td>
                                            <td class="fw-bold text-primary">$<?= number_format((float)($item['precio'] ?? 0), 2) ?></td>
                                            <td>
                                                <?php if (!empty($item['lote_proxima_caducidad'])): ?>
                                                    <span class="small"><?= date('m/d/y', strtotime($item['lote_proxima_caducidad'])) ?></span>
                                                <?php else: ?>
                                                    <span class="text-muted small">Sin lotes</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($dias !== null && ($item['lote_cantidad_activa'] ?? 0) > 0): ?>
                                                    <?php if ($dias <= 3): ?>
                                                        <span class="badge bg-danger"><i class="fas fa-circle-exclamation"></i> <?= $dias ?>d</span>
                                                    <?php elseif ($dias <= 5): ?>
                                                        <span class="badge bg-warning text-dark"><i class="fas fa-exclamation-triangle"></i> <?= $dias ?>d</span>
                                                    <?php elseif ($dias <= $dias_limite): ?>
                                                        <span class="badge bg-info"><i class="fas fa-info-circle"></i> <?= $dias ?>d</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-secondary"><?= $dias ?>d</span>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-success btn-modal-ver-lotes-emp" title="Ver historial de lotes" data-producto-id="<?= (int)($item['idinv'] ?? 0) ?>" data-producto-nombre="<?= htmlspecialchars($item['producto'] ?? '', ENT_QUOTES) ?>"><i class="fas fa-eye"></i></button>
                                                <button type="button" class="btn btn-sm btn-primary btn-modal-agregar-lote-emp" title="Agregar nuevo lote" data-producto-id="<?= (int)($item['idinv'] ?? 0) ?>" data-producto-nombre="<?= htmlspecialchars($item['producto'] ?? '', ENT_QUOTES) ?>"><i class="fas fa-plus"></i></button>
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-sm btn-warning btn-editar-producto-emp" data-idinv="<?= (int)($item['idinv'] ?? 0) ?>" title="Editar producto"><i class="fas fa-edit"></i></button>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr><td colspan="10" class="text-center text-muted py-4"><i class="fas fa-seedling me-1"></i>No hay productos perecederos</td></tr>
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
                                        <th><i class="fas fa-tag me-1"></i>Producto</th>
                                        <th><i class="fas fa-seedling me-1"></i>Naturaleza</th>
                                        <th><i class="fas fa-palette me-1"></i>Color</th>
                                        <th><i class="fas fa-boxes me-1"></i>Stock</th>
                                        <th>P. Compra</th>
                                        <th>P. Venta</th>
                                        <th class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($inventario_no_perecederos)): ?>
                                        <?php foreach ($inventario_no_perecederos as $item):
                                            $stock_np = (int)($item['stock'] ?? 0);
                                            $badge_stock_np = $stock_np == 0 ? 'bg-danger' : ($stock_np < 20 ? 'bg-warning text-dark' : 'bg-success');
                                            $color_badge_np = $colores[$item['color'] ?? ''] ?? '#6c757d';
                                            $text_color_np = in_array($item['color'] ?? '', ['Blanco', 'Amarillo']) ? '#000' : '#fff';
                                        ?>
                                        <tr>
                                            <td class="fw-bold"><?= htmlspecialchars($item['producto'] ?? '') ?></td>
                                            <td><span class="badge bg-secondary"><?= htmlspecialchars($item['naturaleza'] ?? '') ?></span></td>
                                            <td><span class="badge" style="background-color:<?= $color_badge_np ?>;color:<?= $text_color_np ?>"><?= htmlspecialchars($item['color'] ?? '') ?></span></td>
                                            <td><span class="badge <?= $badge_stock_np ?>"><i class="fas fa-box me-1"></i><?= $stock_np ?></span></td>
                                            <td class="text-muted small">$<?= number_format((float)($item['precio_compra'] ?? 0), 2) ?></td>
                                            <td class="fw-bold text-primary">$<?= number_format((float)($item['precio'] ?? 0), 2) ?></td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-sm btn-warning btn-editar-producto-emp" data-idinv="<?= (int)($item['idinv'] ?? 0) ?>" title="Editar producto"><i class="fas fa-edit"></i></button>
                                                <button type="button" class="btn btn-sm btn-info btn-modal-stock-emp" title="Agregar stock" data-producto-id="<?= (int)($item['idinv'] ?? 0) ?>" data-producto-nombre="<?= htmlspecialchars($item['producto'] ?? '', ENT_QUOTES) ?>" data-producto-stock="<?= $stock_np ?>"><i class="fas fa-plus"></i></button>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr><td colspan="7" class="text-center text-muted py-4"><i class="fas fa-box me-1"></i>No hay productos no perecederos</td></tr>
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

    <!-- Modal Editar Producto (solo edición; sin eliminar ni crear) -->
    <div class="modal fade" id="modal-editar-producto-emp" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Editar Producto del Inventario</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="form-editar-producto-emp">
                        <input type="hidden" name="producto_id" id="emp_editar_producto_id">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label"><i class="fas fa-tag me-1"></i>Nombre del Producto *</label>
                                <input type="text" class="form-control" name="nombre_producto" id="emp_editar_nombre_producto" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label"><i class="fas fa-layer-group me-1"></i>Tipo de Producto *</label>
                                <select class="form-select" name="tipo_producto" id="emp_editar_tipo_producto" required>
                                    <option value="">Selecciona el tipo...</option>
                                    <option value="flor">🌸 Flor Natural/Artificial</option>
                                    <option value="chocolate">🍫 Chocolate</option>
                                    <option value="tarjeta">💌 Tarjeta</option>
                                    <option value="peluche">🧸 Peluche</option>
                                    <option value="globo">🎈 Globo</option>
                                    <option value="accesorio">✨ Accesorio</option>
                                    <option value="otro">📦 Otro</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label"><i class="fas fa-boxes me-1"></i>Stock Actual</label>
                                <input type="number" class="form-control" name="stock" id="emp_editar_stock" min="0" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label"><i class="fas fa-shopping-cart me-1"></i>Precio Compra</label>
                                <input type="number" class="form-control" name="precio_compra" id="emp_editar_precio_compra" min="0" step="0.01" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label"><i class="fas fa-dollar-sign me-1"></i>Precio Venta</label>
                                <input type="number" class="form-control" name="precio" id="emp_editar_precio" min="0" step="0.01" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label"><i class="fas fa-palette me-1"></i>Color</label>
                                <input type="text" class="form-control" name="color" id="emp_editar_color" placeholder="Ej: Rojo, Azul">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label"><i class="fas fa-seedling me-1"></i>Naturaleza</label>
                                <select class="form-select" name="naturaleza" id="emp_editar_naturaleza">
                                    <option value="">Seleccionar...</option>
                                    <option value="Natural">Natural</option>
                                    <option value="Artificial">Artificial</option>
                                    <option value="Mixto">Mixto</option>
                                    <option value="Comestible">Comestible</option>
                                    <option value="Decorativo">Decorativo</option>
                                    <option value="Regalo">Regalo</option>
                                    <option value="Accesorio">Accesorio</option>
                                    <option value="No aplica">No aplica</option>
                                    <option value="Sin clasificar">Sin clasificar</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label"><i class="fas fa-info-circle me-1"></i>Estado</label>
                                <select class="form-select" name="estado" id="emp_editar_estado">
                                    <option value="activo">Activo</option>
                                    <option value="desactivado">Desactivado</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fas fa-times me-1"></i>Cancelar</button>
                    <button type="button" class="btn btn-warning" id="emp-btn-guardar-editar"><i class="fas fa-save me-1"></i>Guardar Cambios</button>
                </div>
</div>
    </div>
</div>

    <!-- Modal Ver Lotes (empleado) -->
    <div class="modal fade" id="modal-ver-lotes-emp" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title"><i class="fas fa-boxes me-2"></i>Gestión de Lotes - <span id="ver_lotes_emp_producto_nombre"></span></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="ver_lotes_emp_producto_id">
                    <div class="row mb-3">
                        <div class="col-md-4"><div class="card bg-light"><div class="card-body text-center"><h6>Total Lotes</h6><h3 id="resumen_emp_total_lotes">0</h3></div></div></div>
                        <div class="col-md-4"><div class="card bg-light"><div class="card-body text-center"><h6>Cantidad Activa</h6><h3 id="resumen_emp_cantidad_activa">0</h3></div></div></div>
                        <div class="col-md-4"><div class="card bg-light"><div class="card-body text-center"><h6>Próxima Caducidad</h6><h3 id="resumen_emp_proxima_caducidad">-</h3></div></div></div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr><th>Nº Lote</th><th>Cantidad</th><th>F. Ingreso</th><th>F. Caducidad</th><th>Proveedor</th><th>Precio Compra</th><th>Estado</th></tr>
                            </thead>
                            <tbody id="tabla-lotes-emp-body"></tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button></div>
            </div>
        </div>
    </div>

    <!-- Modal Agregar Lote (empleado) -->
    <div class="modal fade" id="modal-agregar-lote-emp" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-plus-circle me-2"></i>Agregar Nuevo Lote - <span id="agregar_lote_emp_producto_nombre"></span></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="form-agregar-lote-emp">
                        <input type="hidden" name="inv_idinv" id="agregar_lote_emp_producto_id">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Número de Lote <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="numero_lote" id="agregar_lote_emp_numero" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Cantidad <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="cantidad" min="1" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Fecha de Ingreso <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="fecha_ingreso" id="agregar_lote_emp_fecha_ingreso" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Fecha de Caducidad <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="fecha_caducidad" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Proveedor</label>
                                <select class="form-select" name="proveedor" id="agregar_lote_emp_proveedor">
                                    <option value="">Seleccionar proveedor...</option>
                                    <?php foreach ($todos_proveedores as $prov): ?>
                                        <option value="<?= htmlspecialchars($prov['nombre'] ?? '', ENT_QUOTES) ?>"><?= htmlspecialchars($prov['nombre'] ?? '') ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Precio de Compra</label>
                                <input type="number" class="form-control" name="precio_compra" step="0.01" min="0">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Observaciones</label>
                            <textarea class="form-control" name="observaciones" rows="2"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="emp-btn-guardar-lote"><i class="fas fa-save me-1"></i>Guardar Lote</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Agregar Stock (empleado, no perecederos) -->
    <div class="modal fade" id="modal-agregar-stock-emp" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title"><i class="fas fa-plus me-2"></i>Agregar Stock</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="stock_emp_producto_id">
                    <p class="mb-2"><strong>Producto:</strong> <span id="stock_emp_producto_nombre" class="text-info"></span></p>
                    <p class="mb-3 text-muted">Stock actual: <span id="stock_emp_actual" class="badge bg-secondary">0</span></p>
                    <label class="form-label">Cantidad a agregar <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" id="stock_emp_cantidad" min="1" value="1">
                    <label class="form-label mt-2">Motivo (opcional)</label>
                    <input type="text" class="form-control" id="stock_emp_motivo" placeholder="Ej: Reposición">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-info" id="emp-btn-agregar-stock"><i class="fas fa-plus me-1"></i>Agregar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        (function() {
            var baseUrl = 'index.php?ctrl=empleado&action=';
            var baseIndex = 'index.php?';
            function mapAlimentacionToTipo(a) {
                if (!a) return 'otro';
                var s = String(a).toLowerCase();
                if (s.indexOf('agua') !== -1 || s.indexOf('nutrientes') !== -1) return 'flor';
                if (s.indexOf('fresco') !== -1 || s.indexOf('seco') !== -1) return 'chocolate';
                if (s === 'no requiere' || s === 'n/a') return 'otro';
                return 'otro';
            }
            document.querySelectorAll('.btn-editar-producto-emp').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    var id = this.getAttribute('data-idinv');
                    if (!id) return;
                    fetch(baseUrl + 'obtener_producto_inventario&id=' + encodeURIComponent(id))
                        .then(function(r) { return r.json(); })
                        .then(function(res) {
                            if (!res.success || !res.producto) {
                                alert(res.message || 'No se pudo cargar el producto');
                                return;
                            }
                            var p = res.producto;
                            document.getElementById('emp_editar_producto_id').value = p.idinv;
                            document.getElementById('emp_editar_nombre_producto').value = p.producto || '';
                            document.getElementById('emp_editar_stock').value = p.stock || 0;
                            document.getElementById('emp_editar_precio_compra').value = p.precio_compra || 0;
                            document.getElementById('emp_editar_precio').value = p.precio || 0;
                            document.getElementById('emp_editar_color').value = p.color || '';
                            document.getElementById('emp_editar_naturaleza').value = p.naturaleza || '';
                            document.getElementById('emp_editar_estado').value = p.estado || 'activo';
                            var alimentacion = (p.alimentacion || '').trim();
                            document.getElementById('emp_editar_tipo_producto').value = mapAlimentacionToTipo(alimentacion);
                            var modalEl = document.getElementById('modal-editar-producto-emp');
                            if (modalEl && typeof bootstrap !== 'undefined') {
                                new bootstrap.Modal(modalEl).show();
                            }
                        })
                        .catch(function() { alert('Error al cargar el producto'); });
                });
            });
            document.getElementById('emp-btn-guardar-editar').addEventListener('click', function() {
                var form = document.getElementById('form-editar-producto-emp');
                if (!form.checkValidity()) { form.reportValidity(); return; }
                var fd = new FormData(form);
                var btn = this;
                btn.disabled = true;
                fetch(baseUrl + 'editar_producto_inventario', { method: 'POST', body: fd })
                    .then(function(r) { return r.json(); })
                    .then(function(res) {
                        btn.disabled = false;
                        if (res.success) {
                            var modalEl = document.getElementById('modal-editar-producto-emp');
                            if (modalEl && typeof bootstrap !== 'undefined') {
                                bootstrap.Modal.getInstance(modalEl).hide();
                            }
                            alert(res.message || 'Producto actualizado.');
                            window.location.reload();
                        } else {
                            alert(res.message || 'Error al actualizar');
                        }
                    })
                    .catch(function() {
                        btn.disabled = false;
                        alert('Error de conexión');
                    });
            });

            // --- Ver Lotes (empleado) ---
            function formatearFechaEmp(s) {
                if (!s) return '-';
                var d = new Date(s);
                return isNaN(d.getTime()) ? s : (d.getMonth()+1)+'/'+d.getDate()+'/'+String(d.getFullYear()).slice(2);
            }
            function mostrarLotesEmp(lotes, resumen) {
                var tbody = document.getElementById('tabla-lotes-emp-body');
                tbody.innerHTML = '';
                if (!lotes || lotes.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted">No hay lotes registrados</td></tr>';
                } else {
                    var badge = { 'activo': 'bg-success', 'vendido': 'bg-info', 'caducado': 'bg-danger', 'devuelto': 'bg-warning text-dark' };
                    lotes.forEach(function(lote) {
                        var tr = '<tr><td>' + (lote.numero_lote || '') + '</td><td>' + (lote.cantidad || 0) + '</td><td>' + formatearFechaEmp(lote.fecha_ingreso) + '</td><td>' + formatearFechaEmp(lote.fecha_caducidad) + '</td><td>' + (lote.proveedor || '-') + '</td><td>$' + parseFloat(lote.precio_compra || 0).toFixed(2) + '</td><td><span class="badge ' + (badge[lote.estado] || 'bg-secondary') + '">' + (lote.estado || '') + '</span></td></tr>';
                        tbody.innerHTML += tr;
                    });
                }
                if (resumen) {
                    document.getElementById('resumen_emp_total_lotes').textContent = resumen.total_lotes || 0;
                    document.getElementById('resumen_emp_cantidad_activa').textContent = resumen.cantidad_activa || 0;
                    document.getElementById('resumen_emp_proxima_caducidad').textContent = resumen.proxima_caducidad ? formatearFechaEmp(resumen.proxima_caducidad) : 'N/A';
                }
            }
            document.querySelectorAll('.btn-modal-ver-lotes-emp').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    var id = this.getAttribute('data-producto-id');
                    var nombre = this.getAttribute('data-producto-nombre') || '';
                    document.getElementById('ver_lotes_emp_producto_id').value = id;
                    document.getElementById('ver_lotes_emp_producto_nombre').textContent = nombre;
                    fetch(baseIndex + 'ctrl=Clotes&action=obtenerLotes&inv_idinv=' + encodeURIComponent(id))
                        .then(function(r) { return r.json(); })
                        .then(function(data) {
                            if (data.success) {
                                mostrarLotesEmp(data.lotes, data.resumen);
                                new bootstrap.Modal(document.getElementById('modal-ver-lotes-emp')).show();
                            } else {
                                alert('Error: ' + (data.message || 'No se pudieron cargar los lotes'));
                            }
                        })
                        .catch(function() { alert('Error al cargar lotes'); });
                });
            });

            // --- Agregar Lote (empleado) ---
            document.querySelectorAll('.btn-modal-agregar-lote-emp').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    var id = this.getAttribute('data-producto-id');
                    var nombre = this.getAttribute('data-producto-nombre') || '';
                    document.getElementById('agregar_lote_emp_producto_id').value = id;
                    document.getElementById('agregar_lote_emp_producto_nombre').textContent = nombre;
                    document.getElementById('form-agregar-lote-emp').reset();
                    document.getElementById('agregar_lote_emp_producto_id').value = id;
                    var hoy = new Date().toISOString().split('T')[0];
                    document.getElementById('agregar_lote_emp_fecha_ingreso').value = hoy;
                    fetch(baseIndex + 'ctrl=Clotes&action=generarNumeroLote&inv_idinv=' + encodeURIComponent(id))
                        .then(function(r) { return r.json(); })
                        .then(function(data) {
                            document.getElementById('agregar_lote_emp_numero').value = data.success && data.numero_lote ? data.numero_lote : ('LOTE-' + Date.now());
                        })
                        .catch(function() {
                            document.getElementById('agregar_lote_emp_numero').value = 'LOTE-' + Date.now();
                        });
                    new bootstrap.Modal(document.getElementById('modal-agregar-lote-emp')).show();
                });
            });
            document.getElementById('emp-btn-guardar-lote').addEventListener('click', function() {
                var form = document.getElementById('form-agregar-lote-emp');
                if (!form.checkValidity()) { form.reportValidity(); return; }
                var fd = new FormData(form);
                var btn = this;
                btn.disabled = true;
                fetch(baseIndex + 'ctrl=Clotes&action=crearLote', { method: 'POST', body: fd })
                    .then(function(r) { return r.json(); })
                    .then(function(data) {
                        btn.disabled = false;
                        if (data.success) {
                            bootstrap.Modal.getInstance(document.getElementById('modal-agregar-lote-emp')).hide();
                            alert(data.message || 'Lote agregado.');
                            window.location.reload();
                        } else {
                            alert(data.message || 'Error al guardar');
                        }
                    })
                    .catch(function() { btn.disabled = false; alert('Error de conexión'); });
            });

            // --- Agregar Stock (empleado, no perecederos) ---
            document.querySelectorAll('.btn-modal-stock-emp').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    document.getElementById('stock_emp_producto_id').value = this.getAttribute('data-producto-id');
                    document.getElementById('stock_emp_producto_nombre').textContent = this.getAttribute('data-producto-nombre') || '';
                    document.getElementById('stock_emp_actual').textContent = this.getAttribute('data-producto-stock') || '0';
                    document.getElementById('stock_emp_cantidad').value = 1;
                    document.getElementById('stock_emp_motivo').value = '';
                    new bootstrap.Modal(document.getElementById('modal-agregar-stock-emp')).show();
                });
            });
            document.getElementById('emp-btn-agregar-stock').addEventListener('click', function() {
                var id = document.getElementById('stock_emp_producto_id').value;
                var cantidad = parseInt(document.getElementById('stock_emp_cantidad').value, 10) || 0;
                var motivo = document.getElementById('stock_emp_motivo').value;
                if (!id || cantidad < 1) { alert('Ingresa una cantidad válida'); return; }
                var fd = new FormData();
                fd.append('id', id);
                fd.append('cantidad', cantidad);
                fd.append('motivo', motivo);
                var btn = this;
                btn.disabled = true;
                fetch(baseUrl + 'agregar_stock', { method: 'POST', body: fd })
                    .then(function(r) { return r.json(); })
                    .then(function(data) {
                        btn.disabled = false;
                        if (data.success) {
                            bootstrap.Modal.getInstance(document.getElementById('modal-agregar-stock-emp')).hide();
                            alert(data.message || 'Stock agregado.');
                            window.location.reload();
                        } else {
                            alert(data.message || 'Error');
                        }
                    })
                    .catch(function() { btn.disabled = false; alert('Error de conexión'); });
            });
        })();
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
