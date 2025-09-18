<?php
require_once '../../config/db.php';

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
        }
    }
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
?>
