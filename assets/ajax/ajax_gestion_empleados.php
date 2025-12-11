<?php
// ajax_gestion_empleados.php - Gestor unificado de Permisos, Turnos y Vacaciones
// Maneja: permisos, turnos, vacaciones
// Acciones: create_permiso, get_permiso, update_permiso, delete_permiso
//           create_turno, get_turno, update_turno, delete_turno
//           create_vacacion, get_vacacion, update_vacacion, delete_vacacion

header('Content-Type: application/json; charset=utf-8');

if (ob_get_level()) ob_end_clean();
ob_start();

set_exception_handler(function($e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Excepción: ' . $e->getMessage()
    ]);
    exit;
});

require_once __DIR__ . '/../../models/conexion.php';
require_once __DIR__ . '/../../models/Mdgemp.php';

$conn_obj = new conexion();
$db = $conn_obj->get_conexion();
$mdgemp = new Mdgemp();

$action = $_POST['action'] ?? '';
$response = ['success' => false];

// ============================================
// CRUD PERMISOS
// ============================================

if ($action === 'create_permiso') {
    $idempleado = intval($_POST['idempleado'] ?? $_POST['empleado'] ?? 0);
    $tipo = $_POST['tipo'] ?? '';
    $fecha_inicio = $_POST['fecha_inicio'] ?? '';
    $fecha_fin = $_POST['fecha_fin'] ?? '';
    $estado = $_POST['estado'] ?? 'Pendiente';
    
    if ($idempleado === 0 || empty($tipo) || empty($fecha_inicio) || empty($fecha_fin)) {
        echo json_encode(['success' => false, 'error' => 'Campos obligatorios faltantes']);
        exit;
    }
    
    try {
        $id = $mdgemp->crearPermiso($idempleado, $tipo, $fecha_inicio, $fecha_fin, $estado);
        echo json_encode(['success' => true, 'id' => $id]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}

if ($action === 'get_permiso') {
    $id = intval($_POST['id'] ?? 0);
    
    if ($id === 0) {
        echo json_encode(['success' => false, 'error' => 'ID inválido']);
        exit;
    }
    
    try {
        $permiso = $mdgemp->getPermisoById($id);
        if ($permiso) {
            $permiso['success'] = true;
            echo json_encode($permiso);
        } else {
            echo json_encode(['success' => false, 'error' => 'Permiso no encontrado']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}

if ($action === 'update_permiso') {
    $id = intval($_POST['id'] ?? 0);
    $idempleado = intval($_POST['idempleado'] ?? $_POST['empleado'] ?? 0);
    $tipo = $_POST['tipo'] ?? '';
    $fecha_inicio = $_POST['fecha_inicio'] ?? '';
    $fecha_fin = $_POST['fecha_fin'] ?? '';
    $estado = $_POST['estado'] ?? 'Pendiente';
    
    if ($id === 0 || $idempleado === 0 || empty($tipo)) {
        echo json_encode(['success' => false, 'error' => 'Datos inválidos']);
        exit;
    }
    
    try {
        $ok = $mdgemp->actualizarPermiso($id, $idempleado, $tipo, $fecha_inicio, $fecha_fin, $estado);
        echo json_encode(['success' => $ok]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}

if ($action === 'delete_permiso') {
    $id = intval($_POST['id'] ?? 0);
    
    if ($id === 0) {
        echo json_encode(['success' => false, 'error' => 'ID inválido']);
        exit;
    }
    
    try {
        $ok = $mdgemp->eliminarPermiso($id);
        echo json_encode(['success' => $ok]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}

// ============================================
// CRUD TURNOS
// ============================================

if ($action === 'create_turno') {
    $idempleado = intval($_POST['idempleado'] ?? $_POST['empleado'] ?? 0);
    $fecha_inicio = $_POST['fecha_inicio'] ?? '';
    $fecha_fin = $_POST['fecha_fin'] ?? '';
    $horario = $_POST['horario'] ?? '';
    $tipo_temporada = $_POST['tipo_temporada'] ?? $_POST['temporada'] ?? '';
    $turno = $_POST['turno'] ?? $_POST['tipo_turno'] ?? '';
    $observaciones = $_POST['observaciones'] ?? '';
    
    if ($idempleado === 0 || empty($fecha_inicio) || empty($fecha_fin)) {
        echo json_encode(['success' => false, 'error' => 'Campos obligatorios faltantes']);
        exit;
    }
    
    try {
        $id = $mdgemp->crearTurno($idempleado, $fecha_inicio, $fecha_fin, $horario, $tipo_temporada, $turno, $observaciones);
        echo json_encode(['success' => true, 'id' => $id]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}

if ($action === 'get_turno') {
    $id = intval($_POST['id'] ?? 0);
    
    if ($id === 0) {
        echo json_encode(['success' => false, 'error' => 'ID inválido']);
        exit;
    }
    
    try {
        $turno = $mdgemp->getTurnoById($id);
        if ($turno) {
            $turno['success'] = true;
            echo json_encode($turno);
        } else {
            echo json_encode(['success' => false, 'error' => 'Turno no encontrado']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}

if ($action === 'update_turno') {
    $id = intval($_POST['id'] ?? 0);
    $idempleado = intval($_POST['idempleado'] ?? $_POST['empleado'] ?? 0);
    $fecha_inicio = $_POST['fecha_inicio'] ?? '';
    $fecha_fin = $_POST['fecha_fin'] ?? '';
    $horario = $_POST['horario'] ?? '';
    $tipo_temporada = $_POST['tipo_temporada'] ?? $_POST['temporada'] ?? '';
    $turno = $_POST['turno'] ?? $_POST['tipo_turno'] ?? '';
    $observaciones = $_POST['observaciones'] ?? '';
    
    if ($id === 0 || $idempleado === 0) {
        echo json_encode(['success' => false, 'error' => 'Datos inválidos']);
        exit;
    }
    
    try {
        $ok = $mdgemp->actualizarTurno($id, $idempleado, $fecha_inicio, $fecha_fin, $horario, $tipo_temporada, $turno, $observaciones);
        echo json_encode(['success' => $ok]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}

if ($action === 'delete_turno') {
    $id = intval($_POST['id'] ?? 0);
    
    if ($id === 0) {
        echo json_encode(['success' => false, 'error' => 'ID inválido']);
        exit;
    }
    
    try {
        $ok = $mdgemp->eliminarTurno($id);
        echo json_encode(['success' => $ok]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}

// ============================================
// CRUD VACACIONES
// ============================================

if ($action === 'create_vacacion') {
    $id_empleado = intval($_POST['id_empleado'] ?? $_POST['empleado'] ?? 0);
    $fecha_inicio = $_POST['fecha_inicio'] ?? '';
    $fecha_fin = $_POST['fecha_fin'] ?? '';
    $motivo = $_POST['motivo'] ?? '';
    $estado = $_POST['estado'] ?? 'Programadas';
    
    if ($id_empleado === 0 || empty($fecha_inicio) || empty($fecha_fin) || empty($motivo)) {
        echo json_encode(['success' => false, 'error' => 'Campos obligatorios faltantes']);
        exit;
    }
    
    try {
        $id = $mdgemp->crearVacacion($id_empleado, $fecha_inicio, $fecha_fin, $motivo, $estado);
        echo json_encode(['success' => true, 'id' => $id]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}

if ($action === 'get_vacacion') {
    $id = intval($_POST['id'] ?? 0);
    
    if ($id === 0) {
        echo json_encode(['success' => false, 'error' => 'ID inválido']);
        exit;
    }
    
    try {
        $vacacion = $mdgemp->getVacacionById($id);
        if ($vacacion) {
            $vacacion['success'] = true;
            echo json_encode($vacacion);
        } else {
            echo json_encode(['success' => false, 'error' => 'Vacación no encontrada']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}

if ($action === 'update_vacacion') {
    $id = intval($_POST['id'] ?? 0);
    $id_empleado = intval($_POST['id_empleado'] ?? $_POST['empleado'] ?? 0);
    $fecha_inicio = $_POST['fecha_inicio'] ?? '';
    $fecha_fin = $_POST['fecha_fin'] ?? '';
    $motivo = $_POST['motivo'] ?? '';
    $estado = $_POST['estado'] ?? 'Programadas';
    
    if ($id === 0 || $id_empleado === 0) {
        echo json_encode(['success' => false, 'error' => 'Datos inválidos']);
        exit;
    }
    
    try {
        $ok = $mdgemp->actualizarVacacionCompleta($id, $id_empleado, $fecha_inicio, $fecha_fin, $motivo, $estado);
        echo json_encode(['success' => $ok]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}

if ($action === 'delete_vacacion') {
    $id = intval($_POST['id'] ?? 0);
    
    if ($id === 0) {
        echo json_encode(['success' => false, 'error' => 'ID inválido']);
        exit;
    }
    
    try {
        $ok = $mdgemp->eliminarVacacion($id);
        echo json_encode(['success' => $ok]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}

// Acción desconocida
echo json_encode([
    'success' => false,
    'error' => 'Acción no reconocida: ' . htmlspecialchars($action)
]);
exit;
