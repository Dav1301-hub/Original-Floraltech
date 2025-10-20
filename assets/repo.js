// 🔹 FILTRO Y PDF DE PEDIDOS

document.getElementById('btnFiltrarModal')?.addEventListener('click', function() {
    const fechaInicio = document.getElementById('modal_fecha_inicio')?.value;
    const fechaFin = document.getElementById('modal_fecha_fin')?.value;
    const estado = document.getElementById('modal_estado')?.value?.toLowerCase();

    const rows = document.querySelectorAll('#tablaPedidosModal tbody tr');
    if (rows.length === 0) return; // si no es el modal de pedidos, no hace nada

    rows.forEach(row => {
        const fechaPedidoTexto = row.querySelector('td:nth-child(4)')?.textContent.trim();
        const partes = fechaPedidoTexto?.split('/');
        const fechaPedido = partes ? `${partes[2]}-${partes[1]}-${partes[0]}` : '';
        const estadoPedido = row.querySelector('td:nth-child(7)')?.textContent.trim().toLowerCase();

        let mostrar = true;
        if (fechaInicio && fechaPedido < fechaInicio) mostrar = false;
        if (fechaFin && fechaPedido > fechaFin) mostrar = false;
        if (estado && estadoPedido !== estado) mostrar = false;

        row.style.display = mostrar ? '' : 'none';
    });
});

// Selección general de checkboxes en pedidos
document.getElementById('selectAll')?.addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.select-row');
    checkboxes.forEach(cb => cb.checked = this.checked);
});

// Envío del PDF de pedidos
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
    console.log('Pedidos seleccionados:', seleccionados);
});

// 🔹 FILTRO Y PDF DE USUARIOS

document.getElementById('btnFiltrarModalUsuarios')?.addEventListener('click', function() {
    const tipoSeleccionado = document.getElementById('modal_estado_usuarios')?.value.toLowerCase();
    const filas = document.querySelectorAll('#tablaUsuariosModal tbody tr');
    if (filas.length === 0) return;

    filas.forEach(fila => {
        const tipoUsuario = fila.querySelector('td:nth-child(8)')?.textContent.trim().toLowerCase();
        fila.style.display = (!tipoSeleccionado || tipoUsuario === tipoSeleccionado) ? '' : 'none';
    });
});

// Selección general de checkboxes en usuarios
document.getElementById('selectAllUsuarios')?.addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('#tablaUsuariosModal .select-row');
    checkboxes.forEach(cb => cb.checked = this.checked);
});

// Envío del PDF de usuarios
document.getElementById('formPdfUsuarios')?.addEventListener('submit', function(e) {
    const seleccionados = Array.from(document.querySelectorAll('#tablaUsuariosModal tbody tr'))
        .filter(fila => $(fila).is(':visible')) // usa jQuery igual que en pedidos
        .map(fila => fila.querySelector('.select-row'))
        .filter(cb => cb && cb.checked)
        .map(cb => cb.value);

    document.getElementById('pdf_ids_usuarios').value = seleccionados.join(',');
    document.getElementById('tipoSeleccionado').value = document.getElementById('modal_estado_usuarios').value;

    if (seleccionados.length === 0) {
        e.preventDefault();
        alert('Selecciona al menos un usuario para generar el PDF.');
    }

    console.log('Usuarios seleccionados:', seleccionados);
});



// ===============================
// 🌸 FILTRO Y PDF DE FLORES / INVENTARIO
// ===============================
document.getElementById('btnFiltrarModalFlores')?.addEventListener('click', function() {
    const estado = document.getElementById('modal_estado_flores')?.value?.toLowerCase();
    const filas = document.querySelectorAll('#tablaFloresModal tbody tr');
    if (filas.length === 0) return;

    filas.forEach(fila => {
        const estadoFlor = fila.querySelector('td:nth-child(8)')?.textContent.trim().toLowerCase();;
        fila.style.display = (!estado || estadoFlor === estado) ? '' : 'none';
    });
});

// Selección general de checkboxes en flores
document.getElementById('selectAllFlores')?.addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('#tablaFloresModal .select-row');
    checkboxes.forEach(cb => cb.checked = this.checked);
});

// Envío del PDF de flores
document.getElementById('formPdfFlores')?.addEventListener('submit', function(e) {
    const filas = document.querySelectorAll('#tablaFloresModal tbody tr');
    const seleccionados = Array.from(filas)
        .filter(fila => fila.style.display !== 'none') // solo visibles (filtrados)
        .map(fila => fila.querySelector('.select-row'))
        .filter(cb => cb && cb.checked)
        .map(cb => cb.value);

    if (seleccionados.length === 0) {
        e.preventDefault();
        alert('⚠️ Debes seleccionar al menos una flor para generar el PDF.');
        return;
    }

    document.getElementById('pdf_ids_flores').value = seleccionados.join(',');
    console.log('Flores seleccionadas:', seleccionados);
});
