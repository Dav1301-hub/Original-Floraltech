<?php
require_once __DIR__ . '/../models/conexion.php';
require_once __DIR__ . '/../models/mcalendar.php';

class ccalendar {
    // Obtener pedidos como eventos para un rango de fechas
    public function getPedidosEnRango($start, $end) {
        // Usar fecha_entrega_solicitada si existe, si no fecha_pedido
        $sql = "SELECT p.idped, p.numped, p.estado, p.monto_total, p.fecha_entrega_solicitada, p.fecha_pedido, c.nombre as cliente
                FROM ped p
                LEFT JOIN cli c ON p.cli_idcli = c.idcli
                WHERE (
                    (p.fecha_entrega_solicitada IS NOT NULL AND p.fecha_entrega_solicitada BETWEEN :start AND :end)
                    OR (p.fecha_entrega_solicitada IS NULL AND p.fecha_pedido BETWEEN :start AND :end)
                )";
        $stmt = $this->modelo->db->prepare($sql);
        $stmt->execute(['start' => $start, 'end' => $end]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        // Formatear para FullCalendar
        $eventos = [];
        foreach ($result as $row) {
            $fecha = $row['fecha_entrega_solicitada'] ?: $row['fecha_pedido'];
            // Determinar si la fecha tiene hora (YYYY-MM-DD HH:MM:SS)
            $allDay = true;
            $start = $fecha;
            if ($fecha && preg_match('/\d{4}-\d{2}-\d{2} \d{2}:\d{2}/', $fecha)) {
                $allDay = false;
                // Formato ISO para FullCalendar
                $start = str_replace(' ', 'T', $fecha);
            }
            $eventos[] = [
                'id' => $row['idped'],
                'title' => $row['numped'] . ' - ' . $row['cliente'] . ' ($' . number_format($row['monto_total'], 2) . ')',
                'start' => $start,
                'allDay' => $allDay,
                'extendedProps' => [
                    'estado' => $row['estado'],
                    'monto' => $row['monto_total'],
                    'cliente' => $row['cliente'],
                    'numped' => $row['numped']
                ]
            ];
        }
        return $eventos;
    }
    private $modelo;
    public function __construct() {
    $conn = new conexion();
    $db = $conn->get_conexion();
    $this->modelo = new mcalendar($db);
    }
    // Obtener pedidos y resumen por fecha
    public function getPedidosYResumen($fecha) {
        $pedidos = $this->modelo->getPedidosPorFecha($fecha);
        $resumen = $this->modelo->getResumenPorFecha($fecha);
        return [
            'pedidos' => $pedidos,
            'resumen' => $resumen
        ];
    }
}
