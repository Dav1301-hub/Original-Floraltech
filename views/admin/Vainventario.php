<!-- FontAwesome 6.5.2 CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-+Cf+8J2k6U5zQ6QwQ6Q6Q6Q6Q6Q6Q6Q6Q6Q6Q6Q6Q6Q6Q6Q6Q6Q6Q6Q6Q6Q6Q6Q6Q6Q6Q6Q6Q6Q6Q==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<!-- Bootstrap 5 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    /* Responsive para Inventario Admin */
    * {
        box-sizing: border-box;
    }
    
    html, body {
        overflow-x: hidden;
        width: 100%;
        margin: 0;
        padding: 0;
    }
    
    .container-fluid {
        padding: 0 !important;
        margin: 0 !important;
        width: 100% !important;
        max-width: 100% !important;
        overflow-x: hidden;
    }
    
    main.container-fluid {
        padding: 1.5rem 1rem !important;
    }
    
    /* Asegurar que las cards y tablas tengan espacio */
    .card {
        margin-bottom: 1.5rem;
    }
    
    .table-responsive {
        margin-bottom: 1rem;
    }
    
    /* Cards de resumen más compactas */
    .row.mb-4 .card {
        transition: transform 0.2s;
    }
    
    .row.mb-4 .card:hover {
        transform: translateY(-2px);
    }
    
    @media (max-width: 1200px) {
        /* 3 cards por fila en pantallas medianas */
        .row.mb-4 .col-lg-2 {
            flex: 0 0 33.333333%;
            max-width: 33.333333%;
        }
    }
    
    @media (max-width: 992px) {
        /* 2 cards por fila en tablet */
        .row.mb-4 .col-lg-2 {
            flex: 0 0 50%;
            max-width: 50%;
        }
    }
    
    @media (max-width: 768px) {
        main.container-fluid {
            padding: 1.5rem 1rem !important;
        }
        
        /* Filtros en columnas completas */
        .card-body .row .col-md-3 {
            flex: 0 0 100%;
            max-width: 100%;
            margin-bottom: 0.5rem;
        }
        
        /* Botones de acción centrados */
        .d-flex.justify-content-center {
            flex-direction: column;
            align-items: stretch;
        }
        
        .d-flex.justify-content-center .btn {
            margin-bottom: 0.5rem;
        }
        
        /* Tablas más pequeñas */
        .table {
            font-size: 0.85rem;
        }
        
        .table th,
        .table td {
            padding: 0.5rem 0.25rem;
        }
        
        /* Botones de acciones en tabla */
        .btn-group-sm .btn {
            padding: 0.25rem 0.4rem;
            font-size: 0.75rem;
        }
    }
    
    @media (max-width: 576px) {
        main.container-fluid {
            padding: 1rem 0.75rem !important;
        }
        
        /* Cards de resumen en 1 columna en móvil */
        .row.mb-4 .col-lg-2 {
            flex: 0 0 100%;
            max-width: 100%;
        }
        
        /* Reducir tamaño de texto en móvil */
        .row.mb-4 .fs-4 {
            font-size: 1.5rem !important;
        }
        
        .row.mb-4 .small {
            font-size: 0.8rem !important;
        }
        
        /* Títulos más pequeños */
        h2.mb-4 {
            font-size: 1.5rem;
        }
        
        /* Card bodies más compactos */
        .card-body {
            padding: 0.75rem;
        }
        
        /* Paginación más compacta */
        .pagination {
            font-size: 0.85rem;
        }
        
        .pagination .page-link {
            padding: 0.25rem 0.5rem;
        }
        
        /* Modales a pantalla completa */
        .modal-dialog {
            margin: 0;
            max-width: 100%;
            height: 100vh;
        }
        
        .modal-content {
            height: 100vh;
            border-radius: 0;
        }
        
        /* Formularios dentro de modales */
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
        
        /* Extra small */
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
    
    /* Estilos para columnas ordenables */
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
    
    /* Columna de acciones - solución para botones cortados */
    .table-responsive {
        overflow-x: auto !important;
        overflow-y: visible;
        width: 100%;
        box-sizing: border-box;
        -webkit-overflow-scrolling: touch;
    }
    
    .table {
        width: 100%;
        table-layout: auto;
        font-size: 0.65rem;
        margin-bottom: 0;
        min-width: 100%;
    }
    
    .table th {
        font-size: 0.6rem;
        padding: 0.25rem 0.15rem !important;
    }
    
    .table td {
        padding: 0.25rem 0.15rem !important;
        white-space: nowrap;
        font-size: 0.65rem;
    }
    
    /* Columna de acciones - fijar ancho y mostrar botones */
    .table td:last-child,
    .table th:last-child {
        white-space: normal !important;
        padding: 0.25rem 0.1rem !important;
        min-width: 100px;
        position: sticky;
        right: 0;
        background-color: #fff;
    }
    
    /* Botones en columna de acciones - muy compactos */
    .table .btn-group,
    .table .btn-group-sm {
        display: flex !important;
        flex-wrap: wrap;
        gap: 0.05rem;
        width: 100%;
    }
    
    .table .btn {
        padding: 0.15rem 0.25rem;
        font-size: 0.55rem;
        flex: 0 1 auto;
        min-width: auto;
        white-space: nowrap;
        line-height: 1;
        border: 0.5px solid;
    }
    
    .table .btn i,
    .table .btn .fa {
        margin-right: 0 !important;
        font-size: 0.5rem;
    }
    
    /* Badges más pequeños */
    .table .badge {
        padding: 0.15rem 0.25rem;
        font-size: 0.5rem;
    }
    
    /* Reducir espacios en filas */
    .table tbody tr {
        height: auto;
    }
    
    /* En tablets */
    @media (max-width: 992px) {
        .table {
            font-size: 0.6rem;
        }
        
        .table th {
            font-size: 0.55rem;
        }
        
        .table th,
        .table td {
            padding: 0.2rem 0.1rem !important;
        }
        
        .table .btn {
            padding: 0.1rem 0.2rem;
            font-size: 0.5rem;
        }
    }
</style>
<!-- Gestión de Inventario - Vista -->
<div class="section-box" style="padding: 0; margin: 0; width: 100%; max-width: 100%; box-sizing: border-box;">
    <!-- Debug UI: activa añadiendo ?debug_ui=1 a la URL -->
    <script>
        (function(){
            try {
                if (window.location.search && window.location.search.indexOf('debug_ui=1') !== -1) {
                    document.documentElement.classList.add('debug-outline');
                    document.body.classList.add('debug-outline');
                    var tb = document.querySelector('.topbar'); if (tb) tb.classList.add('debug-outline');

                    function rectToStr(r){ return Math.round(r.top)+','+Math.round(r.left)+' / '+Math.round(r.width)+'x'+Math.round(r.height); }

                    var panel = document.createElement('div');
                    panel.id = 'debug-ui-panel';
                    panel.style.position = 'fixed';
                    panel.style.right = '12px';
                    panel.style.top = '12px';
                    panel.style.zIndex = '99999';
                    panel.style.background = 'rgba(0,0,0,0.65)';
                    panel.style.color = '#fff';
                    panel.style.fontSize = '12px';
                    panel.style.padding = '8px 10px';
                    panel.style.borderRadius = '8px';
                    panel.style.boxShadow = '0 6px 18px rgba(0,0,0,0.4)';
                    panel.style.maxWidth = '320px';
                    panel.innerHTML = '<strong>DEBUG UI</strong><br><small id="dbg-lines">calculando...</small><br><a id="dbg-close" href="#" style="color:#ffd; text-decoration:underline; font-size:11px;">Cerrar</a>';
                    document.body.appendChild(panel);

                    function update(){
                        var htmlR = document.documentElement.getBoundingClientRect();
                        var bodyR = document.body.getBoundingClientRect();
                        var topR = tb ? tb.getBoundingClientRect() : {top:-1,left:-1,width:0,height:0};
                        var elAt = document.elementFromPoint(5,5);
                        var elName = elAt ? (elAt.tagName + (elAt.id?('#'+elAt.id):'') + (elAt.className?('.'+elAt.className.split(' ').join('.')):'')) : 'none';

                        // List first few direct children of body with their top positions
                        var children = Array.from(document.body.children).slice(0,8).map(function(ch){
                            var r = ch.getBoundingClientRect();
                            return ch.tagName.toLowerCase() + (ch.id?('#'+ch.id):'') + (ch.className?('.'+ch.className.split(' ').join('.')):'') + ' t:'+Math.round(r.top)+' h:'+Math.round(r.height);
                        }).join(' | ');

                        document.getElementById('dbg-lines').textContent = 'html: '+rectToStr(htmlR)+' | body: '+rectToStr(bodyR)+' | topbar: '+rectToStr(topR)+' | el@5,5: '+elName + '\nchildren: '+children;
                    }

                    document.getElementById('dbg-close').addEventListener('click', function(e){ e.preventDefault(); panel.remove(); });
                    window.addEventListener('resize', update);
                    setTimeout(update,200);
                }
            } catch(e){ console.error('debug-ui error', e); }
        })();
    </script>
    <!-- Backdrop para modales manuales -->
<div id="modal-backdrop" class="modal-backdrop fade" style="display:none;z-index:1040;"></div>

    <!-- Mensajes de error -->
    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            <strong>Error:</strong> <?= htmlspecialchars(urldecode($_GET['error'])) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <!-- Mensajes de éxito -->
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            <strong>Éxito:</strong> Operación completada correctamente
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="d-flex align-items-center mb-4 py-3 px-3 rounded-4 shadow-sm text-white" style="background: linear-gradient(120deg, #0d6efd 0%, #5b21b6 60%, #1e1b4b 100%);">
        <div>
            <p class="mb-1 opacity-75" style="letter-spacing:1px;text-transform:uppercase; color: #ffff"><i class="fas fa-cube me-2"></i>FloralTech Admin</p>
            <h2 class="mb-0 fw-bold" style="color: #ffff">Gestión de Inventario</h2>
        </div>
    </div> 
    <!-- Cards de resumen funcionales -->
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
                    <small class="text-muted" style="font-size: 0.75rem;">0-9 unidades</small>
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

    <!-- Mensajes de éxito y error -->

    <!-- Botones de acción de inventario -->
    <div class="d-flex justify-content-center flex-wrap gap-2 mb-4">
        <button class="btn btn-success shadow-sm" onclick="abrirproducto(); return false;" id="btn-nuevo-producto">
            <i class="fas fa-plus me-2"></i>Nuevo Producto
        </button>
        <button class="btn btn-info shadow-sm" onclick="abrirproveedor(); return false;" id="btn-proveedores">
            <i class="fas fa-truck me-2"></i>Proveedores
        </button>
        <button class="btn btn-primary shadow-sm" onclick="sincronizarTodosStocks(); return false;" id="btn-sincronizar-stocks" title="Sincronizar stock de productos con lotes activos">
            <i class="fas fa-sync-alt me-2"></i>Sincronizar Stocks
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
                <i class="fas fa-file-excel"></i> Excel
            </button>
            <button class="btn btn-outline-danger btn-sm" id="btn-exportar-pdf" title="Exportar a PDF">
                <i class="fas fa-file-pdf"></i> PDF
            </button>
        </div>
    </div>

    <!-- Sección de Productos Perecederos (Flores Naturales) -->
    <div class="card mb-4">
        <div class="card-header text-white" style="background-color: #e67e22;">
            <h5 class="mb-0">
                <i class="fas fa-seedling me-2"></i>Productos Perecederos (Flores Naturales)
                <span class="badge bg-dark float-end">
                    <?php echo $total_elementos_perecederos ?? 0; ?> productos disponibles
                </span>
            </h5>
        </div>
        <div class="card-body p-3">
            <!-- Búsqueda en tiempo real con filtros integrados -->
            <div class="mb-3">
                <div class="row g-2">
                    <div class="col-md-8">
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" 
                                   class="form-control" 
                                   id="buscar-perecederos" 
                                   placeholder="Buscar por nombre, proveedor, lote..."
                                   autocomplete="off">
                            <button class="btn btn-outline-secondary" 
                                    type="button" 
                                    id="limpiar-busqueda-perecederos"
                                    title="Limpiar búsqueda y filtros"
                                    style="display: none;">
                                <i class="fas fa-times"></i>
                            </button>
                            <span class="input-group-text bg-light" id="loading-perecederos" style="display: none;">
                                <i class="fas fa-spinner fa-spin text-primary"></i>
                            </span>
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
                <small class="text-muted mt-2 d-block">
                    <i class="fas fa-info-circle me-1"></i>
                    La búsqueda y filtros se aplican automáticamente mientras escribes
                </small>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="tabla-perecederos">
                    <thead class="table-light">
                        <tr>
                            <th class="sortable" data-sort="nombre" style="cursor: pointer;">
                                Producto <i class="fas fa-sort text-muted"></i>
                            </th>
                            <th class="sortable" data-sort="naturaleza" style="cursor: pointer;">
                                Naturaleza <i class="fas fa-sort text-muted"></i>
                            </th>
                            <th>Color</th>
                            <th class="sortable" data-sort="stock" style="cursor: pointer;">
                                Stock <i class="fas fa-sort text-muted"></i>
                            </th>
                            <th>P. Compra</th>
                            <th class="sortable" data-sort="precio" style="cursor: pointer;">
                                P. Venta <i class="fas fa-sort text-muted"></i>
                            </th>
                            <th>Margen %</th>
                            <th class="sortable" data-sort="valor_total" style="cursor: pointer;">
                                Ingresos Pot. <i class="fas fa-sort text-muted"></i>
                            </th>
                            <th>Fº Ingreso</th>
                            <th class="sortable" data-sort="fecha_caducidad" style="cursor: pointer;">
                                Fº Caducidad <i class="fas fa-sort text-muted"></i>
                            </th>
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
                                    <td>
                                        <div class="fw-bold"><?= htmlspecialchars($item['producto']) ?></div>
                                    </td>
                                    <td><span class="badge bg-success"><?= htmlspecialchars($item['naturaleza']) ?></span></td>
                                    <td>
                                        <span class="badge" style="background-color: 
                                        <?php 
                                        $colores = [
                                            'Rojo' => '#dc3545', 'Rosa' => '#ff69b4', 'Blanco' => '#f8f9fa',
                                            'Amarillo' => '#ffc107', 'Naranja' => '#fd7e14', 'Morado' => '#6f42c1',
                                            'Azul' => '#0d6efd', 'Verde' => '#198754', 'Multicolor' => '#6c757d'
                                        ];
                                        echo $colores[$item['color']] ?? '#6c757d';
                                        ?>; color: <?= in_array($item['color'], ['Blanco', 'Amarillo']) ? '#000' : '#fff' ?>;">
                                            <?= htmlspecialchars($item['color']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge <?= $item['stock'] == 0 ? 'bg-danger' : ($item['stock'] < 20 ? 'bg-warning text-dark' : 'bg-success') ?>">
                                            <?= $item['stock'] ?>
                                        </span>
                                    </td>
                                    <td class="text-muted small">$<?= number_format($item['precio_compra'] ?? 0, 2) ?></td>
                                    <td class="fw-bold text-primary">$<?= number_format($item['precio'] ?? 0, 2) ?></td>
                                    <td>
                                        <?php 
                                        $precio_compra_p = floatval($item['precio_compra'] ?? 0);
                                        $precio_venta_p = floatval($item['precio'] ?? 0);
                                        $margen_p = $precio_compra_p > 0 ? (($precio_venta_p - $precio_compra_p) / $precio_compra_p) * 100 : 0;
                                        $badge_p = $margen_p > 30 ? 'bg-success' : ($margen_p >= 10 ? 'bg-warning text-dark' : 'bg-danger');
                                        ?>
                                        <span class="badge <?= $badge_p ?>" title="Margen de ganancia">
                                            <?= number_format($margen_p, 1) ?>%
                                        </span>
                                    </td>
                                    <td class="fw-bold text-success">$<?= number_format(($item['stock'] ?? 0) * ($item['precio'] ?? 0), 2) ?></td>
                                    <td>
                                        <span class="text-muted small">
                                            <?= $item['fecha_actualizacion'] ? date('m/d/y', strtotime($item['fecha_actualizacion'])) : 'N/A' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if (!empty($item['lote_proxima_caducidad'])): ?>
                                            <?= date('m/d/y', strtotime($item['lote_proxima_caducidad'])) ?>
                                        <?php else: ?>
                                            <span class="text-muted">Sin lotes</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php 
                                        $dias = $item['dias_hasta_caducidad'] ?? null;
                                        if ($dias !== null && $item['lote_cantidad_activa'] > 0):
                                            if ($dias <= 3): ?>
                                                <span class="badge bg-danger">
                                                    <i class="fas fa-circle-exclamation"></i> <?= $dias ?>d
                                                </span>
                                            <?php elseif ($dias <= 5): ?>
                                                <span class="badge bg-warning text-dark">
                                                    <i class="fas fa-exclamation-triangle"></i> <?= $dias ?>d
                                                </span>
                                            <?php elseif ($dias <= 7): ?>
                                                <span class="badge bg-info">
                                                    <i class="fas fa-info-circle"></i> <?= $dias ?>d
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary"><?= $dias ?>d</span>
                                            <?php endif;
                                        else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php 
                                        $dias = $item['dias_hasta_caducidad'] ?? null;
                                        if ($dias !== null && $item['lote_cantidad_activa'] > 0):
                                            if ($dias <= 3): ?>
                                                <span class="badge bg-danger">
                                                    <i class="fas fa-exclamation-triangle"></i> CRÍTICO
                                                </span>
                                            <?php elseif ($dias <= 5): ?>
                                                <span class="badge bg-warning text-dark">
                                                    <i class="fas fa-exclamation-circle"></i> URGENTE
                                                </span>
                                            <?php elseif ($dias <= 7): ?>
                                                <span class="badge bg-info">
                                                    <i class="fas fa-info-circle"></i> ALERTA
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-success">OK</span>
                                            <?php endif;
                                        else: ?>
                                            <span class="badge bg-secondary">N/A</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-success" title="Ver historial de lotes" 
                                                onclick="abrirModalVerLotes('<?= $item['idinv'] ?>', '<?= htmlspecialchars($item['producto'], ENT_QUOTES) ?>')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-primary" title="Agregar nuevo lote"
                                                onclick="abrirModalAgregarLote('<?= $item['idinv'] ?>', '<?= htmlspecialchars($item['producto'], ENT_QUOTES) ?>')">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-warning btn-sm" title="Editar"
                                                onclick="abrirModalEditar('<?= $item['idinv'] ?>', '<?= htmlspecialchars($item['producto'], ENT_QUOTES) ?>')">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <!-- Para productos perecederos se usa "Agregar Lote" en lugar de "Agregar Stock" -->
                                        <!-- El stock se actualiza automáticamente al crear lotes -->
                                        <button type="button" class="btn btn-danger btn-sm" title="Eliminar"
                                                onclick="abrirModalEliminar('<?= $item['idinv'] ?>', '<?= htmlspecialchars($item['producto'], ENT_QUOTES) ?>')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="12" class="text-center text-muted py-4">
                                    <i class="fas fa-seedling" style="font-size:2rem;"></i>
                                    <h6 class="mt-2">No hay productos perecederos</h6>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="3" class="fw-bold">Total Perecederos: <?= $total_elementos_perecederos ?? 0 ?></td>
                            <td colspan="3" class="text-end">
                                <span class="me-3">Stock Total: <span class="badge bg-primary"><?= array_sum(array_column($inventario_perecederos ?? [], 'stock')) ?></span></span>
                            </td>
                            <td colspan="6">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span>Mostrando <?= min(($offset_perecederos ?? 0) + 1, $total_elementos_perecederos ?? 0) ?> - <?= min(($offset_perecederos ?? 0) + ($elementos_por_pagina_perecederos ?? 10), $total_elementos_perecederos ?? 0) ?> de <?= $total_elementos_perecederos ?? 0 ?></span>
                                    <div class="btn-group btn-group-sm">
                                        <a href="?ctrl=cinventario&pagina_perecederos=<?= max(1, ($pagina_actual_perecederos ?? 1) - 1) ?>" 
                                           class="btn btn-outline-secondary <?= ($pagina_actual_perecederos ?? 1) <= 1 ? 'disabled' : '' ?>">
                                            <i class="fas fa-chevron-left"></i>
                                        </a>
                                        <button class="btn btn-outline-secondary active"><?= $pagina_actual_perecederos ?? 1 ?></button>
                                        <a href="?ctrl=cinventario&pagina_perecederos=<?= min($total_paginas_perecederos ?? 1, ($pagina_actual_perecederos ?? 1) + 1) ?>" 
                                           class="btn btn-outline-secondary <?= ($pagina_actual_perecederos ?? 1) >= ($total_paginas_perecederos ?? 1) ? 'disabled' : '' ?>">
                                            <i class="fas fa-chevron-right"></i>
                                        </a>
                                    </div>
                                    <span>Página <?= $pagina_actual_perecederos ?? 1 ?> de <?= $total_paginas_perecederos ?? 1 ?> | <i class="fas fa-redo-alt" style="cursor: pointer;" onclick="window.location.reload()"></i></span>
                                </div>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Sección de Productos No Perecederos (Duraderos) - ACTUALIZADO 2025-11-24 -->
    <div class="card mb-4">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0">
                <i class="fas fa-box me-2"></i>Productos No Perecederos (Duraderos)
                <span class="badge bg-dark float-end">
                    <?php echo $total_elementos_no_perecederos ?? 0; ?> productos disponibles
                </span>
            </h5>
        </div>
        <div class="card-body p-3">
            <!-- Búsqueda en tiempo real con filtros integrados -->
            <div class="mb-3">
                <div class="row g-2">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" 
                                   class="form-control" 
                                   id="buscar-no-perecederos" 
                                   placeholder="Buscar por nombre, categoría..."
                                   autocomplete="off">
                            <button class="btn btn-outline-secondary" 
                                    type="button" 
                                    id="limpiar-busqueda-no-perecederos"
                                    title="Limpiar búsqueda y filtros"
                                    style="display: none;">
                                <i class="fas fa-times"></i>
                            </button>
                            <span class="input-group-text bg-light" id="loading-no-perecederos" style="display: none;">
                                <i class="fas fa-spinner fa-spin text-primary"></i>
                            </span>
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
                <small class="text-muted mt-2 d-block">
                    <i class="fas fa-info-circle me-1"></i>
                    La búsqueda y filtros se aplican automáticamente mientras escribes
                </small>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="tabla-no-perecederos">
                    <thead class="table-light">
                        <tr>
                            <th class="sortable" data-sort="nombre" style="cursor: pointer;">
                                Producto <i class="fas fa-sort text-muted"></i>
                            </th>
                            <th class="sortable" data-sort="naturaleza" style="cursor: pointer;">
                                Naturaleza <i class="fas fa-sort text-muted"></i>
                            </th>
                            <th>Color</th>
                            <th class="sortable" data-sort="stock" style="cursor: pointer;">
                                Stock <i class="fas fa-sort text-muted"></i>
                            </th>
                            <th>P. Compra</th>
                            <th class="sortable" data-sort="precio" style="cursor: pointer;">
                                P. Venta <i class="fas fa-sort text-muted"></i>
                            </th>
                            <th>Margen %</th>
                            <th class="sortable" data-sort="valor_total" style="cursor: pointer;">
                                Ingresos Pot. <i class="fas fa-sort text-muted"></i>
                            </th>
                            <th class="sortable" data-sort="fecha_actualizacion" style="cursor: pointer;">
                                Fº Actualización <i class="fas fa-sort text-muted"></i>
                            </th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($inventario_no_perecederos)): ?>
                            <?php foreach ($inventario_no_perecederos as $item): ?>
                                <tr>
                                    <td>
                                        <div class="fw-bold"><?= htmlspecialchars($item['producto']) ?></div>
                                    </td>
                                    <td><span class="badge bg-secondary"><?= htmlspecialchars($item['naturaleza']) ?></span></td>
                                    <td>
                                        <span class="badge" style="background-color: 
                                        <?php 
                                        $colores = [
                                            'Rojo' => '#dc3545', 'Rosa' => '#ff69b4', 'Blanco' => '#f8f9fa',
                                            'Amarillo' => '#ffc107', 'Naranja' => '#fd7e14', 'Morado' => '#6f42c1',
                                            'Azul' => '#0d6efd', 'Verde' => '#198754', 'Multicolor' => '#6c757d'
                                        ];
                                        echo $colores[$item['color']] ?? '#6c757d';
                                        ?>; color: <?= in_array($item['color'], ['Blanco', 'Amarillo']) ? '#000' : '#fff' ?>;">
                                            <?= htmlspecialchars($item['color']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge <?= $item['stock'] == 0 ? 'bg-danger' : ($item['stock'] < 20 ? 'bg-warning text-dark' : 'bg-success') ?>">
                                            <?= $item['stock'] ?>
                                        </span>
                                    </td>
                                    <td class="text-muted small">$<?= number_format($item['precio_compra'] ?? 0, 2) ?></td>
                                    <td class="fw-bold text-primary">$<?= number_format($item['precio'] ?? 0, 2) ?></td>
                                    <td>
                                        <?php 
                                        $precio_compra_np = floatval($item['precio_compra'] ?? 0);
                                        $precio_venta_np = floatval($item['precio'] ?? 0);
                                        $margen_np = $precio_compra_np > 0 ? (($precio_venta_np - $precio_compra_np) / $precio_compra_np) * 100 : 0;
                                        $badge_np = $margen_np > 30 ? 'bg-success' : ($margen_np >= 10 ? 'bg-warning text-dark' : 'bg-danger');
                                        ?>
                                        <span class="badge <?= $badge_np ?>" title="Margen de ganancia">
                                            <?= number_format($margen_np, 1) ?>%
                                        </span>
                                    </td>
                                    <td class="fw-bold text-success">$<?= number_format(($item['stock'] ?? 0) * ($item['precio'] ?? 0), 2) ?></td>
                                    <td>
                                        <span class="text-muted small" title="Última actualización del producto">
                                            <i class="far fa-clock me-1"></i>
                                            <?= $item['fecha_actualizacion'] ? date('m/d/y H:i', strtotime($item['fecha_actualizacion'])) : 'N/A' ?>
                                        </span>
                                    </td>
                                    <td><?= $item['stock'] > 0 ? '<span class="badge bg-success">Disponible</span>' : '<span class="badge bg-danger">Agotado</span>' ?></td>
                                    <td>
                                        <button type="button" class="btn btn-warning btn-sm" title="Editar"
                                                onclick="abrirModalEditar('<?= $item['idinv'] ?>', '<?= htmlspecialchars($item['producto'], ENT_QUOTES) ?>')">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-info btn-sm" title="Stock"
                                                onclick="abrirModalAgregarStock('<?= $item['idinv'] ?>', '<?= htmlspecialchars($item['producto'], ENT_QUOTES) ?>')">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                        <button type="button" class="btn btn-danger btn-sm" title="Eliminar"
                                                onclick="abrirModalEliminar('<?= $item['idinv'] ?>', '<?= htmlspecialchars($item['producto'], ENT_QUOTES) ?>')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="11" class="text-center text-muted py-4">
                                    <i class="fas fa-box" style="font-size:2rem;"></i>
                                    <h6 class="mt-2">No hay productos no perecederos</h6>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="3" class="fw-bold">Total No Perecederos: <?= $total_elementos_no_perecederos ?? 0 ?></td>
                            <td colspan="3" class="text-end">
                                <span class="me-3">Stock Total: <span class="badge bg-primary"><?= array_sum(array_column($inventario_no_perecederos ?? [], 'stock')) ?></span></span>
                            </td>
                            <td colspan="3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span>Mostrando <?= min(($offset_no_perecederos ?? 0) + 1, $total_elementos_no_perecederos ?? 0) ?> - <?= min(($offset_no_perecederos ?? 0) + ($elementos_por_pagina_no_perecederos ?? 10), $total_elementos_no_perecederos ?? 0) ?> de <?= $total_elementos_no_perecederos ?? 0 ?></span>
                                    <div class="btn-group btn-group-sm">
                                        <a href="?ctrl=cinventario&pagina_no_perecederos=<?= max(1, ($pagina_actual_no_perecederos ?? 1) - 1) ?>" 
                                           class="btn btn-outline-secondary <?= ($pagina_actual_no_perecederos ?? 1) <= 1 ? 'disabled' : '' ?>">
                                            <i class="fas fa-chevron-left"></i>
                                        </a>
                                        <button class="btn btn-outline-secondary active"><?= $pagina_actual_no_perecederos ?? 1 ?></button>
                                        <a href="?ctrl=cinventario&pagina_no_perecederos=<?= min($total_paginas_no_perecederos ?? 1, ($pagina_actual_no_perecederos ?? 1) + 1) ?>" 
                                           class="btn btn-outline-secondary <?= ($pagina_actual_no_perecederos ?? 1) >= ($total_paginas_no_perecederos ?? 1) ? 'disabled' : '' ?>">
                                            <i class="fas fa-chevron-right"></i>
                                        </a>
                                    </div>
                                    <span>Página <?= $pagina_actual_no_perecederos ?? 1 ?> de <?= $total_paginas_no_perecederos ?? 1 ?> | <i class="fas fa-redo-alt" style="cursor: pointer;" onclick="window.location.reload()"></i></span>
                                </div>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
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
                                        <div class="btn-group" role="group">
                                            <button type="button" 
                                                    class="btn btn-warning btn-sm" 
                                                    onclick="editarProveedorModal(<?= $prov['id'] ?>, '<?= htmlspecialchars($prov['nombre'], ENT_QUOTES) ?>', '<?= htmlspecialchars($prov['categoria'], ENT_QUOTES) ?>', '<?= htmlspecialchars($prov['telefono'], ENT_QUOTES) ?>', '<?= htmlspecialchars($prov['email'], ENT_QUOTES) ?>', '<?= htmlspecialchars($prov['direccion'] ?? '', ENT_QUOTES) ?>', '<?= htmlspecialchars($prov['notas'] ?? '', ENT_QUOTES) ?>', '<?= htmlspecialchars($prov['estado'], ENT_QUOTES) ?>')"
                                                    title="Editar proveedor">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" 
                                                    class="btn btn-danger btn-sm btn-modal-eliminar-proveedor" 
                                                    data-proveedor-id="<?= $prov['id'] ?>" 
                                                    data-proveedor-nombre="<?= htmlspecialchars($prov['nombre']) ?>" 
                                                    title="Eliminar proveedor" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#modal-eliminar-proveedor">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
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
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="3" class="fw-bold">Total Proveedores: <?= $total_elementos_proveedores ?? 0 ?></td>
                            <td colspan="3">
                                <div class="d-flex justify-content-end align-items-center">
                                    <span class="me-3">Mostrando <?= min(($offset_proveedores ?? 0) + 1, $total_elementos_proveedores ?? 0) ?> - <?= min(($offset_proveedores ?? 0) + ($elementos_por_pagina_proveedores ?? 10), $total_elementos_proveedores ?? 0) ?> de <?= $total_elementos_proveedores ?? 0 ?></span>
                                    <div class="btn-group btn-group-sm">
                                        <a href="?ctrl=cinventario&pagina_proveedores=<?= max(1, ($pagina_actual_proveedores ?? 1) - 1) ?>" 
                                           class="btn btn-outline-secondary <?= ($pagina_actual_proveedores ?? 1) <= 1 ? 'disabled' : '' ?>">
                                            <i class="fas fa-chevron-left"></i>
                                        </a>
                                        <button class="btn btn-outline-secondary active"><?= $pagina_actual_proveedores ?? 1 ?></button>
                                        <a href="?ctrl=cinventario&pagina_proveedores=<?= min($total_paginas_proveedores ?? 1, ($pagina_actual_proveedores ?? 1) + 1) ?>" 
                                           class="btn btn-outline-secondary <?= ($pagina_actual_proveedores ?? 1) >= ($total_paginas_proveedores ?? 1) ? 'disabled' : '' ?>">
                                            <i class="fas fa-chevron-right"></i>
                                        </a>
                                    </div>
                                    <span class="ms-3">Página <?= $pagina_actual_proveedores ?? 1 ?> de <?= $total_paginas_proveedores ?? 1 ?></span>
                                </div>
                            </td>
                        </tr>
                    </tfoot>
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
</div>

<script src="/Original-Floraltech/assets/js/inventario.js"></script>
<script src="/Original-Floraltech/assets/js/inventario_modal_handler.js"></script>
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
                                <select class="form-select" name="nuevo_producto_proveedor_id" id="nuevo_producto_proveedor_id">
                                    <option value="">Selecciona proveedor...</option>
                                    <?php if (!empty($todos_proveedores)): ?>
                                        <?php foreach ($todos_proveedores as $prov): ?>
                                            <option value="<?= htmlspecialchars($prov['id']) ?>"><?= htmlspecialchars($prov['nombre']) ?></option>
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
                        
                        <!-- Stock Inicial - Solo para NO perecederos -->
                        <div class="col-md-6" id="campo_stock_inicial">
                            <label class="form-label"><i class="fas fa-boxes me-1"></i>Stock Inicial *</label>
                            <input type="number" class="form-control" name="stock" id="input_stock" min="0" required placeholder="Ej: 50">
                            <small class="text-muted">Cantidad inicial en inventario</small>
                        </div>
                        
                        <!-- Alerta para productos perecederos -->
                        <div class="col-12" id="alerta_perecedero" style="display: none;">
                            <div class="alert alert-info mb-0">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Producto Perecedero:</strong> El stock se gestionará mediante lotes. Después de crear el producto, usa el botón <strong>"+ Agregar Lote"</strong> para registrar cada ingreso de mercancía.
                            </div>
                        </div>
                        
                        <!-- Precio Compra -->
                        <div class="col-md-6">
                            <label class="form-label"><i class="fas fa-shopping-cart me-1"></i>Precio de Compra *</label>
                            <input type="number" step="0.01" class="form-control" name="precio_compra" id="precio_compra_agregar" min="0" required placeholder="0.00">
                            <small class="text-muted">Lo que pagas al proveedor</small>
                        </div>
                        
                        <!-- Precio Venta -->
                        <div class="col-md-6">
                            <label class="form-label"><i class="fas fa-dollar-sign me-1"></i>Precio de Venta *</label>
                            <input type="number" step="0.01" class="form-control" name="precio" id="precio_venta_agregar" min="0" required placeholder="0.00">
                            <small class="text-muted">Precio de venta al cliente</small>
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

<!-- Modal: Producto Duplicado - Confirmación -->
<div class="modal fade" id="modal-producto-duplicado" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content border-warning">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle me-2"></i>Producto Ya Existe
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning mb-3">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong id="duplicado-mensaje">Ya existe un producto con este nombre en el inventario.</strong>
                </div>
                
                <div class="card bg-light mb-3">
                    <div class="card-body">
                        <h6 class="card-title mb-2">Información del producto existente:</h6>
                        <p class="mb-1"><strong>Stock actual:</strong> <span id="duplicado-stock" class="badge bg-info">0</span> unidades</p>
                        <p class="mb-0 text-muted small">Última actualización: <span id="duplicado-fecha">-</span></p>
                    </div>
                </div>
                
                <p class="mb-3">¿Qué deseas hacer?</p>
                
                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-primary" onclick="irAAgregarStock()">
                        <i class="fas fa-plus-circle me-2"></i>Agregar Stock al Producto Existente
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </button>
                </div>
                
                <input type="hidden" id="duplicado-producto-id" value="">
                <input type="hidden" id="duplicado-producto-nombre" value="">
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
                            <select class="form-select" name="categoria_proveedor" id="categoria_proveedor_nuevo" required>
                                <option value="">Seleccionar categoría...</option>
                                <option value="Flores Naturales">Flores Naturales</option>
                                <option value="Flores Artificiales">Flores Artificiales</option>
                                <option value="Plantas">Plantas y Arbustos</option>
                                <option value="Chocolates">Chocolates y Dulces</option>
                                <option value="Caramelos">Caramelos Gourmet</option>
                                <option value="Fotografías">Servicios de Fotografía</option>
                                <option value="Globos">Globos y Decoraciones</option>
                                <option value="Tarjetas">Tarjetas y Papelería</option>
                                <option value="Perfumes">Perfumes y Fragancias</option>
                                <option value="Velas">Velas Aromáticas</option>
                                <option value="Accesorios">Accesorios Florales</option>
                                <option value="Macetas">Macetas y Contenedores</option>
                                <option value="Fertilizantes">Fertilizantes y Nutrientes</option>
                                <option value="Herramientas">Herramientas de Jardinería</option>
                                <option value="Cestas">Cestas y Canastas</option>
                                <option value="Lazos">Lazos y Cintas</option>
                                <option value="Empaques">Materiales de Empaque</option>
                                <option value="Preservantes">Preservantes Florales</option>
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
                        <!-- NUEVO: Productos que provee (se filtrará dinámicamente) -->
                        <div class="col-12" id="productos_proveedor_container">
                            <label class="form-label"><i class="fas fa-boxes me-1"></i>Productos que provee *</label>
                            <select class="form-select" name="productos_proveedor[]" id="productos_proveedor_select" multiple size="8">
                                <option value="">Seleccione una categoría primero...</option>
                            </select>
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Mantén presionada <kbd>Ctrl</kbd> (Windows) o <kbd>Cmd</kbd> (Mac) para seleccionar varios productos.
                                <br>Puedes seleccionar todos los productos que este proveedor suministra.
                            </small>
                        </div>
                        <!-- NUEVO: Notas o comentarios -->
                        <div class="col-12">
                            <label class="form-label"><i class="fas fa-sticky-note me-1"></i>Notas / Comentarios</label>
                            <textarea class="form-control" name="notas_proveedor" rows="2" placeholder="Observaciones, condiciones especiales, términos de entrega, etc."></textarea>
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
                        
                        <!-- Precio Compra -->
                        <div class="col-md-4">
                            <label class="form-label"><i class="fas fa-shopping-cart me-1"></i>Precio Compra</label>
                            <input type="number" class="form-control" name="precio_compra" id="editar_precio_compra" min="0" step="0.01" required>
                            <small class="text-muted">Costo de adquisición</small>
                        </div>
                        
                        <!-- Precio Venta -->
                        <div class="col-md-4">
                            <label class="form-label"><i class="fas fa-dollar-sign me-1"></i>Precio Venta</label>
                            <input type="number" class="form-control" name="precio" id="editar_precio" min="0" step="0.01" required>
                            <small class="text-muted">Precio al cliente</small>
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
                    <input type="hidden" name="id" id="stock_producto_id">
                    
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

<!-- Modal Editar Proveedor -->
<div class="modal fade" id="modal-editar-proveedor" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Editar Proveedor</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="?ctrl=cinventario" id="form-editar-proveedor">
                    <input type="hidden" name="accion" value="editar_proveedor">
                    <input type="hidden" name="proveedor_id" id="editar_proveedor_id">
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label"><i class="fas fa-building me-1"></i>Nombre del Proveedor *</label>
                            <input type="text" class="form-control" name="nombre_proveedor" id="editar_nombre_proveedor" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label"><i class="fas fa-tags me-1"></i>Categoría *</label>
                            <select class="form-select" name="categoria_proveedor" id="editar_categoria_proveedor" required>
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
                            <input type="tel" class="form-control" name="telefono_proveedor" id="editar_telefono_proveedor">
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label"><i class="fas fa-envelope me-1"></i>Email</label>
                            <input type="email" class="form-control" name="email_proveedor" id="editar_email_proveedor">
                        </div>
                        
                        <div class="col-12">
                            <label class="form-label"><i class="fas fa-map-marker-alt me-1"></i>Dirección</label>
                            <textarea class="form-control" name="direccion_proveedor" id="editar_direccion_proveedor" rows="2"></textarea>
                        </div>
                        
                        <div class="col-12">
                            <label class="form-label"><i class="fas fa-sticky-note me-1"></i>Notas / Comentarios</label>
                            <textarea class="form-control" name="notas_proveedor" id="editar_notas_proveedor" rows="2"></textarea>
                        </div>
                        
                        <div class="col-12">
                            <label class="form-label"><i class="fas fa-toggle-on me-1"></i>Estado</label>
                            <select class="form-select" name="estado_proveedor" id="editar_estado_proveedor">
                                <option value="activo">Activo</option>
                                <option value="inactivo">Inactivo</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Cancelar
                </button>
                <button type="button" class="btn btn-warning" onclick="verificarYEnviarProveedor()">
                    <i class="fas fa-save me-1"></i>Guardar Cambios
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Eliminar Proveedor -->
<div class="modal fade" id="modal-eliminar-proveedor" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fas fa-trash me-2"></i>Eliminar Proveedor</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="?ctrl=Cinventario" id="form-eliminar-proveedor">
                    <input type="hidden" name="accion" value="eliminar_proveedor">
                    <input type="hidden" name="proveedor_id" id="eliminar_proveedor_id">
                    <div class="text-center">
                        <i class="fas fa-exclamation-triangle text-danger" style="font-size: 3rem;"></i>
                        <h5 class="mt-3">¿Estás seguro?</h5>
                        <p>¿Deseas eliminar el proveedor <span id="eliminar_nombre_proveedor" class="fw-bold text-danger"></span>?</p>
                        <div class="alert alert-warning">
                            <i class="fas fa-info-circle me-2"></i>
                            Esta acción no se puede deshacer.
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Cancelar
                </button>
                <button type="submit" form="form-eliminar-proveedor" class="btn btn-danger">
                    <i class="fas fa-trash me-1"></i>Eliminar Proveedor
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

<!-- Modal Eliminar Producto -->
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
                    <input type="hidden" name="id" id="eliminar_producto_id">
                    <div class="text-center">
                        <i class="fas fa-exclamation-triangle text-danger" style="font-size: 3rem;"></i>
                        <h5 class="mt-3">¿Estás seguro?</h5>
                        <p>¿Deseas eliminar el producto <span id="eliminar_nombre_producto" class="fw-bold text-danger"></span> del inventario?</p>
                        <div class="alert alert-warning">
                            <i class="fas fa-info-circle me-2"></i>
                            Esta acción no se puede deshacer.
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Cancelar
                </button>
                <button type="button" class="btn btn-danger" id="btn-confirmar-eliminar">
                    <i class="fas fa-trash me-1"></i>Eliminar Producto
                </button>
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

// Función abrirModalEliminar definida más adelante en el archivo (línea ~2462)

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
    // Comentado: Las cards del dashboard ya muestran esta información
    // setTimeout(verificarStockBajo, 2000);
    
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

// Función para cambiar tipo de producto
function cambiarTipoProducto() {
    const tipoProducto = document.getElementById('tipo_producto').value;
    const seccionFlor = document.getElementById('seccion_flor');
    const florSelect = document.getElementById('flor_select');
    const campoStock = document.getElementById('campo_stock_inicial');
    const inputStock = document.getElementById('input_stock');
    const alertaPereceder = document.getElementById('alerta_perecedero');
    
    console.log('Tipo de producto seleccionado:', tipoProducto);
    
    if (tipoProducto === 'flor') {
        if (seccionFlor) seccionFlor.style.display = 'block';
        if (florSelect) florSelect.removeAttribute('required');
        // Ocultar campo de stock para flores (perecedero)
        if (campoStock) {
            campoStock.style.display = 'none';
            console.log('Ocultando campo de stock');
        }
        if (inputStock) {
            inputStock.removeAttribute('required');
            inputStock.value = '0'; // Stock inicial = 0 para perecederos
        }
        if (alertaPereceder) {
            alertaPereceder.style.display = 'block';
            console.log('Mostrando alerta perecedero');
        }
    } else {
        if (seccionFlor) seccionFlor.style.display = 'none';
        if (florSelect) {
            florSelect.value = '';
            florSelect.removeAttribute('required');
        }
        // Mostrar campo de stock para productos no perecederos
        if (campoStock) {
            campoStock.style.display = 'block';
            console.log('Mostrando campo de stock');
        }
        if (inputStock) {
            inputStock.setAttribute('required', 'required');
            inputStock.value = '';
        }
        if (alertaPereceder) {
            alertaPereceder.style.display = 'none';
            console.log('Ocultando alerta perecedero');
        }
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
    console.log('🗑️ Abriendo modal eliminar - ID:', id, 'Nombre:', nombre);
    
    try {
        // Configurar valores en el modal
        const inputId = document.getElementById('eliminar_producto_id');
        const spanNombre = document.getElementById('eliminar_nombre_producto');
        
        if (inputId) {
            inputId.value = id;
            console.log('✅ ID asignado:', id);
        } else {
            console.error('❌ Input eliminar_producto_id no encontrado');
        }
        
        if (spanNombre) {
            spanNombre.textContent = nombre;
            console.log('✅ Nombre asignado:', nombre);
        } else {
            console.error('❌ Span eliminar_nombre_producto no encontrado');
        }
        
        // Abrir el modal
        const modalElement = document.getElementById('modal-eliminar-producto');
        if (modalElement) {
            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                const modal = new bootstrap.Modal(modalElement);
                modal.show();
                console.log('✅ Modal mostrado con Bootstrap');
            } else {
                modalElement.style.display = 'block';
                modalElement.classList.add('show');
                console.log('✅ Modal mostrado manualmente');
            }
        } else {
            console.error('❌ Modal modal-eliminar-producto no encontrado');
            // Fallback a confirm
            if (confirm('¿Eliminar "' + nombre + '"?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '?ctrl=cinventario';
                form.innerHTML = '<input type="hidden" name="accion" value="eliminar_producto"><input type="hidden" name="producto_id" value="' + id + '">';
                document.body.appendChild(form);
                form.submit();
            }
        }
    } catch (error) {
        console.error('💥 Error al abrir modal eliminar:', error);
        alert('Error al abrir modal: ' + error.message);
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
    // Botón Exportar Excel
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
            
            console.log('✅ Botón exportar Excel configurado correctamente');
        }
    }, 1000);
    
    // Botón Exportar PDF
    setTimeout(function() {
        const btnExportarPDF = document.getElementById('btn-exportar-pdf');
        
        if (btnExportarPDF) {
            btnExportarPDF.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Modal para seleccionar tipo de exportación
                const tipoExport = confirm('¿Desea exportar TODO el inventario?\\n\\nOK = Todo\\nCancelar = Seleccionar tipo');
                
                let tipo = 'todos';
                if (!tipoExport) {
                    const seleccion = prompt('Seleccione tipo:\\n1 = Perecederos\\n2 = No Perecederos\\n3 = Todos', '3');
                    if (seleccion === '1') tipo = 'perecedero';
                    else if (seleccion === '2') tipo = 'no_perecedero';
                    else tipo = 'todos';
                }
                
                // Feedback visual
                const originalText = btnExportarPDF.innerHTML;
                btnExportarPDF.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generando PDF...';
                btnExportarPDF.disabled = true;
                
                // Descargar PDF
                window.location.href = `?ctrl=Cinventario&action=exportarInventarioPDF&tipo=${tipo}`;
                
                // Restaurar botón
                setTimeout(() => {
                    btnExportarPDF.innerHTML = originalText;
                    btnExportarPDF.disabled = false;
                }, 3000);
            });
            
            console.log('✅ Botón exportar PDF configurado correctamente');
        }
    }, 1000);
});

// Event listener para el botón de eliminar producto
document.addEventListener('DOMContentLoaded', function() {
    console.log('🔍 Configurando botón de eliminar producto...');
    
    const btnEliminar = document.getElementById('btn-confirmar-eliminar');
    console.log('🔘 Botón encontrado:', btnEliminar);
    
    if (btnEliminar) {
        btnEliminar.addEventListener('click', function(e) {
            console.log('🖱️ Click en botón eliminar detectado');
            
            const productoId = document.getElementById('eliminar_producto_id').value;
            console.log('🗑️ Eliminando producto ID:', productoId);
            
            if (!productoId) {
                alert('Error: No se pudo identificar el producto a eliminar');
                return;
            }
            
            // Mostrar loader
            const originalText = btnEliminar.innerHTML;
            btnEliminar.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Eliminando...';
            btnEliminar.disabled = true;
            
            // Crear FormData
            const formData = new FormData();
            formData.append('accion', 'eliminar_producto');
            formData.append('id', productoId);
            
            // Enviar petición AJAX
            fetch('?ctrl=cinventario&accion=eliminar_producto', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error en la respuesta del servidor');
                }
                return response.json();
            })
            .then(data => {
                console.log('📥 Respuesta del servidor:', data);
                
                if (data.success) {
                    // Cerrar modal
                    const modalElement = document.getElementById('modal-eliminar-producto');
                    if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                        const modal = bootstrap.Modal.getInstance(modalElement);
                        if (modal) modal.hide();
                    }
                    
                    // Mostrar mensaje de éxito
                    alert('✅ ' + data.message);
                    
                    // Recargar página
                    location.reload();
                } else {
                    // Mostrar error
                    alert('❌ ' + data.message);
                    
                    // Restaurar botón
                    btnEliminar.innerHTML = originalText;
                    btnEliminar.disabled = false;
                }
            })
            .catch(error => {
                console.error('💥 Error:', error);
                alert('Error de conexión al servidor');
                
                // Restaurar botón
                btnEliminar.innerHTML = originalText;
                btnEliminar.disabled = false;
            });
        });
        
        console.log('✅ Event listener para botón eliminar configurado');
    } else {
        console.error('❌ No se encontró el botón eliminar');
    }
});

</script>

<!-- Script para filtrar productos según categoría del proveedor -->
<script>
// Hacer productosInventario global para que esté disponible en todas las funciones
let productosInventario = <?= json_encode($productos_inventario ?? []) ?>;

document.addEventListener('DOMContentLoaded', function() {
    // Función para filtrar productos según categoría
    function filtrarProductosPorCategoria(categoria, productos) {
        switch(categoria) {
            case 'Flores Naturales':
                // Solo flores con naturaleza "Natural" o "Flor natural"
                return productos.filter(p => {
                    const nat = (p.naturaleza || '').toLowerCase();
                    return nat.includes('natural') || nat.includes('flor');
                });
                
            case 'Flores Artificiales':
                // Solo flores artificiales
                return productos.filter(p => {
                    const nat = (p.naturaleza || '').toLowerCase();
                    return nat.includes('artificial');
                });
                
            case 'Plantas':
                // Plantas de interior, suculentas, arbustos
                return productos.filter(p => {
                    const nombre = (p.producto || '').toLowerCase();
                    const nat = (p.naturaleza || '').toLowerCase();
                    return nombre.includes('planta') || nombre.includes('suculenta') || 
                           nombre.includes('arbusto') || nat.includes('planta');
                });
                
            case 'Chocolates':
                // Productos comestibles tipo chocolate
                return productos.filter(p => {
                    const nombre = (p.producto || '').toLowerCase();
                    const nat = (p.naturaleza || '').toLowerCase();
                    return nombre.includes('chocolate') || nombre.includes('dulce') || 
                           nat.includes('chocolate') || nat.includes('comestible');
                });
                
            case 'Caramelos':
                // Productos tipo caramelo o dulce
                return productos.filter(p => {
                    const nombre = (p.producto || '').toLowerCase();
                    return nombre.includes('caramelo') || nombre.includes('goma') || 
                           nombre.includes('candy');
                });
                
            case 'Fotografías':
                // Servicios de fotografía o productos relacionados
                return productos.filter(p => {
                    const nombre = (p.producto || '').toLowerCase();
                    return nombre.includes('foto') || nombre.includes('cámara') || 
                           nombre.includes('album');
                });
                
            case 'Globos':
                // Globos y decoraciones inflables
                return productos.filter(p => {
                    const nombre = (p.producto || '').toLowerCase();
                    return nombre.includes('globo') || nombre.includes('helio');
                });
                
            case 'Tarjetas':
                // Tarjetas y papelería
                return productos.filter(p => {
                    const nombre = (p.producto || '').toLowerCase();
                    return nombre.includes('tarjeta') || nombre.includes('papel') || 
                           nombre.includes('sobre');
                });
                
            case 'Perfumes':
                // Perfumes y fragancias
                return productos.filter(p => {
                    const nombre = (p.producto || '').toLowerCase();
                    return nombre.includes('perfume') || nombre.includes('fragancia') || 
                           nombre.includes('aroma');
                });
                
            case 'Velas':
                // Velas aromáticas
                return productos.filter(p => {
                    const nombre = (p.producto || '').toLowerCase();
                    return nombre.includes('vela') || nombre.includes('aromatica');
                });
                
            case 'Accesorios':
                // Accesorios florales (lazos, cintas, etc.)
                return productos.filter(p => {
                    const nombre = (p.producto || '').toLowerCase();
                    const nat = (p.naturaleza || '').toLowerCase();
                    return nombre.includes('accesorio') || nombre.includes('lazo') || 
                           nombre.includes('cinta') || nat.includes('accesorio');
                });
                
            case 'Macetas':
                // Macetas y contenedores
                return productos.filter(p => {
                    const nombre = (p.producto || '').toLowerCase();
                    return nombre.includes('maceta') || nombre.includes('contenedor') || 
                           nombre.includes('florero');
                });
                
            case 'Fertilizantes':
                // Fertilizantes y nutrientes
                return productos.filter(p => {
                    const nombre = (p.producto || '').toLowerCase();
                    return nombre.includes('fertilizante') || nombre.includes('abono') || 
                           nombre.includes('nutriente');
                });
                
            case 'Herramientas':
                // Herramientas de jardinería
                return productos.filter(p => {
                    const nombre = (p.producto || '').toLowerCase();
                    return nombre.includes('tijera') || nombre.includes('pala') || 
                           nombre.includes('herramienta') || nombre.includes('rastrillo');
                });
                
            case 'Cestas':
                // Cestas y canastas
                return productos.filter(p => {
                    const nombre = (p.producto || '').toLowerCase();
                    return nombre.includes('cesta') || nombre.includes('canasta') || 
                           nombre.includes('caja');
                });
                
            case 'Lazos':
                // Lazos y cintas decorativas
                return productos.filter(p => {
                    const nombre = (p.producto || '').toLowerCase();
                    return nombre.includes('lazo') || nombre.includes('cinta') || 
                           nombre.includes('moño');
                });
                
            case 'Empaques':
                // Materiales de empaque
                return productos.filter(p => {
                    const nombre = (p.producto || '').toLowerCase();
                    return nombre.includes('empaque') || nombre.includes('celofán') || 
                           nombre.includes('papel') || nombre.includes('bolsa');
                });
                
            case 'Preservantes':
                // Preservantes florales
                return productos.filter(p => {
                    const nombre = (p.producto || '').toLowerCase();
                    return nombre.includes('preservante') || nombre.includes('conservante') || 
                           nombre.includes('nutriente');
                });
                
            default:
                // Si no hay categoría específica, mostrar todos
                return productos;
        }
    }
    
    const categoriaSelect = document.getElementById('categoria_proveedor_nuevo');
    const productosContainer = document.getElementById('productos_proveedor_container');
    const productosSelect = document.getElementById('productos_proveedor_select');
    const formNuevoProveedor = document.getElementById('form-nuevo-proveedor');
    
    if (categoriaSelect && productosSelect) {
        categoriaSelect.addEventListener('change', function() {
            const categoriaSeleccionada = this.value;
            
            if (!categoriaSeleccionada) {
                productosSelect.innerHTML = '<option value="">Seleccione una categoría primero...</option>';
                return;
            }
            
            // Limpiar opciones anteriores
            productosSelect.innerHTML = '';
            
            // Filtrar productos según la categoría
            const productosFiltrados = filtrarProductosPorCategoria(categoriaSeleccionada, productosInventario);
            
            // Array para almacenar IDs de productos a pre-seleccionar
            const productosPreseleccionados = [];
            
            // Agregar opciones filtradas
            if (productosFiltrados.length > 0) {
                // Agregar opción para seleccionar todos primero
                const optionTodos = document.createElement('option');
                optionTodos.value = 'todos';
                optionTodos.textContent = `✓ Seleccionar todos (${productosFiltrados.length} productos)`;
                optionTodos.style.fontWeight = 'bold';
                optionTodos.style.backgroundColor = '#e3f2fd';
                optionTodos.style.color = '#1976d2';
                productosSelect.appendChild(optionTodos);
                
                // Agregar productos filtrados
                productosFiltrados.forEach(prod => {
                    const option = document.createElement('option');
                    option.value = prod.idinv;
                    const stockBadge = prod.stock > 0 ? `✓ ${prod.stock}` : '⚠ 0';
                    option.textContent = `${prod.producto} | ${prod.naturaleza || 'Sin categoría'} | Stock: ${stockBadge}`;
                    productosSelect.appendChild(option);
                    
                    // Pre-seleccionar productos según categoría
                    productosPreseleccionados.push(prod.idinv);
                });
                
                // Pre-seleccionar automáticamente los productos filtrados
                setTimeout(() => {
                    productosPreseleccionados.forEach(id => {
                        const option = productosSelect.querySelector(`option[value="${id}"]`);
                        if (option) option.selected = true;
                    });
                }, 100);
                
            } else {
                // Si no hay productos filtrados, mostrar TODOS los productos disponibles
                const optionInfo = document.createElement('option');
                optionInfo.value = '';
                optionInfo.disabled = true;
                optionInfo.textContent = `ℹ️ No hay productos específicos de "${categoriaSeleccionada}", mostrando todos:`;
                optionInfo.style.color = '#ff9800';
                optionInfo.style.fontWeight = 'bold';
                productosSelect.appendChild(optionInfo);
                
                // Agregar opción para seleccionar todos
                const optionTodos = document.createElement('option');
                optionTodos.value = 'todos';
                optionTodos.textContent = `✓ Seleccionar todos (${productosInventario.length} productos)`;
                optionTodos.style.fontWeight = 'bold';
                optionTodos.style.backgroundColor = '#fff3e0';
                optionTodos.style.color = '#f57c00';
                productosSelect.appendChild(optionTodos);
                
                // Mostrar todos los productos
                productosInventario.forEach(prod => {
                    const option = document.createElement('option');
                    option.value = prod.idinv;
                    const stockBadge = prod.stock > 0 ? `✓ ${prod.stock}` : '⚠ 0';
                    option.textContent = `${prod.producto} | ${prod.naturaleza || 'Sin categoría'} | Stock: ${stockBadge}`;
                    productosSelect.appendChild(option);
                });
                
                productosSelect.required = true;
            }
            
            console.log(`✅ Categoría: ${categoriaSeleccionada}, Productos filtrados: ${productosFiltrados.length}`);
        });
        
        // Manejar selección de "todos"
        productosSelect.addEventListener('change', function() {
            const selectedOptions = Array.from(this.selectedOptions);
            
            if (selectedOptions.find(opt => opt.value === 'todos')) {
                // Seleccionar todos excepto "todos"
                Array.from(this.options).forEach(opt => {
                    if (opt.value !== 'todos' && opt.value !== '') {
                        opt.selected = true;
                    } else if (opt.value === 'todos') {
                        opt.selected = false;
                    }
                });
            }
        });
        
        // Validar formulario antes de enviar
        if (formNuevoProveedor) {
            formNuevoProveedor.addEventListener('submit', function(e) {
                const selectedProducts = Array.from(productosSelect.selectedOptions)
                    .filter(opt => opt.value !== '' && opt.value !== 'todos');
                
                if (selectedProducts.length === 0) {
                    e.preventDefault();
                    alert('⚠️ Por favor selecciona al menos un producto que este proveedor suministra.');
                    productosSelect.focus();
                    return false;
                }
                
                console.log(`✅ Enviando proveedor con ${selectedProducts.length} productos seleccionados`);
            });
        }
        
        console.log('✅ Filtro dinámico de productos por categoría configurado');
        console.log(`📦 Total de productos en inventario: ${productosInventario.length}`);
    }
});

// Función para abrir modal de editar con datos pre-cargados
function abrirModalEditar(productoId, productoNombre) {
    console.log('🔧 Abriendo modal editar para:', productoId, productoNombre);
    console.log('📊 productosInventario disponible:', productosInventario);
    
    // Buscar el producto en el array de inventario
    const producto = productosInventario.find(p => p.idinv == productoId);
    
    if (producto) {
        console.log('✅ Producto encontrado:', producto);
        
        // Llenar los campos del formulario
        document.getElementById('editar_producto_id').value = producto.idinv;
        document.getElementById('editar_nombre_producto').value = producto.producto || '';
        document.getElementById('editar_stock').value = producto.stock || 0;
        document.getElementById('editar_precio_compra').value = producto.precio_compra || 0;
        document.getElementById('editar_precio').value = producto.precio || 0;
        document.getElementById('editar_color').value = producto.color || '';
        document.getElementById('editar_naturaleza').value = producto.naturaleza || '';
        document.getElementById('editar_estado').value = producto.estado || 'Disponible';
        
        // Tipo de producto (si existe)
        const tipoSelect = document.getElementById('editar_tipo_producto');
        if (tipoSelect && producto.tipo) {
            tipoSelect.value = producto.tipo;
        }
        
        // Abrir el modal con Bootstrap
        const modalElement = document.getElementById('modal-editar-producto');
        if (modalElement && typeof bootstrap !== 'undefined') {
            const modal = new bootstrap.Modal(modalElement);
            modal.show();
            console.log('✅ Modal abierto correctamente');
        } else {
            console.error('❌ No se pudo abrir el modal - Bootstrap o elemento no encontrado');
        }
    } else {
        console.error('❌ Producto no encontrado en array:', productoId);
        console.log('Array de productos:', productosInventario);
        alert('Error: No se pudo cargar la información del producto');
    }
}

// Función para abrir modal de agregar stock
function abrirModalAgregarStock(productoId, productoNombre) {
    console.log('📦 Abriendo modal agregar stock para:', productoId, productoNombre);
    
    // Buscar el producto en el array
    const producto = productosInventario.find(p => p.idinv == productoId);
    
    document.getElementById('stock_producto_id').value = productoId;
    document.getElementById('stock_nombre_producto').textContent = productoNombre;
    
    // Mostrar stock actual si está disponible
    if (producto) {
        document.getElementById('stock_actual').textContent = producto.stock || 0;
    }
    
    const modal = new bootstrap.Modal(document.getElementById('modal-agregar-stock'));
    modal.show();
}

// Función para abrir modal de eliminar
function abrirModalEliminar(productoId, productoNombre) {
    console.log('🗑️ Abriendo modal eliminar para:', productoId, productoNombre);
    
    document.getElementById('eliminar_producto_id').value = productoId;
    document.getElementById('eliminar_nombre_producto').textContent = productoNombre;
    
    const modalElement = document.getElementById('modal-eliminar-producto');
    if (modalElement && typeof bootstrap !== 'undefined') {
        const modal = new bootstrap.Modal(modalElement);
        modal.show();
        console.log('✅ Modal eliminar abierto');
    } else {
        console.error('❌ No se pudo abrir modal eliminar');
    }
}

// Función para abrir modal editar proveedor
// Función para abrir modal editar proveedor
function editarProveedorModal(id, nombre, categoria, telefono, email, direccion, notas, estado) {
    console.log('✏️ Editando proveedor:', id, nombre);
    
    // Llenar el formulario
    window._proveedorIdEditar = id;
    document.getElementById('editar_proveedor_id').value = id;
    document.getElementById('editar_nombre_proveedor').value = nombre;
    document.getElementById('editar_categoria_proveedor').value = categoria;
    document.getElementById('editar_telefono_proveedor').value = telefono;
    document.getElementById('editar_email_proveedor').value = email;
    document.getElementById('editar_direccion_proveedor').value = direccion;
    document.getElementById('editar_notas_proveedor').value = notas;
    document.getElementById('editar_estado_proveedor').value = estado;
    
    console.log('✅ ID asignado:', document.getElementById('editar_proveedor_id').value);
    
    // Abrir el modal
    const modal = new bootstrap.Modal(document.getElementById('modal-editar-proveedor'));
    modal.show();
}

// Función para verificar datos antes de enviar
function verificarYEnviarProveedor() {
    // Reasignar el ID justo antes de enviar, por seguridad
    if (window._proveedorIdEditar) {
        document.getElementById('editar_proveedor_id').value = window._proveedorIdEditar;
    }
    const id = document.getElementById('editar_proveedor_id').value;
    const nombre = document.getElementById('editar_nombre_proveedor').value;
    
    console.log('📤 Intentando enviar formulario editar proveedor');
    console.log('ID:', id);
    console.log('Nombre:', nombre);
    
    if (!id || id.trim() === '') {
        console.error('❌ ERROR: ID de proveedor está vacío!');
        alert('Error: No se pudo identificar el proveedor. Por favor, cierra el modal e intenta de nuevo.');
        return false;
    }
    
    console.log('✅ ID válido, enviando formulario...');
    document.getElementById('form-editar-proveedor').submit();
}

// Event listener para botones de editar proveedor
document.addEventListener('DOMContentLoaded', function() {
    // Escuchar clicks en botones de editar proveedor
    document.addEventListener('click', function(e) {
        // Buscar el botón, incluso si se hizo click en el ícono dentro del botón
        const btn = e.target.closest('.btn-modal-editar-proveedor');
        if (btn) {
            e.preventDefault(); // Prevenir comportamiento por defecto
            console.log('✏️ Botón editar proveedor clickeado');
            
            // Obtener todos los datos del botón
            const id = btn.dataset.proveedorId;
            const nombre = btn.dataset.proveedorNombre;
            const categoria = btn.dataset.proveedorCategoria;
            const telefono = btn.dataset.proveedorTelefono;
            const email = btn.dataset.proveedorEmail;
            const direccion = btn.dataset.proveedorDireccion;
            const notas = btn.dataset.proveedorNotas;
            const estado = btn.dataset.proveedorEstado;
            
            console.log('Datos del proveedor:', {id, nombre, categoria, telefono, email, direccion, notas, estado});
            
            // Llenar el formulario
            document.getElementById('editar_proveedor_id').value = id || '';
            document.getElementById('editar_nombre_proveedor').value = nombre || '';
            document.getElementById('editar_categoria_proveedor').value = categoria || '';
            document.getElementById('editar_telefono_proveedor').value = telefono || '';
            document.getElementById('editar_email_proveedor').value = email || '';
            document.getElementById('editar_direccion_proveedor').value = direccion || '';
            document.getElementById('editar_notas_proveedor').value = notas || '';
            document.getElementById('editar_estado_proveedor').value = estado || 'activo';
            
            console.log('✅ Formulario llenado correctamente con ID:', id);
            
            // Abrir el modal manualmente
            const modalElement = document.getElementById('modal-editar-proveedor');
            if (modalElement) {
                const modal = new bootstrap.Modal(modalElement);
                modal.show();
                console.log('✅ Modal abierto');
            } else {
                console.error('❌ No se encontró el modal');
            }
        }
    });
});

// Función para abrir modal eliminar proveedor
function abrirModalEliminarProveedor(proveedorId, proveedorNombre) {
    console.log('🗑️ Abriendo modal eliminar proveedor:', proveedorId, proveedorNombre);
    
    document.getElementById('eliminar_proveedor_id').value = proveedorId;
    document.getElementById('eliminar_proveedor_nombre').textContent = proveedorNombre;
    
    const modal = new bootstrap.Modal(document.getElementById('modal-eliminar-proveedor'));
    modal.show();
}

// ========== FUNCIONES PARA GESTIÓN DE LOTES ==========

// Función para abrir modal ver lotes
function abrirModalVerLotes(productoId, productoNombre) {
    console.log('👁️ Abriendo modal ver lotes para:', productoId, productoNombre);
    
    document.getElementById('ver_lotes_producto_nombre').textContent = productoNombre;
    document.getElementById('ver_lotes_producto_id').value = productoId;
    
    // Cargar lotes con AJAX
    fetch(`?ctrl=Clotes&action=obtenerLotes&inv_idinv=${productoId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                mostrarLotesEnTabla(data.lotes, data.resumen);
                const modal = new bootstrap.Modal(document.getElementById('modal-ver-lotes'));
                modal.show();
            } else {
                alert('Error al cargar lotes: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al cargar lotes');
        });
}

// Función auxiliar para recargar lotes de un producto
function cargarLotesProducto(productoId) {
    fetch(`?ctrl=Clotes&action=obtenerLotes&inv_idinv=${productoId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                mostrarLotesEnTabla(data.lotes, data.resumen);
            } else {
                alert('Error al cargar lotes: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al cargar lotes');
        });
}

// Función para mostrar lotes en la tabla
function mostrarLotesEnTabla(lotes, resumen) {
    const tbody = document.getElementById('tabla-lotes-body');
    tbody.innerHTML = '';
    
    if (lotes.length === 0) {
        tbody.innerHTML = '<tr><td colspan="8" class="text-center text-muted">No hay lotes registrados</td></tr>';
        return;
    }
    
    lotes.forEach(lote => {
        const estadoBadge = {
            'activo': '<span class="badge bg-success">Activo</span>',
            'vendido': '<span class="badge bg-info">Vendido</span>',
            'caducado': '<span class="badge bg-danger">Caducado</span>',
            'devuelto': '<span class="badge bg-warning">Devuelto</span>'
        };
        
        // Calcular días restantes y determinar alerta de caducidad
        const diasRestantes = calcularDiasRestantes(lote.fecha_caducidad);
        let alertaCaducidad = '';
        let iconoAlerta = '';
        
        // Solo mostrar alertas si el lote está activo, tiene cantidad disponible y está próximo a caducar
        if (lote.estado === 'activo' && parseInt(lote.cantidad) > 0 && diasRestantes !== null) {
            if (diasRestantes <= 3) {
                // CRÍTICO: 1-3 días
                alertaCaducidad = `<span class="badge bg-danger ms-2" title="CRÍTICO: ${diasRestantes} días restantes">
                    <i class="fas fa-circle-exclamation"></i> ${diasRestantes} día${diasRestantes !== 1 ? 's' : ''}
                </span>`;
                iconoAlerta = '<i class="fas fa-circle text-danger me-1" title="CRÍTICO"></i>';
            } else if (diasRestantes <= 5) {
                // URGENTE: 4-5 días
                alertaCaducidad = `<span class="badge bg-warning text-dark ms-2" title="URGENTE: ${diasRestantes} días restantes">
                    <i class="fas fa-exclamation-triangle"></i> ${diasRestantes} días
                </span>`;
                iconoAlerta = '<i class="fas fa-circle text-warning me-1" title="URGENTE"></i>';
            } else if (diasRestantes <= 7) {
                // ALERTA: 6-7 días
                alertaCaducidad = `<span class="badge bg-info ms-2" title="ALERTA: ${diasRestantes} días restantes">
                    <i class="fas fa-info-circle"></i> ${diasRestantes} días
                </span>`;
                iconoAlerta = '<i class="fas fa-circle text-info me-1" title="ALERTA"></i>';
            }
        }
        
        const row = `
            <tr>
                <td>${iconoAlerta}${lote.numero_lote}</td>
                <td>${lote.cantidad}</td>
                <td>${formatearFecha(lote.fecha_ingreso)}</td>
                <td>${formatearFecha(lote.fecha_caducidad)}${alertaCaducidad}</td>
                <td>${lote.proveedor || '-'}</td>
                <td>$${parseFloat(lote.precio_compra || 0).toFixed(2)}</td>
                <td>${estadoBadge[lote.estado] || lote.estado}</td>
                <td>
                    <button class="btn btn-sm btn-warning" onclick="editarLote(${lote.idlote})" title="Editar">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-danger" onclick="eliminarLote(${lote.idlote}, '${lote.numero_lote}')" title="Eliminar">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        tbody.innerHTML += row;
    });
    
    // Actualizar resumen
    if (resumen) {
        document.getElementById('resumen_total_lotes').textContent = resumen.total_lotes || 0;
        document.getElementById('resumen_cantidad_activa').textContent = resumen.cantidad_activa || 0;
        document.getElementById('resumen_proxima_caducidad').textContent = 
            resumen.proxima_caducidad ? formatearFecha(resumen.proxima_caducidad) : 'N/A';
    }
}

// Función para abrir modal agregar lote
function abrirModalAgregarLote(productoId, productoNombre) {
    console.log('➕ Abriendo modal agregar lote para:', productoId, productoNombre);
    
    document.getElementById('agregar_lote_producto_id').value = productoId;
    document.getElementById('agregar_lote_producto_nombre').textContent = productoNombre;
    
    // Obtener número de lote sugerido del servidor
    fetch(`?ctrl=Clotes&action=generarNumeroLote&inv_idinv=${productoId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('agregar_numero_lote').value = data.numero_lote;
            } else {
                // Fallback si falla
                document.getElementById('agregar_numero_lote').value = 'LOTE-' + Date.now();
            }
        })
        .catch(error => {
            console.error('Error al generar número de lote:', error);
            // Fallback si falla
            document.getElementById('agregar_numero_lote').value = 'LOTE-' + Date.now();
        });
    
    // Establecer fecha de ingreso a hoy
    document.getElementById('agregar_fecha_ingreso').value = new Date().toISOString().split('T')[0];
    
    const modal = new bootstrap.Modal(document.getElementById('modal-agregar-lote'));
    modal.show();
}

// Función para guardar nuevo lote
function guardarNuevoLote() {
    const form = document.getElementById('form-agregar-lote');
    const formData = new FormData(form);
    
    console.log('Enviando datos del lote:');
    for (let [key, value] of formData.entries()) {
        console.log(key + ': ' + value);
    }
    
    fetch('?ctrl=Clotes&action=crearLote', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        console.log('Response status:', response.status);
        return response.text();
    })
    .then(text => {
        console.log('Response text:', text);
        try {
            const data = JSON.parse(text);
            if (data.success) {
                alert('Lote agregado exitosamente');
                bootstrap.Modal.getInstance(document.getElementById('modal-agregar-lote')).hide();
                form.reset();
                // Recargar página para ver cambios
                window.location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        } catch (e) {
            console.error('Error parsing JSON:', e);
            alert('Error al procesar respuesta del servidor');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al guardar lote: ' + error.message);
    });
}

// Función para editar lote
function editarLote(idlote) {
    console.log('✏️ Editando lote:', idlote);
    
    // Obtener datos del lote
    fetch(`?ctrl=Clotes&action=obtenerLote&idlote=${idlote}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.lote) {
                const lote = data.lote;
                
                // Llenar el formulario
                document.getElementById('editar_idlote').value = lote.idlote;
                document.getElementById('editar_inv_idinv').value = lote.inv_idinv;
                document.getElementById('editar_numero_lote').value = lote.numero_lote;
                document.getElementById('editar_lote_numero').textContent = lote.numero_lote;
                document.getElementById('editar_cantidad').value = lote.cantidad;
                document.getElementById('editar_fecha_caducidad').value = lote.fecha_caducidad;
                document.getElementById('editar_estado').value = lote.estado || 'activo';
                document.getElementById('editar_proveedor').value = lote.proveedor || '';
                document.getElementById('editar_precio_compra').value = lote.precio_compra || '';
                document.getElementById('editar_observaciones').value = lote.observaciones || '';
                
                // Abrir modal
                const modal = new bootstrap.Modal(document.getElementById('modal-editar-lote'));
                modal.show();
            } else {
                alert('Error al cargar datos del lote: ' + (data.message || 'Datos no encontrados'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al obtener datos del lote');
        });
}

// Función para guardar edición de lote
function guardarEdicionLote() {
    const form = document.getElementById('form-editar-lote');
    const formData = new FormData(form);
    
    console.log('💾 Guardando edición de lote');
    
    fetch('?ctrl=Clotes&action=actualizarLote', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        console.log('Response status:', response.status);
        return response.text();
    })
    .then(text => {
        console.log('Response text:', text);
        try {
            const data = JSON.parse(text);
            if (data.success) {
                alert('Lote actualizado exitosamente');
                bootstrap.Modal.getInstance(document.getElementById('modal-editar-lote')).hide();
                // Recargar la lista de lotes si el modal de ver lotes está abierto
                const productoId = document.getElementById('ver_lotes_producto_id').value;
                if (productoId) {
                    cargarLotesProducto(productoId);
                } else {
                    window.location.reload();
                }
            } else {
                alert('Error: ' + data.message);
            }
        } catch (e) {
            console.error('Error parsing JSON:', e);
            alert('Error al procesar respuesta del servidor');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al actualizar lote: ' + error.message);
    });
}


// Función para eliminar lote
function eliminarLote(idlote, numeroLote) {
    if (confirm(`¿Estás seguro de eliminar el lote ${numeroLote}?`)) {
        fetch('?ctrl=Clotes&action=eliminarLote', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `idlote=${idlote}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Lote eliminado exitosamente');
                window.location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al eliminar lote');
        });
    }
}

// Funciones auxiliares
function calcularDiasRestantes(fechaCaducidad) {
    const hoy = new Date();
    const caducidad = new Date(fechaCaducidad);
    const diferencia = caducidad - hoy;
    return Math.ceil(diferencia / (1000 * 60 * 60 * 24));
}

function formatearFecha(fecha) {
    if (!fecha) return '-';
    const date = new Date(fecha);
    return date.toLocaleDateString('es-ES', { year: 'numeric', month: '2-digit', day: '2-digit' });
}

// Función para sincronizar stocks de todos los productos
function sincronizarTodosStocks() {
    if (!confirm('¿Deseas sincronizar el stock de TODOS los productos con sus lotes activos?\n\nEsto recalculará el stock basándose en la suma de lotes.')) {
        return;
    }
    
    const btn = document.getElementById('btn-sincronizar-stocks');
    const btnText = btn ? btn.innerHTML : '';
    
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Sincronizando...';
    }
    
    fetch('?ctrl=Clotes&action=sincronizarTodosStocks')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(`✅ ${data.message}\n\nProductos sincronizados: ${data.productos_sincronizados}`);
                window.location.reload();
            } else {
                alert('❌ Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('❌ Error al sincronizar stocks');
        })
        .finally(() => {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = btnText;
            }
        });
}

// ==========================================
// BÚSQUEDA EN TIEMPO REAL CON DEBOUNCE
// ==========================================

// Variables para almacenar los timeouts de debounce
let debounceTimerPerec = null;
let debounceTimerNoPerec = null;

// Función de debounce genérica
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Función para ejecutar búsqueda con filtros
function ejecutarBusquedaPerec() {
    const termino = document.getElementById('buscar-perecederos')?.value.trim() || '';
    const estadoStock = document.getElementById('filtro-stock-perecederos')?.value || '';
    const btnLimpiar = document.getElementById('limpiar-busqueda-perecederos');
    const loading = document.getElementById('loading-perecederos');
    
    // Mostrar/ocultar botón limpiar si hay algún filtro activo
    if (btnLimpiar) {
        btnLimpiar.style.display = (termino || estadoStock) ? 'block' : 'none';
    }
    
    // Si no hay filtros activos, recargar página
    if (!termino && !estadoStock) {
        window.location.href = '?ctrl=cinventario';
        return;
    }
    
    // Mostrar loading
    if (loading) loading.style.display = 'block';
    
    // Ejecutar búsqueda con filtros y ordenamiento (sin categoría para perecederos)
    buscarProductos(termino, 'perecedero', '', estadoStock, ordenActualPerec, direccionActualPerec);
}

// Búsqueda para productos perecederos
document.getElementById('buscar-perecederos')?.addEventListener('input', function(e) {
    clearTimeout(debounceTimerPerec);
    debounceTimerPerec = setTimeout(() => {
        ejecutarBusquedaPerec();
    }, 500);
});

// Filtro de estado de stock para productos perecederos
document.getElementById('filtro-stock-perecederos')?.addEventListener('change', function() {
    clearTimeout(debounceTimerPerec);
    debounceTimerPerec = setTimeout(() => {
        ejecutarBusquedaPerec();
    }, 300);
});

// Función para ejecutar búsqueda con filtros no perecederos
function ejecutarBusquedaNoPerec() {
    const termino = document.getElementById('buscar-no-perecederos')?.value.trim() || '';
    const categoria = document.getElementById('filtro-categoria-no-perecederos')?.value || '';
    const estadoStock = document.getElementById('filtro-stock-no-perecederos')?.value || '';
    const btnLimpiar = document.getElementById('limpiar-busqueda-no-perecederos');
    const loading = document.getElementById('loading-no-perecederos');
    
    // Mostrar/ocultar botón limpiar si hay algún filtro activo
    if (btnLimpiar) {
        btnLimpiar.style.display = (termino || categoria || estadoStock) ? 'block' : 'none';
    }
    
    // Si no hay filtros activos, recargar página
    if (!termino && !categoria && !estadoStock) {
        window.location.href = '?ctrl=cinventario';
        return;
    }
    
    // Mostrar loading
    if (loading) loading.style.display = 'block';
    
    // Ejecutar búsqueda con filtros y ordenamiento
    buscarProductos(termino, 'no_perecedero', categoria, estadoStock, ordenActualNoPerec, direccionActualNoPerec);
}

// Búsqueda para productos no perecederos
document.getElementById('buscar-no-perecederos')?.addEventListener('input', function(e) {
    clearTimeout(debounceTimerNoPerec);
    debounceTimerNoPerec = setTimeout(() => {
        ejecutarBusquedaNoPerec();
    }, 500);
});

// Filtros de productos no perecederos
document.getElementById('filtro-categoria-no-perecederos')?.addEventListener('change', function() {
    clearTimeout(debounceTimerNoPerec);
    debounceTimerNoPerec = setTimeout(() => {
        ejecutarBusquedaNoPerec();
    }, 300);
});

document.getElementById('filtro-stock-no-perecederos')?.addEventListener('change', function() {
    clearTimeout(debounceTimerNoPerec);
    debounceTimerNoPerec = setTimeout(() => {
        ejecutarBusquedaNoPerec();
    }, 300);
});

// Variables globales para ordenamiento
let ordenActualPerec = '';
let direccionActualPerec = 'ASC';
let ordenActualNoPerec = '';
let direccionActualNoPerec = 'ASC';

// Botones de limpiar búsqueda
document.getElementById('limpiar-busqueda-perecederos')?.addEventListener('click', function() {
    document.getElementById('buscar-perecederos').value = '';
    document.getElementById('filtro-stock-perecederos').value = '';
    ordenActualPerec = '';
    direccionActualPerec = 'ASC';
    this.style.display = 'none';
    window.location.href = '?ctrl=cinventario';
});

document.getElementById('limpiar-busqueda-no-perecederos')?.addEventListener('click', function() {
    document.getElementById('buscar-no-perecederos').value = '';
    document.getElementById('filtro-categoria-no-perecederos').value = '';
    document.getElementById('filtro-stock-no-perecederos').value = '';
    ordenActualNoPerec = '';
    direccionActualNoPerec = 'ASC';
    this.style.display = 'none';
    window.location.href = '?ctrl=cinventario';
});

// Función para buscar productos vía AJAX
function buscarProductos(termino, tipo, categoria = '', estadoStock = '', orden = '', direccion = 'ASC') {
    const loading = tipo === 'perecedero' 
        ? document.getElementById('loading-perecederos')
        : document.getElementById('loading-no-perecederos');
    
    // Construir URL con parámetros
    let url = `?ctrl=cinventario&action=buscar&tipo=${tipo}`;
    if (termino) url += `&termino=${encodeURIComponent(termino)}`;
    if (categoria) url += `&categoria=${encodeURIComponent(categoria)}`;
    if (estadoStock) url += `&estado_stock=${encodeURIComponent(estadoStock)}`;
    if (orden) url += `&orden=${encodeURIComponent(orden)}&direccion=${direccion}`;
    
    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                actualizarTabla(data.productos, tipo);
            } else {
                console.error('Error en búsqueda:', data.message);
                mostrarMensajeNoResultados(tipo);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            mostrarMensajeNoResultados(tipo);
        })
        .finally(() => {
            if (loading) loading.style.display = 'none';
        });
}

// Actualizar tabla con resultados
function actualizarTabla(productos, tipo) {
    const tbody = tipo === 'perecedero'
        ? document.querySelector('.card:has(#buscar-perecederos) tbody')
        : document.querySelector('.card:has(#buscar-no-perecederos) tbody');
    
    if (!tbody) return;
    
    if (productos.length === 0) {
        mostrarMensajeNoResultados(tipo);
        return;
    }
    
    tbody.innerHTML = productos.map(item => generarFilaProducto(item, tipo)).join('');
}

// Generar HTML de fila de producto
function generarFilaProducto(item, tipo) {
    const stockClass = item.stock == 0 ? 'bg-danger' : (item.stock < 20 ? 'bg-warning text-dark' : 'bg-success');
    
    // Calcular margen de ganancia
    const precioCompra = parseFloat(item.precio_compra || 0);
    const precioVenta = parseFloat(item.precio || 0);
    const margenPorcentaje = precioCompra > 0 ? ((precioVenta - precioCompra) / precioCompra) * 100 : 0;
    const margenClass = margenPorcentaje > 30 ? 'bg-success' : (margenPorcentaje >= 10 ? 'bg-warning text-dark' : 'bg-danger');
    
    if (tipo === 'perecedero') {
        return `
            <tr>
                <td><div class="fw-bold">${item.producto}</div></td>
                <td><span class="badge bg-success">${item.naturaleza}</span></td>
                <td><span class="badge" style="background-color: ${getColorHex(item.color)};">${item.color}</span></td>
                <td><span class="badge ${stockClass}">${item.stock}</span></td>
                <td class="text-muted small">$${precioCompra.toFixed(2)}</td>
                <td class="fw-bold text-primary">$${precioVenta.toFixed(2)}</td>
                <td><span class="badge ${margenClass}" title="Margen de ganancia">${margenPorcentaje.toFixed(1)}%</span></td>
                <td class="fw-bold text-success">$${(item.stock * precioVenta).toFixed(2)}</td>
                <td>${item.fecha_actualizacion || '-'}</td>
                <td>${item.lote_proxima_caducidad || 'Sin lotes'}</td>
                <td>${generarBadgeDias(item.dias_hasta_caducidad, item.lote_cantidad_activa)}</td>
                <td>${generarBadgePrioridad(item.dias_hasta_caducidad, item.lote_cantidad_activa)}</td>
                <td>
                    <button class="btn btn-sm btn-success" onclick="abrirModalVerLotes('${item.idinv}', '${item.producto.replace(/'/g, "\\'")}')">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-primary" onclick="abrirModalAgregarLote('${item.idinv}', '${item.producto.replace(/'/g, "\\'")}')">
                        <i class="fas fa-plus"></i>
                    </button>
                </td>
                <td>
                    <button class="btn btn-warning btn-sm" onclick="abrirModalEditar('${item.idinv}', '${item.producto.replace(/'/g, "\\'")}')">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-danger btn-sm" onclick="abrirModalEliminar('${item.idinv}', '${item.producto.replace(/'/g, "\\'")}')">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
    } else {
        return `
            <tr>
                <td><div class="fw-bold">${item.producto}</div></td>
                <td><span class="badge bg-info">${item.naturaleza}</span></td>
                <td><span class="badge" style="background-color: ${getColorHex(item.color)};">${item.color}</span></td>
                <td><span class="badge ${stockClass}">${item.stock}</span></td>
                <td class="text-muted small">$${precioCompra.toFixed(2)}</td>
                <td class="fw-bold text-primary">$${precioVenta.toFixed(2)}</td>
                <td><span class="badge ${margenClass}" title="Margen de ganancia">${margenPorcentaje.toFixed(1)}%</span></td>
                <td class="fw-bold text-success">$${(item.stock * precioVenta).toFixed(2)}</td>
                <td><span class="badge ${stockClass}">${item.estado_stock}</span></td>
                <td>
                    <button class="btn btn-warning btn-sm" onclick="abrirModalEditar('${item.idinv}', '${item.producto.replace(/'/g, "\\'")}')">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-success btn-sm" onclick="abrirModalAgregarStock('${item.idinv}', '${item.producto.replace(/'/g, "\\'")}', ${item.stock})">
                        <i class="fas fa-plus"></i>
                    </button>
                    <button class="btn btn-danger btn-sm" onclick="abrirModalEliminar('${item.idinv}', '${item.producto.replace(/'/g, "\\'")}')">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
    }
}

// Helpers para badges
function getColorHex(color) {
    const colores = {
        'Rojo': '#dc3545', 'Rosa': '#ff69b4', 'Blanco': '#f8f9fa',
        'Amarillo': '#ffc107', 'Naranja': '#fd7e14', 'Morado': '#6f42c1',
        'Azul': '#0d6efd', 'Verde': '#198754', 'Multicolor': '#6c757d'
    };
    return colores[color] || '#6c757d';
}

function generarBadgeDias(dias, cantidadActiva) {
    if (!dias || !cantidadActiva || cantidadActiva <= 0) return '<span class="text-muted">-</span>';
    
    if (dias <= 3) return `<span class="badge bg-danger"><i class="fas fa-circle-exclamation"></i> ${dias}d</span>`;
    if (dias <= 5) return `<span class="badge bg-warning text-dark"><i class="fas fa-exclamation-triangle"></i> ${dias}d</span>`;
    if (dias <= 7) return `<span class="badge bg-info"><i class="fas fa-info-circle"></i> ${dias}d</span>`;
    return `<span class="badge bg-secondary">${dias}d</span>`;
}

function generarBadgePrioridad(dias, cantidadActiva) {
    if (!dias || !cantidadActiva || cantidadActiva <= 0) return '<span class="badge bg-secondary">N/A</span>';
    
    if (dias <= 3) return '<span class="badge bg-danger"><i class="fas fa-exclamation-triangle"></i> CRÍTICO</span>';
    if (dias <= 5) return '<span class="badge bg-warning text-dark"><i class="fas fa-exclamation-circle"></i> URGENTE</span>';
    if (dias <= 7) return '<span class="badge bg-info"><i class="fas fa-info-circle"></i> ALERTA</span>';
    return '<span class="badge bg-success">OK</span>';
}

// Mostrar mensaje cuando no hay resultados
function mostrarMensajeNoResultados(tipo) {
    const tbody = tipo === 'perecedero'
        ? document.querySelector('.card:has(#buscar-perecederos) tbody')
        : document.querySelector('.card:has(#buscar-no-perecederos) tbody');
    
    if (!tbody) return;
    
    const colspan = tipo === 'perecedero' ? 14 : 10;
    tbody.innerHTML = `
        <tr>
            <td colspan="${colspan}" class="text-center text-muted py-4">
                <i class="fas fa-search" style="font-size:2rem;"></i>
                <h6 class="mt-2">No se encontraron resultados</h6>
                <p class="mb-0">Intenta con otros términos de búsqueda</p>
            </td>
        </tr>
    `;
}

// Ordenamiento por columnas
function configurarOrdenamiento() {
    // Perecederos
    document.querySelectorAll('#tabla-perecederos .sortable').forEach(th => {
        th.addEventListener('click', function() {
            const columna = this.getAttribute('data-sort');
            
            // Toggle dirección
            if (ordenActualPerec === columna) {
                direccionActualPerec = direccionActualPerec === 'ASC' ? 'DESC' : 'ASC';
            } else {
                ordenActualPerec = columna;
                direccionActualPerec = 'ASC';
            }
            
            // Actualizar indicadores visuales
            actualizarIndicadoresOrden('perecedero', columna, direccionActualPerec);
            
            // Ejecutar búsqueda con ordenamiento
            ejecutarBusquedaPerec();
        });
    });
    
    // No Perecederos
    document.querySelectorAll('#tabla-no-perecederos .sortable').forEach(th => {
        th.addEventListener('click', function() {
            const columna = this.getAttribute('data-sort');
            
            // Toggle dirección
            if (ordenActualNoPerec === columna) {
                direccionActualNoPerec = direccionActualNoPerec === 'ASC' ? 'DESC' : 'ASC';
            } else {
                ordenActualNoPerec = columna;
                direccionActualNoPerec = 'ASC';
            }
            
            // Actualizar indicadores visuales
            actualizarIndicadoresOrden('no_perecedero', columna, direccionActualNoPerec);
            
            // Ejecutar búsqueda con ordenamiento
            ejecutarBusquedaNoPerec();
        });
    });
}

// Actualizar indicadores visuales de ordenamiento
function actualizarIndicadoresOrden(tipo, columnaActiva, direccion) {
    const tabla = tipo === 'perecedero' ? '#tabla-perecederos' : '#tabla-no-perecederos';
    
    // Resetear todos los iconos
    document.querySelectorAll(`${tabla} .sortable i`).forEach(icon => {
        icon.className = 'fas fa-sort text-muted';
    });
    
    // Actualizar icono de columna activa
    const thActivo = document.querySelector(`${tabla} .sortable[data-sort="${columnaActiva}"]`);
    if (thActivo) {
        const icon = thActivo.querySelector('i');
        if (icon) {
            icon.className = direccion === 'ASC' 
                ? 'fas fa-sort-up text-primary' 
                : 'fas fa-sort-down text-primary';
        }
    }
}

// Inicializar ordenamiento cuando carga la página
document.addEventListener('DOMContentLoaded', function() {
    configurarOrdenamiento();
});

</script>

<!-- Modal Ver Lotes -->
<div class="modal fade" id="modal-ver-lotes" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="fas fa-boxes me-2"></i>Gestión de Lotes - <span id="ver_lotes_producto_nombre"></span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="ver_lotes_producto_id">
                
                <!-- Resumen -->
                <div class="row mb-3">
                    <div class="col-md-4">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <h6>Total Lotes</h6>
                                <h3 id="resumen_total_lotes">0</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <h6>Cantidad Activa</h6>
                                <h3 id="resumen_cantidad_activa">0</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <h6>Próxima Caducidad</h6>
                                <h3 id="resumen_proxima_caducidad">-</h3>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Tabla de lotes -->
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Nº Lote</th>
                                <th>Cantidad</th>
                                <th>F. Ingreso</th>
                                <th>F. Caducidad</th>
                                <th>Proveedor</th>
                                <th>Precio Compra</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tabla-lotes-body">
                            <tr>
                                <td colspan="8" class="text-center">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Cargando...</span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Agregar Lote -->
<div class="modal fade" id="modal-agregar-lote" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-plus-circle me-2"></i>Agregar Nuevo Lote - <span id="agregar_lote_producto_nombre"></span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="form-agregar-lote">
                    <input type="hidden" name="inv_idinv" id="agregar_lote_producto_id">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Número de Lote <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="numero_lote" id="agregar_numero_lote" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Cantidad <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="cantidad" min="1" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Fecha de Ingreso <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="fecha_ingreso" id="agregar_fecha_ingreso" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Fecha de Caducidad <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="fecha_caducidad" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Proveedor</label>
                            <select class="form-select" name="proveedor" id="agregar_lote_proveedor">
                                <option value="">Seleccionar proveedor...</option>
                                <?php if (!empty($todos_proveedores)): ?>
                                    <?php foreach ($todos_proveedores as $prov): ?>
                                        <option value="<?= htmlspecialchars($prov['nombre']) ?>"><?= htmlspecialchars($prov['nombre']) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Precio de Compra</label>
                            <input type="number" class="form-control" name="precio_compra" step="0.01" min="0">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Observaciones</label>
                        <textarea class="form-control" name="observaciones" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="guardarNuevoLote()">
                    <i class="fas fa-save me-1"></i>Guardar Lote
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Editar Lote -->
<div class="modal fade" id="modal-editar-lote" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Editar Lote - <span id="editar_lote_numero"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="form-editar-lote">
                    <input type="hidden" name="idlote" id="editar_idlote">
                    <input type="hidden" name="inv_idinv" id="editar_inv_idinv">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Número de Lote *</label>
                            <input type="text" class="form-control" name="numero_lote" id="editar_numero_lote" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Cantidad *</label>
                            <input type="number" class="form-control" name="cantidad" id="editar_cantidad" min="0" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Fecha de Caducidad *</label>
                            <input type="date" class="form-control" name="fecha_caducidad" id="editar_fecha_caducidad" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Estado</label>
                            <select class="form-select" name="estado" id="editar_estado">
                                <option value="activo">Activo</option>
                                <option value="vencido">Vencido</option>
                                <option value="agotado">Agotado</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Proveedor</label>
                            <select class="form-select" name="proveedor" id="editar_proveedor">
                                <option value="">Seleccionar proveedor...</option>
                                <?php if (!empty($todos_proveedores)): ?>
                                    <?php foreach ($todos_proveedores as $prov): ?>
                                        <option value="<?= htmlspecialchars($prov['nombre']) ?>"><?= htmlspecialchars($prov['nombre']) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Precio de Compra</label>
                            <input type="number" step="0.01" class="form-control" name="precio_compra" id="editar_precio_compra" min="0">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Observaciones</label>
                        <textarea class="form-control" name="observaciones" id="editar_observaciones" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-warning" onclick="guardarEdicionLote()">
                    <i class="fas fa-save me-1"></i>Guardar Cambios
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// ============================================================================
// VALIDACIÓN DE PRECIOS: Precio Compra < Precio Venta
// ============================================================================
document.addEventListener('DOMContentLoaded', function() {
    // Validación en formulario AGREGAR
    const precioCompraAgregar = document.getElementById('precio_compra_agregar');
    const precioVentaAgregar = document.getElementById('precio_venta_agregar');
    
    if (precioCompraAgregar && precioVentaAgregar) {
        function validarPreciosAgregar() {
            const compra = parseFloat(precioCompraAgregar.value) || 0;
            const venta = parseFloat(precioVentaAgregar.value) || 0;
            
            if (compra > 0 && venta > 0 && compra >= venta) {
                precioVentaAgregar.setCustomValidity('El precio de venta debe ser mayor al precio de compra');
                precioVentaAgregar.classList.add('is-invalid');
                return false;
            } else {
                precioVentaAgregar.setCustomValidity('');
                precioVentaAgregar.classList.remove('is-invalid');
                return true;
            }
        }
        
        precioCompraAgregar.addEventListener('input', validarPreciosAgregar);
        precioVentaAgregar.addEventListener('input', validarPreciosAgregar);
    }
    
    // Validación en formulario EDITAR
    const precioCompraEditar = document.getElementById('editar_precio_compra');
    const precioVentaEditar = document.getElementById('editar_precio');
    
    if (precioCompraEditar && precioVentaEditar) {
        function validarPreciosEditar() {
            const compra = parseFloat(precioCompraEditar.value) || 0;
            const venta = parseFloat(precioVentaEditar.value) || 0;
            
            if (compra > 0 && venta > 0 && compra >= venta) {
                precioVentaEditar.setCustomValidity('El precio de venta debe ser mayor al precio de compra');
                precioVentaEditar.classList.add('is-invalid');
                return false;
            } else {
                precioVentaEditar.setCustomValidity('');
                precioVentaEditar.classList.remove('is-invalid');
                return true;
            }
        }
        
        precioCompraEditar.addEventListener('input', validarPreciosEditar);
        precioVentaEditar.addEventListener('input', validarPreciosEditar);
    }
});

// ============================================================================
// DETECCIÓN Y MANEJO DE PRODUCTOS DUPLICADOS
// ============================================================================
document.addEventListener('DOMContentLoaded', function() {
    // Verificar si hay parámetro de duplicado en la URL
    const urlParams = new URLSearchParams(window.location.search);
    const esDuplicado = urlParams.get('duplicado');
    
    if (esDuplicado === '1') {
        // Extraer datos del producto duplicado
        const productoId = urlParams.get('producto_id');
        const productoNombre = urlParams.get('producto_nombre');
        const stockActual = urlParams.get('stock_actual');
        const mensaje = urlParams.get('mensaje');
        
        // Actualizar contenido del modal
        if (mensaje) {
            document.getElementById('duplicado-mensaje').textContent = decodeURIComponent(mensaje);
        }
        
        if (stockActual) {
            document.getElementById('duplicado-stock').textContent = stockActual;
        }
        
        if (productoId) {
            document.getElementById('duplicado-producto-id').value = productoId;
            
            // Buscar fecha_actualizacion del producto en el array de inventario
            if (typeof productosInventario !== 'undefined') {
                const producto = productosInventario.find(p => p.idinv == productoId);
                if (producto && producto.fecha_actualizacion) {
                    const fecha = new Date(producto.fecha_actualizacion);
                    const fechaFormateada = fecha.toLocaleDateString('es-MX', {
                        year: 'numeric',
                        month: '2-digit',
                        day: '2-digit',
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                    document.getElementById('duplicado-fecha').textContent = fechaFormateada;
                }
            }
        }
        
        if (productoNombre) {
            document.getElementById('duplicado-producto-nombre').value = decodeURIComponent(productoNombre);
        }
        
        // Mostrar el modal automáticamente
        const modalDuplicado = new bootstrap.Modal(document.getElementById('modal-producto-duplicado'));
        modalDuplicado.show();
        
        // Limpiar la URL después de mostrar el modal (opcional)
        // Esto evita que el modal se muestre de nuevo si se recarga la página
        const cleanUrl = window.location.pathname + '?ctrl=Cinventario';
        window.history.replaceState({}, document.title, cleanUrl);
    }
});

/**
 * Función para abrir el modal de agregar stock al producto existente
 * Se activa cuando el usuario confirma que quiere agregar stock en lugar de crear un duplicado
 */
function irAAgregarStock() {
    const productoId = document.getElementById('duplicado-producto-id').value;
    const productoNombre = document.getElementById('duplicado-producto-nombre').value;
    
    if (!productoId || !productoNombre) {
        alert('Error: No se pudo identificar el producto');
        return;
    }
    
    // Cerrar el modal de duplicado
    const modalDuplicado = bootstrap.Modal.getInstance(document.getElementById('modal-producto-duplicado'));
    if (modalDuplicado) {
        modalDuplicado.hide();
    }
    
    // Esperar a que el modal se cierre completamente antes de abrir el siguiente
    setTimeout(function() {
        // Usar la función existente para abrir el modal de agregar stock
        abrirModalAgregarStock(productoId, productoNombre);
    }, 300);
}

</script>

</div>
<!-- Fin de la vista de inventario -->
