/**
 * Manejador de modales para inventario
 * Este script configura los eventos de los botones de acción (editar, stock, eliminar)
 * y carga los datos del producto seleccionado en los modales correspondientes
 */

(function() {
    'use strict';
    
    console.log('🚀 Iniciando manejador de modales de inventario...');
    
    /**
     * Configura los event listeners para los botones de acciones del inventario
     */
    function configurarBotonesAccion() {
        console.log('🔧 Configurando botones de acción...');
        
        // Botones de EDITAR
        const botonesEditar = document.querySelectorAll('.btn-modal-editar');
        console.log(`📝 Encontrados ${botonesEditar.length} botones de editar`);
        
        botonesEditar.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                // Extraer datos del producto desde los data attributes
                const productoData = {
                    id: this.dataset.productoId,
                    nombre: this.dataset.productoNombre,
                    naturaleza: this.dataset.productoNaturaleza || '',
                    color: this.dataset.productoColor || '',
                    stock: this.dataset.productoStock || 0,
                    precio: this.dataset.productoPrecio || 0,
                    estado: this.dataset.productoEstado || 'Disponible'
                };
                
                console.log('📝 Abriendo modal de edición para:', productoData);
                cargarDatosModalEditar(productoData);
            });
        });
        
        // Botones de AGREGAR STOCK
        const botonesStock = document.querySelectorAll('.btn-modal-stock');
        console.log(`📦 Encontrados ${botonesStock.length} botones de stock`);
        
        botonesStock.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                // Extraer datos del producto desde los data attributes
                const productoData = {
                    id: this.dataset.productoId,
                    nombre: this.dataset.productoNombre,
                    stock: this.dataset.productoStock || 0
                };
                
                console.log('📦 Abriendo modal de stock para:', productoData);
                cargarDatosModalStock(productoData);
            });
        });
        
        // Botones de ELIMINAR
        const botonesEliminar = document.querySelectorAll('.btn-modal-eliminar');
        console.log(`🗑️ Encontrados ${botonesEliminar.length} botones de eliminar`);
        
        botonesEliminar.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                // Extraer datos del producto desde los data attributes
                const productoData = {
                    id: this.dataset.productoId,
                    nombre: this.dataset.productoNombre
                };
                
                console.log('🗑️ Abriendo modal de eliminación para:', productoData);
                cargarDatosModalEliminar(productoData);
            });
        });
        
        console.log('✅ Botones configurados correctamente');
    }
    
    /**
     * Carga los datos del producto en el modal de edición
     */
    function cargarDatosModalEditar(productoData) {
        console.log('📝 Llenando campos del modal de edición...');
        
        // Llenar campos del formulario
        const campos = {
            'editar_producto_id': productoData.id,
            'editar_nombre_producto': productoData.nombre,
            'editar_tipo_producto': productoData.tipo || '',
            'editar_stock': productoData.stock,
            'editar_precio': productoData.precio,
            'editar_color': productoData.color,
            'editar_naturaleza': productoData.naturaleza,
            'editar_estado': productoData.estado
        };
        
        // Asignar valores a los campos
        Object.keys(campos).forEach(idCampo => {
            const elemento = document.getElementById(idCampo);
            if (elemento) {
                elemento.value = campos[idCampo] || '';
                console.log(`  ✓ ${idCampo} = ${campos[idCampo]}`);
            } else {
                console.warn(`  ⚠️ Campo ${idCampo} no encontrado`);
            }
        });
        
        console.log('✅ Datos cargados en el modal de edición');
    }
    
    /**
     * Carga los datos del producto en el modal de agregar stock
     */
    function cargarDatosModalStock(productoData) {
        console.log('📦 Llenando campos del modal de stock...');
        
        // ID del producto (hidden input)
        const inputId = document.getElementById('stock_producto_id');
        if (inputId) {
            inputId.value = productoData.id;
            console.log(`  ✓ stock_producto_id = ${productoData.id}`);
        }
        
        // Nombre del producto (span)
        const spanNombre = document.getElementById('stock_nombre_producto');
        if (spanNombre) {
            spanNombre.textContent = productoData.nombre;
            console.log(`  ✓ stock_nombre_producto = ${productoData.nombre}`);
        }
        
        // Stock actual (badge/span)
        const spanStock = document.getElementById('stock_actual');
        if (spanStock) {
            spanStock.textContent = productoData.stock;
            console.log(`  ✓ stock_actual = ${productoData.stock}`);
        }
        
        // Limpiar campo de cantidad
        const inputCantidad = document.getElementById('cantidad_agregar');
        if (inputCantidad) {
            inputCantidad.value = '';
            // Auto-focus en el campo cantidad después de abrir el modal
            setTimeout(() => inputCantidad.focus(), 500);
        }
        
        console.log('✅ Datos cargados en el modal de stock');
    }
    
    /**
     * Carga los datos del producto en el modal de eliminar
     */
    function cargarDatosModalEliminar(productoData) {
        console.log('🗑️ Llenando campos del modal de eliminación...');
        
        // ID del producto (hidden input)
        const inputId = document.getElementById('eliminar_producto_id');
        if (inputId) {
            inputId.value = productoData.id;
            console.log(`  ✓ eliminar_producto_id = ${productoData.id}`);
        }
        
        // Nombre del producto (span)
        const spanNombre = document.getElementById('eliminar_nombre_producto');
        if (spanNombre) {
            spanNombre.textContent = productoData.nombre;
            console.log(`  ✓ eliminar_nombre_producto = ${productoData.nombre}`);
        }
        
        console.log('✅ Datos cargados en el modal de eliminación');
    }
    
    /**
     * Inicialización cuando el DOM esté listo
     */
    function inicializar() {
        console.log('🎯 Esperando que el DOM esté listo...');
        
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function() {
                console.log('📄 DOM cargado, configurando botones...');
                setTimeout(configurarBotonesAccion, 100);
            });
        } else {
            console.log('📄 DOM ya está listo, configurando botones...');
            setTimeout(configurarBotonesAccion, 100);
        }
    }
    
    // Exportar funciones para uso externo si es necesario
    window.reconfigurararBotonesInventario = configurarBotonesAccion;
    
    // Iniciar el script
    inicializar();
    
})();
