<?php
// Vista de auditor├¡a: espera variables preparadas por AdminAuditoriaController::obtenerContexto()
$resumenAuditoria = $resumenAuditoria ?? ['acciones' => 0, 'usuarios' => 0, 'incidencias' => 0, 'ultima' => null];
$accionesPorTipo = $accionesPorTipo ?? [];
$actividadSemanal = $actividadSemanal ?? [];
$usuariosTotales = $usuariosTotales ?? 0;
$usuariosActivos = $usuariosActivos ?? 0;
$usuariosActivosHoy = $usuariosActivosHoy ?? 0;
$usuariosRecientes = $usuariosRecientes ?? [];
$productosActivos = $productosActivos ?? 0;
$productosActivosDetalle = $productosActivosDetalle ?? [];
$pagosMes = $pagosMes ?? ['monto' => 0, 'conteo' => 0];
$proyeccionActiva = $proyeccionActiva ?? ['titulo' => 'Sin proyecci├│n', 'monto_objetivo' => 0, 'fecha_inicio' => date('Y-m-01'), 'fecha_fin' => date('Y-m-t'), 'notas' => ''];
$avanceProy = $avanceProy ?? ['monto' => 0, 'conteo' => 0];
$avanceActual = $avanceActual ?? 0;
$porcentajeAvance = $porcentajeAvance ?? 0;
$fechaLimiteDate = $fechaLimiteDate ?? strtotime($proyeccionActiva['fecha_fin']);
$vencido = $vencido ?? false;
$cumplido = $cumplido ?? false;
$pagosPorFecha = $pagosPorFecha ?? [];
$listadoAuditoria = $listadoAuditoria ?? [];
$fechaFiltroPagos = $fechaFiltroPagos ?? date('Y-m-d');
?>
<link rel="stylesheet" href="assets/admin-unificado.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<main class="w-100">
    <div class="container-fluid px-3 px-md-4 py-4 w-100" style="overflow-x:hidden;">
        <div class="p-4 mb-4 rounded-4 shadow-sm" style="background: linear-gradient(120deg, #0d6efd 0%, #5b21b6 60%, #1e1b4b 100%); color:#fff;">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <p class="mb-1 text-white-50 small">Visibilidad integral de actividad, usuarios y metas</p>
                    <h1 class="fw-bold mb-0">Panel de auditoria</h1>
                </div>
                <div class="d-flex gap-3 flex-wrap">
                    <div class="px-3 py-2 rounded-3 bg-white bg-opacity-10 border border-white border-opacity-25">
                        <div class="small text-white-50">Pagos del mes</div>
                        <div class="h5 mb-0">$<?= number_format($pagosMes['monto'] ?? 0, 2) ?> <?= intval($pagosMes['conteo'] ?? 0) ?></div>
                    </div>
                    <div class="px-3 py-2 rounded-3 bg-white bg-opacity-10 border border-white border-opacity-25">
                        <div class="small text-white-50">Usuarios activos hoy</div>
                        <div class="h5 mb-0"><?= $usuariosActivosHoy ?> / <?= $usuariosTotales ?></div>
                    </div>
                    <div class="px-3 py-2 rounded-3 bg-white bg-opacity-10 border border-white border-opacity-25">
                        <div class="small text-white-50">Productos activos</div>
                        <div class="h5 mb-0"><?= number_format($productosActivos) ?></div>
                    </div>
                </div>
            </div>
        </div>

        <ul class="nav nav-pills justify-content-center gap-2 mb-4 flex-column flex-md-row">
            <li class="nav-item">
                <button class="nav-link active w-100 w-md-auto" data-bs-toggle="tab" data-bs-target="#tab-resumen">Resumen</button>
            </li>
            <li class="nav-item">
                <button class="nav-link w-100 w-md-auto" data-bs-toggle="tab" data-bs-target="#tab-actividad">Actividad</button>
            </li>
            <li class="nav-item">
                <button class="nav-link w-100 w-md-auto" data-bs-toggle="tab" data-bs-target="#tab-proyecciones">Proyecciones</button>
            </li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade show active" id="tab-resumen">
                <div class="row g-3 mb-4">
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="card shadow-sm border-0 rounded-4 h-100" style="background: linear-gradient(135deg,#e0e7ff,#eef2ff);">
                            <div class="card-body">
                                <p class="fw-bold text-primary mb-1 small text-uppercase">Acciones totales</p>
                                <div class="d-flex align-items-baseline gap-2">
                                    <span class="display-6 fw-bold text-primary"><?= number_format($resumenAuditoria['acciones']) ?></span>
                                    <span class="text-muted">movimientos</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="card shadow-sm border-0 rounded-4 h-100" style="background: linear-gradient(135deg,#dcfce7,#ecfdf3);">
                            <div class="card-body">
                                <p class="fw-bold text-success mb-1 small text-uppercase">Usuarios involucrados</p>
                                <div class="d-flex align-items-baseline gap-2">
                                    <span class="display-6 fw-bold text-success"><?= number_format($resumenAuditoria['usuarios']) ?></span>
                                    <span class="text-muted">roles en accion</span>
                                </div>
                                <p class="mb-0 small text-muted"><?= $usuariosActivos ?> activos de <?= $usuariosTotales ?> registrados</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="card shadow-sm border-0 rounded-4 h-100" style="background: linear-gradient(135deg,#fef9c3,#fffbeb);">
                            <div class="card-body">
                                <p class="fw-bold text-warning mb-1 small text-uppercase">Incidencias</p>
                                <div class="d-flex align-items-baseline gap-2">
                                    <span class="display-6 fw-bold text-warning"><?= number_format($resumenAuditoria['incidencias']) ?></span>
                                    <span class="text-muted">rechazos/cancelaciones</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="card shadow-sm border-0 rounded-4 h-100" style="background: linear-gradient(135deg,#ffe2e5,#fff1f2);">
                            <div class="card-body">
                                <p class="fw-bold text-danger mb-1 small text-uppercase">Ultima accion</p>
                                <h5 class="fw-bold text-danger mb-0">
                                    <?= $resumenAuditoria['ultima'] ? date('d/m/Y H:i', strtotime($resumenAuditoria['ultima'])) : 'Sin registros' ?>
                                </h5>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-12 col-xl-6">
                        <div class="card shadow-sm h-100 rounded-4 border-0">
                            <div class="card-body d-flex flex-column justify-content-between">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div>
                                        <h6 class="fw-bold text-muted mb-0">Usuarios</h6>
                                        <p class="mb-0 text-secondary small">Activos vs registrados</p>
                                    </div>
                                    <span class="badge bg-primary-subtle text-primary"><?= $usuariosActivos ?>/<?= $usuariosTotales ?></span>
                                </div>
                                <div class="progress" style="height: 10px;">
                                    <div class="progress-bar bg-primary" role="progressbar" style="width: <?= $usuariosTotales > 0 ? min(100, round(($usuariosActivos / max(1,$usuariosTotales))*100, 1)) : 0 ?>%;"></div>
                                </div>
                                <p class="mt-2 mb-0 small text-muted">Activos hoy: <?= $usuariosActivosHoy ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-xl-6">
                        <div class="card shadow-sm h-100 rounded-4 border-0">
                            <div class="card-body d-flex flex-column justify-content-between">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div>
                                        <h6 class="fw-bold text-muted mb-0">Productos activos</h6>
                                        <p class="mb-0 text-secondary small">Inventario disponible</p>
                                    </div>
                                    <span class="badge bg-success-subtle text-success"><?= number_format($productosActivos) ?></span>
                                </div>
                                <p class="mb-0 small text-muted">Productos con stock mayor a cero</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mt-3">
                    <div class="col-12 col-xl-6">
                        <div class="card shadow-sm rounded-4 border-0 h-100">
                            <div class="card-header bg-white fw-bold d-flex justify-content-between align-items-center">
                                <span>Usuarios activos recientes</span>
                                <span class="badge bg-primary-subtle text-primary"><?= count($usuariosRecientes) ?></span>
                            </div>
                            <div class="card-body table-responsive">
                                <table class="table align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Nombre</th>
                                            <th>Usuario</th>
                                            <th>Rol</th>
                                            <th>├Ültimo acceso</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($usuariosRecientes)): ?>
                                            <?php foreach ($usuariosRecientes as $u): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($u['nombre_completo'] ?? 'Sin nombre') ?></td>
                                                    <td><?= htmlspecialchars($u['username'] ?? '-') ?></td>
                                                    <td><span class="badge bg-primary-subtle text-primary"><?= htmlspecialchars($u['rol'] ?? 'N/D') ?></span></td>
                                                    <td><?= !empty($u['fecha_ultimo_acceso']) ? date('d/m H:i', strtotime($u['fecha_ultimo_acceso'])) : 'N/D' ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr><td colspan="4" class="text-center text-muted">No hay usuarios activos.</td></tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-xl-6">
                        <div class="card shadow-sm rounded-4 border-0 h-100">
                            <div class="card-header bg-white fw-bold d-flex justify-content-between align-items-center">
                                <span>Productos activos</span>
                                <span class="badge bg-success-subtle text-success"><?= count($productosActivosDetalle) ?></span>
                            </div>
                            <div class="card-body table-responsive">
                                <table class="table align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Producto</th>
                                            <th>Stock</th>
                                            <th>Naturaleza</th>
                                            <th>Precio</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($productosActivosDetalle)): ?>
                                            <?php foreach ($productosActivosDetalle as $p): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($p['nombre'] ?? 'Sin nombre') ?></td>
                                                    <td><span class="badge bg-success-subtle text-success"><?= number_format($p['stock'] ?? 0) ?></span></td>
                                                    <td><?= htmlspecialchars($p['naturaleza'] ?? '-') ?></td>
                                                    <td>$<?= number_format($p['precio'] ?? 0, 2) ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr><td colspan="4" class="text-center text-muted">No hay productos con stock.</td></tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="tab-actividad">
                <div class="row g-4">
                    <div class="col-12 col-lg-6">
                        <div class="card shadow-sm h-100 rounded-4">
                            <div class="card-header bg-light fw-bold">
                                Acciones por estado
                            </div>
                            <div class="card-body">
                                <?php if (!empty($accionesPorTipo)): ?>
                                    <canvas id="accionesChart" height="320"></canvas>
                                <?php else: ?>
                                    <p class="text-muted text-center mb-0">No hay movimientos registrados.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-lg-6">
                        <div class="card shadow-sm h-100 rounded-4">
                            <div class="card-header bg-light fw-bold">
                                Actividad semanal
                            </div>
                            <div class="card-body">
                                <canvas id="actividadChart" height="320"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card shadow-sm rounded-4">
                            <div class="card-header bg-light fw-bold">
                                Registro detallado de eventos
                            </div>
                            <div class="card-body table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Responsable</th>
                                            <th>Estado</th>
                                            <th>Fecha</th>
                                            <th>M├®todo</th>
                                            <th>Detalle</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($listadoAuditoria)): ?>
                                            <?php $i = 1; foreach ($listadoAuditoria as $row): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars(sprintf('%03d', $i++)) ?></td>
                                                    <td><?= htmlspecialchars($row['usuario']) ?></td>
                                                    <td><?= htmlspecialchars($row['tipo']) ?></td>
                                                    <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($row['fecha']))) ?></td>
                                                    <td><?= htmlspecialchars($row['metodo']) ?></td>
                                                    <td>
                                                        <?php
                                                            $pedido = $row['numped'] ?? 'S/N';
                                                            $cliente = $row['cliente'] ?? 'Sin cliente';
                                                            $detalle = sprintf('Pedido %s ÔÇó Cliente: %s ÔÇó $%s',
                                                                $pedido,
                                                                $cliente,
                                                                number_format($row['monto'], 2)
                                                            );
                                                        ?>
                                                        <?= htmlspecialchars($detalle) ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="6" class="text-center text-muted">No se encontraron registros para mostrar.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="tab-proyecciones">
                <div class="row g-4">
                    <div class="col-12 col-lg-6">
                        <div class="card shadow-sm h-100 rounded-4">
                            <div class="card-header bg-light fw-bold">Objetivo de pagos por rango</div>
                            <div class="card-body">
                                <form method="POST" class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label">Titulo</label>
                                        <input type="text" class="form-control" name="objetivo_titulo" placeholder="Ej: Meta Q4" value="<?= htmlspecialchars($proyeccionActiva['titulo'] ?? 'Meta de pagos') ?>">
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="form-label">Fecha inicio</label>
                                        <input type="date" class="form-control" name="objetivo_fecha_inicio" value="<?= htmlspecialchars($proyeccionActiva['fecha_inicio'] ?? date('Y-m-01')) ?>" required>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="form-label">Fecha fin</label>
                                        <input type="date" class="form-control" name="objetivo_fecha_fin" value="<?= htmlspecialchars($proyeccionActiva['fecha_fin'] ?? date('Y-m-t')) ?>" required>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="form-label">Monto objetivo</label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" step="0.01" min="0" class="form-control" name="objetivo_monto" value="<?= htmlspecialchars($proyeccionActiva['monto_objetivo'] ?? 0) ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Notas</label>
                                        <textarea class="form-control" name="objetivo_notas" rows="2" placeholder="Detalle breve de la meta"><?= htmlspecialchars($proyeccionActiva['notas'] ?? '') ?></textarea>
                                    </div>
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Guardar objetivo</button>
                                    </div>
                                </form>
                                <hr>
                                <p class="mb-1 text-muted small">Pagos completados en el rango</p>
                                <h5 class="fw-bold">$<?= number_format($avanceActual, 2) ?> <span class="text-muted fs-6">(<?= intval($avanceProy['conteo']) ?> transacciones)</span></h5>
                                <div class="progress" style="height: 14px;">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: <?= $porcentajeAvance ?>%;"></div>
                                </div>
                                <div class="d-flex justify-content-between mt-2 small text-muted">
                                    <span>Meta: $<?= number_format($proyeccionActiva['monto_objetivo'] ?? 0, 2) ?></span>
                                    <span><?= $porcentajeAvance ?>%</span>
                                </div>
                                <div class="mt-3">
                                    <?php if ($cumplido): ?>
                                        <div class="alert alert-success mb-2">Objetivo cumplido antes de la fecha limite.</div>
                                    <?php elseif ($vencido): ?>
                                        <div class="alert alert-danger mb-2">No se cumplio la meta al <?= date('d/m/Y', $fechaLimiteDate) ?>.</div>
                                    <?php else: ?>
                                        <div class="alert alert-info mb-2">Aun en curso. Limite: <?= date('d/m/Y', $fechaLimiteDate) ?>.</div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-6">
                        <div class="card shadow-sm h-100 rounded-4 mb-4">
                            <div class="card-header bg-light fw-bold d-flex justify-content-between align-items-center">
                                <span>Pagos por fecha</span>
                                <form class="d-flex align-items-center gap-2" method="GET">
                                    <input type="hidden" name="ctrl" value="dashboard">
                                    <input type="hidden" name="action" value="admin">
                                    <input type="hidden" name="page" value="auditoria">
                                    <input type="date" class="form-control form-control-sm" name="fecha_pagos" value="<?= htmlspecialchars($fechaFiltroPagos) ?>">
                                    <button class="btn btn-sm btn-outline-primary" type="submit">Filtrar</button>
                                </form>
                            </div>
                            <div class="card-body table-responsive">
                                <table class="table align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>ID</th>
                                            <th>Fecha</th>
                                            <th>Cliente</th>
                                            <th>Pedido</th>
                                            <th>M├®todo</th>
                                            <th class="text-end">Monto</th>
                                            <th>Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($pagosPorFecha)): ?>
                                            <?php foreach ($pagosPorFecha as $pf): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($pf['idpago']) ?></td>
                                                    <td><?= date('d/m H:i', strtotime($pf['fecha_pago'])) ?></td>
                                                    <td><?= htmlspecialchars($pf['cliente']) ?></td>
                                                    <td><?= htmlspecialchars($pf['numped']) ?></td>
                                                    <td><?= htmlspecialchars($pf['metodo_pago']) ?></td>
                                                    <td class="text-end">$<?= number_format($pf['monto'], 2) ?></td>
                                                    <td><span class="badge bg-<?= strtolower($pf['estado_pag']) === 'completado' ? 'success' : (strtolower($pf['estado_pag']) === 'pendiente' ? 'warning text-dark' : 'danger') ?>"><?= htmlspecialchars($pf['estado_pag']) ?></span></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr><td colspan="7" class="text-center text-muted">Sin pagos en la fecha seleccionada.</td></tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script id="auditoria-data" type="application/json">
<?= json_encode([
    'acciones' => $accionesPorTipo,
    'actividad' => [
        'labels' => array_map(function($d) { return date('D', strtotime($d)); }, array_keys($actividadSemanal)),
        'data' => array_values($actividadSemanal)
    ]
], JSON_UNESCAPED_UNICODE) ?>
</script>
<script src="assets/js/auditoria.js"></script>


