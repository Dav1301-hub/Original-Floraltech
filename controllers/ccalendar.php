<?php
require_once __DIR__ . '/../models/conexion.php';
require_once __DIR__ . '/../models/mcalendar.php';

class ccalendar {
    private $modelo;
    private $db;
    
    public function __construct() {
        $conn = new conexion();
        $this->db = $conn->get_conexion();
        $this->modelo = new mcalendar($this->db);
    }
    
    // Obtener pedidos como eventos para un rango de fechas
    public function getPedidosEnRango($start, $end) {
        // Mostrar TODOS los pedidos (Pendientes, En Proceso, Completados, etc.)
        // Usar fecha_entrega_solicitada si existe, si no fecha_pedido
        $sql = "SELECT p.idped, p.numped, p.estado, p.monto_total, p.fecha_entrega_solicitada, p.fecha_pedido, c.nombre as cliente
                FROM ped p
                LEFT JOIN cli c ON p.cli_idcli = c.idcli
                WHERE (
                    (p.fecha_entrega_solicitada IS NOT NULL AND DATE(p.fecha_entrega_solicitada) BETWEEN :start AND :end)
                    OR (p.fecha_entrega_solicitada IS NULL AND DATE(p.fecha_pedido) BETWEEN :start AND :end)
                )
                ORDER BY COALESCE(p.fecha_entrega_solicitada, p.fecha_pedido) ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['start' => $start, 'end' => $end]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Log para debugging
        error_log("Calendario - Rango: $start a $end");
        error_log("Pedidos encontrados: " . count($result));
        foreach($result as $row) {
            error_log("Pedido: {$row['numped']} - Fecha entrega: {$row['fecha_entrega_solicitada']} - Fecha pedido: {$row['fecha_pedido']}");
        }
        
        // Agrupar pedidos por fecha (solo día, sin hora)
        $pedidosPorFecha = [];
        foreach ($result as $row) {
            $fecha = $row['fecha_entrega_solicitada'] ?: $row['fecha_pedido'];
            // Extraer solo la fecha (YYYY-MM-DD)
            if (strpos($fecha, ' ') !== false) {
                $fechaSolo = substr($fecha, 0, 10);
            } else {
                $fechaSolo = $fecha;
            }
            
            if (!isset($pedidosPorFecha[$fechaSolo])) {
                $pedidosPorFecha[$fechaSolo] = [];
            }
            $pedidosPorFecha[$fechaSolo][] = $row;
        }
        
        // Crear UN evento por día con el conteo de pedidos
        $eventos = [];
        foreach ($pedidosPorFecha as $fecha => $pedidos) {
            $cantidadPedidos = count($pedidos);
            $eventos[] = [
                'id' => 'dia_' . $fecha,
                'title' => $cantidadPedidos . ($cantidadPedidos == 1 ? ' Pedido' : ' Pedidos'),
                'start' => $fecha,
                'allDay' => true,
                'className' => 'evento-pedido-con-flores',
                'extendedProps' => [
                    'cantidadPedidos' => $cantidadPedidos
                ]
            ];
        }
        
        return $eventos;
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
