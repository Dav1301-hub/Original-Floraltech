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

// Funciones para gestión de inventario
function editarFlor(id) {
    console.log('Editando flor con ID:', id);
    
    // Cargar los datos del producto
    fetch(`?ctrl=Cinventario&accion=obtener_producto&id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const producto = data.producto;
                
                // Llenar los campos del modal con los datos del producto
                document.getElementById('editar_producto_id').value = id;
                document.getElementById('editar_nombre_producto').value = producto.producto || '';
                document.getElementById('editar_tipo_producto').value = producto.tipo || '';
                document.getElementById('editar_stock').value = producto.stock || 0;
                document.getElementById('editar_precio').value = producto.precio || 0;
                document.getElementById('editar_color').value = producto.color || '';
                document.getElementById('editar_naturaleza').value = producto.naturaleza || '';
                document.getElementById('editar_estado').value = producto.estado || 'Disponible';
                
                // Mostrar el modal
                const modalElement = document.getElementById('modal-editar-producto');
                if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    const modal = new bootstrap.Modal(modalElement);
                    modal.show();
                } else if (typeof $ !== 'undefined') {
                    $(modalElement).modal('show');
                } else {
                    modalElement.style.display = 'block';
                    modalElement.classList.add('show');
                }
            } else {
                console.error('Error al obtener datos del producto:', data.message);
                mostrarMensajeError('Error al cargar los datos del producto: ' + (data.message || 'Error desconocido'));
            }
        })
        .catch(error => {
            console.error('Error en la petición:', error);
            // Fallback: abrir modal con campos vacíos
            document.getElementById('editar_producto_id').value = id;
            const modalElement = document.getElementById('modal-editar-producto');
            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                const modal = new bootstrap.Modal(modalElement);
                modal.show();
            } else if (typeof $ !== 'undefined') {
                $(modalElement).modal('show');
            } else {
                modalElement.style.display = 'block';
                modalElement.classList.add('show');
            }
        });
}

function eliminarFlor(id) {
    console.log('Eliminando flor con ID:', id);
    
    // Crear modal de confirmación de eliminación dinámicamente
    const modalHTML = `
        <div class="modal fade" id="modal-confirmar-eliminacion" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-exclamation-triangle me-2"></i>Confirmar Eliminación
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="text-center">
                            <i class="fas fa-trash-alt text-danger" style="font-size: 3rem; margin-bottom: 1rem;"></i>
                            <h5>¿Estás seguro de eliminar este producto?</h5>
                            <p class="text-muted">Esta acción no se puede deshacer. El producto será eliminado permanentemente del inventario.</p>
                            <div class="alert alert-warning">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Advertencia:</strong> Se perderán todos los datos del producto incluyendo su historial.
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>Cancelar
                        </button>
                        <button type="button" class="btn btn-danger" id="confirmar-eliminacion">
                            <i class="fas fa-trash me-1"></i>Sí, Eliminar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Eliminar modal existente si existe
    const existingModal = document.getElementById('modal-confirmar-eliminacion');
    if (existingModal) {
        existingModal.remove();
    }
    
    // Agregar el modal al body
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    
    // Mostrar el modal
    const modalElement = document.getElementById('modal-confirmar-eliminacion');
    let modal;
    
    if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
        modal = new bootstrap.Modal(modalElement);
        modal.show();
    } else if (typeof $ !== 'undefined') {
        $(modalElement).modal('show');
    } else {
        modalElement.style.display = 'block';
        modalElement.classList.add('show');
    }
    
    // Event listener para el botón de confirmar
    document.getElementById('confirmar-eliminacion').addEventListener('click', function() {
        // Cerrar modal
        if (modal && typeof bootstrap !== 'undefined') {
            modal.hide();
        } else if (typeof $ !== 'undefined') {
            $(modalElement).modal('hide');
        } else {
            modalElement.style.display = 'none';
            modalElement.classList.remove('show');
        }
        
        // Ejecutar eliminación
        procesarEliminarProducto(id);
        
        // Limpiar modal después de un breve delay
        setTimeout(() => {
            if (modalElement) {
                modalElement.remove();
            }
        }, 500);
    });
}

// Función auxiliar para procesar la eliminación
function procesarEliminarProducto(id) {
    fetch(`?ctrl=Cinventario&accion=eliminar_producto&id=${id}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Mostrar mensaje de éxito con modal
            mostrarMensajeExito('✅ Producto eliminado correctamente');
            // Recargar la página después de un breve delay
            setTimeout(() => {
                location.reload();
            }, 1500);
        } else {
            console.error('Error al eliminar producto:', data.message);
            mostrarMensajeError('❌ Error al eliminar el producto: ' + (data.message || 'Error desconocido'));
        }
    })
    .catch(error => {
        console.error('Error en la petición:', error);
        mostrarMensajeError('❌ Error de conexión al servidor');
    });
}

function agregarAInventario(id) {
    console.log('Agregando stock al producto con ID:', id);
    
    // Primero obtener los datos del producto para mostrar en el modal
    fetch(`?ctrl=Cinventario&accion=obtener_producto&id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const producto = data.producto;
                
                // Llenar los campos del modal
                document.getElementById('stock_producto_id').value = id;
                document.getElementById('stock_nombre_producto').textContent = producto.producto || 'Producto';
                document.getElementById('stock_actual').textContent = producto.stock || 0;
                document.getElementById('cantidad_agregar').value = '';
                
                // Limpiar textarea de motivo
                const motivoTextarea = document.querySelector('#form-agregar-stock textarea[name="motivo"]');
                if (motivoTextarea) {
                    motivoTextarea.value = '';
                }
                
                // Mostrar el modal
                const modalElement = document.getElementById('modal-agregar-stock');
                if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    const modal = new bootstrap.Modal(modalElement);
                    modal.show();
                } else if (typeof $ !== 'undefined') {
                    $(modalElement).modal('show');
                } else {
                    modalElement.style.display = 'block';
                    modalElement.classList.add('show');
                }
                
                // Enfocar el campo de cantidad
                setTimeout(() => {
                    const cantidadInput = document.getElementById('cantidad_agregar');
                    if (cantidadInput) {
                        cantidadInput.focus();
                    }
                }, 500);
                
            } else {
                console.error('Error al obtener datos del producto:', data.message);
                // Fallback: crear modal genérico si no se pueden obtener los datos
                crearModalAgregarStockGenerico(id);
            }
        })
        .catch(error => {
            console.error('Error al obtener datos del producto:', error);
            // Fallback: crear modal genérico
            crearModalAgregarStockGenerico(id);
        });
}

// Función fallback para crear modal genérico de agregar stock
function crearModalAgregarStockGenerico(id) {
    const modalHTML = `
        <div class="modal fade" id="modal-agregar-stock-generico" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-info text-white">
                        <h5 class="modal-title"><i class="fas fa-plus me-2"></i>Agregar Stock al Inventario</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="text-center mb-4">
                            <h6>Producto ID: <span class="fw-bold text-info">${id}</span></h6>
                            <p class="text-muted">No se pudieron cargar los detalles del producto</p>
                        </div>
                        
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label"><i class="fas fa-plus me-1"></i>Cantidad a Agregar *</label>
                                <input type="number" class="form-control" id="cantidad_agregar_generico" min="1" required>
                                <small class="text-muted">Ingresa la cantidad de unidades que deseas agregar al inventario</small>
                            </div>
                            
                            <div class="col-12">
                                <label class="form-label"><i class="fas fa-comment me-1"></i>Motivo (Opcional)</label>
                                <textarea class="form-control" id="motivo_generico" rows="2" placeholder="Ej: Reposición, Compra nueva, Devolución..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>Cancelar
                        </button>
                        <button type="button" class="btn btn-info" id="confirmar-agregar-stock">
                            <i class="fas fa-plus me-1"></i>Agregar Stock
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Eliminar modal existente si existe
    const existingModal = document.getElementById('modal-agregar-stock-generico');
    if (existingModal) {
        existingModal.remove();
    }
    
    // Agregar el modal al body
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    
    // Mostrar el modal
    const modalElement = document.getElementById('modal-agregar-stock-generico');
    let modal;
    
    if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
        modal = new bootstrap.Modal(modalElement);
        modal.show();
    } else if (typeof $ !== 'undefined') {
        $(modalElement).modal('show');
    } else {
        modalElement.style.display = 'block';
        modalElement.classList.add('show');
    }
    
    // Enfocar el campo de cantidad
    setTimeout(() => {
        const cantidadInput = document.getElementById('cantidad_agregar_generico');
        if (cantidadInput) {
            cantidadInput.focus();
        }
    }, 500);
    
    // Event listener para el botón de confirmar
    document.getElementById('confirmar-agregar-stock').addEventListener('click', function() {
        const cantidad = document.getElementById('cantidad_agregar_generico').value;
        const motivo = document.getElementById('motivo_generico').value || '';
        
        if (cantidad && parseInt(cantidad) > 0) {
            // Cerrar modal
            if (modal && typeof bootstrap !== 'undefined') {
                modal.hide();
            } else if (typeof $ !== 'undefined') {
                $(modalElement).modal('hide');
            } else {
                modalElement.style.display = 'none';
                modalElement.classList.remove('show');
            }
            
            // Procesar agregar stock
            procesarAgregarStock(id, cantidad, motivo);
            
            // Limpiar modal después de un breve delay
            setTimeout(() => {
                if (modalElement) {
                    modalElement.remove();
                }
            }, 500);
        } else {
            mostrarMensajeError('Por favor, ingresa una cantidad válida (número mayor a 0)');
        }
    });
}

// Función auxiliar para procesar la adición de stock
function procesarAgregarStock(id, cantidad, motivo = '') {
    fetch(`?ctrl=Cinventario&accion=agregar_stock&id=${id}&cantidad=${cantidad}&motivo=${encodeURIComponent(motivo)}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            mostrarMensajeExito(`✅ Se agregaron ${cantidad} unidades al stock correctamente`);
            // Recargar la página después de un breve delay
            setTimeout(() => {
                location.reload();
            }, 1500);
        } else {
            console.error('Error al agregar stock:', data.message);
            mostrarMensajeError('❌ Error al agregar stock: ' + (data.message || 'Error desconocido'));
        }
    })
    .catch(error => {
        console.error('Error en la petición:', error);
        mostrarMensajeError('❌ Error de conexión al servidor');
    });
}

// Función para mostrar mensajes de éxito con modal
function mostrarMensajeExito(mensaje) {
    const modalHTML = `
        <div class="modal fade" id="modal-mensaje-exito" tabindex="-1">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-check-circle me-2"></i>Éxito
                        </h5>
                    </div>
                    <div class="modal-body text-center">
                        <i class="fas fa-check-circle text-success" style="font-size: 3rem; margin-bottom: 1rem;"></i>
                        <p class="mb-0">${mensaje}</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" data-bs-dismiss="modal">
                            <i class="fas fa-thumbs-up me-1"></i>¡Perfecto!
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    mostrarModalTemporal(modalHTML, 'modal-mensaje-exito', 2000);
}

// Función para mostrar mensajes de error con modal
function mostrarMensajeError(mensaje) {
    const modalHTML = `
        <div class="modal fade" id="modal-mensaje-error" tabindex="-1">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-exclamation-triangle me-2"></i>Error
                        </h5>
                    </div>
                    <div class="modal-body text-center">
                        <i class="fas fa-times-circle text-danger" style="font-size: 3rem; margin-bottom: 1rem;"></i>
                        <p class="mb-0">${mensaje}</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>Entendido
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    mostrarModalTemporal(modalHTML, 'modal-mensaje-error');
}

// Función auxiliar para mostrar modales temporales
function mostrarModalTemporal(modalHTML, modalId, autoCloseTime = 0) {
    // Eliminar modal existente si existe
    const existingModal = document.getElementById(modalId);
    if (existingModal) {
        existingModal.remove();
    }
    
    // Agregar el modal al body
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    
    // Mostrar el modal
    const modalElement = document.getElementById(modalId);
    let modal;
    
    if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
        modal = new bootstrap.Modal(modalElement);
        modal.show();
    } else if (typeof $ !== 'undefined') {
        $(modalElement).modal('show');
    } else {
        modalElement.style.display = 'block';
        modalElement.classList.add('show');
    }
    
    // Auto cerrar si se especifica tiempo
    if (autoCloseTime > 0) {
        setTimeout(() => {
            if (modal && typeof bootstrap !== 'undefined') {
                modal.hide();
            } else if (typeof $ !== 'undefined') {
                $(modalElement).modal('hide');
            } else {
                modalElement.style.display = 'none';
                modalElement.classList.remove('show');
            }
            
            // Limpiar modal después de animación
            setTimeout(() => {
                if (modalElement) {
                    modalElement.remove();
                }
            }, 500);
        }, autoCloseTime);
    }
    
    // Event listener para limpiar modal al cerrar
    modalElement.addEventListener('hidden.bs.modal', function() {
        setTimeout(() => {
            if (modalElement) {
                modalElement.remove();
            }
        }, 100);
    });
}

// Event listeners para los formularios de los modales
document.addEventListener('DOMContentLoaded', function() {
    // Event listener para el formulario de editar producto
    const formEditarProducto = document.getElementById('form-editar-producto');
    if (formEditarProducto) {
        formEditarProducto.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('?ctrl=Cinventario', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    mostrarMensajeExito('✅ Producto actualizado correctamente');
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    mostrarMensajeError('❌ Error al actualizar el producto: ' + (data.message || 'Error desconocido'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarMensajeError('❌ Error de conexión al servidor');
            });
        });
    }
    
    // Event listener para el formulario de agregar stock
    const formAgregarStock = document.getElementById('form-agregar-stock');
    if (formAgregarStock) {
        formAgregarStock.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const id = formData.get('producto_id');
            const cantidad = formData.get('cantidad');
            const motivo = formData.get('motivo') || '';
            
            if (cantidad && parseInt(cantidad) > 0) {
                procesarAgregarStock(id, cantidad, motivo);
                
                // Cerrar el modal
                const modalElement = document.getElementById('modal-agregar-stock');
                if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    const modal = bootstrap.Modal.getInstance(modalElement);
                    if (modal) modal.hide();
                } else if (typeof $ !== 'undefined') {
                    $(modalElement).modal('hide');
                } else {
                    modalElement.style.display = 'none';
                    modalElement.classList.remove('show');
                }
            } else {
                mostrarMensajeError('Por favor, ingresa una cantidad válida (número mayor a 0)');
            }
        });
    }
});