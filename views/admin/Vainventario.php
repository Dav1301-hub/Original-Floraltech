<?php
// Variables disponibles: $stats, $productos, $tipos, $error (opcional)
$stats = $stats ?? ['total_registrados' => 0, 'total_activos' => 0, 'stock_bajo' => 0, 'sin_stock' => 0, 'valor_total' => 0];
$productos = $productos ?? [];
$tipos = $tipos ?? [];
$error = $error ?? '';
?>

<style>
    .text-muted-small {color: #6b7280; font-size: 0.9rem;}
    /* Badges suaves usando la paleta base */
    .badge-soft-success {background:#e0f7f0; color:#0f766e;}
    .badge-soft-warning {background:#fff7e6; color:#b45309;}
    .badge-soft-danger {background:#ffe4e6; color:#b91c1c;}
    .badge-soft-secondary {background:#e5e7eb; color:#374151;}
    /* Tarjetas metricas estilo auditoria/reportes */
    .metrics-card {
        border-radius: 18px;
        padding: 16px;
        color: #0f172a;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        border: none;
    }
    .metric-green {background: linear-gradient(135deg, #d1f4e0 0%, #b2f0d1 100%) !important; color:#0f172a;}
    .metric-pink {background: linear-gradient(135deg, #ffe2f0 0%, #ffc7e0 100%) !important; color:#0f172a;}
    .metric-yellow {background: linear-gradient(135deg, #fff5d7 0%, #ffe7a3 100%) !important; color:#0f172a;}
    .metric-purple {background: linear-gradient(135deg, #e6e0ff 0%, #d0c4ff 100%) !important; color:#0f172a;}
    .metric-icon {
        width: 38px; height: 38px; border-radius: 10px;
        display: inline-flex; align-items: center; justify-content: center;
        background: rgba(0,0,0,0.08);
        color: #0f172a;
    }
</style>

<div class="container-fluid px-3 px-md-4 py-2">
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-3 py-3 px-3 rounded-4 shadow-sm text-white" style="background: linear-gradient(120deg, #0d6efd 0%, #5b21b6 60%, #1e1b4b 100%);">
        <div>
            <p class="mb-1 opacity-75" style="letter-spacing:1px;text-transform:uppercase;">Floraltech Admin</p>
            <h2 class="fw-bold mb-0">Inventario</h2>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <button class="btn btn-light text-primary fw-semibold shadow-sm btn-sm btn-nuevo-tipo">Nueva categoria</button>
            <button class="btn btn-light text-primary fw-semibold shadow-sm btn-sm btn-nuevo">Nuevo producto</button>
        </div>
    </div>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($error) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            Operacion realizada correctamente.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="card shadow-sm border-0 rounded-4 h-100 text-center" style="background: linear-gradient(135deg,#e0e7ff,#eef2ff);">
                <div class="card-body">
                    <i class="bi bi-bar-chart-line display-6 text-primary"></i>
                    <h6 class="fw-bold mt-2 mb-1 text-primary">Productos activos</h6>
                    <div class="small text-muted">Registrados</div>
                    <div class="h4 fw-bold text-primary mb-0"><?= (int)($stats['total_activos'] ?? 0) ?></div>
                    <div class="small text-muted">Totales: <?= (int)($stats['total_registrados'] ?? 0) ?></div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card shadow-sm border-0 rounded-4 h-100 text-center" style="background: linear-gradient(135deg,#ffe2f0,#ffc7e0);">
                <div class="card-body">
                    <i class="bi bi-arrow-bar-down display-6 text-danger"></i>
                    <h6 class="fw-bold mt-2 mb-1 text-danger">Stock bajo (&lt;20)</h6>
                    <div class="small text-muted">Total</div>
                    <div class="h4 fw-bold text-danger mb-0"><?= (int)($stats['stock_bajo'] ?? 0) ?></div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card shadow-sm border-0 rounded-4 h-100 text-center" style="background: linear-gradient(135deg,#fef9c3,#fffbeb);">
                <div class="card-body">
                    <i class="bi bi-box display-6 text-warning"></i>
                    <h6 class="fw-bold mt-2 mb-1 text-warning">Sin stock</h6>
                    <div class="small text-muted">Total</div>
                    <div class="h4 fw-bold text-warning mb-0"><?= (int)($stats['sin_stock'] ?? 0) ?></div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card shadow-sm border-0 rounded-4 h-100 text-center" style="background: linear-gradient(135deg,#dcfce7,#ecfdf3);">
                <div class="card-body">
                    <i class="bi bi-cash-stack display-6 text-success"></i>
                    <h6 class="fw-bold mt-2 mb-1 text-success">Valor inventario</h6>
                    <div class="small text-muted">Monto</div>
                    <div class="h4 fw-bold text-success mb-0">$<?= number_format((float)$stats['valor_total'], 0, ',', '.') ?></div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-header bg-white d-flex align-items-center justify-content-between rounded-top-4">
            <div>
                <h5 class="mb-0">Productos</h5>
                <small class="text-muted"><?= count($productos) ?> items</small>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Producto</th>
                            <th>Categoria</th>
                            <th>Naturaleza</th>
                            <th>Color</th>
                            <th class="text-end">Precio</th>
                            <th class="text-end">Stock</th>
                            <th class="text-end">Disponible</th>
                            <th class="text-end">Actualizado</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($productos)): ?>
                            <tr><td colspan="9" class="text-center text-muted py-4">No hay productos en inventario.</td></tr>
                        <?php else: ?>
                            <?php foreach ($productos as $p): ?>
                                <?php
                                    $stockVal = (int)($p['stock_disp'] ?? $p['stock'] ?? 0);
                                    $badge = 'badge-soft-success';
                                    if ($stockVal === 0) { $badge = 'badge-soft-danger'; }
                                    elseif ($stockVal < 20) { $badge = 'badge-soft-warning'; }
                                    $dispBadge = !empty($p['disponible']) ? 'badge-soft-success' : 'badge-soft-secondary';
                                    $dispText  = !empty($p['disponible']) ? 'Si' : 'No';
                                ?>
                                <tr data-producto='<?= json_encode($p, JSON_HEX_APOS | JSON_HEX_TAG) ?>'>
                                    <td>
                                        <div class="fw-semibold text-primary"><?= htmlspecialchars($p['nombre']) ?></div>
                                        <div class="text-muted small">#<?= (int)$p['id'] ?></div>
                                    </td>
                                    <td><?= htmlspecialchars($p['tipo'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($p['naturaleza']) ?></td>
                                    <td><?= htmlspecialchars($p['color']) ?></td>
                                    <td class="text-end fw-semibold">$<?= number_format((float)$p['precio'], 0, ',', '.') ?></td>
                                    <td class="text-end"><span class="fw-semibold text-dark"><?= $stockVal ?></span></td>
                                    <td class="text-end"><span class="fw-semibold text-dark"><?= $dispText ?></span></td>
                                    <td class="text-end text-muted small">
                                        <?= !empty($p['fecha_actualizacion']) ? date('d/m/Y H:i', strtotime($p['fecha_actualizacion'])) : 'Sin fecha' ?>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-outline-primary btn-edit" data-id="<?= $p['id'] ?>">Editar</button>
                                            <button class="btn btn-outline-danger btn-delete" data-id="<?= $p['id'] ?>">Eliminar</button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header bg-white d-flex align-items-center justify-content-between rounded-top-4">
            <div>
                <h5 class="mb-0">Categorias (Tipos de flor)</h5>
                <small class="text-muted"><?= count($tipos) ?> tipos</small>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Categoria</th>
                            <th>Descripcion</th>
                            <th class="text-end">Productos</th>
                            <th class="text-end">Stock total</th>
                            <th class="text-end">Disponible</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($tipos)): ?>
                            <tr><td colspan="6" class="text-center text-muted py-4">No hay categorias.</td></tr>
                        <?php else: ?>
                            <?php foreach ($tipos as $t): ?>
                                <tr data-tipo='<?= json_encode($t, JSON_HEX_APOS | JSON_HEX_TAG) ?>'>
                                    <td class="fw-semibold text-primary"><?= htmlspecialchars($t['nombre']) ?></td>
                                    <td class="text-muted small"><?= htmlspecialchars($t['descripcion']) ?></td>
                                    <td class="text-end fw-semibold"><?= (int)($t['total_productos'] ?? 0) ?></td>
                                    <td class="text-end fw-semibold"><?= (int)($t['total_stock'] ?? 0) ?></td>
                                    <td class="text-end">
                                        <span class="badge rounded-pill <?= !empty($t['disponible']) ? 'badge-soft-success' : 'badge-soft-secondary' ?>"><?= !empty($t['disponible']) ? 'Si' : 'No' ?></span>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-outline-primary btn-edit-tipo">Editar</button>
                                            <button class="btn btn-outline-danger btn-delete-tipo">Eliminar</button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Producto -->
<div class="modal fade" id="modalProducto" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalProductoLabel">Nuevo producto</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="formProducto">
        <div class="modal-body">
            <input type="hidden" name="accion" value="crear">
            <input type="hidden" name="id" id="productoId">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nombre</label>
                    <input type="text" class="form-control" name="nombre" id="nombre" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Categoria</label>
                    <select class="form-select" name="tflor_idtflor" id="tflor_idtflor" required>
                        <option value="">Selecciona un tipo</option>
                        <?php foreach ($tipos as $t): ?>
                            <option value="<?= $t['idtflor'] ?>"><?= htmlspecialchars($t['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Color</label>
                    <input type="text" class="form-control" name="color" id="color" placeholder="Rojo, Blanco...">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Naturaleza</label>
                    <input type="text" class="form-control" name="naturaleza" id="naturaleza" placeholder="Natural / Artificial">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Precio</label>
                    <input type="number" step="0.01" min="0" class="form-control" name="precio" id="precio" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Stock</label>
                    <input type="number" min="0" class="form-control" name="stock" id="stock" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Alimentacion / manejo</label>
                    <input type="text" class="form-control" name="alimentacion" id="alimentacion" placeholder="Riego, cuidados...">
                </div>
                <div class="col-12">
                    <label class="form-label">Descripcion</label>
                    <textarea class="form-control" rows="2" name="descripcion" id="descripcion" placeholder="Notas del producto"></textarea>
                </div>
                <div class="col-12">
                    <label class="form-label">Motivo / Comentario interno</label>
                    <input type="text" class="form-control" name="motivo" id="motivo" placeholder="Opcional">
                </div>
                <div class="col-12">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" value="1" id="disponible" name="disponible" checked>
                        <label class="form-check-label" for="disponible">Disponible para venta</label>
                    </div>
                </div>
            </div>
            <div id="formAlert" class="mt-3"></div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary">Guardar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Categoria -->
<div class="modal fade" id="modalTipo" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalTipoLabel">Nueva categoria</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="formTipo">
        <div class="modal-body">
            <input type="hidden" name="accion" value="crear_tipo">
            <input type="hidden" name="id" id="tipoId">
            <div class="mb-3">
                <label class="form-label">Nombre</label>
                <input type="text" class="form-control" name="nombre" id="tipoNombre" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Descripcion</label>
                <textarea class="form-control" rows="2" name="descripcion" id="tipoDesc"></textarea>
            </div>
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" value="1" id="tipoDisp" name="disponible" checked>
                <label class="form-check-label" for="tipoDisp">Disponible</label>
            </div>
            <div id="formTipoAlert" class="mt-3"></div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary">Guardar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modalEl = document.getElementById('modalProducto');
    const modal = new bootstrap.Modal(modalEl);
    const form = document.getElementById('formProducto');
    const formAlert = document.getElementById('formAlert');
    const title = document.getElementById('modalProductoLabel');
    const dispInput = document.getElementById('disponible');

    // Modal categoria
    const modalTipoEl = document.getElementById('modalTipo');
    const modalTipo = new bootstrap.Modal(modalTipoEl);
    const formTipo = document.getElementById('formTipo');
    const formTipoAlert = document.getElementById('formTipoAlert');
    const titleTipo = document.getElementById('modalTipoLabel');

    const resetForm = () => {
        form.reset();
        form.elements['accion'].value = 'crear';
        form.elements['id'].value = '';
        dispInput.checked = true;
        formAlert.innerHTML = '';
        title.textContent = 'Nuevo producto';
    };

    document.querySelectorAll('.btn-nuevo').forEach(btn => {
        btn.addEventListener('click', () => {
            resetForm();
            modal.show();
        });
    });

    const resetTipo = () => {
        formTipo.reset();
        formTipo.elements['accion'].value = 'crear_tipo';
        formTipo.elements['id'].value = '';
        formTipoAlert.innerHTML = '';
        formTipo.elements['disponible'].checked = true;
        titleTipo.textContent = 'Nueva categoria';
    };

    document.querySelectorAll('.btn-nuevo-tipo').forEach(btn => {
        btn.addEventListener('click', () => {
            resetTipo();
            modalTipo.show();
        });
    });

    document.querySelectorAll('.btn-edit').forEach(btn => {
        btn.addEventListener('click', () => {
            const tr = btn.closest('tr');
            const data = JSON.parse(tr.getAttribute('data-producto'));
            resetForm();
            title.textContent = 'Editar producto';
            form.elements['accion'].value = 'actualizar';
            form.elements['id'].value = data.id;
            form.elements['nombre'].value = data.nombre || '';
            form.elements['tflor_idtflor'].value = data.tflor_idtflor || '';
            form.elements['naturaleza'].value = data.naturaleza || '';
            form.elements['color'].value = data.color || '';
            form.elements['precio'].value = data.precio || 0;
            form.elements['stock'].value = data.stock || data.stock_disp || 0;
            form.elements['alimentacion'].value = data.alimentacion || '';
            form.elements['descripcion'].value = data.descripcion || '';
            form.elements['motivo'].value = data.motivo || '';
            dispInput.checked = !!Number(data.disponible);
            modal.show();
        });
    });

    document.querySelectorAll('.btn-delete').forEach(btn => {
        btn.addEventListener('click', async () => {
            if (!confirm('Eliminar este producto?')) return;
            const id = btn.getAttribute('data-id');
            const fd = new FormData();
            fd.append('accion', 'eliminar');
            fd.append('id', id);
            const res = await fetch('index.php?ctrl=cinventario', { method: 'POST', body: fd, headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            const json = await res.json();
            if (json.success) {
                location.reload();
            } else {
                alert(json.message || 'Error al eliminar');
            }
        });
    });

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        formAlert.innerHTML = '';
        const fd = new FormData(form);
        const btn = form.querySelector('button[type="submit"]');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Guardando...';
        try {
            const res = await fetch('index.php?ctrl=cinventario', { method: 'POST', body: fd, headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            const json = await res.json();
            if (json.success) {
                location.reload();
            } else {
                formAlert.innerHTML = `<div class="alert alert-danger">${json.message || 'Error al guardar'}</div>`;
            }
        } catch (err) {
            formAlert.innerHTML = `<div class="alert alert-danger">Error: ${err}</div>`;
        } finally {
            btn.disabled = false;
            btn.innerHTML = 'Guardar';
        }
    });

    // Editar / eliminar categoria
    document.querySelectorAll('.btn-edit-tipo').forEach(btn => {
        btn.addEventListener('click', () => {
            const tr = btn.closest('tr');
            const data = JSON.parse(tr.getAttribute('data-tipo'));
            resetTipo();
            titleTipo.textContent = 'Editar categoria';
            formTipo.elements['accion'].value = 'actualizar_tipo';
            formTipo.elements['id'].value = data.idtflor;
            formTipo.elements['nombre'].value = data.nombre || '';
            formTipo.elements['descripcion'].value = data.descripcion || '';
            formTipo.elements['disponible'].checked = !!Number(data.disponible);
            modalTipo.show();
        });
    });

    document.querySelectorAll('.btn-delete-tipo').forEach(btn => {
        btn.addEventListener('click', async () => {
            if (!confirm('Eliminar esta categoria?')) return;
            const tr = btn.closest('tr');
            const data = JSON.parse(tr.getAttribute('data-tipo'));
            const fd = new FormData();
            fd.append('accion', 'eliminar_tipo');
            fd.append('id', data.idtflor);
            const res = await fetch('index.php?ctrl=cinventario', { method: 'POST', body: fd, headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            const json = await res.json();
            if (json.success) {
                location.reload();
            } else {
                alert(json.message || 'No se pudo eliminar (verifique que no tenga productos asociados)');
            }
        });
    });

    formTipo.addEventListener('submit', async (e) => {
        e.preventDefault();
        formTipoAlert.innerHTML = '';
        const fd = new FormData(formTipo);
        const btn = formTipo.querySelector('button[type="submit"]');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Guardando...';
        try {
            const res = await fetch('index.php?ctrl=cinventario', { method: 'POST', body: fd, headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            const json = await res.json();
            if (json.success) {
                location.reload();
            } else {
                formTipoAlert.innerHTML = `<div class="alert alert-danger">${json.message || 'Error al guardar'}</div>`;
            }
        } catch (err) {
            formTipoAlert.innerHTML = `<div class="alert alert-danger">Error: ${err}</div>`;
        } finally {
            btn.disabled = false;
            btn.innerHTML = 'Guardar';
        }
    });
});
</script>
