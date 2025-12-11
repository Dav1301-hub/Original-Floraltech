<?php
// ajax_empleado.php - Gestor unificado de empleados y clientes
// Maneja: empleados (usu), clientes (cli)
// Acciones: create, read, update, delete, list, get, get_cli, create_cli, update_cli, delete_cli

header('Content-Type: application/json; charset=utf-8');

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
$conn = new conexion();
$db = $conn->get_conexion();

$action = $_POST['action'] ?? '';
$response = ['success' => false];

// ============================================
// CRUD EMPLEADOS (tabla usu)
// ============================================

if ($action === 'get') {
    $id = intval($_POST['id'] ?? 0);
    $stmt = $db->prepare('SELECT idusu, username, nombre_completo, naturaleza, telefono, email, clave, tpusu_idtpusu, fecha_registro, activo FROM usu WHERE idusu = ?');
    $stmt->execute([$id]);
    $response = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    $response['success'] = !!$response;
    echo json_encode($response);
    exit;
}

if ($action === 'create') {
    $nombre = trim($_POST['nombre_completo'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $naturaleza = trim($_POST['naturaleza'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $rol = intval($_POST['tpusu_idtpusu'] ?? 5);
    $estado = isset($_POST['activo']) ? intval($_POST['activo']) : 1;
    $password = trim($_POST['password'] ?? '123456');

    // Validación
    if (!$nombre || !$username || !$email) {
        echo json_encode(['success' => false, 'error' => 'Nombre, usuario y email son obligatorios']);
        exit;
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'error' => 'El email no es válido']);
        exit;
    }
    
    if (!empty($telefono) && !preg_match('/^\d{7,}$/', $telefono)) {
        echo json_encode(['success' => false, 'error' => 'El teléfono debe contener al menos 7 dígitos']);
        exit;
    }

    $chk = $db->prepare("SELECT idusu FROM usu WHERE email = :email OR username = :username LIMIT 1");
    $chk->execute([':email' => $email, ':username' => $username]);
    if ($chk->fetch()) {
        echo json_encode(['success' => false, 'error' => 'Ya existe un usuario con ese email o username']);
        exit;
    }

    try {
        $stmt = $db->prepare("
            INSERT INTO usu (username, nombre_completo, naturaleza, telefono, email, clave, tpusu_idtpusu, fecha_registro, activo)
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), ?)
        ");
        $ok = $stmt->execute([
            $username,
            $nombre,
            $naturaleza,
            $telefono,
            $email,
            password_hash($password, PASSWORD_DEFAULT),
            $rol,
            $estado
        ]);
        echo json_encode(['success' => $ok, 'id' => $ok ? $db->lastInsertId() : null]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => 'Error: ' . $e->getMessage()]);
    }
    exit;
}

if ($action === 'update') {
    $id = intval($_POST['id'] ?? 0);
    $nombre = trim($_POST['nombre_completo'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $naturaleza = trim($_POST['naturaleza'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $rol = intval($_POST['tpusu_idtpusu'] ?? 5);
    $estado = isset($_POST['activo']) ? intval($_POST['activo']) : 1;
    $password = trim($_POST['password'] ?? '');

    if ($id === 0) {
        echo json_encode(['success' => false, 'error' => 'ID de usuario no válido']);
        exit;
    }

    if (!$nombre || !$username || !$email) {
        echo json_encode(['success' => false, 'error' => 'Nombre, usuario y email son obligatorios']);
        exit;
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'error' => 'El email no es válido']);
        exit;
    }

    $chk = $db->prepare("SELECT idusu FROM usu WHERE (email = :email OR username = :username) AND idusu <> :id LIMIT 1");
    $chk->execute([':email' => $email, ':username' => $username, ':id' => $id]);
    if ($chk->fetch()) {
        echo json_encode(['success' => false, 'error' => 'Ya existe un usuario con ese email o username']);
        exit;
    }

    try {
        $fields = [
            'nombre_completo' => $nombre,
            'username'        => $username,
            'naturaleza'      => $naturaleza,
            'telefono'        => $telefono,
            'email'           => $email,
            'tpusu_idtpusu'   => $rol,
            'activo'          => $estado
        ];
        $setParts = [];
        $params = [];
        foreach ($fields as $k => $v) {
            $setParts[] = "$k = ?";
            $params[] = $v;
        }
        if (!empty($password)) {
            $setParts[] = "clave = ?";
            $params[] = password_hash($password, PASSWORD_DEFAULT);
        }
        $params[] = $id;
        $sql = "UPDATE usu SET " . implode(', ', $setParts) . " WHERE idusu = ?";
        $stmt = $db->prepare($sql);
        $ok = $stmt->execute($params);
        echo json_encode(['success' => $ok]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => 'Error: ' . $e->getMessage()]);
    }
    exit;
}

if ($action === 'delete') {
    $id = intval($_POST['id'] ?? 0);
    
    if ($id === 0) {
        echo json_encode(['success' => false, 'error' => 'ID de usuario no válido']);
        exit;
    }
    
    try {
        // Opción 1: Marcar como inactivo (más seguro)
        $stmt = $db->prepare('UPDATE usu SET activo = 0 WHERE idusu = ?');
        $ok = $stmt->execute([$id]);
        echo json_encode(['success' => $ok]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => 'Error: ' . $e->getMessage()]);
    }
    exit;
}

// --- ACCIONES PARA CLIENTES (tabla cli) ---
if ($action === 'get_cli') {
    $id = intval($_POST['id'] ?? 0);
    $stmt = $db->prepare('SELECT idcli, nombre, email, telefono, direccion, fecha_registro FROM cli WHERE idcli = ?');
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    $row['success'] = !!$row;
    echo json_encode($row);
    exit;
}

if ($action === 'create_cli') {
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $direccion = trim($_POST['direccion'] ?? '');
    $fecha_reg = $_POST['fecha_registro'] ?? date('Y-m-d');

    if (!$nombre) {
        echo json_encode(['success' => false, 'error' => 'El nombre es obligatorio']);
        exit;
    }
    
    if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'error' => 'El email no es válido']);
        exit;
    }

    if ($email) {
        $chk = $db->prepare("SELECT idcli FROM cli WHERE email = ? LIMIT 1");
        $chk->execute([$email]);
        if ($chk->fetch()) {
            echo json_encode(['success' => false, 'error' => 'Ya existe un cliente con ese email']);
            exit;
        }
    }

    try {
        $stmt = $db->prepare("INSERT INTO cli (nombre, email, telefono, direccion, fecha_registro) VALUES (?, ?, ?, ?, ?)");
        $ok = $stmt->execute([$nombre, $email ?: null, $telefono ?: null, $direccion, $fecha_reg]);
        echo json_encode(['success' => $ok, 'id' => $ok ? $db->lastInsertId() : null]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => 'Error: ' . $e->getMessage()]);
    }
    exit;
}

if ($action === 'update_cli') {
    $id = intval($_POST['id'] ?? 0);
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $direccion = trim($_POST['direccion'] ?? '');
    $fecha_reg = $_POST['fecha_registro'] ?? date('Y-m-d');

    if ($id === 0) {
        echo json_encode(['success' => false, 'error' => 'ID de cliente no válido']);
        exit;
    }

    if (!$nombre) {
        echo json_encode(['success' => false, 'error' => 'El nombre es obligatorio']);
        exit;
    }
    
    if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'error' => 'El email no es válido']);
        exit;
    }

    if ($email) {
        $chk = $db->prepare("SELECT idcli FROM cli WHERE email = ? AND idcli <> ? LIMIT 1");
        $chk->execute([$email, $id]);
        if ($chk->fetch()) {
            echo json_encode(['success' => false, 'error' => 'Ya existe un cliente con ese email']);
            exit;
        }
    }

    try {
        $stmt = $db->prepare("UPDATE cli SET nombre=?, email=?, telefono=?, direccion=?, fecha_registro=? WHERE idcli=?");
        $ok = $stmt->execute([$nombre, $email ?: null, $telefono ?: null, $direccion, $fecha_reg, $id]);
        echo json_encode(['success' => $ok]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => 'Error: ' . $e->getMessage()]);
    }
    exit;
}

if ($action === 'delete_cli') {
    $id = intval($_POST['id'] ?? 0);
    
    if ($id === 0) {
        echo json_encode(['success' => false, 'error' => 'ID de cliente no válido']);
        exit;
    }
    
    try {
        $stmt = $db->prepare("DELETE FROM cli WHERE idcli = ?");
        $ok = $stmt->execute([$id]);
        echo json_encode(['success' => $ok]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => 'Error: ' . $e->getMessage()]);
    }
    exit;
}

if ($action === 'view') {
    $id = intval($_POST['id'] ?? 0);
    
    if ($id === 0) {
        echo json_encode(['success' => false, 'error' => 'ID de usuario no válido']);
        exit;
    }
    
    try {
        $stmt = $db->prepare('SELECT idusu, nombre_completo, username, email, telefono, tpusu_idtpusu, activo, naturaleza, fecha_registro FROM usu WHERE idusu = ?');
        $stmt->execute([$id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            $response = $user;
            $response['success'] = true;
        } else {
            $response['error'] = 'Usuario no encontrado';
        }
    } catch (Exception $e) {
        $response['error'] = 'Error: ' . $e->getMessage();
    }
    echo json_encode($response);
    exit;
}

// Acción desconocida
echo json_encode([
    'success' => false,
    'error' => 'Acción no reconocida: ' . htmlspecialchars($action)
]);
exit;
