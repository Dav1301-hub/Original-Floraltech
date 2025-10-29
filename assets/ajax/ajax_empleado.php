<?php
// DEBUG: Forzar mostrar todos los errores como JSON y loguear todo
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Si hay salida previa, limpiar buffer
if (ob_get_level()) ob_end_clean();

// Capturar cualquier salida inesperada
ob_start(function($buffer) {
    if (trim($buffer) !== '') {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'error' => 'Salida inesperada: ' . $buffer
        ]);
        return '';
    }
    return $buffer;
});
// --- Verificación de sesión/autenticación para AJAX ---

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Unificar nombre de variable de sesión
if (!isset($_SESSION['usuario_id'])) {
    if (isset($_SESSION['user_id'])) {
        $_SESSION['usuario_id'] = $_SESSION['user_id'];
    }
}
if (!isset($_SESSION['usuario_id'])) {
    header('Content-Type: application/json');
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'error' => 'Sesión expirada o no autenticado. Por favor, inicie sesión nuevamente.'
    ]);
    exit;
}
// ajax_empleado.php
// Forzar header JSON SIEMPRE
header('Content-Type: application/json; charset=utf-8');

// Manejo global de errores para evitar HTML en la respuesta
set_exception_handler(function($e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Excepción: ' . $e->getMessage()
    ]);
    exit;
});
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => "Error PHP [$errno]: $errstr en $errfile:$errline"
    ]);
    exit;
});

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
