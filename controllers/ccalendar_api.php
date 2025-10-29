<?php
require_once __DIR__ . '/ccalendar.php';
header('Content-Type: application/json');
if (isset($_GET['start']) && isset($_GET['end'])) {
    // Endpoint para eventos de calendario (rango de fechas)
    $start = $_GET['start'];
    $end = $_GET['end'];
    $controller = new ccalendar();
    $eventos = $controller->getPedidosEnRango($start, $end);
    echo json_encode($eventos);
    exit;
}

if (!isset($_GET['fecha'])) {
    echo json_encode(['error' => 'Fecha no especificada']);
    exit;
}
$fecha = $_GET['fecha'];
$controller = new ccalendar();
$data = $controller->getPedidosYResumen($fecha);
// DEBUG temporal
if (isset($_GET['debug'])) {
    header('Content-Type: text/plain');
    echo "Fecha recibida: $fecha\n";
    echo "Pedidos encontrados: ".count($data['pedidos'])."\n";
    print_r($data['pedidos']);
    exit;
}
echo json_encode($data);
