// Manejadores de eventos para Permisos, Turnos y Vacaciones

// Opciones por temporada (Plantillas de Turnos)
const opcionesPorTemporada = {
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

function actualizarOpcionesTurno(temporadaId, tipoId, horarioId, obsId) {
    const temporadaSelect = document.getElementById(temporadaId);
    const tipoTurnoSelect = document.getElementById(tipoId);
    const horarioInput = document.getElementById(horarioId);
    const observacionesInput = document.getElementById(obsId);

    if (!temporadaSelect || !tipoTurnoSelect) return;

    const temporada = temporadaSelect.value;
    tipoTurnoSelect.innerHTML = '';

    if (opcionesPorTemporada[temporada]) {
        opcionesPorTemporada[temporada].forEach(function(opt) {
            const option = document.createElement('option');
            option.value = opt.value;
            option.textContent = opt.text;
            option.dataset.horario = opt.horario;
            option.dataset.obs = opt.obs;
            tipoTurnoSelect.appendChild(option);
        });

        // Al cambiar temporada, actualizar horario y observaciones con el primer tipo
        const firstOpt = opcionesPorTemporada[temporada][0];
        if (horarioInput) horarioInput.value = firstOpt.horario;
        if (observacionesInput) observacionesInput.value = firstOpt.obs;
    }
}

// ============================================
// PERMISOS
// ============================================

function editarPermiso(id) {
    fetch('assets/ajax/ajax_gestion_empleados.php', {
        method: 'POST',
        body: new URLSearchParams({
            action: 'get_permiso',
            id: id
        })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            document.getElementById('edit_permiso_id').value = data.idpermiso || '';
            document.getElementById('edit_permisoEmpleado').value = data.idempleado || '';
            document.getElementById('edit_permisoTipo').value = data.tipo || '';
            document.getElementById('edit_permisoFechaInicio').value = data.fecha_inicio || '';
            document.getElementById('edit_permisoFechaFin').value = data.fecha_fin || '';
            document.getElementById('edit_permisoEstado').value = data.estado || 'Pendiente';
            new bootstrap.Modal(document.getElementById('editarPermisoModal')).show();
        } else {
            alert('Error: ' + (data.error || 'No se pudo cargar el permiso'));
        }
    })
    .catch(err => alert('Error: ' + err));
}

function eliminarPermiso(id) {
    if (!confirm('¿Estás seguro de que deseas eliminar este permiso?')) return;
    
    fetch('assets/ajax/ajax_gestion_empleados.php', {
        method: 'POST',
        body: new URLSearchParams({
            action: 'delete_permiso',
            id: id
        })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            alert('Permiso eliminado correctamente');
            location.reload();
        } else {
            alert('Error: ' + (data.error || 'No se pudo eliminar'));
        }
    })
    .catch(err => alert('Error: ' + err));
}

// Evento submit para crear permiso
document.addEventListener('DOMContentLoaded', function() {
    const formNuevoPermiso = document.getElementById('formNuevoPermiso');
    if (formNuevoPermiso) {
        formNuevoPermiso.addEventListener('submit', function(e) {
            e.preventDefault();
            const fd = new FormData(this);
            fd.append('action', 'create_permiso');
            
            fetch('assets/ajax/ajax_gestion_empleados.php', {
                method: 'POST',
                body: fd
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    alert('Permiso creado correctamente');
                    location.reload();
                } else {
                    alert('Error: ' + (data.error || 'No se pudo crear'));
                }
            })
            .catch(err => alert('Error: ' + err));
        });
    }
    
    const formEditarPermiso = document.getElementById('formEditarPermiso');
    if (formEditarPermiso) {
        formEditarPermiso.addEventListener('submit', function(e) {
            e.preventDefault();
            const fd = new FormData(this);
            fd.append('action', 'update_permiso');
            
            fetch('assets/ajax/ajax_gestion_empleados.php', {
                method: 'POST',
                body: fd
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    alert('Permiso actualizado correctamente');
                    location.reload();
                } else {
                    alert('Error: ' + (data.error || 'No se pudo actualizar'));
                }
            })
            .catch(err => alert('Error: ' + err));
        });
    }
});

// ============================================
// TURNOS
// ============================================

function editarTurno(id) {
    fetch('assets/ajax/ajax_gestion_empleados.php', {
        method: 'POST',
        body: new URLSearchParams({
            action: 'get_turno',
            id: id
        })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            document.getElementById('edit_turno_id').value = data.idturno || '';
            document.getElementById('edit_turnoEmpleado').value = data.idempleado || '';
            document.getElementById('edit_turnoFechaInicio').value = data.fecha_inicio || '';
            document.getElementById('edit_turnoFechaFin').value = data.fecha_fin || '';
            
            // Campos de temporada y turno
            const temporadaSelect = document.getElementById('edit_turnoTemporada');
            if (temporadaSelect) {
                temporadaSelect.value = data.tipo_temporada || 'normal';
                actualizarOpcionesTurno('edit_turnoTemporada', 'edit_turnoTipo', 'edit_turnoHorario', 'edit_turnoObservaciones');
                document.getElementById('edit_turnoTipo').value = data.turno || '';
            }
            
            document.getElementById('edit_turnoHorario').value = data.horario || '';
            document.getElementById('edit_turnoObservaciones').value = data.observaciones || '';
            
            new bootstrap.Modal(document.getElementById('editarTurnoModal')).show();
        } else {
            alert('Error: ' + (data.error || 'No se pudo cargar el turno'));
        }
    })
    .catch(err => alert('Error: ' + err));
}

function eliminarTurno(id) {
    if (!confirm('¿Estás seguro de que deseas eliminar este turno?')) return;
    
    fetch('assets/ajax/ajax_gestion_empleados.php', {
        method: 'POST',
        body: new URLSearchParams({
            action: 'delete_turno',
            id: id
        })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            alert('Turno eliminado correctamente');
            location.reload();
        } else {
            alert('Error: ' + (data.error || 'No se pudo eliminar'));
        }
    })
    .catch(err => alert('Error: ' + err));
}

// Evento submit para crear turno
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar lógica de plantillas de turnos
    const setupTemplateLogic = (temporadaId, tipoId, horarioId, obsId) => {
        const temporadaSelect = document.getElementById(temporadaId);
        const tipoTurnoSelect = document.getElementById(tipoId);
        
        if (temporadaSelect && tipoTurnoSelect) {
            temporadaSelect.addEventListener('change', () => actualizarOpcionesTurno(temporadaId, tipoId, horarioId, obsId));
            
            tipoTurnoSelect.addEventListener('change', function() {
                const selected = this.options[this.selectedIndex];
                const horarioInput = document.getElementById(horarioId);
                const observacionesInput = document.getElementById(obsId);
                if (horarioInput) horarioInput.value = selected ? selected.dataset.horario : '';
                if (observacionesInput) observacionesInput.value = selected ? selected.dataset.obs : '';
            });
            
            // Inicializar opciones
            actualizarOpcionesTurno(temporadaId, tipoId, horarioId, obsId);
        }
    };

    setupTemplateLogic('turnoTemporada', 'turnoTipo', 'turnoHorario', 'turnoObservaciones');
    setupTemplateLogic('edit_turnoTemporada', 'edit_turnoTipo', 'edit_turnoHorario', 'edit_turnoObservaciones');

    const formNuevoTurno = document.getElementById('formNuevoTurno');
    if (formNuevoTurno) {
        formNuevoTurno.addEventListener('submit', function(e) {
            e.preventDefault();
            const fd = new FormData(this);
            fd.append('action', 'create_turno');
            
            fetch('assets/ajax/ajax_gestion_empleados.php', {
                method: 'POST',
                body: fd
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    alert('Turno creado correctamente');
                    location.reload();
                } else {
                    alert('Error: ' + (data.error || 'No se pudo crear'));
                }
            })
            .catch(err => alert('Error: ' + err));
        });
    }
    
    const formEditarTurno = document.getElementById('formEditarTurno');
    if (formEditarTurno) {
        formEditarTurno.addEventListener('submit', function(e) {
            e.preventDefault();
            const fd = new FormData(this);
            fd.append('action', 'update_turno');
            
            fetch('assets/ajax/ajax_gestion_empleados.php', {
                method: 'POST',
                body: fd
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    alert('Turno actualizado correctamente');
                    location.reload();
                } else {
                    alert('Error: ' + (data.error || 'No se pudo actualizar'));
                }
            })
            .catch(err => alert('Error: ' + err));
        });
    }
});

// ============================================
// VACACIONES
// ============================================

function editarVacacion(id) {
    fetch('assets/ajax/ajax_gestion_empleados.php', {
        method: 'POST',
        body: new URLSearchParams({
            action: 'get_vacacion',
            id: id
        })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            document.getElementById('edit_vacacion_id').value = data.id || '';
            document.getElementById('edit_vacacionEmpleado').value = data.id_empleado || '';
            document.getElementById('edit_vacacionFechaInicio').value = data.fecha_inicio || '';
            document.getElementById('edit_vacacionFechaFin').value = data.fecha_fin || '';
            document.getElementById('edit_vacacionMotivo').value = data.motivo || '';
            document.getElementById('edit_vacacionEstado').value = data.estado || 'Programadas';
            new bootstrap.Modal(document.getElementById('editarVacacionModal')).show();
        } else {
            alert('Error: ' + (data.error || 'No se pudo cargar la vacación'));
        }
    })
    .catch(err => alert('Error: ' + err));
}

function eliminarVacacion(id) {
    if (!confirm('¿Estás seguro de que deseas eliminar esta vacación?')) return;
    
    fetch('assets/ajax/ajax_gestion_empleados.php', {
        method: 'POST',
        body: new URLSearchParams({
            action: 'delete_vacacion',
            id: id
        })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            alert('Vacación eliminada correctamente');
            location.reload();
        } else {
            alert('Error: ' + (data.error || 'No se pudo eliminar'));
        }
    })
    .catch(err => alert('Error: ' + err));
}

// Evento submit para crear vacación
document.addEventListener('DOMContentLoaded', function() {
    const formNuevaVacacion = document.getElementById('formNuevaVacacion');
    if (formNuevaVacacion) {
        formNuevaVacacion.addEventListener('submit', function(e) {
            e.preventDefault();
            const fd = new FormData(this);
            fd.append('action', 'create_vacacion');
            
            fetch('assets/ajax/ajax_gestion_empleados.php', {
                method: 'POST',
                body: fd
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    alert('Vacación creada correctamente');
                    location.reload();
                } else {
                    alert('Error: ' + (data.error || 'No se pudo crear'));
                }
            })
            .catch(err => alert('Error: ' + err));
        });
    }
    
    const formEditarVacacion = document.getElementById('formEditarVacacion');
    if (formEditarVacacion) {
        formEditarVacacion.addEventListener('submit', function(e) {
            e.preventDefault();
            const fd = new FormData(this);
            fd.append('action', 'update_vacacion');
            
            fetch('assets/ajax/ajax_gestion_empleados.php', {
                method: 'POST',
                body: fd
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    alert('Vacación actualizada correctamente');
                    location.reload();
                } else {
                    alert('Error: ' + (data.error || 'No se pudo actualizar'));
                }
            })
            .catch(err => alert('Error: ' + err));
        });
    }
});
