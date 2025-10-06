document.getElementById('btnFiltrarModal').addEventListener('click', function() {
    const fechaInicio = document.getElementById('modal_fecha_inicio').value;
    const fechaFin = document.getElementById('modal_fecha_fin').value;
    const estado = document.getElementById('modal_estado').value.toLowerCase();

    const rows = document.querySelectorAll('#tablaPedidosModal tbody tr');
    rows.forEach(row => {
        // Cambiado a la columna 4 (fecha)
        const fechaPedidoTexto = row.querySelector('td:nth-child(4)').textContent.trim();
        const partes = fechaPedidoTexto.split('/');
        // Convierte a yyyy-mm-dd para comparar
        const fechaPedido = `${partes[2]}-${partes[1]}-${partes[0]}`;

        const estadoPedido = row.querySelector('td:nth-child(7)').textContent.trim().toLowerCase();

        let mostrar = true;

        if (fechaInicio && fechaPedido < fechaInicio) mostrar = false;
        if (fechaFin && fechaPedido > fechaFin) mostrar = false;
        if (estado && estadoPedido !== estado) mostrar = false;

        row.style.display = mostrar ? '' : 'none';
    });
});




document.getElementById('selectAll')?.addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.select-row');
    checkboxes.forEach(cb => cb.checked = this.checked);
});


$('#tablaPedidosModal').DataTable({
  columnDefs: [
    { orderable: false, targets: 0 }, // desactiva el orden en la columna de checkboxes
    { searchable: false, targets: 0 }  // evita que interfiera con la búsqueda
  ]
});

document.getElementById('formPdfPedidos').addEventListener('submit', function(e) {
    // Solo checkboxes seleccionados y VISIBLES (con DataTables)
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
    // Para depuración:
    console.log('IDs seleccionados:', seleccionados);
});