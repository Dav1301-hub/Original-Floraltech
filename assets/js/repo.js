// JS simple para filtros en modales de reportes
function filtrarTabla(selector, predicate) {
    document.querySelectorAll(`${selector} tbody tr`).forEach(row => {
        row.style.display = predicate(row) ? '' : 'none';
    });
}

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
});

// Seleccionar todo en pedidos
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

document.getElementById('btnFiltrarModalUsuarios')?.addEventListener('click', () => {
    const tipo = document.getElementById('modal_rol_usuarios')?.value?.toLowerCase();
    filtrarTabla('#tablaUsuariosModal', row => {
        const tipoRow = (row.dataset.tipo || '').toLowerCase();
        return !tipo || tipoRow === tipo;
    });
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
