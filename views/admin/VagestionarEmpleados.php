<?php
// Gestión de Empleados, Permisos, Turnos y Vacaciones
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../models/Mdgemp.php';
$userModel = new Mdgemp();

// Procesar formulario de nuevo empleado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nuevo_empleado'])) {
    try {
        $userModel->crearEmpleado($_POST);
        $mensaje = 'Empleado creado exitosamente.';
        $tipo_mensaje = 'success';
        header('Location: ' . $_SERVER['REQUEST_URI'] . '?msg=success');
        exit();
    } catch (Exception $e) {
        $mensaje = $e->getMessage();
        $tipo_mensaje = 'danger';
    }
}

// Obtener datos usando el modelo
$empleados = $userModel->getAllEmpleados();
$tipos_usuario = $userModel->getTiposUsuario();
$empleados_activos = $userModel->getEmpleadosActivos();
$permisos = $userModel->getPermisosEmpleados();
$turnos = $userModel->getTurnosEmpleados();
$vacaciones = $userModel->getVacacionesEmpleados();

// Estadísticas
$vacaciones_activas = 0;
foreach ($vacaciones as $vacacion) {
    if ($vacacion['estado'] == 'En curso') $vacaciones_activas++;
}

$permisos_pendientes = 0;
foreach ($permisos as $permiso) {
    if ($permiso['estado'] == 'Pendiente') $permisos_pendientes++;
}

$turnos_semana = 0;
$inicio_semana = date('Y-m-d', strtotime('monday this week'));
$fin_semana = date('Y-m-d', strtotime('sunday this week'));
foreach ($turnos as $turno) {
    if ($turno['fecha_inicio'] >= $inicio_semana && $turno['fecha_inicio'] <= $fin_semana) $turnos_semana++;
}

$mensaje = $mensaje ?? '';
$tipo_mensaje = $tipo_mensaje ?? '';
$total_empleados = count($empleados);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Empleados - FloralTech</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/Original-Floraltechx/assets/dgemp.css">
</head>
<body>
    <div class="container-fluid py-4">
        <!-- Encabezado con gradiente -->
        <div class="d-flex align-items-center justify-content-between mb-4 py-3 px-3 rounded-4 shadow-sm text-white" style="background: linear-gradient(120deg, #0d6efd 0%, #5b21b6 60%, #1e1b4b 100%);">
            <div>
                <p class="mb-1 opacity-75" style="letter-spacing:1px;text-transform:uppercase;"><i class="fas fa-users me-2"></i>FloralTech Admin</p>
                <h2 class="mb-0 fw-bold">Gestión de Empleados</h2>
            </div>
            <button class="btn btn-light text-primary fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#nuevoEmpleadoModal">
                <i class="fas fa-plus me-2"></i>Nuevo Empleado
            </button>
        </div>

        <?php if ($mensaje): ?>
            <div class="alert alert-<?= $tipo_mensaje ?> alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($mensaje) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Tarjetas de estadísticas -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stats-card">
                    <i class="fas fa-user-check"></i>
                    <h3><?= $empleados_activos ?></h3>
                    <p>Empleados Activos</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <i class="fas fa-calendar-check"></i>
                    <h3><?= $permisos_pendientes ?></h3>
                    <p>Permisos Pendientes</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <i class="fas fa-calendar-alt"></i>
                    <h3><?= $turnos_semana ?></h3>
                    <p>Turnos Esta Semana</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <i class="fas fa-sun"></i>
                    <h3><?= $vacaciones_activas ?></h3>
                    <p>Vacaciones Activas</p>
                </div>
            </div>
        </div>

        <!-- Tabs de navegación -->
        <ul class="nav nav-tabs mb-4" id="tabsGestion" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="tabEmpleados" data-bs-toggle="tab" data-bs-target="#contenidoEmpleados" type="button" role="tab">
                    <i class="fas fa-users me-2"></i>Empleados
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="tabPermisos" data-bs-toggle="tab" data-bs-target="#contenidoPermisos" type="button" role="tab">
                    <i class="fas fa-user-clock me-2"></i>Permisos
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="tabTurnos" data-bs-toggle="tab" data-bs-target="#contenidoTurnos" type="button" role="tab">
                    <i class="fas fa-business-time me-2"></i>Turnos
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="tabVacaciones" data-bs-toggle="tab" data-bs-target="#contenidoVacaciones" type="button" role="tab">
                    <i class="fas fa-calendar-day me-2"></i>Vacaciones
                </button>
            </li>
        </ul>

        <!-- Contenido de Tabs -->
        <div class="tab-content" id="tabsContenido">
            <!-- TAB EMPLEADOS -->
            <div class="tab-pane fade show active" id="contenidoEmpleados" role="tabpanel">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white border-0">
                        <h5 class="mb-0">Lista de Empleados</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre</th>
                                        <th>Usuario</th>
                                        <th>Email</th>
                                        <th>Teléfono</th>
                                        <th>Estado</th>
                                        <th>Registro</th>
                                        <th class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($empleados)): ?>
                                        <?php foreach ($empleados as $emp): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($emp['idusu']) ?></td>
                                                <td><?= htmlspecialchars($emp['nombre_completo']) ?></td>
                                                <td><?= htmlspecialchars($emp['username']) ?></td>
                                                <td><?= htmlspecialchars($emp['email']) ?></td>
                                                <td><?= htmlspecialchars($emp['telefono']) ?></td>
                                                <td>
                                                    <?php if ($emp['activo']): ?>
                                                        <span class="badge bg-success">Activo</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-danger">Inactivo</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?= date('d/m/Y', strtotime($emp['fecha_registro'])) ?></td>
                                                <td class="text-center">
                                                    <button class="btn btn-sm btn-outline-primary" onclick="alert('Editar empleado')"><i class="fas fa-edit"></i></button>
                                                    <button class="btn btn-sm btn-outline-danger" onclick="alert('Eliminar empleado')"><i class="fas fa-trash"></i></button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="8" class="text-center text-muted py-4">No hay empleados registrados.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB PERMISOS -->
            <div class="tab-pane fade" id="contenidoPermisos" role="tabpanel">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white border-0 d-flex justify-content-between">
                        <h5 class="mb-0">Gestión de Permisos</h5>
                        <button class="btn btn-sm btn-primary"><i class="fas fa-plus me-1"></i>Nuevo Permiso</button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Empleado</th>
                                        <th>Tipo</th>
                                        <th>Fecha Inicio</th>
                                        <th>Fecha Fin</th>
                                        <th>Estado</th>
                                        <th class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($permisos)): ?>
                                        <?php foreach ($permisos as $perm): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($perm['idpermiso']) ?></td>
                                                <td><?= htmlspecialchars($perm['empleado']) ?></td>
                                                <td><?= htmlspecialchars($perm['tipo']) ?></td>
                                                <td><?= date('d/m/Y', strtotime($perm['fecha_inicio'])) ?></td>
                                                <td><?= date('d/m/Y', strtotime($perm['fecha_fin'])) ?></td>
                                                <td>
                                                    <?php 
                                                    $badge = match($perm['estado']) {
                                                        'Aprobado' => 'bg-success',
                                                        'Rechazado' => 'bg-danger',
                                                        default => 'bg-warning'
                                                    };
                                                    ?>
                                                    <span class="badge <?= $badge ?>"><?= htmlspecialchars($perm['estado']) ?></span>
                                                </td>
                                                <td class="text-center">
                                                    <button class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></button>
                                                    <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-4">No hay permisos registrados.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB TURNOS -->
            <div class="tab-pane fade" id="contenidoTurnos" role="tabpanel">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white border-0 d-flex justify-content-between">
                        <h5 class="mb-0">Gestión de Turnos</h5>
                        <button class="btn btn-sm btn-primary"><i class="fas fa-plus me-1"></i>Nuevo Turno</button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Empleado</th>
                                        <th>Fecha Inicio</th>
                                        <th>Fecha Fin</th>
                                        <th>Horario</th>
                                        <th class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($turnos)): ?>
                                        <?php foreach ($turnos as $turno): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($turno['idturno']) ?></td>
                                                <td><?= htmlspecialchars($turno['empleado']) ?></td>
                                                <td><?= date('d/m/Y', strtotime($turno['fecha_inicio'])) ?></td>
                                                <td><?= date('d/m/Y', strtotime($turno['fecha_fin'])) ?></td>
                                                <td><?= htmlspecialchars($turno['horario']) ?></td>
                                                <td class="text-center">
                                                    <button class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></button>
                                                    <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-4">No hay turnos registrados.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB VACACIONES -->
            <div class="tab-pane fade" id="contenidoVacaciones" role="tabpanel">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white border-0 d-flex justify-content-between">
                        <h5 class="mb-0">Gestión de Vacaciones</h5>
                        <button class="btn btn-sm btn-primary"><i class="fas fa-plus me-1"></i>Nueva Vacación</button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Empleado</th>
                                        <th>Fecha Inicio</th>
                                        <th>Fecha Fin</th>
                                        <th>Motivo</th>
                                        <th>Estado</th>
                                        <th class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($vacaciones)): ?>
                                        <?php foreach ($vacaciones as $vac): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($vac['id']) ?></td>
                                                <td><?= htmlspecialchars($vac['empleado']) ?></td>
                                                <td><?= date('d/m/Y', strtotime($vac['fecha_inicio'])) ?></td>
                                                <td><?= date('d/m/Y', strtotime($vac['fecha_fin'])) ?></td>
                                                <td><?= htmlspecialchars($vac['motivo']) ?></td>
                                                <td>
                                                    <?php 
                                                    $badge = match($vac['estado']) {
                                                        'En curso' => 'bg-info',
                                                        'Aprobada' => 'bg-success',
                                                        'Rechazada' => 'bg-danger',
                                                        default => 'bg-warning'
                                                    };
                                                    ?>
                                                    <span class="badge <?= $badge ?>"><?= htmlspecialchars($vac['estado']) ?></span>
                                                </td>
                                                <td class="text-center">
                                                    <button class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></button>
                                                    <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-4">No hay vacaciones registradas.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Nuevo Empleado -->
    <div class="modal fade" id="nuevoEmpleadoModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Nuevo Empleado</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Nombre Completo</label>
                                <input type="text" class="form-control" name="nombre_completo" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Username</label>
                                <input type="text" class="form-control" name="username" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Teléfono</label>
                                <input type="tel" class="form-control" name="telefono">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Rol</label>
                                <select class="form-select" name="tpusu_idtpusu" required>
                                    <option value="">Seleccione...</option>
                                    <?php foreach ($tipos_usuario as $id => $nombre): ?>
                                        <option value="<?= $id ?>"><?= htmlspecialchars($nombre) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Contraseña</label>
                                <input type="password" class="form-control" name="password">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Naturaleza / Cargo</label>
                                <input type="text" class="form-control" name="naturaleza">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" name="nuevo_empleado" value="1" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/Original-Floraltechx/assets/js/gestion_empleados_handlers.js?v=<?= time() ?>"></script>
</body>
</html>




