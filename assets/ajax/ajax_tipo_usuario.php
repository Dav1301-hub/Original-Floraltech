<?php
// ajax_tipo_usuario.php
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

require_once '../../models/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idusu = isset($_POST['idusu']) ? intval($_POST['idusu']) : 0;
    $tipo = isset($_POST['tipo']) ? intval($_POST['tipo']) : 0;
    $response = ['success' => false];

    if ($idusu > 0 && $tipo > 0) {
        $conn = new conexion();
        $db = $conn->get_conexion();
        $stmt = $db->prepare('UPDATE usu SET tpusu_idtpusu = ? WHERE idusu = ?');
        if ($stmt->execute([$tipo, $idusu])) {
            $response['success'] = true;
        } else {
            $response['error'] = 'Error al actualizar en la base de datos';
        }
    } else {
        $response['error'] = 'Parámetros inválidos';
    }
    echo json_encode($response);
    exit;
}

echo json_encode(['success' => false, 'error' => 'Método no permitido']);
exit;
