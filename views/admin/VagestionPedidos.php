
<?php
// Este archivo es incluido por dashboard.php, no es una página independiente
// La exportación se maneja desde el controlador dashboard.php antes de incluir este archivo

// Conectar a la base de datos
require_once($_SERVER['DOCUMENT_ROOT'] . '/Original-Floraltech/views/config/database.php');

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    if (!$conn) {
        throw new Exception('No se pudo conectar a la base de datos');
    }
} catch (Exception $e) {
    die('Error de conexión: ' . $e->getMessage());
}

// Variables para mensajes
$mensaje_exito = '';
$mensaje_error = '';

// Procesar acciones POST (cambio de estado, cancelación, etc.)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'cambiar_estado':
                if (isset($_POST['id_pedido']) && isset($_POST['nuevo_estado'])) {
                    try {
                        $stmt = $conn->prepare("UPDATE ped SET estado = ? WHERE idped = ?");
                        $stmt->execute([$_POST['nuevo_estado'], $_POST['id_pedido']]);
                        
                        // Si es AJAX, devolver JSON
                        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                            header('Content-Type: application/json');
                            echo json_encode(['success' => true, 'mensaje' => 'Estado actualizado correctamente']);
                            exit;
                        }
                        
                        $mensaje_exito = "Estado del pedido actualizado correctamente.";
                    } catch (PDOException $e) {
                        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                            header('Content-Type: application/json');
                            echo json_encode(['success' => false, 'mensaje' => $e->getMessage()]);
                            exit;
                        }
                        $mensaje_error = "Error al actualizar el estado: " . $e->getMessage();
                    }
                }
                break;
                
            case 'actualizar_entrega':
                if (isset($_POST['pedido_id']) && isset($_POST['fecha_entrega'])) {
                    try {
                        $stmt = $conn->prepare("UPDATE ped SET fecha_entrega_solicitada = ? WHERE idped = ?");
                        $stmt->execute([$_POST['fecha_entrega'], $_POST['pedido_id']]);
                        $mensaje_exito = "Fecha de entrega actualizada correctamente.";
                    } catch (PDOException $e) {
                        $mensaje_error = "Error al actualizar la fecha de entrega: " . $e->getMessage();
                    }
                }
                break;
                
            case 'editar_pedido':
                if (isset($_POST['id_pedido'])) {
                    try {
                        // Preparar los campos a actualizar
                        $updates = [];
                        $update_params = [];
                        
                        if (isset($_POST['direccion_entrega'])) {
                            $updates[] = "direccion_entrega = ?";
                            $update_params[] = $_POST['direccion_entrega'];
                        }
                        
                        if (isset($_POST['fecha_entrega_solicitada'])) {
                            $updates[] = "fecha_entrega_solicitada = ?";
                            $update_params[] = $_POST['fecha_entrega_solicitada'];
                        }
                        
                        if (isset($_POST['estado'])) {
                            $updates[] = "estado = ?";
                            $update_params[] = $_POST['estado'];
                        }
                        
                        if (isset($_POST['notas'])) {
                            $updates[] = "notas = ?";
                            $update_params[] = $_POST['notas'];
                        }
                        
                        if (isset($_POST['empleado_id']) && !empty($_POST['empleado_id'])) {
                            $updates[] = "empleado_id = ?";
                            $update_params[] = $_POST['empleado_id'];
                        }
                        
                        if (!empty($updates)) {
                            $update_params[] = $_POST['id_pedido'];
                            $sql = "UPDATE ped SET " . implode(", ", $updates) . " WHERE idped = ?";
                            $stmt = $conn->prepare($sql);
                            $stmt->execute($update_params);
                            
                            // Responder con JSON
                            header('Content-Type: application/json');
                            echo json_encode(['success' => true, 'mensaje' => 'Pedido actualizado correctamente']);
                            exit;
                        }
                    } catch (PDOException $e) {
                        header('Content-Type: application/json');
                        echo json_encode(['success' => false, 'mensaje' => 'Error al actualizar: ' . $e->getMessage()]);
                        exit;
                    }
                }
                break;
        }
    }
}

// Filtros de búsqueda
$where_conditions = ["1=1"];
$params = [];

// Búsqueda rápida global
if (isset($_GET['buscar']) && !empty($_GET['buscar'])) {
    $busqueda = '%' . $_GET['buscar'] . '%';
    $where_conditions[] = "(p.numped LIKE ? OR c.nombre LIKE ? OR c.email LIKE ? OR p.direccion_entrega LIKE ?)";
    $params = array_merge($params, [$busqueda, $busqueda, $busqueda, $busqueda]);
}

if (isset($_GET['estado']) && !empty($_GET['estado'])) {
    $where_conditions[] = "p.estado = ?";
    $params[] = $_GET['estado'];
}

if (isset($_GET['cliente']) && !empty($_GET['cliente'])) {
    $where_conditions[] = "c.nombre LIKE ?";
    $params[] = '%' . $_GET['cliente'] . '%';
}

if (isset($_GET['fecha_desde']) && !empty($_GET['fecha_desde'])) {
    $where_conditions[] = "DATE(p.fecha_pedido) >= ?";
    $params[] = $_GET['fecha_desde'];
}

if (isset($_GET['fecha_hasta']) && !empty($_GET['fecha_hasta'])) {
    $where_conditions[] = "DATE(p.fecha_pedido) <= ?";
    $params[] = $_GET['fecha_hasta'];
}

$where_clause = implode(' AND ', $where_conditions);

// Paginación
$registros_por_pagina = 20;
$pagina_actual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$offset = ($pagina_actual - 1) * $registros_por_pagina;

// Contar total de registros para paginación
try {
    $sql_count = "SELECT COUNT(DISTINCT p.idped) as total FROM ped p INNER JOIN cli c ON p.cli_idcli = c.idcli WHERE $where_clause";
    $stmt_count = $conn->prepare($sql_count);
    $stmt_count->execute($params);
    $total_registros = $stmt_count->fetch(PDO::FETCH_ASSOC)['total'];
    $total_paginas = ceil($total_registros / $registros_por_pagina);
} catch (PDOException $e) {
    $total_registros = 0;
    $total_paginas = 1;
}

// Obtener pedidos de la base de datos
try {
    $sql = "
        SELECT 
            p.idped,
            p.numped,
            p.fecha_pedido,
            p.monto_total,
            p.estado,
            p.direccion_entrega,
            p.fecha_entrega_solicitada,
            c.nombre as cliente_nombre,
            c.email as cliente_email,
            c.telefono as cliente_telefono,
            c.direccion as cliente_direccion,
            pg.metodo_pago,
            pg.estado_pag as estado_pago,
            pg.fecha_pago,
            COUNT(dp.idtflor) as total_productos
        FROM ped p
        INNER JOIN cli c ON p.cli_idcli = c.idcli
        LEFT JOIN pagos pg ON p.idped = pg.ped_idped
        LEFT JOIN detped dp ON p.idped = dp.idped
        WHERE $where_clause
        GROUP BY p.idped, pg.idpago
        ORDER BY " . obtenerOrdenamiento() . "
        LIMIT $registros_por_pagina OFFSET $offset
    ";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    $mensaje_error = "Error al obtener pedidos: " . $e->getMessage();
    $pedidos = [];
}

// Calcular estadísticas
$totalPedidos = count($pedidos);
$totalPendientes = count(array_filter($pedidos, fn($p) => $p['estado'] === 'Pendiente'));
$totalEnProceso = count(array_filter($pedidos, fn($p) => $p['estado'] === 'En proceso'));
$totalCompletados = count(array_filter($pedidos, fn($p) => $p['estado'] === 'Completado'));
$ventasTotales = array_sum(array_column($pedidos, 'monto_total'));

// Función para obtener detalles de productos de un pedido
function obtenerDetallesPedido($conn, $idPedido) {
    try {
        $stmt = $conn->prepare("
            SELECT 
                dp.cantidad,
                dp.precio_unitario,
                tf.nombre as producto_nombre,
                tf.naturaleza as producto_color,
                tf.descripcion as producto_descripcion
            FROM detped dp
            INNER JOIN tflor tf ON dp.idtflor = tf.idtflor
            WHERE dp.idped = ?
        ");
        $stmt->execute([$idPedido]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}

// Función para obtener ordenamiento
function obtenerOrdenamiento() {
    $orden_columna = $_GET['orden'] ?? 'fecha_pedido';
    $orden_dir = $_GET['dir'] ?? 'DESC';
    
    $columnas_validas = [
        'fecha_pedido' => 'p.fecha_pedido',
        'numped' => 'p.numped',
        'cliente' => 'c.nombre',
        'monto_total' => 'p.monto_total',
        'estado' => 'p.estado',
        'fecha_entrega' => 'p.fecha_entrega_solicitada'
    ];
    
    $direcciones_validas = ['ASC', 'DESC'];
    
    $columna = $columnas_validas[$orden_columna] ?? 'p.fecha_pedido';
    $direccion = in_array($orden_dir, $direcciones_validas) ? $orden_dir : 'DESC';
    
    return "$columna $direccion";
}

// Función para mostrar iconos de ordenamiento
function obtenerIconoOrden($columna) {
    $orden_actual = $_GET['orden'] ?? 'fecha_pedido';
    $dir_actual = $_GET['dir'] ?? 'DESC';
    
    if ($orden_actual === $columna) {
        return $dir_actual === 'ASC' ? '<i class="fas fa-sort-up"></i>' : '<i class="fas fa-sort-down"></i>';
    }
    return '<i class="fas fa-sort text-muted"></i>';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Pedidos - FloralTech</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Optimización de espacios con fuentes legibles */
        .container-fluid { padding: 0.5rem 1rem; }
        .card { margin-bottom: 0.75rem; }
        .card-body { padding: 0.75rem; }
        h1 { margin-bottom: 1rem; font-size: 1.75rem; }
        .table-container { margin-top: 0.5rem; }
        
        /* Mejorar legibilidad de textos */
        .table { font-size: 0.95rem; }
        .table td, .table th { padding: 0.6rem 0.5rem; }
        .card-title { font-size: 0.9rem; }
        .fs-3 { font-size: 1.75rem !important; }
        .badge { font-size: 0.85rem; padding: 0.35em 0.65em; }
        
        @media print {
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>
<div class="container-fluid">
    <h1>Gestión de Pedidos</h1>

    <!-- Resumen general -->
    <div class="row g-2 mb-3">
        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="card text-center h-100">
                <div class="card-body p-2">
                    <h6 class="card-title mb-1">Total</h6>
                    <div class="fs-3 fw-bold text-primary"><?= $totalPedidos ?></div>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="card text-center h-100">
                <div class="card-body p-2">
                    <h6 class="card-title mb-1">Pendientes</h6>
                    <div class="fs-3 fw-bold text-warning"><?= $totalPendientes ?></div>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="card text-center h-100">
                <div class="card-body p-2">
                    <h6 class="card-title mb-1">En Proceso</h6>
                    <div class="fs-3 fw-bold text-info"><?= $totalEnProceso ?></div>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="card text-center h-100">
                <div class="card-body p-2">
                    <h6 class="card-title mb-1">Completados</h6>
                    <div class="fs-3 fw-bold text-success"><?= $totalCompletados ?></div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-8 col-sm-12">
            <div class="card text-center h-100">
                <div class="card-body p-2">
                    <h6 class="card-title mb-1">Ventas Totales</h6>
                    <div class="fs-3 fw-bold text-success">$<?= number_format($ventasTotales, 0, ',', '.') ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Barra de búsqueda rápida -->
    <div class="card mb-2">
        <div class="card-body p-2">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" id="busquedaRapida" placeholder="Buscar por número, cliente, email..." value="<?= $_GET['buscar'] ?? '' ?>">
                    </div>
                </div>
                <div class="col-md-6 text-end">
                    <button class="btn btn-success btn-sm" onclick="exportarExcel()" title="Exportar a Excel">
                        <i class="fas fa-file-excel me-1"></i> Excel
                    </button>
                    <button class="btn btn-danger btn-sm" onclick="exportarPDF()" title="Exportar a PDF">
                        <i class="fas fa-file-pdf me-1"></i> PDF
                    </button>
                    <button class="btn btn-secondary btn-sm" onclick="toggleFiltrosAvanzados()">
                        <i class="fas fa-filter me-1"></i> Filtros Avanzados
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros avanzados (colapsables) -->
    <div class="card mb-2" id="filtrosAvanzados" style="display: none;">
        <div class="card-body p-2">
            <div class="fw-bold mb-2"><i class="fas fa-filter me-2"></i>Filtros Avanzados</div>
            <form method="GET">
                <div class="row g-2 align-items-end">
                <input type="hidden" name="ctrl" value="dashboard">
                <input type="hidden" name="action" value="admin">
                <input type="hidden" name="page" value="pedidos">
                <div class="col-md-3">
                    <label class="form-label">Cliente</label>
                    <input type="text" class="form-control" placeholder="Nombre del cliente" name="cliente" value="<?= $_GET['cliente'] ?? '' ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Estado</label>
                    <select class="form-select" name="estado">
                        <option value="">Todos los estados</option>
                        <option value="Pendiente" <?= ($_GET['estado'] ?? '') === 'Pendiente' ? 'selected' : '' ?>>Pendiente</option>
                        <option value="En proceso" <?= ($_GET['estado'] ?? '') === 'En proceso' ? 'selected' : '' ?>>En proceso</option>
                        <option value="Completado" <?= ($_GET['estado'] ?? '') === 'Completado' ? 'selected' : '' ?>>Completado</option>
                        <option value="Cancelado" <?= ($_GET['estado'] ?? '') === 'Cancelado' ? 'selected' : '' ?>>Cancelado</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Fecha Desde</label>
                    <input type="date" class="form-control" name="fecha_desde" value="<?= $_GET['fecha_desde'] ?? '' ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Fecha Hasta</label>
                    <input type="date" class="form-control" name="fecha_hasta" value="<?= $_GET['fecha_hasta'] ?? '' ?>">
                </div>
                <div class="col-md-auto d-flex gap-2 align-items-end">
                    <button type="submit" class="btn btn-success btn-sm px-3">Filtrar</button>
                    <a href="?ctrl=dashboard&action=admin&page=pedidos" class="btn btn-link btn-sm text-secondary">Limpiar</a>
                </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Listado de pedidos CON DATOS REALES -->
    <div class="card mb-2">
        <div class="card-header p-2 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fs-6"><i class="fas fa-list me-2"></i>Lista de Pedidos</h5>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-secondary">Página <?= $pagina_actual ?> de <?= $total_paginas ?></span>
                <span class="badge bg-primary"><?= $total_registros ?> pedidos totales</span>
            </div>
        </div>
        <div class="card-body p-0">
            <?php if (!empty($pedidos)): ?>
            <div class="table-responsive">
                <table class="table table-sm table-striped align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="sortable" onclick="ordenarPor('numped')" style="cursor: pointer;">
                                Pedido <?= obtenerIconoOrden('numped') ?>
                            </th>
                            <th class="sortable" onclick="ordenarPor('cliente')" style="cursor: pointer;">
                                Cliente <?= obtenerIconoOrden('cliente') ?>
                            </th>
            <th class="sortable" onclick="ordenarPor('fecha_pedido')" style="cursor: pointer;">
                                Fecha Creación <?= obtenerIconoOrden('fecha_pedido') ?>
                            </th>
                            <th class="sortable" onclick="ordenarPor('fecha_entrega')" style="cursor: pointer;">
                                Fecha Entrega <?= obtenerIconoOrden('fecha_entrega') ?>
                            </th>
                            <th>Productos</th>
                            <th class="sortable" onclick="ordenarPor('monto_total')" style="cursor: pointer;">
                                Total <?= obtenerIconoOrden('monto_total') ?>
                            </th>
                            <th class="sortable" onclick="ordenarPor('estado')" style="cursor: pointer;">
                                Estado Pedido <?= obtenerIconoOrden('estado') ?>
                            </th>
                            <th>Estado Pago</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pedidos as $pedido): ?>
                        <tr>
                            <td>
                                <strong class="text-primary"><?= htmlspecialchars($pedido['numped']) ?></strong>
                                <br><small class="text-muted">ID: <?= $pedido['idped'] ?></small>
                            </td>
                            <td>
                                <strong><?= htmlspecialchars($pedido['cliente_nombre']) ?></strong>
                                <br><small class="text-muted"><?= htmlspecialchars($pedido['cliente_email']) ?></small>
                            </td>
                            <td>
                                <?= date('d/m/Y', strtotime($pedido['fecha_pedido'])) ?>
                                <br><small class="text-muted"><?= date('H:i', strtotime($pedido['fecha_pedido'])) ?></small>
                            </td>
                            <td>
                                <?php if (!empty($pedido['fecha_entrega_solicitada'])): ?>
                                    <i class="fas fa-calendar-check text-primary"></i> <?= date('d/m/Y', strtotime($pedido['fecha_entrega_solicitada'])) ?>
                                <?php else: ?>
                                    <small class="text-muted"><em>Sin fecha</em></small>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark"><?= $pedido['total_productos'] ?></span>
                            </td>
                            <td>
                                <strong class="text-success">$<?= number_format($pedido['monto_total'], 2) ?></strong>
                            </td>
                            <td>
                                <span class="badge bg-<?= 
                                    $pedido['estado'] === 'Pendiente' ? 'warning' : 
                                    ($pedido['estado'] === 'En proceso' ? 'info' : 
                                    ($pedido['estado'] === 'Completado' ? 'success' : 'secondary')) 
                                ?>">
                                    <?= htmlspecialchars($pedido['estado']) ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($pedido['estado_pago']): ?>
                                    <span class="badge bg-<?= 
                                        $pedido['estado_pago'] === 'Pendiente' ? 'warning' : 
                                        ($pedido['estado_pago'] === 'Completado' ? 'success' : 'danger') 
                                    ?>">
                                        <?= htmlspecialchars($pedido['estado_pago']) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Sin pago</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <!-- Botón de ver detalle -->
                                    <button type="button" class="btn btn-sm btn-info" onclick="verDetallePedido(<?= $pedido['idped'] ?>)" title="Ver detalles">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    
                                    <!-- Botón de editar (solo si no está completado o cancelado) -->
                                    <?php if ($pedido['estado'] !== 'Completado' && $pedido['estado'] !== 'Cancelado'): ?>
                                        <button type="button" class="btn btn-sm btn-warning" onclick="editarPedido(<?= $pedido['idped'] ?>)" title="Editar pedido">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    <?php endif; ?>
                                    
                                    <!-- Botones de cambio de estado directo -->
                                    <?php if ($pedido['estado'] === 'Pendiente'): ?>
                                        <button type="button" class="btn btn-sm btn-primary" onclick="cambiarEstado(<?= $pedido['idped'] ?>, 'En proceso')" title="Procesar pedido">
                                            <i class="fas fa-cog"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-success" onclick="cambiarEstado(<?= $pedido['idped'] ?>, 'Completado')" title="Marcar completado">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger" onclick="cambiarEstado(<?= $pedido['idped'] ?>, 'Cancelado')" title="Cancelar pedido">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    <?php elseif ($pedido['estado'] === 'En proceso'): ?>
                                        <button type="button" class="btn btn-sm btn-success" onclick="cambiarEstado(<?= $pedido['idped'] ?>, 'Completado')" title="Marcar completado">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger" onclick="cambiarEstado(<?= $pedido['idped'] ?>, 'Cancelado')" title="Cancelar pedido">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Finalizado</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                    <h4>No hay pedidos</h4>
                    <p class="text-muted">No se encontraron pedidos con los filtros aplicados</p>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#nuevoPedidoModal">
                        <i class="fas fa-plus me-2"></i>Crear primer pedido
                    </button>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Paginación -->
        <?php if ($total_paginas > 1): ?>
        <div class="card-footer">
            <nav>
                <ul class="pagination pagination-sm justify-content-center mb-0">
                    <!-- Botón Anterior -->
                    <li class="page-item <?= $pagina_actual <= 1 ? 'disabled' : '' ?>">
                        <a class="page-link" href="?ctrl=dashboard&action=admin&page=pedidos&pagina=<?= $pagina_actual - 1 ?><?= isset($_GET['estado']) ? '&estado=' . urlencode($_GET['estado']) : '' ?><?= isset($_GET['cliente']) ? '&cliente=' . urlencode($_GET['cliente']) : '' ?><?= isset($_GET['fecha_desde']) ? '&fecha_desde=' . $_GET['fecha_desde'] : '' ?><?= isset($_GET['fecha_hasta']) ? '&fecha_hasta=' . $_GET['fecha_hasta'] : '' ?>">
                            <i class="fas fa-chevron-left"></i> Anterior
                        </a>
                    </li>
                    
                    <?php
                    // Mostrar páginas cercanas
                    $rango = 2;
                    $inicio = max(1, $pagina_actual - $rango);
                    $fin = min($total_paginas, $pagina_actual + $rango);
                    
                    if ($inicio > 1) {
                        echo '<li class="page-item"><a class="page-link" href="?ctrl=dashboard&action=admin&page=pedidos&pagina=1' . (isset($_GET['estado']) ? '&estado=' . urlencode($_GET['estado']) : '') . (isset($_GET['cliente']) ? '&cliente=' . urlencode($_GET['cliente']) : '') . (isset($_GET['fecha_desde']) ? '&fecha_desde=' . $_GET['fecha_desde'] : '') . (isset($_GET['fecha_hasta']) ? '&fecha_hasta=' . $_GET['fecha_hasta'] : '') . '">1</a></li>';
                        if ($inicio > 2) echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                    }
                    
                    for ($i = $inicio; $i <= $fin; $i++):
                    ?>
                        <li class="page-item <?= $i == $pagina_actual ? 'active' : '' ?>">
                            <a class="page-link" href="?ctrl=dashboard&action=admin&page=pedidos&pagina=<?= $i ?><?= isset($_GET['estado']) ? '&estado=' . urlencode($_GET['estado']) : '' ?><?= isset($_GET['cliente']) ? '&cliente=' . urlencode($_GET['cliente']) : '' ?><?= isset($_GET['fecha_desde']) ? '&fecha_desde=' . $_GET['fecha_desde'] : '' ?><?= isset($_GET['fecha_hasta']) ? '&fecha_hasta=' . $_GET['fecha_hasta'] : '' ?>">
                                <?= $i ?>
                            </a>
                        </li>
                    <?php
                    endfor;
                    
                    if ($fin < $total_paginas) {
                        if ($fin < $total_paginas - 1) echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                        echo '<li class="page-item"><a class="page-link" href="?ctrl=dashboard&action=admin&page=pedidos&pagina=' . $total_paginas . (isset($_GET['estado']) ? '&estado=' . urlencode($_GET['estado']) : '') . (isset($_GET['cliente']) ? '&cliente=' . urlencode($_GET['cliente']) : '') . (isset($_GET['fecha_desde']) ? '&fecha_desde=' . $_GET['fecha_desde'] : '') . (isset($_GET['fecha_hasta']) ? '&fecha_hasta=' . $_GET['fecha_hasta'] : '') . '">' . $total_paginas . '</a></li>';
                    }
                    ?>
                    
                    <!-- Botón Siguiente -->
                    <li class="page-item <?= $pagina_actual >= $total_paginas ? 'disabled' : '' ?>">
                        <a class="page-link" href="?ctrl=dashboard&action=admin&page=pedidos&pagina=<?= $pagina_actual + 1 ?><?= isset($_GET['estado']) ? '&estado=' . urlencode($_GET['estado']) : '' ?><?= isset($_GET['cliente']) ? '&cliente=' . urlencode($_GET['cliente']) : '' ?><?= isset($_GET['fecha_desde']) ? '&fecha_desde=' . $_GET['fecha_desde'] : '' ?><?= isset($_GET['fecha_hasta']) ? '&fecha_hasta=' . $_GET['fecha_hasta'] : '' ?>">
                            Siguiente <i class="fas fa-chevron-right"></i>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal para ver detalles del pedido -->
<div class="modal fade" id="modalDetallePedido" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-receipt me-2"></i>Detalles del Pedido <span id="modalNumeroPedido"></span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modalDetallePedidoBody">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para editar pedido -->
<div class="modal fade" id="modalEditarPedido" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-edit me-2"></i>Editar Pedido <span id="modalEditarNumeroPedido"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEditarPedido" onsubmit="guardarEdicionPedido(event)">
                <div class="modal-body">
                    <input type="hidden" id="editIdPedido" name="id_pedido">
                    
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="editDireccion" class="form-label">
                                <i class="fas fa-map-marker-alt me-1"></i>Dirección de Entrega
                            </label>
                            <textarea class="form-control" id="editDireccion" name="direccion_entrega" rows="2" required></textarea>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="editFechaEntrega" class="form-label">
                                <i class="fas fa-calendar-alt me-1"></i>Fecha de Entrega Solicitada
                            </label>
                            <input type="date" class="form-control" id="editFechaEntrega" name="fecha_entrega_solicitada" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="editEstado" class="form-label">
                                <i class="fas fa-info-circle me-1"></i>Estado del Pedido
                            </label>
                            <select class="form-select" id="editEstado" name="estado" required>
                                <option value="Pendiente">Pendiente</option>
                                <option value="En proceso">En Proceso</option>
                                <option value="Completado">Completado</option>
                                <option value="Cancelado">Cancelado</option>
                            </select>
                        </div>
                        
                        <div class="col-12">
                            <label for="editNotas" class="form-label">
                                <i class="fas fa-sticky-note me-1"></i>Notas Internas <small class="text-muted">(opcional)</small>
                            </label>
                            <textarea class="form-control" id="editNotas" name="notas" rows="3" placeholder="Agregar notas internas sobre el pedido..."></textarea>
                            <small class="text-muted">Estas notas son solo para uso interno y no serán visibles para el cliente.</small>
                        </div>
                        
                        <div class="col-12">
                            <label for="editEmpleado" class="form-label">
                                <i class="fas fa-user-tie me-1"></i>Asignar Empleado <small class="text-muted">(opcional)</small>
                            </label>
                            <select class="form-select" id="editEmpleado" name="empleado_id">
                                <option value="">Sin asignar</option>
                                <!-- Se cargarán dinámicamente los empleados -->
                            </select>
                        </div>
                    </div>
                    
                    <div id="alertaEditarPedido"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- JavaScript funcional para acciones de pedidos -->
<script>
// Función para cambiar estado de pedido con AJAX
function cambiarEstado(idPedido, nuevoEstado) {
    const iconos = {
        'En proceso': '⚙️',
        'Completado': '✅', 
        'Cancelado': '❌'
    };
    
    if (confirm(`${iconos[nuevoEstado]} ¿Estás seguro de cambiar el estado del pedido #${idPedido} a "${nuevoEstado}"?`)) {
        // Mostrar loading en los botones
        const fila = document.querySelector(`tr:has(small:contains("ID: ${idPedido}"))`);
        const botones = document.querySelectorAll(`button[onclick*="cambiarEstado(${idPedido}"]`);
        const botonesOriginales = [];
        
        botones.forEach(btn => {
            botonesOriginales.push(btn.innerHTML);
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        });
        
        // Enviar petición AJAX
        fetch(window.location.href, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: new URLSearchParams({
                'action': 'cambiar_estado',
                'id_pedido': idPedido,
                'nuevo_estado': nuevoEstado
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Mostrar mensaje de éxito
                mostrarAlerta('success', data.mensaje);
                
                // Recargar la página después de 1 segundo para actualizar la tabla
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                mostrarAlerta('danger', 'Error: ' + data.mensaje);
                // Restaurar botones
                botones.forEach((btn, index) => {
                    btn.disabled = false;
                    btn.innerHTML = botonesOriginales[index];
                });
            }
        })
        .catch(error => {
            mostrarAlerta('danger', 'Error de conexión: ' + error);
            // Restaurar botones
            botones.forEach((btn, index) => {
                btn.disabled = false;
                btn.innerHTML = botonesOriginales[index];
            });
        });
    }
}

// Función para mostrar alertas
function mostrarAlerta(tipo, mensaje) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${tipo} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        ${mensaje}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    const container = document.querySelector('.container');
    container.insertBefore(alertDiv, container.firstChild);
    
    // Auto-dismiss después de 5 segundos
    setTimeout(() => {
        alertDiv.remove();
    }, 5000);
}

// Función para ver detalles de pedido en modal
function verDetallePedido(idPedido) {
    const modal = new bootstrap.Modal(document.getElementById('modalDetallePedido'));
    const modalBody = document.getElementById('modalDetallePedidoBody');
    const modalNumero = document.getElementById('modalNumeroPedido');
    
    // Mostrar loading
    modalBody.innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
        </div>
    `;
    
    modal.show();
    
    // Buscar información del pedido en la tabla
    const filas = document.querySelectorAll('tbody tr');
    let infoPedido = null;
    
    filas.forEach(fila => {
        if (fila.innerHTML.includes(`ID: ${idPedido}`)) {
            const celdas = fila.querySelectorAll('td');
            if (celdas.length >= 8) {
                infoPedido = {
                    numero: celdas[0].querySelector('strong').textContent.trim(),
                    id: idPedido,
                    cliente: celdas[1].querySelector('strong').textContent.trim(),
                    email: celdas[1].querySelector('small').textContent.trim(),
                    fechaCreacion: celdas[2].textContent.trim(),
                    fechaEntrega: celdas[3].textContent.trim(),
                    totalProductos: celdas[4].textContent.trim(),
                    total: celdas[5].textContent.trim(),
                    estado: celdas[6].textContent.trim(),
                    estadoPago: celdas[7].textContent.trim()
                };
            }
        }
    });
    
    if (infoPedido) {
        modalNumero.textContent = `#${infoPedido.numero}`;
        
        // Cargar detalles de productos
        fetch(`/Original-Floraltech/controllers/Cpedido.php?action=detalle&id=${idPedido}`)
            .then(response => response.json())
            .then(data => {
                let productosHTML = '';
                if (data.productos && data.productos.length > 0) {
                    productosHTML = `
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Producto</th>
                                        <th>Cantidad</th>
                                        <th>Precio Unit.</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                    `;
                    data.productos.forEach(prod => {
                        productosHTML += `
                            <tr>
                                <td>${prod.nombre}</td>
                                <td>${prod.cantidad}</td>
                                <td>$${parseFloat(prod.precio_unitario).toFixed(2)}</td>
                                <td>$${parseFloat(prod.subtotal).toFixed(2)}</td>
                            </tr>
                        `;
                    });
                    productosHTML += `
                                </tbody>
                            </table>
                        </div>
                    `;
                } else {
                    productosHTML = '<p class="text-muted">No se pudieron cargar los productos</p>';
                }
                
                modalBody.innerHTML = `
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-primary mb-3"><i class="fas fa-user me-2"></i>Información del Cliente</h6>
                            <p><strong>Nombre:</strong> ${infoPedido.cliente}</p>
                            <p><strong>Email:</strong> ${infoPedido.email}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-primary mb-3"><i class="fas fa-info-circle me-2"></i>Información del Pedido</h6>
                            <p><strong>Número:</strong> ${infoPedido.numero}</p>
                            <p><strong>Fecha Creación:</strong> ${infoPedido.fechaCreacion}</p>
                            <p><strong>Fecha Entrega:</strong> ${infoPedido.fechaEntrega}</p>
                            <p><strong>Estado:</strong> <span class="badge bg-info">${infoPedido.estado}</span></p>
                            <p><strong>Estado Pago:</strong> <span class="badge bg-warning">${infoPedido.estadoPago}</span></p>
                        </div>
                    </div>
                    <hr>
                    <h6 class="text-primary mb-3"><i class="fas fa-box me-2"></i>Productos</h6>
                    ${productosHTML}
                    <div class="text-end mt-3">
                        <h5><strong>Total: ${infoPedido.total}</strong></h5>
                    </div>
                `;
            })
            .catch(error => {
                // Si falla el fetch, mostrar info básica
                modalBody.innerHTML = `
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-primary mb-3"><i class="fas fa-user me-2"></i>Información del Cliente</h6>
                            <p><strong>Nombre:</strong> ${infoPedido.cliente}</p>
                            <p><strong>Email:</strong> ${infoPedido.email}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-primary mb-3"><i class="fas fa-info-circle me-2"></i>Información del Pedido</h6>
                            <p><strong>Número:</strong> ${infoPedido.numero}</p>
                            <p><strong>Fecha Creación:</strong> ${infoPedido.fechaCreacion}</p>
                            <p><strong>Fecha Entrega:</strong> ${infoPedido.fechaEntrega}</p>
                            <p><strong>Estado:</strong> <span class="badge bg-info">${infoPedido.estado}</span></p>
                            <p><strong>Estado Pago:</strong> <span class="badge bg-warning">${infoPedido.estadoPago}</span></p>
                        </div>
                    </div>
                    <hr>
                    <div class="text-center text-muted">
                        <p>No se pudieron cargar los detalles de productos</p>
                    </div>
                    <div class="text-end mt-3">
                        <h5><strong>Total: ${infoPedido.total}</strong></h5>
                    </div>
                `;
            });
    } else {
        modalBody.innerHTML = '<div class="alert alert-danger">No se pudo encontrar la información del pedido</div>';
    }
}

// Auto-dismiss alerts después de 5 segundos
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            if (alert && alert.parentNode) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }
        }, 5000);
    });
    
    // Agregar tooltips a los botones
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>

<script src="/Original-Floraltech/assets/js/gestion_pedidos.js"></script>
