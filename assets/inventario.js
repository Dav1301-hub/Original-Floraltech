const hamburger = document.querySelector("#toggle-btn");



// Inicializa DataTable con paginador y selector de cantidad de filas
document.addEventListener("DOMContentLoaded", function () {
    const tabla = $('#tabla-inventario').DataTable({
        paging: true,
        lengthChange: true,
        searching: true, // Desactiva el buscador de DataTables
        pageLength: 5,
        lengthMenu: [5, 10, 25, 50, 100],
        language: {
            lengthMenu: "Mostrar _MENU_ filas por página",
            zeroRecords: "No se encontraron registros",
            info: "Mostrando página _PAGE_ de _PAGES_",
            infoEmpty: "No hay registros disponibles",
            infoFiltered: "(filtrado de _MAX_ registros totales)",
            paginate: {
                previous: "Anterior",
                next: "Siguiente"
            }
        }
    });

   
    const buscador = document.querySelector('.buscador input');
    if (buscador) {
        buscador.addEventListener('input', function (){
            tabla.search(buscador.value).draw();
        });
    }
});


function abrirproducto(){ 
    document.getElementById('modal-nuevo-producto').style.display = 'block';
    document.getElementById('modal-backdrop').classList.add('show');
}
function cerrarproducto(){
    document.getElementById('modal-nuevo-producto').style.display = 'none';
    document.getElementById('modal-backdrop').classList.remove('show');
}

function abrirproveedor(){
    document.getElementById('modal-proveedores').style.display ='block';
    document.getElementById('modal-backdrop').classList.add('show');
}

function cerrarproveedor(){
    document.getElementById('modal-proveedores').style.display ='none';
    document.getElementById('modal-backdrop').classList.remove('show');
}