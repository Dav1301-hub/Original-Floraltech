<?php
// Gestión de Usuarios (unificada)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$usuarios        = $usuarios ?? $empleados ?? [];
$usuarios_total  = $usuarios_total  ?? 0;
$usuarios_activos= $usuarios_activos?? 0;
$clientes_total  = $clientes_total  ?? 0;
$empleados_total = $empleados_total ?? 0;
$admins_total    = $admins_total    ?? 0;
?>

<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-4 py-3 px-3 rounded-4 shadow-sm text-white" style="background: linear-gradient(120deg, #0d6efd 0%, #5b21b6 60%, #1e1b4b 100%);">
        <div>
            <p class="mb-1 opacity-75" style="letter-spacing:1px;text-transform:uppercase;"><i class="fas fa-users me-2"></i>FloralTech Admin</p>
            <h2 class="mb-0 fw-bold">Gestión de Usuarios</h2>
        </div>
        <button class="btn btn-light text-primary fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalNuevoUsuario">
            <i class="fas fa-plus me-2"></i>Nuevo usuario
        </button>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card shadow-sm border-0 h-100 metric-card metric-green">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <small class="text-muted text-uppercase">Usuarios</small>
                            <h3 class="mb-0"><?= $usuarios_total ?></h3>
                        </div>
                        <span class="metric-icon"><i class="fas fa-users"></i></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card shadow-sm border-0 h-100 metric-card metric-purple">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <small class="text-muted text-uppercase">Activos</small>
                            <h3 class="mb-0"><?= $usuarios_activos ?></h3>
                        </div>
                        <span class="metric-icon"><i class="fas fa-toggle-on"></i></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card shadow-sm border-0 h-100 metric-card metric-yellow">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <small class="text-muted text-uppercase">Clientes</small>
                            <h3 class="mb-0"><?= $clientes_total ?></h3>
                        </div>
                        <span class="metric-icon"><i class="fas fa-hand-holding-heart"></i></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card shadow-sm border-0 h-100 metric-card metric-pink">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <small class="text-muted text-uppercase">Colaboradores</small>
                            <h3 class="mb-0"><?= $empleados_total ?></h3>
                        </div>
                        <span class="metric-icon"><i class="fas fa-briefcase"></i></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Buscar</label>
                    <input type="text" id="filtroTexto" class="form-control" placeholder="Nombre, usuario, email">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Rol</label>
                    <select id="filtroRol" class="form-select">
                        <option value="">Todos</option>
                        <option value="1">Administrador</option>
                        <option value="2">Vendedor</option>
                        <option value="3">Inventario</option>
                        <option value="4">Repartidor</option>
                        <option value="5">Cliente</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Estado</label>
                    <select id="filtroEstado" class="form-select">
                        <option value="">Todos</option>
                        <option value="1">Activo</option>
                        <option value="0">Inactivo</option>
                    </select>
                </div>
                <div class="col-md-2 d-grid">
                    <button class="btn btn-outline-secondary" id="btnLimpiarFiltros"><i class="fas fa-eraser me-1"></i>Limpiar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Usuarios registrados</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle" id="tablaUsuarios">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Usuario</th>
                            <th>Rol</th>
                            <th>Estado</th>
                            <th>Registro</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($usuarios)): ?>
                            <?php foreach ($usuarios as $u): ?>
                                <tr data-rol="<?= htmlspecialchars($u['tpusu_idtpusu']) ?>" data-activo="<?= (int)$u['activo'] ?>">
                                    <td><?= htmlspecialchars($u['idusu']) ?></td>
                                    <td><?= htmlspecialchars($u['nombre_completo']) ?></td>
                                    <td>
                                        <div class="fw-semibold"><?= htmlspecialchars($u['username'] ?? '') ?></div>
                                        <small class="text-muted"><?= htmlspecialchars($u['email'] ?? '') ?></small>
                                    </td>
                                    <td><span class="badge bg-primary-subtle text-primary"><?= htmlspecialchars($u['rol'] ?? '') ?></span></td>
                                    <td>
                                        <?php if ((int)$u['activo'] === 1): ?>
                                            <span class="badge bg-success-subtle text-success">Activo</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Inactivo</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= !empty($u['fecha_registro']) ? date('d/m/Y', strtotime($u['fecha_registro'])) : 'N/D' ?></td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button class="btn btn-outline-primary" onclick="abrirEditarUsuario(<?= (int)$u['idusu'] ?>)" title="Editar"><i class="fas fa-edit"></i></button>
                                            <button class="btn btn-outline-danger" onclick="confirmarEliminarUsuario(<?= (int)$u['idusu'] ?>)" title="Eliminar"><i class="fas fa-trash"></i></button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="7" class="text-center text-muted py-4">No hay usuarios registrados.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <nav aria-label="Paginación usuarios">
                <ul class="pagination justify-content-center" id="pagerUsuarios"></ul>
            </nav>
        </div>
    </div>

    <div class="card shadow-sm border-0 mb-3">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Buscar (clientes cli)</label>
                    <input type="text" class="form-control" id="filtroCliTexto" placeholder="Nombre o correo">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Desde</label>
                    <input type="date" class="form-control" id="filtroCliDesde">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Hasta</label>
                    <input type="date" class="form-control" id="filtroCliHasta">
                </div>
                <div class="col-md-2 d-grid">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalClienteCli"><i class="fas fa-plus me-1"></i>Nuevo cliente</button>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white border-0 d-flex align-items-center justify-content-between">
            <h5 class="mb-0">Clientes no registrados</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle" id="tablaClientesCli">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Contacto</th>
                            <th>Dirección</th>
                            <th>Registro</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($clientes_cli)): ?>
                            <?php foreach ($clientes_cli as $c): ?>
                                <tr data-fecha="<?= htmlspecialchars($c['fecha_registro'] ?? '') ?>" data-texto="<?= strtolower(htmlspecialchars($c['nombre'] . ' ' . ($c['email'] ?? ''))) ?>">
                                    <td><?= htmlspecialchars($c['idcli']) ?></td>
                                    <td><?= htmlspecialchars($c['nombre']) ?></td>
                                    <td>
                                        <div class="fw-semibold"><?= htmlspecialchars($c['email'] ?? '') ?></div>
                                        <small class="text-muted"><?= htmlspecialchars($c['telefono'] ?? '') ?></small>
                                    </td>
                                    <td><?= htmlspecialchars($c['direccion'] ?? '') ?></td>
                                    <td><?= !empty($c['fecha_registro']) ? date('d/m/Y', strtotime($c['fecha_registro'])) : 'N/D' ?></td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button class="btn btn-outline-primary" onclick="editarClienteCli(<?= (int)$c['idcli'] ?>)"><i class="fas fa-edit"></i></button>
                                            <button class="btn btn-outline-danger" onclick="confirmarEliminarClienteCli(<?= (int)$c['idcli'] ?>)"><i class="fas fa-trash"></i></button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="6" class="text-center text-muted py-4">No hay clientes sin registro.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <nav aria-label="Paginación clientes">
                <ul class="pagination justify-content-center" id="pagerClientes"></ul>
            </nav>
        </div>
    </div>
</div>

<!-- Modal Nuevo Usuario -->
<div class="modal fade" id="modalNuevoUsuario" tabindex="-1" aria-labelledby="modalNuevoUsuarioLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="formNuevoUsuario" autocomplete="off">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalNuevoUsuarioLabel">Crear nuevo usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nombre completo</label>
                            <input type="text" class="form-control" name="nombre_completo" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Usuario</label>
                            <input type="text" class="form-control" name="username" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Teléfono</label>
                            <input type="text" class="form-control" name="telefono">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Rol</label>
                            <select class="form-select" name="tpusu_idtpusu" required>
                                <option value="">Seleccione</option>
                                <option value="1">Administrador</option>
                                <option value="2">Vendedor</option>
                                <option value="3">Inventario</option>
                                <option value="4">Repartidor</option>
                                <option value="5">Cliente</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Estado</label>
                            <select class="form-select" name="activo">
                                <option value="1">Activo</option>
                                <option value="0">Inactivo</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Contraseña</label>
                            <input type="password" class="form-control" name="password" placeholder="Por defecto 123456">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Naturaleza / Cargo</label>
                            <input type="text" class="form-control" name="naturaleza" placeholder="Cargo o nota">
                        </div>
                    </div>
                    <div id="alertNuevoUsuario" class="mt-3"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Editar Usuario -->
<div class="modal fade" id="modalEditarUsuario" tabindex="-1" aria-labelledby="modalEditarUsuarioLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="formEditarUsuario" autocomplete="off">
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditarUsuarioLabel">Editar usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nombre completo</label>
                            <input type="text" class="form-control" name="nombre_completo" id="edit_nombre" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Usuario</label>
                            <input type="text" class="form-control" name="username" id="edit_username" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" id="edit_email" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Teléfono</label>
                            <input type="text" class="form-control" name="telefono" id="edit_telefono">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Rol</label>
                            <select class="form-select" name="tpusu_idtpusu" id="edit_rol" required>
                                <option value="1">Administrador</option>
                                <option value="2">Vendedor</option>
                                <option value="3">Inventario</option>
                                <option value="4">Repartidor</option>
                                <option value="5">Cliente</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Estado</label>
                            <select class="form-select" name="activo" id="edit_estado">
                                <option value="1">Activo</option>
                                <option value="0">Inactivo</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Contraseña (opcional)</label>
                            <input type="password" class="form-control" name="password" id="edit_password" placeholder="Dejar vacío para no cambiar">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Naturaleza / Cargo</label>
                            <input type="text" class="form-control" name="naturaleza" id="edit_naturaleza">
                        </div>
                    </div>
                    <div id="alertEditarUsuario" class="mt-3"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Confirmar Eliminar -->
<div class="modal fade" id="modalEliminarUsuario" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Eliminar usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">¿Seguro que deseas eliminar este usuario? Esta acción no se puede deshacer.</p>
                <input type="hidden" id="delete_id">
                <div id="alertEliminarUsuario" class="mt-3"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" onclick="eliminarUsuario()">Eliminar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Cliente (cli) -->
<div class="modal fade" id="modalClienteCli" tabindex="-1" aria-labelledby="modalClienteCliLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formClienteCli" autocomplete="off">
                <input type="hidden" name="id" id="cli_id">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalClienteCliLabel">Cliente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" class="form-control" name="nombre" id="cli_nombre" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" id="cli_email">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Teléfono</label>
                        <input type="text" class="form-control" name="telefono" id="cli_telefono">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Dirección</label>
                        <input type="text" class="form-control" name="direccion" id="cli_direccion">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Fecha registro</label>
                        <input type="date" class="form-control" name="fecha_registro" id="cli_fecha">
                    </div>
                    <div id="alertClienteCli"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Confirmar Eliminar Cliente (cli) -->
<div class="modal fade" id="modalEliminarCli" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Eliminar cliente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">¿Seguro que deseas eliminar este cliente? Esta acción no se puede deshacer.</p>
                <input type="hidden" id="delete_cli_id">
                <div id="alertEliminarCli" class="mt-3"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" onclick="eliminarClienteCli()">Eliminar</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const inputTexto = document.getElementById('filtroTexto');
    const selectRol = document.getElementById('filtroRol');
    const selectEstado = document.getElementById('filtroEstado');
    const btnLimpiar = document.getElementById('btnLimpiarFiltros');
    const tbody = document.querySelector('#tablaUsuarios tbody');

    function filtrar() {
        const q = (inputTexto.value || '').toLowerCase();
        const rol = selectRol.value;
        const est = selectEstado.value;
        Array.from(tbody.querySelectorAll('tr')).forEach(tr => {
            const rolTr = tr.getAttribute('data-rol');
            const estTr = tr.getAttribute('data-activo');
            const texto = tr.innerText.toLowerCase();
            const matchTexto = texto.includes(q);
            const matchRol = !rol || rol === rolTr;
            const matchEst = est === '' || est === estTr;
            const match = matchTexto && matchRol && matchEst;
            tr.dataset.match = match ? '1' : '0';
            tr.style.display = match ? '' : 'none';
        });
        paginarTabla('tablaUsuarios', 'pagerUsuarios', 10, true);
    }

        [inputTexto, selectRol, selectEstado].forEach(el => {
        el && el.addEventListener('input', filtrar);
        el && el.addEventListener('change', filtrar);
    });
    document.getElementById('perPageUsuarios')?.addEventListener('change', () => paginarTabla('tablaUsuarios','pagerUsuarios', getPageSize(document.getElementById('perPageUsuarios')), true));
    btnLimpiar?.addEventListener('click', function() {
        inputTexto.value = '';
        selectRol.value = '';
        selectEstado.value = '';
        filtrar();
    });
    filtrar();

    // Paginación para ambas tablas (cliente-side simple)
        function getPageSize(selectEl) {
        if (!selectEl) return 10;
        const val = selectEl.value;
        if (val === 'all') return Infinity;
        const num = parseInt(val, 10);
        return isNaN(num) ? 10 : num;
    }

    function paginarTabla(tableId, pagerId, pageSize, resetPage = false) {
        const table = document.getElementById(tableId);
        const pager = document.getElementById(pagerId);
        if (!table || !pager) return;
        const allRows = Array.from(table.querySelectorAll('tbody tr'));
        const filtered = allRows.filter(r => (r.dataset.match ?? '1') === '1');
        const total = filtered.length;
        const pages = pageSize === Infinity ? 1 : Math.max(1, Math.ceil(total / pageSize));
        let current = parseInt(pager.getAttribute('data-page') || '1', 10);
        if (resetPage) current = 1;
        if (current > pages) current = pages;
        pager.setAttribute('data-page', current);

        allRows.forEach(r => {
            const isMatch = (r.dataset.match ?? '1') === '1';
            if (!isMatch) {
                r.style.display = 'none';
            }
        });
        filtered.forEach((r, idx) => {
            const p = pageSize === Infinity ? 1 : (Math.floor(idx / pageSize) + 1);
            r.style.display = (p === current) ? '' : 'none';
        });

        pager.innerHTML = '';
        for (let i = 1; i <= pages; i++) {
            const li = document.createElement('li');
            li.className = 'page-item' + (i === current ? ' active' : '');
            const a = document.createElement('a');
            a.className = 'page-link';
            a.href = '#';
            a.textContent = i;
            a.addEventListener('click', (e) => {
                e.preventDefault();
                pager.setAttribute('data-page', i);
                paginarTabla(tableId, pagerId, pageSize);
            });
            li.appendChild(a);
            pager.appendChild(li);
        }
    }

    paginarTabla('tablaUsuarios', 'pagerUsuarios', getPageSize(document.getElementById('perPageUsuarios')));

    // Crear usuario
    const formNuevo = document.getElementById('formNuevoUsuario');
    formNuevo?.addEventListener('submit', function(e) {
        e.preventDefault();
        const alerta = document.getElementById('alertNuevoUsuario');
        alerta.innerHTML = '';
        const fd = new FormData(formNuevo);
        fd.append('action', 'create');
        if (!fd.get('password')) {
            fd.set('password', '123456');
        }
        fetch('assets/ajax/ajax_empleado.php', {
            method: 'POST',
            body: fd
        })
        .then(r => r.text())
        .then(text => {
            try {
                const data = JSON.parse(text);
                if (data.success) {
                    alerta.innerHTML = '<div class="alert alert-success">Usuario creado correctamente</div>';
                    setTimeout(() => window.location.reload(), 900);
                } else {
                    alerta.innerHTML = `<div class="alert alert-danger">${data.error || 'No se pudo crear el usuario'}</div>`;
                }
            } catch (e) {
                alerta.innerHTML = `<div class="alert alert-danger">Respuesta inválida: ${text}</div>`;
            }
        })
        .catch(err => alerta.innerHTML = `<div class="alert alert-danger">${err}</div>`);
    });

    // Editar usuario
    const formEditar = document.getElementById('formEditarUsuario');
    formEditar?.addEventListener('submit', function(e) {
        e.preventDefault();
        const alerta = document.getElementById('alertEditarUsuario');
        alerta.innerHTML = '';
        const fd = new FormData(formEditar);
        fd.append('action', 'update');
        fetch('assets/ajax/ajax_empleado.php', { method: 'POST', body: fd })
        .then(r => r.text())
        .then(text => {
            try {
                const data = JSON.parse(text);
                if (data.success) {
                    alerta.innerHTML = '<div class="alert alert-success">Usuario actualizado</div>';
                    setTimeout(() => window.location.reload(), 900);
                } else {
                    alerta.innerHTML = `<div class="alert alert-danger">${data.error || 'No se pudo actualizar'}</div>`;
                }
            } catch (e) {
                alerta.innerHTML = `<div class="alert alert-danger">Respuesta inválida: ${text}</div>`;
            }
        })
        .catch(err => alerta.innerHTML = `<div class="alert alert-danger">${err}</div>`);
    });
});

// Filtros y paginación para clientes (cli)
document.addEventListener('DOMContentLoaded', function() {
    const txt = document.getElementById('filtroCliTexto');
    const desde = document.getElementById('filtroCliDesde');
    const hasta = document.getElementById('filtroCliHasta');
    const tbodyCli = document.querySelector('#tablaClientesCli tbody');

        function paginarCli(pageSize = 10, resetPage = false) {
        const pager = document.getElementById('pagerClientes');
        const perPageCli = document.getElementById('perPageClientes');
        if (perPageCli) {
            const val = perPageCli.value;
            if (val === 'all') pageSize = Infinity; else pageSize = parseInt(val, 10) || 10;
        }
        const allRows = Array.from(tbodyCli.querySelectorAll('tr'));
        const filtered = allRows.filter(r => (r.dataset.match ?? '1') === '1');
        const total = filtered.length;
        const pages = pageSize === Infinity ? 1 : Math.max(1, Math.ceil(total / pageSize));
        let current = parseInt(pager.getAttribute('data-page') || '1', 10);
        if (resetPage) current = 1;
        if (current > pages) current = pages;
        pager.setAttribute('data-page', current);
        allRows.forEach(r => {
            const isMatch = (r.dataset.match ?? '1') === '1';
            if (!isMatch) {
                r.style.display = 'none';
            }
        });
        filtered.forEach((r, idx) => {
            const p = pageSize === Infinity ? 1 : (Math.floor(idx / pageSize) + 1);
            r.style.display = (p === current) ? '' : 'none';
        });
        pager.innerHTML = '';
        for (let i = 1; i <= pages; i++) {
            const li = document.createElement('li');
            li.className = 'page-item' + (i === current ? ' active' : '');
            const a = document.createElement('a');
            a.className = 'page-link';
            a.href = '#';
            a.textContent = i;
            a.addEventListener('click', (e) => {
                e.preventDefault();
                pager.setAttribute('data-page', i);
                paginarCli(pageSize);
            });
            li.appendChild(a);
            pager.appendChild(li);
        }
    }

    function filtrarCli() {
        const q = (txt.value || '').toLowerCase();
        const d1 = desde.value ? new Date(desde.value) : null;
        const d2 = hasta.value ? new Date(hasta.value) : null;
        Array.from(tbodyCli.querySelectorAll('tr')).forEach(tr => {
            const texto = (tr.getAttribute('data-texto') || '').toLowerCase();
            const fecha = tr.getAttribute('data-fecha') || '';
            let ok = true;
            if (q && !texto.includes(q)) ok = false;
            if (fecha && (d1 || d2)) {
                const f = new Date(fecha);
                if (d1 && f < d1) ok = false;
                if (d2 && f > d2) ok = false;
            }
            tr.dataset.match = ok ? '1' : '0';
        });
        paginarCli(10, true);
    }

    [txt, desde, hasta].forEach(el => {
        el && el.addEventListener('input', filtrarCli);
        el && el.addEventListener('change', filtrarCli);
    });
    document.getElementById('perPageClientes')?.addEventListener('change', () => paginarCli(10, true));
    filtrarCli();
});

// CRUD clientes cli
let cliEditId = null;
const formCli = document.getElementById('formClienteCli');
formCli?.addEventListener('submit', function(e) {
    e.preventDefault();
    const alerta = document.getElementById('alertClienteCli');
    alerta.innerHTML = '';
    const fd = new FormData(formCli);
    const action = cliEditId ? 'update_cli' : 'create_cli';
    fd.append('action', action);
    if (cliEditId) fd.set('id', cliEditId);
    fetch('assets/ajax/ajax_empleado.php', { method: 'POST', body: fd })
        .then(r => r.text())
        .then(text => {
            try {
                const data = JSON.parse(text);
                if (data.success) {
                    alerta.innerHTML = '<div class="alert alert-success">Cliente guardado</div>';
                    setTimeout(() => window.location.reload(), 800);
                } else {
                    alerta.innerHTML = `<div class="alert alert-danger">${data.error || 'No se pudo guardar'}</div>`;
                }
            } catch (e) {
                alerta.innerHTML = `<div class="alert alert-danger">Respuesta inválida: ${text}</div>`;
            }
        })
        .catch(err => alerta.innerHTML = `<div class="alert alert-danger">${err}</div>`);
});

function editarClienteCli(id) {
    cliEditId = id;
    const alerta = document.getElementById('alertClienteCli');
    alerta.innerHTML = '';
    fetch('assets/ajax/ajax_empleado.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'action=get_cli&id=' + id
    })
    .then(r => r.text())
    .then(text => {
        const data = JSON.parse(text);
        if (!data.success) throw new Error('No se pudo cargar');
        document.getElementById('cli_id').value = data.idcli;
        document.getElementById('cli_nombre').value = data.nombre || '';
        document.getElementById('cli_email').value = data.email || '';
        document.getElementById('cli_telefono').value = data.telefono || '';
        document.getElementById('cli_direccion').value = data.direccion || '';
        document.getElementById('cli_fecha').value = data.fecha_registro || '';
        document.getElementById('modalClienteCliLabel').innerText = 'Editar cliente';
        new bootstrap.Modal(document.getElementById('modalClienteCli')).show();
    })
    .catch(err => alert('Error: ' + err));
}

function eliminarClienteCli(id) {
    if (!confirm('¿Eliminar cliente?')) return;
    fetch('assets/ajax/ajax_empleado.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'action=delete_cli&id=' + id
    })
    .then(r => r.text())
    .then(text => {
        const data = JSON.parse(text);
        if (data.success) {
            alert('Cliente eliminado');
            window.location.reload();
        } else {
            alert(data.error || 'No se pudo eliminar');
        }
    });
}

// Abrir modal cliente vacío
document.addEventListener('DOMContentLoaded', function() {
    const modalEl = document.getElementById('modalClienteCli');
    if (!modalEl) return;
    modalEl.addEventListener('show.bs.modal', function(e) {
        if (!cliEditId) {
            document.getElementById('formClienteCli').reset();
            document.getElementById('cli_id').value = '';
            document.getElementById('modalClienteCliLabel').innerText = 'Nuevo cliente';
            document.getElementById('alertClienteCli').innerHTML = '';
        }
    });
});
function abrirEditarUsuario(id) {
    const alerta = document.getElementById('alertEditarUsuario');
    alerta.innerHTML = '';
    fetch('assets/ajax/ajax_empleado.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'action=get&id=' + id
    })
    .then(r => r.text())
    .then(text => {
        const data = JSON.parse(text);
        if (!data.success) throw new Error('No se pudo cargar');
        document.getElementById('edit_id').value = data.idusu;
        document.getElementById('edit_nombre').value = data.nombre_completo || '';
        document.getElementById('edit_username').value = data.username || '';
        document.getElementById('edit_email').value = data.email || '';
        document.getElementById('edit_telefono').value = data.telefono || '';
        document.getElementById('edit_naturaleza').value = data.naturaleza || '';
        document.getElementById('edit_rol').value = data.tpusu_idtpusu || '';
        document.getElementById('edit_estado').value = data.activo || '1';
        document.getElementById('edit_password').value = '';
        new bootstrap.Modal(document.getElementById('modalEditarUsuario')).show();
    })
    .catch(err => alert('Error: ' + err));
}

function confirmarEliminarUsuario(id) {
    document.getElementById('delete_id').value = id;
    document.getElementById('alertEliminarUsuario').innerHTML = '';
    new bootstrap.Modal(document.getElementById('modalEliminarUsuario')).show();
}

function eliminarUsuario() {
    const id = document.getElementById('delete_id').value;
    const alerta = document.getElementById('alertEliminarUsuario');
    alerta.innerHTML = '';
    fetch('assets/ajax/ajax_empleado.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'action=delete&id=' + encodeURIComponent(id)
    })
    .then(r => r.text())
    .then(text => {
        const data = JSON.parse(text);
        if (data.success) {
            alerta.innerHTML = '<div class="alert alert-success">Usuario eliminado</div>';
            setTimeout(() => window.location.reload(), 800);
        } else {
            alerta.innerHTML = `<div class="alert alert-danger">${data.error || 'No se pudo eliminar'}</div>`;
        }
    })
    .catch(err => alerta.innerHTML = `<div class="alert alert-danger">${err}</div>`);
}

// Confirmación y eliminación para clientes (cli) usando modal
function confirmarEliminarClienteCli(id) {
    const alerta = document.getElementById('alertEliminarCli');
    if (alerta) alerta.innerHTML = '';
    const hidden = document.getElementById('delete_cli_id');
    if (hidden) hidden.value = id;
    const modal = document.getElementById('modalEliminarCli');
    if (modal) new bootstrap.Modal(modal).show();
}

function eliminarClienteCli() {
    const id = document.getElementById('delete_cli_id').value;
    const alerta = document.getElementById('alertEliminarCli');
    alerta.innerHTML = '';
    if (!id) return;
    fetch('assets/ajax/ajax_empleado.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'action=delete_cli&id=' + encodeURIComponent(id)
    })
    .then(r => r.text())
    .then(text => {
        const data = JSON.parse(text);
        if (data.success) {
            alerta.innerHTML = '<div class="alert alert-success">Cliente eliminado</div>';
            setTimeout(() => window.location.reload(), 800);
        } else {
            alerta.innerHTML = `<div class="alert alert-danger">${data.error || 'No se pudo eliminar'}</div>`;
        }
    })
    .catch(err => alerta.innerHTML = `<div class="alert alert-danger">${err}</div>`);
}
</script>






