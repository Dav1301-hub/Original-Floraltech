<?php
// ajax_vacacion.php
require_once __DIR__ . '/../../models/conexion.php';
$conn = new conexion();
$db = $conn->get_conexion();

$action = $_POST['action'] ?? '';
$response = ['success' => false];

if ($action === 'get') {
    $id = intval($_POST['id'] ?? 0);
    $stmt = $db->prepare('SELECT * FROM vacaciones WHERE id = ?');
    $stmt->execute([$id]);
    $response = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    $response['success'] = !!$response;
    echo json_encode($response);
    exit;
}
if ($action === 'update') {
    $id = intval($_POST['id'] ?? 0);
    $id_empleado = intval($_POST['id_empleado'] ?? 0);
    $fecha_inicio = $_POST['fecha_inicio'] ?? date('Y-m-d');
    $fecha_fin = $_POST['fecha_fin'] ?? date('Y-m-d');
    $motivo = $_POST['motivo'] ?? '';
    $tipo = $_POST['tipo'] ?? 'Personales';
    $estado = $_POST['estado'] ?? 'Programadas';
    
    try {
        $stmt = $db->prepare('UPDATE vacaciones SET id_empleado=?, fecha_inicio=?, fecha_fin=?, motivo=?, tipo=?, estado=? WHERE id=?');
        $ok = $stmt->execute([$id_empleado, $fecha_inicio, $fecha_fin, $motivo, $tipo, $estado, $id]);
        $response['success'] = $ok;
        
        if (!$ok) {
            $response['error'] = 'Error al actualizar en la base de datos';
        }
    } catch (Exception $e) {
        $response['error'] = 'Error: ' . $e->getMessage();
    }
    
    echo json_encode($response);
    exit;
}
if ($action === 'delete') {
    $id = intval($_POST['id'] ?? 0);
    $stmt = $db->prepare('DELETE FROM vacaciones WHERE id = ?');
    $ok = $stmt->execute([$id]);
    $response['success'] = $ok;
    echo json_encode($response);
    exit;
}
if ($action === 'view') {
    $id = intval($_POST['id'] ?? 0);
    $stmt = $db->prepare('SELECT * FROM vacaciones WHERE id = ?');
    $stmt->execute([$id]);
    $response = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    $response['success'] = !!$response;
    echo json_encode($response);
    exit;
}
if ($action === 'create') {
    $id_empleado = intval($_POST['id_empleado'] ?? 0);
    $fecha_inicio = $_POST['fecha_inicio'] ?? date('Y-m-d');
    $fecha_fin = $_POST['fecha_fin'] ?? date('Y-m-d');
    $motivo = $_POST['motivo'] ?? '';
    $tipo = $_POST['tipo'] ?? 'Personales';
    $estado = $_POST['estado'] ?? 'Programadas';
    
    // Validación básica
    if ($id_empleado === 0) {
        $response['error'] = 'ID de empleado no válido';
        echo json_encode($response);
        exit;
    }
    
    if (empty($motivo)) {
        $response['error'] = 'El motivo es obligatorio';
        echo json_encode($response);
        exit;
    }
    
    try {
        $stmt = $db->prepare('INSERT INTO vacaciones (id_empleado, fecha_inicio, fecha_fin, motivo, tipo, estado) VALUES (?, ?, ?, ?, ?, ?)');
        $ok = $stmt->execute([$id_empleado, $fecha_inicio, $fecha_fin, $motivo, $tipo, $estado]);
        $response['success'] = $ok;
        
        if (!$ok) {
            $response['error'] = 'Error al insertar en la base de datos';
        }
    } catch (Exception $e) {
        $response['error'] = 'Error: ' . $e->getMessage();
    }
    
    echo json_encode($response);
    exit;
}
