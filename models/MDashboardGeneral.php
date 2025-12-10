<?php
// Modelo para dashboard general
class MDashboardGeneral {
    private $db;
    public function __construct($db) {
        $this->db = $db;
    }
    
    public function getKPIs() {
        try {
            $kpis = [];
            
            // Total pagos
            $kpis['totalPagos'] = $this->db->query("SELECT COUNT(*) FROM pagos")->fetchColumn();
            
            // Pagos pendientes y rechazados
            $kpis['pagosPendientes'] = $this->db->query("SELECT COUNT(*) FROM pagos WHERE estado_pag = 'Pendiente'")->fetchColumn();
            $kpis['pagosRechazados'] = $this->db->query("SELECT COUNT(*) FROM pagos WHERE estado_pag IN ('Rechazado','Reembolsado','Cancelado')")->fetchColumn();
            
            // Usuarios registrados
            $kpis['usuariosRegistrados'] = $this->db->query("SELECT COUNT(*) FROM usu WHERE activo = 1")->fetchColumn();
            
            // Nuevos usuarios (últimos 7 días)
            $kpis['nuevosUsuarios'] = $this->db->query("SELECT COUNT(*) FROM usu WHERE fecha_registro >= DATE_SUB(NOW(), INTERVAL 7 DAY)")->fetchColumn();
            
            // Ingresos del mes actual (suma de monto de pagos completados)
            $mesActual = date('m');
            $anoActual = date('Y');
            $ingresosMes = $this->db->query("
                SELECT COALESCE(SUM(monto), 0) 
                FROM pagos 
                WHERE estado_pag = 'Completado' 
                AND MONTH(fecha_pago) = $mesActual 
                AND YEAR(fecha_pago) = $anoActual
            ")->fetchColumn();
            $kpis['ingresosMes'] = round($ingresosMes, 2);
            
            // Tasa de conversión del mes actual
            $totalPedidosMes = $this->db->query("SELECT COUNT(*) FROM ped WHERE MONTH(fecha_pedido) = $mesActual AND YEAR(fecha_pedido) = $anoActual")->fetchColumn();
            $pedidosCompletadosMes = $this->db->query("SELECT COUNT(*) FROM ped WHERE estado = 'Completado' AND MONTH(fecha_pedido) = $mesActual AND YEAR(fecha_pedido) = $anoActual")->fetchColumn();
            $kpis['tasaConversion'] = $totalPedidosMes > 0 ? round(($pedidosCompletadosMes / $totalPedidosMes) * 100, 1) : 0;
            
            return $kpis;
            
        } catch(Exception $e) {
            error_log("Error en getKPIs: " . $e->getMessage());
            return [
                'totalPagos' => 0,
                'pagosPendientes' => 0,
                'pagosRechazados' => 0,
                'usuariosRegistrados' => 0,
                'nuevosUsuarios' => 0,
                'ingresosMes' => 0,
                'tasaConversion' => 0
            ];
        }
    }
    
    public function getTendencias() {
        try {
            // Tendencia de pagos (comparar semana actual vs anterior)
            $pagosSemanaActual = $this->db->query("SELECT COUNT(*) FROM pagos WHERE YEARWEEK(fecha_pago,1) = YEARWEEK(NOW(),1)")->fetchColumn();
            $pagosSemanaAnterior = $this->db->query("SELECT COUNT(*) FROM pagos WHERE YEARWEEK(fecha_pago,1) = YEARWEEK(NOW(),1)-1")->fetchColumn();
            $tendenciaPagos = $pagosSemanaAnterior > 0 ? round((($pagosSemanaActual-$pagosSemanaAnterior)/$pagosSemanaAnterior)*100) : 0;
            
            // Tendencia de pagos pendientes
            $pendientesSemanaActual = $this->db->query("SELECT COUNT(*) FROM pagos WHERE estado_pag = 'Pendiente' AND YEARWEEK(fecha_pago,1) = YEARWEEK(NOW(),1)")->fetchColumn();
            $pendientesSemanaAnterior = $this->db->query("SELECT COUNT(*) FROM pagos WHERE estado_pag = 'Pendiente' AND YEARWEEK(fecha_pago,1) = YEARWEEK(NOW(),1)-1")->fetchColumn();
            $tendenciaPendientes = $pendientesSemanaAnterior > 0 ? round((($pendientesSemanaActual-$pendientesSemanaAnterior)/$pendientesSemanaAnterior)*100) : 0;
            
            // Tendencia de pagos rechazados
            $rechazadosSemanaActual = $this->db->query("SELECT COUNT(*) FROM pagos WHERE estado_pag IN ('Rechazado','Reembolsado','Cancelado') AND YEARWEEK(fecha_pago,1) = YEARWEEK(NOW(),1)")->fetchColumn();
            $rechazadosSemanaAnterior = $this->db->query("SELECT COUNT(*) FROM pagos WHERE estado_pag IN ('Rechazado','Reembolsado','Cancelado') AND YEARWEEK(fecha_pago,1) = YEARWEEK(NOW(),1)-1")->fetchColumn();
            $tendenciaRechazados = $rechazadosSemanaAnterior > 0 ? round((($rechazadosSemanaActual-$rechazadosSemanaAnterior)/$rechazadosSemanaAnterior)*100) : 0;
            
            // Tendencia de ingresos (comparar semana actual vs anterior)
            $ingresosSemanaActual = $this->db->query("SELECT COALESCE(SUM(monto), 0) FROM pagos WHERE estado_pag = 'Completado' AND YEARWEEK(fecha_pago,1) = YEARWEEK(NOW(),1)")->fetchColumn();
            $ingresosSemanaAnterior = $this->db->query("SELECT COALESCE(SUM(monto), 0) FROM pagos WHERE estado_pag = 'Completado' AND YEARWEEK(fecha_pago,1) = YEARWEEK(NOW(),1)-1")->fetchColumn();
            $tendenciaIngresos = $ingresosSemanaAnterior > 0 ? round((($ingresosSemanaActual-$ingresosSemanaAnterior)/$ingresosSemanaAnterior)*100) : 0;
            
            // Tendencia de tasa de conversión (comparar semana actual vs anterior)
            $totalPedidosSemanaActual = $this->db->query("SELECT COUNT(*) FROM ped WHERE YEARWEEK(fecha_pedido,1) = YEARWEEK(NOW(),1)")->fetchColumn();
            $completadosSemanaActual = $this->db->query("SELECT COUNT(*) FROM ped WHERE estado = 'Completado' AND YEARWEEK(fecha_pedido,1) = YEARWEEK(NOW(),1)")->fetchColumn();
            $conversionSemanaActual = $totalPedidosSemanaActual > 0 ? ($completadosSemanaActual / $totalPedidosSemanaActual) * 100 : 0;
            
            $totalPedidosSemanaAnterior = $this->db->query("SELECT COUNT(*) FROM ped WHERE YEARWEEK(fecha_pedido,1) = YEARWEEK(NOW(),1)-1")->fetchColumn();
            $completadosSemanaAnterior = $this->db->query("SELECT COUNT(*) FROM ped WHERE estado = 'Completado' AND YEARWEEK(fecha_pedido,1) = YEARWEEK(NOW(),1)-1")->fetchColumn();
            $conversionSemanaAnterior = $totalPedidosSemanaAnterior > 0 ? ($completadosSemanaAnterior / $totalPedidosSemanaAnterior) * 100 : 0;
            
            $tendenciaConversion = $conversionSemanaAnterior > 0 ? round((($conversionSemanaActual-$conversionSemanaAnterior)/$conversionSemanaAnterior)*100) : 0;
            
            return [
                'tendenciaPagos' => $tendenciaPagos,
                'tendenciaPendientes' => $tendenciaPendientes,
                'tendenciaRechazados' => $tendenciaRechazados,
                'tendenciaIngresos' => $tendenciaIngresos,
                'tendenciaConversion' => $tendenciaConversion
            ];
            
        } catch(Exception $e) {
            error_log("Error en getTendencias: " . $e->getMessage());
            return [
                'tendenciaPagos' => 0,
                'tendenciaPendientes' => 0,
                'tendenciaRechazados' => 0,
                'tendenciaIngresos' => 0,
                'tendenciaConversion' => 0
            ];
        }
    }
    
    public function getActividadReciente() {
        try {
            $actividad = [];
            
            // Obtener últimos pedidos recientes
            $pedidos = $this->db->query("
                SELECT 
                    p.fecha_pedido AS fecha, 
                    CONCAT('Pedido ', p.numped, ' - ', c.nombre, ' ($', FORMAT(p.monto_total, 2), ')') AS descripcion
                FROM ped p
                LEFT JOIN cli c ON p.cli_idcli = c.idcli
                ORDER BY p.fecha_pedido DESC 
                LIMIT 10
            ")->fetchAll(PDO::FETCH_ASSOC);
            
            $actividad = array_merge($actividad, $pedidos);
            
            // Ordenar por fecha descendente
            usort($actividad, function($a, $b) { 
                return strtotime($b['fecha']) - strtotime($a['fecha']); 
            });
            
            return array_slice($actividad, 0, 10);
            
        } catch(Exception $e) {
            error_log("Error en getActividadReciente: " . $e->getMessage());
            return [];
        }
    }
    
    public function getResumenPedidosMes() {
        try {
            $mesActual = date('m');
            $anoActual = date('Y');
            
            // Contar pedidos del mes actual
            $pedidosMes = $this->db->query("SELECT COUNT(*) FROM ped WHERE MONTH(fecha_pedido) = $mesActual AND YEAR(fecha_pedido) = $anoActual")->fetchColumn();
            $pedidosCompletados = $this->db->query("SELECT COUNT(*) FROM ped WHERE estado = 'Completado' AND MONTH(fecha_pedido) = $mesActual AND YEAR(fecha_pedido) = $anoActual")->fetchColumn();
            $pedidosPendientes = $this->db->query("SELECT COUNT(*) FROM ped WHERE estado = 'Pendiente' AND MONTH(fecha_pedido) = $mesActual AND YEAR(fecha_pedido) = $anoActual")->fetchColumn();
            $pedidosEnProceso = $this->db->query("SELECT COUNT(*) FROM ped WHERE estado LIKE '%proceso%' AND MONTH(fecha_pedido) = $mesActual AND YEAR(fecha_pedido) = $anoActual")->fetchColumn();
            $pedidosCancelados = $this->db->query("SELECT COUNT(*) FROM ped WHERE estado IN ('Cancelado','Rechazado') AND MONTH(fecha_pedido) = $mesActual AND YEAR(fecha_pedido) = $anoActual")->fetchColumn();
            
            // Si no hay datos del mes actual, buscar el último mes con datos
            $mesReferencia = "$mesActual/$anoActual";
            if($pedidosMes == 0) {
                $ultimoMes = $this->db->query("SELECT MONTH(fecha_pedido) as mes, YEAR(fecha_pedido) as ano FROM ped ORDER BY fecha_pedido DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);
                
                if($ultimoMes) {
                    $mesConsulta = $ultimoMes['mes'];
                    $anoConsulta = $ultimoMes['ano'];
                    
                    $pedidosMes = $this->db->query("SELECT COUNT(*) FROM ped WHERE MONTH(fecha_pedido) = $mesConsulta AND YEAR(fecha_pedido) = $anoConsulta")->fetchColumn();
                    $pedidosCompletados = $this->db->query("SELECT COUNT(*) FROM ped WHERE estado = 'Completado' AND MONTH(fecha_pedido) = $mesConsulta AND YEAR(fecha_pedido) = $anoConsulta")->fetchColumn();
                    $pedidosPendientes = $this->db->query("SELECT COUNT(*) FROM ped WHERE estado = 'Pendiente' AND MONTH(fecha_pedido) = $mesConsulta AND YEAR(fecha_pedido) = $anoConsulta")->fetchColumn();
                    $pedidosEnProceso = $this->db->query("SELECT COUNT(*) FROM ped WHERE estado LIKE '%proceso%' AND MONTH(fecha_pedido) = $mesConsulta AND YEAR(fecha_pedido) = $anoConsulta")->fetchColumn();
                    $pedidosCancelados = $this->db->query("SELECT COUNT(*) FROM ped WHERE estado IN ('Cancelado','Rechazado') AND MONTH(fecha_pedido) = $mesConsulta AND YEAR(fecha_pedido) = $anoConsulta")->fetchColumn();
                    
                    $mesReferencia = "$mesConsulta/$anoConsulta";
                }
            }
            
            return [
                'pedidosMes' => $pedidosMes,
                'pedidosCompletados' => $pedidosCompletados,
                'pedidosPendientes' => $pedidosPendientes,
                'pedidosEnProceso' => $pedidosEnProceso,
                'pedidosCancelados' => $pedidosCancelados,
                'mesReferencia' => $mesReferencia
            ];
            
        } catch(Exception $e) {
            error_log("Error en getResumenPedidosMes: " . $e->getMessage());
            return [
                'pedidosMes' => 0,
                'pedidosCompletados' => 0,
                'pedidosPendientes' => 0,
                'pedidosEnProceso' => 0,
                'pedidosCancelados' => 0,
                'mesReferencia' => date('m/Y')
            ];
        }
    }
    
    public function getEntregasProximas() {
        try {
            $hoy = date('Y-m-d');
            $manana = date('Y-m-d', strtotime('+1 day'));
            
            // Entregas para hoy
            $entregasHoy = $this->db->query("
                SELECT 
                    p.numped,
                    p.fecha_entrega_solicitada,
                    p.estado,
                    p.monto_total,
                    c.nombre as cliente
                FROM ped p
                LEFT JOIN cli c ON p.cli_idcli = c.idcli
                WHERE DATE(p.fecha_entrega_solicitada) = '$hoy'
                ORDER BY p.fecha_entrega_solicitada ASC
            ")->fetchAll(PDO::FETCH_ASSOC);
            
            // Entregas para mañana
            $entregasManana = $this->db->query("
                SELECT 
                    p.numped,
                    p.fecha_entrega_solicitada,
                    p.estado,
                    p.monto_total,
                    c.nombre as cliente
                FROM ped p
                LEFT JOIN cli c ON p.cli_idcli = c.idcli
                WHERE DATE(p.fecha_entrega_solicitada) = '$manana'
                ORDER BY p.fecha_entrega_solicitada ASC
            ")->fetchAll(PDO::FETCH_ASSOC);
            
            return [
                'hoy' => $entregasHoy,
                'manana' => $entregasManana,
                'cantidadHoy' => count($entregasHoy),
                'cantidadManana' => count($entregasManana)
            ];
            
        } catch(Exception $e) {
            error_log("Error en getEntregasProximas: " . $e->getMessage());
            return [
                'hoy' => [],
                'manana' => [],
                'cantidadHoy' => 0,
                'cantidadManana' => 0
            ];
        }
    }
    
    public function getTendenciaVentas($dias = 14) {
        try {
            $tendencia = [];
            
            for($i = $dias - 1; $i >= 0; $i--) {
                $fecha = date('Y-m-d', strtotime("-$i days"));
                $fechaLabel = date('d/m', strtotime("-$i days"));
                
                // Contar pedidos del día
                $pedidos = $this->db->query("
                    SELECT COUNT(*) 
                    FROM ped 
                    WHERE DATE(fecha_pedido) = '$fecha'
                ")->fetchColumn();
                
                // Sumar monto total de pedidos del día
                $monto = $this->db->query("
                    SELECT COALESCE(SUM(monto_total), 0) 
                    FROM ped 
                    WHERE DATE(fecha_pedido) = '$fecha'
                ")->fetchColumn();
                
                $tendencia[] = [
                    'fecha' => $fechaLabel,
                    'pedidos' => (int)$pedidos,
                    'monto' => round($monto, 2)
                ];
            }
            
            return $tendencia;
            
        } catch(Exception $e) {
            error_log("Error en getTendenciaVentas: " . $e->getMessage());
            return [];
        }
    }
    
    public function getTopProductos($limite = 5, $dias = 30) {
        try {
            $fechaInicio = date('Y-m-d', strtotime("-$dias days"));
            
            $query = "
                SELECT 
                    t.nombre,
                    t.color,
                    SUM(d.cantidad) as total_vendido,
                    COUNT(DISTINCT d.idped) as num_pedidos,
                    SUM(d.subtotal) as ingresos_total
                FROM detped d
                JOIN tflor t ON d.idtflor = t.idtflor
                JOIN ped p ON d.idped = p.idped
                WHERE p.fecha_pedido >= '$fechaInicio'
                GROUP BY t.idtflor, t.nombre, t.color
                ORDER BY total_vendido DESC
                LIMIT $limite
            ";
            
            $productos = $this->db->query($query)->fetchAll(PDO::FETCH_ASSOC);
            
            // Calcular total para porcentajes
            $totalGeneral = 0;
            foreach($productos as $prod) {
                $totalGeneral += $prod['total_vendido'];
            }
            
            // Agregar porcentajes
            foreach($productos as &$prod) {
                $prod['porcentaje'] = $totalGeneral > 0 ? round(($prod['total_vendido'] / $totalGeneral) * 100, 1) : 0;
            }
            
            return $productos;
            
        } catch(Exception $e) {
            error_log("Error en getTopProductos: " . $e->getMessage());
            return [];
        }
    }
}
