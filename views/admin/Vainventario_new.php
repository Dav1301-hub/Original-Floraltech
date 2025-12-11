<!-- FontAwesome 6.5.2 CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SymVoUVlj7fh8iVC4yNLsn5WiJFtUEkeuapwojV3iFxnW2VjJ5eV+ES8E8Eul0otT5IiAlfo15COLORPRNT586fQw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

<style>
    /* Responsive para Inventario Admin */
    body {
        overflow-x: hidden !important;
        width: 100vw;
        max-width: 100vw;
        margin: 0;
        padding: 0;
    }
    
    .container-fluid {
        padding: 0 !important;
        margin: 0 !important;
        width: 100% !important;
        max-width: 100% !important;
    }
    
    main.container-fluid {
        padding: 2rem 2.5rem !important;
    }
    
    .card {
        margin-bottom: 1.5rem;
    }
    
    .table-responsive {
        margin-bottom: 1rem;
    }
    
    .row.mb-4 .card {
        transition: transform 0.2s;
    }
    
    .row.mb-4 .card:hover {
        transform: translateY(-2px);
    }
    
    @media (max-width: 1200px) {
        .row.mb-4 .col-lg-2 {
            flex: 0 0 33.333333%;
            max-width: 33.333333%;
        }
    }
    
    @media (max-width: 992px) {
        .row.mb-4 .col-lg-2 {
            flex: 0 0 50%;
            max-width: 50%;
        }
    }
    
    @media (max-width: 768px) {
        main.container-fluid {
            padding: 1.5rem 1rem !important;
        }
        
        .card-body .row .col-md-3 {
            flex: 0 0 100%;
            max-width: 100%;
            margin-bottom: 0.5rem;
        }
        
        .d-flex.justify-content-center {
            flex-direction: column;
            align-items: stretch;
        }
        
        .d-flex.justify-content-center .btn {
            margin-bottom: 0.5rem;
        }
        
        .table {
            font-size: 0.85rem;
        }
        
        .table th,
        .table td {
            padding: 0.5rem 0.25rem;
        }
        
        .btn-group-sm .btn {
            padding: 0.25rem 0.4rem;
            font-size: 0.75rem;
        }
    }
    
    @media (max-width: 576px) {
        main.container-fluid {
            padding: 1rem 0.75rem !important;
        }
        
        .row.mb-4 .col-lg-2 {
            flex: 0 0 100%;
            max-width: 100%;
        }
        
        .row.mb-4 .fs-4 {
            font-size: 1.5rem !important;
        }
        
        .row.mb-4 .small {
            font-size: 0.8rem !important;
        }
        
        h2.mb-4 {
            font-size: 1.5rem;
        }
        
        .card-body {
            padding: 0.75rem;
        }
        
        .pagination {
            font-size: 0.85rem;
        }
        
        .pagination .page-link {
            padding: 0.25rem 0.5rem;
        }
        
        .modal-dialog {
            margin: 0;
            max-width: 100%;
            height: 100vh;
        }
        
        .modal-content {
            height: 100vh;
            border-radius: 0;
        }
        
        .modal-body .row .col-md-6,
        .modal-body .row .col-md-4 {
            flex: 0 0 100%;
            max-width: 100%;
        }
    }
    
    @media (max-width: 380px) {
        main.container-fluid {
            padding: 0.75rem 0.5rem !important;
        }
        
        h2.mb-4 {
            font-size: 1.25rem;
        }
        
        .btn {
            font-size: 0.85rem;
            padding: 0.375rem 0.5rem;
        }
        
        .fs-3 {
            font-size: 1.5rem !important;
        }
    }
    
    .sortable {
        user-select: none;
        transition: background-color 0.2s ease;
    }
    
    .sortable:hover {
        background-color: rgba(0, 0, 0, 0.05);
    }
    
    .sortable i {
        font-size: 0.8em;
        margin-left: 4px;
        transition: all 0.2s ease;
    }
    
    .sortable:hover i {
        opacity: 1;
    }
</style>

<!-- MAIN CONTENT -->
<main class="container-fluid" style="padding: 2rem 2.5rem; margin: 0; width: 100%; max-width: 100%; box-sizing: border-box;">
    <!-- Error Messages -->
    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            <strong>Error:</strong> <?= htmlspecialchars(urldecode($_GET['error'])) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <!-- Success Messages -->
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
                    <div class="fs-4 fw-bold text-dark"><?= $proximos_caducar ?? 1 ?></div>
                    <small class="text-muted" style="font-size: 0.75rem;">En 7 días</small>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="card text-center border-0 shadow-sm h-100" style="cursor: pointer;">
                <div class="card-body py-3 px-2">
                    <i class="fas fa-dollar-sign fa-lg text-success mb-2"></i>
                    <div class="fw-bold text-muted small">Valor Total</div>
                    <div class="fs-4 fw-bold text-dark">$<?= number_format($valor_total ?? 7730.33, 2) ?></div>
                    <small class="text-muted" style="font-size: 0.75rem;">inventario</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="d-flex justify-content-center flex-wrap gap-2 mb-4">
        <button class="btn btn-success shadow-sm" onclick="InventarioApp.abrirNuevoProducto()">
            <i class="fas fa-plus me-2"></i>Nuevo Producto
        </button>
        <button class="btn btn-info shadow-sm" onclick="InventarioApp.abrirProveedores()">
            <i class="fas fa-truck me-2"></i>Proveedores
        </button>
        <button class="btn btn-primary shadow-sm" onclick="InventarioApp.sincronizarStocks()">
            <i class="fas fa-sync-alt me-2"></i>Sincronizar Stocks
        </button>
        <button class="btn btn-warning shadow-sm" onclick="InventarioApp.abrirConfiguracion()">
            <i class="fas fa-cog me-2"></i>Configuración
        </button>
    </div>

    <!-- List Controls -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="d-flex align-items-center gap-2">
            <label for="itemsPerPage" class="form-label mb-0">Mostrar:</label>
            <select id="itemsPerPage" class="form-select form-select-sm" style="width: auto;" onchange="InventarioApp.cambiarLimite()">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
            <span class="text-muted">productos por página</span>
        </div>
        
        <div class="d-flex gap-2">
            <button class="btn btn-outline-secondary btn-sm" onclick="location.reload()">
                <i class="fas fa-sync-alt"></i> Actualizar
            </button>
            <button class="btn btn-outline-info btn-sm" id="btn-exportar">
                <i class="fas fa-file-excel"></i> Excel
            </button>
            <button class="btn btn-outline-danger btn-sm" id="btn-exportar-pdf">
                <i class="fas fa-file-pdf"></i> PDF
            </button>
        </div>
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
                        <input type="text" class="form-control" id="buscar-perecederos" placeholder="Buscar por nombre, proveedor, lote..." autocomplete="off">
                        <button class="btn btn-outline-secondary" type="button" id="limpiar-busqueda-perecederos" style="display: none;"><i class="fas fa-times"></i></button>
                        <span class="input-group-text bg-light" id="loading-perecederos" style="display: none;"><i class="fas fa-spinner fa-spin text-primary"></i></span>
                    </div>
                </div>
                <div class="col-md-4">
                    <select class="form-select" id="filtro-stock-perecederos">
                        <option value="">📊 Todos los estados</option>
                        <option value="bajo">Stock Bajo</option>
                        <option value="sin_stock">Sin Stock</option>
                        <option value="normal">Stock Normal</option>
                    </select>
                </div>
            </div>
            <small class="text-muted"><i class="fas fa-info-circle me-1"></i>La búsqueda se aplica automáticamente</small>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="tabla-perecederos">
                <thead class="table-light">
                    <tr>
                        <th class="sortable" data-sort="nombre"><i class="fas fa-sort text-muted"></i> Producto</th>
                        <th>Naturaleza</th>
                        <th>Color</th>
                        <th class="sortable" data-sort="stock"><i class="fas fa-sort text-muted"></i> Stock</th>
                        <th>P. Compra</th>
                        <th class="sortable" data-sort="precio"><i class="fas fa-sort text-muted"></i> P. Venta</th>
                        <th>Margen %</th>
                        <th class="sortable" data-sort="valor_total"><i class="fas fa-sort text-muted"></i> Ingresos Pot.</th>
                        <th>Fº Ingreso</th>
                        <th class="sortable" data-sort="fecha_caducidad"><i class="fas fa-sort text-muted"></i> Fº Caducidad</th>
                        <th>Obs. Días Rest.</th>
                        <th>Prioridad</th>
                        <th>Lotes</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($inventario_perecederos)): ?>
                        <?php foreach ($inventario_perecederos as $item): ?>
                            <tr>
                                <td><div class="fw-bold"><?= htmlspecialchars($item['producto']) ?></div></td>
                                <td><span class="badge bg-success"><?= htmlspecialchars($item['naturaleza']) ?></span></td>
                                <td><span class="badge" style="background-color: <?= InventarioApp.getColorHex(htmlspecialchars($item['color'])) ?>; color: <?= in_array($item['color'], ['Blanco', 'Amarillo']) ? '#000' : '#fff' ?>;"><?= htmlspecialchars($item['color']) ?></span></td>
                                <td><span class="badge <?= $item['stock'] == 0 ? 'bg-danger' : ($item['stock'] < 20 ? 'bg-warning text-dark' : 'bg-success') ?>"><?= $item['stock'] ?></span></td>
                                <td class="text-muted small">$<?= number_format($item['precio_compra'] ?? 0, 2) ?></td>
                                <td class="fw-bold text-primary">$<?= number_format($item['precio'] ?? 0, 2) ?></td>
                                <td><?php 
                                    $pc = floatval($item['precio_compra'] ?? 0);
                                    $pv = floatval($item['precio'] ?? 0);
                                    $margen = $pc > 0 ? (($pv - $pc) / $pc) * 100 : 0;
                                    $badge = $margen > 30 ? 'bg-success' : ($margen >= 10 ? 'bg-warning text-dark' : 'bg-danger');
                                    echo '<span class="badge ' . $badge . '">' . number_format($margen, 1) . '%</span>';
                                ?></td>
                                <td class="fw-bold text-success">$<?= number_format(($item['stock'] ?? 0) * ($item['precio'] ?? 0), 2) ?></td>
                                <td><span class="text-muted small"><?= $item['fecha_actualizacion'] ? date('m/d/y', strtotime($item['fecha_actualizacion'])) : 'N/A' ?></span></td>
                                <td><?= !empty($item['lote_proxima_caducidad']) ? date('m/d/y', strtotime($item['lote_proxima_caducidad'])) : '<span class="text-muted">Sin lotes</span>' ?></td>
                                <td><?php 
                                    $dias = $item['dias_hasta_caducidad'] ?? null;
                                    if ($dias !== null && $item['lote_cantidad_activa'] > 0):
                                        echo InventarioApp.generarBadgeDias($dias);
                                    else:
                                        echo '<span class="text-muted">-</span>';
                                    endif;
                                ?></td>
                                <td><?php 
                                    $dias = $item['dias_hasta_caducidad'] ?? null;
                                    if ($dias !== null && $item['lote_cantidad_activa'] > 0):
                                        echo InventarioApp.generarBadgePrioridad($dias);
                                    else:
                                        echo '<span class="badge bg-secondary">N/A</span>';
                                    endif;
                                ?></td>
                                <td>
                                    <button class="btn btn-sm btn-success" onclick="InventarioApp.abrirVerLotes('<?= $item['idinv'] ?>', '<?= htmlspecialchars($item['producto'], ENT_QUOTES) ?>')"><i class="fas fa-eye"></i></button>
                                    <button class="btn btn-sm btn-primary" onclick="InventarioApp.abrirAgregarLote('<?= $item['idinv'] ?>', '<?= htmlspecialchars($item['producto'], ENT_QUOTES) ?>')"><i class="fas fa-plus"></i></button>
                                </td>
                                <td>
                                    <button class="btn btn-warning btn-sm" onclick="InventarioApp.abrirEditar('<?= $item['idinv'] ?>', '<?= htmlspecialchars($item['producto'], ENT_QUOTES) ?>')"><i class="fas fa-edit"></i></button>
                                    <button class="btn btn-danger btn-sm" onclick="InventarioApp.abrirEliminar('<?= $item['idinv'] ?>', '<?= htmlspecialchars($item['producto'], ENT_QUOTES) ?>')"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="14" class="text-center text-muted py-4"><i class="fas fa-seedling" style="font-size:2rem;"></i><h6 class="mt-2">No hay productos perecederos</h6></td></tr>
                    <?php endif; ?>
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <td colspan="3" class="fw-bold">Total: <?= $total_elementos_perecederos ?? 0 ?></td>
                        <td colspan="11">Stock Total: <span class="badge bg-primary"><?= array_sum(array_column($inventario_perecederos ?? [], 'stock')) ?></span></td>
                    </tr>
                </tfoot>
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
                        <input type="text" class="form-control" id="buscar-no-perecederos" placeholder="Buscar por nombre, categoría..." autocomplete="off">
                        <button class="btn btn-outline-secondary" type="button" id="limpiar-busqueda-no-perecederos" style="display: none;"><i class="fas fa-times"></i></button>
                        <span class="input-group-text bg-light" id="loading-no-perecederos" style="display: none;"><i class="fas fa-spinner fa-spin text-primary"></i></span>
                    </div>
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="filtro-categoria-no-perecederos">
                        <option value="">📂 Todas las categorías</option>
                        <option value="Artificial">Artificial</option>
                        <option value="Comestible">Comestible</option>
                        <option value="Decorativo">Decorativo</option>
                        <option value="Regalo">Regalo</option>
                        <option value="Accesorio">Accesorio</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="filtro-stock-no-perecederos">
                        <option value="">📊 Todos los estados</option>
                        <option value="bajo">Stock Bajo</option>
                        <option value="sin_stock">Sin Stock</option>
                        <option value="normal">Stock Normal</option>
                    </select>
                </div>
            </div>
            <small class="text-muted"><i class="fas fa-info-circle me-1"></i>La búsqueda y filtros se aplican automáticamente</small>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="tabla-no-perecederos">
                <thead class="table-light">
                    <tr>
                        <th class="sortable" data-sort="nombre"><i class="fas fa-sort text-muted"></i> Producto</th>
                        <th>Naturaleza</th>
                        <th>Color</th>
                        <th class="sortable" data-sort="stock"><i class="fas fa-sort text-muted"></i> Stock</th>
                        <th>P. Compra</th>
                        <th class="sortable" data-sort="precio"><i class="fas fa-sort text-muted"></i> P. Venta</th>
                        <th>Margen %</th>
                        <th class="sortable" data-sort="valor_total"><i class="fas fa-sort text-muted"></i> Ingresos Pot.</th>
                        <th class="sortable" data-sort="fecha_actualizacion"><i class="fas fa-sort text-muted"></i> Fº Actualización</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($inventario_no_perecederos)): ?>
                        <?php foreach ($inventario_no_perecederos as $item): ?>
                            <tr>
                                <td><div class="fw-bold"><?= htmlspecialchars($item['producto']) ?></div></td>
                                <td><span class="badge bg-secondary"><?= htmlspecialchars($item['naturaleza']) ?></span></td>
                                <td><span class="badge" style="background-color: <?= InventarioApp.getColorHex(htmlspecialchars($item['color'])) ?>; color: <?= in_array($item['color'], ['Blanco', 'Amarillo']) ? '#000' : '#fff' ?>;"><?= htmlspecialchars($item['color']) ?></span></td>
                                <td><span class="badge <?= $item['stock'] == 0 ? 'bg-danger' : ($item['stock'] < 20 ? 'bg-warning text-dark' : 'bg-success') ?>"><?= $item['stock'] ?></span></td>
                                <td class="text-muted small">$<?= number_format($item['precio_compra'] ?? 0, 2) ?></td>
                                <td class="fw-bold text-primary">$<?= number_format($item['precio'] ?? 0, 2) ?></td>
                                <td><?php 
                                    $pc = floatval($item['precio_compra'] ?? 0);
                                    $pv = floatval($item['precio'] ?? 0);
                                    $margen = $pc > 0 ? (($pv - $pc) / $pc) * 100 : 0;
                                    $badge = $margen > 30 ? 'bg-success' : ($margen >= 10 ? 'bg-warning text-dark' : 'bg-danger');
                                    echo '<span class="badge ' . $badge . '">' . number_format($margen, 1) . '%</span>';
                                ?></td>
                                <td class="fw-bold text-success">$<?= number_format(($item['stock'] ?? 0) * ($item['precio'] ?? 0), 2) ?></td>
                                <td><span class="text-muted small"><i class="far fa-clock me-1"></i><?= $item['fecha_actualizacion'] ? date('m/d/y H:i', strtotime($item['fecha_actualizacion'])) : 'N/A' ?></span></td>
                                <td><?= $item['stock'] > 0 ? '<span class="badge bg-success">Disponible</span>' : '<span class="badge bg-danger">Agotado</span>' ?></td>
                                <td>
                                    <button class="btn btn-warning btn-sm" onclick="InventarioApp.abrirEditar('<?= $item['idinv'] ?>', '<?= htmlspecialchars($item['producto'], ENT_QUOTES) ?>')"><i class="fas fa-edit"></i></button>
                                    <button class="btn btn-info btn-sm" onclick="InventarioApp.abrirStock('<?= $item['idinv'] ?>', '<?= htmlspecialchars($item['producto'], ENT_QUOTES) ?>')"><i class="fas fa-plus"></i></button>
                                    <button class="btn btn-danger btn-sm" onclick="InventarioApp.abrirEliminar('<?= $item['idinv'] ?>', '<?= htmlspecialchars($item['producto'], ENT_QUOTES) ?>')"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="11" class="text-center text-muted py-4"><i class="fas fa-box" style="font-size:2rem;"></i><h6 class="mt-2">No hay productos no perecederos</h6></td></tr>
                    <?php endif; ?>
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <td colspan="3" class="fw-bold">Total: <?= $total_elementos_no_perecederos ?? 0 ?></td>
                        <td colspan="8">Stock Total: <span class="badge bg-primary"><?= array_sum(array_column($inventario_no_perecederos ?? [], 'stock')) ?></span></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Providers Section -->
    <div class="card mt-5">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0"><i class="fas fa-truck me-2"></i>Proveedores Registrados</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-striped align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Nombre</th>
                        <th>Categoría</th>
                        <th>Teléfono</th>
                        <th>Email</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($proveedores)): ?>
                        <?php foreach ($proveedores as $prov): ?>
                            <tr>
                                <td><?= htmlspecialchars($prov['nombre']) ?></td>
                                <td><?= htmlspecialchars($prov['categoria']) ?></td>
                                <td><?= htmlspecialchars($prov['telefono']) ?></td>
                                <td><?= htmlspecialchars($prov['email']) ?></td>
                                <td><?= $prov['estado'] === 'activo' ? '<span class="badge bg-success">Activo</span>' : '<span class="badge bg-secondary">Inactivo</span>' ?></td>
                                <td>
                                    <button class="btn btn-warning btn-sm" onclick="InventarioApp.abrirEditarProveedor(<?= intval($prov['id']) ?>, '<?= htmlspecialchars($prov['nombre'], ENT_QUOTES) ?>', '<?= htmlspecialchars($prov['categoria'], ENT_QUOTES) ?>', '<?= htmlspecialchars($prov['telefono'], ENT_QUOTES) ?>', '<?= htmlspecialchars($prov['email'], ENT_QUOTES) ?>', '<?= htmlspecialchars($prov['direccion'] ?? '', ENT_QUOTES) ?>', '<?= htmlspecialchars($prov['notas'] ?? '', ENT_QUOTES) ?>', '<?= htmlspecialchars($prov['estado'], ENT_QUOTES) ?>')"><i class="fas fa-edit"></i></button>
                                    <button class="btn btn-danger btn-sm" data-proveedor-id="<?= intval($prov['id']) ?>" data-proveedor-nombre="<?= htmlspecialchars($prov['nombre']) ?>" data-bs-toggle="modal" data-bs-target="#modal-eliminar-proveedor"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="text-center text-muted py-4"><i class="fas fa-truck" style="font-size:2rem;"></i><h6 class="mt-2">No hay proveedores registrados</h6></td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</main>

<!-- MODALS -->
<?php include 'modals/modal_productos.php'; ?>
<?php include 'modals/modal_proveedores.php'; ?>
<?php include 'modals/modal_lotes.php'; ?>

<!-- BOOTSTRAP -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- APP SCRIPT -->
<script>
const InventarioApp = {
    // Configuración
    config: {
        debounceDelay: 500,
        tiempoTimeout: 10000
    },

    // Datos globales
    data: {
        productos: <?= json_encode($productos_inventario ?? []) ?>,
        pagActual: <?= $pagina_actual ?? 1 ?>,
        limiteActual: <?= $elementos_por_pagina ?? 10 ?>,
        ordenPerec: '',
        direccionPerec: 'ASC',
        ordenNoPerec: '',
        direccionNoPerec: 'ASC'
    },

    // Inicializar app
    init() {
        this.setupEventListeners();
        this.setupExportButtons();
        console.log('✅ InventarioApp iniciado correctamente');
    },

    // Setup event listeners
    setupEventListeners() {
        // Búsqueda perecederos
        const buscarPerec = document.getElementById('buscar-perecederos');
        const filtroPerec = document.getElementById('filtro-stock-perecederos');
        if (buscarPerec) {
            buscarPerec.addEventListener('input', () => this.debounce(() => this.buscar('perecedero'), this.config.debounceDelay));
        }
        if (filtroPerec) {
            filtroPerec.addEventListener('change', () => this.buscar('perecedero'));
        }

        // Búsqueda no perecederos
        const buscarNoPerec = document.getElementById('buscar-no-perecederos');
        const filtroCateg = document.getElementById('filtro-categoria-no-perecederos');
        const filtroStock = document.getElementById('filtro-stock-no-perecederos');
        if (buscarNoPerec) {
            buscarNoPerec.addEventListener('input', () => this.debounce(() => this.buscar('no_perecedero'), this.config.debounceDelay));
        }
        if (filtroCateg) {
            filtroCateg.addEventListener('change', () => this.buscar('no_perecedero'));
        }
        if (filtroStock) {
            filtroStock.addEventListener('change', () => this.buscar('no_perecedero'));
        }

        // Limpiar búsquedas
        document.getElementById('limpiar-busqueda-perecederos')?.addEventListener('click', () => this.limpiarBusqueda('perecedero'));
        document.getElementById('limpiar-busqueda-no-perecederos')?.addEventListener('click', () => this.limpiarBusqueda('no_perecedero'));

        // Ordenamiento
        document.querySelectorAll('.sortable').forEach(th => {
            th.addEventListener('click', () => this.cambiarOrden(th.dataset.sort, th.closest('table').id));
        });
    },

    // Debounce helper
    debounce(func, wait) {
        clearTimeout(this.debounceTimer);
        this.debounceTimer = setTimeout(func, wait);
    },

    // Modales
    abrirNuevoProducto() {
        const modal = new bootstrap.Modal(document.getElementById('modal-nuevo-producto'));
        modal.show();
    },

    abrirProveedores() {
        const modal = new bootstrap.Modal(document.getElementById('modal-proveedores'));
        modal.show();
    },

    abrirConfiguracion() {
        const modal = new bootstrap.Modal(document.getElementById('modal-configuracion'));
        modal.show();
    },

    abrirEditar(id, nombre) {
        const producto = this.data.productos.find(p => p.idinv == id);
        if (!producto) {
            alert('Error: Producto no encontrado');
            return;
        }
        
        document.getElementById('editar_producto_id').value = id;
        document.getElementById('editar_nombre_producto').value = nombre;
        document.getElementById('editar_stock').value = producto.stock || 0;
        document.getElementById('editar_precio_compra').value = producto.precio_compra || 0;
        document.getElementById('editar_precio').value = producto.precio || 0;
        
        const modal = new bootstrap.Modal(document.getElementById('modal-editar-producto'));
        modal.show();
    },

    abrirStock(id, nombre) {
        const producto = this.data.productos.find(p => p.idinv == id);
        document.getElementById('stock_producto_id').value = id;
        document.getElementById('stock_nombre_producto').textContent = nombre;
        document.getElementById('stock_actual').textContent = producto?.stock || 0;
        
        const modal = new bootstrap.Modal(document.getElementById('modal-agregar-stock'));
        modal.show();
    },

    abrirEliminar(id, nombre) {
        document.getElementById('eliminar_producto_id').value = id;
        document.getElementById('eliminar_nombre_producto').textContent = nombre;
        
        const modal = new bootstrap.Modal(document.getElementById('modal-eliminar-producto'));
        modal.show();
    },

    abrirVerLotes(id, nombre) {
        document.getElementById('ver_lotes_producto_id').value = id;
        document.getElementById('ver_lotes_producto_nombre').textContent = nombre;
        const modal = new bootstrap.Modal(document.getElementById('modal-ver-lotes'));
        modal.show();
    },

    abrirAgregarLote(id, nombre) {
        document.getElementById('agregar_lote_producto_id').value = id;
        document.getElementById('agregar_lote_producto_nombre').textContent = nombre;
        const modal = new bootstrap.Modal(document.getElementById('modal-agregar-lote'));
        modal.show();
    },

    abrirEditarProveedor(id, nombre, categoria, telefono, email, direccion, notas, estado) {
        document.getElementById('editar_proveedor_id').value = id;
        document.getElementById('editar_nombre_proveedor').value = nombre;
        document.getElementById('editar_categoria_proveedor').value = categoria;
        document.getElementById('editar_telefono_proveedor').value = telefono;
        document.getElementById('editar_email_proveedor').value = email;
        document.getElementById('editar_direccion_proveedor').value = direccion;
        document.getElementById('editar_notas_proveedor').value = notas;
        document.getElementById('editar_estado_proveedor').value = estado;
        
        const modal = new bootstrap.Modal(document.getElementById('modal-editar-proveedor'));
        modal.show();
    },

    // Búsqueda
    buscar(tipo) {
        console.log('🔍 Buscando tipo:', tipo);
        // Implementar búsqueda AJAX aquí
    },

    limpiarBusqueda(tipo) {
        if (tipo === 'perecedero') {
            document.getElementById('buscar-perecederos').value = '';
            document.getElementById('filtro-stock-perecederos').value = '';
            document.getElementById('limpiar-busqueda-perecederos').style.display = 'none';
        } else {
            document.getElementById('buscar-no-perecederos').value = '';
            document.getElementById('filtro-categoria-no-perecederos').value = '';
            document.getElementById('filtro-stock-no-perecederos').value = '';
            document.getElementById('limpiar-busqueda-no-perecederos').style.display = 'none';
        }
        window.location.href = '?ctrl=cinventario';
    },

    cambiarOrden(columna, tablaId) {
        console.log('📊 Ordenando por:', columna);
    },

    cambiarLimite() {
        const limite = document.getElementById('itemsPerPage').value;
        const url = new URL(window.location);
        url.searchParams.set('per_page', limite);
        url.searchParams.set('pagina', 1);
        window.location.href = url.toString();
    },

    sincronizarStocks() {
        if (!confirm('¿Sincronizar stock de todos los productos?')) return;
        // Implementar sincronización aquí
    },

    // Utilidades
    getColorHex(color) {
        const colores = {
            'Rojo': '#dc3545', 'Rosa': '#ff69b4', 'Blanco': '#f8f9fa',
            'Amarillo': '#ffc107', 'Naranja': '#fd7e14', 'Morado': '#6f42c1',
            'Azul': '#0d6efd', 'Verde': '#198754', 'Multicolor': '#6c757d'
        };
        return colores[color] || '#6c757d';
    },

    generarBadgeDias(dias) {
        if (dias <= 3) return `<span class="badge bg-danger"><i class="fas fa-circle-exclamation"></i> ${dias}d</span>`;
        if (dias <= 5) return `<span class="badge bg-warning text-dark"><i class="fas fa-exclamation-triangle"></i> ${dias}d</span>`;
        if (dias <= 7) return `<span class="badge bg-info"><i class="fas fa-info-circle"></i> ${dias}d</span>`;
        return `<span class="badge bg-secondary">${dias}d</span>`;
    },

    generarBadgePrioridad(dias) {
        if (dias <= 3) return '<span class="badge bg-danger"><i class="fas fa-exclamation-triangle"></i> CRÍTICO</span>';
        if (dias <= 5) return '<span class="badge bg-warning text-dark"><i class="fas fa-exclamation-circle"></i> URGENTE</span>';
        if (dias <= 7) return '<span class="badge bg-info"><i class="fas fa-info-circle"></i> ALERTA</span>';
        return '<span class="badge bg-success">OK</span>';
    },

    setupExportButtons() {
        document.getElementById('btn-exportar')?.addEventListener('click', () => this.exportarExcel());
        document.getElementById('btn-exportar-pdf')?.addEventListener('click', () => this.exportarPDF());
    },

    exportarExcel() {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '?ctrl=Cinventario';
        form.innerHTML = '<input type="hidden" name="accion" value="exportar_inventario">';
        document.body.appendChild(form);
        form.submit();
        document.body.removeChild(form);
    },

    exportarPDF() {
        const tipo = confirm('¿Exportar TODO el inventario?') ? 'todos' : 'perecedero';
        window.location.href = `?ctrl=Cinventario&action=exportarInventarioPDF&tipo=${tipo}`;
    }
};

// Inicializar cuando DOM esté listo
document.addEventListener('DOMContentLoaded', () => InventarioApp.init());
</script>

<script src="/Original-Floraltech/assets/inventario.js"></script>
<script src="/Original-Floraltech/assets/inventario_modal_handler.js"></script>
