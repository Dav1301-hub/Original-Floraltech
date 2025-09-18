<?php
// ajax_empleado.php
require_once __DIR__ . '/../../models/conexion.php';
$conn = new conexion();
$db = $conn->get_conexion();

$action = $_POST['action'] ?? '';
$response = ['success' => false];

if ($action === 'get') {
    $id = intval($_POST['id'] ?? 0);
    $stmt = $db->prepare('SELECT * FROM usu WHERE idusu = ?');
    $stmt->execute([$id]);
    $response = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    $response['success'] = !!$response;
    echo json_encode($response);
    exit;
}
if ($action === 'update') {
    $id = intval($_POST['id'] ?? 0);
    $nombre = trim($_POST['nombre'] ?? '');
    $apellido = trim($_POST['apellido'] ?? '');
    $cargo = trim($_POST['cargo'] ?? '');
    $fecha_ingreso = $_POST['fecha_ingreso'] ?? date('Y-m-d');
    $tipo_contrato = $_POST['tipo_contrato'] ?? 'indefinido';
    $estado = $_POST['estado'] ?? 'activo';
    $activo = ($estado === 'activo') ? 1 : 0;
    $nombre_completo = $nombre . ' ' . $apellido;
    $stmt = $db->prepare('UPDATE usu SET nombre_completo=?, naturaleza=?, fecha_registro=?, activo=? WHERE idusu=?');
    $ok = $stmt->execute([$nombre_completo, $cargo, $fecha_ingreso, $activo, $id]);
    $response['success'] = $ok;
    echo json_encode($response);
    exit;
}
if ($action === 'delete') {
    $id = intval($_POST['id'] ?? 0);
    $stmt = $db->prepare('DELETE FROM usu WHERE idusu = ?');
    $ok = $stmt->execute([$id]);
    $response['success'] = $ok;
    echo json_encode($response);
    exit;
}
if ($action === 'view') {
    $id = intval($_POST['id'] ?? 0);
    $stmt = $db->prepare('SELECT * FROM usu WHERE idusu = ?');
    $stmt->execute([$id]);
    $response = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    $response['success'] = !!$response;
    echo json_encode($response);
    exit;
}
// Puedes agregar acciones para permisos, turnos y vacaciones aquí
