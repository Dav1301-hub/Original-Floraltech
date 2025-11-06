<?php
// test_simple.php - Prueba básica de AJAX
header('Content-Type: application/json; charset=utf-8');

try {
    require_once __DIR__ . '/../../models/conexion.php';
    $conn = new conexion();
    $db = $conn->get_conexion();
    
    $action = $_POST['action'] ?? 'ping';
    
    if ($action === 'ping') {
        echo json_encode([
            'success' => true,
            'message' => 'Servidor respondiendo correctamente',
            'timestamp' => date('Y-m-d H:i:s'),
            'post_data' => $_POST
        ]);
        exit;
    }
    
    if ($action === 'test_query') {
        $stmt = $db->query('SELECT COUNT(*) as total FROM usu');
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'success' => true,
            'message' => 'Conexión a BD exitosa',
            'total_usuarios' => $result['total']
        ]);
        exit;
    }
    
    if ($action === 'get') {
        $id = intval($_POST['id'] ?? 0);
        $stmt = $db->prepare('SELECT idusu, username, nombre_completo, email, activo FROM usu WHERE idusu = ?');
        $stmt->execute([$id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            echo json_encode([
                'success' => true,
                'data' => $user
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'error' => 'Usuario no encontrado',
                'id_buscado' => $id
            ]);
        }
        exit;
    }
    
    echo json_encode([
        'success' => false,
        'error' => 'Acción no reconocida',
        'action' => $action
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
}
