// Limpieza forzada de backdrop y clase modal-open al cerrar el modal de turnos
document.addEventListener('DOMContentLoaded', function() {
    // Limpieza forzada de backdrop y clase modal-open al cerrar cualquier modal
    document.querySelectorAll('.modal').forEach(function(modalEl) {
        modalEl.addEventListener('hidden.bs.modal', function () {
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
        });
    });
});
    // Actualizar tipo de usuario por AJAX
    document.querySelectorAll('.tipo-select').forEach(function(select) {
        select.addEventListener('change', function() {
            var idusu = this.getAttribute('data-idusu');
            var tipo = this.value;
            fetch('/Original-Floraltech/assets/ajax/ajax_tipo_usuario.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'idusu=' + encodeURIComponent(idusu) + '&tipo=' + encodeURIComponent(tipo)
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    this.classList.add('border-success');
                    setTimeout(() => this.classList.remove('border-success'), 1000);
                } else {
                    this.classList.add('border-danger');
                    setTimeout(() => this.classList.remove('border-danger'), 1000);
                    alert('No se pudo actualizar el tipo de usuario.');
                }
            })
            .catch(() => {
                this.classList.add('border-danger');
                setTimeout(() => this.classList.remove('border-danger'), 1000);
                alert('Error de conexión al actualizar tipo de usuario.');
            });
        });
    });
// Editar permiso
function editarPermiso(id) {
    // Limpiar campos antes de cargar
    document.getElementById('edit_permiso_id').value = '';
    document.getElementById('edit_permisoEmpleado').selectedIndex = 0;
    document.getElementById('edit_permisoTipo').selectedIndex = 0;
    document.getElementById('edit_permisoFechaInicio').value = '';
    document.getElementById('edit_permisoFechaFin').value = '';
    document.getElementById('edit_permisoEstado').selectedIndex = 0;

    fetch('/Original-Floraltech/assets/ajax/ajax_permiso.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'action=get&id=' + id
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            document.getElementById('edit_permiso_id').value = data.idpermiso;
            // Seleccionar empleado
            var selectEmpleado = document.getElementById('edit_permisoEmpleado');
            for (var i = 0; i < selectEmpleado.options.length; i++) {
                if (parseInt(selectEmpleado.options[i].value) === parseInt(data.idempleado)) {
                    selectEmpleado.selectedIndex = i;
                    break;
                }
            }
            // Seleccionar tipo
            var selectTipo = document.getElementById('edit_permisoTipo');
            for (var j = 0; j < selectTipo.options.length; j++) {
                if (selectTipo.options[j].value === data.tipo) {
                    selectTipo.selectedIndex = j;
                    break;
                }
            }
            document.getElementById('edit_permisoFechaInicio').value = data.fecha_inicio || '';
            document.getElementById('edit_permisoFechaFin').value = data.fecha_fin || '';
            // Seleccionar estado
            var selectEstado = document.getElementById('edit_permisoEstado');
            for (var k = 0; k < selectEstado.options.length; k++) {
                if (selectEstado.options[k].value === data.estado) {
                    selectEstado.selectedIndex = k;
                    break;
                }
            }
            var modal = new bootstrap.Modal(document.getElementById('editarPermisoModal'));
            modal.show();
        } else {
            alert('No se pudo cargar el permiso.');
        }
    });
}

// Editar turno
function editarTurno(id) {
    // Limpiar campos antes de cargar
    var idField = document.getElementById('edit_turno_id');
    var fechaInicioField = document.getElementById('edit_turnoFechaInicio');
    var fechaFinField = document.getElementById('edit_turnoFechaFin');
    var horarioField = document.getElementById('edit_turnoHorario');
    var selectEmpleado = document.getElementById('edit_turnoEmpleado');
    
    if (!idField || !fechaInicioField || !fechaFinField || !horarioField || !selectEmpleado) {
        alert('No se encontró el formulario de edición de turnos en esta vista.');
        return;
    }
    
    idField.value = '';
    fechaInicioField.value = '';
    fechaFinField.value = '';
    horarioField.value = '';
    selectEmpleado.selectedIndex = 0;

    fetch('/Original-Floraltech/assets/ajax/ajax_turno.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'action=get&id=' + id
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            idField.value = data.idturno || '';
            // Seleccionar empleado
            for (var i = 0; i < selectEmpleado.options.length; i++) {
                if (parseInt(selectEmpleado.options[i].value) === parseInt(data.idempleado)) {
                    selectEmpleado.selectedIndex = i;
                    break;
                }
            }
            fechaInicioField.value = data.fecha_inicio || '';
            fechaFinField.value = data.fecha_fin || '';
            horarioField.value = data.horario || '';
            
            var modal = new bootstrap.Modal(document.getElementById('editarTurnoModal'));
            modal.show();
        } else {
            alert('No se pudo cargar el turno.');
        }
    });
}

// Editar vacación
function editarVacacion(id) {
    // Limpiar campos antes de cargar
    var idField = document.getElementById('edit_vacacion_id');
    var fechaInicioField = document.getElementById('edit_vacacionFechaInicio');
    var fechaFinField = document.getElementById('edit_vacacionFechaFin');
    var motivoField = document.getElementById('edit_vacacionMotivo');
    var selectEmpleado = document.getElementById('edit_vacacionEmpleado');
    var selectEstado = document.getElementById('edit_vacacionEstado');
    if (!idField || !fechaInicioField || !fechaFinField || !motivoField || !selectEmpleado || !selectEstado) {
        alert('No se encontró el formulario de edición de vacaciones en esta vista.');
        return;
    }
    idField.value = '';
    fechaInicioField.value = '';
    fechaFinField.value = '';
    motivoField.value = '';
    selectEmpleado.selectedIndex = 0;
    selectEstado.selectedIndex = 0;

    fetch('/Original-Floraltech/assets/ajax/ajax_vacacion.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'action=get&id=' + id
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            idField.value = data.id;
            // Seleccionar empleado
            for (var i = 0; i < selectEmpleado.options.length; i++) {
                if (parseInt(selectEmpleado.options[i].value) === parseInt(data.id_empleado)) {
                    selectEmpleado.selectedIndex = i;
                    break;
                }
            }
            fechaInicioField.value = data.fecha_inicio || '';
            fechaFinField.value = data.fecha_fin || '';
            motivoField.value = data.motivo || '';
            // Seleccionar estado
            for (var j = 0; j < selectEstado.options.length; j++) {
                if (selectEstado.options[j].value === data.estado) {
                    selectEstado.selectedIndex = j;
                    break;
                }
            }
            var modal = new bootstrap.Modal(document.getElementById('editarVacacionModal'));
            modal.show();
        } else {
            alert('No se pudo cargar la vacación.');
        }
    });
}

// Eliminar turno
function eliminarTurno(id) {
    if (!confirm('¿Seguro que deseas eliminar este turno?')) return;
    fetch('/Original-Floraltech/assets/ajax/ajax_turno.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'action=delete&id=' + id
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            alert('Turno eliminado');
            location.reload();
        } else {
            alert('Error al eliminar turno');
        }
    });
}

// Eliminar permiso
function eliminarPermiso(id) {
    if (!confirm('¿Seguro que deseas eliminar este permiso?')) return;
    fetch('/Original-Floraltech/assets/ajax/ajax_permiso.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'action=delete&id=' + id
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            alert('Permiso eliminado');
            location.reload();
        } else {
            alert('Error al eliminar permiso');
        }
    });
}

// Eliminar vacación
function eliminarVacacion(id) {
    if (!confirm('¿Seguro que deseas eliminar esta vacación?')) return;
    fetch('/Original-Floraltech/assets/ajax/ajax_vacacion.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'action=delete&id=' + id
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            alert('Vacación eliminada');
            location.reload();
        } else {
            alert('Error al eliminar vacación');
        }
    });
}
// dgemp.js - Funciones JS para la gestión de empleados, permisos, turnos y vacaciones

function validarNuevoEmpleado() {
    var nombre = document.getElementById('nombre').value.trim();
    var apellido = document.getElementById('apellido').value.trim();
    var documento = document.getElementById('documento').value.trim();
    var cargo = document.getElementById('cargo').value.trim();
    if (!nombre || !apellido || !documento || !cargo) {
        alert('Todos los campos son obligatorios.');
        return false;
    }
    return true;
}

function cargarEmpleado(id) {
    // Limpiar campos antes de cargar
    document.getElementById('edit_id').value = '';
    document.getElementById('edit_nombre').value = '';
    document.getElementById('edit_apellido').value = '';
    document.getElementById('edit_documento').value = '';
    document.getElementById('edit_cargo').value = '';
    document.getElementById('edit_fecha_ingreso').value = '';
    document.getElementById('edit_tipo_contrato').value = '';
    document.getElementById('edit_estado').value = '';
    document.getElementById('edit_password').value = '';

    fetch('/Original-Floraltech/assets/ajax/ajax_empleado.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'action=get&id=' + id
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            document.getElementById('edit_id').value = data.idusu;
            // Separar nombre y apellido correctamente
            let nombre = '';
            let apellido = '';
            if (data.nombre_completo) {
                let partes = data.nombre_completo.split(' ');
                nombre = partes[0];
                apellido = partes.slice(1).join(' ');
            }
            document.getElementById('edit_nombre').value = nombre;
            document.getElementById('edit_apellido').value = apellido;
            document.getElementById('edit_documento').value = data.username;
            document.getElementById('edit_cargo').value = data.naturaleza;
            document.getElementById('edit_fecha_ingreso').value = data.fecha_registro ? data.fecha_registro.split(' ')[0] : '';
            document.getElementById('edit_tipo_contrato').value = 'indefinido';
            document.getElementById('edit_estado').value = data.activo == 1 ? 'activo' : 'inactivo';
            // Abrir el modal solo cuando los datos estén listos
            var modal = new bootstrap.Modal(document.getElementById('editarEmpleadoModal'));
            modal.show();
        } else {
            alert('No se pudo cargar el empleado.');
        }
    });
}

function actualizarEmpleado() {
    var id = document.getElementById('edit_id').value;
    var nombre = document.getElementById('edit_nombre').value;
    var apellido = document.getElementById('edit_apellido').value;
    var cargo = document.getElementById('edit_cargo').value;
    var fecha_ingreso = document.getElementById('edit_fecha_ingreso').value;
    var tipo_contrato = document.getElementById('edit_tipo_contrato').value;
    var estado = document.getElementById('edit_estado').value;
    var password = document.getElementById('edit_password').value;
    
    var params = `action=update&id=${id}&nombre=${encodeURIComponent(nombre)}&apellido=${encodeURIComponent(apellido)}&cargo=${encodeURIComponent(cargo)}&fecha_ingreso=${fecha_ingreso}&tipo_contrato=${tipo_contrato}&estado=${estado}`;
    
    // Solo incluir la contraseña si se proporcionó
    if (password.trim() !== '') {
        params += `&password=${encodeURIComponent(password)}`;
    }
    
    fetch('/Original-Floraltech/assets/ajax/ajax_empleado.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: params
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            alert('Empleado actualizado exitosamente');
            location.reload();
        } else {
            alert('Error al actualizar empleado');
        }
    });
}

function eliminarEmpleado(id) {
    if (!confirm('¿Seguro que deseas eliminar este empleado?')) return;
    fetch('/Original-Floraltech/assets/ajax/ajax_empleado.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'action=delete&id=' + id
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            alert('Empleado eliminado');
            location.reload();
        } else {
            alert('Error al eliminar');
        }
    });
}

function verEmpleado(id) {
    fetch('/Original-Floraltech/assets/ajax/ajax_empleado.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'action=view&id=' + id
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            alert('Nombre: ' + data.nombre_completo + '\nDocumento: ' + data.username + '\nCargo: ' + data.naturaleza);
        } else {
            alert('No se pudo cargar el empleado.');
        }
    });
}

// Guardar nuevo permiso
if (document.getElementById('formNuevoPermiso')) {
    document.getElementById('formNuevoPermiso').addEventListener('submit', function(e) {
        e.preventDefault();
        const fd = new FormData(this);
        fd.append('action', 'create');
            // Validación previa: mostrar datos antes de enviar
            let datos = {
                empleado: fd.get('empleado'),
                tipo: fd.get('tipo'),
                fecha_inicio: fd.get('fecha_inicio'),
                fecha_fin: fd.get('fecha_fin'),
                estado: fd.get('estado')
            };
            let camposVacios = Object.values(datos).some(v => !v);
            if (camposVacios || datos.empleado == 0) {
                alert('Todos los campos son obligatorios y el empleado debe ser válido.\n' + JSON.stringify(datos, null, 2));
                return;
            }
            // Mostrar datos en consola para depuración
            console.log('Datos a enviar:', datos);
        fetch('/Original-Floraltech/assets/ajax/ajax_permiso.php', {
            method: 'POST',
            body: fd
        })
        .then(r => r.json())
        .then(data => {
                if (data.success) {
                    alert('Permiso registrado');
                    location.reload();
                } else {
                    let msg = 'Error al registrar permiso';
                    if (data.error) {
                        msg += '\n' + (typeof data.error === 'string' ? data.error : JSON.stringify(data.error));
                    }
                    alert(msg);
                }
        });
    });
}
// Guardar nueva vacación
if (document.getElementById('formNuevaVacacion')) {
    document.getElementById('formNuevaVacacion').addEventListener('submit', function(e) {
        e.preventDefault();
        console.log('formNuevaVacacion submitted');
        const submitBtn = this.querySelector('button[type="submit"]');
        if (submitBtn) submitBtn.disabled = true;
        const fd = new FormData(this);
        fd.append('action', 'create');
        
        // Validación previa
        const empleado = fd.get('id_empleado');
        const fechaInicio = fd.get('fecha_inicio');
        const fechaFin = fd.get('fecha_fin');
        const motivo = fd.get('motivo');
        const estado = fd.get('estado');
        
        console.log('Datos a enviar:', {
            empleado: empleado,
            fechaInicio: fechaInicio,
            fechaFin: fechaFin,
            motivo: motivo,
            estado: estado
        });
        
        if (!empleado || !fechaInicio || !fechaFin || !motivo) {
            alert('Todos los campos son obligatorios.');
            if (submitBtn) submitBtn.disabled = false;
            return;
        }
        
        fetch('/Original-Floraltech/assets/ajax/ajax_vacacion.php', {
            method: 'POST',
            body: fd
        })
        .then(r => {
            console.log('Response status:', r.status);
            return r.json();
        })
        .then(data => {
            console.log('Response data:', data);
            if (submitBtn) submitBtn.disabled = false;
            if (data.success) {
                alert('Vacación registrada exitosamente');
                location.reload();
            } else {
                alert('Error al registrar vacación: ' + (data.error || 'Error desconocido'));
            }
        })
        .catch(err => {
            console.error('Error:', err);
            if (submitBtn) submitBtn.disabled = false;
            alert('Error de conexión: ' + err.message);
        });
    });
}

// Alternativa: Event listener para el botón de guardar vacación (respaldo)
document.addEventListener('DOMContentLoaded', function() {
    const btnGuardarVacacion = document.querySelector('#formNuevaVacacion button[type="submit"]');
    if (btnGuardarVacacion) {
        btnGuardarVacacion.addEventListener('click', function(e) {
            console.log('Botón Guardar clicked - fallback');
            // Solo ejecutar si el evento submit no se dispara
            setTimeout(function() {
                const form = document.getElementById('formNuevaVacacion');
                if (form) {
                    console.log('Ejecutando submit manual');
                    form.dispatchEvent(new Event('submit'));
                }
            }, 100);
        });
    }
});
// Guardar nuevo turno
// Lógica dinámica para el modal de turnos
document.addEventListener('DOMContentLoaded', function() {
    var temporadaSelect = document.getElementById('turnoTemporada');
    var tipoTurnoSelect = document.getElementById('turnoTipo');
    var horarioInput = document.getElementById('turnoHorario');
    var observacionesInput = document.getElementById('turnoObservaciones');
    if (temporadaSelect && tipoTurnoSelect && horarioInput && observacionesInput) {
        // Opciones por temporada
        var opcionesPorTemporada = {
            normal: [
                { value: 'mañana', text: 'Turno Mañana (7am-3pm)', horario: '7:00-15:00', obs: 'Turno estándar en baja demanda.' },
                { value: 'tarde', text: 'Turno Tarde (3pm-11pm)', horario: '15:00-23:00', obs: 'Turno estándar en baja demanda.' }
            ],
            alta: [
                { value: 'doble', text: 'Turno Doble (7am-7pm)', horario: '7:00-19:00', obs: 'Turno extendido por alta demanda.' },
                { value: 'extra', text: 'Turno Extra (11pm-7am)', horario: '23:00-7:00', obs: 'Turno nocturno especial.' }
            ],
            finsemana: [
                { value: 'sabado', text: 'Sábado completo', horario: '7:00-19:00', obs: 'Cobertura especial sábado.' },
                { value: 'domingo', text: 'Domingo completo', horario: '7:00-19:00', obs: 'Cobertura especial domingo.' }
            ],
            especial: [
                { value: 'evento', text: 'Evento especial', horario: 'A definir', obs: 'Horario y observaciones según evento.' }
            ]
        };
        function actualizarOpcionesTurno() {
            var temporada = temporadaSelect.value;
            tipoTurnoSelect.innerHTML = '';
            if (opcionesPorTemporada[temporada]) {
                opcionesPorTemporada[temporada].forEach(function(opt) {
                    var option = document.createElement('option');
                    option.value = opt.value;
                    option.textContent = opt.text;
                    option.dataset.horario = opt.horario;
                    option.dataset.obs = opt.obs;
                    tipoTurnoSelect.appendChild(option);
                });
                tipoTurnoSelect.dispatchEvent(new Event('change'));
            }
        }
        temporadaSelect.addEventListener('change', actualizarOpcionesTurno);
        tipoTurnoSelect.addEventListener('change', function() {
            var selected = tipoTurnoSelect.options[tipoTurnoSelect.selectedIndex];
            horarioInput.value = selected ? selected.dataset.horario : '';
            observacionesInput.value = selected ? selected.dataset.obs : '';
        });
        // Inicializar al abrir modal
        actualizarOpcionesTurno();
    }
});
if (document.getElementById('formNuevoTurno')) {
    document.getElementById('formNuevoTurno').addEventListener('submit', function(e) {
        e.preventDefault();
        const fd = new FormData(this);
        fd.append('action', 'create');
        // Validación: empleado, horario y fechas
        const empleado = fd.get('empleado');
        const horario = fd.get('horario');
        const fecha_inicio = fd.get('fecha_inicio');
        const fecha_fin = fd.get('fecha_fin');
        if (!empleado || !horario || !fecha_inicio || !fecha_fin) {
            alert('Todos los campos obligatorios deben estar completos.');
            return;
        }
        fetch('/Original-Floraltech/assets/ajax/ajax_turno.php', {
            method: 'POST',
            body: fd
        })
        .then(r => {
            if (!r.ok) {
                alert('Error de conexión con el servidor.');
                return Promise.reject('Network error');
            }
            return r.json();
        })
        .then(data => {
            if (data && data.success) {
                alert('Turno registrado');
                location.reload();
            } else {
                alert('Error al registrar turno.\n' + (data && data.error ? data.error : 'No se recibió respuesta válida.'));
            }
        })
        .catch(err => {
            alert('Error inesperado: ' + err);
        });
    });
}

window.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.btn-outline-primary[data-bs-target="#editarEmpleadoModal"]').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            var id = btn.closest('tr').querySelector('td').textContent;
            cargarEmpleado(id);
        });
    });
    document.querySelectorAll('.btn-outline-danger').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            var id = btn.closest('tr').querySelector('td').textContent;
            eliminarEmpleado(id);
        });
    });
    document.querySelectorAll('.btn-outline-info').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            var id = btn.closest('tr').querySelector('td').textContent;
            verEmpleado(id);
        });
    });
    var actualizarBtn = document.querySelector('#editarEmpleadoModal .btn-primary');
    if (actualizarBtn) {
        actualizarBtn.addEventListener('click', function() {
            actualizarEmpleado();
        });
    }
// Eliminar permiso
    document.querySelectorAll('#permisos .btn-outline-danger').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            var id = btn.closest('tr').querySelector('td').textContent;
            if (!confirm('¿Seguro que deseas eliminar este permiso?')) return;
            fetch('/Original-Floraltech/assets/ajax/ajax_permiso.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'action=delete&id=' + id
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    alert('Permiso eliminado');
                    location.reload();
                } else {
                    alert('Error al eliminar permiso');
                }
            });
        });
    });
// Eliminar turno
    document.querySelectorAll('#turnos .btn-outline-danger').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            var id = btn.closest('tr').querySelector('td').textContent;
            if (!confirm('¿Seguro que deseas eliminar este turno?')) return;
            fetch('/Original-Floraltech/assets/ajax/ajax_turno.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'action=delete&id=' + id
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    alert('Turno eliminado');
                    location.reload();
                } else {
                    alert('Error al eliminar turno');
                }
            });
        });
    });
// Eliminar vacación
    document.querySelectorAll('#vacaciones .btn-outline-danger').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            var id = btn.closest('tr').querySelector('td').textContent;
            if (!confirm('¿Seguro que deseas eliminar esta vacación?')) return;
            fetch('/Original-Floraltech/assets/ajax/ajax_vacacion.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'action=delete&id=' + id
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    alert('Vacación eliminada');
                    location.reload();
                } else {
                    alert('Error al eliminar vacación');
                }
            });
        });
    });
// ...existing code...
    document.getElementById('formEditarPermiso').addEventListener('submit', function(e) {
        e.preventDefault();
        const fd = new FormData(this);
        fd.append('action', 'update');
        fetch('/Original-Floraltech/assets/ajax/ajax_permiso.php', {
            method: 'POST',
            body: fd
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                alert('Permiso actualizado');
                location.reload();
            } else {
                alert('Error al actualizar permiso');
            }
        });
    });
// Editar turno
    document.querySelectorAll('#turnos .btn-outline-primary').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            // Buscar el ID en la primera celda de la fila
            var tr = btn.closest('tr');
            var id = tr ? tr.cells[0].textContent.trim() : null;
            if (!id) {
                alert('No se pudo obtener el ID del turno.');
                return;
            }
            fetch('/Original-Floraltech/assets/ajax/ajax_turno.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'action=get&id=' + id
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    var idField = document.getElementById('edit_turno_id');
                    var fechaInicioField = document.getElementById('edit_turnoFechaInicio');
                    var fechaFinField = document.getElementById('edit_turnoFechaFin');
                    var horarioField = document.getElementById('edit_turnoHorario');
                    var selectEmpleado = document.getElementById('edit_turnoEmpleado');
                    if (idField) idField.value = data.idturno || '';
                    if (selectEmpleado) {
                        for (var i = 0; i < selectEmpleado.options.length; i++) {
                            if (selectEmpleado.options[i].value == data.idempleado) {
                                selectEmpleado.selectedIndex = i;
                                break;
                            }
                        }
                    }
                    if (fechaInicioField) fechaInicioField.value = data.fecha_inicio || '';
                    if (fechaFinField) fechaFinField.value = data.fecha_fin || '';
                    if (horarioField) horarioField.value = data.horario || '';
                    var modalEl = document.getElementById('editarTurnoModal');
                    if (modalEl) {
                        var modal = new bootstrap.Modal(modalEl);
                        modal.show();
                    }
                } else {
                    alert('No se pudo cargar el turno.');
                }
            });
        });
    });
    document.getElementById('formEditarTurno').addEventListener('submit', function(e) {
        e.preventDefault();
        const fd = new FormData(this);
        fd.append('action', 'update');
        fetch('/Original-Floraltech/assets/ajax/ajax_turno.php', {
            method: 'POST',
            body: fd
        })
        .then(r => r.json())
        .then(data => {
            var modalEl = document.getElementById('editarTurnoModal');
            var modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) modal.hide();
            if (data.success) {
                alert('Turno actualizado');
                location.reload();
            } else {
                alert('Error al actualizar turno');
            }
        });
    });
// Editar vacación
    document.querySelectorAll('#vacaciones .btn-outline-primary').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            // Buscar el ID en la primera celda de la fila
            var tr = btn.closest('tr');
            var id = tr ? tr.cells[0].textContent.trim() : null;
            if (!id) {
                alert('No se pudo obtener el ID de la vacación.');
                return;
            }
            editarVacacion(id);
        });
    });
// ...existing code...
    document.getElementById('formEditarVacacion').addEventListener('submit', function(e) {
        e.preventDefault();
        const fd = new FormData(this);
        fd.append('action', 'update');
        fetch('/Original-Floraltech/assets/ajax/ajax_vacacion.php', {
            method: 'POST',
            body: fd
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                alert('Vacación actualizada');
                location.reload();
            } else {
                alert('Error al actualizar vacación');
            }
        });
    });
});
