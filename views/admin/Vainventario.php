<!-- FontAwesome 6.5.2 CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-+Cf+8J2k6U5zQ6QwQ6Q6Q6Q6Q6Q6Q6Q6Q6Q6Q6Q6Q6Q6Q6Q6Q6Q6Q6Q6Q6Q6Q6Q6Q6Q6Q6Q6Q6Q6Q==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<!-- Gestión de Inventario - Vista -->
<main class="container-fluid py-4">
    <!-- Backdrop para modales manuales -->
<div id="modal-backdrop" class="modal-backdrop fade" style="display:none;z-index:1040;"></div>
    <h2 class="mb-4 fw-bold">Gestión de Inventario</h2> 
    <!-- Cards de resumen funcionales -->
        <div class="row mb-4 g-3">
            <div class="col-md-3">
                <div class="card text-center border-0 shadow-sm">
                    <div class="card-body">
                        <i class="fas fa-layer-group fa-2x text-primary mb-2"></i>
                        <div class="fw-bold text-muted">Total Productos</div>
                        <div class="fs-3 fw-bold text-dark"><?= $total_productos ?? 0 ?></div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center border-0 shadow-sm">
                    <div class="card-body">
                        <i class="fas fa-exclamation-triangle fa-2x text-warning mb-2"></i>
                        <div class="fw-bold text-muted">Stock Bajo</div>
                        <div class="fs-3 fw-bold text-dark"><?= $stock_bajo ?? 0 ?></div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center border-0 shadow-sm">
                    <div class="card-body">
                        <i class="fas fa-times-circle fa-2x text-danger mb-2"></i>
                        <div class="fw-bold text-muted">Sin Stock</div>
                        <div class="fs-3 fw-bold text-dark"><?= $sin_stock ?? 0 ?></div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center border-0 shadow-sm">
                    <div class="card-body">
                        <i class="fas fa-dollar-sign fa-2x text-success mb-2"></i>
                        <div class="fw-bold text-muted">Valor Total</div>
                        <div class="fs-3 fw-bold text-dark">$<?= number_format($valor_total ?? 0, 2) ?></div>
                    </div>
                </div>
            </div>
    </div>

    <!-- Filtros funcionales -->
    <div class="card mb-4">
        <div class="card-body pb-2">
            <div class="fw-bold fs-5 mb-2"><i class="fas fa-filter me-2"></i>Filtros de Inventario</div>
            <form method="GET" action="index.php">
                <input type="hidden" name="ctrl" value="Cinventario">
                <input type="hidden" name="action" value="index">
                <div class="row justify-content-center g-2 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Categoría/Naturaleza</label>
                        <select name="categoria" class="form-select">
                            <option value="">Todas las categorías</option>
                            <option value="Natural" <?= ($_GET['categoria'] ?? '') == 'Natural' ? 'selected' : '' ?>>Natural</option>
                            <option value="Artificial" <?= ($_GET['categoria'] ?? '') == 'Artificial' ? 'selected' : '' ?>>Artificial</option>
                            <option value="Comestible" <?= ($_GET['categoria'] ?? '') == 'Comestible' ? 'selected' : '' ?>>Comestible</option>
                            <option value="Decorativo" <?= ($_GET['categoria'] ?? '') == 'Decorativo' ? 'selected' : '' ?>>Decorativo</option>
                            <option value="Regalo" <?= ($_GET['categoria'] ?? '') == 'Regalo' ? 'selected' : '' ?>>Regalo</option>
                            <option value="Accesorio" <?= ($_GET['categoria'] ?? '') == 'Accesorio' ? 'selected' : '' ?>>Accesorio</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Estado de Stock</label>
                        <select name="estado_stock" class="form-select">
                            <option value="">Todos los estados</option>
                            <option value="bajo" <?= ($_GET['estado_stock'] ?? '') == 'bajo' ? 'selected' : '' ?>>Stock Bajo</option>
                            <option value="sin_stock" <?= ($_GET['estado_stock'] ?? '') == 'sin_stock' ? 'selected' : '' ?>>Sin Stock</option>
                            <option value="normal" <?= ($_GET['estado_stock'] ?? '') == 'normal' ? 'selected' : '' ?>>Stock Normal</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Buscar</label>
                        <input type="text" name="buscar" class="form-control" placeholder="Nombre de la flor..." value="<?= htmlspecialchars($_GET['buscar'] ?? '') ?>">
                    </div>
                    <div class="col-md-3 d-flex gap-2 align-items-end">
                        <button type="submit" class="btn btn-success px-4">Filtrar</button>
                        <a href="?ctrl=Cinventario" class="btn btn-link text-secondary">Limpiar Filtros</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    
    <!-- Mensajes de éxito y error -->

    <!-- Botones de acción de inventario -->
    <div class="d-flex justify-content-center flex-wrap gap-2 mb-4">
        <button class="btn btn-success shadow-sm" onclick="abrirproducto(); return false;" id="btn-nuevo-producto">
            <i class="fas fa-plus me-2"></i>Nuevo Producto
        </button>
        <button class="btn btn-info shadow-sm" onclick="abrirproveedor(); return false;" id="btn-proveedores">
            <i class="fas fa-truck me-2"></i>Proveedores
        </button>
        <button class="btn btn-warning shadow-sm" 
                onclick="console.log('Botón configuración clickeado'); 
                         try { 
                             const modalEl = document.getElementById('modal-configuracion');
                             console.log('Modal element:', modalEl);
                             console.log('Bootstrap available:', typeof bootstrap);
                             if (modalEl && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                                 const modal = new bootstrap.Modal(modalEl); 
                                 modal.show(); 
                                 console.log('Modal mostrado');
                             } else {
                                 alert('Error: Bootstrap no disponible o modal no encontrado');
                             }
                         } catch(e) { 
                             console.error('Error:', e); 
                             alert('Error al abrir modal: ' + e.message); 
                         } 
                         return false;" 
                id="btn-configuracion">
            <i class="fas fa-cog me-2"></i>Configuración
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
            <button class="btn btn-outline-secondary btn-sm" 
                    onclick="console.log('Actualizando listado...'); 
                             try { 
                                 window.location.reload(); 
                                 console.log('Página recargada'); 
                             } catch(e) { 
                                 console.error('Error al actualizar:', e); 
                                 alert('Error al actualizar: ' + e.message); 
                             }" 
                    title="Actualizar listado">
                <i class="fas fa-sync-alt"></i> Actualizar
            </button>
            <button class="btn btn-outline-danger btn-sm" onclick="detenerCarga()" title="Detener carga" style="display: none;" id="stopLoadingBtn">
                <i class="fas fa-stop"></i> Detener
            </button>
            <button class="btn btn-outline-info btn-sm" id="btn-exportar" title="Exportar a Excel">
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
            <table class="table table-hover align-middle" id="tabla-inventario">
                <thead class="table-light">
                    <tr>
                        <th>Producto</th>
                        <th class="d-none d-md-table-cell">Naturaleza</th>
                        <th class="d-none d-lg-table-cell">Color</th>
                        <th>Stock</th>
                        <th class="d-none d-sm-table-cell">Estado</th>
                        <th class="d-none d-md-table-cell">Precio Unitario</th>
                        <th class="d-none d-lg-table-cell">Valor Total</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="inventarioTableBody">
                    <?php if (!empty($inventario)): ?>
                        <?php foreach ($inventario as $item): ?>
                            <tr>
                                <td class="fw-bold">
                                    <?php echo '<div>' . htmlspecialchars($item['producto']) . '</div>'; ?>
                                    <small class="text-muted d-md-none">
                                        <?php echo htmlspecialchars($item['naturaleza']); ?> 
                                        <span class="d-lg-none">- <?php echo htmlspecialchars($item['color']); ?></span>
                                    </small>
                                </td>
                                <td class="d-none d-md-table-cell"><?= htmlspecialchars($item['naturaleza']) ?></td>
                                <td class="d-none d-lg-table-cell"><?= htmlspecialchars($item['color']) ?></td>
                                <td>
                                    <span class="badge <?= $item['stock'] == 0 ? 'bg-danger' : ($item['stock'] < 20 ? 'bg-warning' : 'bg-success') ?>">
                                        <?= $item['stock'] ?>
                                    </span>
                                </td>
                                <td class="d-none d-sm-table-cell">
                                    <span class="fw-bold <?= $item['stock'] == 0 ? 'text-danger' : ($item['stock'] < 20 ? 'text-warning' : 'text-success') ?>">
                                        <?= $item['estado_stock'] ?>
                                    </span>
                                </td>
                                <td class="d-none d-md-table-cell">$<?= number_format($item['precio'] ?? 0, 2) ?></td>
                                <td class="d-none d-lg-table-cell">$<?= number_format(($item['stock'] ?? 0) * ($item['precio'] ?? 0), 2) ?></td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button type="button" 
                                                class="btn btn-warning btn-sm btn-modal-editar" 
                                                data-producto-id="<?= $item['idinv'] ?>" 
                                                data-producto-nombre="<?= htmlspecialchars($item['producto']) ?>"
                                                data-producto-naturaleza="<?= htmlspecialchars($item['naturaleza']) ?>"
                                                data-producto-color="<?= htmlspecialchars($item['color']) ?>"
                                                data-producto-stock="<?= $item['stock'] ?>"
                                                data-producto-precio="<?= $item['precio'] ?? 0 ?>"
                                                data-producto-estado="<?= $item['estado_stock'] ?>"
                                                title="Editar" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#modal-editar-producto">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" 
                                                class="btn btn-info btn-sm btn-modal-stock" 
                                                data-producto-id="<?= $item['idinv'] ?>" 
                                                data-producto-nombre="<?= htmlspecialchars($item['producto']) ?>"
                                                data-producto-stock="<?= $item['stock'] ?>"
                                                title="Agregar stock" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#modal-agregar-stock">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                        <button type="button" 
                                                class="btn btn-danger btn-sm btn-modal-eliminar" 
                                                data-producto-id="<?= $item['idinv'] ?>" 
                                                data-producto-nombre="<?= htmlspecialchars($item['producto']) ?>" 
                                                title="Eliminar producto" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#modal-eliminar-producto">
                                            <i class="fas fa-trash"></i>
                                        </button>
<!-- Modal Eliminar Producto (ubicado al final del archivo) -->
<div class="modal fade" id="modal-eliminar-producto" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fas fa-trash me-2"></i>Eliminar Producto</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="?ctrl=cinventario" id="form-eliminar-producto">
                    <input type="hidden" name="accion" value="eliminar_producto">
                    <input type="hidden" name="producto_id" id="eliminar_producto_id">
                    <p>¿Estás seguro que deseas eliminar el producto <span id="eliminar_nombre_producto" class="fw-bold text-danger"></span> del inventario?</p>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Cancelar
                </button>
                <button type="submit" form="form-eliminar-producto" class="btn btn-danger">
                    <i class="fas fa-trash me-1"></i>Eliminar
                </button>
            </div>
        </div>
    </div>
</div>
                                    </div>
                                </td>
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
    <!-- Tabla de Proveedores -->
    <div class="card mt-5">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0"><i class="fas fa-truck me-2"></i>Proveedores Registrados</h5>
        </div>
        <div class="card-body p-0">
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
                                    <td>
                                        <?php if ($prov['estado'] === 'activo'): ?>
                                            <span class="badge bg-success">Activo</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Inactivo</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <!-- Acciones: editar, eliminar, ver más -->
                                        <button class="btn btn-sm btn-warning" title="Editar"><i class="fas fa-edit"></i></button>
                                        <button class="btn btn-sm btn-danger" title="Eliminar"><i class="fas fa-trash"></i></button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    <i class="fas fa-truck" style="font-size:2rem;"></i>
                                    <h6 class="mt-2">No hay proveedores registrados</h6>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
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
<!-- Cargar Bootstrap JS para modales -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</main>

<script src="/Original-Floraltech/assets/inventario.js"></script>
<script src="/Original-Floraltech/assets/inventario_modal_handler.js"></script>
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
                <form method="POST" action="?ctrl=Cinventario" id="form-nuevo-producto">
                    <input type="hidden" name="accion" value="nuevo_producto">
                    
                    <div class="row g-3">
                        <!-- Proveedor -->
                        <div class="col-md-6">
                            <label class="form-label"><i class="fas fa-truck me-1"></i>Proveedor</label>
                            <div class="input-group">
                                <select class="form-select" name="proveedor_id" id="editar_proveedor_id">
                                    <option value="">Selecciona proveedor...</option>
                                    <?php if (!empty($proveedores)): ?>
                                        <?php foreach ($proveedores as $prov): ?>
                                            <option value="<?= htmlspecialchars($prov['idproveedor']) ?>"><?= htmlspecialchars($prov['nombre']) ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                                <button type="button" class="btn btn-outline-info" onclick="abrirproveedor(); return false;">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                            <small class="text-muted">Puedes seleccionar un proveedor existente o agregar uno nuevo.</small>
                        </div>
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

</div>

<div class="modal fade" id="modal-proveedores" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title"><i class="fas fa-truck me-2"></i>Nuevo Proveedor</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="?ctrl=Cinventario" id="form-nuevo-proveedor">
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
                        <!-- NUEVO: Productos que provee -->
                        <div class="col-12">
                            <label class="form-label"><i class="fas fa-boxes me-1"></i>Productos que provee</label>
                            <select class="form-select" name="productos_proveedor[]" multiple>
                                <?php if (!empty($productos_inventario)): ?>
                                    <?php foreach ($productos_inventario as $prod): ?>
                                        <option value="<?= htmlspecialchars($prod['idinv']) ?>">
                                            <?= htmlspecialchars($prod['producto']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <option value="">No hay productos registrados</option>
                                <?php endif; ?>
                            </select>
                            <small class="text-muted">Mantén presionada Ctrl (Windows) o Cmd (Mac) para seleccionar varios.</small>
                        </div>
                        <!-- NUEVO: Notas o comentarios -->
                        <div class="col-12">
                            <label class="form-label"><i class="fas fa-sticky-note me-1"></i>Notas / Comentarios</label>
                            <textarea class="form-control" name="notas_proveedor" rows="2" placeholder="Observaciones, condiciones especiales, etc."></textarea>
                        </div>
                        <!-- NUEVO: Estado del proveedor -->
                        <div class="col-12">
                            <label class="form-label"><i class="fas fa-toggle-on me-1"></i>Estado</label>
                            <select class="form-select" name="estado_proveedor">
                                <option value="activo" selected>Activo</option>
                                <option value="inactivo">Inactivo</option>
                            </select>
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

<!-- Modal Editar Producto -->
<div class="modal fade" id="modal-editar-producto" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Editar Producto del Inventario</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="?ctrl=cinventario" id="form-editar-producto">
                    <input type="hidden" name="accion" value="editar_producto">
                    <input type="hidden" name="producto_id" id="editar_producto_id">
                    
                    <div class="row g-3">
                        <!-- Nombre del Producto -->
                        <div class="col-md-6">
                            <label class="form-label"><i class="fas fa-tag me-1"></i>Nombre del Producto *</label>
                            <input type="text" class="form-control" name="nombre_producto" id="editar_nombre_producto" required>
                        </div>
                        
                        <!-- Tipo de Producto -->
                        <div class="col-md-6">
                            <label class="form-label"><i class="fas fa-layer-group me-1"></i>Tipo de Producto *</label>
                            <select class="form-select" name="tipo_producto" id="editar_tipo_producto" required>
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
                        
                        <!-- Stock Actual -->
                        <div class="col-md-4">
                            <label class="form-label"><i class="fas fa-boxes me-1"></i>Stock Actual</label>
                            <input type="number" class="form-control" name="stock" id="editar_stock" min="0" required>
                        </div>
                        
                        <!-- Precio Unitario -->
                        <div class="col-md-4">
                            <label class="form-label"><i class="fas fa-dollar-sign me-1"></i>Precio Unitario</label>
                            <input type="number" class="form-control" name="precio" id="editar_precio" min="0" step="0.01" required>
                        </div>
                        
                        <!-- Color -->
                        <div class="col-md-4">
                            <label class="form-label"><i class="fas fa-palette me-1"></i>Color</label>
                            <input type="text" class="form-control" name="color" id="editar_color" placeholder="Ej: Rojo, Azul">
                        </div>
                        
                        <!-- Naturaleza -->
                        <div class="col-md-6">
                            <label class="form-label"><i class="fas fa-seedling me-1"></i>Naturaleza</label>
                            <select class="form-select" name="naturaleza" id="editar_naturaleza">
                                <option value="">Seleccionar...</option>
                                <option value="Natural">Natural</option>
                                <option value="Artificial">Artificial</option>
                                <option value="Mixto">Mixto</option>
                                <option value="No aplica">No aplica</option>
                            </select>
                        </div>
                        
                        <!-- Estado -->
                        <div class="col-md-6">
                            <label class="form-label"><i class="fas fa-info-circle me-1"></i>Estado</label>
                            <select class="form-select" name="estado" id="editar_estado">
                                <option value="Disponible">Disponible</option>
                                <option value="Agotado">Agotado</option>
                                <option value="Descontinuado">Descontinuado</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Cancelar
                </button>
                <button type="submit" form="form-editar-producto" class="btn btn-warning">
                    <i class="fas fa-save me-1"></i>Guardar Cambios
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Agregar Stock -->
<div class="modal fade" id="modal-agregar-stock" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title"><i class="fas fa-plus me-2"></i>Agregar Stock al Inventario</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="?ctrl=cinventario" id="form-agregar-stock">
                    <input type="hidden" name="accion" value="agregar_stock">
                    <input type="hidden" name="producto_id" id="stock_producto_id">
                    
                    <div class="text-center mb-4">
                        <h6>Producto: <span id="stock_nombre_producto" class="fw-bold text-info"></span></h6>
                        <p class="text-muted">Stock actual: <span id="stock_actual" class="badge bg-secondary"></span></p>
                    </div>
                    
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label"><i class="fas fa-plus me-1"></i>Cantidad a Agregar *</label>
                            <input type="number" class="form-control" name="cantidad" id="cantidad_agregar" min="1" required>
                            <small class="text-muted">Ingresa la cantidad de unidades que deseas agregar al inventario</small>
                        </div>
                        
                        <div class="col-12">
                            <label class="form-label"><i class="fas fa-comment me-1"></i>Motivo (Opcional)</label>
                            <textarea class="form-control" name="motivo" rows="2" placeholder="Ej: Reposición, Compra nueva, Devolución..."></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Cancelar
                </button>
                <button type="submit" form="form-agregar-stock" class="btn btn-info">
                    <i class="fas fa-plus me-1"></i>Agregar Stock
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Configuración de Parámetros -->
<div class="modal fade" id="modal-configuracion" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title"><i class="fas fa-cog me-2"></i>Configuración de Parámetros de Inventario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="?ctrl=Cinventario" id="form-configuracion">
                    <input type="hidden" name="accion" value="actualizar_parametros">
                    
                    <div class="row g-3">
                        <div class="col-12">
                            <h6 class="text-muted"><i class="fas fa-exclamation-triangle me-2"></i>Niveles de Alerta de Stock</h6>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label"><i class="fas fa-arrow-down me-1"></i>Stock Mínimo *</label>
                            <input type="number" class="form-control" name="stock_minimo" min="0" value="10" required 
                                   placeholder="Cantidad mínima antes de alertar">
                            <small class="text-muted">Nivel crítico de stock</small>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label"><i class="fas fa-arrow-up me-1"></i>Stock Máximo</label>
                            <input type="number" class="form-control" name="stock_maximo" min="0" value="1000" 
                                   placeholder="Cantidad máxima recomendada">
                            <small class="text-muted">Opcional: para alertas de sobrestock</small>
                        </div>
                        
                        <div class="col-12">
                            <hr>
                            <h6 class="text-muted"><i class="fas fa-dollar-sign me-2"></i>Configuración de Precios</h6>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label"><i class="fas fa-percentage me-1"></i>Margen de Ganancia (%)</label>
                            <input type="number" class="form-control" name="margen_ganancia" min="0" max="100" step="0.1" value="30" 
                                   placeholder="Porcentaje de ganancia">
                            <small class="text-muted">Para cálculo automático de precios de venta</small>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label"><i class="fas fa-coins me-1"></i>Moneda</label>
                            <select class="form-select" name="moneda">
                                <option value="COP" selected>COP - Peso Colombiano</option>
                                <option value="USD">USD - Dólar Americano</option>
                                <option value="EUR">EUR - Euro</option>
                            </select>
                        </div>
                        
                        <div class="col-12">
                            <hr>
                            <h6 class="text-muted"><i class="fas fa-bell me-2"></i>Configuración de Alertas</h6>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="alertas_stock_bajo" id="alertas_stock_bajo" checked>
                                <label class="form-check-label" for="alertas_stock_bajo">
                                    <i class="fas fa-exclamation-triangle me-1"></i>Alertas de Stock Bajo
                                </label>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="alertas_vencimiento" id="alertas_vencimiento" checked>
                                <label class="form-check-label" for="alertas_vencimiento">
                                    <i class="fas fa-calendar-times me-1"></i>Alertas de Vencimiento
                                </label>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
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
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>Cancelar
                        </button>
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-save me-1"></i>Guardar Configuración
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Función para abrir modal de nuevo producto
window.abrirproducto = function() {
    const modalElement = document.getElementById('modal-nuevo-producto');
    if (modalElement && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
        const modal = new bootstrap.Modal(modalElement);
        modal.show();
    } else if (modalElement) {
        modalElement.style.display = 'block';
        modalElement.classList.add('show');
    } else {
        alert('No se encontró el modal de nuevo producto');
    }
}
                                </td>
                                <td class="d-none d-md-table-cell"><?php echo htmlspecialchars($item['naturaleza']); ?></td>
                                <td class="d-none d-lg-table-cell"><?php echo htmlspecialchars($item['color']); ?></td>
                                <td>
                                    <span class="badge <?php echo ($item['stock'] == 0 ? 'bg-danger' : ($item['stock'] < 20 ? 'bg-warning' : 'bg-success')); ?>">
                                        <?php echo $item['stock']; ?>
                                    </span>
                                    <div class="d-sm-none small text-muted mt-1">
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
                                        <span class="<?php echo $estado_class; ?> fw-bold"><?php echo $item['estado_stock']; ?></span>
                                    </div>
                                </td>
                                <td class="d-none d-sm-table-cell">
                                    <span class="<?php echo ($item['stock'] == 0 ? 'text-danger fw-bold' : ($item['stock'] < 20 ? 'text-warning fw-bold' : 'text-success fw-bold')); ?>">
                                        <?php echo ($item['stock'] == 0 ? 'Sin Stock' : ($item['stock'] < 20 ? 'Bajo' : 'Normal')); ?>
                                    </span>
                                </td>
                                <td class="d-none d-md-table-cell">$<?php echo is_numeric($item['precio']) ? number_format($item['precio'], 0, ',', '.') : $item['precio']; ?></td>
                                <td class="d-none d-lg-table-cell">$<?php echo (is_numeric($item['stock']) && is_numeric($item['precio'])) ? number_format($item['stock'] * $item['precio'], 0, ',', '.') : 0; ?></td>
                                <!-- NUEVA COLUMNA: Proveedores -->
                                <td>
                                    <?php
                                    $proveedores_producto = array();
                                    if (!empty($proveedores)) {
                                        foreach ($proveedores as $prov) {
                                            if (!empty($prov['productos']) && in_array($item['idinv'], $prov['productos'])) {
                                                $proveedores_producto[] = $prov['nombre'];
                                            }
                                        }
                                    }
                                    if (!empty($proveedores_producto)) {
                                        echo htmlspecialchars(implode(', ', $proveedores_producto));
                                    } else {
                                        echo '<span class="text-muted">Sin proveedor</span>';
                                    }
                                    ?>
                                    <button class="btn btn-sm btn-outline-primary ms-2" title="Seleccionar proveedor" data-id="<?php echo $item['idinv']; ?>"><i class="fas fa-user-plus"></i></button>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm d-flex d-md-inline-flex" role="group">
                                        <button class="btn btn-warning btn-sm btn-modal-editar" data-id="<?php echo $item['idinv']; ?>" title="Editar"><i class="fas fa-edit"></i></button>
                                        <button class="btn btn-info btn-sm btn-modal-stock" data-id="<?php echo $item['idinv']; ?>" title="Agregar Stock"><i class="fas fa-plus"></i></button>
                                        <button class="btn btn-danger btn-sm btn-modal-eliminar" data-id="<?php echo $item['idinv']; ?>" title="Eliminar"><i class="fas fa-trash"></i></button>
                                    </div>
                                </td>
                            </tr>
                const actionInput = document.createElement('input');
                actionInput.type = 'hidden';
                actionInput.name = 'accion';
                actionInput.value = 'exportar_inventario';
                
                form.appendChild(actionInput);
                document.body.appendChild(form);
                
                console.log('Cambiando texto del boton...');
                // Cambiar texto del botón
                const originalText = this.innerHTML;
                this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Exportando...';
                this.disabled = true;
                
                console.log('Enviando formulario...');
                form.submit();
                
                console.log('Configurando timeout...');
                // Restaurar botón después de un tiempo
                setTimeout(() => {
                    if (document.body.contains(form)) {
                        document.body.removeChild(form);
                    }
                    this.innerHTML = originalText;
                    this.disabled = false;
                    console.log('Boton restaurado');
                        <tr>
                            <td><?= htmlspecialchars($item['producto']) ?></td>
                            <td><?= htmlspecialchars($item['naturaleza']) ?></td>
                            <td><?= htmlspecialchars($item['color']) ?></td>
                            <td>
                                <span class="badge bg-<?= ($item['stock'] == 0) ? 'danger' : (($item['stock'] < 20) ? 'warning' : 'success') ?>">
                                    <?= $item['stock'] ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($item['stock'] == 0): ?>
                                    <span class="text-danger fw-bold">Sin Stock</span>
                                <?php elseif ($item['stock'] < 20): ?>
                                    <span class="text-warning fw-bold">Bajo</span>
                                <?php else: ?>
                                    <span class="text-success fw-bold">Normal</span>
                                <?php endif; ?>
                            </td>
                            <td>$<?= is_numeric($item['precio']) ? number_format($item['precio'], 0, ',', '.') : $item['precio'] ?></td>
                            <td>$<?= is_numeric($item['stock']) && is_numeric($item['precio']) ? number_format($item['stock'] * $item['precio'], 0, ',', '.') : 0 ?></td>
                            <td>
                                <?php
                                // Mostrar proveedores asociados a este producto
                                $proveedores_producto = [];
                                if (!empty($proveedores)) {
                                    foreach ($proveedores as $prov) {
                                        // Buscar si el proveedor tiene este producto asociado
                                        $sql = "SELECT COUNT(*) FROM proveedor_producto WHERE proveedor_id = ? AND producto_id = ?";
                                        $stmt = $GLOBALS['db']->prepare($sql);
                                        $stmt->execute([$prov['id'], $item['idinv']]);
                                        if ($stmt->fetchColumn() > 0) {
                                            $proveedores_producto[] = $prov['nombre'];
                                        }
                                    }
                                }
                                ?>
                                <?php if (!empty($proveedores_producto)): ?>
                                    <?= implode(', ', $proveedores_producto) ?>
                                <?php else: ?>
                                    <span class="text-muted">Sin proveedor</span>
                                <?php endif; ?>
                                <!-- Opción para seleccionar/editar proveedor -->
                                <button class="btn btn-sm btn-outline-primary ms-2" title="Seleccionar proveedor" data-id="<?= $item['idinv'] ?>"><i class="fas fa-user-plus"></i></button>
                            </td>
                            <td>
                                <!-- Acciones -->
                                <button class="btn btn-warning btn-sm btn-modal-editar" data-id="<?= $item['idinv'] ?>" title="Editar"><i class="fas fa-edit"></i></button>
                                <button class="btn btn-info btn-sm btn-modal-stock" data-id="<?= $item['idinv'] ?>" title="Agregar Stock"><i class="fas fa-plus"></i></button>
                                <button class="btn btn-danger btn-sm btn-modal-eliminar" data-id="<?= $item['idinv'] ?>" title="Eliminar"><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>
        console.log('🎭 Bootstrap.Modal:', typeof bootstrap?.Modal);
        
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            console.log('✅ Creando modal...');
            const modal = new bootstrap.Modal(modalElement);
            modal.show();
            console.log('🎉 Modal mostrado exitosamente');
        } else {
            console.error('❌ Bootstrap no está disponible');
            alert('Error: Bootstrap no está cargado correctamente');
        }
    } catch (error) {
        console.error('💥 Error al abrir modal:', error);
        alert('Error al abrir el modal de configuración: ' + error.message);
    }
}

// Función para abrir modal de proveedores
window.abrirproveedor = function() {
    const modalElement = document.getElementById('modal-proveedores');
    if (modalElement && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
        const modal = new bootstrap.Modal(modalElement);
        modal.show();
    } else if (modalElement) {
        modalElement.style.display = 'block';
        modalElement.classList.add('show');
    } else {
        alert('No se encontró el modal de proveedores');
    }
}
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            console.log('✅ Creando modal...');
            const modal = new bootstrap.Modal(modalElement);
            modal.show();
            console.log('🎉 Modal mostrado exitosamente');
        } else {
            console.error('❌ Bootstrap no está disponible');
            alert('Error: Bootstrap no está cargado correctamente');
        }
    } catch (error) {
        console.error('💥 Error al abrir modal:', error);
        alert('Error al abrir el modal de proveedores: ' + error.message);
    }
}

// Función de prueba para configuración
window.testConfiguracion = function() {
    console.log('🧪 Probando función de configuración...');
    console.log('🔍 Modal existe:', !!document.getElementById('modal-configuracion'));
    console.log('🔍 Bootstrap disponible:', typeof bootstrap);
    if (typeof window.abrirConfiguracion === 'function') {
        console.log('✅ Función abrirConfiguracion disponible');
        window.abrirConfiguracion();
    } else {
        console.error('❌ Función abrirConfiguracion no disponible');
    }
}

// Función de verificación de estado
window.verificarEstado = function() {
    console.log('🔍 Estado del sistema:');
    console.log('✅ Bootstrap disponible:', typeof bootstrap);
    console.log('✅ jQuery disponible:', typeof $);
    console.log('✅ Modal nuevo producto:', !!document.getElementById('modal-nuevo-producto'));
    console.log('✅ Modal proveedores:', !!document.getElementById('modal-proveedores'));
    console.log('✅ Modal configuración:', !!document.getElementById('modal-configuracion'));
    console.log('✅ Modal editar producto:', !!document.getElementById('modal-editar-producto'));
    console.log('✅ Modal agregar stock:', !!document.getElementById('modal-agregar-stock'));
    console.log('✅ Modal eliminar producto:', !!document.getElementById('modal-eliminar-producto'));
    console.log('✅ Funciones principales:', {
        abrirproducto: typeof window.abrirproducto,
        abrirproveedor: typeof window.abrirproveedor,
        abrirConfiguracion: typeof window.abrirConfiguracion
    });
    console.log('✅ Funciones de modales:', {
        abrirModalEditar: typeof window.abrirModalEditar,
        abrirModalAgregarStock: typeof window.abrirModalAgregarStock,
        abrirModalEliminar: typeof window.abrirModalEliminar
    });
    console.log('✅ Funciones de acciones:', {
        editarFlor: typeof window.editarFlor,
        agregarAInventario: typeof window.agregarAInventario,
        eliminarFlor: typeof window.eliminarFlor
    });
}

// Asegurar que las funciones estén disponibles cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    console.log('📋 DOM Cargado - Iniciando sistema...');
    
    // Envolver todo en try-catch para capturar errores
    try {
        // Limpiar cualquier error previo
        console.clear();
        
        console.log('🎉 Sistema iniciado correctamente');
        
        // Configurar funciones básicas primero
        setTimeout(function() {
            try {
                verificarEstado();
                setupModalButtons();
            } catch (e) {
                console.warn('Error en configuración inicial:', e);
            }
        }, 500);
        
        // Verificar Bootstrap
        if (typeof bootstrap !== 'undefined') {
            console.log('✅ Bootstrap está disponible');
        } else {
            console.log('⏳ Esperando Bootstrap...');
            setTimeout(function() {
                try {
                    verificarEstado();
                    setupModalButtons();
                } catch (e) {
                    console.warn('Error en verificación tardía:', e);
                }
            }, 1500);
        }
        
    } catch (error) {
        console.error('💥 Error al iniciar sistema:', error);
        
        // Intentar recuperación básica
        setTimeout(function() {
            try {
                console.log('🔄 Intentando recuperación básica...');
                if (typeof limpiarErrores === 'function') {
                    limpiarErrores();
                }
            } catch (e) {
                console.warn('Error en recuperación:', e);
            }
        }, 2000);
    }
});

// Función para configurar los botones de modal - VERSION SIMPLIFICADA
function setupModalButtons() {
    console.log('🔧 Configurando botones de modal...');
    
    // Usar setTimeout para asegurar que el DOM esté completamente listo
    setTimeout(function() {
        try {
            // Encontrar todos los botones de modal y agregar listeners directamente
            const botonesEditar = document.querySelectorAll('.btn-modal-editar');
            const botonesStock = document.querySelectorAll('.btn-modal-stock');
            const botonesEliminar = document.querySelectorAll('.btn-modal-eliminar');
            
            console.log('📋 Botones encontrados:', {
                editar: botonesEditar.length,
                stock: botonesStock.length,
                eliminar: botonesEliminar.length
            });
            
            // Configurar botones de editar
            botonesEditar.forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const id = this.dataset.productoId;
                    const nombre = this.dataset.productoNombre;
                    console.log('🔧 Editar clickeado:', id, nombre);
                    if (typeof abrirModalEditar === 'function') {
                        abrirModalEditar(id, nombre);
                    } else {
                        console.error('❌ Función abrirModalEditar no existe');
                        alert('Error: Función de editar no disponible');
                    }
                });
            });
            
            // Configurar botones de stock
            botonesStock.forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const id = this.dataset.productoId;
                    const nombre = this.dataset.productoNombre;
                    console.log('📦 Stock clickeado:', id, nombre);
                    if (typeof abrirModalAgregarStock === 'function') {
                        abrirModalAgregarStock(id, nombre);
                    } else {
                        console.error('❌ Función abrirModalAgregarStock no existe');
                        alert('Error: Función de stock no disponible');
                    }
                });
            });
            
            // Configurar botones de eliminar
            botonesEliminar.forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const id = this.dataset.productoId;
                    const nombre = this.dataset.productoNombre;
                    console.log('🗑️ Eliminar clickeado:', id, nombre);
                    if (typeof abrirModalEliminar === 'function') {
                        abrirModalEliminar(id, nombre);
                    } else {
                        console.error('❌ Función abrirModalEliminar no existe');
                        // Usar confirmación simple como fallback
                        if (confirm('¿Estás seguro de que deseas eliminar "' + nombre + '"?')) {
                            console.log('Usuario confirmó eliminación de:', id);
                            // Aquí podrías agregar lógica de eliminación
                        }
                    }
                });
            });
            
            console.log('✅ Botones configurados correctamente');
            
        } catch (error) {
            console.error('💥 Error al configurar botones:', error);
        }
    }, 100);
}

// Función de prueba para verificar que todo funciona
window.testearBotones = function() {
    console.log('🧪 Testeando sistema de botones...');
    
    console.log('📋 Estado de funciones:');
    console.log('- abrirModalEditar:', typeof window.abrirModalEditar);
    console.log('- abrirModalAgregarStock:', typeof window.abrirModalAgregarStock);
    console.log('- abrirModalEliminar:', typeof window.abrirModalEliminar);
    
    console.log('📋 Estado de modales:');
    console.log('- modal-editar-producto:', !!document.getElementById('modal-editar-producto'));
    console.log('- modal-agregar-stock:', !!document.getElementById('modal-agregar-stock'));
    console.log('- modal-eliminar-producto:', !!document.getElementById('modal-eliminar-producto'));
    
    console.log('📋 Botones encontrados:');
    console.log('- btn-modal-editar:', document.querySelectorAll('.btn-modal-editar').length);
    console.log('- btn-modal-stock:', document.querySelectorAll('.btn-modal-stock').length);
    console.log('- btn-modal-eliminar:', document.querySelectorAll('.btn-modal-eliminar').length);
    
    // Testear manualmente el primer botón de editar
    const primerBotonEditar = document.querySelector('.btn-modal-editar');
    if (primerBotonEditar) {
        console.log('🔧 Primer botón editar encontrado:', primerBotonEditar.dataset);
    }
}

// Función de emergencia para limpiar errores
window.limpiarErrores = function() {
    console.log('🧹 Limpiando errores JavaScript...');
    
    // Limpiar console de errores
    console.clear();
    
    // Restablecer funciones básicas
    try {
        // Verificar estado básico
        console.log('✅ Verificando funciones básicas...');
        verificarEstado();
        
        // Reconfigurar botones
        console.log('🔧 Reconfigurando botones...');
        setupModalButtons();
        
        console.log('✅ Sistema limpio y funcionando');
        return true;
    } catch (error) {
        console.error('❌ Error al limpiar:', error);
        return false;
    }
}

// Función para reconfigurar botones después de actualizaciones AJAX
window.reconfigurarrBotones = function() {
    console.log('🔄 Reconfigurando botones después de actualización...');
    setupModalButtons();
}

// Hook para cuando se actualice el contenido vía AJAX
window.onInventarioActualizado = function() {
    console.log('📋 Inventario actualizado - reconfigurando botones...');
    setTimeout(function() {
        setupModalButtons();
    }, 200);
}

// Funciones específicas para abrir modales de acciones
window.abrirModalEditar = function(idProducto, nombreProducto) {
    console.log('🔧 Abriendo modal editar para:', idProducto, nombreProducto);
    try {
        const modalElement = document.getElementById('modal-editar-producto');
        if (!modalElement) {
            console.error('❌ Modal editar no encontrado');
            alert('Error: Modal de editar no encontrado');
            return;
        }
        
        // Llenar los campos del modal con los datos del producto (IDs corregidos)
        const inputId = modalElement.querySelector('#editar_producto_id');
        const inputNombre = modalElement.querySelector('#editar_nombre_producto');
        
        console.log('🔍 Elementos encontrados:', {
            inputId: !!inputId,
            inputNombre: !!inputNombre
        });
        
        if (inputId) {
            inputId.value = idProducto;
            console.log('✅ ID asignado:', idProducto);
        } else {
            console.warn('⚠️ Input ID no encontrado');
        }
        
        if (inputNombre) {
            inputNombre.value = nombreProducto;
            console.log('✅ Nombre asignado:', nombreProducto);
        } else {
            console.warn('⚠️ Input Nombre no encontrado');
        }
        
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            const modal = new bootstrap.Modal(modalElement);
            modal.show();
            console.log('✅ Modal mostrado');
        } else {
            console.error('❌ Bootstrap no disponible');
            alert('Error: Bootstrap no está disponible');
        }
    } catch (error) {
        console.error('💥 Error al abrir modal editar:', error);
        alert('Error al abrir modal: ' + error.message);
    }
}

window.abrirModalAgregarStock = function(idProducto, nombreProducto) {
    console.log('🔧 Abriendo modal agregar stock para:', idProducto, nombreProducto);
    try {
        const modalElement = document.getElementById('modal-agregar-stock');
        if (!modalElement) {
            console.error('❌ Modal agregar stock no encontrado');
            alert('Error: Modal de agregar stock no encontrado');
            return;
        }
        
        // Llenar los campos del modal con los datos del producto (IDs corregidos)
        const inputId = modalElement.querySelector('#stock_producto_id');
        const spanNombre = modalElement.querySelector('#stock_nombre_producto');
        const inputCantidad = modalElement.querySelector('#cantidad_agregar');
        
        console.log('🔍 Elementos encontrados:', {
            inputId: !!inputId,
            spanNombre: !!spanNombre,
            inputCantidad: !!inputCantidad
        });
        
        if (inputId) {
            inputId.value = idProducto;
            console.log('✅ ID asignado:', idProducto);
        } else {
            console.warn('⚠️ Input ID no encontrado');
        }
        
        if (spanNombre) {
            spanNombre.textContent = nombreProducto;
            console.log('✅ Nombre asignado:', nombreProducto);
        } else {
            console.warn('⚠️ Span Nombre no encontrado');
        }
        
        // Limpiar campo de cantidad
        if (inputCantidad) {
            inputCantidad.value = '';
            inputCantidad.focus();
        }
        
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            const modal = new bootstrap.Modal(modalElement);
            modal.show();
            console.log('✅ Modal de stock mostrado');
        } else {
            console.error('❌ Bootstrap no disponible');
            alert('Error: Bootstrap no está disponible');
        }
    } catch (error) {
        console.error('💥 Error al abrir modal agregar stock:', error);
        alert('Error al abrir modal: ' + error.message);
    }
}

window.abrirModalEliminar = function(idProducto, nombreProducto) {
    console.log('🔧 Función eliminar llamada para:', idProducto, nombreProducto);
    try {
        // No hay modal específico de eliminar, usar confirmación simple
        const confirmMsg = '¿Estás seguro de que deseas eliminar el producto "' + nombreProducto + '"?\n\nEsta acción no se puede deshacer.';
        
        if (confirm(confirmMsg)) {
            console.log('✅ Usuario confirmó eliminación de:', idProducto);
            
            // Crear y enviar formulario para eliminar
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '?ctrl=cinventario';
            form.style.display = 'none';
            
            const accionInput = document.createElement('input');
            accionInput.type = 'hidden';
            accionInput.name = 'accion';
            accionInput.value = 'eliminar_producto';
            form.appendChild(accionInput);
            
            const idInput = document.createElement('input');
            idInput.type = 'hidden';
            idInput.name = 'producto_id';
            idInput.value = idProducto;
            form.appendChild(idInput);
            
            document.body.appendChild(form);
            form.submit();
            
            console.log('📋 Formulario de eliminación enviado');
        } else {
            console.log('❌ Usuario canceló eliminación');
        }
    } catch (error) {
        console.error('💥 Error al eliminar producto:', error);
        alert('Error al eliminar producto: ' + error.message);
    }
}

// Función para cambiar elementos por página (con fallback robusto)
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
// Editar flor inline
window.editarFlor = function(florData) {
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
window.agregarAInventario = function(idFlor, nombreFlor) {
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
window.eliminarFlor = function(idFlor, nombreFlor) {
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
    const url = 'controllers/CinventarioApi.php?' + params.toString();
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

// Función para exportar inventario a Excel
function exportarInventario() {
    try {
        console.log('📊 Iniciando exportación de inventario...');
        
        // Crear formulario temporal
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '?ctrl=Cinventario';
        form.style.display = 'none';
        
        const actionInput = document.createElement('input');
        actionInput.name = 'accion';
        actionInput.value = 'exportar_inventario';
        
        form.appendChild(actionInput);
        document.body.appendChild(form);
        
        // Mostrar mensaje de carga
        const originalText = event.target.innerHTML;
        event.target.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Exportando...';
        event.target.disabled = true;
        
        form.submit();
        
        // Restaurar botón después de un tiempo
        setTimeout(() => {
            document.body.removeChild(form);
            event.target.innerHTML = originalText;
            event.target.disabled = false;
        }, 3000);
        
        console.log('✅ Exportación iniciada correctamente');
    } catch (error) {
        console.error('💥 Error al exportar:', error);
        alert('Error al exportar el inventario: ' + error.message);
    }
}
</script>

<script src="/Original-Floraltech/assets/inventario.js"></script>

<script>
window.abrirModalEditar = function(id, nombre) {
    try {
        const modal = document.getElementById('modal-editar-producto');
        const inputId = modal.querySelector('#editar_producto_id');
        const inputNombre = modal.querySelector('#editar_nombre_producto');
        if (inputId) inputId.value = id;
        if (inputNombre) inputNombre.value = nombre;
        new bootstrap.Modal(modal).show();
    } catch (e) { alert('Error al abrir modal de editar'); }
};

window.abrirModalAgregarStock = function(id, nombre) {
    try {
        const modal = document.getElementById('modal-agregar-stock');
        const inputId = modal.querySelector('#stock_producto_id');
        const spanNombre = modal.querySelector('#stock_nombre_producto');
        if (inputId) inputId.value = id;
        if (spanNombre) spanNombre.textContent = nombre;
        new bootstrap.Modal(modal).show();
    } catch (e) { alert('Error al abrir modal de stock'); }
};

window.abrirModalEliminar = function(id, nombre) {
    if (confirm('¿Eliminar "' + nombre + '"?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '?ctrl=cinventario';
        form.innerHTML = '<input type="hidden" name="accion" value="eliminar_producto"><input type="hidden" name="producto_id" value="' + id + '">';
        document.body.appendChild(form);
        form.submit();
    }
};

// Configurar botones cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        document.querySelectorAll('.btn-modal-editar').forEach(btn => {
            btn.onclick = function() { abrirModalEditar(this.dataset.productoId, this.dataset.productoNombre); };
        });
        document.querySelectorAll('.btn-modal-stock').forEach(btn => {
            btn.onclick = function() { abrirModalAgregarStock(this.dataset.productoId, this.dataset.productoNombre); };
        });
        document.querySelectorAll('.btn-modal-eliminar').forEach(btn => {
            btn.onclick = function() { abrirModalEliminar(this.dataset.productoId, this.dataset.productoNombre); };
        });
        console.log('✅ Todos los botones configurados');
    }, 500);
});

// Verificar si hay errores
window.addEventListener('error', function(e) {
    console.error('Error JavaScript en inventario:', e.message, 'en línea:', e.lineno);
});
</script>

<!-- Script mejorado para el botón exportar -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Arreglo para el botón de exportar
    setTimeout(function() {
        const btnExportar = document.getElementById('btn-exportar');
        
        if (btnExportar) {
            // Limpiar listeners anteriores
            const newBtn = btnExportar.cloneNode(true);
            btnExportar.parentNode.replaceChild(newBtn, btnExportar);
            
            newBtn.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Feedback visual
                const originalText = newBtn.innerHTML;
                newBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Exportando...';
                newBtn.disabled = true;
                
                // Crear y enviar formulario
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '?ctrl=Cinventario';
                form.style.display = 'none';
                
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'accion';
                input.value = 'exportar_inventario';
                
                form.appendChild(input);
                document.body.appendChild(form);
                form.submit();
                
                // Restaurar botón
                setTimeout(() => {
                    newBtn.innerHTML = originalText;
                    newBtn.disabled = false;
                    if (document.body.contains(form)) {
                        document.body.removeChild(form);
                    }
                }, 3000);
            });
            
            console.log('✅ Botón exportar configurado correctamente');
        }
    }, 1000);
});
</script>


</main>
<!-- Fin de la vista de inventario -->
