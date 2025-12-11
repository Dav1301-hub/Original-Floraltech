// Funciones adicionales para gestion de pedidos (admin)

// Abrir modal de edicion reutilizando el modal de nuevo pedido
function editarPedido(idPedido) {
    const modalEl = document.getElementById('nuevoPedidoModal');
    if (!modalEl) return;

    const modalInstance = new bootstrap.Modal(modalEl);
    const titleEl = modalEl.querySelector('.modal-title');
    const alertaDiv = document.getElementById('alertaNuevoPedido');
    const modoInput = document.getElementById('pedidoModo');
    const idInput = document.getElementById('pedidoIdHidden');

    if (!modoInput || !idInput || !alertaDiv || !titleEl) return;

    titleEl.innerHTML = '<i class="fas fa-pen me-2"></i>Editar pedido';
    modoInput.value = 'editar';
    idInput.value = idPedido;
    alertaDiv.innerHTML = '<div class="text-center py-3"><div class="spinner-border text-primary"></div></div>';
    modalInstance.show();

    fetch(`controllers/Cpedido.php?action=detalle&id=${encodeURIComponent(idPedido)}`)
        .then(r => r.json())
        .then(data => {
            if (!data.success || !data.pedido) {
                throw new Error(data.mensaje || 'No se pudo cargar el pedido');
            }
            const p = data.pedido;
            document.querySelector('input[name="nombre_cliente"]').value = p.cliente_nombre || '';
            document.querySelector('input[name="email_cliente"]').value = p.cliente_email || '';
            document.querySelector('input[name="telefono_cliente"]').value = p.cliente_telefono || '';
            document.querySelector('textarea[name="notas"]').value = p.notas || '';
            document.querySelector('input[name="direccion_entrega"]').value = p.direccion_entrega || '';
            document.querySelector('input[name="fecha_entrega_solicitada"]').value = p.fecha_entrega_solicitada ? p.fecha_entrega_solicitada.split(' ')[0] : '';
            document.querySelector('select[name="estado"]').value = p.estado || 'Pendiente';
            document.querySelector('select[name="estado_pago"]').value = p.estado_pago || 'Pendiente';
            document.querySelector('select[name="metodo_pago"]').value = p.metodo_pago || 'efectivo';
            document.getElementById('nuevoMonto').value = p.monto_total ? parseFloat(p.monto_total).toFixed(2) : '';
            cargarEmpleados('nuevoEmpleado', p.empleado_id || '');
            cargarClientes('nuevoCliente', p.cli_idcli || '').then(() => {
                const selectCliente = document.getElementById('nuevoCliente');
                if (selectCliente && p.cli_idcli) {
                    selectCliente.value = p.cli_idcli;
                }
            });
            alertaDiv.innerHTML = '';
        })
        .catch(error => {
            alertaDiv.innerHTML = `<div class="alert alert-danger">Error al cargar el pedido: ${error}</div>`;
        });
}

// Abrir modal de pago
function editarPago(idPedido) {
    const modal = new bootstrap.Modal(document.getElementById('modalPagoPedido'));
    document.getElementById('pagoIdPedido').value = idPedido;
    document.getElementById('alertaPagoPedido').innerHTML = '';
    // Tomar datos actuales de la fila
    const filas = document.querySelectorAll('tbody tr');
    let info = null;
    filas.forEach(fila => {
        if (fila.innerHTML.includes(`ID: ${idPedido}`)) {
            const c = fila.querySelectorAll('td');
            if (c.length >= 8) {
                info = {
                    numero: c[0].querySelector('strong').textContent.trim(),
                    monto: c[5].textContent.replace(/[^0-9.,]/g, '') || ''
                };
            }
        }
    });
    if (info) {
        document.getElementById('pagoNumeroPedido').textContent = `#${info.numero}`;
        document.getElementById('pagoMonto').value = parseFloat(info.monto.replace('.', '').replace(',', '.')) || '';
    }
    // valores por defecto
    document.getElementById('pagoMetodo').value = 'efectivo';
    document.getElementById('pagoEstado').value = 'Pendiente';
    modal.show();
}

// Cargar empleados activos para asignacion
function cargarEmpleados(targetId = 'nuevoEmpleado', selectedId = '') {
    fetch('controllers/Cpedido.php?action=empleados_activos')
        .then(response => response.json())
        .then(data => {
            const select = document.getElementById(targetId);
            if (!select) return;
            select.innerHTML = '<option value=\"\">Sin asignar</option>';

            if (!data.success || !data.empleados) {
                return;
            }

            data.empleados.forEach(emp => {
                const option = document.createElement('option');
                option.value = emp.idusu;
                option.textContent = emp.nombre_completo || emp.nombre || emp.username || `Empleado ${emp.idusu}`;
                if (String(selectedId) === String(emp.idusu)) {
                    option.selected = true;
                }
                select.appendChild(option);
            });
        })
        .catch(error => console.error('Error al cargar empleados:', error));
}

function actualizarCampoCliente(selector, valor) {
    if (!valor) return;
    const campo = document.querySelector(selector);
    if (!campo) return;
    campo.value = valor;
}

function cargarClientes(targetId = 'nuevoCliente', selectedId = '') {
    return fetch('controllers/Cpedido.php?action=clientes')
        .then(response => response.json())
        .then(data => {
            const select = document.getElementById(targetId);
            if (!select) return;
            select.innerHTML = '<option value="">Seleccionar cliente existente</option>';
            if (!data.success || !Array.isArray(data.clientes)) {
                return;
            }
            data.clientes.forEach(cliente => {
                const option = document.createElement('option');
                option.value = cliente.idcli;
                const etiquetas = [cliente.nombre || `Cliente ${cliente.idcli}`];
                if (cliente.email) etiquetas.push(cliente.email);
                if (cliente.telefono) etiquetas.push(cliente.telefono);
            option.textContent = etiquetas.join(' - ');
                option.dataset.nombre = cliente.nombre || '';
                option.dataset.email = cliente.email || '';
                option.dataset.telefono = cliente.telefono || '';
                option.dataset.direccion = cliente.direccion || '';
                if (String(selectedId) === String(cliente.idcli)) {
                    option.selected = true;
                }
                select.appendChild(option);
            });
        })
        .catch(error => console.error('Error al cargar clientes:', error));
}

function aplicarClienteSeleccionadoDesdeSelect(selectEl) {
    if (!selectEl) return;
    const option = selectEl.options[selectEl.selectedIndex];
    if (!option || !option.value) return;
    actualizarCampoCliente('input[name="nombre_cliente"]', option.dataset.nombre || '');
    actualizarCampoCliente('input[name="email_cliente"]', option.dataset.email || '');
    actualizarCampoCliente('input[name="telefono_cliente"]', option.dataset.telefono || '');
    actualizarCampoCliente('input[name="direccion_entrega"]', option.dataset.direccion || '');
}

function guardarPago(event) {
    event.preventDefault();
    const form = event.target;
    const alertaDiv = document.getElementById('alertaPagoPedido');
    const submitBtn = form.querySelector('button[type="submit"]');
    const original = submitBtn.innerHTML;
    alertaDiv.innerHTML = '';

    const formData = new FormData(form);
    formData.append('action', 'editar_pago');

    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Guardando...';

    fetch('controllers/Cpedido.php', { method: 'POST', body: formData })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                alertaDiv.innerHTML = '<div class="alert alert-success">Pago actualizado correctamente</div>';
                setTimeout(() => window.location.reload(), 1000);
            } else {
                alertaDiv.innerHTML = `<div class="alert alert-danger">${data.mensaje || 'No se pudo actualizar el pago'}</div>`;
                submitBtn.disabled = false;
                submitBtn.innerHTML = original;
            }
        })
        .catch(err => {
            alertaDiv.innerHTML = `<div class="alert alert-danger">Error: ${err}</div>`;
            submitBtn.disabled = false;
            submitBtn.innerHTML = original;
        });
}

// --- Productos para nuevo pedido ---
let catalogoCategorias = [];
let productosPorCat = {};
function cargarCategorias() {
    if (catalogoCategorias.length > 0) return Promise.resolve(catalogoCategorias);
    return fetch('controllers/Cpedido.php?action=categorias')
        .then(r => r.json())
        .then(data => {
            if (data.success && data.categorias) {
                catalogoCategorias = data.categorias;
            }
            return catalogoCategorias;
        })
        .catch(() => []);
}
function cargarProductosPorCategoria(catId) {
    if (productosPorCat[catId]) return Promise.resolve(productosPorCat[catId]);
    return fetch(`controllers/Cpedido.php?action=productos_por_categoria&cat_id=${catId}`)
        .then(r => r.json())
        .then(data => {
            if (data.success && data.productos) {
                productosPorCat[catId] = data.productos;
                return productosPorCat[catId];
            }
            return [];
        })
        .catch(() => []);
}

function agregarFilaProducto() {
    const tbody = document.querySelector('#tablaProductosNuevo tbody');
    if (!tbody) return;
    const row = document.createElement('tr');
    row.innerHTML = `
        <td>
            <div class="d-flex gap-1">
                <select class="form-select form-select-sm categoria-select" onchange="cargarProductosEnFila(this)">
                    <option value="">Categoria</option>
                </select>
                <select class="form-select form-select-sm producto-select" name="producto_id[]" onchange="actualizarPrecio(this)">
                    <option value="">Producto</option>
                </select>
            </div>
        </td>
        <td><input type="number" step="0.01" min="0" class="form-control form-control-sm precio-input" name="precio_unitario[]" onchange="actualizarSubtotal(this)" /></td>
        <td><input type="number" step="1" min="0" class="form-control form-control-sm cantidad-input" name="cantidad[]" onchange="actualizarSubtotal(this)" /></td>
        <td class="subtotal-cell fw-semibold text-end">$0</td>
        <td class="text-end"><button type="button" class="btn btn-outline-danger btn-sm" onclick="eliminarFilaProducto(this)"><i class="fas fa-trash"></i></button></td>
    `;
    tbody.appendChild(row);
    cargarCategorias().then(() => {
        const catSelect = row.querySelector('.categoria-select');
        catalogoCategorias.forEach(c => {
            const opt = document.createElement('option');
            opt.value = c.id;
            opt.textContent = c.nombre;
            catSelect.appendChild(opt);
        });
    });
}

function eliminarFilaProducto(btn) {
    const row = btn.closest('tr');
    if (row) row.remove();
    recalcularTotalNuevo();
}

function actualizarPrecio(selectEl) {
    const precioInput = selectEl.closest('tr').querySelector('.precio-input');
    const selected = selectEl.options[selectEl.selectedIndex];
    const precio = selected && selected.dataset.precio ? parseFloat(selected.dataset.precio) : 0;
    precioInput.value = precio > 0 ? precio : '';
    // Si el producto no esta disponible, mostrar error
    if (selected && selected.dataset.disponible === '0') {
        if (typeof abrirModalInfo === 'function') {
            abrirModalInfo('Producto no disponible', 'Este producto no está disponible');
        } else {
            alert('Este producto no está disponible');
        }
        selectEl.value = '';
        precioInput.value = '';
        actualizarSubtotal(precioInput);
        return;
    }
    actualizarSubtotal(precioInput);
}

function actualizarSubtotal(inputEl) {
    const row = inputEl.closest('tr');
    const precio = parseFloat(row.querySelector('.precio-input').value || '0');
    const cantidad = parseFloat(row.querySelector('.cantidad-input').value || '0');
    const subtotal = precio * cantidad;
    row.querySelector('.subtotal-cell').textContent = `$${subtotal.toFixed(2)}`;
    recalcularTotalNuevo();
}

function recalcularTotalNuevo() {
    const rows = document.querySelectorAll('#tablaProductosNuevo tbody tr');
    let total = 0;
    rows.forEach(r => {
        const precio = parseFloat(r.querySelector('.precio-input').value || '0');
        const cantidad = parseFloat(r.querySelector('.cantidad-input').value || '0');
        total += precio * cantidad;
    });
    const montoInput = document.getElementById('nuevoMonto');
    if (montoInput) {
        if (total > 0) {
            montoInput.value = total.toFixed(2);
            montoInput.readOnly = true;
        } else {
            montoInput.readOnly = false;
        }
    }
}

function cargarProductosEnFila(catSelect) {
    const row = catSelect.closest('tr');
    const prodSelect = row.querySelector('.producto-select');
    prodSelect.innerHTML = '<option value="">Producto</option>';
    const catId = catSelect.value;
    if (!catId) return;
    cargarProductosPorCategoria(catId).then(prods => {
        prods.forEach(p => {
            const opt = document.createElement('option');
            opt.value = p.id;
            opt.textContent = `${p.nombre} (Stock: ${p.stock ?? 0})`;
            opt.dataset.precio = p.precio || 0;
            opt.dataset.disponible = p.disponible ? '1' : '0';
            prodSelect.appendChild(opt);
        });
    });
}
// --- fin productos ---

// Ordenamiento de columnas
function ordenarPor(columna) {
    const urlParams = new URLSearchParams(window.location.search);
    const ordenActual = urlParams.get('orden');
    const dirActual = urlParams.get('dir') || 'DESC';

    let nuevaDir = 'ASC';
    if (ordenActual === columna && dirActual === 'ASC') {
        nuevaDir = 'DESC';
    }

    urlParams.set('orden', columna);
    urlParams.set('dir', nuevaDir);
    urlParams.delete('pagina');

    window.location.href = window.location.pathname + '?' + urlParams.toString();
}

// Exportaciones
function exportarExcel() {
    const urlParams = new URLSearchParams(window.location.search);
    urlParams.set('export', 'excel');
    window.location.href = window.location.pathname + '?' + urlParams.toString();
}
function exportarPDF() {
    const urlParams = new URLSearchParams(window.location.search);
    urlParams.set('export', 'pdf');
    window.open(window.location.pathname + '?' + urlParams.toString(), '_blank');
}

// Toggle filtros
function toggleFiltrosAvanzados() {
    const filtros = document.getElementById('filtrosAvanzados');
    const icon = document.querySelector('#btnFiltros i');

    if (filtros.style.display === 'none') {
        filtros.style.display = 'block';
        icon.classList.remove('fa-filter');
        icon.classList.add('fa-times');
    } else {
        filtros.style.display = 'none';
        icon.classList.remove('fa-times');
        icon.classList.add('fa-filter');
    }
}

// Busqueda con debounce
document.addEventListener('DOMContentLoaded', function() {
    const inputBusqueda = document.getElementById('busquedaRapida');
    let timeoutBusqueda = null;

    if (inputBusqueda) {
        inputBusqueda.addEventListener('keyup', function() {
            clearTimeout(timeoutBusqueda);

            timeoutBusqueda = setTimeout(() => {
                const urlParams = new URLSearchParams(window.location.search);
                const valorBusqueda = this.value.trim();

                if (valorBusqueda) {
                    urlParams.set('buscar', valorBusqueda);
                } else {
                    urlParams.delete('buscar');
                }
                urlParams.delete('pagina');

                window.location.href = window.location.pathname + '?' + urlParams.toString();
            }, 800);
        });
    }
});
