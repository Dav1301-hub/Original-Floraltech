<?php
// Error Messages
if (isset($_GET['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>
        <strong>Error:</strong> <?= htmlspecialchars(urldecode($_GET['error'])) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>
        <strong>Éxito:</strong> Operación completada correctamente
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<h2 class="mb-4 fw-bold">Gestión de Inventario</h2>

<!-- Summary Cards -->
<div class="row mb-4 g-3">
    <div class="col-lg-2 col-md-4 col-sm-6">
        <div class="card text-center border-0 shadow-sm h-100">
            <div class="card-body py-3 px-2">
                <i class="fas fa-boxes fa-lg text-info mb-2"></i>
                <div class="fw-bold text-muted small">Total Productos</div>
                <div class="fs-4 fw-bold text-dark"><?= $total_productos ?? 0 ?></div>
            </div>
        </div>
    </div>
    <div class="col-lg-2 col-md-4 col-sm-6">
        <div class="card text-center border-0 shadow-sm h-100">
            <div class="card-body py-3 px-2">
                <i class="fas fa-exclamation-triangle fa-lg text-warning mb-2"></i>
                <div class="fw-bold text-muted small">Stock Bajo</div>
                <div class="fs-4 fw-bold text-dark"><?= $stock_bajo ?? 0 ?></div>
                <small class="text-muted" style="font-size: 0.75rem;">10-19 unidades</small>
            </div>
        </div>
    </div>
    <div class="col-lg-2 col-md-4 col-sm-6">
        <div class="card text-center border-0 shadow-sm h-100">
            <div class="card-body py-3 px-2">
                <i class="fas fa-exclamation-circle fa-lg mb-2" style="color: #ff6b35;"></i>
                <div class="fw-bold text-muted small">Stock Crítico</div>
                <div class="fs-4 fw-bold text-dark"><?= $stock_critico ?? 0 ?></div>
                <small class="text-muted" style="font-size: 0.75rem;">1-9 unidades</small>
            </div>
        </div>
    </div>
    <div class="col-lg-2 col-md-4 col-sm-6">
        <div class="card text-center border-0 shadow-sm h-100">
            <div class="card-body py-3 px-2">
                <i class="fas fa-times-circle fa-lg text-danger mb-2"></i>
                <div class="fw-bold text-muted small">Sin Stock</div>
                <div class="fs-4 fw-bold text-dark"><?= $sin_stock ?? 0 ?></div>
                <small class="text-muted" style="font-size: 0.75rem;">0 unidades</small>
            </div>
        </div>
    </div>
    <div class="col-lg-2 col-md-4 col-sm-6">
        <div class="card text-center border-0 shadow-sm h-100">
            <div class="card-body py-3 px-2">
                <i class="fas fa-gift fa-lg text-primary mb-2"></i>
                <div class="fw-bold text-muted small">Próximos a Caducar</div>
                <div class="fs-4 fw-bold text-dark">1</div>
                <small class="text-muted" style="font-size: 0.75rem;">En 7 días</small>
            </div>
        </div>
    </div>
    <div class="col-lg-2 col-md-4 col-sm-6">
        <div class="card text-center border-0 shadow-sm h-100">
            <div class="card-body py-3 px-2">
                <i class="fas fa-dollar-sign fa-lg text-success mb-2"></i>
                <div class="fw-bold text-muted small">Valor Total</div>
                <div class="fs-4 fw-bold text-dark">$<?= number_format($valor_total ?? 0, 2) ?></div>
                <small class="text-muted" style="font-size: 0.75rem;">inventario</small>
            </div>
        </div>
    </div>
</div>

<!-- Action Buttons -->
<div class="d-flex justify-content-center flex-wrap gap-2 mb-4">
    <button class="btn btn-success shadow-sm" onclick="alert('Nuevo Producto')">
        <i class="fas fa-plus me-2"></i>Nuevo Producto
    </button>
    <button class="btn btn-info shadow-sm" onclick="alert('Proveedores')">
        <i class="fas fa-truck me-2"></i>Proveedores
    </button>
    <button class="btn btn-primary shadow-sm" onclick="alert('Sincronizar')">
        <i class="fas fa-sync-alt me-2"></i>Sincronizar Stocks
    </button>
    <button class="btn btn-warning shadow-sm" onclick="alert('Configuración')">
        <i class="fas fa-cog me-2"></i>Configuración
    </button>
</div>

<!-- Perecederos Section -->
<div class="card mb-4">
    <div class="card-header text-white" style="background-color: #e67e22;">
        <h5 class="mb-0">
            <i class="fas fa-seedling me-2"></i>Productos Perecederos (Flores Naturales)
            <span class="badge bg-dark float-end"><?= $total_elementos_perecederos ?? 0 ?> productos</span>
        </h5>
    </div>
    <div class="card-body p-3">
        <div class="row g-2 mb-3">
            <div class="col-md-8">
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control" placeholder="Buscar por nombre...">
                </div>
            </div>
            <div class="col-md-4">
                <select class="form-select">
                    <option>📊 Todos los estados</option>
                    <option value="bajo">Stock Bajo</option>
                    <option value="sin_stock">Sin Stock</option>
                </select>
            </div>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Producto</th>
                    <th>Naturaleza</th>
                    <th>Stock</th>
                    <th>P. Compra</th>
                    <th>P. Venta</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($inventario_perecederos)): ?>
                    <?php foreach ($inventario_perecederos as $item): ?>
                        <tr>
                            <td><div class="fw-bold"><?= htmlspecialchars($item['producto']) ?></div></td>
                            <td><span class="badge bg-success"><?= htmlspecialchars($item['naturaleza']) ?></span></td>
                            <td><span class="badge <?= $item['stock'] > 0 ? 'bg-success' : 'bg-danger' ?>"><?= $item['stock'] ?></span></td>
                            <td>$<?= number_format($item['precio_compra'] ?? 0, 2) ?></td>
                            <td class="fw-bold">$<?= number_format($item['precio'] ?? 0, 2) ?></td>
                            <td>
                                <button class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></button>
                                <button class="btn btn-sm btn-info"><i class="fas fa-plus"></i></button>
                                <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="6" class="text-center text-muted py-4">No hay productos perecederos</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- No Perecederos Section -->
<div class="card mb-4">
    <div class="card-header bg-success text-white">
        <h5 class="mb-0">
            <i class="fas fa-box me-2"></i>Productos No Perecederos (Duraderos)
            <span class="badge bg-dark float-end"><?= $total_elementos_no_perecederos ?? 0 ?> productos</span>
        </h5>
    </div>
    <div class="card-body p-3">
        <div class="row g-2 mb-3">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control" placeholder="Buscar por nombre...">
                </div>
            </div>
            <div class="col-md-3">
                <select class="form-select">
                    <option>📂 Todas las categorías</option>
                </select>
            </div>
            <div class="col-md-3">
                <select class="form-select">
                    <option>📊 Todos los estados</option>
                </select>
            </div>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Producto</th>
                    <th>Naturaleza</th>
                    <th>Stock</th>
                    <th>P. Compra</th>
                    <th>P. Venta</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($inventario_no_perecederos)): ?>
                    <?php foreach ($inventario_no_perecederos as $item): ?>
                        <tr>
                            <td><div class="fw-bold"><?= htmlspecialchars($item['producto']) ?></div></td>
                            <td><span class="badge bg-secondary"><?= htmlspecialchars($item['naturaleza']) ?></span></td>
                            <td><span class="badge <?= $item['stock'] > 0 ? 'bg-success' : 'bg-danger' ?>"><?= $item['stock'] ?></span></td>
                            <td>$<?= number_format($item['precio_compra'] ?? 0, 2) ?></td>
                            <td class="fw-bold">$<?= number_format($item['precio'] ?? 0, 2) ?></td>
                            <td>
                                <button class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></button>
                                <button class="btn btn-sm btn-info"><i class="fas fa-plus"></i></button>
                                <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="6" class="text-center text-muted py-4">No hay productos no perecederos</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
