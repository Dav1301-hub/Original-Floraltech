<?php
// ajax_empleado.php - Endpoint AJAX para gestión de empleados
header('Content-Type: application/json; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', 0); // No mostrar errores en pantalla, solo en JSON

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
    $password = trim($_POST['password'] ?? '');
    $activo = ($estado === 'activo') ? 1 : 0;
    $nombre_completo = $nombre . ' ' . $apellido;
    // Si se proporcionó una nueva contraseña, incluirla en la actualización
    if (!empty($password)) {
        $clave_hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $db->prepare('UPDATE usu SET nombre_completo=?, naturaleza=?, fecha_registro=?, activo=?, clave=? WHERE idusu=?');
        $ok = $stmt->execute([$nombre_completo, $cargo, $fecha_ingreso, $activo, $clave_hash, $id]);
    } else {
        // Si no se proporcionó contraseña, no actualizar el campo clave
        $stmt = $db->prepare('UPDATE usu SET nombre_completo=?, naturaleza=?, fecha_registro=?, activo=? WHERE idusu=?');
        $ok = $stmt->execute([$nombre_completo, $cargo, $fecha_ingreso, $activo, $id]);
    }
    $response['success'] = isset($ok) ? $ok : false;
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
    require_once __DIR__ . '/../../models/Mdgemp.php';
    $mdgemp = new Mdgemp();
    try {
        $user = $mdgemp->getUserById($id);
        if ($user) {
            $response = [
                'success' => true,
                'idusu' => $user['idusu'],
                'nombre' => $user['nombre_completo'] ? explode(' ', $user['nombre_completo'])[0] : '',
                'apellido' => $user['nombre_completo'] ? (implode(' ', array_slice(explode(' ', $user['nombre_completo']), 1))) : '',
                'username' => $user['username'],
                'naturaleza' => $user['naturaleza'],
                'fecha_registro' => $user['fecha_registro'],
                'estado' => $user['activo'] ? 'activo' : 'inactivo',
                'tipo_usuario' => $user['tipo_usuario_nombre'] ?? '',
            ];
        } else {
            $response = [
                'success' => false,
                'debug' => 'No se encontró usuario con id=' . $id,
                'user_debug' => $user
            ];
        }
    } catch (PDOException $e) {
        $response = [
            'success' => false,
            'error' => 'PDOException: ' . $e->getMessage(),
            'debug' => 'Error en la consulta getUserById',
        ];
    } catch (Exception $e) {
        $response = [
            'success' => false,
            'error' => 'Exception: ' . $e->getMessage(),
            'debug' => 'Error general en getUserById',
        ];
    }
    echo json_encode($response);
    exit;
}

// Puedes agregar acciones para permisos, turnos y vacaciones aquí

// Si ninguna acción fue reconocida, devolver error JSON por defecto
echo json_encode([
    'success' => false,
    'error' => 'Acción no reconocida o parámetro action faltante.'
]);
exit;
