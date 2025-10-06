<?php
// Modelo para dashboard general
class MDashboardGeneral {
    private $db;
    public function __construct($db) {
        $this->db = $db;
    }
    public function getKPIs() {
        return [
            'totalPagos' => $this->db->query("SELECT COUNT(*) FROM pagos")->fetchColumn(),
            'pagosPendientes' => $this->db->query("SELECT COUNT(*) FROM pagos WHERE estado_pag = 'Pendiente'")->fetchColumn(),
            'pagosRechazados' => $this->db->query("SELECT COUNT(*) FROM pagos WHERE estado_pag IN ('Rechazado','Reembolsado','Cancelado')")->fetchColumn(),
            'usuariosRegistrados' => $this->db->query("SELECT COUNT(*) FROM usu")->fetchColumn(),
            'nuevosUsuarios' => $this->db->query("SELECT COUNT(*) FROM usu WHERE fecha_registro >= DATE_SUB(NOW(), INTERVAL 7 DAY)")->fetchColumn()
        ];
    }
    public function getTendencias() {
        $pagosSemanaActual = $this->db->query("SELECT COUNT(*) FROM pagos WHERE YEARWEEK(fecha_pago,1) = YEARWEEK(NOW(),1)")->fetchColumn();
        $pagosSemanaAnterior = $this->db->query("SELECT COUNT(*) FROM pagos WHERE YEARWEEK(fecha_pago,1) = YEARWEEK(NOW(),1)-1")->fetchColumn();
        $tendenciaPagos = $pagosSemanaAnterior > 0 ? round((($pagosSemanaActual-$pagosSemanaAnterior)/$pagosSemanaAnterior)*100) : 0;
        $pendientesSemanaActual = $this->db->query("SELECT COUNT(*) FROM pagos WHERE estado_pag = 'Pendiente' AND YEARWEEK(fecha_pago,1) = YEARWEEK(NOW(),1)")->fetchColumn();
        $pendientesSemanaAnterior = $this->db->query("SELECT COUNT(*) FROM pagos WHERE estado_pag = 'Pendiente' AND YEARWEEK(fecha_pago,1) = YEARWEEK(NOW(),1)-1")->fetchColumn();
        $tendenciaPendientes = $pendientesSemanaAnterior > 0 ? round((($pendientesSemanaActual-$pendientesSemanaAnterior)/$pendientesSemanaAnterior)*100) : 0;
        $rechazadosSemanaActual = $this->db->query("SELECT COUNT(*) FROM pagos WHERE estado_pag IN ('Rechazado','Reembolsado','Cancelado') AND YEARWEEK(fecha_pago,1) = YEARWEEK(NOW(),1)")->fetchColumn();
        $rechazadosSemanaAnterior = $this->db->query("SELECT COUNT(*) FROM pagos WHERE estado_pag IN ('Rechazado','Reembolsado','Cancelado') AND YEARWEEK(fecha_pago,1) = YEARWEEK(NOW(),1)-1")->fetchColumn();
        $tendenciaRechazados = $rechazadosSemanaAnterior > 0 ? round((($rechazadosSemanaActual-$rechazadosSemanaAnterior)/$rechazadosSemanaAnterior)*100) : 0;
        return [
            'tendenciaPagos' => $tendenciaPagos,
            'tendenciaPendientes' => $tendenciaPendientes,
            'tendenciaRechazados' => $tendenciaRechazados
        ];
    }
    public function getActividadReciente() {
        $pagosRecientes = $this->db->query("SELECT fecha_pago AS fecha, CONCAT('Pago ', estado_pag, ' por ', metodo_pago, ' ($', monto, ')') AS descripcion FROM pagos ORDER BY fecha_pago DESC LIMIT 3")->fetchAll(PDO::FETCH_ASSOC);
        $usuariosRecientes = $this->db->query("SELECT fecha_registro AS fecha, CONCAT('Nuevo usuario registrado: ', nombre_completo) AS descripcion FROM usu ORDER BY fecha_registro DESC LIMIT 2")->fetchAll(PDO::FETCH_ASSOC);
        $actividadReciente = array_merge($pagosRecientes, $usuariosRecientes);
        usort($actividadReciente, function($a, $b) { return strtotime($b['fecha']) - strtotime($a['fecha']); });
        return $actividadReciente;
    }
    public function getResumenPedidosMes() {
        $mesActual = date('m');
        return [
            'pedidosMes' => $this->db->query("SELECT COUNT(*) FROM ped WHERE MONTH(fecha_pedido) = $mesActual AND YEAR(fecha_pedido) = YEAR(NOW())")->fetchColumn(),
            'pedidosCompletados' => $this->db->query("SELECT COUNT(*) FROM ped WHERE estado = 'Completado' AND MONTH(fecha_pedido) = $mesActual AND YEAR(fecha_pedido) = YEAR(NOW())")->fetchColumn(),
            'pedidosPendientes' => $this->db->query("SELECT COUNT(*) FROM ped WHERE estado LIKE '%Pendiente%' AND MONTH(fecha_pedido) = $mesActual AND YEAR(fecha_pedido) = YEAR(NOW())")->fetchColumn(),
            'pedidosRechazados' => $this->db->query("SELECT COUNT(*) FROM ped WHERE estado IN ('Cancelado','Rechazado') AND MONTH(fecha_pedido) = $mesActual AND YEAR(fecha_pedido) = YEAR(NOW())")->fetchColumn()
        ];
    }
}
