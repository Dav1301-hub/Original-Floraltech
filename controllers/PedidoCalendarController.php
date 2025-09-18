<?php
// controllers/PedidoCalendarController.php
header('Content-Type: application/json');
require_once '../../models/conexion.php';

$conn = new conexion();
$db = $conn->get_conexion();

// Consulta pedidos (ajusta los nombres de tabla/campos según tu BD)
$stmt = $db->prepare("SELECT p.idpedido, p.numped, p.fecha_pedido, c.nombre AS cliente, p.estado, p.monto
                      FROM pedido p
                      JOIN cli c ON p.idcli = c.idcli");
$stmt->execute();
$pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$eventos = [];
foreach ($pedidos as $pedido) {
    $eventos[] = [
        'title' => 'Pedido #' . $pedido['numped'] . ' - ' . $pedido['cliente'],
        'start' => date('Y-m-d', strtotime($pedido['fecha_pedido'])),
        'extendedProps' => [
            'detalles' => "Pedido #{$pedido['numped']}\nCliente: {$pedido['cliente']}\nMonto: $ {$pedido['monto']}\nEstado: {$pedido['estado']}"
        ]
    ];
}
echo json_encode($eventos);
