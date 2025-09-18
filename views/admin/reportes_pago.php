<?php ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes de Pagos - FloralTech</title>
    <link rel="stylesheet" href="/assets/dashboard-admin.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
</head>
<body>
<div class="container">
    <!-- Tarjetas de accesos rápidos a reportes -->
        <div class="row mb-4 g-3">
            <div class="col-12 col-md-3 mb-3">
                <div class="card border-success h-100 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalGanancias" style="cursor:pointer;">
                    <div class="card-body text-center">
                        <i class="bi bi-currency-dollar h1 text-success"></i>
                        <h6 class="fw-bold mt-2">Ganancias</h6>
                        <div class="small">Ingresos: <span class="fw-bold">$<?= number_format($datos['resumen']['total_recaudado'] ?? 0, 2) ?></span></div>
                        <div class="small">Utilidad Neta: <span class="fw-bold">$<?= number_format($datos['resumen']['ganancia_neta'] ?? 0, 2) ?></span></div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-3 mb-3">
                <div class="card border-primary h-100 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalVentas" style="cursor:pointer;">
                    <div class="card-body text-center">
                        <i class="bi bi-bar-chart-line h1 text-primary"></i>
                        <h6 class="fw-bold mt-2">Ventas</h6>
                        <div class="small">Total: <span class="fw-bold">$<?= number_format($datos['ventas']['total'] ?? 0, 2) ?></span></div>
                        <div class="small">Pedidos: <span class="fw-bold"><?= $datos['ventas']['pedidos'] ?? 0 ?></span></div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-3 mb-3">
                <div class="card border-danger h-100 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalCostos" style="cursor:pointer;">
                    <div class="card-body text-center">
                        <i class="bi bi-receipt h1 text-danger"></i>
                        <h6 class="fw-bold mt-2">Costos</h6>
                        <div class="small">Total: <span class="fw-bold">$<?= number_format($datos['costos']['total'] ?? 0, 2) ?></span></div>
                        <div class="small">Fijos: <span class="fw-bold">$<?= number_format($datos['costos']['fijos'] ?? 0, 2) ?></span></div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-3 mb-3">
                <div class="card border-info h-100 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalInventario" style="cursor:pointer;">
                    <div class="card-body text-center">
                        <i class="bi bi-box-seam h1 text-info"></i>
                        <h6 class="fw-bold mt-2">Inventario</h6>
                        <div class="small">Stock: <span class="fw-bold"><?= $datos['inventario']['stock_total'] ?? 0 ?></span></div>
                        <div class="small">Productos: <span class="fw-bold"><?= $datos['inventario']['productos'] ?? 0 ?></span></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mb-4 g-3">
            <div class="col-12 col-md-3 mb-3">
                <div class="card border-warning h-100 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalCuentas" style="cursor:pointer;">
                    <div class="card-body text-center">
                        <i class="bi bi-person-badge h1 text-warning"></i>
                        <h6 class="fw-bold mt-2">Cuentas</h6>
                        <div class="small">Por Cobrar: <span class="fw-bold">$<?= number_format($datos['cuentas']['por_cobrar'] ?? 0, 2) ?></span></div>
                        <div class="small">Por Pagar: <span class="fw-bold">$<?= number_format($datos['cuentas']['por_pagar'] ?? 0, 2) ?></span></div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-3 mb-3">
                <div class="card border-secondary h-100 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalPagos" style="cursor:pointer;">
                    <div class="card-body text-center">
                        <i class="bi bi-cash-stack h1 text-secondary"></i>
                        <h6 class="fw-bold mt-2">Pagos</h6>
                        <div class="small">Realizados: <span class="fw-bold">$<?= number_format($datos['pagos']['realizados'] ?? 0, 2) ?></span></div>
                        <div class="small">Pendientes: <span class="fw-bold">$<?= number_format($datos['pagos']['pendientes'] ?? 0, 2) ?></span></div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-3 mb-3">
                <div class="card border-dark h-100 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalProyecciones" style="cursor:pointer;">
                    <div class="card-body text-center">
                        <i class="bi bi-bar-chart h1 text-dark"></i>
                        <h6 class="fw-bold mt-2">Proyecciones</h6>
                        <div class="small">Ventas: <span class="fw-bold">$<?= number_format($datos['proyecciones']['ventas'] ?? 0, 2) ?></span></div>
                        <div class="small">Ganancias: <span class="fw-bold">$<?= number_format($datos['proyecciones']['ganancias'] ?? 0, 2) ?></span></div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-3 mb-3">
                <div class="card border-light h-100 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalAuditoria" style="cursor:pointer;">
                    <div class="card-body text-center">
                        <i class="bi bi-shield-check h1 text-secondary"></i>
                        <h6 class="fw-bold mt-2">Auditoría</h6>
                        <div class="small">Acciones: <span class="fw-bold"><?= $datos['auditoria']['acciones'] ?? 0 ?></span></div>
                        <div class="small">Incidencias: <span class="fw-bold"><?= $datos['auditoria']['incidencias'] ?? 0 ?></span></div>
                    </div>
                </div>
            </div>
        </div>
    
        <!-- Filtros adicionales mes/año antes de las cards -->
    <form class="row mb-3 g-2 flex-wrap" method="get">
                <div class="col-12 col-md-2">
                <input type="text" class="form-control" name="cliente" placeholder="Cliente" value="<?= htmlspecialchars($_GET['cliente'] ?? '') ?>">
            </div>
                <div class="col-12 col-md-2">
                <input type="date" class="form-control" name="fecha_inicio" value="<?= htmlspecialchars($_GET['fecha_inicio'] ?? '') ?>">
            </div>
                <div class="col-12 col-md-2">
                <input type="date" class="form-control" name="fecha_fin" value="<?= htmlspecialchars($_GET['fecha_fin'] ?? '') ?>">
            </div>
                <div class="col-12 col-md-2">
                <select class="form-select" name="estado">
                    <option value="todos">Todos</option>
                    <option value="completado" <?= (isset($_GET['estado']) && $_GET['estado'] == 'completado') ? 'selected' : '' ?>>Completado</option>
                    <option value="pendiente" <?= (isset($_GET['estado']) && $_GET['estado'] == 'pendiente') ? 'selected' : '' ?>>Pendiente</option>
                    <option value="cancelado" <?= (isset($_GET['estado']) && $_GET['estado'] == 'cancelado') ? 'selected' : '' ?>>Cancelado</option>
                </select>
            </div>
                <div class="col-12 col-md-2">
                <select class="form-select" name="mes">
                    <option value="">Mes</option>
                    <?php for ($m = 1; $m <= 12; $m++): ?>
                        <option value="<?= str_pad($m, 2, '0', STR_PAD_LEFT) ?>" <?= (isset($_GET['mes']) && $_GET['mes'] == str_pad($m, 2, '0', STR_PAD_LEFT)) ? 'selected' : '' ?>><?= date('F', mktime(0,0,0,$m,1)) ?></option>
                    <?php endfor; ?>
                </select>
            </div>
                <div class="col-12 col-md-2">
                <select class="form-select" name="anio">
                    <option value="">Año</option>
                    <?php $anioActual = date('Y');
                    for ($a = $anioActual; $a >= $anioActual-5; $a--): ?>
                        <option value="<?= $a ?>" <?= (isset($_GET['anio']) && $_GET['anio'] == $a) ? 'selected' : '' ?>><?= $a ?></option>
                    <?php endfor; ?>
                </select>
            </div>
                <div class="col-12 col-md-2">
                <button type="submit" class="btn btn-primary w-100">Filtrar</button>
            </div>
        </form>
                ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Fecha</th>
                                <th>Cliente</th>
                                <th>Pedido ID</th>
                                <th>Total</th>
                                <th>Método</th>
                                <th>Estado</th>
                                <th>Transacción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($pagosFiltrados)): ?>
                                <?php foreach ($pagosFiltrados as $pago): ?>
                                <tr>
                                    <td><?= date('d/m/Y H:i', strtotime($pago['fecha_pago'])) ?></td>
                                    <td><?= htmlspecialchars($pago['cliente']) ?></td>
                                    <td><?= htmlspecialchars($pago['numped']) ?></td>
                                    <td>$<?= number_format($pago['monto'], 2) ?></td>
                                    <td><?= htmlspecialchars($pago['metodo_pago']) ?></td>
                                    <td>
                                        <span class="badge bg-<?= 
                                            $pago['estado_pag'] === 'Completado' ? 'success' : 
                                            ($pago['estado_pag'] === 'Pendiente' ? 'warning' : 'danger') 
                                        ?>">
                                            <?= htmlspecialchars($pago['estado_pag']) ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($pago['transaccion_id'] ?? 'N/A') ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center text-warning">No se encontraron datos para los filtros seleccionados.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modalVentas" tabindex="-1" aria-labelledby="modalVentasLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalVentasLabel">Reporte de Ventas</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                                            <div class="row mb-4">
                                                <div class="col-md-3 mb-3">
                                                    <div class="card border-success h-100 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalGanancias" style="cursor:pointer;">
                                                        <div class="card-body text-center">
                                                            <i class="bi bi-currency-dollar h1 text-success"></i>
                                                            <h6 class="fw-bold mt-2">Ganancias</h6>
                                                            <div class="small">Ingresos: <span class="fw-bold">$<?= number_format($datos['resumen']['total_recaudado'] ?? 0, 2) ?></span></div>
                                                            <div class="small">Utilidad Neta: <span class="fw-bold">$<?= number_format($datos['resumen']['ganancia_neta'] ?? 0, 2) ?></span></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 mb-3">
                                                    <div class="card border-primary h-100 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalVentas" style="cursor:pointer;">
                                                        <div class="card-body text-center">
                                                            <i class="bi bi-bar-chart-line h1 text-primary"></i>
                                                            <h6 class="fw-bold mt-2">Ventas</h6>
                                                            <div class="small">Total: <span class="fw-bold">$<?= number_format($datos['ventas']['total'] ?? 0, 2) ?></span></div>
                                                            <div class="small">Pedidos: <span class="fw-bold"><?= $datos['ventas']['pedidos'] ?? 0 ?></span></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 mb-3">
                                                    <div class="card border-danger h-100 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalCostos" style="cursor:pointer;">
                                                        <div class="card-body text-center">
                                                            <i class="bi bi-receipt h1 text-danger"></i>
                                                            <h6 class="fw-bold mt-2">Costos</h6>
                                                            <div class="small">Total: <span class="fw-bold">$<?= number_format($datos['costos']['total'] ?? 0, 2) ?></span></div>
                                                            <div class="small">Fijos: <span class="fw-bold">$<?= number_format($datos['costos']['fijos'] ?? 0, 2) ?></span></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 mb-3">
                                                    <div class="card border-info h-100 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalInventario" style="cursor:pointer;">
                                                        <div class="card-body text-center">
                                                            <i class="bi bi-box-seam h1 text-info"></i>
                                                            <h6 class="fw-bold mt-2">Inventario</h6>
                                                            <div class="small">Stock: <span class="fw-bold"><?= $datos['inventario']['stock_total'] ?? 0 ?></span></div>
                                                            <div class="small">Productos: <span class="fw-bold"><?= $datos['inventario']['productos'] ?? 0 ?></span></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mb-4">
                                                <div class="col-md-3 mb-3">
                                                    <div class="card border-warning h-100 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalCuentas" style="cursor:pointer;">
                                                        <div class="card-body text-center">
                                                            <i class="bi bi-person-badge h1 text-warning"></i>
                                                            <h6 class="fw-bold mt-2">Cuentas</h6>
                                                            <div class="small">Por Cobrar: <span class="fw-bold">$<?= number_format($datos['cuentas']['por_cobrar'] ?? 0, 2) ?></span></div>
                                                            <div class="small">Por Pagar: <span class="fw-bold">$<?= number_format($datos['cuentas']['por_pagar'] ?? 0, 2) ?></span></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 mb-3">
                                                    <div class="card border-secondary h-100 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalPagos" style="cursor:pointer;">
                                                        <div class="card-body text-center">
                                                            <i class="bi bi-cash-stack h1 text-secondary"></i>
                                                            <h6 class="fw-bold mt-2">Pagos</h6>
                                                            <div class="small">Realizados: <span class="fw-bold">$<?= number_format($datos['pagos']['realizados'] ?? 0, 2) ?></span></div>
                                                            <div class="small">Pendientes: <span class="fw-bold">$<?= number_format($datos['pagos']['pendientes'] ?? 0, 2) ?></span></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 mb-3">
                                                    <div class="card border-dark h-100 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalProyecciones" style="cursor:pointer;">
                                                        <div class="card-body text-center">
                                                            <i class="bi bi-bar-chart h1 text-dark"></i>
                                                            <h6 class="fw-bold mt-2">Proyecciones</h6>
                                                            <div class="small">Ventas: <span class="fw-bold">$<?= number_format($datos['proyecciones']['ventas'] ?? 0, 2) ?></span></div>
                                                            <div class="small">Ganancias: <span class="fw-bold">$<?= number_format($datos['proyecciones']['ganancias'] ?? 0, 2) ?></span></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 mb-3">
                                                    <div class="card border-light h-100 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalAuditoria" style="cursor:pointer;">
                                                        <div class="card-body text-center">
                                                            <i class="bi bi-shield-check h1 text-secondary"></i>
                                                            <h6 class="fw-bold mt-2">Auditoría</h6>
                                                            <div class="small">Acciones: <span class="fw-bold"><?= $datos['auditoria']['acciones'] ?? 0 ?></span></div>
                                                            <div class="small">Incidencias: <span class="fw-bold"><?= $datos['auditoria']['incidencias'] ?? 0 ?></span></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                                                <p class="card-text">Consulta el detalle de gastos y pagos realizados a cada proveedor.</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- ...existing code... -->
                </div>
            </div>
        </div>
        <div class="modal fade" id="modalInventario" tabindex="-1" aria-labelledby="modalInventarioLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalInventarioLabel">Reporte de Inventario</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                                            <div class="modal-body">
                                                <div class="row g-3 mb-4">
                                                    <div class="col-md-6">
                                                        <div class="card bg-light border-0 shadow-sm">
                                                            <div class="card-body">
                                                                <h5 class="card-title"><i class="bi bi-box-seam"></i> Existencias actuales de flores y accesorios</h5>
                                                                <p class="card-text">Consulta el inventario actual de flores y accesorios disponibles.</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="card bg-light border-0 shadow-sm">
                                                            <div class="card-body">
                                                                <h5 class="card-title"><i class="bi bi-exclamation-triangle"></i> Productos próximos a vencerse</h5>
                                                                <p class="card-text">Identifica los productos que están próximos a vencer para evitar pérdidas.</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- ...existing code... -->
                </div>
            </div>
        </div>
        <div class="modal fade" id="modalCuentas" tabindex="-1" aria-labelledby="modalCuentasLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalCuentasLabel">Reporte de Cuentas</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                                            <div class="modal-body">
                                                <div class="row g-3 mb-4">
                                                    <div class="col-md-6">
                                                        <div class="card bg-light border-0 shadow-sm">
                                                            <div class="card-body">
                                                                <h5 class="card-title"><i class="bi bi-person-badge"></i> Clientes deudores</h5>
                                                                <p class="card-text">Listado de clientes que tienen cuentas por cobrar pendientes.</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="card bg-light border-0 shadow-sm">
                                                            <div class="card-body">
                                                                <h5 class="card-title"><i class="bi bi-file-earmark-text"></i> Proveedores con facturas pendientes</h5>
                                                                <p class="card-text">Listado de proveedores que tienen facturas por pagar pendientes.</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- ...existing code... -->
                </div>
            </div>
        </div>
        <div class="modal fade" id="modalPagos" tabindex="-1" aria-labelledby="modalPagosLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalPagosLabel">Reporte de Pagos</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                                            <div class="modal-body">
                                                <div class="row g-3 mb-4">
                                                    <div class="col-md-6">
                                                        <div class="card bg-light border-0 shadow-sm">
                                                            <div class="card-body">
                                                                <h5 class="card-title"><i class="bi bi-cash-stack"></i> Entradas y salidas de efectivo</h5>
                                                                <p class="card-text">Visualiza todos los movimientos de efectivo realizados en caja y banco.</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="card bg-light border-0 shadow-sm">
                                                            <div class="card-body">
                                                                <h5 class="card-title"><i class="bi bi-bank"></i> Saldo disponible en caja/banco</h5>
                                                                <p class="card-text">Consulta el saldo actual disponible en caja y cuentas bancarias.</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- ...existing code... -->
                </div>
            </div>
        </div>
        <div class="modal fade" id="modalProyecciones" tabindex="-1" aria-labelledby="modalProyeccionesLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalProyeccionesLabel">Reporte de Proyecciones</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                                            <div class="modal-body">
                                                <div class="row g-3 mb-4">
                                                    <div class="col-md-6">
                                                        <div class="card bg-light border-0 shadow-sm">
                                                            <div class="card-body">
                                                                <h5 class="card-title"><i class="bi bi-bar-chart"></i> Pronósticos de ventas por temporada</h5>
                                                                <p class="card-text">Visualiza los pronósticos de ventas para cada temporada relevante.</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="card bg-light border-0 shadow-sm">
                                                            <div class="card-body">
                                                                <h5 class="card-title"><i class="bi bi-graph-up-arrow"></i> Gráficos de tendencia histórica</h5>
                                                                <p class="card-text">Consulta los gráficos de tendencia histórica para tomar mejores decisiones.</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- ...existing code... -->
                </div>
            </div>
        </div>
        <div class="modal fade" id="modalUsuarios" tabindex="-1" aria-labelledby="modalUsuariosLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalUsuariosLabel">Reporte de Usuarios</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                                            <div class="modal-body">
                                                <div class="row g-3 mb-4">
                                                    <div class="col-md-6">
                                                        <div class="card bg-light border-0 shadow-sm">
                                                            <div class="card-body">
                                                                <h5 class="card-title"><i class="bi bi-shield-check"></i> Control de transacciones</h5>
                                                                <p class="card-text">Revisa el estado de las transacciones: aprobadas, pendientes y fallidas.</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="card bg-light border-0 shadow-sm">
                                                            <div class="card-body">
                                                                <h5 class="card-title"><i class="bi bi-exclamation-octagon"></i> Identificación de inconsistencias y responsables</h5>
                                                                <p class="card-text">Detecta inconsistencias y visualiza los responsables de cada transacción.</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- ...existing code... -->
                </div>
            </div>
        </div>
<!-- Bootstrap JS Bundle (includes Popper) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    </div>
    <?php
    // Inicializar variables con valores por defecto
    $tipo = $tipo ?? ($_GET['tipo'] ?? 'ganancias');
    if (!isset($datos) || !is_array($datos)) {
        $datos = [];
    }
    $datos = array_merge([
        'resumen' => [
            'total_recaudado' => 0,
            'ganancia_neta' => 0,
            'total_ventas' => 0,
            'total_costos' => 0,
            'pagos_completados' => 0,
            'pagos_pendientes' => 0,
        ],
        'ventas' => [
            'total' => 0,
            'pedidos' => 0,
            'clientes' => 0,
            'promedio' => 0,
        ],
        'costos' => [
            'total' => 0,
            'fijos' => 0,
            'variables' => 0,
            'otros' => 0,
        ],
        'inventario' => [
            'stock_total' => 0,
            'productos' => 0,
            'stock_bajo' => 0,
            'stock_critico' => 0,
        ],
        'cuentas' => [
            'por_cobrar' => 0,
            'por_pagar' => 0,
            'saldo_neto' => 0,
            'movimientos' => 0,
        ],
        'pagos' => [
            'realizados' => 0,
            'pendientes' => 0,
            'rechazados' => 0,
            'transacciones' => 0,
        ],
        'proyecciones' => [
            'ventas' => 0,
            'ganancias' => 0,
            'costos' => 0,
            'periodo' => '',
        ],
        'auditoria' => [
            'acciones' => 0,
            'usuarios' => 0,
            'ultima' => '',
            'incidencias' => 0,
        ],
    ], $datos);
    ?>
    
            <!-- Modals for each report type -->
            <!-- Pagos Modal -->
            <div class="modal fade" id="modalPagos" tabindex="-1" aria-labelledby="modalPagosLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalPagosLabel">Reporte de Pagos</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <!-- Filtros, gráfico y tabla de pagos aquí -->
                            <div class="row mb-4">
                                <div class="col-md-3 mb-3">
                                    <div class="card border-success h-100 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalGanancias" style="cursor:pointer;">
                                        <div class="card-body text-center">
                                            <i class="bi bi-currency-dollar h1 text-success"></i>
                                            <h6 class="fw-bold mt-2">Ganancias</h6>
                                            <div class="small">Ingresos: <span class="fw-bold">$<?= number_format($datos['resumen']['total_recaudado'] ?? 0, 2) ?></span></div>
                                            <div class="small">Utilidad Neta: <span class="fw-bold">$<?= number_format($datos['resumen']['ganancia_neta'] ?? 0, 2) ?></span></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <div class="card border-primary h-100 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalVentas" style="cursor:pointer;">
                                        <div class="card-body text-center">
                                            <i class="bi bi-bar-chart-line h1 text-primary"></i>
                                            <h6 class="fw-bold mt-2">Ventas</h6>
                                            <div class="small">Total: <span class="fw-bold">$<?= number_format($datos['ventas']['total'] ?? 0, 2) ?></span></div>
                                            <div class="small">Pedidos: <span class="fw-bold"><?= $datos['ventas']['pedidos'] ?? 0 ?></span></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <div class="card border-danger h-100 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalCostos" style="cursor:pointer;">
                                        <div class="card-body text-center">
                                            <i class="bi bi-receipt h1 text-danger"></i>
                                            <h6 class="fw-bold mt-2">Costos</h6>
                                            <div class="small">Total: <span class="fw-bold">$<?= number_format($datos['costos']['total'] ?? 0, 2) ?></span></div>
                                            <div class="small">Fijos: <span class="fw-bold">$<?= number_format($datos['costos']['fijos'] ?? 0, 2) ?></span></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <div class="card border-info h-100 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalInventario" style="cursor:pointer;">
                                        <div class="card-body text-center">
                                            <i class="bi bi-box-seam h1 text-info"></i>
                                            <h6 class="fw-bold mt-2">Inventario</h6>
                                            <div class="small">Stock: <span class="fw-bold"><?= $datos['inventario']['stock_total'] ?? 0 ?></span></div>
                                            <div class="small">Productos: <span class="fw-bold"><?= $datos['inventario']['productos'] ?? 0 ?></span></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <canvas id="chartPagos"></canvas>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Fecha</th>
                                                <th>Monto</th>
                                                <th>Estado</th>
                                                <th>Método</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Aquí van los datos dinámicos -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cuentas Modal -->
            <div class="modal fade" id="modalCuentas" tabindex="-1" aria-labelledby="modalCuentasLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalCuentasLabel">Reporte de Cuentas</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <!-- Filtros, gráfico y tabla de cuentas aquí -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Usuarios Modal -->
            <div class="modal fade" id="modalUsuarios" tabindex="-1" aria-labelledby="modalUsuariosLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalUsuariosLabel">Reporte de Usuarios</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <!-- Filtros, gráfico y tabla de usuarios aquí -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Inventario Modal -->
            <div class="modal fade" id="modalInventario" tabindex="-1" aria-labelledby="modalInventarioLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalInventarioLabel">Reporte de Inventario</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <!-- Filtros, gráfico y tabla de inventario aquí -->
                        </div>
                    </div>
                </div>
            </div>


    <h2 class="my-4">Reporte de <?= htmlspecialchars(ucfirst($tipo)) ?></h2>

    <!-- Filtros del reporte -->
    <fieldset class="border rounded p-3 mb-4 bg-light">
        <legend class="float-none w-auto px-2 mb-2 fw-bold"><i class="bi bi-funnel"></i> Filtros del reporte</legend>
        <form class="d-flex flex-row flex-wrap align-items-end justify-content-between w-100 gap-3" method="get" action="" style="min-height:70px;">
            <div style="min-width:160px;">
                <label for="fecha_inicio" class="form-label mb-1">Fecha Inicio</label>
                <input type="date" id="fecha_inicio" name="fecha_inicio" class="form-control" value="<?= htmlspecialchars($_GET['fecha_inicio'] ?? '') ?>">
            </div>
            <div style="min-width:160px;">
                <label for="fecha_fin" class="form-label mb-1">Fecha Fin</label>
                <input type="date" id="fecha_fin" name="fecha_fin" class="form-control" value="<?= htmlspecialchars($_GET['fecha_fin'] ?? '') ?>">
            </div>
            <div style="min-width:140px;">
                <label for="estado" class="form-label mb-1">Estado</label>
                <select id="estado" name="estado" class="form-select">
                    <option value="" <?= empty($_GET['estado']) ? 'selected' : '' ?>>Todos</option>
                    <option value="Completado" <?= (($_GET['estado'] ?? '') === 'Completado') ? 'selected' : '' ?>>Completado</option>
                    <option value="Pendiente" <?= (($_GET['estado'] ?? '') === 'Pendiente') ? 'selected' : '' ?>>Pendiente</option>
                    <option value="Cancelado" <?= (($_GET['estado'] ?? '') === 'Cancelado') ? 'selected' : '' ?>>Cancelado</option>
                </select>
            </div>
            <div class="d-flex align-items-end" style="min-width:120px;">
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search"></i> Filtrar</button>
            </div>
            <div class="d-flex flex-row flex-wrap align-items-center gap-2 mt-2" style="flex:1;">
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-outline-info btn-sm" onclick="setQuickDate('hoy')">Hoy</button>
                    <button type="button" class="btn btn-outline-info btn-sm" onclick="setQuickDate('7dias')">Últimos 7 días</button>
                    <button type="button" class="btn btn-outline-info btn-sm" onclick="setQuickDate('mes')">Este mes</button>
                    <button type="button" class="btn btn-outline-info btn-sm" onclick="setQuickDate('mespasado')">Mes pasado</button>
                </div>
                <button type="button" class="btn btn-outline-dark btn-sm" onclick="toggleDemo()"><i class="bi bi-eye"></i> Datos de Ejemplo</button>
                <button type="button" class="btn btn-success btn-sm" onclick="exportar('csv')"><i class="bi bi-file-earmark-spreadsheet"></i> Exportar Excel</button>
                <button type="button" class="btn btn-danger btn-sm" onclick="exportar('pdf')"><i class="bi bi-file-earmark-pdf"></i> Exportar PDF</button>
            </div>
        </form>
    </fieldset>
    
    <?php if (isset($_SESSION['mensaje'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?= htmlspecialchars($_SESSION['mensaje']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['mensaje']); ?>
    <?php endif; ?>

    <?php
    $mostrarDemo = isset($_GET['demo']) && $_GET['demo'] === '1';
    if ($mostrarDemo) {
        // Datos de ejemplo para modo demo
        $datos = [
            [
                'idpago' => '1001', 'fecha_pago' => date('Y-m-d H:i'), 'numped' => 'PED-001', 'cliente' => 'Juan Pérez', 'metodo_pago' => 'Efectivo', 'monto' => 120.50, 'estado_pag' => 'Completado', 'transaccion_id' => 'TX12345'
            ],
            [
                'idpago' => '1002', 'fecha_pago' => date('Y-m-d H:i', strtotime('-2 days')), 'numped' => 'PED-002', 'cliente' => 'Ana Gómez', 'metodo_pago' => 'Tarjeta', 'monto' => 250.00, 'estado_pag' => 'Pendiente', 'transaccion_id' => 'TX12346'
            ],
            [
                'idpago' => '1003', 'fecha_pago' => date('Y-m-d H:i', strtotime('-7 days')), 'numped' => 'PED-003', 'cliente' => 'Carlos Ruiz', 'metodo_pago' => 'Transferencia', 'monto' => 80.00, 'estado_pag' => 'Completado', 'transaccion_id' => 'TX12347'
            ],
        ];
    }
    ?>
    <?php if (empty($datos)): ?>
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Fecha</th>
                        <th>Cliente</th>
                        <th>Pedido ID</th>
                        <th>Total</th>
                        <th>Método</th>
                        <th>Estado</th>
                        <th>Transacción</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="7" class="text-center text-warning">
                            <i class="bi bi-exclamation-triangle"></i> No se encontraron datos para los filtros seleccionados.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <?php
        // Resumen numérico arriba de la tabla
        if ($tipo === 'ganancias' && !empty($datos['resumen'])) {
            $totalVentas = $datos['resumen']['total_recaudado'] ?? 0;
            $ganancia = $datos['resumen']['ganancia_neta'] ?? ($totalVentas * 0.7);
            $pedidos = $datos['resumen']['pagos_completados'] + $datos['resumen']['pagos_pendientes'];
            $ticketProm = $pedidos ? $totalVentas / $pedidos : 0;
        ?>
        <div class="row mb-3">
            <div class="col-md-3">
                <div class="card bg-success text-white text-center">
                    <div class="card-body">
                        <div class="fw-bold">Total Ventas</div>
                        <div class="h5">$<?= number_format($totalVentas, 2) ?></div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-primary text-white text-center">
                    <div class="card-body">
                        <div class="fw-bold">Ganancia Neta</div>
                        <div class="h5">$<?= number_format($ganancia, 2) ?></div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white text-center">
                    <div class="card-body">
                        <div class="fw-bold">Pedidos</div>
                        <div class="h5"><?= $pedidos ?></div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white text-center">
                    <div class="card-body">
                        <div class="fw-bold">Ticket Promedio</div>
                        <div class="h5">$<?= number_format($ticketProm, 2) ?></div>
                    </div>
                </div>
            </div>
        </div>
        <?php } ?>
            <!-- Reporte de Ganancias -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Estadísticas de Ganancias</h5>
                </div>
                <div class="card-body">
                    <?php if (isset($datos['metodos_pago'])): ?>
                        <div class="row">
                            <div class="col-md-6">
                                <canvas id="metodosPagoChart" height="300"></canvas>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-striped">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Método</th>
                                            <th>Cantidad</th>
                                            <th>Total</th>
                                            <th>Porcentaje</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($datos['metodos_pago'] as $metodo): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($metodo['metodo_pago']) ?></td>
                                            <td><?= $metodo['cantidad'] ?></td>
                                            <td>$<?= number_format($metodo['ingresos_totales'], 2) ?></td>
                                            <td><?= number_format($metodo['porcentaje'], 2) ?>%</td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($datos['resumen'])): ?>
                        <hr>
                        <div class="row mt-4">
                            <div class="col-md-4">
                                <div class="card text-white bg-success mb-3">
                                    <div class="card-body">
                                        <h5 class="card-title">Total Recaudado</h5>
                                        <p class="card-text h4">$<?= number_format($datos['resumen']['total_recaudado'], 2) ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card text-white bg-info mb-3">
                                    <div class="card-body">
                                        <h5 class="card-title">Pagos Completados</h5>
                                        <p class="card-text h4"><?= $datos['resumen']['pagos_completados'] ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card text-white bg-warning mb-3">
                                    <div class="card-body">
                                        <h5 class="card-title">Pagos Pendientes</h5>
                                        <p class="card-text h4"><?= $datos['resumen']['pagos_pendientes'] ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
    <?php endif; ?>
    <?php if ($tipo === 'auditoria'): ?>
            <!-- Reporte de Auditoría -->
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Auditoría de Pagos</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Fecha</th>
                                    <th>Pedido</th>
                                    <th>Cliente</th>
                                    <th>Método</th>
                                    <th>Monto</th>
                                    <th>Estado</th>
                                    <th>Transacción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Filtro por cliente y fechas
                                $clienteFiltro = strtolower(trim($_GET['cliente'] ?? ''));
                                $fechaInicio = $_GET['fecha_inicio'] ?? '';
                                $fechaFin = $_GET['fecha_fin'] ?? '';
                                foreach ($datos as $pago):
                                    if ($clienteFiltro && strpos(strtolower($pago['cliente']), $clienteFiltro) === false) continue;
                                    if ($fechaInicio && strtotime($pago['fecha_pago']) < strtotime($fechaInicio)) continue;
                                    if ($fechaFin && strtotime($pago['fecha_pago']) > strtotime($fechaFin . ' 23:59:59')) continue;
                                ?>
                                <tr>
                                    <td><?= htmlspecialchars($pago['idpago']) ?></td>
                                    <td><?= date('d/m/Y H:i', strtotime($pago['fecha_pago'])) ?></td>
                                    <td><?= htmlspecialchars($pago['numped']) ?></td>
                                    <td><?= htmlspecialchars($pago['cliente']) ?></td>
                                    <td><?= htmlspecialchars($pago['metodo_pago']) ?></td>
                                    <td>$<?= number_format($pago['monto'], 2) ?></td>
                                    <td>
                                        <span class="badge bg-<?= 
                                            $pago['estado_pag'] === 'Completado' ? 'success' : 
                                            ($pago['estado_pag'] === 'Pendiente' ? 'warning' : 'danger') 
                                        ?>">
                                            <?= htmlspecialchars($pago['estado_pag']) ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($pago['transaccion_id'] ?? 'N/A') ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-danger">
                Tipo de reporte no reconocido: <?= htmlspecialchars($tipo) ?>
            </div>
        <?php endif; ?>

    <!-- Navegación secundaria -->
    <nav class="mt-4">
        <ul class="nav nav-pills justify-content-center gap-2">
            <li class="nav-item">
                <a href="/floraltech/controllers/ReportesPagoController.php?tipo=proyecciones" class="nav-link">
                    <i class="bi bi-graph-up"></i> Proyecciones
                </a>
            </li>
            <li class="nav-item">
                <a href="/floraltech/controllers/ReportesPagoController.php?tipo=auditoria" class="nav-link">
                    <i class="bi bi-search"></i> Auditoría
                </a>
            </li>
            <li class="nav-item">
                <a href="/floraltech/controllers/ReportesPagoController.php?tipo=detalles" class="nav-link">
                    <i class="bi bi-clipboard-data"></i> Detalles
                </a>
            </li>
            <li class="nav-item">
                <a href="/floraltech/controllers/ReportesPagoController.php?tipo=dashboard" class="nav-link">
                    <i class="bi bi-house"></i> Dashboard
                </a>
            </li>
        </ul>
    </nav>
</div>

<!-- Incluir Chart.js desde CDN para mejor compatibilidad -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Rango de fechas rápido
function setQuickDate(tipo) {
    const hoy = new Date();
    let inicio = '';
    let fin = '';
    if (tipo === 'hoy') {
        inicio = fin = hoy.toISOString().slice(0,10);
    } else if (tipo === '7dias') {
        const hace7 = new Date(hoy.getTime() - 6*24*60*60*1000);
        inicio = hace7.toISOString().slice(0,10);
        fin = hoy.toISOString().slice(0,10);
    } else if (tipo === 'mes') {
        inicio = new Date(hoy.getFullYear(), hoy.getMonth(), 1).toISOString().slice(0,10);
        fin = new Date(hoy.getFullYear(), hoy.getMonth()+1, 0).toISOString().slice(0,10);
    } else if (tipo === 'mespasado') {
        const mesPasado = new Date(hoy.getFullYear(), hoy.getMonth()-1, 1);
        inicio = mesPasado.toISOString().slice(0,10);
        fin = new Date(hoy.getFullYear(), hoy.getMonth(), 0).toISOString().slice(0,10);
    }
    document.querySelector('input[name="fecha_inicio"]').value = inicio;
    document.querySelector('input[name="fecha_fin"]').value = fin;
}

// Toggle modo demo
function toggleDemo() {
    const url = new URL(window.location.href);
    url.searchParams.set('demo', url.searchParams.get('demo') === '1' ? '0' : '1');
    window.location.href = url.toString();
}

// Exportar CSV/PDF (solo CSV simulado)
function exportar(tipo) {
    if (tipo === 'csv') {
        let csv = '';
        const rows = document.querySelectorAll('table tbody tr');
        rows.forEach(row => {
            let cols = Array.from(row.querySelectorAll('td')).map(td => td.innerText.replace(/\$/g, ''));
            csv += cols.join(',') + '\n';
        });
        const blob = new Blob([csv], {type: 'text/csv'});
        const a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = 'reporte.csv';
        a.click();
    } else if (tipo === 'pdf') {
        alert('Exportar a PDF no implementado en demo.');
    }
}
</script>
<script>
    <?php if ($tipo === 'ganancias' && isset($datos['metodos_pago'])): ?>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('metodosPagoChart').getContext('2d');
        const chartData = {
            labels: <?= json_encode(array_column($datos['metodos_pago'], 'metodo_pago')) ?>,
            datasets: [{
                data: <?= json_encode(array_column($datos['metodos_pago'], 'ingresos_totales')) ?>,
                backgroundColor: [
                    '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'
                ],
                hoverBackgroundColor: [
                    '#2e59d9', '#17a673', '#2c9faf', '#dda20a', '#be2617'
                ],
                hoverBorderColor: "rgba(234, 236, 244, 1)",
            }]
        };
        
        new Chart(ctx, {
            type: 'doughnut',
            data: chartData,
            options: {
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = Math.round((value / total) * 100);
                                return `${label}: $${value.toLocaleString()} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    });
    <?php endif; ?>
</script>

<?php 
// Incluir footer con direccionamiento correcto
require_once __DIR__ . '/../partials/footer.php';
?>