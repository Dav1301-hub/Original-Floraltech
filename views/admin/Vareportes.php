
<?php
// Vista de reportes: asume variables provistas por ReportesController::obtenerContexto()
?><main>
    <div class="container my-4">
        <div class="p-4 rounded-4 shadow-sm mb-4" style="background: linear-gradient(120deg,#0d6efd 0%,#5b21b6 60%,#1e1b4b 100%); color:#fff;">            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
               <div>
                    <p class="mb-1 text-white-50 small">Indicadores rapidos y tablas detalladas</p>
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

    <!-- Modal Inventario -->
    <div class="modal fade" id="tablaModalFlores" tabindex="-1" aria-labelledby="tablaModalFloresLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header text-white" style="background: linear-gradient(90deg, #6a5af9 0%, #7c3aed 100%);">
                    <div>
                        <h5 class="modal-title" id="tablaModalFloresLabel">Inventario de flores</h5>
                        <small class="text-white-50">Filtra por estado y exporta stock y valor.</small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div class="container my-3">
                        <div class="row g-2 align-items-end">
                            <div class="col-12 col-md-4">
                                <label class="form-label small mb-1">Estado</label>
                                <select class="form-select" id="modal_estado_flores" name="estado_flor">
                                    <option value="">Todas</option>
                                    <option value="disponible">Disponible</option>
                                    <option value="agotado">Agotado</option>
                                    <option value="no disponible">No disponible</option>
                                </select>
                            </div>
                            <div class="col-12 col-md-4 d-grid">
                                <button type="button" class="btn text-white" id="btnFiltrarModalFlores"
                                    style="background: linear-gradient(90deg, #6a5af9 0%, #7c3aed 100%);">
                                    Filtrar
                                </button>
                            </div>
                        </div>
                        <?php
                            $statsInv = [
                                'items' => count($modalInventario ?? []),
                                'stock' => array_sum(array_column($modalInventario ?? [], 'stock')),
                                'valor' => array_sum(array_column($modalInventario ?? [], 'valor_total'))
                            ];
                        ?>
                        <div class="row g-3 mt-2">
                            <div class="col-12 col-md-3">
                                <div class="border rounded-3 p-3 bg-light">
                                    <div class="small text-muted">Productos</div>
                                    <div class="h6 mb-0"><?= number_format($statsInv['items']) ?></div>
                                </div>
                            </div>
                            <div class="col-12 col-md-3">
                                <div class="border rounded-3 p-3 bg-light">
                                    <div class="small text-muted">Stock total</div>
                                    <div class="h6 mb-0"><?= number_format($statsInv['stock']) ?></div>
                                </div>
                            </div>
                            <div class="col-12 col-md-3">
                                <div class="border rounded-3 p-3 bg-light">
                                    <div class="small text-muted">Valor total</div>
                                    <div class="h6 mb-0">$<?= number_format($statsInv['valor'], 2) ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="row g-3 mt-3">
                            <div class="col-12 col-md-6">
                                <form id="formPdfFlores" action="controllers/repopdf.php" method="POST" class="d-grid">
                                    <input type="hidden" name="accion" value="flores_pdf">
                                    <input type="hidden" name="ids" id="pdf_ids_flores">
                                    <button type="submit" class="btn btn-primary"
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
                                    <th>Precio unitario</th>
                                    <th>Valor total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($modalInventario)): ?>
                                    <?php foreach ($modalInventario as $f): ?>
                                        <?php $estadoFlor = strtolower($f['estado']); ?>
                                        <tr data-estado="<?= $estadoFlor ?>">
                                            <td><input type="checkbox" class="select-row" value="<?= htmlspecialchars($f['idtflor']) ?>"></td>
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

    <!-- Modal Ventas -->
    <div class="modal fade" id="tablaModal" tabindex="-1" aria-labelledby="tablaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header text-white" style="background: linear-gradient(90deg, #6a5af9 0%, #7c3aed 100%);" >
                    <div>
                        <h5 class="modal-title" id="tablaModalLabel">Lista de ventas</h5>
                        <small class="text-white-50">Filtra por rango, estado y revisa entrega solicitada/notas.</small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div class="container my-3">
                        <div class="row g-2 align-items-end">
                            <div class="col-12 col-md-3">
                                <label class="form-label small mb-1">Fecha inicio</label>
                                <input type="date" class="form-control" name="fecha_inicio" id="modal_fecha_inicio">
                            </div>
                            <div class="col-12 col-md-3">
                                <label class="form-label small mb-1">Fecha fin</label>
                                <input type="date" class="form-control" name="fecha_fin" id="modal_fecha_fin">
                            </div>
                            <div class="col-12 col-md-3">
                                <label class="form-label small mb-1">Estado</label>
                                <select class="form-select" name="estado" id="modal_estado">
                                    <option value="">Todos</option>
                                    <option value="Pendiente">Pendiente</option>
                                    <option value="Completado">Completado</option>
                                    <option value="Cancelado">Cancelado</option>
                                </select>
                            </div>
                            <div class="col-12 col-md-3 d-grid">
                                <button type="button" class="btn text-white" id="btnFiltrarModal" style="background: linear-gradient(90deg, #6a5af9 0%, #7c3aed 100%);">
                                    <i class="bi bi-funnel me-1"></i>Filtrar
                                </button>
                            </div>
                        </div>
                        <?php
                            $statsPedidos = [
                                'total' => count($modalPedidos ?? []),
                                'monto' => array_sum(array_column($modalPedidos ?? [], 'monto_total')),
                                'completados' => count(array_filter($modalPedidos ?? [], fn($p) => strtolower($p['estado']) === 'completado')),
                                'pendientes' => count(array_filter($modalPedidos ?? [], fn($p) => strtolower($p['estado']) === 'pendiente'))
                            ];
                        ?>
                        <div class="row g-3 mt-2">
                            <div class="col-12 col-md-3">
                                <div class="border rounded-3 p-3 bg-light">
                                    <div class="small text-muted">Pedidos</div>
                                    <div class="h6 mb-0"><?= number_format($statsPedidos['total']) ?></div>
                                </div>
                            </div>
                            <div class="col-12 col-md-3">
                                <div class="border rounded-3 p-3 bg-light">
                                    <div class="small text-muted">Monto total</div>
                                    <div class="h6 mb-0">$<?= number_format($statsPedidos['monto'], 2) ?></div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="border rounded-3 p-3 bg-light">
                                    <div class="small text-muted">Completados</div>
                                    <div class="h6 mb-0 text-success"><?= number_format($statsPedidos['completados']) ?></div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="border rounded-3 p-3 bg-light">
                                    <div class="small text-muted">Pendientes</div>
                                    <div class="h6 mb-0 text-warning"><?= number_format($statsPedidos['pendientes']) ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="row g-3 mt-3">
                            <div class="col-12 col-md-6">
                                <form id="formPdfPedidos" action="controllers/repopdf.php" method="POST" class="d-grid">
                                    <input type="hidden" name="ids" id="pdf_ids">
                                    <button type="submit" class="btn btn-primary" style="background: linear-gradient(90deg, #6a5af9 0%, #7c3aed 100%);">
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
                                    <th>Numero</th>
                                    <th>Fecha pedido</th>
                                    <th>Entrega solicitada</th>
                                    <th>Monto total</th>
                                    <th>Cliente</th>
                                    <th>Estado</th>
                                    <th>Empleado</th>
                                    <th>Notas</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($modalPedidos)): ?>
                                    <?php foreach ($modalPedidos as $pedido): ?>
                                        <?php
                                            $estadoLower = strtolower($pedido['estado']);
                                            $fechaPedidoIso = date('Y-m-d', strtotime($pedido['fecha_pedido']));
                                        ?>
                                        <tr data-fecha="<?= $fechaPedidoIso ?>" data-estado="<?= $estadoLower ?>">
                                            <td><input type="checkbox" class="select-row" value="<?= htmlspecialchars($pedido['idped']) ?>"></td>
                                            <td><?= htmlspecialchars($pedido['idped']) ?></td>
                                            <td><?= htmlspecialchars($pedido['numped']) ?></td>
                                            <td><?= date('d/m/Y', strtotime($pedido['fecha_pedido'])) ?></td>
                                            <td><?= !empty($pedido['fecha_entrega_solicitada']) ? date('d/m/Y', strtotime($pedido['fecha_entrega_solicitada'])) : 'Sin fecha' ?></td>
                                            <td>$<?= number_format($pedido['monto_total'], 2) ?></td>
                                            <td><?= htmlspecialchars($pedido['cli_idcli']) ?></td>
                                            <td><span class="badge bg-<?= $estadoLower === 'completado' ? 'success' : ($estadoLower === 'pendiente' ? 'warning text-dark' : 'secondary') ?>"><?= htmlspecialchars($pedido['estado']) ?></span></td>
                                            <td><?= htmlspecialchars($pedido['empleado_id']) ?></td>
                                            <td class="text-muted small"><?= htmlspecialchars($pedido['notas'] ?? '') ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="10" class="text-center text-warning">No hay pedidos registrados.</td>
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
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header text-white" style="background: linear-gradient(90deg, #6a5af9 0%, #7c3aed 100%);" >
                    <div>
                        <h5 class="modal-title" id="tablaModalLabel">Lista de usuarios</h5>
                        <small class="text-white-50">Filtra por rol y exporta solo los seleccionados.</small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div class="container my-3">
                        <div class="row g-2 align-items-end">
                            <div class="col-12 col-md-6">
                                <label class="form-label small mb-1">Tipo usuario</label>
                                <select class="form-select" name="estado" id="modal_estado_usuarios">
                                    <option value="">Todos</option>
                                    <option value="Administrador">Administrador</option>
                                    <option value="Vendedor">Vendedor</option>
                                    <option value="Inventario">Inventario</option>
                                    <option value="Repartidor">Repartidor</option>
                                    <option value="Cliente">Cliente</option>
                                </select>
                            </div>
                            <div class="col-12 col-md-6 d-grid">
                                <button type="button" class="btn text-white" id="btnFiltrarModalUsuarios" style="background: linear-gradient(90deg, #6a5af9 0%, #7c3aed 100%);">Filtrar</button>
                            </div>
                        </div>
                        <?php
                            $statsUsuarios = [
                                'total' => count($modalUsuarios ?? []),
                                'activos' => count(array_filter($modalUsuarios ?? [], fn($u) => (int)$u['activo'] === 1))
                            ];
                        ?>
                        <div class="row g-3 mt-2">
                            <div class="col-6 col-md-3">
                                <div class="border rounded-3 p-3 bg-light">
                                    <div class="small text-muted">Usuarios</div>
                                    <div class="h6 mb-0"><?= number_format($statsUsuarios['total']) ?></div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="border rounded-3 p-3 bg-light">
                                    <div class="small text-muted">Activos</div>
                                    <div class="h6 mb-0 text-success"><?= number_format($statsUsuarios['activos']) ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="row g-3 mt-3">
                            <div class="col-12 col-md-6">
                                <form id="formPdfUsuarios" action="controllers/repopdf.php" method="POST" class="d-grid">
                                    <input type="hidden" name="accion" value="usuarios_pdf">
                                    <input type="hidden" name="tipo" id="tipoSeleccionado">
                                    <input type="hidden" name="ids" id="pdf_ids_usuarios">
                                    <button type="submit" class="btn btn-primary" style="background: linear-gradient(90deg, #6a5af9 0%, #7c3aed 100%);">
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
                                    <th>Nombre completo</th>
                                    <th>Telefono</th>
                                    <th>Correo</th>
                                    <th>Estado</th>
                                    <th>Tipo usuario</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($modalUsuarios)): ?>
                                    <?php foreach ($modalUsuarios as $u): ?>
                                        <?php $tipoLower = strtolower($u['tipo_usuario']); ?>
                                        <tr data-tipo="<?= $tipoLower ?>">
                                            <td><input type="checkbox" class="select-row" value="<?= htmlspecialchars($u['idusu']) ?>"></td>
                                            <td><?= htmlspecialchars($u['idusu']) ?></td>
                                            <td><?= htmlspecialchars($u['username']) ?></td>
                                            <td><?= htmlspecialchars($u['nombre_completo']) ?></td>
                                            <td><?= htmlspecialchars($u['telefono']) ?></td>
                                            <td><?= htmlspecialchars($u['email']) ?></td>
                                            <td><?= ($u['activo'] ? 'Si' : 'No') ?></td>
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
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header text-white" style="background: linear-gradient(90deg, #6a5af9 0%, #7c3aed 100%);">
                    <div>
                        <h5 class="modal-title" id="tablaModalFloresLabel">Inventario de flores</h5>
                        <small class="text-white-50">Filtra por estado y exporta stock y valor.</small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div class="container my-3">
                        <div class="row g-2 align-items-end">
                            <div class="col-12 col-md-4">
                                <label class="form-label small mb-1">Estado</label>
                                <select class="form-select" id="modal_estado_flores" name="estado_flor">
                                    <option value="">Todas</option>
                                    <option value="disponible">Disponible</option>
                                    <option value="agotado">Agotado</option>
                                    <option value="no disponible">No disponible</option>
                                </select>
                            </div>
                            <div class="col-12 col-md-4 d-grid">
                                <button type="button" class="btn text-white" id="btnFiltrarModalFlores"
                                    style="background: linear-gradient(90deg, #6a5af9 0%, #7c3aed 100%);">
                                    Filtrar
                                </button>
                            </div>
                        </div>
                        <?php
                            $statsInv = [
                                'items' => count($modalInventario ?? []),
                                'stock' => array_sum(array_column($modalInventario ?? [], 'stock')),
                                'valor' => array_sum(array_column($modalInventario ?? [], 'valor_total'))
                            ];
                        ?>
                        <div class="row g-3 mt-2">
                            <div class="col-12 col-md-3">
                                <div class="border rounded-3 p-3 bg-light">
                                    <div class="small text-muted">Productos</div>
                                    <div class="h6 mb-0"><?= number_format($statsInv['items']) ?></div>
                                </div>
                            </div>
                            <div class="col-12 col-md-3">
                                <div class="border rounded-3 p-3 bg-light">
                                    <div class="small text-muted">Stock total</div>
                                    <div class="h6 mb-0"><?= number_format($statsInv['stock']) ?></div>
                                </div>
                            </div>
                            <div class="col-12 col-md-3">
                                <div class="border rounded-3 p-3 bg-light">
                                    <div class="small text-muted">Valor total</div>
                                    <div class="h6 mb-0">$<?= number_format($statsInv['valor'], 2) ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="row g-3 mt-3">
                            <div class="col-12 col-md-6">
                                <form id="formPdfFlores" action="controllers/repopdf.php" method="POST" class="d-grid">
                                    <input type="hidden" name="accion" value="flores_pdf">
                                    <input type="hidden" name="ids" id="pdf_ids_flores">
                                    <button type="submit" class="btn btn-primary"
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
                                    <th>Precio unitario</th>
                                    <th>Valor total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($modalInventario)): ?>
                                    <?php foreach ($modalInventario as $f): ?>
                                        <?php $estadoFlor = strtolower($f['estado']); ?>
                                        <tr data-estado="<?= $estadoFlor ?>">
                                            <td><input type="checkbox" class="select-row" value="<?= htmlspecialchars($f['idtflor']) ?>"></td>
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
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header text-white" style="background: linear-gradient(90deg, #6a5af9 0%, #7c3aed 100%);" >
                    <div>
                        <h5 class="modal-title" id="tablaModalLabel">Lista de pagos</h5>
                        <small class="text-white-50">Incluye cliente, pedido y totales resumidos.</small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div class="container my-3">
                        <div class="row g-2 align-items-end">
                            <div class="col-12 col-md-3">
                                <label class="form-label small mb-1">Fecha inicio</label>
                                <input type="date" class="form-control" name="fecha_inicio" id="modal_fecha_inicio_pagos">
                            </div>
                            <div class="col-12 col-md-3">
                                <label class="form-label small mb-1">Fecha fin</label>
                                <input type="date" class="form-control" name="fecha_fin" id="modal_fecha_fin_pagos">
                            </div>
                            <div class="col-12 col-md-3">
                                <label class="form-label small mb-1">Estado</label>
                                <select class="form-select" name="estado" id="modal_estado_pagos">
                                    <option value="">Todos</option>
                                    <option value="Pendiente">Pendiente</option>
                                    <option value="Completado">Completado</option>
                                    <option value="Reembolsado">Reembolsado</option>
                                </select>
                            </div>
                            <div class="col-12 col-md-3 d-grid">
                                <button type="button" class="btn w-100 text-white" id="btnFiltrarPagos" style="background: linear-gradient(90deg, #6a5af9 0%, #7c3aed 100%);">Filtrar</button>
                            </div>
                        </div>
                        <?php
                            $statsPagos = [
                                'total' => count($modalPagos ?? []),
                                'monto' => array_sum(array_column($modalPagos ?? [], 'monto')),
                                'completados' => count(array_filter($modalPagos ?? [], fn($p) => strtolower($p['estado_pag']) === 'completado')),
                                'pendientes' => count(array_filter($modalPagos ?? [], fn($p) => strtolower($p['estado_pag']) === 'pendiente'))
                            ];
                        ?>
                        <div class="row g-3 mt-2">
                            <div class="col-12 col-md-3">
                                <div class="border rounded-3 p-3 bg-light">
                                    <div class="small text-muted">Pagos</div>
                                    <div class="h6 mb-0"><?= number_format($statsPagos['total']) ?></div>
                                </div>
                            </div>
                            <div class="col-12 col-md-3">
                                <div class="border rounded-3 p-3 bg-light">
                                    <div class="small text-muted">Monto total</div>
                                    <div class="h6 mb-0">$<?= number_format($statsPagos['monto'], 2) ?></div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="border rounded-3 p-3 bg-light">
                                    <div class="small text-muted">Completados</div>
                                    <div class="h6 mb-0 text-success"><?= number_format($statsPagos['completados']) ?></div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="border rounded-3 p-3 bg-light">
                                    <div class="small text-muted">Pendientes</div>
                                    <div class="h6 mb-0 text-warning"><?= number_format($statsPagos['pendientes']) ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="row g-3 mt-3">
                            <div class="col-12 col-md-6">
                                <form id="formPdfPagos" action="controllers/repopdf.php" method="POST" class="d-grid">
                                    <input type="hidden" name="accion" value="pagos_pdf">
                                    <input type="hidden" name="ids" id="pdf_ids_pagos">
                                   <button type="submit" class="btn btn-primary" 
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
                                    <th>Fecha pago</th>
                                    <th>Metodo</th>
                                    <th>Estado</th>
                                    <th>Monto</th>
                                    <th>Pedido</th>
                                    <th>Cliente</th>
                                    <th>Transaccion</th>
                                    <th>Comprobante</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($modalPagos)): ?>
                                    <?php foreach ($modalPagos as $pago): ?>
                                        <?php
                                            $estadoPago = strtolower($pago['estado_pag']);
                                            $fechaPagoIso = date('Y-m-d', strtotime($pago['fecha_pago']));
                                        ?>
                                        <tr data-fecha="<?= $fechaPagoIso ?>" data-estado="<?= $estadoPago ?>">
                                            <td><input type="checkbox" class="select-row" value="<?= htmlspecialchars($pago['idpago']) ?>"></td>
                                            <td><?= htmlspecialchars($pago['idpago']) ?></td>
                                            <td><?= date('d/m/Y', strtotime($pago['fecha_pago'])) ?></td>
                                            <td><?= htmlspecialchars($pago['metodo_pago']) ?></td>
                                            <td><span class="badge bg-<?= $estadoPago === 'completado' ? 'success' : ($estadoPago === 'pendiente' ? 'warning text-dark' : 'secondary') ?>"><?= htmlspecialchars($pago['estado_pag']) ?></span></td>
                                            <td>$<?= number_format($pago['monto'], 2) ?></td>
                                            <td><?= htmlspecialchars($pago['numped'] ?? '-') ?></td>
                                            <td><?= htmlspecialchars($pago['cliente'] ?? '-') ?></td>
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
                                        <td colspan="10" class="text-center text-warning">No hay pagos registrados.</td>
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

