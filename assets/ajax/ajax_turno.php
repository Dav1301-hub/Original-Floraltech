<?php
// ajax_turno.php
require_once __DIR__ . '/../../models/conexion.php';
$conn = new conexion();
$db = $conn->get_conexion();

$action = $_POST['action'] ?? '';
$response = ['success' => false];

if ($action === 'get') {
    $id = intval($_POST['id'] ?? 0);
    $stmt = $db->prepare('SELECT * FROM turnos WHERE idturno = ?');
    $stmt->execute([$id]);
    $response = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    $response['success'] = !!$response;
    echo json_encode($response);
    exit;
}
if ($action === 'update') {
    $id = intval($_POST['id'] ?? 0);
    $idempleado = intval($_POST['empleado'] ?? 0);
    $fecha_inicio = $_POST['fecha_inicio'] ?? date('Y-m-d');
    $fecha_fin = $_POST['fecha_fin'] ?? date('Y-m-d');
    $horario = $_POST['horario'] ?? '';
    $stmt = $db->prepare('UPDATE turnos SET idempleado=?, fecha_inicio=?, fecha_fin=?, horario=? WHERE idturno=?');
    $ok = $stmt->execute([$idempleado, $fecha_inicio, $fecha_fin, $horario, $id]);
    $response['success'] = $ok;
    echo json_encode($response);
    exit;
}
if ($action === 'delete') {
    $id = intval($_POST['id'] ?? 0);
    $stmt = $db->prepare('DELETE FROM turnos WHERE idturno = ?');
    $ok = $stmt->execute([$id]);
    $response['success'] = $ok;
    echo json_encode($response);
    exit;
}
if ($action === 'view') {
    $id = intval($_POST['id'] ?? 0);
    $stmt = $db->prepare('SELECT * FROM turnos WHERE idturno = ?');
    $stmt->execute([$id]);
    $response = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    $response['success'] = !!$response;
    echo json_encode($response);
    exit;
}
if ($action === 'create') {
    $idempleado = intval($_POST['empleado'] ?? 0);
    $fecha_inicio = $_POST['fecha_inicio'] ?? date('Y-m-d');
    $fecha_fin = $_POST['fecha_fin'] ?? date('Y-m-d');
    $horario = $_POST['horario'] ?? '';
    $stmt = $db->prepare('INSERT INTO turnos (idempleado, fecha_inicio, fecha_fin, horario) VALUES (?, ?, ?, ?)');
    $ok = $stmt->execute([$idempleado, $fecha_inicio, $fecha_fin, $horario]);
    $response['success'] = $ok;
    echo json_encode($response);
    exit;
}
// Puedes agregar acciones para vacaciones aquí
