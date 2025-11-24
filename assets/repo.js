// Filtro y PDF de pedidos
document.getElementById('btnFiltrarModal')?.addEventListener('click', () => {
    const fechaInicio = document.getElementById('modal_fecha_inicio')?.value;
    const fechaFin = document.getElementById('modal_fecha_fin')?.value;
    const estado = document.getElementById('modal_estado')?.value?.toLowerCase();

    document.querySelectorAll('#tablaPedidosModal tbody tr').forEach(row => {
        const fecha = row.dataset.fecha;
        const estadoRow = row.dataset.estado;
        let mostrar = true;
        if (fechaInicio && fecha < fechaInicio) mostrar = false;
        if (fechaFin && fecha > fechaFin) mostrar = false;
        if (estado && estadoRow !== estado) mostrar = false;
        row.style.display = mostrar ? '' : 'none';
    });
});

document.getElementById('selectAll')?.addEventListener('change', function() {
    document.querySelectorAll('#tablaPedidosModal .select-row').forEach(cb => cb.checked = this.checked);
});

document.getElementById('formPdfPedidos')?.addEventListener('submit', function(e) {
    const seleccionados = Array.from(document.querySelectorAll('#tablaPedidosModal tbody tr'))
        .filter(row => $(row).is(':visible'))
        .map(row => row.querySelector('.select-row'))
        .filter(cb => cb && cb.checked)
        .map(cb => cb.value);

    document.getElementById('pdf_ids').value = seleccionados.join(',');
    if (seleccionados.length === 0) {
        e.preventDefault();
        alert('Selecciona al menos un pedido para generar el PDF.');
    }
});

// Filtro y PDF de usuarios
document.getElementById('btnFiltrarModalUsuarios')?.addEventListener('click', () => {
    const tipo = document.getElementById('modal_estado_usuarios')?.value.toLowerCase();
    document.querySelectorAll('#tablaUsuariosModal tbody tr').forEach(row => {
        const tipoRow = row.dataset.tipo;
        row.style.display = (!tipo || tipoRow === tipo) ? '' : 'none';
    });
});

document.getElementById('selectAllUsuarios')?.addEventListener('change', function() {
    document.querySelectorAll('#tablaUsuariosModal .select-row').forEach(cb => cb.checked = this.checked);
});

document.getElementById('formPdfUsuarios')?.addEventListener('submit', function(e) {
    const seleccionados = Array.from(document.querySelectorAll('#tablaUsuariosModal tbody tr'))
        .filter(row => $(row).is(':visible'))
        .map(row => row.querySelector('.select-row'))
        .filter(cb => cb && cb.checked)
        .map(cb => cb.value);

    document.getElementById('pdf_ids_usuarios').value = seleccionados.join(',');
    document.getElementById('tipoSeleccionado').value = document.getElementById('modal_estado_usuarios').value;
    if (seleccionados.length === 0) {
        e.preventDefault();
        alert('Selecciona al menos un usuario para generar el PDF.');
    }
});

// Filtro y PDF de flores / inventario
document.getElementById('btnFiltrarModalFlores')?.addEventListener('click', () => {
    const estado = document.getElementById('modal_estado_flores')?.value?.toLowerCase();
    document.querySelectorAll('#tablaFloresModal tbody tr').forEach(row => {
        const estadoRow = row.dataset.estado;
        row.style.display = (!estado || estadoRow === estado) ? '' : 'none';
    });
});

document.getElementById('selectAllFlores')?.addEventListener('change', function() {
    document.querySelectorAll('#tablaFloresModal .select-row').forEach(cb => cb.checked = this.checked);
});

document.getElementById('formPdfFlores')?.addEventListener('submit', function(e) {
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

// Filtro y PDF de pagos
document.getElementById('btnFiltrarPagos')?.addEventListener('click', () => {
    const fechaInicio = document.getElementById('modal_fecha_inicio_pagos')?.value;
    const fechaFin = document.getElementById('modal_fecha_fin_pagos')?.value;
    const estado = document.getElementById('modal_estado_pagos')?.value?.toLowerCase();

    document.querySelectorAll('#tablaPagosModal tbody tr').forEach(row => {
        const fecha = row.dataset.fecha;
        const estadoRow = row.dataset.estado;
        let mostrar = true;
        if (fechaInicio && fecha < fechaInicio) mostrar = false;
        if (fechaFin && fecha > fechaFin) mostrar = false;
        if (estado && estadoRow !== estado) mostrar = false;
        row.style.display = mostrar ? '' : 'none';
    });
});

document.getElementById('selectAllPagos')?.addEventListener('change', function() {
    document.querySelectorAll('#tablaPagosModal .select-row').forEach(cb => cb.checked = this.checked);
});

document.getElementById('formPdfPagos')?.addEventListener('submit', function(e) {
    const seleccionados = Array.from(document.querySelectorAll('#tablaPagosModal tbody tr'))
        .filter(row => $(row).is(':visible'))
        .map(row => row.querySelector('.select-row'))
        .filter(cb => cb && cb.checked)
        .map(cb => cb.value);

    document.getElementById('pdf_ids_pagos').value = seleccionados.join(',');

    if (seleccionados.length === 0) {
        e.preventDefault();
        alert('Selecciona al menos un pago para generar el PDF.');
    }
});

