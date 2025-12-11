<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once($_SERVER['DOCUMENT_ROOT'] . '/Original-Floraltech/views/config/database.php');

try {
    $database = new Database();
    $conn = $database->getConnection();

    if (!$conn) {
        throw new Exception('No se pudo conectar a la base de datos');
    }
} catch (Exception $e) {
    die('Error de conexion: ' . $e->getMessage());
}

$mensaje_exito = '';
$mensaje_error = '';
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

// Solo mantenemos la accion de cambio de estado desde esta vista
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'cambiar_estado') {
    $idPedido = isset($_POST['id_pedido']) ? (int)$_POST['id_pedido'] : 0;
    $nuevoEstado = trim($_POST['nuevo_estado'] ?? '');

    if ($idPedido > 0 && $nuevoEstado !== '') {
        try {
            $stmt = $conn->prepare('UPDATE ped SET estado = ? WHERE idped = ?');
            $stmt->execute([$nuevoEstado, $idPedido]);

            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'mensaje' => 'Estado actualizado correctamente']);
                exit;
            }
            $mensaje_exito = 'Estado actualizado correctamente.';
        } catch (PDOException $e) {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'mensaje' => $e->getMessage()]);
                exit;
            }
            $mensaje_error = 'Error al actualizar el estado: ' . $e->getMessage();
        }
    } elseif ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'mensaje' => 'Datos incompletos']);
        exit;
    }
}

// Helpers
function obtenerOrdenamiento(): string
{
    $orden_columna = $_GET['orden'] ?? 'fecha_pedido';
    $orden_dir = strtoupper($_GET['dir'] ?? 'DESC');

    $columnas_validas = [
        'fecha_pedido'   => 'p.fecha_pedido',
        'numped'         => 'p.numped',
        'cliente'        => 'c.nombre',
        'monto_total'    => 'p.monto_total',
        'estado'         => 'p.estado',
        'fecha_entrega'  => 'p.fecha_entrega_solicitada'
    ];

    $direcciones_validas = ['ASC', 'DESC'];
    $columna = $columnas_validas[$orden_columna] ?? 'p.fecha_pedido';
    $direccion = in_array($orden_dir, $direcciones_validas, true) ? $orden_dir : 'DESC';

    return "$columna $direccion";
}

function obtenerIconoOrden(string $columna): string
{
    $orden_actual = $_GET['orden'] ?? 'fecha_pedido';
    $dir_actual = strtoupper($_GET['dir'] ?? 'DESC');

    if ($orden_actual === $columna) {
        return $dir_actual === 'ASC'
            ? '<i class="fas fa-sort-up"></i>'
            : '<i class="fas fa-sort-down"></i>';
    }
    return '<i class="fas fa-sort text-muted"></i>';
}

function badgeEstado(string $estado): string
{
    switch ($estado) {
        case 'Pendiente':
            return 'warning text-dark';
        case 'En proceso':
            return 'info text-white';
        case 'Completado':
            return 'success';
        case 'Cancelado':
            return 'danger';
        default:
            return 'secondary';
    }
}

function badgePago(?string $estado): string
{
    $estado = $estado ?? '';
    $low = strtolower($estado);
    if ($low === 'completado' || $low === 'aprobado' || $low === 'aceptado') {
        return 'success';
    }
    if ($low === 'pendiente') {
        return 'warning text-dark';
    }
    if ($low === 'cancelado' || $low === 'rechazado') {
        return 'danger';
    }
    return 'secondary';
}

// Filtros
$where_conditions = ['1=1'];
$params = [];

$busqueda = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';
if ($busqueda !== '') {
    $like = '%' . $busqueda . '%';
    $where_conditions[] = '(p.numped LIKE ? OR c.nombre LIKE ? OR c.email LIKE ? OR p.direccion_entrega LIKE ?)';
    $params = array_merge($params, [$like, $like, $like, $like]);
}

$estadoFiltro = isset($_GET['estado']) ? trim($_GET['estado']) : '';
if ($estadoFiltro !== '') {
    $where_conditions[] = 'p.estado = ?';
    $params[] = $estadoFiltro;
}

$clienteFiltro = isset($_GET['cliente']) ? trim($_GET['cliente']) : '';
if ($clienteFiltro !== '') {
    $where_conditions[] = 'c.nombre LIKE ?';
    $params[] = '%' . $clienteFiltro . '%';
}

$fechaDesde = isset($_GET['fecha_desde']) ? trim($_GET['fecha_desde']) : '';
$fechaHasta = isset($_GET['fecha_hasta']) ? trim($_GET['fecha_hasta']) : '';

if ($fechaDesde !== '') {
    $where_conditions[] = 'DATE(p.fecha_pedido) >= ?';
    $params[] = $fechaDesde;
}
if ($fechaHasta !== '') {
    $where_conditions[] = 'DATE(p.fecha_pedido) <= ?';
    $params[] = $fechaHasta;
}

$where_clause = implode(' AND ', $where_conditions);

// Paginacion
$registros_por_pagina = 20;
$pagina_actual = isset($_GET['pagina']) ? max(1, (int)$_GET['pagina']) : 1;
$offset = ($pagina_actual - 1) * $registros_por_pagina;

try {
    $sql_count = "SELECT COUNT(DISTINCT p.idped) AS total
                  FROM ped p
                  INNER JOIN cli c ON p.cli_idcli = c.idcli
                  WHERE $where_clause";
    $stmt_count = $conn->prepare($sql_count);
    $stmt_count->execute($params);
    $total_registros = (int)($stmt_count->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);
    $total_paginas = max(1, (int)ceil($total_registros / $registros_por_pagina));
} catch (PDOException $e) {
    $total_registros = 0;
    $total_paginas = 1;
    $mensaje_error = 'Error al contar pedidos: ' . $e->getMessage();
}

// Estadisticas
$totalPedidos = 0;
$totalPendientes = 0;
$totalEnProceso = 0;
$totalCompletados = 0;
$ventasTotales = 0.0;

try {
    $sql_stats = "SELECT
            COUNT(DISTINCT p.idped) AS total_pedidos,
            SUM(CASE WHEN p.estado = 'Pendiente' THEN 1 ELSE 0 END) AS pendientes,
            SUM(CASE WHEN p.estado = 'En proceso' THEN 1 ELSE 0 END) AS en_proceso,
            SUM(CASE WHEN p.estado = 'Completado' THEN 1 ELSE 0 END) AS completados,
            SUM(CASE WHEN LOWER(pg.estado_pag) IN ('completado','aprobado','aceptado') THEN p.monto_total ELSE 0 END) AS ventas
        FROM ped p
        INNER JOIN cli c ON p.cli_idcli = c.idcli
        LEFT JOIN pagos pg ON pg.ped_idped = p.idped
        WHERE $where_clause";
    $stmt_stats = $conn->prepare($sql_stats);
    $stmt_stats->execute($params);
    $stats = $stmt_stats->fetch(PDO::FETCH_ASSOC) ?: [];
    $totalPedidos = (int)($stats['total_pedidos'] ?? 0);
    $totalPendientes = (int)($stats['pendientes'] ?? 0);
    $totalEnProceso = (int)($stats['en_proceso'] ?? 0);
    $totalCompletados = (int)($stats['completados'] ?? 0);
    $ventasTotales = (float)($stats['ventas'] ?? 0);
} catch (PDOException $e) {
    $mensaje_error = 'Error al obtener estadisticas: ' . $e->getMessage();
}

// Pedidos
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
            p.notas,
            p.empleado_id,
            c.nombre AS cliente_nombre,
            c.email AS cliente_email,
            c.telefono AS cliente_telefono,
            c.direccion AS cliente_direccion,
            pg.idpago,
            pg.metodo_pago,
            pg.estado_pag AS estado_pago,
            pg.fecha_pago,
            COUNT(dp.idtflor) AS total_productos
        FROM ped p
        INNER JOIN cli c ON p.cli_idcli = c.idcli
        LEFT JOIN pagos pg ON pg.ped_idped = p.idped
        LEFT JOIN detped dp ON dp.idped = p.idped
        WHERE $where_clause
        GROUP BY p.idped
        ORDER BY " . obtenerOrdenamiento() . "
        LIMIT $registros_por_pagina OFFSET $offset
    ";

    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $mensaje_error = 'Error al obtener pedidos: ' . $e->getMessage();
    $pedidos = [];
}
?>

<!-- Bootstrap 5 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- FontAwesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<style>
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

<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-4 py-3 px-3 rounded-4 shadow-sm text-white" style="background: linear-gradient(120deg, #0d6efd 0%, #5b21b6 60%, #1e1b4b 100%);">
        <div>
            <p class="mb-1 opacity-75 text-uppercase" style="letter-spacing:1px; color: #ffff"><i class="fas fa-seedling me-2"></i>FloralTech Admin</p>
            <h2 class="mb-0 fw-bold" style="color: #ffff">Gestion de Pedidos</h2>
        </div>
        <button class="btn btn-light text-primary fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#nuevoPedidoModal">
            <i class="fas fa-plus me-2"></i>Nuevo pedido
        </button>
    </div>

    <?php if ($mensaje_exito): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($mensaje_exito) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <?php if ($mensaje_error): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($mensaje_error) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row g-3 mb-4">
        <div class="col-6 col-md-2">
            <div class="card shadow-sm border-0 h-100 metric-card metric-blue">
                <div class="card-body text-center">
                    <small class="text-muted text-uppercase">Total</small>
                    <div class="fs-3 fw-bold text-primary"><?= $totalPedidos ?></div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="card shadow-sm border-0 h-100 metric-card metric-yellow">
                <div class="card-body text-center">
                    <small class="text-muted text-uppercase">Pendientes</small>
                    <div class="fs-4 fw-bold text-warning"><?= $totalPendientes ?></div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="card shadow-sm border-0 h-100 metric-card metric-purple">
                <div class="card-body text-center">
                    <small class="text-muted text-uppercase">En proceso</small>
                    <div class="fs-4 fw-bold text-info"><?= $totalEnProceso ?></div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="card shadow-sm border-0 h-100 metric-card metric-green">
                <div class="card-body text-center">
                    <small class="text-muted text-uppercase">Completados</small>
                    <div class="fs-4 fw-bold text-success"><?= $totalCompletados ?></div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card shadow-sm border-0 h-100 metric-card metric-pink">
                <div class="card-body text-center">
                    <small class="text-muted text-uppercase">Ventas Totales</small>
                    <div class="fs-4 fw-bold text-success">$<?= number_format($ventasTotales, 2) ?></div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0 mb-3">
        <div class="card-body py-3">
            <div class="row align-items-center g-2">
                <div class="col-lg-7">
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" id="busquedaRapida" placeholder="Buscar por numero, cliente, email..." value="<?= htmlspecialchars($busqueda) ?>">
                    </div>
                </div>
                <div class="col-lg-5 d-flex justify-content-lg-end gap-2 flex-wrap">
                    <span class="badge bg-secondary">Pagina <?= $pagina_actual ?> de <?= $total_paginas ?></span>
                    <span class="badge bg-primary"><?= $total_registros ?> pedidos</span>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fs-6"><i class="fas fa-list me-2"></i>Lista de pedidos</h5>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-secondary">Pagina <?= $pagina_actual ?> de <?= $total_paginas ?></span>
                <span class="badge bg-primary"><?= $total_registros ?> pedidos totales</span>
            </div>
        </div>
        <div class="card-body p-0">
            <?php if (!empty($pedidos)): ?>
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0" id="tablaPedidos">
                        <thead class="table-light">
                            <tr>
                                <th style="cursor:pointer;" onclick="ordenarPor('numped')">Pedido <?= obtenerIconoOrden('numped') ?></th>
                                <th style="cursor:pointer;" onclick="ordenarPor('cliente')">Cliente <?= obtenerIconoOrden('cliente') ?></th>
                                <th style="cursor:pointer;" onclick="ordenarPor('fecha_pedido')">Fecha creacion <?= obtenerIconoOrden('fecha_pedido') ?></th>
                                <th style="cursor:pointer;" onclick="ordenarPor('fecha_entrega')">Fecha entrega <?= obtenerIconoOrden('fecha_entrega') ?></th>
                                <th class="text-center">Productos</th>
                                <th style="cursor:pointer;" onclick="ordenarPor('monto_total')">Total <?= obtenerIconoOrden('monto_total') ?></th>
                                <th style="cursor:pointer;" onclick="ordenarPor('estado')">Estado pedido <?= obtenerIconoOrden('estado') ?></th>
                                <th>Estado pago</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pedidos as $pedido): ?>
                                <tr
                                    data-id="<?= (int)$pedido['idped'] ?>"
                                    data-numero="<?= htmlspecialchars($pedido['numped']) ?>"
                                    data-cliente="<?= htmlspecialchars($pedido['cliente_nombre']) ?>"
                                    data-email="<?= htmlspecialchars($pedido['cliente_email']) ?>"
                                    data-fecha-creacion="<?= htmlspecialchars($pedido['fecha_pedido']) ?>"
                                    data-fecha-entrega="<?= htmlspecialchars($pedido['fecha_entrega_solicitada'] ?? '') ?>"
                                    data-total="<?= number_format((float)$pedido['monto_total'], 2, '.', '') ?>"
                                    data-estado="<?= htmlspecialchars($pedido['estado']) ?>"
                                    data-estado-pago="<?= htmlspecialchars($pedido['estado_pago'] ?? 'Sin pago') ?>"
                                    data-total-productos="<?= (int)$pedido['total_productos'] ?>"
                                >
                                    <td>
                                        <strong class="text-primary"><?= htmlspecialchars($pedido['numped']) ?></strong>
                                        <br><small class="text-muted">ID: <?= (int)$pedido['idped'] ?></small>
                                    </td>
                                    <td>
                                        <strong><?= htmlspecialchars($pedido['cliente_nombre']) ?></strong>
                                        <br><small class="text-muted"><?= htmlspecialchars($pedido['cliente_email'] ?? '') ?></small>
                                    </td>
                                    <td>
                                        <?= !empty($pedido['fecha_pedido']) ? date('d/m/Y H:i', strtotime($pedido['fecha_pedido'])) : 'N/D' ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($pedido['fecha_entrega_solicitada'])): ?>
                                            <i class="fas fa-calendar-check text-primary"></i>
                                            <?= date('d/m/Y', strtotime($pedido['fecha_entrega_solicitada'])) ?>
                                        <?php else: ?>
                                            <small class="text-muted"><em>Sin fecha</em></small>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark"><?= (int)$pedido['total_productos'] ?></span>
                                    </td>
                                    <td>
                                        <strong class="text-success">$<?= number_format((float)$pedido['monto_total'], 2) ?></strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?= badgeEstado($pedido['estado']) ?>"><?= htmlspecialchars($pedido['estado']) ?></span>
                                    </td>
                                    <td>
                                        <?php if (!empty($pedido['estado_pago'])): ?>
                                            <span class="badge bg-<?= badgePago($pedido['estado_pago']) ?>">
                                                <?= htmlspecialchars($pedido['estado_pago']) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Sin pago</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button type="button" class="btn btn-outline-secondary" onclick="verDetallePedido(<?= (int)$pedido['idped'] ?>)" title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <?php if ($pedido['estado'] !== 'Completado' && $pedido['estado'] !== 'Cancelado'): ?>
                                                <button type="button" class="btn btn-outline-warning text-dark" onclick="editarPedido(<?= (int)$pedido['idped'] ?>)" title="Editar pedido">
                                                    <i class="fas fa-pen"></i>
                                                </button>
                                            <?php endif; ?>
                                            <button type="button" class="btn btn-outline-info text-info" onclick="editarPago(<?= (int)$pedido['idped'] ?>)" title="Gestionar pago">
                                                <i class="fas fa-credit-card"></i>
                                            </button>
                                            <?php if ($pedido['estado'] === 'Pendiente'): ?>
                                                <button type="button" class="btn btn-outline-primary" onclick="cambiarEstado(<?= (int)$pedido['idped'] ?>, 'En proceso')" title="Procesar pedido">
                                                    <i class="fas fa-cog"></i>
                                                </button>
                                                <button type="button" class="btn btn-outline-success" onclick="cambiarEstado(<?= (int)$pedido['idped'] ?>, 'Completado')" title="Marcar completado">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                <button type="button" class="btn btn-outline-danger" onclick="cambiarEstado(<?= (int)$pedido['idped'] ?>, 'Cancelado')" title="Cancelar pedido">
                                                    <i class="fas fa-ban"></i>
                                                </button>
                                            <?php elseif ($pedido['estado'] === 'En proceso'): ?>
                                                <button type="button" class="btn btn-outline-success" onclick="cambiarEstado(<?= (int)$pedido['idped'] ?>, 'Completado')" title="Marcar completado">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                <button type="button" class="btn btn-outline-danger" onclick="cambiarEstado(<?= (int)$pedido['idped'] ?>, 'Cancelado')" title="Cancelar pedido">
                                                    <i class="fas fa-ban"></i>
                                                </button>
                                            <?php else: ?>
                                                <?php
                                                    $accionesFinales = [
                                                        'Pendiente' => ['class' => 'btn-outline-secondary text-dark', 'title' => 'Reabrir pedido', 'icon' => 'fa-undo'],
                                                        'En proceso' => ['class' => 'btn-outline-primary', 'title' => 'Procesar pedido', 'icon' => 'fa-cog'],
                                                        'Completado' => ['class' => 'btn-outline-success', 'title' => 'Marcar completado', 'icon' => 'fa-check'],
                                                        'Cancelado' => ['class' => 'btn-outline-danger', 'title' => 'Cancelar pedido', 'icon' => 'fa-ban'],
                                                    ];
                                                ?>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <?php foreach ($accionesFinales as $estadoAccion => $cfg): ?>
                                                        <button type="button"
                                                            class="btn <?= htmlspecialchars($cfg['class'], ENT_QUOTES) ?>"
                                                            onclick="cambiarEstado(<?= (int)$pedido['idped'] ?>, '<?= htmlspecialchars($estadoAccion, ENT_QUOTES) ?>')"
                                                            title="<?= htmlspecialchars($cfg['title'], ENT_QUOTES) ?>"
                                                            <?= $estadoAccion === $pedido['estado'] ? 'disabled' : '' ?>
                                                        >
                                                            <i class="fas <?= htmlspecialchars($cfg['icon'], ENT_QUOTES) ?>"></i>
                                                        </button>
                                                    <?php endforeach; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                    <h4>No hay pedidos</h4>
                    <p class="text-muted">No se encontraron pedidos con los filtros aplicados.</p>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#nuevoPedidoModal">
                        <i class="fas fa-plus me-2"></i>Crear primer pedido
                    </button>
                </div>
            <?php endif; ?>
        </div>
        <?php if ($total_paginas > 1): ?>
            <div class="card-footer bg-white">
                <nav>
                    <ul class="pagination pagination-sm justify-content-center mb-0">
                        <?php
                            $qsBase = $_GET;
                            $qsBase['pagina'] = max(1, $pagina_actual - 1);
                            $prevDisabled = $pagina_actual <= 1 ? 'disabled' : '';
                        ?>
                        <li class="page-item <?= $prevDisabled ?>">
                            <a class="page-link" href="?<?= http_build_query($qsBase) ?>"><i class="fas fa-chevron-left"></i></a>
                        </li>
                        <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                            <?php $qs = $_GET; $qs['pagina'] = $i; ?>
                            <li class="page-item <?= $i === $pagina_actual ? 'active' : '' ?>">
                                <a class="page-link" href="?<?= http_build_query($qs) ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                        <?php
                            $qsBase['pagina'] = min($total_paginas, $pagina_actual + 1);
                            $nextDisabled = $pagina_actual >= $total_paginas ? 'disabled' : '';
                        ?>
                        <li class="page-item <?= $nextDisabled ?>">
                            <a class="page-link" href="?<?= http_build_query($qsBase) ?>"><i class="fas fa-chevron-right"></i></a>
                        </li>
                    </ul>
                </nav>
            </div>
        <?php endif; ?>
    </div>
</div>
<!-- Modal: Detalle -->
<div class="modal fade" id="modalDetallePedido" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Pedido <span id="modalNumeroPedido"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modalDetallePedidoBody">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Pago -->
<div class="modal fade" id="modalPagoPedido" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formPagoPedido">
                <div class="modal-header">
                    <h5 class="modal-title">Pago de pedido <span id="pagoNumeroPedido"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id_pedido" id="pagoIdPedido">
                    <div class="mb-3">
                        <label class="form-label">Monto</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" step="0.01" min="0" class="form-control" name="monto_total" id="pagoMonto" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Metodo de pago</label>
                        <select class="form-select" name="metodo_pago" id="pagoMetodo">
                            <option value="efectivo">Efectivo</option>
                            <option value="tarjeta">Tarjeta</option>
                            <option value="transferencia">Transferencia</option>
                            <option value="otro">Otro</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Estado del pago</label>
                        <select class="form-select" name="estado_pago" id="pagoEstado">
                            <option value="Pendiente">Pendiente</option>
                            <option value="Completado">Completado</option>
                            <option value="Cancelado">Cancelado</option>
                            <option value="Rechazado">Rechazado</option>
                        </select>
                    </div>
                    <div id="alertaPagoPedido"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Guardar pago</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Modal: Nuevo pedido -->
<div class="modal fade" id="nuevoPedidoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <form id="formNuevoPedido">
                <input type="hidden" name="modo" id="pedidoModo" value="crear">
                <input type="hidden" name="id_pedido" id="pedidoIdHidden">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-plus me-2"></i>Crear nuevo pedido</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Cliente existente</label>
                            <select class="form-select" name="cli_id" id="nuevoCliente">
                                <option value="">Seleccionar cliente existente</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nombre de cliente (si no existe)</label>
                            <input type="text" class="form-control" name="nombre_cliente" placeholder="Nombre del cliente">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Telefono</label>
                            <input type="text" class="form-control" name="telefono_cliente" placeholder="Telefono del cliente">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email_cliente" placeholder="Correo del cliente">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Direccion de entrega</label>
                            <input type="text" class="form-control" name="direccion_entrega" placeholder="Direccion completa de entrega">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Fecha de entrega solicitada</label>
                            <input type="date" class="form-control" name="fecha_entrega_solicitada">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Empleado asignado</label>
                            <select class="form-select" name="empleado_id" id="nuevoEmpleado"></select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Estado del pedido</label>
                            <select class="form-select" name="estado">
                                <option value="Pendiente">Pendiente</option>
                                <option value="En proceso">En proceso</option>
                                <option value="Completado">Completado</option>
                                <option value="Cancelado">Cancelado</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Estado del pago</label>
                            <select class="form-select" name="estado_pago">
                                <option value="Pendiente">Pendiente</option>
                                <option value="Completado">Completado</option>
                                <option value="Cancelado">Cancelado</option>
                                <option value="Rechazado">Rechazado</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Metodo de pago</label>
                            <select class="form-select" name="metodo_pago">
                                <option value="efectivo">Efectivo</option>
                                <option value="tarjeta">Tarjeta</option>
                                <option value="transferencia">Transferencia</option>
                                <option value="otro">Otro</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Monto total</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" min="0" class="form-control" name="monto_total" id="nuevoMonto" required>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Notas</label>
                            <textarea class="form-control" name="notas" rows="2"></textarea>
                        </div>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0">Productos del pedido</h6>
                        <button type="button" class="btn btn-outline-primary btn-sm" id="btnAgregarProducto"><i class="fas fa-plus me-1"></i>Agregar producto</button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm" id="tablaProductosNuevo">
                            <thead class="table-light">
                                <tr>
                                    <th style="min-width:280px;">Producto</th>
                                    <th style="width:120px;">Precio unit.</th>
                                    <th style="width:120px;">Cantidad</th>
                                    <th style="width:120px;">Subtotal</th>
                                    <th style="width:80px;"></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                    <div id="alertaNuevoPedido" class="mt-2"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Crear pedido</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/gestion_pedidos.js"></script>
<script>
function cambiarEstado(idPedido, nuevoEstado) {
    if (!idPedido || !nuevoEstado) return;
    const mensajes = {
        'En proceso': 'Procesar pedido',
        'Completado': 'Marcar como completado',
        'Cancelado': 'Cancelar pedido'
    };
    if (!confirm(`Confirmas ${mensajes[nuevoEstado] || 'esta accion'} para el pedido #${idPedido}?`)) return;

    const botones = document.querySelectorAll(`button[onclick*="cambiarEstado(${idPedido}"]`);
    const originales = [];
    botones.forEach((btn) => {
        originales.push(btn.innerHTML);
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    });

    fetch('controllers/Cpedido.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: new URLSearchParams({
            action: 'cambiar_estado',
            id_pedido: idPedido,
            nuevo_estado: nuevoEstado
        })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        } else {
            alert(data.mensaje || 'No se pudo actualizar el estado');
            botones.forEach((btn, idx) => { btn.disabled = false; btn.innerHTML = originales[idx]; });
        }
    })
    .catch(err => {
        alert('Error: ' + err);
        botones.forEach((btn, idx) => { btn.disabled = false; btn.innerHTML = originales[idx]; });
    });
}

function verDetallePedido(idPedido) {
    const modal = new bootstrap.Modal(document.getElementById('modalDetallePedido'));
    const modalBody = document.getElementById('modalDetallePedidoBody');
    const modalNumero = document.getElementById('modalNumeroPedido');
    modalBody.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary"></div></div>';
    modalNumero.textContent = '';

    const fila = document.querySelector(`tr[data-id="${idPedido}"]`);
    if (!fila) {
        modalBody.innerHTML = '<div class="alert alert-danger">No se encontro la fila del pedido.</div>';
        modal.show();
        return;
    }

    const numero = fila.dataset.numero || idPedido;
    const cliente = fila.dataset.cliente || 'Cliente';
    const email = fila.dataset.email || '';
    const fechaCreacion = fila.dataset.fechaCreacion || '';
    const fechaEntrega = fila.dataset.fechaEntrega || 'Sin fecha';
    const totalProd = fila.dataset.totalProductos || '0';
    const totalMonto = fila.dataset.total || '0';
    const estado = fila.dataset.estado || '';
    const estadoPago = fila.dataset.estadoPago || 'Sin pago';

    modalNumero.textContent = '#' + numero;
    modal.show();

    fetch('controllers/Cpedido.php?action=detalle&id=' + encodeURIComponent(idPedido))
        .then(r => r.json())
        .then(data => {
            let productosHTML = '<p class="text-muted">No hay productos registrados.</p>';
            if (data.productos && data.productos.length > 0) {
                productosHTML = '<div class="table-responsive"><table class="table table-sm"><thead><tr><th>Producto</th><th>Cantidad</th><th>Precio</th><th>Subtotal</th></tr></thead><tbody>';
                data.productos.forEach(prod => {
                    productosHTML += `<tr>
                        <td>${prod.nombre}</td>
                        <td>${prod.cantidad}</td>
                        <td>$${parseFloat(prod.precio_unitario).toFixed(2)}</td>
                        <td>$${parseFloat(prod.subtotal).toFixed(2)}</td>
                    </tr>`;
                });
                productosHTML += '</tbody></table></div>';
            }

            const notas = data.pedido?.notas || '';
            const notasHTML = notas ? `<div class="alert alert-info mt-3"><strong><i class="fas fa-sticky-note me-2"></i>Notas:</strong><br>${notas.replace(/\n/g, '<br>')}</div>` : '';
            
            modalBody.innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-primary mb-3"><i class="fas fa-user me-2"></i>Cliente</h6>
                        <p class="mb-1"><strong>Nombre:</strong> ${cliente}</p>
                        <p class="mb-1"><strong>Email:</strong> ${email || 'N/D'}</p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-primary mb-3"><i class="fas fa-info-circle me-2"></i>Pedido</h6>
                        <p class="mb-1"><strong>Numero:</strong> ${numero}</p>
                        <p class="mb-1"><strong>Fecha creacion:</strong> ${fechaCreacion ? fechaCreacion : 'N/D'}</p>
                        <p class="mb-1"><strong>Fecha entrega:</strong> ${fechaEntrega}</p>
                        <p class="mb-1"><strong>Estado:</strong> ${estado}</p>
                        <p class="mb-1"><strong>Estado pago:</strong> ${estadoPago}</p>
                    </div>
                </div>
                <hr>
                <h6 class="text-primary mb-3"><i class="fas fa-box me-2"></i>Productos</h6>
                ${productosHTML}
                ${notasHTML}
                <div class="text-end mt-3">
                    <h5>Total: $${parseFloat(totalMonto).toFixed(2)}</h5>
                    <p class="text-muted mb-0">Productos: ${totalProd}</p>
                </div>
            `;
        })
        .catch(() => {
            modalBody.innerHTML = '<div class="alert alert-danger">No se pudo cargar el detalle del pedido.</div>';
        });
}

function guardarNuevoPedido(event) {
    event.preventDefault();
    const form = event.target;
    const alerta = document.getElementById('alertaNuevoPedido');
    const btn = form.querySelector('button[type="submit"]');
    const original = btn.innerHTML;
    alerta.innerHTML = '';

    const modo = document.getElementById('pedidoModo')?.value || 'crear';
    const accion = modo === 'editar' ? 'editar_pedido' : 'crear_pedido';
    const textoCarga = modo === 'editar' ? '<i class="fas fa-spinner fa-spin me-1"></i>Guardando...' : '<i class="fas fa-spinner fa-spin me-1"></i>Creando...';

    const fd = new FormData(form);
    fd.append('action', accion);

    btn.disabled = true;
    btn.innerHTML = textoCarga;

    fetch('controllers/Cpedido.php', {
        method: 'POST',
        body: fd
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            const mensaje = modo === 'editar'
                ? 'Pedido actualizado correctamente'
                : 'Pedido creado correctamente';
            alerta.innerHTML = `<div class="alert alert-success">${mensaje}</div>`;
            setTimeout(() => window.location.reload(), 1000);
        } else {
            const mensajeError = data.mensaje || (modo === 'editar'
                ? 'No se pudo actualizar el pedido'
                : 'No se pudo crear el pedido');
            alerta.innerHTML = `<div class="alert alert-danger">${mensajeError}</div>`;
            btn.disabled = false;
            btn.innerHTML = original;
        }
    })
    .catch(err => {
        alerta.innerHTML = '<div class="alert alert-danger">Error: ' + err + '</div>';
        btn.disabled = false;
        btn.innerHTML = original;
    });
}

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('formPagoPedido')?.addEventListener('submit', guardarPago);
    document.getElementById('formNuevoPedido')?.addEventListener('submit', guardarNuevoPedido);

    document.getElementById('btnAgregarProducto')?.addEventListener('click', agregarFilaProducto);
    if (document.querySelector('#tablaProductosNuevo tbody') && !document.querySelector('#tablaProductosNuevo tbody tr')) {
        agregarFilaProducto();
    }
    cargarEmpleados('nuevoEmpleado');
    cargarClientes('nuevoCliente');
    document.getElementById('nuevoCliente')?.addEventListener('change', function () {
        aplicarClienteSeleccionadoDesdeSelect(this);
    });

    const modalNuevo = document.getElementById('nuevoPedidoModal');
    modalNuevo?.addEventListener('show.bs.modal', () => {
        if (document.getElementById('pedidoModo')?.value !== 'editar') {
            cargarClientes('nuevoCliente');
        }
    });
    modalNuevo?.addEventListener('hidden.bs.modal', () => {
        const form = document.getElementById('formNuevoPedido');
        form?.reset();
        document.getElementById('pedidoModo').value = 'crear';
        document.getElementById('pedidoIdHidden').value = '';
        const titulo = modalNuevo.querySelector('.modal-title');
        if (titulo) {
            titulo.innerHTML = '<i class="fas fa-plus me-2"></i>Crear nuevo pedido';
        }
        const alerta = document.getElementById('alertaNuevoPedido');
        if (alerta) {
            alerta.innerHTML = '';
        }
        const tbody = document.querySelector('#tablaProductosNuevo tbody');
        if (tbody) {
            tbody.innerHTML = '';
        }
        agregarFilaProducto();
        const montoInput = document.getElementById('nuevoMonto');
        if (montoInput) {
            montoInput.readOnly = false;
        }
    });
});
</script>
