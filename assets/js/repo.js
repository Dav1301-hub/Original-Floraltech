// JS dinámico para reportes reactivos
function filtrarTabla(selector, predicate) {
    document.querySelectorAll(`${selector} tbody tr`).forEach(row => {
        row.style.display = predicate(row) ? '' : 'none';
    });
}

// --- FUNCIONES DE ACTUALIZACIÓN DE GRÁFICOS ---

function actualizarGraficoVentas() {
    if (!window.chartVentas) return;
    const filas = document.querySelectorAll('#tablaPedidosModal tbody tr:not([style*="display: none"])');
    const ventasPorFecha = {};
    let totalMonto = 0;
    let totalPedidos = 0;

    filas.forEach(fila => {
        const fecha = fila.dataset.fecha;
        const monto = parseFloat(fila.dataset.monto) || 0;
        totalMonto += monto;
        totalPedidos++;
        if (fecha) {
            ventasPorFecha[fecha] = (ventasPorFecha[fecha] || 0) + monto;
        }
    });

    // Actualizar Resumen Ventas
    const lblTotal = document.getElementById('totalVentasVisible');
    const lblCant = document.getElementById('cantPedidosVisible');
    if (lblTotal) lblTotal.textContent = '$' + totalMonto.toLocaleString('en-US', { minimumFractionDigits: 2 });
    if (lblCant) lblCant.textContent = totalPedidos;

    const sortedDates = Object.keys(ventasPorFecha).sort();
    const labels = sortedDates.map(d => d.split('-').reverse().slice(0, 2).join('/'));
    const data = sortedDates.map(d => ventasPorFecha[d]);

    window.chartVentas.data.labels = labels;
    window.chartVentas.data.datasets[0].data = data;
    window.chartVentas.update();
}

function actualizarGraficoUsuarios() {
    if (!window.chartUsuarios) return;
    const filas = document.querySelectorAll('#tablaUsuariosModal tbody tr:not([style*="display: none"])');
    const usuariosPorRol = {};
    let activos = 0;

    filas.forEach(fila => {
        const rol = fila.querySelector('td:last-child .badge')?.textContent || 'Sin rol';
        const estaActivo = (fila.cells[6]?.textContent || '').trim().toLowerCase() === 'si';
        if (estaActivo) activos++;
        usuariosPorRol[rol] = (usuariosPorRol[rol] || 0) + 1;
    });

    const total = filas.length;
    const inactivos = total - activos;

    // Actualizar Resumen Usuarios
    if (document.getElementById('usuariosActivosCount')) document.getElementById('usuariosActivosCount').textContent = activos;
    if (document.getElementById('usuariosTotalCount')) document.getElementById('usuariosTotalCount').textContent = total;
    if (document.getElementById('usuariosFooterTotal')) document.getElementById('usuariosFooterTotal').textContent = total;
    if (document.getElementById('usuariosFooterInactivos')) document.getElementById('usuariosFooterInactivos').textContent = inactivos;

    window.chartUsuarios.data.labels = Object.keys(usuariosPorRol);
    window.chartUsuarios.data.datasets[0].data = Object.values(usuariosPorRol);
    window.chartUsuarios.update();
}

function actualizarGraficoInventario() {
    if (!window.chartInventario) return;
    const filas = document.querySelectorAll('#tablaFloresModal tbody tr:not([style*="display: none"])');
    const dataArr = [];
    let stockTotal = 0;
    let valorTotal = 0;

    filas.forEach(fila => {
        const stock = parseInt(fila.dataset.stock) || 0;
        const valTotText = fila.cells[9]?.textContent.replace('$', '').replace(',', '');
        const valTot = parseFloat(valTotText) || 0;

        stockTotal += stock;
        valorTotal += valTot;

        dataArr.push({
            producto: fila.dataset.producto || 'N/D',
            stock: stock
        });
    });

    // Actualizar Resumen Inventario
    if (document.getElementById('invTotalProductos')) document.getElementById('invTotalProductos').textContent = filas.length;
    if (document.getElementById('invStockTotal')) document.getElementById('invStockTotal').textContent = stockTotal.toLocaleString();
    if (document.getElementById('invValorTotalH4')) document.getElementById('invValorTotalH4').textContent = '$' + valorTotal.toLocaleString('en-US', { minimumFractionDigits: 2 });

    // Ordenar y tomar top 10
    dataArr.sort((a, b) => b.stock - a.stock);
    const top10 = dataArr.slice(0, 10);

    window.chartInventario.data.labels = top10.map(i => i.producto.substring(0, 15));
    window.chartInventario.data.datasets[0].data = top10.map(i => i.stock);
    window.chartInventario.update();
}

function actualizarGraficoPagos() {
    if (!window.chartPagos) return;
    const filas = document.querySelectorAll('#tablaPagosModal tbody tr:not([style*="display: none"])');
    const pagosPorEstado = {};
    let completados = 0;
    let pendientes = 0;

    filas.forEach(fila => {
        const estadoRaw = fila.cells[7]?.textContent?.trim() || 'Sin estado';
        const monto = parseFloat(fila.dataset.monto) || 0;

        if (estadoRaw.toLowerCase() === 'completado') completados += monto;
        if (estadoRaw.toLowerCase() === 'pendiente') pendientes += monto;

        pagosPorEstado[estadoRaw] = (pagosPorEstado[estadoRaw] || 0) + monto;
    });

    // Actualizar Resumen Pagos
    if (document.getElementById('pagosCompletadosH4')) document.getElementById('pagosCompletadosH4').textContent = '$' + completados.toLocaleString('en-US', { minimumFractionDigits: 2 });
    if (document.getElementById('pagosPendientesH4')) document.getElementById('pagosPendientesH4').textContent = '$' + pendientes.toLocaleString('en-US', { minimumFractionDigits: 2 });
    if (document.getElementById('pagosTotalH4')) document.getElementById('pagosTotalH4').textContent = '$' + (completados + pendientes).toLocaleString('en-US', { minimumFractionDigits: 2 });

    window.chartPagos.data.labels = Object.keys(pagosPorEstado);
    window.chartPagos.data.datasets[0].data = Object.values(pagosPorEstado);
    window.chartPagos.update();
}

// --- EVENTOS DE FILTRADO ---

document.getElementById('btnFiltrarModal')?.addEventListener('click', () => {
    const inicio = document.getElementById('modal_fecha_inicio')?.value;
    const fin = document.getElementById('modal_fecha_fin')?.value;
    const estado = document.getElementById('modal_estado')?.value?.toLowerCase();

    filtrarTabla('#tablaPedidosModal', row => {
        const fecha = row.dataset.fecha || '';
        const estadoRow = (row.dataset.estado || '').toLowerCase();
        if (inicio && (!fecha || fecha < inicio)) return false;
        if (fin && (!fecha || fecha > fin)) return false;
        if (estado && estadoRow !== estado) return false;
        return true;
    });

    actualizarGraficoVentas();
});

document.getElementById('btnFiltrarModalUsuarios')?.addEventListener('click', () => {
    const tipo = document.getElementById('modal_rol_usuarios')?.value?.toLowerCase();
    filtrarTabla('#tablaUsuariosModal', row => {
        const tipoRow = (row.dataset.tipo || '').toLowerCase();
        return !tipo || tipoRow === tipo;
    });
    actualizarGraficoUsuarios();
});

document.getElementById('btnFiltrarModalFlores')?.addEventListener('click', () => {
    const estado = document.getElementById('modal_estado_flores')?.value?.toLowerCase();
    const categoria = document.getElementById('modal_categoria_flores')?.value?.toLowerCase();
    filtrarTabla('#tablaFloresModal', row => {
        const estadoRow = (row.dataset.estado || '').toLowerCase();
        const categoriaRow = (row.dataset.categoria || '').toLowerCase();
        if (estado && estadoRow !== estado) return false;
        if (categoria && !categoriaRow.includes(categoria)) return false;
        return true;
    });
    actualizarGraficoInventario();
});

document.getElementById('btnFiltrarPagos')?.addEventListener('click', () => {
    const inicio = document.getElementById('modal_fecha_inicio_pagos')?.value;
    const fin = document.getElementById('modal_fecha_fin_pagos')?.value;
    const estado = document.getElementById('modal_estado_pagos')?.value?.toLowerCase();
    filtrarTabla('#tablaPagosModal', row => {
        const fecha = row.dataset.fecha || '';
        const estadoRow = (row.dataset.estado || '').toLowerCase();
        if (inicio && (!fecha || fecha < inicio)) return false;
        if (fin && (!fecha || fecha > fin)) return false;
        if (estado && estadoRow !== estado) return false;
        return true;
    });
    actualizarGraficoPagos();
});

// Seleccionar todo y PDF scripts...
document.getElementById('selectAll')?.addEventListener('change', function () {
    document.querySelectorAll('#tablaPedidosModal .select-row').forEach(cb => cb.checked = this.checked);
});

document.getElementById('formPdfPedidos')?.addEventListener('submit', function (e) {
    const seleccionados = Array.from(document.querySelectorAll('#tablaPedidosModal tbody tr'))
        .filter(row => row.style.display !== 'none')
        .map(row => row.querySelector('.select-row'))
        .filter(cb => cb && cb.checked)
        .map(cb => cb.value);
    document.getElementById('pdf_ids').value = seleccionados.join(',');
    if (seleccionados.length === 0) {
        e.preventDefault();
        alert('Selecciona al menos un pedido para generar el PDF.');
    }
});

document.getElementById('selectAllUsuarios')?.addEventListener('change', function () {
    document.querySelectorAll('#tablaUsuariosModal .select-row').forEach(cb => cb.checked = this.checked);
});

document.getElementById('formPdfUsuarios')?.addEventListener('submit', function (e) {
    const seleccionados = Array.from(document.querySelectorAll('#tablaUsuariosModal tbody tr'))
        .filter(row => row.style.display !== 'none')
        .map(row => row.querySelector('.select-row'))
        .filter(cb => cb && cb.checked)
        .map(cb => cb.value);
    document.getElementById('pdf_ids_usuarios').value = seleccionados.join(',');
    document.getElementById('tipoSeleccionado').value = document.getElementById('modal_rol_usuarios').value;
    if (seleccionados.length === 0) {
        e.preventDefault();
        alert('Selecciona al menos un usuario para generar el PDF.');
    }
});

document.getElementById('selectAllFlores')?.addEventListener('change', function () {
    document.querySelectorAll('#tablaFloresModal .select-row').forEach(cb => cb.checked = this.checked);
});

document.getElementById('formPdfFlores')?.addEventListener('submit', function (e) {
    const seleccionados = Array.from(document.querySelectorAll('#tablaFloresModal tbody tr'))
        .filter(row => row.style.display !== 'none')
        .map(row => row.querySelector('.select-row'))
        .filter(cb => cb && cb.checked)
        .map(cb => cb.value);
    if (seleccionados.length === 0) {
        e.preventDefault();
        alert('Debes seleccionar al menos una flor para generar el PDF.');
        return;
    }
    document.getElementById('pdf_ids_flores').value = seleccionados.join(',');
});

document.getElementById('selectAllPagos')?.addEventListener('change', function () {
    document.querySelectorAll('#tablaPagosModal .select-row').forEach(cb => cb.checked = this.checked);
});

document.getElementById('formPdfPagos')?.addEventListener('submit', function (e) {
    const seleccionados = Array.from(document.querySelectorAll('#tablaPagosModal tbody tr'))
        .filter(row => row.style.display !== 'none')
        .map(row => row.querySelector('.select-row'))
        .filter(cb => cb && cb.checked)
        .map(cb => cb.value);
    document.getElementById('pdf_ids_pagos').value = seleccionados.join(',');
    if (seleccionados.length === 0) {
        e.preventDefault();
        alert('Selecciona al menos un pago para generar el PDF.');
    }
});

// --- INICIALIZACIÓN DE MODALES ---
document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('tablaModal')?.addEventListener('shown.bs.modal', actualizarGraficoVentas);
    document.getElementById('modalUsuario')?.addEventListener('shown.bs.modal', actualizarGraficoUsuarios);
    document.getElementById('tablaModalFlores')?.addEventListener('shown.bs.modal', actualizarGraficoInventario);
    document.getElementById('modalPagos')?.addEventListener('shown.bs.modal', actualizarGraficoPagos);
});
