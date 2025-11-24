<?php
// Vista de reportes: asume variables provistas por ReportesController::obtenerContexto()
?>
<main>
    <div class="container my-4">
        <div class="p-4 rounded-4 shadow-sm mb-4" style="background: linear-gradient(120deg,#0d6efd 0%,#5b21b6 60%,#1e1b4b 100%); color:#fff;">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <p class="mb-1 text-white-50 small">Indicadores rápidos y tablas detalladas</p>
                    <h2 class="fw-bold mb-0">Reportes</h2>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-12 col-md-6">
                <div class="card h-100 shadow-sm border-0 rounded-4" style="background: linear-gradient(135deg,#e0f2fe,#eff6ff);" data-bs-toggle="modal" data-bs-target="#tablaModal">
                    <div class="card-body text-center">
                        <i class="bi bi-bar-chart-line h1 text-primary"></i>
                        <h6 class="fw-bold mt-2">Ventas</h6>
                        <div class="small">Total: <span class="fw-bold">$<?= number_format($datos['ventas']['total'] ?? 0, 2) ?></span></div>
                        <div class="small">Pedidos: <span class="fw-bold"><?= $datos['ventas']['pedidos'] ?? 0 ?></span></div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="card h-100 shadow-sm border-0 rounded-4" style="background: linear-gradient(135deg,#ecfdf3,#f0fdf4);" data-bs-toggle="modal" data-bs-target="#tablaModalFlores">
                    <div class="card-body text-center">
                        <i class="bi bi-box-seam h1 text-success"></i>
                        <h6 class="fw-bold mt-2">Inventario</h6>
                        <div class="small">Stock: <span class="fw-bold"><?= $datos['inventario']['stock_total'] ?? 0 ?></span></div>
                        <div class="small">Productos: <span class="fw-bold"><?= $datos['inventario']['productos'] ?? 0 ?></span></div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="card h-100 shadow-sm border-0 rounded-4" style="background: linear-gradient(135deg,#fef9c3,#fffbeb);" data-bs-toggle="modal" data-bs-target="#modalUsuario">
                    <div class="card-body text-center">
                        <i class="bi bi-person-badge h1 text-warning"></i>
                        <h6 class="fw-bold mt-2">Cuentas</h6>
                        <div class="small">Total Usuarios: <span class="fw-bold"><?= $totalUsuarios ?? 0 ?></span></div>
                        <div class="small">Activos: <span class="fw-bold"><?= $datos['usuarios']['activos'] ?? 0 ?></span></div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="card h-100 shadow-sm border-0 rounded-4" style="background: linear-gradient(135deg,#f4f4f5,#e4e4e7);" data-bs-toggle="modal" data-bs-target="#tablaModalPagos">
                    <div class="card-body text-center">
                        <i class="bi bi-cash-stack h1 text-secondary"></i>
                        <h6 class="fw-bold mt-2">Pagos</h6>
                        <div class="small">Realizados: <span class="fw-bold">$<?= number_format($datos['pagos']['realizados'] ?? 0, 2) ?></span></div>
                        <div class="small">Pendientes: <span class="fw-bold">$<?= number_format($datos['pagos']['pendientes'] ?? 0, 2) ?></span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Ventas -->
    <div class="modal fade" id="tablaModal" tabindex="-1" aria-labelledby="tablaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header text-white" style="background: linear-gradient(90deg, #6a5af9 0%, #7c3aed 100%);" >
                    <h5 class="modal-title" id="tablaModalLabel">Lista de Ventas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div class="container my-4">
                        <div class="row g-3">
                            <form class="row mb-3 g-2 flex-wrap" onsubmit="return false;">
                                <div class="col-12 col-md-3">
                                    <input type="date" class="form-control" name="fecha_inicio" id="modal_fecha_inicio">
                                </div>
                                <div class="col-12 col-md-3">
                                    <input type="date" class="form-control" name="fecha_fin" id="modal_fecha_fin">
                                </div>
                                <div class="col-12 col-md-3">
                                    <select class="form-select" name="estado" id="modal_estado">
                                        <option value="">Todos</option>
                                        <option value="Pendiente">Pendiente</option>
                                        <option value="Completado">Completado</option>
                                        <option value="Cancelado">Cancelado</option>
                                    </select>
                                </div>
                                <div class="col-12 col-md-3">
                                    <button type="button" class="btn w-100 text-white" id="btnFiltrarModal" style="background: linear-gradient(90deg, #6a5af9 0%, #7c3aed 100%);">Filtrar</button>
                                </div>
                            </form>
                            <div class="col-12 col-md-6">
                                <form id="formPdfPedidos" action="controllers/repopdf.php" method="POST">
                                    <input type="hidden" name="ids" id="pdf_ids">
                                    <button type="submit" class="btn btn-primary w-100" style="background: linear-gradient(90deg, #6a5af9 0%, #7c3aed 100%);">
                                        <i class="bi bi-file-earmark-pdf"></i> Generar PDF
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle" id="tablaPedidosModal">
                            <thead class="table-light">
                                <tr>
                                    <th><input type="checkbox" id="selectAll" title="Seleccionar todo"></th>
                                    <th>ID</th>
                                    <th>Número Pedido</th>
                                    <th>Fecha Pedido</th>
                                    <th>Monto Total</th>
                                    <th>Cliente</th>
                                    <th>Estado</th>
                                    <th>Empleado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($modalPedidos)): ?>
                                    <?php foreach ($modalPedidos as $pedido): ?>
                                        <tr>
                                            <td><input type="checkbox" class="select-row" value="<?=htmlspecialchars($pedido['idped'])?>"></td>
                                            <td><?= htmlspecialchars($pedido['idped']) ?></td>
                                            <td><?= htmlspecialchars($pedido['numped']) ?></td>
                                            <td><?= date('d/m/Y', strtotime($pedido['fecha_pedido'])) ?></td>
                                            <td>$<?= number_format($pedido['monto_total'], 2) ?></td>
                                            <td><?= htmlspecialchars($pedido['cli_idcli']) ?></td>
                                            <td><?= htmlspecialchars($pedido['estado']) ?></td>
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
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="background: linear-gradient(90deg, #6a5af9 0%, #7c3aed 100%);">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Usuarios -->
    <div class="modal fade" id="modalUsuario" tabindex="-1" aria-labelledby="tablaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header text-white" style="background: linear-gradient(90deg, #6a5af9 0%, #7c3aed 100%);" >
                    <h5 class="modal-title" id="tablaModalLabel">Lista de Usuarios</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div class="container my-4">
                        <div class="row g-3">
                            <form class="row mb-3 g-2 flex-wrap" onsubmit="return false;">
                                <div class="col-12 col-md-6">
                                    <select class="form-select" name="estado" id="modal_estado_usuarios">
                                        <option value="">Todos</option>
                                        <option value="Administrador">Administrador</option>
                                        <option value="Vendedor">Vendedor</option>
                                        <option value="Inventario">Inventario</option>
                                        <option value="Repartidor">Repartidor</option>
                                        <option value="Cliente">Cliente</option>
                                    </select>
                                </div>
                                <div class="col-12 col-md-6">
                                    <button type="button" class="btn w-100 text-white" id="btnFiltrarModalUsuarios" style="background: linear-gradient(90deg, #6a5af9 0%, #7c3aed 100%);">Filtrar</button>
                                </div>
                            </form>
                            <div class="col-12 col-md-6">
                                <form id="formPdfUsuarios" action="controllers/repopdf.php" method="POST">
                                    <input type="hidden" name="accion" value="usuarios_pdf">
                                    <input type="hidden" name="tipo" id="tipoSeleccionado">
                                    <input type="hidden" name="ids" id="pdf_ids_usuarios">
                                    <button type="submit" class="btn btn-primary w-100" style="background: linear-gradient(90deg, #6a5af9 0%, #7c3aed 100%);">
                                        <i class="bi bi-file-earmark-pdf"></i> Generar PDF
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle" id="tablaUsuariosModal">
                            <thead class="table-light">
                                <tr>
                                    <th><input type="checkbox" id="selectAllUsuarios" title="Seleccionar todo"></th>
                                    <th>ID</th>
                                    <th>Usuario</th>
                                    <th>Nombre Completo</th>
                                    <th>Teléfono</th>
                                    <th>Correo</th>
                                    <th>Estado</th>
                                    <th>Tipo Usuario</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($modalUsuarios)): ?>
                                    <?php foreach ($modalUsuarios as $u): ?>
                                        <tr>
                                            <td><input type="checkbox" class="select-row" value="<?=htmlspecialchars($u['idusu'])?>"></td>
                                            <td><?= htmlspecialchars($u['idusu']) ?></td>
                                            <td><?= htmlspecialchars($u['username']) ?></td>
                                            <td><?= htmlspecialchars($u['nombre_completo']) ?></td>
                                            <td><?= htmlspecialchars($u['telefono']) ?></td>
                                            <td><?= htmlspecialchars($u['email']) ?></td>
                                            <td><?= ($u['activo'] ? 'Sí' : 'No') ?></td>
                                            <td><?= htmlspecialchars($u['tipo_usuario']) ?></td>
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
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="background: linear-gradient(90deg, #6a5af9 0%, #7c3aed 100%);">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Inventario -->
    <div class="modal fade" id="tablaModalFlores" tabindex="-1" aria-labelledby="tablaModalFloresLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header text-white" style="background: linear-gradient(90deg, #6a5af9 0%, #7c3aed 100%);">
                    <h5 class="modal-title" id="tablaModalFloresLabel">Inventario de Flores</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div class="container my-4">
                        <div class="row g-3">
                            <form class="row mb-3 g-2 flex-wrap" onsubmit="return false;">
                                <div class="col-12 col-md-4">
                                    <select class="form-select" id="modal_estado_flores" name="estado_flor">
                                        <option value="">Todas</option>
                                        <option value="disponible">Disponible</option>
                                        <option value="agotado">Agotado</option>
                                        <option value="no disponible">No disponible</option>
                                    </select>
                                </div>
                                <div class="col-12 col-md-4">
                                    <button type="button" class="btn w-100 text-white" id="btnFiltrarModalFlores"
                                        style="background: linear-gradient(90deg, #6a5af9 0%, #7c3aed 100%);">
                                        Filtrar
                                    </button>
                                </div>
                            </form>

                            <div class="col-12 col-md-6">
                                <form id="formPdfFlores" action="controllers/repopdf.php" method="POST">
                                    <input type="hidden" name="accion" value="flores_pdf">
                                    <input type="hidden" name="ids" id="pdf_ids_flores">
                                    <button type="submit" class="btn btn-primary w-100"
                                        style="background: linear-gradient(90deg, #6a5af9 0%, #7c3aed 100%);">
                                        <i class="bi bi-file-earmark-pdf"></i> Generar PDF
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
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
                                <?php if (!empty($modalInventario)): ?>
                                    <?php foreach ($modalInventario as $f): ?>
                                        <tr>
                                            <td><input type="checkbox" class="select-row" value="<?=htmlspecialchars($f['idtflor'])?>"></td>
                                            <td><?= htmlspecialchars($f['idtflor']) ?></td>
                                            <td><?= htmlspecialchars($f['producto']) ?></td>
                                            <td><?= htmlspecialchars($f['naturaleza']) ?></td>
                                            <td><?= htmlspecialchars($f['color']) ?></td>
                                            <td><?= htmlspecialchars($f['stock']) ?></td>
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
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"style="background: linear-gradient(90deg, #6a5af9 0%, #7c3aed 100%);">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Pagos -->
    <div class="modal fade" id="tablaModalPagos" tabindex="-1" aria-labelledby="tablaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header text-white" style="background: linear-gradient(90deg, #6a5af9 0%, #7c3aed 100%);" >
                    <h5 class="modal-title" id="tablaModalLabel">Lista de Pagos</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div class="container my-4">
                        <div class="row g-3">
                            <form class="row mb-3 g-2 flex-wrap" onsubmit="return false;">
                                <div class="col-12 col-md-3">
                                    <input type="date" class="form-control" name="fecha_inicio" id="modal_fecha_inicio_pagos">
                                </div>
                                <div class="col-12 col-md-3">
                                    <input type="date" class="form-control" name="fecha_fin" id="modal_fecha_fin_pagos">
                                </div>
                                <div class="col-12 col-md-3">
                                    <select class="form-select" name="estado" id="modal_estado_pagos">
                                        <option value="">Todos</option>
                                        <option value="Pendiente">Pendiente</option>
                                        <option value="Completado">Completado</option>
                                        <option value="Reembolsado">Reembolsado</option>
                                    </select>
                                </div>
                                <div class="col-12 col-md-3">
                                    <button type="button" class="btn w-100 text-white" id="btnFiltrarPagos" style="background: linear-gradient(90deg, #6a5af9 0%, #7c3aed 100%);">Filtrar</button>
                                </div>
                            </form>
                            <div class="col-12 col-md-6">
                                <form id="formPdfPagos" action="controllers/repopdf.php" method="POST">
                                    <input type="hidden" name="accion" value="pagos_pdf">
                                    <input type="hidden" name="ids" id="pdf_ids_pagos">
                                    <button type="submit" class="btn btn-primary w-100" 
                                        style="background: linear-gradient(90deg, #6a5af9 0%, #7c3aed 100%);">
                                        <i class="bi bi-file-earmark-pdf"></i> Generar PDF
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle" id="tablaPagosModal">
                            <thead class="table-light">
                                <tr>
                                    <th><input type="checkbox" id="selectAllPagos" title="Seleccionar todo"></th>
                                    <th>ID</th>
                                    <th>Fecha Pago</th>
                                    <th>Método</th>
                                    <th>Estado</th>
                                    <th>Monto</th>
                                    <th>Transacción</th>
                                    <th>Comprobante</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($modalPagos)): ?>
                                    <?php foreach ($modalPagos as $pago): ?>
                                        <tr>
                                            <td><input type="checkbox" class="select-row" value="<?= htmlspecialchars($pago['idpago']) ?>"></td>
                                            <td><?= htmlspecialchars($pago['idpago']) ?></td>
                                            <td><?= date('d/m/Y', strtotime($pago['fecha_pago'])) ?></td>
                                            <td><?= htmlspecialchars($pago['metodo_pago']) ?></td>
                                            <td><?= htmlspecialchars($pago['estado_pag']) ?></td>
                                            <td>$<?= number_format($pago['monto'], 2) ?></td>
                                            <td><?= htmlspecialchars($pago['transaccion_id']) ?></td>
                                            <td>
                                                <?php if (!empty($pago['comprobante_transferencia'])): ?>
                                                    <a href="<?= htmlspecialchars($pago['comprobante_transferencia']) ?>" target="_blank">Ver</a>
                                                <?php else: ?>
                                                    <span class="text-muted">Sin comprobante</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center text-warning">No hay pagos registrados.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="background: linear-gradient(90deg, #6a5af9 0%, #7c3aed 100%);">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="assets/repo.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</main>
