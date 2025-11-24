<?php
// ajax_permiso.php
// Forzar header JSON SIEMPRE
header('Content-Type: application/json; charset=utf-8');

// Limpiar cualquier salida previa
if (ob_get_level()) ob_end_clean();
ob_start();

// Manejo global de errores
set_exception_handler(function($e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Excepción: ' . $e->getMessage()
    ]);
    exit;
});

require_once __DIR__ . '/../../models/conexion.php';
$conn = new Conexion();
$db = $conn->get_conexion();

$action = $_POST['action'] ?? '';
$response = ['success' => false];

if ($action === 'get') {
    $id = intval($_POST['id'] ?? 0);
    $stmt = $db->prepare('SELECT * FROM permisos WHERE idpermiso = ?');
    $stmt->execute([$id]);
    $response = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    $response['success'] = !!$response;
    echo json_encode($response);
    exit;
}
if ($action === 'update') {
    $id = intval($_POST['id'] ?? 0);
    $idempleado = intval($_POST['empleado'] ?? 0);
    $tipo = trim($_POST['tipo'] ?? '');
    $fecha_inicio = $_POST['fecha_inicio'] ?? date('Y-m-d');
    $fecha_fin = $_POST['fecha_fin'] ?? date('Y-m-d');
    $estado = $_POST['estado'] ?? 'Pendiente';
    $stmt = $db->prepare('UPDATE permisos SET idempleado=?, tipo=?, fecha_inicio=?, fecha_fin=?, estado=? WHERE idpermiso=?');
    $ok = $stmt->execute([$idempleado, $tipo, $fecha_inicio, $fecha_fin, $estado, $id]);
    $response['success'] = $ok;
    echo json_encode($response);
    exit;
}
if ($action === 'delete') {
    $id = intval($_POST['id'] ?? 0);
    $stmt = $db->prepare('DELETE FROM permisos WHERE idpermiso = ?');
    $ok = $stmt->execute([$id]);
    $response['success'] = $ok;
    echo json_encode($response);
    exit;
}
if ($action === 'view') {
    $id = intval($_POST['id'] ?? 0);
    $stmt = $db->prepare('SELECT * FROM permisos WHERE idpermiso = ?');
    $stmt->execute([$id]);
    $response = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    $response['success'] = !!$response;
    echo json_encode($response);
    exit;
}
if ($action === 'create') {
    $idempleado = intval($_POST['empleado'] ?? 0);
    $tipo = trim($_POST['tipo'] ?? '');
    $fecha_inicio = $_POST['fecha_inicio'] ?? date('Y-m-d');
    $fecha_fin = $_POST['fecha_fin'] ?? date('Y-m-d');
    $estado = $_POST['estado'] ?? 'Pendiente';
    
    // Validación
    if ($idempleado == 0 || empty($tipo)) {
        $response['error'] = 'Empleado y tipo son obligatorios';
        echo json_encode($response);
        exit;
    }
    
    try {
        $stmt = $db->prepare('INSERT INTO permisos (idempleado, tipo, fecha_inicio, fecha_fin, estado) VALUES (?, ?, ?, ?, ?)');
        $ok = $stmt->execute([$idempleado, $tipo, $fecha_inicio, $fecha_fin, $estado]);
        $response['success'] = $ok;
        if (!$ok) {
            $response['error'] = $stmt->errorInfo();
        }
    } catch (Exception $e) {
        $response['success'] = false;
        $response['error'] = $e->getMessage();
    }
    echo json_encode($response);
    exit;
}

// Si ninguna acción fue reconocida
echo json_encode([
    'success' => false,
    'error' => 'Acción no reconocida: ' . $action
]);
exit;
