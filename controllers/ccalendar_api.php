<?php
require_once __DIR__ . '/ccalendar.php';
header('Content-Type: application/json');
if (!isset($_GET['fecha'])) {
    echo json_encode(['error' => 'Fecha no especificada']);
    exit;
}
$fecha = $_GET['fecha'];
$controller = new ccalendar();
$data = $controller->getPedidosYResumen($fecha);
echo json_encode($data);
