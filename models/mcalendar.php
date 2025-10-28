<?php
class mcalendar {
    private $db;
    public function __construct($db) {
        $this->db = $db;
    }
    // Obtener pedidos por fecha de entrega
    public function getPedidosPorFecha($fecha) {
        try {
            // Mostrar pedidos programados para la fecha de entrega o creados ese día
            $sql = "SELECT p.idped AS id, p.monto_total AS monto, p.estado, p.fecha_entrega_solicitada, p.fecha_pedido,
                           COALESCE(c.nombre, CONCAT('Cliente ID: ', p.cli_idcli)) AS cliente 
                    FROM ped p 
                    LEFT JOIN cli c ON p.cli_idcli = c.idcli 
                    WHERE (DATE(p.fecha_entrega_solicitada) = :fecha OR DATE(p.fecha_pedido) = :fecha)
                    ORDER BY COALESCE(p.fecha_entrega_solicitada, p.fecha_pedido) DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['fecha' => $fecha]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(Exception $e) {
            // Si falla el JOIN, usar consulta simple
            $sql = "SELECT idped AS id, monto_total AS monto, estado, fecha_entrega_solicitada, fecha_pedido,
                           CONCAT('Cliente ID: ', cli_idcli) AS cliente 
                    FROM ped 
                    WHERE (DATE(fecha_entrega_solicitada) = :fecha OR DATE(fecha_pedido) = :fecha)
                    ORDER BY COALESCE(fecha_entrega_solicitada, fecha_pedido) DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['fecha' => $fecha]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }
    // Obtener resumen por fecha de entrega
    public function getResumenPorFecha($fecha) {
        try {
            $sqlTotal = "SELECT COUNT(*) FROM ped WHERE (DATE(fecha_entrega_solicitada) = :fecha OR DATE(fecha_pedido) = :fecha)";
            $sqlCompletados = "SELECT COUNT(*) FROM ped WHERE estado = 'Completado' AND (DATE(fecha_entrega_solicitada) = :fecha OR DATE(fecha_pedido) = :fecha)";
            $sqlPendientes = "SELECT COUNT(*) FROM ped WHERE estado = 'Pendiente' AND (DATE(fecha_entrega_solicitada) = :fecha OR DATE(fecha_pedido) = :fecha)";
            $sqlEnProceso = "SELECT COUNT(*) FROM ped WHERE estado LIKE '%proceso%' AND (DATE(fecha_entrega_solicitada) = :fecha OR DATE(fecha_pedido) = :fecha)";
            $sqlCancelados = "SELECT COUNT(*) FROM ped WHERE estado IN ('Cancelado','Rechazado') AND (DATE(fecha_entrega_solicitada) = :fecha OR DATE(fecha_pedido) = :fecha)";
            
            $stmtTotal = $this->db->prepare($sqlTotal);
            $stmtCompletados = $this->db->prepare($sqlCompletados);
            $stmtPendientes = $this->db->prepare($sqlPendientes);
            $stmtEnProceso = $this->db->prepare($sqlEnProceso);
            $stmtCancelados = $this->db->prepare($sqlCancelados);
            
            $stmtTotal->execute(['fecha' => $fecha]);
            $stmtCompletados->execute(['fecha' => $fecha]);
            $stmtPendientes->execute(['fecha' => $fecha]);
            $stmtEnProceso->execute(['fecha' => $fecha]);
            $stmtCancelados->execute(['fecha' => $fecha]);
            
            return [
                'total' => $stmtTotal->fetchColumn(),
                'completados' => $stmtCompletados->fetchColumn(),
                'pendientes' => $stmtPendientes->fetchColumn(),
                'enProceso' => $stmtEnProceso->fetchColumn(),
                'rechazados' => $stmtCancelados->fetchColumn() // Mantenemos 'rechazados' para compatibilidad con JS
            ];
        } catch(Exception $e) {
            return [
                'total' => 0,
                'completados' => 0,
                'pendientes' => 0,
                'enProceso' => 0,
                'rechazados' => 0
            ];
        }
    }
}
