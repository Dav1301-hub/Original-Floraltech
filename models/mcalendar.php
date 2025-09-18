<?php
class mcalendar {
    private $db;
    public function __construct($db) {
        $this->db = $db;
    }
    // Obtener pedidos por fecha
    public function getPedidosPorFecha($fecha) {
    $sql = "SELECT p.idped AS id, p.monto_total AS monto, p.estado, p.fecha_pedido, c.nombre AS cliente FROM ped p JOIN cli c ON p.cli_idcli = c.idcli WHERE DATE(p.fecha_pedido) = :fecha ORDER BY p.fecha_pedido DESC";
    $stmt = $this->db->prepare($sql);
    $stmt->execute(['fecha' => $fecha]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // Obtener resumen por fecha
    public function getResumenPorFecha($fecha) {
        $sqlTotal = "SELECT COUNT(*) FROM ped WHERE DATE(fecha_pedido) = :fecha";
        $sqlCompletados = "SELECT COUNT(*) FROM ped WHERE estado = 'Completado' AND DATE(fecha_pedido) = :fecha";
        $sqlPendientes = "SELECT COUNT(*) FROM ped WHERE estado LIKE '%Pendiente%' AND DATE(fecha_pedido) = :fecha";
        $sqlRechazados = "SELECT COUNT(*) FROM ped WHERE estado IN ('Cancelado','Rechazado') AND DATE(fecha_pedido) = :fecha";
        $stmtTotal = $this->db->prepare($sqlTotal);
        $stmtCompletados = $this->db->prepare($sqlCompletados);
        $stmtPendientes = $this->db->prepare($sqlPendientes);
        $stmtRechazados = $this->db->prepare($sqlRechazados);
        $stmtTotal->execute(['fecha' => $fecha]);
        $stmtCompletados->execute(['fecha' => $fecha]);
        $stmtPendientes->execute(['fecha' => $fecha]);
        $stmtRechazados->execute(['fecha' => $fecha]);
        return [
            'total' => $stmtTotal->fetchColumn(),
            'completados' => $stmtCompletados->fetchColumn(),
            'pendientes' => $stmtPendientes->fetchColumn(),
            'rechazados' => $stmtRechazados->fetchColumn()
        ];
    }
}
