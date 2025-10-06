const hamburger = document.querySelector("#toggle-btn");



document.addEventListener("DOMContentLoaded", function () {
    // Verificar si existe sistema AJAX personalizado en la página
    const tieneAjaxPersonalizado = document.querySelector('[onclick*="cargarPagina"]') !== null;
    const tablaInventario = document.getElementById('tabla-inventario');
    
    console.log('Tabla inventario encontrada:', !!tablaInventario);
    console.log('Sistema AJAX personalizado detectado:', tieneAjaxPersonalizado);
    
    if (tablaInventario && !tieneAjaxPersonalizado) {
        // Inicializar DataTable solo si no hay sistema AJAX personalizado
        console.log('Inicializando DataTable tradicional...');
        try {
            const tabla = $('#tabla-inventario').DataTable({
                paging: true,
                lengthChange: true,
                searching: true,
                pageLength: 10,
                lengthMenu: [10, 25, 50, 100],
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
        } catch (error) {
            console.error('Error inicializando DataTable:', error);
        }
    } else {
        console.log('Sistema AJAX personalizado detectado o tabla no encontrada');
        
        // Funciones de fallback para paginación si no están definidas
        if (typeof window.cargarPagina === 'undefined') {
            console.log('Definiendo función cargarPagina de fallback...');
            window.cargarPagina = function(page) {
                console.log('Función fallback cargarPagina llamada con página:', page);
                // Recarga tradicional como fallback
                const url = new URL(window.location);
                url.searchParams.set('pagina', page);
                window.location.href = url.toString();
            };
        }
        
        if (typeof window.cambiarElementosPorPagina === 'undefined') {
            console.log('Definiendo función cambiarElementosPorPagina de fallback...');
            window.cambiarElementosPorPagina = function(limit) {
                console.log('Función fallback cambiarElementosPorPagina llamada con límite:', limit);
                const url = new URL(window.location);
                url.searchParams.set('per_page', limit);
                url.searchParams.set('pagina', 1);
                window.location.href = url.toString();
            };
        }
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