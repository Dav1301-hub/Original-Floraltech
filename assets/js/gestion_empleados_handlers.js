// gestion_empleados_handlers.js
// Manejadores de eventos para Permisos, Turnos y Vacaciones

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
            document.getElementById('edit_turnoHorario').value = data.horario || '';
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
