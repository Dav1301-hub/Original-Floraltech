<!-- Gestión de Inventario - Vista -->
<main class="container-fluid py-4">
    <h2 class="mb-4 fw-bold">Gestión de Inventario</h2>
    
    <!-- DEBUG: Confirmar que la vista correcta se está cargando -->
    <div class="alert alert-info">
        <strong>DEBUG:</strong> Vista de inventario cargada correctamente. 
        Elementos disponibles: <?= isset($inventario) ? count($inventario) : 0 ?> 
        de <?= $total_elementos ?? 0 ?> total.
    </div>
    
    <!-- Mensajes de éxito y error -->
    <?php if (!empty($mensaje_exito)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i><?= htmlspecialchars($mensaje_exito) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($mensaje_error)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i><?= htmlspecialchars($mensaje_error) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle me-2"></i><?= htmlspecialchars($error_message) ?>
        </div>
    <?php endif; ?>
    
    <!-- Tarjetas métricas -->
    <div class="row mb-4 g-3">
        <div class="col-12 col-md-3">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <i class="fas fa-boxes h2 text-primary"></i>
                    <h6 class="fw-bold mt-2">Total Productos</h6>
                    <div class="h4"><?= $total_productos ?? 0 ?></div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-3">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <i class="fas fa-exclamation-triangle h2 text-warning"></i>
                    <h6 class="fw-bold mt-2">Stock Bajo</h6>
                    <div class="h4"><?= $stock_bajo ?? 0 ?></div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-3">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <i class="fas fa-times-circle h2 text-danger"></i>
                    <h6 class="fw-bold mt-2">Sin Stock</h6>
                    <div class="h4"><?= $sin_stock ?? 0 ?></div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-3">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <i class="fas fa-dollar-sign h2 text-success"></i>
                    <h6 class="fw-bold mt-2">Valor Total</h6>
                    <div class="h4">$<?= number_format($valor_total ?? 0, 0, ',', '.') ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros de Inventario -->
    <fieldset class="border rounded p-3 mb-4 bg-light">
        <legend class="float-none w-auto px-2 mb-2 fw-bold"><i class="fas fa-filter"></i> Filtros de Inventario</legend>
        <form class="row g-2 flex-wrap align-items-end" method="get" action="?ctrl=cinventario">
            <input type="hidden" name="ctrl" value="cinventario">
            <div class="col-12 col-md-3">
                <label for="categoria" class="form-label mb-1">Naturaleza</label>
                <select id="categoria" name="categoria" class="form-select">
                    <option value="">Todas las naturalezas</option>
                    <option value="Natural" <?= (isset($_GET['categoria']) && $_GET['categoria'] == 'Natural') ? 'selected' : '' ?>>Natural</option>
                    <option value="Artificial" <?= (isset($_GET['categoria']) && $_GET['categoria'] == 'Artificial') ? 'selected' : '' ?>>Artificial</option>
                </select>
            </div>
            <div class="col-12 col-md-3">
                <label for="estado_stock" class="form-label mb-1">Estado de Stock</label>
                <select id="estado_stock" name="estado_stock" class="form-select">
                    <option value="">Todos los estados</option>
                    <option value="bajo" <?= (isset($_GET['estado_stock']) && $_GET['estado_stock'] == 'bajo') ? 'selected' : '' ?>>Bajo</option>
                    <option value="sin_stock" <?= (isset($_GET['estado_stock']) && $_GET['estado_stock'] == 'sin_stock') ? 'selected' : '' ?>>Sin Stock</option>
                    <option value="normal" <?= (isset($_GET['estado_stock']) && $_GET['estado_stock'] == 'normal') ? 'selected' : '' ?>>Normal</option>
                </select>
            </div>
            <div class="col-12 col-md-3">
                <label for="buscar" class="form-label mb-1">Buscar</label>
                <input type="text" id="buscar" name="buscar" class="form-control" placeholder="Nombre de la flor..." value="<?= htmlspecialchars($_GET['buscar'] ?? '') ?>">
            </div>
            <div class="col-12 col-md-3">
                <button type="submit" class="btn btn-primary w-100">Filtrar</button>
                <a href="?ctrl=cinventario" class="d-block mt-2 text-secondary">Limpiar Filtros</a>
            </div>
        </form>
    </fieldset>

    <!-- Botones de acción de inventario -->
    <div class="d-flex justify-content-center flex-wrap gap-2 mb-4">
        <button class="btn btn-success shadow-sm" onclick="abrirproducto()" id="btn-nuevo-producto">
            <i class="fas fa-plus me-2"></i>Nuevo Producto
        </button>
        <button class="btn btn-info shadow-sm" onclick="abrirproveedor()">
            <i class="fas fa-truck me-2"></i>Proveedores
        </button>
        <button class="btn btn-secondary shadow-sm" onclick="abrirParametros()">
            <i class="fas fa-cog me-2"></i>Parámetros Inventario
        </button>
        <button class="btn btn-warning shadow-sm" onclick="gestionarFlores()">
            <i class="fas fa-seedling me-2"></i>Gestión de Flores
        </button>
    </div>

    <!-- Controles de listado -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="d-flex align-items-center gap-2">
            <label for="itemsPerPage" class="form-label mb-0">Mostrar:</label>
            <select id="itemsPerPage" class="form-select form-select-sm" style="width: auto;" onchange="cambiarLimite()">
                <option value="10" <?= (($elementos_por_pagina ?? 10) == 10) ? 'selected' : '' ?>>10</option>
                <option value="25" <?= (($elementos_por_pagina ?? 10) == 25) ? 'selected' : '' ?>>25</option>
                <option value="50" <?= (($elementos_por_pagina ?? 10) == 50) ? 'selected' : '' ?>>50</option>
                <option value="100" <?= (($elementos_por_pagina ?? 10) == 100) ? 'selected' : '' ?>>100</option>
            </select>
            <span class="text-muted">productos por página</span>
        </div>
        
        <div class="d-flex gap-2">
            <button class="btn btn-outline-secondary btn-sm" onclick="recargarListado()" title="Actualizar listado">
                <i class="fas fa-sync-alt"></i> Actualizar
            </button>
            <button class="btn btn-outline-danger btn-sm" onclick="detenerCarga()" title="Detener carga" style="display: none;" id="stopLoadingBtn">
                <i class="fas fa-stop"></i> Detener
            </button>
            <button class="btn btn-outline-info btn-sm" onclick="exportarInventario()" title="Exportar a Excel">
                <i class="fas fa-file-excel"></i> Exportar
            </button>
        </div>
    </div>

    <!-- Tabla de inventario -->
    <div class="position-relative">
        <!-- Loading indicator -->
        <div id="loadingIndicator" class="position-absolute w-100 h-100 d-flex align-items-center justify-content-center bg-white bg-opacity-75" style="display: none !important; z-index: 10;">
            <div class="text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
                <p class="mt-2 text-muted">Actualizando inventario...</p>
            </div>
        </div>
        
        <div class="table-responsive" id="productListContainer">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Producto</th>
                        <th>Naturaleza</th>
                        <th>Color</th>
                        <th>Stock</th>
                        <th>Estado</th>
                        <th>Precio Unitario</th>
                        <th>Valor Total</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="inventarioTableBody">
                    <?php if (!empty($inventario)): ?>
                        <?php foreach ($inventario as $item): ?>
                            <tr>
                                <td class="fw-bold"><?= htmlspecialchars($item['producto']) ?></td>
                                <td><?= htmlspecialchars($item['naturaleza']) ?></td>
                                <td><?= htmlspecialchars($item['color']) ?></td>
                                <td>
                                    <span class="badge <?= $item['stock'] == 0 ? 'bg-danger' : ($item['stock'] < 20 ? 'bg-warning' : 'bg-success') ?>">
                                        <?= $item['stock'] ?>
                                    </span>
                                </td>
                                <td>
                                    <?php
                                    $estado_class = '';
                                    switch($item['estado_stock']) {
                                        case 'Sin Stock':
                                            $estado_class = 'text-danger';
                                            break;
                                        case 'Bajo':
                                            $estado_class = 'text-warning';
                                            break;
                                        default:
                                            $estado_class = 'text-success';
                                    }
                                    ?>
                                    <span class="<?= $estado_class ?> fw-bold"><?= $item['estado_stock'] ?></span>
                                </td>
                                <td>$<?= number_format($item['precio'], 0, ',', '.') ?></td>
                                <td>$<?= number_format($item['stock'] * $item['precio'], 0, ',', '.') ?></td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-warning btn-sm" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-info btn-sm" title="Agregar Stock">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center text-warning py-4">
                            <i class="fas fa-search" style="font-size:2rem;"></i>
                            <h5 class="mt-2">No se encontraron productos</h5>
                            <p>No hay productos en el inventario o no coinciden con los filtros aplicados</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Paginación AJAX -->
    <div id="paginationContainer">
        <?php if (isset($total_paginas) && $total_paginas > 1): ?>
        <div class="d-flex justify-content-between align-items-center mt-4">
            <div class="text-muted" id="paginationInfo">
                Mostrando <?= min(($offset ?? 0) + 1, $total_elementos ?? 0) ?> - <?= min(($offset ?? 0) + ($elementos_por_pagina ?? 10), $total_elementos ?? 0) ?> 
                de <?= $total_elementos ?? 0 ?> productos
            </div>
            
            <nav aria-label="Paginación del inventario">
                <ul class="pagination pagination-sm mb-0">
                    <!-- Botón anterior -->
                    <li class="page-item <?= ($pagina_actual ?? 1) <= 1 ? 'disabled' : '' ?>">
                        <a class="page-link" href="#" onclick="cargarPagina(<?= ($pagina_actual ?? 1) - 1 ?>)" aria-label="Anterior">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                    
                    <?php
                    // Mostrar páginas
                    $pagina_actual = $pagina_actual ?? 1;
                    $total_paginas = $total_paginas ?? 1;
                    
                    $inicio = max(1, $pagina_actual - 2);
                    $fin = min($total_paginas, $pagina_actual + 2);
                    
                    // Primera página si no está en el rango visible
                    if ($inicio > 1) {
                        echo '<li class="page-item"><a class="page-link" href="#" onclick="cargarPagina(1)">1</a></li>';
                        if ($inicio > 2) {
                            echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                        }
                    }
                    
                    // Páginas en el rango visible
                    for ($i = $inicio; $i <= $fin; $i++):
                    ?>
                        <li class="page-item <?= $i == $pagina_actual ? 'active' : '' ?>">
                            <a class="page-link" href="#" onclick="cargarPagina(<?= $i ?>)"><?= $i ?></a>
                        </li>
                    <?php 
                    endfor;
                    
                    // Última página si no está en el rango visible
                    if ($fin < $total_paginas) {
                        if ($fin < $total_paginas - 1) {
                            echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                        }
                        echo '<li class="page-item"><a class="page-link" href="#" onclick="cargarPagina(' . $total_paginas . ')">' . $total_paginas . '</a></li>';
                    }
                    ?>
                    
                    <!-- Botón siguiente -->
                    <li class="page-item <?= ($pagina_actual ?? 1) >= ($total_paginas ?? 1) ? 'disabled' : '' ?>">
                        <a class="page-link" href="#" onclick="cargarPagina(<?= ($pagina_actual ?? 1) + 1 ?>)" aria-label="Siguiente">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                </ul>
            </nav>
            
            <!-- Selector de elementos por página -->
            <div class="d-flex align-items-center">
                <span class="text-muted me-2 small">Mostrar:</span>
                <select class="form-select form-select-sm" style="width: auto;" onchange="cambiarElementosPorPagina(this.value)">
                    <option value="10" <?= ($elementos_por_pagina ?? 10) == 10 ? 'selected' : '' ?>>10</option>
                    <option value="25" <?= ($elementos_por_pagina ?? 10) == 25 ? 'selected' : '' ?>>25</option>
                    <option value="50" <?= ($elementos_por_pagina ?? 10) == 50 ? 'selected' : '' ?>>50</option>
                    <option value="100" <?= ($elementos_por_pagina ?? 10) == 100 ? 'selected' : '' ?>>100</option>
                </select>
            </div>
        </div>
        <?php endif; ?>
    </div>
</main>

<!-- Modales -->
<!-- Modal Nuevo Producto Mejorado -->
<div class="modal fade" id="modal-nuevo-producto" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-plus me-2"></i>Agregar Nuevo Producto al Inventario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="?ctrl=cinventario" id="form-nuevo-producto">
                    <input type="hidden" name="accion" value="nuevo_producto">
                    
                    <div class="row g-3">
                        <!-- Tipo de Producto -->
                        <div class="col-md-6">
                            <label class="form-label"><i class="fas fa-layer-group me-1"></i>Tipo de Producto *</label>
                            <select class="form-select" name="tipo_producto" id="tipo_producto" required onchange="cambiarTipoProducto()">
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
                        
                        <!-- Nombre del Producto -->
                        <div class="col-md-6">
                            <label class="form-label"><i class="fas fa-tag me-1"></i>Nombre del Producto *</label>
                            <input type="text" class="form-control" name="nombre_producto" required placeholder="Ej: Rosa Roja, Chocolate Ferrero, Tarjeta de Amor">
                            <small class="text-muted">Nombre descriptivo del producto</small>
                        </div>
                        
                        <!-- Selección de Flor (solo visible para flores) -->
                        <div class="col-12" id="seccion_flor" style="display: none;">
                            <label class="form-label"><i class="fas fa-seedling me-1"></i>Seleccionar Flor Existente (Opcional)</label>
                            <select class="form-select" name="tflor_idtflor" id="flor_select">
                                <option value="">Crear nueva flor o dejar en blanco para producto genérico</option>
                                <?php if (!empty($flores_para_select)): ?>
                                    <?php foreach ($flores_para_select as $flor): ?>
                                        <option value="<?= $flor['idtflor'] ?>"><?= htmlspecialchars($flor['nombre']) ?> (<?= htmlspecialchars($flor['naturaleza']) ?> - <?= htmlspecialchars($flor['color']) ?>)</option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <small class="text-muted">Si es una flor nueva, déjalo en blanco y se creará automáticamente</small>
                        </div>
                        
                        <!-- Categoría/Naturaleza -->
                        <div class="col-md-6">
                            <label class="form-label"><i class="fas fa-list me-1"></i>Categoría *</label>
                            <select class="form-select" name="categoria" required>
                                <option value="">Selecciona categoría...</option>
                                <option value="Natural">Natural</option>
                                <option value="Artificial">Artificial</option>
                                <option value="Comestible">Comestible</option>
                                <option value="Decorativo">Decorativo</option>
                                <option value="Regalo">Regalo</option>
                                <option value="Accesorio">Accesorio</option>
                            </select>
                        </div>
                        
                        <!-- Color -->
                        <div class="col-md-6">
                            <label class="form-label"><i class="fas fa-palette me-1"></i>Color Principal</label>
                            <select class="form-select" name="color">
                                <option value="Multicolor">Multicolor</option>
                                <option value="Rojo">Rojo</option>
                                <option value="Rosa">Rosa</option>
                                <option value="Blanco">Blanco</option>
                                <option value="Amarillo">Amarillo</option>
                                <option value="Naranja">Naranja</option>
                                <option value="Morado">Morado</option>
                                <option value="Azul">Azul</option>
                                <option value="Verde">Verde</option>
                                <option value="Negro">Negro</option>
                                <option value="Dorado">Dorado</option>
                                <option value="Plateado">Plateado</option>
                            </select>
                        </div>
                        
                        <!-- Stock Inicial -->
                        <div class="col-md-6">
                            <label class="form-label"><i class="fas fa-boxes me-1"></i>Stock Inicial *</label>
                            <input type="number" class="form-control" name="stock" min="0" required placeholder="Ej: 50">
                            <small class="text-muted">Cantidad inicial en inventario</small>
                        </div>
                        
                        <!-- Precio -->
                        <div class="col-md-6">
                            <label class="form-label"><i class="fas fa-dollar-sign me-1"></i>Precio Unitario *</label>
                            <input type="number" step="0.01" class="form-control" name="precio" min="0" required placeholder="0.00">
                            <small class="text-muted">Precio de venta por unidad</small>
                        </div>
                        
                        <!-- Descripción -->
                        <div class="col-12">
                            <label class="form-label"><i class="fas fa-align-left me-1"></i>Descripción</label>
                            <textarea class="form-control" name="descripcion" rows="3" placeholder="Descripción detallada del producto, características especiales, etc."></textarea>
                        </div>
                    </div>
                    
                    <div class="mt-4 d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>Cancelar
                        </button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-plus me-1"></i>Agregar al Inventario
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Gestión de Flores Avanzado -->
<div class="modal fade" id="modal-gestion-flores" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title"><i class="fas fa-seedling me-2"></i>Gestión Completa de Flores</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Pestañas de navegación -->
                <ul class="nav nav-tabs mb-3" id="floresTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="lista-tab" data-bs-toggle="tab" data-bs-target="#lista-flores" type="button" role="tab">
                            <i class="fas fa-list me-1"></i>Lista de Flores
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="nueva-tab" data-bs-toggle="tab" data-bs-target="#nueva-flor" type="button" role="tab">
                            <i class="fas fa-plus me-1"></i>Nueva Flor
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="estadisticas-tab" data-bs-toggle="tab" data-bs-target="#estadisticas-flores" type="button" role="tab">
                            <i class="fas fa-chart-pie me-1"></i>Estadísticas
                        </button>
                    </li>
                </ul>

                <!-- Contenido de las pestañas -->
                <div class="tab-content" id="floresTabContent">
                    <!-- Lista de todas las flores -->
                    <div class="tab-pane fade show active" id="lista-flores" role="tabpanel">
                        <div class="mb-3">
                            <input type="text" class="form-control" id="buscar-flor" placeholder="🔍 Buscar flores por nombre, naturaleza o color...">
                        </div>
                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                            <table class="table table-hover table-sm">
                                <thead class="table-light sticky-top">
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre</th>
                                        <th>Naturaleza</th>
                                        <th>Color</th>
                                        <th>En Inventario</th>
                                        <th>Stock</th>
                                        <th>Precio</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="tabla-flores">
                                    <?php if (isset($todas_las_flores) && is_array($todas_las_flores)): ?>
                                        <?php foreach ($todas_las_flores as $flor): ?>
                                        <tr data-flor-id="<?= $flor['idtflor'] ?>">
                                            <td><span class="badge bg-secondary"><?= $flor['idtflor'] ?></span></td>
                                            <td><strong><?= htmlspecialchars($flor['nombre']) ?></strong></td>
                                        <td>
                                            <span class="badge bg-<?= $flor['naturaleza'] === 'Natural' ? 'success' : 'info' ?>">
                                                <?= htmlspecialchars($flor['naturaleza']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge" style="background-color: <?= $flor['color'] === 'Multicolor' ? '#ff6b6b' : ($flor['color'] === 'Blanco' ? '#f8f9fa; color: #000' : '#' . substr(md5($flor['color']), 0, 6)) ?>">
                                                <?= htmlspecialchars($flor['color']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($flor['idinv']): ?>
                                                <i class="fas fa-check text-success"></i> Sí
                                            <?php else: ?>
                                                <i class="fas fa-times text-danger"></i> No
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?= $flor['stock'] ? $flor['stock'] : '<span class="text-muted">N/A</span>' ?>
                                        </td>
                                        <td>
                                            <?= $flor['precio'] ? '$' . number_format($flor['precio'], 2) : '<span class="text-muted">N/A</span>' ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?= 
                                                $flor['estado_inventario'] === 'Disponible' ? 'success' : 
                                                ($flor['estado_inventario'] === 'Stock Bajo' ? 'warning' : 
                                                ($flor['estado_inventario'] === 'Sin Stock' ? 'danger' : 'secondary')) 
                                            ?>">
                                                <?= $flor['estado_inventario'] ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <button class="btn btn-outline-primary" onclick="editarFlor(<?= htmlspecialchars(json_encode($flor)) ?>)" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <?php if (!$flor['idinv']): ?>
                                                <button class="btn btn-outline-success" onclick="agregarAInventario(<?= $flor['idtflor'] ?>, '<?= htmlspecialchars($flor['nombre']) ?>')" title="Agregar a inventario">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                                <?php endif; ?>
                                                <button class="btn btn-outline-danger" onclick="eliminarFlor(<?= $flor['idtflor'] ?>, '<?= htmlspecialchars($flor['nombre']) ?>')" title="Eliminar">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7" class="text-center">
                                                <div class="alert alert-info mb-0">
                                                    <i class="fas fa-info-circle"></i>
                                                    No hay flores registradas en el sistema.
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Nueva flor -->
                    <div class="tab-pane fade" id="nueva-flor" role="tabpanel">
                        <form method="POST" action="?ctrl=cinventario" id="form-nueva-flor">
                            <input type="hidden" name="accion" value="nueva_flor">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label"><i class="fas fa-tag me-1"></i>Nombre de la Flor *</label>
                                    <input type="text" class="form-control" name="nombre" required placeholder="Ej: Rosa Roja Premium">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label"><i class="fas fa-leaf me-1"></i>Naturaleza *</label>
                                    <select class="form-select" name="naturaleza" required>
                                        <option value="">Seleccionar...</option>
                                        <option value="Natural">Natural</option>
                                        <option value="Artificial">Artificial</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label"><i class="fas fa-palette me-1"></i>Color *</label>
                                    <select class="form-select" name="color" required>
                                        <option value="">Seleccionar...</option>
                                        <option value="Rojo">Rojo</option>
                                        <option value="Blanco">Blanco</option>
                                        <option value="Rosa">Rosa</option>
                                        <option value="Amarillo">Amarillo</option>
                                        <option value="Morado">Morado</option>
                                        <option value="Azul">Azul</option>
                                        <option value="Multicolor">Multicolor</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label"><i class="fas fa-file-alt me-1"></i>Descripción</label>
                                    <textarea class="form-control" name="descripcion" rows="3" placeholder="Descripción detallada de la flor..."></textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label"><i class="fas fa-boxes me-1"></i>Stock Inicial (Opcional)</label>
                                    <input type="number" class="form-control" name="stock_inicial" min="0" placeholder="Cantidad inicial">
                                    <small class="text-muted">Si especificas stock, se agregará automáticamente al inventario</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label"><i class="fas fa-dollar-sign me-1"></i>Precio Inicial (Opcional)</label>
                                    <input type="number" step="0.01" class="form-control" name="precio_inicial" min="0" placeholder="0.00">
                                    <small class="text-muted">Precio de venta por unidad</small>
                                </div>
                            </div>
                            <div class="mt-4 d-flex justify-content-end gap-2">
                                <button type="button" class="btn btn-secondary" onclick="limpiarFormularioFlor()">
                                    <i class="fas fa-eraser me-1"></i>Limpiar
                                </button>
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-seedling me-1"></i>Crear Flor
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Estadísticas -->
                    <div class="tab-pane fade" id="estadisticas-flores" role="tabpanel">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <div class="card text-center">
                                    <div class="card-body">
                                        <i class="fas fa-seedling fa-2x text-success mb-2"></i>
                                        <h6>Total Flores</h6>
                                        <h4 class="text-success"><?= isset($todas_las_flores) && is_array($todas_las_flores) ? count($todas_las_flores) : 0 ?></h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card text-center">
                                    <div class="card-body">
                                        <i class="fas fa-leaf fa-2x text-primary mb-2"></i>
                                        <h6>Naturales</h6>
                                        <h4 class="text-primary"><?= count(array_filter($todas_las_flores, fn($f) => $f['naturaleza'] === 'Natural')) ?></h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card text-center">
                                    <div class="card-body">
                                        <i class="fas fa-cog fa-2x text-info mb-2"></i>
                                        <h6>Artificiales</h6>
                                        <h4 class="text-info"><?= count(array_filter($todas_las_flores, fn($f) => $f['naturaleza'] === 'Artificial')) ?></h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card text-center">
                                    <div class="card-body">
                                        <i class="fas fa-boxes fa-2x text-warning mb-2"></i>
                                        <h6>En Inventario</h6>
                                        <h4 class="text-warning"><?= count(array_filter($todas_las_flores, fn($f) => $f['idinv'] !== null)) ?></h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Gráfico de distribución por color -->
                        <div class="mt-4">
                            <h6><i class="fas fa-chart-pie me-2"></i>Distribución por Colores</h6>
                            <div class="row">
                                <?php 
                                $colores = array_count_values(array_column($todas_las_flores, 'color'));
                                foreach ($colores as $color => $cantidad): 
                                ?>
                                <div class="col-md-4 mb-2">
                                    <div class="d-flex align-items-center">
                                        <span class="badge me-2" style="background-color: <?= $color === 'Multicolor' ? '#ff6b6b' : ($color === 'Blanco' ? '#f8f9fa; color: #000' : '#' . substr(md5($color), 0, 6)) ?>">
                                            <?= htmlspecialchars($color) ?>
                                        </span>
                                        <div class="flex-grow-1">
                                            <div class="progress" style="height: 8px;">
                                                <div class="progress-bar" style="width: <?= ($cantidad / count($todas_las_flores)) * 100 ?>%; background-color: <?= $color === 'Multicolor' ? '#ff6b6b' : ($color === 'Blanco' ? '#6c757d' : '#' . substr(md5($color), 0, 6)) ?>"></div>
                                            </div>
                                        </div>
                                        <span class="ms-2 small"><?= $cantidad ?></span>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-proveedores" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title"><i class="fas fa-truck me-2"></i>Nuevo Proveedor</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="?ctrl=cinventario" id="form-nuevo-proveedor">
                    <input type="hidden" name="accion" value="nuevo_proveedor">
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label"><i class="fas fa-building me-1"></i>Nombre del Proveedor *</label>
                            <input type="text" class="form-control" name="nombre_proveedor" required 
                                   placeholder="Ej: Flores del Valle S.A.">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><i class="fas fa-tags me-1"></i>Categoría *</label>
                            <select class="form-select" name="categoria_proveedor" required>
                                <option value="">Seleccionar categoría...</option>
                                <option value="flores_frescas">Flores Frescas</option>
                                <option value="flores_artificiales">Flores Artificiales</option>
                                <option value="plantas">Plantas y Arbustos</option>
                                <option value="chocolates">Chocolates y Dulces</option>
                                <option value="caramelos">Caramelos Gourmet</option>
                                <option value="fotografias">Servicios de Fotografía</option>
                                <option value="globos">Globos y Decoraciones</option>
                                <option value="tarjetas">Tarjetas y Papelería</option>
                                <option value="perfumes">Perfumes y Fragancias</option>
                                <option value="velas">Velas Aromáticas</option>
                                <option value="accesorios">Accesorios Florales</option>
                                <option value="macetas">Macetas y Contenedores</option>
                                <option value="fertilizantes">Fertilizantes y Nutrientes</option>
                                <option value="herramientas">Herramientas de Jardinería</option>
                                <option value="cestas">Cestas y Canastas</option>
                                <option value="lazos">Lazos y Cintas</option>
                                <option value="empaques">Materiales de Empaque</option>
                                <option value="preservantes">Preservantes Florales</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><i class="fas fa-phone me-1"></i>Teléfono</label>
                            <input type="tel" class="form-control" name="telefono_proveedor" 
                                   placeholder="Ej: +503 2234-5678">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><i class="fas fa-envelope me-1"></i>Email</label>
                            <input type="email" class="form-control" name="email_proveedor" 
                                   placeholder="contacto@proveedor.com">
                        </div>
                        <div class="col-12">
                            <label class="form-label"><i class="fas fa-map-marker-alt me-1"></i>Dirección</label>
                            <textarea class="form-control" name="direccion_proveedor" rows="2" 
                                      placeholder="Dirección completa del proveedor..."></textarea>
                        </div>
                    </div>
                    
                    <div class="mt-4 d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>Cancelar
                        </button>
                        <button type="submit" class="btn btn-info">
                            <i class="fas fa-truck me-1"></i>Agregar Proveedor
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Función para abrir modal de nuevo producto
function abrirproducto() {
    console.log('🔧 Función abrirproducto() llamada');
    
    try {
        const modalElement = document.getElementById('modal-nuevo-producto');
        console.log('🔍 Modal element:', modalElement);
        
        if (!modalElement) {
            console.error('❌ Modal no encontrado');
            alert('Error: Modal no encontrado');
            return;
        }
        
        console.log('📋 Bootstrap object:', typeof bootstrap);
        console.log('🎭 Bootstrap.Modal:', typeof bootstrap?.Modal);
        
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            console.log('✅ Creando modal...');
            const modal = new bootstrap.Modal(modalElement);
            modal.show();
            console.log('🎉 Modal mostrado exitosamente');
        } else {
            console.error('❌ Bootstrap no está disponible');
            // Fallback: usar jQuery si está disponible
            if (typeof $ !== 'undefined') {
                console.log('🔄 Usando jQuery como fallback');
                $(modalElement).modal('show');
            } else {
                console.log('🔄 Usando método alternativo manual');
                abrirModalAlternativo();
            }
        }
    } catch (error) {
        console.error('💥 Error al abrir modal:', error);
        alert('Error al abrir el modal: ' + error.message);
    }
}

// Función alternativa para abrir modal (fallback)
function abrirModalAlternativo() {
    console.log('🔄 Usando método alternativo para abrir modal');
    const modalElement = document.getElementById('modal-nuevo-producto');
    if (modalElement) {
        modalElement.style.display = 'block';
        modalElement.classList.add('show');
        modalElement.style.backgroundColor = 'rgba(0,0,0,0.5)';
        
        // Agregar evento para cerrar modal
        const closeButtons = modalElement.querySelectorAll('[data-bs-dismiss="modal"], .btn-secondary');
        closeButtons.forEach(btn => {
            btn.onclick = function() {
                modalElement.style.display = 'none';
                modalElement.classList.remove('show');
            };
        });
    }
}

// Verificar al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    console.log('📄 Página cargada');
    console.log('🔍 Bootstrap disponible:', typeof bootstrap !== 'undefined');
    console.log('🔍 jQuery disponible:', typeof $ !== 'undefined');
    
    // Agregar listener alternativo al botón
    const btnNuevoProducto = document.getElementById('btn-nuevo-producto');
    if (btnNuevoProducto) {
        console.log('✅ Botón encontrado, agregando listener adicional');
        btnNuevoProducto.addEventListener('click', function(e) {
            console.log('🖱️ Click detectado en botón');
            e.preventDefault();
            abrirproducto();
        });
    }
});

// Función para abrir modal de proveedores
function abrirproveedor() {
    try {
        const modalElement = document.getElementById('modal-proveedores');
        if (!modalElement) {
            alert('Error: Modal de proveedores no encontrado');
            return;
        }
        
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            const modal = new bootstrap.Modal(modalElement);
            modal.show();
        } else {
            alert('Error: Bootstrap no está cargado correctamente');
        }
    } catch (error) {
        console.error('Error al abrir modal:', error);
        alert('Error al abrir el modal de proveedores');
    }
}

/ Función para cambiar elementos por página (con fallback robusto)
function cambiarElementosPorPagina(nuevoValor) {
    console.log('Cambiando elementos por página a:', nuevoValor);
    
    // Verificar si AJAX está disponible
    const productContainer = document.getElementById('productListContainer');
    const ajaxDisponible = productContainer && 
                          typeof actualizarListado === 'function' && 
                          window.navigator.onLine !== false;
    
    if (ajaxDisponible) {
        console.log('Intentando cambio vía AJAX...');
        currentLimit = parseInt(nuevoValor);
        currentPage = 1; // Resetear a primera página
        
        try {
            actualizarListado();
            return;
        } catch (error) {
            console.error('Error en AJAX, usando recarga tradicional:', error);
        }
    }
    
    // Fallback: recarga tradicional (siempre funciona)
    console.log('Usando recarga tradicional para cambio de límite');
    const urlParams = new URLSearchParams(window.location.search);
    urlParams.set('per_page', nuevoValor);
    urlParams.set('pagina', 1); // Siempre volver a la primera página
    
    // Mantener filtros existentes
    const buscar = document.querySelector('input[name="buscar"]');
    if (buscar && buscar.value) {
        urlParams.set('buscar', buscar.value);
    }
    
    const categoria = document.querySelector('select[name="categoria"]');
    if (categoria && categoria.value) {
        urlParams.set('categoria', categoria.value);
    }
    
    // Mantener el parámetro 'ctrl' si existe
    if (!urlParams.has('ctrl')) {
        urlParams.set('ctrl', 'cinventario');
    }
    
    console.log('Redirigiendo con nuevo límite a:', '?' + urlParams.toString());
    window.location.href = '?' + urlParams.toString();
}

// Función para abrir parámetros de inventario
function abrirParametros() {
    // Crear modal dinámico para parámetros
    const modalHTML = `
        <div class="modal fade" id="modal-parametros" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-secondary text-white">
                        <h5 class="modal-title"><i class="fas fa-cog me-2"></i>Configuración de Inventario</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" action="?ctrl=cinventario" id="form-parametros">
                            <input type="hidden" name="accion" value="actualizar_parametros">
                            
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label"><i class="fas fa-exclamation-triangle me-1"></i>Stock Mínimo para Alerta</label>
                                    <input type="number" class="form-control" name="stock_minimo" value="20" min="1" max="100">
                                    <small class="text-muted">Productos con stock menor a este número aparecerán como "Stock Bajo"</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label"><i class="fas fa-calendar-alt me-1"></i>Días para Alerta de Vencimiento</label>
                                    <input type="number" class="form-control" name="dias_vencimiento" value="30" min="1" max="365">
                                    <small class="text-muted">Días de anticipación para alertas de vencimiento</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label"><i class="fas fa-money-bill me-1"></i>Moneda</label>
                                    <select class="form-select" name="moneda">
                                        <option value="USD">Dólares (USD)</option>
                                        <option value="EUR">Euros (EUR)</option>
                                        <option value="GTQ">Quetzales (GTQ)</option>
                                        <option value="HNL">Lempiras (HNL)</option>
                                        <option value="NIO">Córdobas (NIO)</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label"><i class="fas fa-percent me-1"></i>IVA (%)</label>
                                    <input type="number" step="0.01" class="form-control" name="iva_porcentaje" value="13.00" min="0" max="100">
                                    <small class="text-muted">Porcentaje de IVA aplicado a los precios</small>
                                </div>
                                <div class="col-12">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="alertas_email" id="alertas_email" checked>
                                        <label class="form-check-label" for="alertas_email">
                                            <i class="fas fa-envelope me-1"></i>Enviar alertas por email
                                        </label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i>
                                        <strong>Nota:</strong> Estos parámetros afectarán el cálculo de alertas y reportes en todo el sistema.
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-4 d-flex justify-content-end gap-2">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                    <i class="fas fa-times me-1"></i>Cancelar
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i>Guardar Configuración
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Remover modal anterior si existe
    const existingModal = document.getElementById('modal-parametros');
    if (existingModal) {
        existingModal.remove();
    }
    
    // Agregar y mostrar nuevo modal
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    
    try {
        const modalElement = document.getElementById('modal-parametros');
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            const modal = new bootstrap.Modal(modalElement);
            modal.show();
        } else {
            alert('Error: Bootstrap no está cargado correctamente');
        }
    } catch (error) {
        console.error('Error al mostrar modal:', error);
        alert('Error al mostrar el modal de parámetros');
    }
}

// Función para generar reportes específicos - MOVIDO A VISTA DE REPORTES

// Función para mostrar alertas de stock
function verificarStockBajo() {
    const stockBajo = <?= $stock_bajo ?? 0 ?>;
    const sinStock = <?= $sin_stock ?? 0 ?>;
    
    if (stockBajo > 0 || sinStock > 0) {
        let mensaje = '';
        if (sinStock > 0) {
            mensaje += `⚠️ ${sinStock} producto(s) sin stock\n`;
        }
        if (stockBajo > 0) {
            mensaje += `⚠️ ${stockBajo} producto(s) con stock bajo\n`;
        }
        mensaje += '\nPuedes revisar estos productos en la sección de reportes.';
        
        alert(mensaje);
    }
}

// Ejecutar verificación al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    // Verificar stock bajo al cargar
    setTimeout(verificarStockBajo, 2000);
    
    // Configurar búsqueda de flores
    const buscarFlor = document.getElementById('buscar-flor');
    if (buscarFlor) {
        buscarFlor.addEventListener('input', function() {
            const busqueda = this.value.toLowerCase();
            const filas = document.querySelectorAll('#tabla-flores tr');
            
            filas.forEach(function(fila) {
                const texto = fila.textContent.toLowerCase();
                fila.style.display = texto.includes(busqueda) ? '' : 'none';
            });
        });
    }
});

// Función para gestionar flores
function gestionarFlores() {
    try {
        const modalElement = document.getElementById('modal-gestion-flores');
        if (!modalElement) {
            alert('Error: Modal de gestión de flores no encontrado');
            return;
        }
        
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            const modal = new bootstrap.Modal(modalElement);
            modal.show();
        } else {
            alert('Error: Bootstrap no está cargado correctamente');
        }
    } catch (error) {
        console.error('Error al abrir modal:', error);
        alert('Error al abrir el modal de gestión de flores');
    }
}

// Editar flor inline
function editarFlor(florData) {
    // Cambiar a la pestaña de nueva flor usando trigger
    const nuevaTab = document.getElementById('nueva-tab');
    if (nuevaTab) {
        nuevaTab.click();
    }
    
    // Esperar un poco para que se muestre la pestaña
    setTimeout(function() {
        // Rellenar el formulario con los datos existentes
        const nombreInput = document.querySelector('#form-nueva-flor input[name="nombre"]');
        const naturalezaSelect = document.querySelector('#form-nueva-flor select[name="naturaleza"]');
        const colorSelect = document.querySelector('#form-nueva-flor select[name="color"]');
        const descripcionTextarea = document.querySelector('#form-nueva-flor textarea[name="descripcion"]');
        
        if (nombreInput) nombreInput.value = florData.nombre || '';
        if (naturalezaSelect) naturalezaSelect.value = florData.naturaleza || '';
        if (colorSelect) colorSelect.value = florData.color || '';
        if (descripcionTextarea) descripcionTextarea.value = florData.descripcion || '';
        
        // Cambiar el formulario para edición
        const accionInput = document.querySelector('#form-nueva-flor input[name="accion"]');
        if (accionInput) {
            accionInput.value = 'editar_flor';
        }
        
        // Agregar campo ID si no existe  
        let idInput = document.querySelector('#form-nueva-flor input[name="idtflor"]');
        if (!idInput) {
            idInput = document.createElement('input');
            idInput.type = 'hidden';
            idInput.name = 'idtflor';
            const form = document.getElementById('form-nueva-flor');
            if (form) form.appendChild(idInput);
        }
        if (idInput) idInput.value = florData.idtflor;
        
        // Cambiar texto del botón
        const submitBtn = document.querySelector('#form-nueva-flor button[type="submit"]');
        if (submitBtn) {
            submitBtn.innerHTML = '<i class="fas fa-save me-1"></i>Actualizar Flor';
            submitBtn.className = 'btn btn-primary';
        }
        
        // Agregar botón para cancelar edición
        if (!document.getElementById('cancelar-edicion')) {
            const cancelBtn = document.createElement('button');
            cancelBtn.type = 'button';
            cancelBtn.className = 'btn btn-secondary';
            cancelBtn.id = 'cancelar-edicion';
            cancelBtn.innerHTML = '<i class="fas fa-times me-1"></i>Cancelar Edición';
            cancelBtn.onclick = cancelarEdicion;
            
            const buttonGroup = document.querySelector('#form-nueva-flor .mt-4 .gap-2');
            if (buttonGroup) {
                buttonGroup.insertBefore(cancelBtn, buttonGroup.firstChild);
            }
        }
    }, 100);
}

// Cancelar edición
function cancelarEdicion() {
    // Limpiar formulario
    const form = document.getElementById('form-nueva-flor');
    if (form) {
        form.reset();
    }
    
    // Restaurar acción
    const accionInput = document.querySelector('#form-nueva-flor input[name="accion"]');
    if (accionInput) {
        accionInput.value = 'nueva_flor';
    }
    
    // Remover campo ID
    const idInput = document.querySelector('#form-nueva-flor input[name="idtflor"]');
    if (idInput) {
        idInput.remove();
    }
    
    // Restaurar botón
    const submitBtn = document.querySelector('#form-nueva-flor button[type="submit"]');
    if (submitBtn) {
        submitBtn.innerHTML = '<i class="fas fa-seedling me-1"></i>Crear Flor';
        submitBtn.className = 'btn btn-warning';
    }
    
    // Remover botón cancelar
    const cancelBtn = document.getElementById('cancelar-edicion');
    if (cancelBtn) {
        cancelBtn.remove();
    }
}

// Agregar flor al inventario
function agregarAInventario(idFlor, nombreFlor) {
    if (!idFlor || !nombreFlor) {
        alert('Error: Datos de flor inválidos');
        return;
    }
    
    if (confirm('¿Deseas agregar "' + nombreFlor + '" al inventario?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.style.display = 'none';
        
        const accionInput = document.createElement('input');
        accionInput.name = 'accion';
        accionInput.value = 'agregar_a_inventario';
        
        const idInput = document.createElement('input');
        idInput.name = 'id_flor';
        idInput.value = idFlor;
        
        form.appendChild(accionInput);
        form.appendChild(idInput);
        document.body.appendChild(form);
        form.submit();
    }
}

// Eliminar flor
function eliminarFlor(idFlor, nombreFlor) {
    if (!idFlor || !nombreFlor) {
        alert('Error: Datos de flor inválidos');
        return;
    }
    
    if (confirm('¿Estás seguro de que deseas eliminar "' + nombreFlor + '"?\n\nEsta acción no se puede deshacer.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.style.display = 'none';
        
        const accionInput = document.createElement('input');
        accionInput.name = 'accion';
        accionInput.value = 'eliminar_flor';
        
        const idInput = document.createElement('input');
        idInput.name = 'id_flor';
        idInput.value = idFlor;
        
        form.appendChild(accionInput);
        form.appendChild(idInput);
        document.body.appendChild(form);
        form.submit();
    }
}

// Limpiar formulario de nueva flor
function limpiarFormularioFlor() {
    const form = document.getElementById('form-nueva-flor');
    if (form) {
        form.reset();
    }
    cancelarEdicion();
}

// ==================== FUNCIONES DE PAGINACIÓN AJAX ====================

// Variables globales para paginación
let currentPage = <?= $pagina_actual ?? 1 ?>;
let currentLimit = <?= $elementos_por_pagina ?? 10 ?>;
let currentFilters = {};
let loadingTimeout;

// Función para detener la carga manualmente
function detenerCarga() {
    const loadingIndicator = document.getElementById('loadingIndicator');
    const productContainer = document.getElementById('productListContainer');
    const stopBtn = document.getElementById('stopLoadingBtn');
    
    if (loadingIndicator) {
        loadingIndicator.style.display = 'none';
        loadingIndicator.style.visibility = 'hidden';
    }
    if (productContainer) {
        productContainer.style.opacity = '1';
    }
    if (stopBtn) {
        stopBtn.style.display = 'none';
    }
    
    if (loadingTimeout) {
        clearTimeout(loadingTimeout);
    }
    
    console.log('Carga detenida manualmente');
}

// Función para ocultar el loading al cargar la página
function ocultarLoadingInicial() {
    const loadingIndicator = document.getElementById('loadingIndicator');
    const productContainer = document.getElementById('productListContainer');
    const stopBtn = document.getElementById('stopLoadingBtn');
    
    if (loadingIndicator) {
        loadingIndicator.style.display = 'none';
        loadingIndicator.style.visibility = 'hidden';
    }
    if (productContainer) {
        productContainer.style.opacity = '1';
    }
    if (stopBtn) {
        stopBtn.style.display = 'none';
    }
    
    // También ocultar cualquier alerta de error
    const errorAlert = document.querySelector('.alert-danger');
    if (errorAlert) {
        errorAlert.style.display = 'none';
    }
}

// Función para cargar página específica (con fallback robusto)
function cargarPagina(page) {
    console.log('=== cargarPagina llamada con página:', page, '===');
    if (page < 1) {
        console.log('Página inválida:', page);
        return;
    }
    
    // Verificar si AJAX está disponible y funcionando
    const productContainer = document.getElementById('productListContainer');
    const ajaxDisponible = productContainer && 
                          typeof fetch !== 'undefined' && 
                          window.navigator.onLine !== false;
    
    if (ajaxDisponible) {
        console.log('Intentando cargar vía AJAX...');
        currentPage = page;
        
        try {
            actualizarListado();
        } catch (error) {
            console.error('Error en AJAX, usando recarga tradicional:', error);
            recargarPaginaTradicional(page);
        }
    } else {
        console.log('AJAX no disponible, usando recarga tradicional');
        recargarPaginaTradicional(page);
    }
}

// Función para recarga tradicional (siempre funciona)
function recargarPaginaTradicional(page) {
    const url = new URL(window.location);
    url.searchParams.set('pagina', page);
    
    // Mantener filtros existentes
    const buscar = document.querySelector('input[name="buscar"]');
    if (buscar && buscar.value) {
        url.searchParams.set('buscar', buscar.value);
    }
    
    const categoria = document.querySelector('select[name="categoria"]');
    if (categoria && categoria.value) {
        url.searchParams.set('categoria', categoria.value);
    }
    
    // Mantener el controlador
    if (!url.searchParams.has('ctrl')) {
        url.searchParams.set('ctrl', 'cinventario');
    }
    
    console.log('Redirigiendo a:', url.toString());
    window.location.href = url.toString();
}

// Función para cambiar límite de elementos
function cambiarLimite() {
    const limitSelect = document.getElementById('itemsPerPage');
    if (limitSelect) {
        currentLimit = limitSelect.value;
        currentPage = 1; // Resetear a primera página
        
        try {
            actualizarListado();
        } catch (error) {
            console.error('Error en AJAX, usando fallback tradicional:', error);
            // Fallback: recargar página con nuevo límite
            const url = new URL(window.location);
            url.searchParams.set('per_page', currentLimit);
            url.searchParams.set('pagina', 1);
            window.location.href = url.toString();
        }
    }
}

// Función para recargar listado
function recargarListado() {
    try {
        actualizarListado();
    } catch (error) {
        console.error('Error en AJAX, recargando página:', error);
        window.location.reload();
    }
}

// Función principal para actualizar listado vía AJAX
function actualizarListado() {
    console.log('=== INICIO actualizarListado ===');
    
    // Mostrar indicador de carga
    const loadingIndicator = document.getElementById('loadingIndicator');
    const productContainer = document.getElementById('productListContainer');
    const stopBtn = document.getElementById('stopLoadingBtn');
    
    if (loadingIndicator) {
        loadingIndicator.style.display = 'flex';
        loadingIndicator.style.visibility = 'visible';
    }
    if (productContainer) {
        productContainer.style.opacity = '0.6';
    }
    if (stopBtn) {
        stopBtn.style.display = 'inline-block';
    }
    
    // Timeout de seguridad para evitar carga infinita
    loadingTimeout = setTimeout(() => {
        console.warn('Timeout: La carga tardó más de 10 segundos');
        detenerCarga();
        mostrarError('La carga tardó demasiado. Intenta nuevamente.');
    }, 10000);
    
    // Obtener filtros actuales del formulario
    const params = new URLSearchParams();
    params.append('action', 'getListado');
    params.append('page', currentPage);
    params.append('limit', currentLimit);
    
    // Agregar filtros si existen
    const buscarInput = document.querySelector('input[name="buscar"]');
    if (buscarInput && buscarInput.value) {
        params.append('buscar', buscarInput.value);
    }
    
    const categoriaSelect = document.querySelector('select[name="categoria"]');
    if (categoriaSelect && categoriaSelect.value && categoriaSelect.value !== '') {
        params.append('categoria', categoriaSelect.value);
    }
    
    const estadoStockSelect = document.querySelector('select[name="estado_stock"]');
    if (estadoStockSelect && estadoStockSelect.value) {
        params.append('estado_stock', estadoStockSelect.value);
    }
    
    // Realizar petición AJAX
    const url = 'controllers/InventarioApiController.php?' + params.toString();
    console.log('Solicitando:', url);
    
    fetch(url)
    .then(response => {
        console.log('Respuesta recibida:', response.status, response.statusText);
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        return response.text(); // Primero obtener como texto para debug
    })
    .then(text => {
        clearTimeout(loadingTimeout);
        console.log('Respuesta cruda (primeros 200 chars):', text.substring(0, 200));
        
        try {
            const data = JSON.parse(text);
            console.log('Datos parseados:', data);
            
            if (data.success) {
                // Reemplazar todo el contenedor de la tabla con el nuevo HTML
                if (productContainer) {
                    productContainer.innerHTML = data.html;
                    productContainer.style.opacity = '1';
                }
                
                // Actualizar el contenedor de paginación también si está fuera de la tabla
                const paginationContainer = document.getElementById('paginationContainer');
                if (paginationContainer) {
                    // Buscar la nueva paginación en el HTML recibido
                    const tempDiv = document.createElement('div');
                    tempDiv.innerHTML = data.html;
                    const newPagination = tempDiv.querySelector('#paginationContainer');
                    if (newPagination) {
                        paginationContainer.innerHTML = newPagination.innerHTML;
                    }
                }
                
                // Actualizar variables globales
                currentPage = data.pagination.current_page;
                currentLimit = data.pagination.per_page;
                
                // Actualizar selector de elementos por página
                const limitSelect = document.getElementById('itemsPerPage');
                if (limitSelect && limitSelect.value != data.pagination.per_page) {
                    limitSelect.value = data.pagination.per_page;
                }
                
                // Scroll suave hacia arriba del listado
                const listadoSection = document.querySelector('.table-responsive');
                if (listadoSection) {
                    listadoSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
                
                console.log('Listado actualizado exitosamente');
                
            } else {
                console.error('Error en la respuesta:', data.error);
                mostrarError('Error al cargar el listado: ' + (data.error || 'Error desconocido'));
            }
        } catch (parseError) {
            console.error('Error al parsear JSON:', parseError);
            console.error('Texto recibido completo:', text);
            mostrarError('Error en el formato de respuesta del servidor. Revisa la consola para más detalles.');
        }
    })
    .catch(error => {
        clearTimeout(loadingTimeout);
        console.error('Error en la petición:', error);
        mostrarError('Error de conexión: ' + error.message);
    })
    .finally(() => {
        // Ocultar indicador de carga
        if (loadingIndicator) {
            loadingIndicator.style.display = 'none';
            loadingIndicator.style.visibility = 'hidden';
        }
        if (productContainer) {
            productContainer.style.opacity = '1';
        }
        if (stopBtn) {
            stopBtn.style.display = 'none';
        }
        console.log('=== FIN actualizarListado ===');
    });
}

// Función para actualizar información de paginación en la UI
function actualizarInfoPaginacion(pagination) {
    // Actualizar selector de elementos por página
    const limitSelect = document.getElementById('itemsPerPage');
    if (limitSelect && limitSelect.value != pagination.per_page) {
        limitSelect.value = pagination.per_page;
    }
    
    currentPage = pagination.current_page;
    currentLimit = pagination.per_page;
}

// Función para mostrar errores
function mostrarError(mensaje) {
    // Crear o actualizar alerta de error
    let alertContainer = document.getElementById('ajax-alert-container');
    if (!alertContainer) {
        alertContainer = document.createElement('div');
        alertContainer.id = 'ajax-alert-container';
        alertContainer.style.position = 'fixed';
        alertContainer.style.top = '20px';
        alertContainer.style.right = '20px';
        alertContainer.style.zIndex = '9999';
        document.body.appendChild(alertContainer);
    }
    
    alertContainer.innerHTML = `
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            ${mensaje}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    // Auto-dismiss after 5 seconds
    setTimeout(() => {
        const alert = alertContainer.querySelector('.alert');
        if (alert) {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }
    }, 5000);
}

// Función para aplicar filtros y actualizar listado
function aplicarFiltros() {
    currentPage = 1; // Resetear a primera página cuando se aplican filtros
    actualizarListado();
}

// Event listeners para formulario de filtros
document.addEventListener('DOMContentLoaded', function() {
    console.log('=== INICIALIZANDO INVENTARIO ===');
    console.log('Página actual:', currentPage, 'Límite:', currentLimit);
    
    // Ocultar loading inicial
    ocultarLoadingInicial();
    
    // Interceptar envío del formulario de filtros
    const formFiltros = document.querySelector('form[method="GET"]');
    if (formFiltros) {
        console.log('Form de filtros encontrado');
        formFiltros.addEventListener('submit', function(e) {
            e.preventDefault();
            console.log('Form enviado - aplicando filtros');
            aplicarFiltros();
        });
    }
    
    // Auto-filtrado en tiempo real para el campo de búsqueda
    const buscarInput = document.querySelector('input[name="buscar"]');
    if (buscarInput) {
        console.log('Input de búsqueda encontrado');
        let timeout;
        buscarInput.addEventListener('input', function() {
            clearTimeout(timeout);
            timeout = setTimeout(() => {
                console.log('Aplicando filtro de búsqueda:', this.value);
                aplicarFiltros();
            }, 500); // Esperar 500ms después de dejar de escribir
        });
    }
    
    // Auto-filtrado para selects
    const selectores = document.querySelectorAll('select[name="categoria"], select[name="estado_stock"]');
    selectores.forEach(select => {
        console.log('Select encontrado:', select.name);
        select.addEventListener('change', function() {
            console.log('Select cambió:', this.name, '=', this.value);
            aplicarFiltros();
        });
    });
    
    // Interceptar clicks en enlaces de paginación existentes
    document.addEventListener('click', function(e) {
        if (e.target.closest('.pagination .page-link')) {
            e.preventDefault();
            const link = e.target.closest('.page-link');
            const onClick = link.getAttribute('onclick');
            
            console.log('Click en paginación detectado:', onClick);
            
            if (onClick && onClick.includes('cargarPagina')) {
                // El onclick ya maneja la lógica
                return;
            }
            
            // Fallback para enlaces sin onclick
            const href = link.getAttribute('href');
            if (href && href.includes('pagina=')) {
                const pageMatch = href.match(/pagina=(\d+)/);
                if (pageMatch) {
                    console.log('Cargando página via href:', pageMatch[1]);
                    cargarPagina(parseInt(pageMatch[1]));
                }
            }
        }
    });
    
    console.log('=== INICIALIZACIÓN COMPLETA ===');
});
});

// Función para cambiar tipo de producto
function cambiarTipoProducto() {
    const tipoProducto = document.getElementById('tipo_producto').value;
    const seccionFlor = document.getElementById('seccion_flor');
    const florSelect = document.getElementById('flor_select');
    
    if (tipoProducto === 'flor') {
        seccionFlor.style.display = 'block';
        florSelect.removeAttribute('required');
    } else {
        seccionFlor.style.display = 'none';
        florSelect.value = '';
        florSelect.removeAttribute('required');
    }
    
    // Actualizar placeholder del nombre según el tipo
    const nombreInput = document.querySelector('input[name="nombre_producto"]');
    const ejemplos = {
        'flor': 'Ej: Rosa Roja Premium, Tulipán Holandés',
        'chocolate': 'Ej: Ferrero Rocher, Chocolate Godiva',
        'tarjeta': 'Ej: Tarjeta de Amor, Tarjeta de Cumpleaños',
        'peluche': 'Ej: Osito de Peluche, Peluche Unicornio',
        'globo': 'Ej: Globo Corazón, Globo Número',
        'accesorio': 'Ej: Lazo Decorativo, Papel de Regalo',
        'otro': 'Ej: Vela Aromática, Mini Jarrón'
    };
    
    if (nombreInput && ejemplos[tipoProducto]) {
        nombreInput.placeholder = ejemplos[tipoProducto];
    }
}

// Función bonus para exportar inventario
function exportarInventario() {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = 'controllers/ExportarInventarioController.php';
    form.style.display = 'none';
    
    const actionInput = document.createElement('input');
    actionInput.name = 'action';
    actionInput.value = 'exportar_excel';
    
    form.appendChild(actionInput);
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
}
</script>

</main>
<!-- Fin de la vista de inventario -->