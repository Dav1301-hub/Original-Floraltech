<?php
// Procesar formulario de nuevo empleado antes de cualquier salida

// Cargar modelo de usuario
require_once __DIR__ . '/../../models/Mdgemp.php';
$userModel = new Mdgemp();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nuevo_empleado'])) {
    $nombre = trim($_POST['nombre'] ?? '');
    $apellido = trim($_POST['apellido'] ?? '');
    $documento = trim($_POST['documento'] ?? '');
    $cargo = trim($_POST['cargo'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Validaciones básicas
    if ($nombre === '' || $apellido === '' || $documento === '' || $cargo === '') {
        $mensaje = 'Todos los campos son obligatorios.';
        $tipo_mensaje = 'danger';
    } else {
        try {
            // Usar el modelo para crear el empleado
            $userModel->crearEmpleado($_POST);
            $mensaje = 'Empleado creado exitosamente.';
            $tipo_mensaje = 'success';
            // Redirigir para evitar reenvío del formulario
            header('Location: ' . $_SERVER['REQUEST_URI'] . '?msg=success');
            exit();
        } catch (Exception $e) {
            $mensaje = $e->getMessage();
            $tipo_mensaje = 'danger';
        }
    }
}

// Obtener datos usando el modelo
$empleados = $userModel->getAllEmpleados();
$tipos_usuario = $userModel->getTiposUsuario();
$empleados_activos = $userModel->getEmpleadosActivos();

// Variables para mensajes
$mensaje = $mensaje ?? '';
$tipo_mensaje = $tipo_mensaje ?? '';

// Consulta permisos (mantener por ahora - podría moverse al modelo después)
require_once __DIR__ . '/../../models/conexion.php';
$conn = new conexion();
$db = $conn->get_conexion();

$permisos = [];
try {
    $stmt = $db->prepare("SELECT p.idpermiso, u.nombre_completo as empleado, p.tipo, p.fecha_inicio, p.fecha_fin, p.estado FROM permisos p LEFT JOIN usu u ON p.idempleado = u.idusu");
    $stmt->execute();
    $permisos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {}

// Consulta turnos
$turnos = [];
try {
    $stmt = $db->prepare("SELECT t.idturno, u.nombre_completo as empleado, t.fecha_inicio, t.fecha_fin, t.horario FROM turnos t LEFT JOIN usu u ON t.idempleado = u.idusu");
    $stmt->execute();
    $turnos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {}

// Consulta vacaciones
$vacaciones = [];
try {
    $stmt = $db->prepare("SELECT v.id, u.nombre_completo as empleado, v.fecha_inicio, v.fecha_fin, v.estado, v.motivo, v.tipo FROM vacaciones v LEFT JOIN usu u ON v.id_empleado = u.idusu");
    $stmt->execute();
    $vacaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {}

// Estadísticas para las tarjetas (empleados_activos ya se obtuvo del modelo)
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
    <link rel="stylesheet" href="/Original-Floraltech/assets/dgemp.css">
    <style>
        .debug-info {
            position: fixed;
            bottom: 10px;
            right: 10px;
            background: #f8f9fa;
            padding: 10px;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            font-size: 12px;
            z-index: 9999;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <?php if ($mensaje): ?>
            <div class="alert alert-<?= $tipo_mensaje ?> alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($mensaje) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Encabezado principal con título y botón -->
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div class="section-title mb-0"><i class="fas fa-users me-2"></i>Gestión de Empleados</div>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#nuevoEmpleadoModal">
                <i class="fas fa-plus me-1"></i> Nuevo Empleado
            </button>
        </div>

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
                    <i class="fas fa-calendar-day"></i>
                    <h3><?= $vacaciones_activas ?></h3>
                    <p>Vacaciones Activas</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <i class="fas fa-user-clock"></i>
                    <h3><?= $permisos_pendientes ?></h3>
                    <p>Permisos Pendientes</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <i class="fas fa-business-time"></i>
                    <h3><?= $turnos_semana ?></h3>
                    <p>Turnos Esta Semana</p>
                </div>
            </div>
        </div>
        <!-- Navegación por pestañas -->
        <ul class="nav nav-tabs mb-4" id="gestionTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="empleados-tab" data-bs-toggle="tab" data-bs-target="#empleados" type="button" role="tab" aria-controls="empleados" aria-selected="true">
                    <i class="fas fa-users me-1"></i> Empleados
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="permisos-tab" data-bs-toggle="tab" data-bs-target="#permisos" type="button" role="tab" aria-controls="permisos" aria-selected="false">
                    <i class="fas fa-user-clock me-1"></i> Permisos
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="turnos-tab" data-bs-toggle="tab" data-bs-target="#turnos" type="button" role="tab" aria-controls="turnos" aria-selected="false">
                    <i class="fas fa-business-time me-1"></i> Turnos
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="vacaciones-tab" data-bs-toggle="tab" data-bs-target="#vacaciones" type="button" role="tab" aria-controls="vacaciones" aria-selected="false">
                    <i class="fas fa-calendar-day me-1"></i> Vacaciones
                </button>
            </li>
        </ul>
        <div class="tab-content" id="gestionTabsContent">
            <div class="tab-pane fade show active" id="empleados" role="tabpanel" aria-labelledby="empleados-tab">
                <!-- Sección Gestión de Empleados -->
                <div class="section-block">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Documento</th>
                                    <th>Cargo</th>
                                    <th>Fecha Ingreso</th>
                                    <th>Tipo</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($empleados as $empleado): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($empleado['idusu']) ?></td>
                                        <td><?= htmlspecialchars($empleado['nombre_completo']) ?></td>
                                        <td><?= htmlspecialchars($empleado['username']) ?></td>
                                        <td><?= htmlspecialchars($empleado['naturaleza']) ?></td>
                                        <td><?= date('d/m/Y', strtotime($empleado['fecha_registro'])) ?></td>
                                        <td>
                                                <?php 
                                                    $tipo_id = (int)$empleado['tpusu_idtpusu'];
                                                ?>
                                                <select class="form-select form-select-sm tipo-select" data-idusu="<?= $empleado['idusu'] ?>">
                                                    <?php foreach ($tipos_usuario as $id => $nombre): ?>
                                                        <option value="<?= $id ?>" <?= $tipo_id === $id ? 'selected' : '' ?>><?= $nombre . ' - ' . $id ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                        </td>
                                        <td>
                                            <span class="badge <?= $empleado['activo'] ? 'bg-success' : 'bg-danger' ?>">
                                                <?= $empleado['activo'] ? 'Activo' : 'Inactivo' ?>
                                            </span>
                                        </td>
                                        <td class="actions-column">
                                            <a href="#" class="btn btn-sm btn-outline-primary" onclick="cargarEmpleado(<?= $empleado['idusu'] ?>)" data-bs-toggle="tooltip" data-bs-placement="top" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="#" class="btn btn-sm btn-outline-danger" onclick="eliminarEmpleado(<?= $empleado['idusu'] ?>)" data-bs-toggle="tooltip" data-bs-placement="top" title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                            <a href="#" class="btn btn-sm btn-outline-info" onclick="verEmpleado(<?= $empleado['idusu'] ?>)" data-bs-toggle="tooltip" data-bs-placement="top" title="Ver Detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <nav aria-label="Page navigation">
                        <ul class="pagination justify-content-center">
                            <li class="page-item disabled">
                                <a class="page-link" href="#" tabindex="-1">Anterior</a>
                            </li>
                            <li class="page-item active"><a class="page-link" href="#">1</a></li>
                            <li class="page-item"><a class="page-link" href="#">2</a></li>
                            <li class="page-item"><a class="page-link" href="#">3</a></li>
                            <li class="page-item">
                                <a class="page-link" href="#">Siguiente</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
            <div class="tab-pane fade" id="permisos" role="tabpanel" aria-labelledby="permisos-tab">
                <!-- Sección Permisos -->
                <div class="section-block">
                    <div class="section-title"><i class="fas fa-user-clock me-2"></i>Gestión de Permisos</div>
                    <div class="card-header d-flex justify-content-between align-items-center mb-3">
                        <span>Solicitudes de Permisos</span>
                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#nuevoPermisoModal">
                            <i class="fas fa-plus me-1"></i> Nuevo Permiso
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Empleado</th>
                                    <th>Tipo</th>
                                    <th>Fecha Inicio</th>
                                    <th>Fecha Fin</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($permisos as $permiso): ?>
                                <tr>
                                    <td><?= htmlspecialchars($permiso['idpermiso']) ?></td>
                                    <td><?= htmlspecialchars($permiso['empleado']) ?></td>
                                    <td><?= htmlspecialchars($permiso['tipo']) ?></td>
                                    <td><?= date('d/m/Y', strtotime($permiso['fecha_inicio'])) ?></td>
                                    <td><?= date('d/m/Y', strtotime($permiso['fecha_fin'])) ?></td>
                                    <td>
                                        <?php 
                                            $estado = $permiso['estado'];
                                            $badge_class = '';
                                            switch ($estado) {
                                                case 'Pendiente':
                                                    $badge_class = 'bg-warning text-dark';
                                                    break;
                                                case 'Aprobado':
                                                    $badge_class = 'bg-success text-white';
                                                    break;
                                                case 'Rechazado':
                                                    $badge_class = 'bg-danger text-white';
                                                    break;
                                                default:
                                                    $badge_class = 'bg-secondary text-white';
                                            }
                                        ?>
                                        <span class="badge <?= $badge_class ?>"><?= htmlspecialchars($estado) ?></span>
                                    </td>
                                    <td class="actions-column">
                                        <a href="#" class="btn btn-sm btn-outline-primary" onclick="editarPermiso(<?= $permiso['idpermiso'] ?>)" data-bs-toggle="tooltip" data-bs-placement="top" title="Editar Permiso"><i class="fas fa-edit"></i></a>
                                        <a href="#" class="btn btn-sm btn-outline-danger" onclick="eliminarPermiso(<?= $permiso['idpermiso'] ?>)" data-bs-toggle="tooltip" data-bs-placement="top" title="Eliminar Permiso"><i class="fas fa-trash"></i></a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="turnos" role="tabpanel" aria-labelledby="turnos-tab">
                <!-- Sección Turnos -->
                <div class="section-block">
                    <div class="section-title"><i class="fas fa-business-time me-2"></i>Gestión de Turnos</div>
                    <div class="card-header d-flex justify-content-between align-items-center mb-3">
                        <span>Gestión de Turnos</span>
                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#nuevoTurnoModal">
                            <i class="fas fa-plus me-1"></i> Nuevo Turno
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Empleado</th>
                                    <th>Fecha Inicio</th>
                                    <th>Fecha Fin</th>
                                    <th>Horario</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($turnos as $turno): ?>
                                <tr>
                                    <td><?= htmlspecialchars($turno['idturno']) ?></td>
                                    <td><?= htmlspecialchars($turno['empleado']) ?></td>
                                    <td><?= date('d/m/Y', strtotime($turno['fecha_inicio'])) ?></td>
                                    <td><?= date('d/m/Y', strtotime($turno['fecha_fin'])) ?></td>
                                    <td><?= htmlspecialchars($turno['horario']) ?></td>
                                    <td class="actions-column">
                                        <a href="#" class="btn btn-sm btn-outline-primary" onclick="editarTurno(<?= $turno['idturno'] ?>)" data-bs-toggle="tooltip" data-bs-placement="top" title="Editar Turno">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="#" class="btn btn-sm btn-outline-danger" onclick="eliminarTurno(<?= $turno['idturno'] ?>)" data-bs-toggle="tooltip" data-bs-placement="top" title="Eliminar Turno"><i class="fas fa-trash"></i></a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="vacaciones" role="tabpanel" aria-labelledby="vacaciones-tab">
                <!-- Sección Vacaciones -->
                <div class="section-block">
                    <div class="section-title"><i class="fas fa-calendar-day me-2"></i>Gestión de Vacaciones</div>
                    <div class="card-header d-flex justify-content-between align-items-center mb-3">
                        <span>Gestión de Vacaciones</span>
                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#nuevaVacacionModal">
                            <i class="fas fa-plus me-1"></i> Nueva Vacación
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Empleado</th>
                                    <th>Fecha Inicio</th>
                                    <th>Fecha Fin</th>
                                    <th>Días</th>
                                    <th>Tipo</th>
                                    <th>Estado</th>
                                    <th>Motivo</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($vacaciones as $vacacion): 
                                // Calcular días de vacaciones
                                $fecha_inicio = new DateTime($vacacion['fecha_inicio']);
                                $fecha_fin = new DateTime($vacacion['fecha_fin']);
                                $diferencia = $fecha_inicio->diff($fecha_fin);
                                $dias_vacaciones = $diferencia->days + 1; // +1 para incluir el día de inicio
                                
                                // Usar el tipo de la base de datos si existe, sino calcularlo
                                $tipo_vacacion = !empty($vacacion['tipo']) ? $vacacion['tipo'] : (
                                    $dias_vacaciones <= 3 ? "Cortas" : (
                                        $dias_vacaciones <= 7 ? "Semanales" : (
                                            $dias_vacaciones <= 15 ? "Quincenales" : "Extendidas"
                                        )
                                    )
                                );
                            ?>
                                <tr>
                                    <td><?= htmlspecialchars($vacacion['id']) ?></td>
                                    <td><?= htmlspecialchars($vacacion['empleado']) ?></td>
                                    <td><?= date('d/m/Y', strtotime($vacacion['fecha_inicio'])) ?></td>
                                    <td><?= date('d/m/Y', strtotime($vacacion['fecha_fin'])) ?></td>
                                    <td><span class="badge bg-secondary"><?= $dias_vacaciones ?> día<?= $dias_vacaciones != 1 ? 's' : '' ?></span></td>
                                    <td><span class="badge bg-primary"><?= $tipo_vacacion ?></span></td>
                                    <td>
                                        <?php 
                                            $estado_vacacion = $vacacion['estado'];
                                            $badge_class_vacacion = '';
                                            switch ($estado_vacacion) {
                                                case 'Programadas':
                                                    $badge_class_vacacion = 'bg-warning text-dark';
                                                    break;
                                                case 'Aprobadas':
                                                    $badge_class_vacacion = 'bg-success text-white';
                                                    break;
                                                case 'Denegadas':
                                                    $badge_class_vacacion = 'bg-danger text-white';
                                                    break;
                                                case 'Finalizadas':
                                                    $badge_class_vacacion = 'bg-secondary text-white';
                                                    break;
                                                case 'En curso':
                                                    $badge_class_vacacion = 'bg-primary text-white';
                                                    break;
                                                default:
                                                    $badge_class_vacacion = 'bg-info text-white';
                                            }
                                        ?>
                                        <span class="badge <?= $badge_class_vacacion ?>"><?= htmlspecialchars($estado_vacacion) ?></span>
                                    </td>
                                    <td><?= htmlspecialchars($vacacion['motivo']) ?></td>
                                    <td class="actions-column">
                                        <a href="#" class="btn btn-sm btn-outline-primary" onclick="editarVacacion(<?= $vacacion['id'] ?>)" data-bs-toggle="tooltip" data-bs-placement="top" title="Editar Vacación"><i class="fas fa-edit"></i></a>
                                        <a href="#" class="btn btn-sm btn-outline-danger" onclick="eliminarVacacion(<?= $vacacion['id'] ?>)" data-bs-toggle="tooltip" data-bs-placement="top" title="Eliminar Vacación"><i class="fas fa-trash"></i></a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Nuevo Empleado -->
        <div class="modal fade" id="nuevoEmpleadoModal" tabindex="-1" aria-labelledby="nuevoEmpleadoModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="nuevoEmpleadoModalLabel">Nuevo Empleado</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" autocomplete="off" onsubmit="return validarNuevoEmpleado();">
                            <input type="hidden" name="nuevo_empleado" value="1">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="nombre" class="form-label">Nombre</label>
                                    <input type="text" class="form-control" id="nombre" name="nombre" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="apellido" class="form-label">Apellido</label>
                                    <input type="text" class="form-control" id="apellido" name="apellido" required>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="documento" class="form-label">Documento</label>
                                    <input type="text" class="form-control" id="documento" name="documento" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="cargo" class="form-label">Cargo</label>
                                    <input type="text" class="form-control" id="cargo" name="cargo" required>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="fecha_ingreso" class="form-label">Fecha de Ingreso</label>
                                    <input type="date" class="form-control" id="fecha_ingreso" name="fecha_ingreso" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="tipo_contrato" class="form-label">Tipo de Contrato</label>
                                    <select class="form-select" id="tipo_contrato" name="tipo_contrato">
                                        <option value="indefinido">Indefinido</option>
                                        <option value="fijo">Fijo</option>
                                        <option value="obra">Obra</option>
                                        <option value="temporal">Temporal</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="estado" class="form-label">Estado</label>
                                    <select class="form-select" id="estado" name="estado">
                                        <option value="activo">Activo</option>
                                        <option value="inactivo">Inactivo</option>
                                        <option value="renuncia">Renuncia</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="password" class="form-label">Contraseña</label>
                                    <input type="password" class="form-control" id="password" name="password" 
                                           placeholder="Dejar vacío para contraseña por defecto (123456)">
                                    <small class="form-text text-muted">Si no se especifica, se usará "123456" como contraseña por defecto</small>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-primary">Guardar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Editar Empleado -->
        <div class="modal fade" id="editarEmpleadoModal" tabindex="-1" aria-labelledby="editarEmpleadoModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editarEmpleadoModalLabel">Editar Empleado</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form>
                            <input type="hidden" id="edit_id">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="edit_nombre" class="form-label">Nombre</label>
                                    <input type="text" class="form-control" id="edit_nombre" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="edit_apellido" class="form-label">Apellido</label>
                                    <input type="text" class="form-control" id="edit_apellido" required>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="edit_documento" class="form-label">Documento</label>
                                    <input type="text" class="form-control" id="edit_documento" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="edit_cargo" class="form-label">Cargo</label>
                                    <input type="text" class="form-control" id="edit_cargo" required>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="edit_fecha_ingreso" class="form-label">Fecha de Ingreso</label>
                                    <input type="date" class="form-control" id="edit_fecha_ingreso" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="edit_tipo_contrato" class="form-label">Tipo de Contrato</label>
                                    <select class="form-select" id="edit_tipo_contrato">
                                        <option value="indefinido">Indefinido</option>
                                        <option value="fijo">Fijo</option>
                                        <option value="obra">Obra</option>
                                        <option value="temporal">Temporal</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="edit_estado" class="form-label">Estado</label>
                                    <select class="form-select" id="edit_estado">
                                        <option value="activo">Activo</option>
                                        <option value="inactivo">Inactivo</option>
                                        <option value="renuncia">Renuncia</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="edit_password" class="form-label">Nueva Contraseña</label>
                                    <input type="password" class="form-control" id="edit_password" name="password" 
                                        placeholder="Dejar vacío para mantener contraseña actual">
                                    <small class="form-text text-muted">Solo llenar si desea cambiar la contraseña</small>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary" onclick="actualizarEmpleado()">Actualizar</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Ver Detalles Empleado -->
        <div class="modal fade" id="verEmpleadoModal" tabindex="-1" aria-labelledby="verEmpleadoModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="verEmpleadoModalLabel">Detalles del Empleado</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form>
                            <input type="hidden" id="ver_id">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Nombre</label>
                                    <input type="text" class="form-control" id="ver_nombre" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Apellido</label>
                                    <input type="text" class="form-control" id="ver_apellido" readonly>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Documento</label>
                                    <input type="text" class="form-control" id="ver_documento" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Cargo</label>
                                    <input type="text" class="form-control" id="ver_cargo" readonly>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Fecha de Ingreso</label>
                                    <input type="date" class="form-control" id="ver_fecha_ingreso" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Tipo de Contrato</label>
                                    <input type="text" class="form-control" id="ver_tipo_contrato" readonly>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Estado</label>
                                    <input type="text" class="form-control" id="ver_estado" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Tipo Usuario</label>
                                    <input type="text" class="form-control" id="ver_tipo_usuario" readonly>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Nuevo Permiso -->
        <div class="modal fade" id="nuevoPermisoModal" tabindex="-1" aria-labelledby="nuevoPermisoModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="nuevoPermisoModalLabel">Nuevo Permiso</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="formNuevoPermiso">
                            <div class="mb-3">
                                <label for="permisoEmpleado" class="form-label">Empleado</label>
                                <select class="form-select" id="permisoEmpleado" name="empleado" required>
                                    <?php foreach ($empleados as $empleado): ?>
                                        <option value="<?= $empleado['idusu'] ?>"><?= htmlspecialchars($empleado['nombre_completo']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                    <label for="permisoTipo" class="form-label">Tipo</label>
                                    <select class="form-select" id="permisoTipo" name="tipo" required>
                                        <option value="Citación judicial/administrativa">Citación judicial/administrativa</option>
                                        <option value="Citación entidad pública">Citación entidad pública</option>
                                        <option value="Calamidad doméstica">Calamidad doméstica</option>
                                        <option value="Maternidad">Maternidad</option>
                                        <option value="Paternidad">Paternidad</option>
                                        <option value="Citas médicas">Citas médicas</option>
                                        <option value="Sindical">Sindical</option>
                                        <option value="Estudio/capacitación">Estudio/capacitación</option>
                                        <option value="Personal">Personal</option>
                                        <option value="Mudanza">Mudanza</option>
                                        <option value="Licencia no remunerada">Licencia no remunerada</option>
                                        <option value="Licencia por luto">Licencia por luto</option>
                                        <option value="Especial (convenio colectivo)">Especial (convenio colectivo)</option>
                                    </select>
                            </div>
                            <div class="mb-3">
                                <label for="permisoFechaInicio" class="form-label">Fecha Inicio</label>
                                <input type="date" class="form-control" id="permisoFechaInicio" name="fecha_inicio" required>
                            </div>
                            <div class="mb-3">
                                <label for="permisoFechaFin" class="form-label">Fecha Fin</label>
                                <input type="date" class="form-control" id="permisoFechaFin" name="fecha_fin" required>
                            </div>
                            <div class="mb-3">
                                <label for="permisoEstado" class="form-label">Estado</label>
                                <select class="form-select" id="permisoEstado" name="estado">
                                    <option value="Pendiente">Pendiente</option>
                                    <option value="Aprobado">Aprobado</option>
                                    <option value="Rechazado">Rechazado</option>
                                </select>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-primary">Guardar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Editar Permiso -->
        <div class="modal fade" id="editarPermisoModal" tabindex="-1" aria-labelledby="editarPermisoModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editarPermisoModalLabel">Editar Permiso</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="formEditarPermiso">
                            <input type="hidden" id="edit_permiso_id" name="id">
                            <div class="mb-3">
                                <label for="edit_permisoEmpleado" class="form-label">Empleado</label>
                                <select class="form-select" id="edit_permisoEmpleado" name="empleado" required>
                                    <?php foreach ($empleados as $empleado): ?>
                                        <option value="<?= $empleado['idusu'] ?>"><?= htmlspecialchars($empleado['nombre_completo']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                    <label for="edit_permisoTipo" class="form-label">Tipo</label>
                                    <select class="form-select" id="edit_permisoTipo" name="tipo" required>
                                        <option value="Citación judicial/administrativa">Citación judicial/administrativa</option>
                                        <option value="Citación entidad pública">Citación entidad pública</option>
                                        <option value="Calamidad doméstica">Calamidad doméstica</option>
                                        <option value="Maternidad">Maternidad</option>
                                        <option value="Paternidad">Paternidad</option>
                                        <option value="Citas médicas">Citas médicas</option>
                                        <option value="Sindical">Sindical</option>
                                        <option value="Estudio/capacitación">Estudio/capacitación</option>
                                        <option value="Personal">Personal</option>
                                        <option value="Mudanza">Mudanza</option>
                                        <option value="Licencia no remunerada">Licencia no remunerada</option>
                                        <option value="Licencia por luto">Licencia por luto</option>
                                        <option value="Especial (convenio colectivo)">Especial (convenio colectivo)</option>
                                    </select>
                            </div>
                            <div class="mb-3">
                                <label for="edit_permisoFechaInicio" class="form-label">Fecha Inicio</label>
                                <input type="date" class="form-control" id="edit_permisoFechaInicio" name="fecha_inicio" required>
                            </div>
                            <div class="mb-3">
                                <label for="edit_permisoFechaFin" class="form-label">Fecha Fin</label>
                                <input type="date" class="form-control" id="edit_permisoFechaFin" name="fecha_fin" required>
                            </div>
                            <div class="mb-3">
                                <label for="edit_permisoEstado" class="form-label">Estado</label>
                                <select class="form-select" id="edit_permisoEstado" name="estado">
                                    <option value="Pendiente">Pendiente</option>
                                    <option value="Aprobado">Aprobado</option>
                                    <option value="Rechazado">Rechazado</option>
                                </select>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-primary">Actualizar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Nuevo Vacación -->
        <div class="modal fade" id="nuevaVacacionModal" tabindex="-1" aria-labelledby="nuevaVacacionModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="nuevaVacacionModalLabel">Nueva Vacación</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="formNuevaVacacion" autocomplete="off">
                            <!-- Eliminado campo oculta para evitar doble registro -->
                            <div class="mb-3">
                                <label for="vacacionEmpleado" class="form-label">Empleado</label>
                                <select class="form-select" id="vacacionEmpleado" name="id_empleado" required>
                                    <?php foreach ($empleados as $empleado): ?>
                                        <option value="<?= $empleado['idusu'] ?>"><?= htmlspecialchars($empleado['nombre_completo']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="vacacionFechaInicio" class="form-label">Fecha Inicio</label>
                                <input type="date" class="form-control" id="vacacionFechaInicio" name="fecha_inicio" required>
                            </div>
                            <div class="mb-3">
                                <label for="vacacionFechaFin" class="form-label">Fecha Fin</label>
                                <input type="date" class="form-control" id="vacacionFechaFin" name="fecha_fin" required>
                            </div>
                            <div class="mb-3">
                                <label for="vacacionMotivo" class="form-label">Motivo</label>
                                <input type="text" class="form-control" id="vacacionMotivo" name="motivo" required>
                            </div>
                            <div class="mb-3">
                                <label for="vacacionTipo" class="form-label">Tipo de Vacaciones</label>
                                <select class="form-select" id="vacacionTipo" name="tipo">
                                    <option value="Cortas">Cortas (1-3 días)</option>
                                    <option value="Semanales">Semanales (4-7 días)</option>
                                    <option value="Quincenales">Quincenales (8-15 días)</option>
                                    <option value="Extendidas">Extendidas (16+ días)</option>
                                    <option value="Anuales">Anuales</option>
                                    <option value="Por enfermedad">Por enfermedad</option>
                                    <option value="Por maternidad">Por maternidad</option>
                                    <option value="Por paternidad">Por paternidad</option>
                                    <option value="Personales">Personales</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="vacacionEstado" class="form-label">Estado</label>
                                <select class="form-select" id="vacacionEstado" name="estado">
                                    <option value="Programadas">Programadas</option>
                                    <option value="Aprobadas">Aprobadas</option>
                                    <option value="Denegadas">Denegadas</option>
                                    <option value="Finalizadas">Finalizadas</option>
                                </select>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-primary">Guardar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Editar Vacación -->
        <div class="modal fade" id="editarVacacionModal" tabindex="-1" aria-labelledby="editarVacacionModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editarVacacionModalLabel">Editar Vacación</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="formEditarVacacion">
                            <input type="hidden" id="edit_vacacion_id" name="id">
                            <div class="mb-3">
                                <label for="edit_vacacionEmpleado" class="form-label">Empleado</label>
                                <select class="form-select" id="edit_vacacionEmpleado" name="id_empleado" required>
                                    <?php foreach ($empleados as $empleado): ?>
                                        <option value="<?= $empleado['idusu'] ?>"><?= htmlspecialchars($empleado['nombre_completo']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="edit_vacacionFechaInicio" class="form-label">Fecha Inicio</label>
                                <input type="date" class="form-control" id="edit_vacacionFechaInicio" name="fecha_inicio" required>
                            </div>
                            <div class="mb-3">
                                <label for="edit_vacacionFechaFin" class="form-label">Fecha Fin</label>
                                <input type="date" class="form-control" id="edit_vacacionFechaFin" name="fecha_fin" required>
                            </div>
                            <div class="mb-3">
                                <label for="edit_vacacionMotivo" class="form-label">Motivo</label>
                                <input type="text" class="form-control" id="edit_vacacionMotivo" name="motivo" required>
                            </div>
                            <div class="mb-3">
                                <label for="edit_vacacionTipo" class="form-label">Tipo de Vacaciones</label>
                                <select class="form-select" id="edit_vacacionTipo" name="tipo">
                                    <option value="Cortas">Cortas (1-3 días)</option>
                                    <option value="Semanales">Semanales (4-7 días)</option>
                                    <option value="Quincenales">Quincenales (8-15 días)</option>
                                    <option value="Extendidas">Extendidas (16+ días)</option>
                                    <option value="Anuales">Anuales</option>
                                    <option value="Por enfermedad">Por enfermedad</option>
                                    <option value="Por maternidad">Por maternidad</option>
                                    <option value="Por paternidad">Por paternidad</option>
                                    <option value="Personales">Personales</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="edit_vacacionEstado" class="form-label">Estado</label>
                                <select class="form-select" id="edit_vacacionEstado" name="estado">
                                    <option value="Programadas">Programadas</option>
                                    <option value="Aprobadas">Aprobadas</option>
                                    <option value="Denegadas">Denegadas</option>
                                    <option value="Finalizadas">Finalizadas</option>
                                </select>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-primary">Actualizar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
                </div>
            </div>
        </div>

        <!-- Modal Nuevo Turno -->
        <div class="modal fade" id="nuevoTurnoModal" tabindex="-1" aria-labelledby="nuevoTurnoModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="nuevoTurnoModalLabel">Nuevo Turno</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="formNuevoTurno">
                            <div class="mb-3">
                                <label for="turnoEmpleado" class="form-label">Empleado</label>
                                <select class="form-select" id="turnoEmpleado" name="empleado" required>
                                    <?php foreach ($empleados as $empleado): ?>
                                        <option value="<?= $empleado['idusu'] ?>"><?= htmlspecialchars($empleado['nombre_completo']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="turnoTemporada" class="form-label">Tipo de temporada</label>
                                <select class="form-select" id="turnoTemporada" name="temporada" required>
                                    <option value="normal">Temporada normal (baja demanda)</option>
                                    <option value="alta">Temporada alta (fechas especiales)</option>
                                    <option value="finsemana">Fines de semana</option>
                                    <option value="especial">Fechas especiales (eventos, novias, funerales)</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="turnoTipo" class="form-label">Turno</label>
                                <select class="form-select" id="turnoTipo" name="tipo_turno" required>
                                    <!-- Opciones dinámicas por JS -->
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="turnoHorario" class="form-label">Horario sugerido</label>
                                <input type="text" class="form-control" id="turnoHorario" name="horario" readonly required>
                            </div>
                            <div class="mb-3">
                                <label for="turnoObservaciones" class="form-label">Observaciones</label>
                                <textarea class="form-control" id="turnoObservaciones" name="observaciones" rows="2" readonly></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="turnoFechaInicio" class="form-label">Fecha Inicio</label>
                                <input type="date" class="form-control" id="turnoFechaInicio" name="fecha_inicio" required>
                            </div>
                            <div class="mb-3">
                                <label for="turnoFechaFin" class="form-label">Fecha Fin</label>
                                <input type="date" class="form-control" id="turnoFechaFin" name="fecha_fin" required>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-primary">Guardar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Editar Turno -->
        <div class="modal fade" id="editarTurnoModal" tabindex="-1" aria-labelledby="editarTurnoModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editarTurnoModalLabel">Editar Turno</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="formEditarTurno">
                            <input type="hidden" id="edit_turno_id" name="id">
                            <div class="mb-3">
                                <label for="edit_turnoEmpleado" class="form-label">Empleado</label>
                                <select class="form-select" id="edit_turnoEmpleado" name="empleado" required>
                                    <?php foreach ($empleados as $empleado): ?>
                                        <option value="<?= $empleado['idusu'] ?>"><?= htmlspecialchars($empleado['nombre_completo']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="edit_turnoFechaInicio" class="form-label">Fecha Inicio</label>
                                <input type="date" class="form-control" id="edit_turnoFechaInicio" name="fecha_inicio" required>
                            </div>
                            <div class="mb-3">
                                <label for="edit_turnoFechaFin" class="form-label">Fecha Fin</label>
                                <input type="date" class="form-control" id="edit_turnoFechaFin" name="fecha_fin" required>
                            </div>
                            <div class="mb-3">
                                <label for="edit_turnoHorario" class="form-label">Horario</label>
                                <input type="text" class="form-control" id="edit_turnoHorario" name="horario" required>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-primary">Actualizar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="debug-info" id="debugInfo">
        <small>🔧 Debug: Funciones JS... <span id="debugStatus">Cargando...</span></small>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/Original-Floraltech/assets/dgemp_v2.js?v=<?= time() ?>"></script>
    <script src="/Original-Floraltech/assets/test_vacaciones.js?v=<?= time() ?>"></script>
    
    <script>
        // Script de debug - verificar que las funciones estén cargadas
        console.log('=== DEBUG GESTIÓN EMPLEADOS ===');
        console.log('cargarEmpleado:', typeof cargarEmpleado);
        console.log('eliminarEmpleado:', typeof eliminarEmpleado);
        console.log('verEmpleado:', typeof verEmpleado);
        console.log('editarPermiso:', typeof editarPermiso);
        console.log('eliminarPermiso:', typeof eliminarPermiso);
        console.log('editarVacacion:', typeof editarVacacion);
        console.log('eliminarVacacion:', typeof eliminarVacacion);
        
        // Actualizar estado visual
        const debugStatus = document.getElementById('debugStatus');
        if (typeof cargarEmpleado !== 'function') {
            console.error('❌ ERROR: cargarEmpleado no está definida');
            debugStatus.innerHTML = '❌ ERROR';
            debugStatus.style.color = 'red';
            alert('ERROR: Las funciones JavaScript no se cargaron correctamente. Revisa la consola (F12).');
        } else {
            console.log('✅ Funciones JavaScript cargadas correctamente');
            debugStatus.innerHTML = '✅ OK';
            debugStatus.style.color = 'green';
            // Ocultar debug después de 3 segundos si todo está bien
            setTimeout(() => {
                document.getElementById('debugInfo').style.display = 'none';
            }, 3000);
        }
    </script>
    <!-- Script de lógica de turnos, debe ir al final -->
</body>
<script>
        document.addEventListener('DOMContentLoaded', function () {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Solución: destruir tooltips antes de abrir cualquier modal
            var modalTriggers = document.querySelectorAll('[data-bs-toggle="modal"]');
            modalTriggers.forEach(function(trigger) {
                trigger.addEventListener('click', function() {
                    tooltipList.forEach(function(tooltip) {
                        tooltip.hide && tooltip.hide();
                        tooltip.dispose && tooltip.dispose();
                    });
                });
            });
        });
</script>
</html>