
<?php
require_once __DIR__ . '/../../controllers/ReportesController.php';

// Obtener contexto si no se inyecto desde el controlador
if (!isset($datos)) {
    $reportesCtrl = new ReportesController();
    $ctx = $reportesCtrl->obtenerContexto();
    extract($ctx);
}

$pagosFiltrados = $modalPagos ?? ($dtAllPagos ?? []);
$pedidosFiltrados = $modalPedidos ?? ($dtAll ?? []);
$usuariosFiltrados = $modalUsuarios ?? ($dtAllUsu ?? []);
$inventarioFiltrado = $modalInventario ?? ($dtAllInv ?? []);
$totalUsuarios = $totalUsuarios ?? 0;
$tiposUsuarios = $tiposUsuarios ?? [];

function badgeClaseEstado($estado)
{
    $estadoNormalizado = strtolower(trim((string) $estado));
    if (in_array($estadoNormalizado, ['completado', 'completada'])) {
        return 'success';
    }
    if ($estadoNormalizado === 'pendiente') {
        return 'warning text-dark';
    }
    if (in_array($estadoNormalizado, ['cancelado', 'cancelada', 'anulado'])) {
        return 'danger';
    }
    return 'secondary';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes - FloralTech</title>
    <link rel="stylesheet" href="/assets/dashboard-admin.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        .filter-box {background:#f8fafc; border:1px solid #e5e7eb;}
        .filters-wrapper {display:flex; flex-wrap:wrap; gap:12px; align-items:flex-end;}
        .filters-form {flex:1 1 260px; display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:12px; align-items:end;}
        .filters-actions {min-width:170px;}
        .filters-export {min-width:190px; align-self:flex-end;}
        @media (max-width: 767px) {
            .filters-form {grid-template-columns:1fr;}
            .filters-actions,
            .filters-export {width:100%;}
        }
    </style>
</head>
<body style="background:#f7f7fb;">
    <div class="container-fluid px-4 py-4">
        <div class="p-4 mb-4 rounded-4 shadow-sm" style="background: linear-gradient(120deg, #0d6efd 0%, #5b21b6 60%, #1e1b4b 100%); color:#fff;">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <p class="mb-1 text-white-50 small">Visibilidad rapida de ventas, inventario, cuentas y pagos</p>
                    <h1 class="fw-bold mb-0">Panel de reportes</h1>
                </div>
                <div class="d-flex gap-3 flex-wrap">
                    <div class="px-3 py-2 rounded-3 bg-white bg-opacity-10 border border-white border-opacity-25">
                        <div class="small text-white-50">Pedidos</div>
                        <div class="h5 mb-0"><?= number_format($datos['ventas']['pedidos'] ?? 0) ?></div>
                    </div>
                    <div class="px-3 py-2 rounded-3 bg-white bg-opacity-10 border border-white border-opacity-25">
                        <div class="small text-white-50">Usuarios</div>
                        <div class="h5 mb-0"><?= number_format($totalUsuarios) ?></div>
                    </div>
                    <div class="px-3 py-2 rounded-3 bg-white bg-opacity-10 border border-white border-opacity-25">
                        <div class="small text-white-50">Pagos completos</div>
                        <div class="h5 mb-0">$<?= number_format($datos['pagos']['realizados'] ?? 0, 2) ?></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-12 col-md-3">
                <div class="card shadow-sm border-0 rounded-4 h-100" data-bs-toggle="modal" data-bs-target="#tablaModal" role="button" style="background: linear-gradient(135deg,#e0e7ff,#eef2ff);">
                    <div class="card-body text-center">
                        <i class="bi bi-bar-chart-line display-6 text-primary"></i>
                        <h6 class="fw-bold mt-2 mb-1">Ventas</h6>
                        <div class="small text-muted">Total</div>
                        <div class="h5 fw-bold text-primary">$<?= number_format($datos['ventas']['total'] ?? 0, 2) ?></div>
                        <div class="small text-muted">Pedidos: <?= $datos['ventas']['pedidos'] ?? 0 ?></div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-3">
                <div class="card shadow-sm border-0 rounded-4 h-100" data-bs-toggle="modal" data-bs-target="#tablaModalFlores" role="button" style="background: linear-gradient(135deg,#dcfce7,#ecfdf3);">
                    <div class="card-body text-center">
                        <i class="bi bi-box-seam display-6 text-success"></i>
                        <h6 class="fw-bold mt-2 mb-1">Inventario</h6>
                        <div class="small text-muted">Stock total</div>
                        <div class="h5 fw-bold text-success"><?= number_format($datos['inventario']['stock_total'] ?? 0) ?></div>
                        <div class="small text-muted">Productos: <?= $datos['inventario']['productos'] ?? 0 ?></div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-3">
                <div class="card shadow-sm border-0 rounded-4 h-100" data-bs-toggle="modal" data-bs-target="#modalUsuario" role="button" style="background: linear-gradient(135deg,#fef9c3,#fffbeb);">
                    <div class="card-body text-center">
                        <i class="bi bi-people-fill display-6 text-warning"></i>
                        <h6 class="fw-bold mt-2 mb-1">Usuarios</h6>
                        <div class="small text-muted">Registrados</div>
                        <div class="h5 fw-bold text-warning"><?= number_format($totalUsuarios) ?></div>
                        <div class="small text-muted">Activos: <?= $datos['usuarios']['activos'] ?? 0 ?></div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-3">
                <div class="card shadow-sm border-0 rounded-4 h-100" data-bs-toggle="modal" data-bs-target="#modalPagos" role="button" style="background: linear-gradient(135deg,#ffe2e5,#fff1f2);">
                    <div class="card-body text-center">
                        <i class="bi bi-cash-stack display-6 text-danger"></i>
                        <h6 class="fw-bold mt-2 mb-1">Pagos</h6>
                        <div class="small text-muted">Completados</div>
                        <div class="h5 fw-bold text-danger">$<?= number_format($datos['pagos']['realizados'] ?? 0, 2) ?></div>
                        <div class="small text-muted">Pendientes: $<?= number_format($datos['pagos']['pendientes'] ?? 0, 2) ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Pedidos / Ventas -->
    <div class="modal fade" id="tablaModal" tabindex="-1" aria-labelledby="tablaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow-lg">
                <div class="modal-header text-white rounded-top-4" style="background: linear-gradient(120deg,#6a5af9 0%,#7c3aed 100%);">
                    <div>
                        <h5 class="modal-title" id="tablaModalLabel">Ventas y pedidos</h5>
                        <small class="text-white-50">Filtra por rango de fechas o estado para inspeccionar operaciones.</small>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div class="filter-box rounded-3 p-3 mb-3 shadow-sm">
                        <div class="filters-wrapper">
                            <form class="filters-form" onsubmit="return false;">
                                <div>
                                    <label class="form-label small text-muted mb-1">Desde</label>
                                    <input type="date" class="form-control" name="fecha_inicio" id="modal_fecha_inicio">
                                </div>
                                <div>
                                    <label class="form-label small text-muted mb-1">Hasta</label>
                                    <input type="date" class="form-control" name="fecha_fin" id="modal_fecha_fin">
                                </div>
                                <div>
                                    <label class="form-label small text-muted mb-1">Estado</label>
                                    <select class="form-select" name="estado" id="modal_estado">
                                        <option value="">Todos</option>
                                        <option value="Pendiente">Pendiente</option>
                                        <option value="Completado">Completado</option>
                                        <option value="Cancelado">Cancelado</option>
                                    </select>
                                </div>
                                <div class="filters-actions">
                                    <button type="button" class="btn text-white w-100" id="btnFiltrarModal" style="background: linear-gradient(120deg,#6a5af9 0%,#7c3aed 100%);">
                                        <i class="bi bi-funnel me-1"></i>Filtrar
                                    </button>
                                </div>
                            </form>
                            <form id="formPdfPedidos" action="controllers/repopdf.php" method="POST" class="filters-export">
                                <input type="hidden" name="ids" id="pdf_ids">
                                <button type="submit" class="btn btn-outline-light text-primary fw-bold w-100" style="border-color:#7c3aed;">
                                    <i class="bi bi-file-earmark-pdf"></i> Exportar PDF
                                </button>
                            </form>
                        </div>
                    </div>
                    <div class="alert alert-info d-flex align-items-center gap-2 py-2">
                        <i class="bi bi-info-circle"></i>
                        Usa los filtros para ocultar filas en vivo; solo se exportan los pedidos visibles/seleccionados.
                    </div>
                    <div class="table-responsive mt-3">
                        <table class="table table-hover align-middle" id="tablaPedidosModal">
                            <thead class="table-light">
                                <tr>
                                    <th><input type="checkbox" id="selectAll" title="Seleccionar todo"></th>
                                    <th>ID</th>
                                    <th>Numero Pedido</th>
                                    <th>Fecha Pedido</th>
                                    <th>Monto Total</th>
                                    <th>Cliente</th>
                                    <th>Estado</th>
                                    <th>Empleado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($pedidosFiltrados)): ?>
                                    <?php foreach ($pedidosFiltrados as $pedido): ?>
                                        <?php $fechaPedidoISO = date('Y-m-d', strtotime($pedido['fecha_pedido'])); ?>
                                        <tr data-fecha="<?= $fechaPedidoISO ?>" data-estado="<?= strtolower($pedido['estado'] ?? '') ?>">
                                            <td><input type="checkbox" class="select-row" value="<?= htmlspecialchars($pedido['idped']) ?>"></td>
                                            <td><?= htmlspecialchars($pedido['idped']) ?></td>
                                            <td><?= htmlspecialchars($pedido['numped']) ?></td>
                                            <td><?= date('d/m/Y', strtotime($pedido['fecha_pedido'])) ?></td>
                                            <td>$<?= number_format($pedido['monto_total'], 2) ?></td>
                                            <td><?= htmlspecialchars($pedido['cli_idcli']) ?></td>
                                            <td><span class="badge bg-<?= badgeClaseEstado($pedido['estado'] ?? '') ?>"><?= htmlspecialchars($pedido['estado']) ?></span></td>
                                            <td><?= htmlspecialchars($pedido['empleado_id']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center text-warning">No hay pedidos registrados.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer bg-light rounded-bottom-4">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Usuarios -->
    <div class="modal fade" id="modalUsuario" tabindex="-1" aria-labelledby="tablaModalUsuariosLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow-lg">
                <div class="modal-header text-white rounded-top-4" style="background: linear-gradient(120deg,#f59e0b,#f97316);">
                    <div>
                        <h5 class="modal-title" id="tablaModalUsuariosLabel">Usuarios registrados</h5>
                        <small class="text-white-50">Filtra por rol para revisar los perfiles activos.</small>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div class="filter-box rounded-3 p-3 mb-3 shadow-sm">
                        <div class="filters-wrapper">
                            <form class="filters-form" onsubmit="return false;">
                                <div>
                                    <label class="form-label small text-muted mb-1">Rol</label>
                                    <select class="form-select" name="rol" id="modal_rol_usuarios">
                                        <option value="">Todos</option>
                                        <?php foreach ($tiposUsuarios as $tipo): ?>
                                            <option value="<?= strtolower($tipo) ?>"><?= htmlspecialchars($tipo) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="filters-actions">
                                    <button type="button" class="btn text-white w-100" id="btnFiltrarModalUsuarios" style="background: linear-gradient(120deg,#f59e0b,#f97316);">
                                        <i class="bi bi-funnel me-1"></i>Filtrar
                                    </button>
                                </div>
                            </form>
                            <form id="formPdfUsuarios" action="controllers/repopdf.php" method="POST" class="filters-export">
                                <input type="hidden" name="accion" value="usuarios_pdf">
                                <input type="hidden" name="tipo" id="tipoSeleccionado">
                                <input type="hidden" name="ids" id="pdf_ids_usuarios">
                                <button type="submit" class="btn btn-outline-warning text-warning fw-bold w-100">
                                    <i class="bi bi-file-earmark-pdf"></i> Exportar PDF
                                </button>
                            </form>
                        </div>
                    </div>
                    <div class="alert alert-warning bg-warning-subtle text-dark border-0 py-2">
                        Usuarios activos: <strong><?= $datos['usuarios']['activos'] ?? 0 ?></strong> / <?= $totalUsuarios ?>
                    </div>
                    <div class="table-responsive mt-3">
                        <table class="table table-hover align-middle" id="tablaUsuariosModal">
                            <thead class="table-light">
                                <tr>
                                    <th><input type="checkbox" id="selectAllUsuarios" title="Seleccionar todo"></th>
                                    <th>ID</th>
                                    <th>Usuario</th>
                                    <th>Nombre Completo</th>
                                    <th>Telefono</th>
                                    <th>Correo</th>
                                    <th>Activo</th>
                                    <th>Tipo Usuario</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($usuariosFiltrados)): ?>
                                    <?php foreach ($usuariosFiltrados as $u): ?>
                                        <tr data-tipo="<?= strtolower($u['tipo_usuario'] ?? '') ?>">
                                            <td><input type="checkbox" class="select-row" value="<?= htmlspecialchars($u['idusu']) ?>"></td>
                                            <td><?= htmlspecialchars($u['idusu']) ?></td>
                                            <td><?= htmlspecialchars($u['username']) ?></td>
                                            <td><?= htmlspecialchars($u['nombre_completo']) ?></td>
                                            <td><?= htmlspecialchars($u['telefono']) ?></td>
                                            <td><?= htmlspecialchars($u['email']) ?></td>
                                            <td><?= ($u['activo'] ? 'Si' : 'No') ?></td>
                                            <td><span class="badge bg-light text-dark"><?= htmlspecialchars($u['tipo_usuario']) ?></span></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center text-warning">No hay usuarios registrados.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer bg-light rounded-bottom-4">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Inventario -->
    <div class="modal fade" id="tablaModalFlores" tabindex="-1" aria-labelledby="tablaModalFloresLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow-lg">
                <div class="modal-header text-white rounded-top-4" style="background: linear-gradient(120deg,#10b981,#38bdf8);">
                    <div>
                        <h5 class="modal-title" id="tablaModalFloresLabel">Inventario de flores y productos</h5>
                        <small class="text-white-50">Monitorea disponibilidad por estado y genera reportes.</small>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div class="filter-box rounded-3 p-3 mb-3 shadow-sm">
                        <div class="filters-wrapper">
                            <form class="filters-form" onsubmit="return false;">
                                <div>
                                    <label class="form-label small text-muted mb-1">Estado</label>
                                    <select class="form-select" id="modal_estado_flores" name="estado_flor">
                                        <option value="">Todas</option>
                                        <option value="disponible">Disponible</option>
                                        <option value="agotado">Agotado</option>
                                        <option value="no disponible">No disponible</option>
                                    </select>
                                </div>
                                <div class="filters-actions">
                                    <button type="button" class="btn text-white w-100" id="btnFiltrarModalFlores" style="background: linear-gradient(120deg,#10b981,#38bdf8);">
                                        <i class="bi bi-funnel me-1"></i>Filtrar
                                    </button>
                                </div>
                            </form>
                            <form id="formPdfFlores" action="controllers/repopdf.php" method="POST" class="filters-export">
                                <input type="hidden" name="accion" value="flores_pdf">
                                <input type="hidden" name="ids" id="pdf_ids_flores">
                                <button type="submit" class="btn btn-outline-success text-success fw-bold w-100">
                                    <i class="bi bi-file-earmark-pdf"></i> Exportar PDF
                                </button>
                            </form>
                        </div>
                    </div>
                    <div class="alert alert-success bg-success-subtle border-0 py-2">
                        Stock total: <strong><?= number_format($datos['inventario']['stock_total'] ?? 0) ?></strong> | Productos: <?= number_format($datos['inventario']['productos'] ?? 0) ?>
                    </div>
                    <div class="table-responsive mt-3">
                        <table class="table table-hover align-middle" id="tablaFloresModal">
                            <thead class="table-light">
                                <tr>
                                    <th><input type="checkbox" id="selectAllFlores" title="Seleccionar todo"></th>
                                    <th>ID</th>
                                    <th>Producto</th>
                                    <th>Naturaleza</th>
                                    <th>Color</th>
                                    <th>Stock</th>
                                    <th>Estado</th>
                                    <th>Precio Unitario</th>
                                    <th>Valor Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($inventarioFiltrado)): ?>
                                    <?php foreach ($inventarioFiltrado as $f): ?>
                                        <tr data-estado="<?= strtolower($f['estado'] ?? '') ?>">
                                            <td><input type="checkbox" class="select-row" value="<?= htmlspecialchars($f['idtflor']) ?>"></td>
                                            <td><?= htmlspecialchars($f['idtflor']) ?></td>
                                            <td><?= htmlspecialchars($f['producto']) ?></td>
                                            <td><?= htmlspecialchars($f['naturaleza']) ?></td>
                                            <td><?= htmlspecialchars($f['color']) ?></td>
                                            <td><span class="badge bg-success-subtle text-success"><?= htmlspecialchars($f['stock']) ?></span></td>
                                            <td><?= htmlspecialchars($f['estado']) ?></td>
                                            <td>$<?= number_format($f['precio_unitario'], 2) ?></td>
                                            <td>$<?= number_format($f['valor_total'], 2) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="9" class="text-center text-warning">No hay registros en el inventario.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer bg-light rounded-bottom-4">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Pagos -->
    <div class="modal fade" id="modalPagos" tabindex="-1" aria-labelledby="modalPagosLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow-lg">
                <div class="modal-header text-white rounded-top-4" style="background: linear-gradient(120deg,#6a5af9,#ec4899);">
                    <div>
                        <h5 class="modal-title" id="modalPagosLabel">Pagos registrados</h5>
                        <small class="text-white-50">Monitorea los cobros recientes y exporta los filtrados.</small>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div class="filter-box rounded-3 p-3 mb-3 shadow-sm">
                        <div class="filters-wrapper">
                            <form class="filters-form" onsubmit="return false;">
                                <div>
                                    <label class="form-label small text-muted mb-1">Desde</label>
                                    <input type="date" class="form-control" name="fecha_inicio" id="modal_fecha_inicio_pagos">
                                </div>
                                <div>
                                    <label class="form-label small text-muted mb-1">Hasta</label>
                                    <input type="date" class="form-control" name="fecha_fin" id="modal_fecha_fin_pagos">
                                </div>
                                <div>
                                    <label class="form-label small text-muted mb-1">Estado</label>
                                    <select class="form-select" name="estado_pag" id="modal_estado_pagos">
                                        <option value="">Todos</option>
                                        <option value="Completado">Completado</option>
                                        <option value="Pendiente">Pendiente</option>
                                        <option value="Cancelado">Cancelado</option>
                                    </select>
                                </div>
                                <div class="filters-actions">
                                    <button type="button" class="btn text-white w-100" id="btnFiltrarPagos" style="background: linear-gradient(120deg,#6a5af9,#ec4899);">
                                        <i class="bi bi-funnel me-1"></i>Filtrar
                                    </button>
                                </div>
                            </form>
                            <form id="formPdfPagos" action="controllers/repopdf.php" method="POST" class="filters-export">
                                <input type="hidden" name="accion" value="pagos_pdf">
                                <input type="hidden" name="ids" id="pdf_ids_pagos">
                                <button type="submit" class="btn btn-outline-danger text-danger fw-bold w-100">
                                    <i class="bi bi-file-earmark-pdf"></i> Exportar PDF
                                </button>
                            </form>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-12 col-md-6">
                            <div class="p-3 rounded-3 bg-light border">
                                <div class="small text-muted">Pagos completados</div>
                                <div class="h5 mb-0 text-success">$<?= number_format($datos['pagos']['realizados'] ?? 0, 2) ?></div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="p-3 rounded-3 bg-light border">
                                <div class="small text-muted">Pagos pendientes</div>
                                <div class="h5 mb-0 text-warning">$<?= number_format($datos['pagos']['pendientes'] ?? 0, 2) ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive mt-3">
                        <table class="table table-hover align-middle" id="tablaPagosModal">
                            <thead class="table-light">
                                <tr>
                                    <th><input type="checkbox" id="selectAllPagos" title="Seleccionar todo"></th>
                                    <th>ID</th>
                                    <th>Fecha</th>
                                    <th>Pedido</th>
                                    <th>Cliente</th>
                                    <th>Metodo</th>
                                    <th>Monto</th>
                                    <th>Estado</th>
                                    <th>Transaccion</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($pagosFiltrados)): ?>
                                    <?php foreach ($pagosFiltrados as $pago): ?>
                                        <?php
                                            $estadoBadge = badgeClaseEstado($pago['estado_pag'] ?? '');
                                            $estadoTexto = htmlspecialchars($pago['estado_pag'] ?? 'N/D');
                                            $fechaPagoISO = date('Y-m-d', strtotime($pago['fecha_pago']));
                                        ?>
                                        <tr data-fecha="<?= $fechaPagoISO ?>" data-estado="<?= strtolower($pago['estado_pag'] ?? '') ?>">
                                            <td><input type="checkbox" class="select-row" value="<?= htmlspecialchars($pago['idpago'] ?? $pago['numped'] ?? '') ?>"></td>
                                            <td><?= htmlspecialchars($pago['idpago'] ?? '-') ?></td>
                                            <td><?= date('d/m/Y H:i', strtotime($pago['fecha_pago'])) ?></td>
                                            <td><?= htmlspecialchars($pago['numped'] ?? '-') ?></td>
                                            <td><?= htmlspecialchars($pago['cliente'] ?? 'Cliente no definido') ?></td>
                                            <td><?= htmlspecialchars($pago['metodo_pago'] ?? 'N/D') ?></td>
                                            <td>$<?= number_format($pago['monto'] ?? 0, 2) ?></td>
                                            <td><span class="badge bg-<?= $estadoBadge ?>"><?= $estadoTexto ?></span></td>
                                            <td><?= htmlspecialchars($pago['transaccion_id'] ?? 'N/A') ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="9" class="text-center text-warning">No hay pagos registrados.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer bg-light rounded-bottom-4">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/repo.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
