<?php
// test_ajax.php - Script de diagnóstico para probar AJAX endpoints
header('Content-Type: application/json; charset=utf-8');

// Información de depuración
$debug = [
    'php_version' => phpversion(),
    'session_status' => session_status(),
    'session_id' => session_id(),
    'post_data' => $_POST,
    'server_info' => [
        'REQUEST_METHOD' => $_SERVER['REQUEST_METHOD'] ?? 'N/A',
        'CONTENT_TYPE' => $_SERVER['CONTENT_TYPE'] ?? 'N/A',
        'HTTP_REFERER' => $_SERVER['HTTP_REFERER'] ?? 'N/A',
    ],
    'timestamp' => date('Y-m-d H:i:s')
];

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$debug['session_after_start'] = [
    'status' => session_status(),
    'id' => session_id(),
    'usuario_id' => $_SESSION['usuario_id'] ?? 'NO EXISTE',
    'user_id' => $_SESSION['user_id'] ?? 'NO EXISTE',
    'all_session_vars' => array_keys($_SESSION)
];

// Probar conexión a BD
try {
    require_once __DIR__ . '/../../models/conexion.php';
    $conn = new conexion();
    $db = $conn->get_conexion();
    
    $debug['database'] = [
        'connection' => 'OK',
        'pdo_driver' => $db->getAttribute(PDO::ATTR_DRIVER_NAME)
    ];
    
    // Contar registros en tablas clave
    $stmt = $db->query('SELECT COUNT(*) as total FROM usu');
    $debug['database']['total_usuarios'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    $stmt = $db->query('SELECT COUNT(*) as total FROM permisos');
    $debug['database']['total_permisos'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    $stmt = $db->query('SELECT COUNT(*) as total FROM turnos');
    $debug['database']['total_turnos'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    $stmt = $db->query('SELECT COUNT(*) as total FROM vacaciones');
    $debug['database']['total_vacaciones'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
} catch (Exception $e) {
    $debug['database'] = [
        'connection' => 'ERROR',
        'error' => $e->getMessage()
    ];
}

echo json_encode([
    'success' => true,
    'message' => 'Test AJAX endpoint funcionando correctamente',
    'debug' => $debug
], JSON_PRETTY_PRINT);
