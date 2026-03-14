<?php
$navbar_volver_url = 'index.php?ctrl=empleado&action=dashboard';
$navbar_volver_text = 'Volver al Dashboard';
$mensaje = isset($_SESSION['mensaje']) ? $_SESSION['mensaje'] : '';
$tipo_mensaje = isset($_SESSION['tipo_mensaje']) ? $_SESSION['tipo_mensaje'] : '';
// Limpiar los mensajes después de mostrarlos
unset($_SESSION['mensaje']);
unset($_SESSION['tipo_mensaje']);

// Filtros para mantener en el formulario
$estadoPedido = $_GET['estado_pedido'] ?? '';
$estadoPago = $_GET['estado_pago'] ?? '';
$fechaDesde = $_GET['fecha_desde'] ?? '';
$fechaHasta = $_GET['fecha_hasta'] ?? '';

// Los pedidos ya vienen filtrados y paginados desde el controlador
// Debug temporal
if (!isset($pedidosPaginados)) {
    error_log("ERROR: Variable \$pedidosPaginados no existe en la vista");
    $pedidosPaginados = [];
} else {
    error_log("Vista recibió " . count($pedidosPaginados) . " pedidos paginados");
    if (count($pedidosPaginados) > 0) {
        error_log("Primer pedido en vista: " . print_r($pedidosPaginados[0], true));
    }
}

// Las variables de paginación también vienen del controlador:
// $paginaActual, $totalPaginas, $totalPedidos, $pedidosPorPagina

function badgeEstadoPedido($estado) {
    $e = $estado ?? '';
    if ($e === 'Pendiente') return 'warning text-dark';
    if ($e === 'En proceso' || $e === 'En Proceso') return 'info';
    if ($e === 'Completado') return 'success';
    if ($e === 'Cancelado') return 'danger';
    return 'secondary';
}
function badgeEstadoPago($estado) {
    $e = strtolower($estado ?? '');
    if ($e === 'completado' || $e === 'aprobado') return 'success';
    if ($e === 'pendiente') return 'warning text-dark';
    if ($e === 'cancelado' || $e === 'rechazado') return 'danger';
    return 'secondary';
}
?>
<!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Gestión de Pedidos - FloralTech</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="assets/css/dashboard-empleado.css">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
        <style>
            .filter-card { background: var(--emp-bg-card); border: 1px solid var(--emp-border); border-radius: var(--emp-radius); box-shadow: var(--emp-shadow); padding: 1rem; margin-bottom: 1rem; }
            .filter-title { color: var(--emp-primary); font-size: 1.05rem; font-weight: 600; margin-bottom: 0.75rem; border-bottom: 2px solid var(--emp-primary); padding-bottom: 0.5rem; }
            .pedido-list-header { background: var(--emp-header-bg); color: white; padding: 0.75rem 1rem; border-radius: var(--emp-radius) var(--emp-radius) 0 0; display: flex; align-items: center; justify-content: space-between; font-weight: 600; }
            .pedido-list-header .badge { background: rgba(255,255,255,0.2); color: white; }
            .empty-state-pedidos { text-align: center; padding: 2rem 1rem; color: var(--emp-text-muted); }
            .empty-state-pedidos i { font-size: 2.5rem; margin-bottom: 0.75rem; color: var(--emp-text-light); }
            .empty-state-pedidos h4 { font-size: 1.1rem; margin-bottom: 0.5rem; color: var(--emp-text); font-weight: 600; }
        </style>
    </head>
    <body class="empleado-theme">
        <div class="dashboard-container">
            <?php include __DIR__ . '/partials/navbar_empleado.php'; ?>
            <div class="main-content">
                <div class="content-wrapper">
                    <!-- Filtros compactos -->
                    <div class="filter-card">
                        <div class="filter-title">
                            <i class="fas fa-filter me-2"></i>Filtros de Búsqueda
                        </div>
                        <form method="GET" action="" class="row g-2 align-items-end">
                            <input type="hidden" name="ctrl" value="empleado">
                            <input type="hidden" name="action" value="gestion_pedidos">
                            <input type="hidden" name="pagina" value="1">
                            <div class="col-md-3">
                                <label class="form-label">Estado del Pedido</label>
                                <select name="estado_pedido" class="form-select">
                                    <option value="">Todos los estados</option>
                                    <option value="Pendiente" <?= $estadoPedido=='Pendiente'?'selected':'' ?>>Pendiente</option>
                                    <option value="En proceso" <?= ($estadoPedido==='En proceso' || $estadoPedido==='En Proceso')?'selected':'' ?>>En proceso</option>
                                    <option value="En Preparación" <?= $estadoPedido=='En Preparación'?'selected':'' ?>>En Preparación</option>
                                    <option value="Completado" <?= $estadoPedido=='Completado'?'selected':'' ?>>Completado</option>
                                    <option value="Cancelado" <?= $estadoPedido=='Cancelado'?'selected':'' ?>>Cancelado</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Estado del Pago</label>
                                <select name="estado_pago" class="form-select">
                                    <option value="">Todos los pagos</option>
                                    <option value="Sin pago" <?= $estadoPago=='Sin pago'?'selected':'' ?>>Sin pago</option>
                                    <option value="Pendiente" <?= $estadoPago=='Pendiente'?'selected':'' ?>>Pendiente</option>
                                    <option value="Procesando" <?= $estadoPago=='Procesando'?'selected':'' ?>>Procesando</option>
                                    <option value="Completado" <?= $estadoPago=='Completado'?'selected':'' ?>>Completado</option>
                                    <option value="Rechazado" <?= $estadoPago=='Rechazado'?'selected':'' ?>>Rechazado</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Desde</label>
                                <input type="date" name="fecha_desde" class="form-control" value="<?= htmlspecialchars($fechaDesde) ?>">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Hasta</label>
                                <input type="date" name="fecha_hasta" class="form-control" value="<?= htmlspecialchars($fechaHasta) ?>">
                            </div>
                            <div class="col-md-2 d-flex gap-1">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-search"></i> Filtrar
                                </button>
                                <a href="?ctrl=empleado&action=gestion_pedidos" class="btn btn-outline-secondary btn-sm">
                                    <i class="fas fa-times"></i>
                                </a>
                            </div>
                        </form>
                    </div>

                    <!-- Lista de pedidos -->
                    <div class="content-card">
                        <div class="pedido-list-header d-flex justify-content-between align-items-center">
                            <div>
                                <span><i class="fas fa-list me-2"></i>Lista de Pedidos</span>
                                <span class="badge">
                                    <?= $totalPedidos ?> total 
                                    <?php if ($totalPedidos > 0): ?>
                                        (Página <?= $paginaActual ?> de <?= $totalPaginas ?>)
                                    <?php endif; ?>
                                </span>
                            </div>
                            <a href="index.php?ctrl=empleado&action=nuevoPedidoForm" class="btn btn-success btn-sm">
                                <i class="fas fa-plus me-2"></i>Nuevo Pedido
                            </a>
                        </div>
                        <div class="card-body">
                            <?php if ($mensaje): ?>
                                <div class="alert alert-<?= $tipo_mensaje ?> alert-dismissible fade show" role="alert">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <?= htmlspecialchars($mensaje) ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            <?php endif; ?>

                            <!-- 📊 Alertas de Inventario por Descuentos Automáticos -->
                            <?php if (isset($_SESSION['alertas_inventario']) && !empty($_SESSION['alertas_inventario'])): ?>
                                <div style="margin-bottom: 20px;">
                                    <?php foreach ($_SESSION['alertas_inventario'] as $alerta): ?>
                                        <?php 
                                            $clase_bootstrap = 'alert-warning';
                                            $icono_fas = 'fa-exclamation-triangle';
                                            
                                            switch ($alerta['tipo'] ?? 'advertencia') {
                                                case 'crítica':
                                                    $clase_bootstrap = 'alert-danger';
                                                    $icono_fas = 'fa-circle-exclamation';
                                                    break;
                                                case 'baja':
                                                    $clase_bootstrap = 'alert-warning';
                                                    $icono_fas = 'fa-bolt';
                                                    break;
                                                case 'error':
                                                    $clase_bootstrap = 'alert-danger';
                                                    $icono_fas = 'fa-times-circle';
                                                    break;
                                                case 'advertencia':
                                                default:
                                                    $clase_bootstrap = 'alert-warning';
                                                    $icono_fas = 'fa-exclamation-triangle';
                                                    break;
                                            }
                                        ?>
                                        <div class="alert <?= $clase_bootstrap ?> alert-dismissible fade show" role="alert">
                                            <i class="fas <?= $icono_fas ?> me-2"></i>
                                            <strong><?= htmlspecialchars($alerta['flor'] ?? 'Alerta') ?>:</strong>
                                            <?= htmlspecialchars($alerta['mensaje']) ?>
                                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <?php unset($_SESSION['alertas_inventario']); ?>
                            <?php endif; ?>
                            
                            <?php if (empty($pedidosPaginados)): ?>
                                <div class="empty-state-pedidos">
                                    <i class="fas fa-clipboard-list"></i>
                                    <h4>No hay pedidos</h4>
                                    <p>No se encontraron pedidos con los filtros aplicados</p>
                                    <!-- Debug temporal -->
                                    <small class="text-muted">
                                        Debug: Total pedidos = <?= $totalPedidos ?>, 
                                        Página actual = <?= $paginaActual ?>, 
                                        Pedidos paginados = <?= count($pedidosPaginados) ?>
                                        <?php if (isset($pedidos)): ?>
                                            <br>Pedidos originales = <?= count($pedidos) ?>
                                        <?php else: ?>
                                            <br>Variable $pedidos no definida
                                        <?php endif; ?>
                                    </small>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0" id="tablaPedidosEmpleado">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Pedido</th>
                                                <th>Cliente</th>
                                                <th>Fecha</th>
                                                <th>Entrega</th>
                                                <th>Total</th>
                                                <th>Estado pedido</th>
                                                <th>Estado pago</th>
                                                <th class="text-center">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($pedidosPaginados as $pedido):
                                                $estado = $pedido['estado'] ?? '';
                                                $estadoPago = $pedido['estado_pago'] ?? 'Sin pago';
                                                $fechaPed = !empty($pedido['fecha_pedido']) ? date('d/m/Y H:i', strtotime($pedido['fecha_pedido'])) : 'N/D';
                                                $fechaEnt = !empty($pedido['fecha_entrega_solicitada']) ? date('d/m/Y', strtotime($pedido['fecha_entrega_solicitada'])) : '';
                                            ?>
                                                <tr data-id="<?= (int)$pedido['idped'] ?>"
                                                    data-numero="<?= htmlspecialchars($pedido['numped'] ?? '') ?>"
                                                    data-cliente="<?= htmlspecialchars($pedido['cliente_nombre'] ?? '') ?>"
                                                    data-email="<?= htmlspecialchars($pedido['cliente_email'] ?? '') ?>"
                                                    data-fecha-creacion="<?= htmlspecialchars($fechaPed) ?>"
                                                    data-fecha-entrega="<?= htmlspecialchars($fechaEnt) ?>"
                                                    data-total="<?= number_format((float)($pedido['monto_total'] ?? 0), 2, '.', '') ?>"
                                                    data-estado="<?= htmlspecialchars($estado) ?>"
                                                    data-estado-pago="<?= htmlspecialchars($estadoPago) ?>"
                                                    data-total-productos="<?= (int)($pedido['total_productos'] ?? 0) ?>">
                                                    <td>
                                                        <strong class="text-primary"><?= htmlspecialchars($pedido['numped'] ?? 'PED-' . $pedido['idped']) ?></strong>
                                                        <br><small class="text-muted">ID: <?= (int)$pedido['idped'] ?></small>
                                                    </td>
                                                    <td>
                                                        <strong><?= htmlspecialchars($pedido['cliente_nombre'] ?? '') ?></strong>
                                                        <?php if (!empty($pedido['cliente_email'])): ?>
                                                        <br><small class="text-muted"><?= htmlspecialchars($pedido['cliente_email']) ?></small>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?= $fechaPed ?></td>
                                                    <td><?= $fechaEnt ?: '<small class="text-muted">Sin fecha</small>' ?></td>
                                                    <td><strong class="text-success">$<?= number_format((float)($pedido['monto_total'] ?? 0), 2) ?></strong></td>
                                                    <td><span class="badge bg-<?= badgeEstadoPedido($estado) ?>"><?= htmlspecialchars($estado) ?></span></td>
                                                    <td><span class="badge bg-<?= badgeEstadoPago($estadoPago) ?>"><?= htmlspecialchars($estadoPago) ?></span></td>
                                                    <td class="text-center">
                                                        <div class="btn-group btn-group-sm" role="group">
                                                            <button type="button" class="btn btn-outline-secondary" onclick="verDetallePedidoEmp(<?= (int)$pedido['idped'] ?>)" title="Ver detalles"><i class="fas fa-eye"></i></button>
                                                            <a href="index.php?ctrl=empleado&action=generar_factura&idpedido=<?= (int)$pedido['idped'] ?>" class="btn btn-outline-success" title="Descargar factura" target="_blank"><i class="fas fa-file-invoice-dollar"></i></a>
                                                            <?php if ($estado !== 'Completado' && $estado !== 'Cancelado'): ?>
                                                            <button type="button" class="btn btn-outline-warning text-dark" onclick="editarPedidoEmp(<?= (int)$pedido['idped'] ?>)" title="Editar pedido"><i class="fas fa-pen"></i></button>
                                                            <?php endif; ?>
                                                            <button type="button" class="btn btn-outline-info text-info" onclick="editarPagoEmp(<?= (int)$pedido['idped'] ?>)" title="Gestionar pago"><i class="fas fa-credit-card"></i></button>
                                                            <?php if ($estado === 'Pendiente'): ?>
                                                                <button type="button" class="btn btn-outline-primary" onclick="cambiarEstadoEmp(<?= (int)$pedido['idped'] ?>, 'En proceso')" title="En proceso"><i class="fas fa-cog"></i></button>
                                                                <button type="button" class="btn btn-outline-success" onclick="cambiarEstadoEmp(<?= (int)$pedido['idped'] ?>, 'Completado')" title="Completado"><i class="fas fa-check"></i></button>
                                                                <button type="button" class="btn btn-outline-danger" onclick="cambiarEstadoEmp(<?= (int)$pedido['idped'] ?>, 'Cancelado')" title="Cancelar"><i class="fas fa-ban"></i></button>
                                                            <?php elseif ($estado === 'En proceso' || $estado === 'En Proceso'): ?>
                                                                <button type="button" class="btn btn-outline-success" onclick="cambiarEstadoEmp(<?= (int)$pedido['idped'] ?>, 'Completado')" title="Completado"><i class="fas fa-check"></i></button>
                                                                <button type="button" class="btn btn-outline-danger" onclick="cambiarEstadoEmp(<?= (int)$pedido['idped'] ?>, 'Cancelado')" title="Cancelar"><i class="fas fa-ban"></i></button>
                                                            <?php else: ?>
                                                                <button type="button" class="btn btn-outline-secondary" onclick="cambiarEstadoEmp(<?= (int)$pedido['idped'] ?>, 'Pendiente')" title="Reabrir" <?= $estado === 'Pendiente' ? 'disabled' : '' ?>><i class="fas fa-undo"></i></button>
                                                                <button type="button" class="btn btn-outline-primary" onclick="cambiarEstadoEmp(<?= (int)$pedido['idped'] ?>, 'En proceso')" title="En proceso" <?= ($estado === 'En proceso' || $estado === 'En Proceso') ? 'disabled' : '' ?>><i class="fas fa-cog"></i></button>
                                                                <button type="button" class="btn btn-outline-success" onclick="cambiarEstadoEmp(<?= (int)$pedido['idped'] ?>, 'Completado')" title="Completado" <?= $estado === 'Completado' ? 'disabled' : '' ?>><i class="fas fa-check"></i></button>
                                                                <button type="button" class="btn btn-outline-danger" onclick="cambiarEstadoEmp(<?= (int)$pedido['idped'] ?>, 'Cancelado')" title="Cancelar" <?= $estado === 'Cancelado' ? 'disabled' : '' ?>><i class="fas fa-ban"></i></button>
                                                            <?php endif; ?>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                
                                <!-- Paginación -->
                                <?php if ($totalPaginas > 1): ?>
                                    <div class="d-flex justify-content-between align-items-center mt-3 px-3 pb-3">
                                        <div class="text-muted">
                                            Mostrando <?= count($pedidosPaginados) ?> de <?= $totalPedidos ?> pedidos
                                        </div>
                                        <nav aria-label="Paginación de pedidos">
                                            <ul class="pagination mb-0">
                                                <!-- Botón Anterior -->
                                                <?php if ($paginaActual > 1): ?>
                                                    <li class="page-item">
                                                        <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['pagina' => $paginaActual - 1])) ?>">
                                                            <i class="fas fa-chevron-left"></i>
                                                        </a>
                                                    </li>
                                                <?php else: ?>
                                                    <li class="page-item disabled">
                                                        <span class="page-link"><i class="fas fa-chevron-left"></i></span>
                                                    </li>
                                                <?php endif; ?>

                                                <!-- Números de página -->
                                                <?php
                                                // Calcular el rango de páginas a mostrar
                                                $inicio = max(1, $paginaActual - 2);
                                                $fin = min($totalPaginas, $paginaActual + 2);
                                                
                                                // Ajustar si estamos cerca del inicio o fin
                                                if ($fin - $inicio < 4) {
                                                    if ($inicio == 1) {
                                                        $fin = min($totalPaginas, $inicio + 4);
                                                    } else {
                                                        $inicio = max(1, $fin - 4);
                                                    }
                                                }

                                                // Mostrar primera página si no está en el rango
                                                if ($inicio > 1): ?>
                                                    <li class="page-item">
                                                        <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['pagina' => 1])) ?>">1</a>
                                                    </li>
                                                    <?php if ($inicio > 2): ?>
                                                        <li class="page-item disabled"><span class="page-link">...</span></li>
                                                    <?php endif; ?>
                                                <?php endif; ?>

                                                <!-- Páginas en el rango -->
                                                <?php for ($i = $inicio; $i <= $fin; $i++): ?>
                                                    <li class="page-item <?= $i == $paginaActual ? 'active' : '' ?>">
                                                        <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['pagina' => $i])) ?>"><?= $i ?></a>
                                                    </li>
                                                <?php endfor; ?>

                                                <!-- Mostrar última página si no está en el rango -->
                                                <?php if ($fin < $totalPaginas): ?>
                                                    <?php if ($fin < $totalPaginas - 1): ?>
                                                        <li class="page-item disabled"><span class="page-link">...</span></li>
                                                    <?php endif; ?>
                                                    <li class="page-item">
                                                        <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['pagina' => $totalPaginas])) ?>"><?= $totalPaginas ?></a>
                                                    </li>
                                                <?php endif; ?>

                                                <!-- Botón Siguiente -->
                                                <?php if ($paginaActual < $totalPaginas): ?>
                                                    <li class="page-item">
                                                        <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['pagina' => $paginaActual + 1])) ?>">
                                                            <i class="fas fa-chevron-right"></i>
                                                        </a>
                                                    </li>
                                                <?php else: ?>
                                                    <li class="page-item disabled">
                                                        <span class="page-link"><i class="fas fa-chevron-right"></i></span>
                                                    </li>
                                                <?php endif; ?>
                                            </ul>
                                        </nav>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Detalle -->
        <div class="modal fade" id="modalDetallePedidoEmp" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Pedido <span id="modalNumeroPedidoEmp"></span></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body" id="modalDetallePedidoBodyEmp"></div>
                </div>
            </div>
        </div>
        <!-- Modal Pago -->
        <div class="modal fade" id="modalPagoPedidoEmp" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form id="formPagoPedidoEmp">
                        <div class="modal-header">
                            <h5 class="modal-title">Pago del pedido <span id="pagoNumeroPedidoEmp"></span></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="id_pedido" id="pagoIdPedidoEmp">
                            <div class="mb-3">
                                <label class="form-label">Monto</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="0.01" min="0" class="form-control" name="monto_total" id="pagoMontoEmp" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Método de pago</label>
                                <select class="form-select" name="metodo_pago" id="pagoMetodoEmp">
                                    <option value="efectivo">Efectivo</option>
                                    <option value="tarjeta">Tarjeta</option>
                                    <option value="transferencia">Transferencia</option>
                                    <option value="otro">Otro</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Estado del pago</label>
                                <select class="form-select" name="estado_pago" id="pagoEstadoEmp">
                                    <option value="Pendiente">Pendiente</option>
                                    <option value="Completado">Completado</option>
                                    <option value="Cancelado">Cancelado</option>
                                    <option value="Rechazado">Rechazado</option>
                                </select>
                            </div>
                            <div id="alertaPagoPedidoEmp"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-primary">Guardar pago</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Modal Editar pedido (datos básicos) -->
        <div class="modal fade" id="modalEditarPedidoEmp" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form id="formEditarPedidoEmp">
                        <div class="modal-header">
                            <h5 class="modal-title">Editar pedido <span id="editarNumeroPedidoEmp"></span></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="id_pedido" id="editarIdPedidoEmp">
                            <div class="mb-3">
                                <label class="form-label">Dirección de entrega</label>
                                <input type="text" class="form-control" name="direccion_entrega" id="editarDireccionEmp">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Fecha entrega solicitada</label>
                                <input type="date" class="form-control" name="fecha_entrega_solicitada" id="editarFechaEmp">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Estado del pedido</label>
                                <select class="form-select" name="estado" id="editarEstadoEmp">
                                    <option value="Pendiente">Pendiente</option>
                                    <option value="En proceso">En proceso</option>
                                    <option value="Completado">Completado</option>
                                    <option value="Cancelado">Cancelado</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Notas</label>
                                <textarea class="form-control" name="notas" id="editarNotasEmp" rows="2"></textarea>
                            </div>
                            <div id="alertaEditarPedidoEmp"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-primary">Guardar cambios</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            var baseUrlPedido = 'controllers/Cpedido.php';
            function cambiarEstadoEmp(idPedido, nuevoEstado) {
                if (!idPedido || !nuevoEstado) return;
                var msg = { 'En proceso': 'Poner en proceso', 'Completado': 'Marcar como completado', 'Cancelado': 'Cancelar pedido', 'Pendiente': 'Reabrir pedido' };
                if (!confirm('¿Confirmas ' + (msg[nuevoEstado] || nuevoEstado) + ' para el pedido #' + idPedido + '?')) return;
                var btns = document.querySelectorAll('button[onclick*="cambiarEstadoEmp(' + idPedido + '"]');
                var orig = []; btns.forEach(function(b){ orig.push(b.innerHTML); b.disabled = true; b.innerHTML = '<i class="fas fa-spinner fa-spin"></i>'; });
                fetch(baseUrlPedido, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
                    body: new URLSearchParams({ action: 'cambiar_estado', id_pedido: idPedido, nuevo_estado: nuevoEstado })
                }).then(function(r){ return r.json(); }).then(function(data){
                    if (data.success) window.location.reload();
                    else { alert(data.mensaje || 'Error'); btns.forEach(function(b,i){ b.disabled = false; b.innerHTML = orig[i]; }); }
                }).catch(function(err){ alert('Error: ' + err); btns.forEach(function(b,i){ b.disabled = false; b.innerHTML = orig[i]; }); });
            }
            function verDetallePedidoEmp(idPedido) {
                var modal = new bootstrap.Modal(document.getElementById('modalDetallePedidoEmp'));
                var body = document.getElementById('modalDetallePedidoBodyEmp');
                var num = document.getElementById('modalNumeroPedidoEmp');
                body.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary"></div></div>';
                var row = document.querySelector('tr[data-id="' + idPedido + '"]');
                if (!row) { body.innerHTML = '<div class="alert alert-danger">Pedido no encontrado.</div>'; modal.show(); return; }
                num.textContent = '#' + (row.dataset.numero || idPedido);
                modal.show();
                fetch(baseUrlPedido + '?action=detalle&id=' + encodeURIComponent(idPedido)).then(function(r){ return r.json(); }).then(function(data){
                    var prod = '<p class="text-muted">No hay productos.</p>';
                    if (data.productos && data.productos.length) {
                        prod = '<table class="table table-sm"><thead><tr><th>Producto</th><th>Cant.</th><th>Precio</th><th>Subtotal</th></tr></thead><tbody>';
                        data.productos.forEach(function(p){ prod += '<tr><td>' + (p.nombre||'') + '</td><td>' + p.cantidad + '</td><td>$' + parseFloat(p.precio_unitario).toFixed(2) + '</td><td>$' + parseFloat(p.subtotal).toFixed(2) + '</td></tr>'; });
                        prod += '</tbody></table>';
                    }
                    body.innerHTML = '<div class="row"><div class="col-md-6"><strong>Cliente:</strong> ' + (row.dataset.cliente||'') + '<br><strong>Email:</strong> ' + (row.dataset.email||'') + '</div><div class="col-md-6"><strong>Fecha:</strong> ' + (row.dataset.fechaCreacion||'') + '<br><strong>Entrega:</strong> ' + (row.dataset.fechaEntrega||'') + '<br><strong>Estado:</strong> ' + (row.dataset.estado||'') + '<br><strong>Pago:</strong> ' + (row.dataset.estadoPago||'') + '</div></div><hr><h6>Productos</h6>' + prod + '<div class="text-end mt-3"><strong>Total: $' + parseFloat(row.dataset.total||0).toFixed(2) + '</strong></div>';
                }).catch(function(){ body.innerHTML = '<div class="alert alert-danger">Error al cargar.</div>'; });
            }
            function editarPagoEmp(idPedido) {
                var row = document.querySelector('tr[data-id="' + idPedido + '"]');
                document.getElementById('pagoIdPedidoEmp').value = idPedido;
                document.getElementById('pagoNumeroPedidoEmp').textContent = '#' + (row ? row.dataset.numero : idPedido);
                document.getElementById('pagoMontoEmp').value = row ? row.dataset.total : '';
                document.getElementById('alertaPagoPedidoEmp').innerHTML = '';
                new bootstrap.Modal(document.getElementById('modalPagoPedidoEmp')).show();
            }
            document.getElementById('formPagoPedidoEmp').addEventListener('submit', function(e){
                e.preventDefault();
                var f = e.target;
                var alerta = document.getElementById('alertaPagoPedidoEmp');
                var fd = new FormData(f);
                fd.append('action', 'editar_pago');
                fetch(baseUrlPedido, { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' }, body: fd }).then(function(r){ return r.json(); }).then(function(data){
                    if (data.success) { bootstrap.Modal.getInstance(document.getElementById('modalPagoPedidoEmp')).hide(); window.location.reload(); }
                    else alerta.innerHTML = '<div class="alert alert-danger">' + (data.mensaje||'Error') + '</div>';
                }).catch(function(err){ alerta.innerHTML = '<div class="alert alert-danger">Error: ' + err + '</div>'; });
            });
            function editarPedidoEmp(idPedido) {
                document.getElementById('editarIdPedidoEmp').value = idPedido;
                var row = document.querySelector('tr[data-id="' + idPedido + '"]');
                document.getElementById('editarNumeroPedidoEmp').textContent = '#' + (row ? row.dataset.numero : idPedido);
                document.getElementById('alertaEditarPedidoEmp').innerHTML = '';
                fetch(baseUrlPedido + '?action=detalle&id=' + idPedido).then(function(r){ return r.json(); }).then(function(data){
                    if (data.success && data.pedido) {
                        var p = data.pedido;
                        document.getElementById('editarDireccionEmp').value = p.direccion_entrega || '';
                        document.getElementById('editarFechaEmp').value = (p.fecha_entrega_solicitada || '').split(' ')[0];
                        document.getElementById('editarEstadoEmp').value = (p.estado || 'Pendiente').replace('En Proceso','En proceso');
                        document.getElementById('editarNotasEmp').value = p.notas || '';
                    }
                });
                new bootstrap.Modal(document.getElementById('modalEditarPedidoEmp')).show();
            }
            document.getElementById('formEditarPedidoEmp').addEventListener('submit', function(e){
                e.preventDefault();
                var f = e.target;
                var alerta = document.getElementById('alertaEditarPedidoEmp');
                var fd = new FormData(f);
                fd.append('action', 'editar_pedido');
                fd.append('empleado_id', '<?= (int)($_SESSION["user"]["idusu"] ?? 0) ?>');
                fetch(baseUrlPedido, { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' }, body: fd }).then(function(r){ return r.json(); }).then(function(data){
                    if (data.success) { bootstrap.Modal.getInstance(document.getElementById('modalEditarPedidoEmp')).hide(); window.location.reload(); }
                    else alerta.innerHTML = '<div class="alert alert-danger">' + (data.mensaje||'Error') + '</div>';
                }).catch(function(err){ alerta.innerHTML = '<div class="alert alert-danger">Error: ' + err + '</div>'; });
            });
            setTimeout(function() {
                document.querySelectorAll('.alert').forEach(function(a){ try { new bootstrap.Alert(a).close(); } catch(_){} });
            }, 5000);
        </script>
    </body>
    </html>
