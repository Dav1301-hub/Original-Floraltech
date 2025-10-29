<?php
// Modelo para dashboard general
class MDashboardGeneral {
    private $db;
    public function __construct($db) {
        $this->db = $db;
    }
    public function getKPIs() {
        try {
            // Verificar qué columnas existen y usar las correctas
            $kpis = [];
            
            // Total pagos
            try {
                $kpis['totalPagos'] = $this->db->query("SELECT COUNT(*) FROM pagos")->fetchColumn();
            } catch(Exception $e) {
                $kpis['totalPagos'] = 0;
            }
            
            // Pagos pendientes - intentar diferentes nombres de columna
            try {
                $stmt = $this->db->query("SHOW COLUMNS FROM pagos LIKE '%estado%'");
                $estadoColumn = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if($estadoColumn) {
                    $columnName = $estadoColumn['Field'];
                    $kpis['pagosPendientes'] = $this->db->query("SELECT COUNT(*) FROM pagos WHERE $columnName = 'Pendiente'")->fetchColumn();
                    $kpis['pagosRechazados'] = $this->db->query("SELECT COUNT(*) FROM pagos WHERE $columnName IN ('Rechazado','Reembolsado','Cancelado')")->fetchColumn();
                } else {
                    $kpis['pagosPendientes'] = 0;
                    $kpis['pagosRechazados'] = 0;
                }
            } catch(Exception $e) {
                $kpis['pagosPendientes'] = 0;
                $kpis['pagosRechazados'] = 0;
            }
            
            // Usuarios registrados
            try {
                $kpis['usuariosRegistrados'] = $this->db->query("SELECT COUNT(*) FROM usu")->fetchColumn();
            } catch(Exception $e) {
                $kpis['usuariosRegistrados'] = 0;
            }
            
            // Nuevos usuarios - intentar diferentes nombres de columna fecha
            try {
                $stmt = $this->db->query("SHOW COLUMNS FROM usu LIKE '%fecha%'");
                $fechaColumn = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if($fechaColumn) {
                    $columnName = $fechaColumn['Field'];
                    $kpis['nuevosUsuarios'] = $this->db->query("SELECT COUNT(*) FROM usu WHERE $columnName >= DATE_SUB(NOW(), INTERVAL 7 DAY)")->fetchColumn();
                } else {
                    $kpis['nuevosUsuarios'] = 0;
                }
            } catch(Exception $e) {
                $kpis['nuevosUsuarios'] = 0;
            }
            
            return $kpis;
            
        } catch(Exception $e) {
            // Si todo falla, devolver datos por defecto
            return [
                'totalPagos' => 0,
                'pagosPendientes' => 0,
                'pagosRechazados' => 0,
                'usuariosRegistrados' => 0,
                'nuevosUsuarios' => 0
            ];
        }
    }
    public function getTendencias() {
        try {
            // Intentar obtener columna de fecha en tabla pagos
            $stmt = $this->db->query("SHOW COLUMNS FROM pagos LIKE '%fecha%'");
            $fechaColumn = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if($fechaColumn) {
                $columnName = $fechaColumn['Field'];
                
                $pagosSemanaActual = $this->db->query("SELECT COUNT(*) FROM pagos WHERE YEARWEEK($columnName,1) = YEARWEEK(NOW(),1)")->fetchColumn();
                $pagosSemanaAnterior = $this->db->query("SELECT COUNT(*) FROM pagos WHERE YEARWEEK($columnName,1) = YEARWEEK(NOW(),1)-1")->fetchColumn();
                $tendenciaPagos = $pagosSemanaAnterior > 0 ? round((($pagosSemanaActual-$pagosSemanaAnterior)/$pagosSemanaAnterior)*100) : 0;
                
                // Verificar si existe columna estado
                $stmtEstado = $this->db->query("SHOW COLUMNS FROM pagos LIKE '%estado%'");
                $estadoColumn = $stmtEstado->fetch(PDO::FETCH_ASSOC);
                
                if($estadoColumn) {
                    $estadoColumnName = $estadoColumn['Field'];
                    $pendientesSemanaActual = $this->db->query("SELECT COUNT(*) FROM pagos WHERE $estadoColumnName = 'Pendiente' AND YEARWEEK($columnName,1) = YEARWEEK(NOW(),1)")->fetchColumn();
                    $pendientesSemanaAnterior = $this->db->query("SELECT COUNT(*) FROM pagos WHERE $estadoColumnName = 'Pendiente' AND YEARWEEK($columnName,1) = YEARWEEK(NOW(),1)-1")->fetchColumn();
                    $tendenciaPendientes = $pendientesSemanaAnterior > 0 ? round((($pendientesSemanaActual-$pendientesSemanaAnterior)/$pendientesSemanaAnterior)*100) : 0;
                    
                    $rechazadosSemanaActual = $this->db->query("SELECT COUNT(*) FROM pagos WHERE $estadoColumnName IN ('Rechazado','Reembolsado','Cancelado') AND YEARWEEK($columnName,1) = YEARWEEK(NOW(),1)")->fetchColumn();
                    $rechazadosSemanaAnterior = $this->db->query("SELECT COUNT(*) FROM pagos WHERE $estadoColumnName IN ('Rechazado','Reembolsado','Cancelado') AND YEARWEEK($columnName,1) = YEARWEEK(NOW(),1)-1")->fetchColumn();
                    $tendenciaRechazados = $rechazadosSemanaAnterior > 0 ? round((($rechazadosSemanaActual-$rechazadosSemanaAnterior)/$rechazadosSemanaAnterior)*100) : 0;
                } else {
                    $tendenciaPendientes = 0;
                    $tendenciaRechazados = 0;
                }
            } else {
                $tendenciaPagos = 0;
                $tendenciaPendientes = 0;
                $tendenciaRechazados = 0;
            }
            
            return [
                'tendenciaPagos' => $tendenciaPagos,
                'tendenciaPendientes' => $tendenciaPendientes,
                'tendenciaRechazados' => $tendenciaRechazados
            ];
            
        } catch(Exception $e) {
            return [
                'tendenciaPagos' => 0,
                'tendenciaPendientes' => 0,
                'tendenciaRechazados' => 0
            ];
        }
    }
    public function getActividadReciente() {
        try {
            // Verificar columnas de tabla pagos
            $stmt = $this->db->query("SHOW COLUMNS FROM pagos");
            $pagoColumns = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            $fechaCol = null;
            $estadoCol = null;
            $metodoCol = null;
            $montoCol = null;
            
            foreach($pagoColumns as $col) {
                if(stripos($col, 'fecha') !== false) $fechaCol = $col;
                if(stripos($col, 'estado') !== false) $estadoCol = $col;
                if(stripos($col, 'metodo') !== false) $metodoCol = $col;
                if(stripos($col, 'monto') !== false) $montoCol = $col;
            }
            
            $pagosRecientes = [];
            if($fechaCol && $estadoCol && $metodoCol && $montoCol) {
                $pagosRecientes = $this->db->query("SELECT $fechaCol AS fecha, CONCAT('Pago ', $estadoCol, ' por ', $metodoCol, ' ($', $montoCol, ')') AS descripcion FROM pagos ORDER BY $fechaCol DESC LIMIT 3")->fetchAll(PDO::FETCH_ASSOC);
            }
            
            // Verificar columnas de tabla usu
            $stmt2 = $this->db->query("SHOW COLUMNS FROM usu");
            $usuColumns = $stmt2->fetchAll(PDO::FETCH_COLUMN);
            
            $fechaUsuCol = null;
            $nombreCol = null;
            
            foreach($usuColumns as $col) {
                if(stripos($col, 'fecha') !== false) $fechaUsuCol = $col;
                if(stripos($col, 'nombre') !== false) $nombreCol = $col;
            }
            
            $usuariosRecientes = [];
            if($fechaUsuCol && $nombreCol) {
                $usuariosRecientes = $this->db->query("SELECT $fechaUsuCol AS fecha, CONCAT('Nuevo usuario registrado: ', $nombreCol) AS descripcion FROM usu ORDER BY $fechaUsuCol DESC LIMIT 2")->fetchAll(PDO::FETCH_ASSOC);
            }
            
            $actividadReciente = array_merge($pagosRecientes, $usuariosRecientes);
            if(count($actividadReciente) > 0) {
                usort($actividadReciente, function($a, $b) { 
                    return strtotime($b['fecha']) - strtotime($a['fecha']); 
                });
            }
            
            return $actividadReciente;
            
        } catch(Exception $e) {
            return [
                ['fecha' => date('Y-m-d H:i:s'), 'descripcion' => 'Sistema iniciado correctamente'],
                ['fecha' => date('Y-m-d H:i:s'), 'descripcion' => 'Dashboard cargado']
            ];
        }
    }
    public function getResumenPedidosMes() {
        try {
            $mesActual = date('m');
            $anoActual = date('Y');
            
            // Usar nombres exactos de columnas según la estructura real de la tabla ped
            $fechaCol = 'fecha_pedido';  // Nombre exacto de la columna
            $estadoCol = 'estado';       // Nombre exacto de la columna
            
            // Verificar que la tabla existe
            $stmt = $this->db->query("SHOW TABLES LIKE 'ped'");
            if($stmt->rowCount() == 0) {
                throw new Exception("Tabla 'ped' no existe");
            }
            
            // Consultas con los nombres correctos de columnas
            $pedidosMes = $this->db->query("SELECT COUNT(*) FROM ped WHERE MONTH($fechaCol) = $mesActual AND YEAR($fechaCol) = $anoActual")->fetchColumn();
            $pedidosCompletados = $this->db->query("SELECT COUNT(*) FROM ped WHERE $estadoCol = 'Completado' AND MONTH($fechaCol) = $mesActual AND YEAR($fechaCol) = $anoActual")->fetchColumn();
            $pedidosPendientes = $this->db->query("SELECT COUNT(*) FROM ped WHERE $estadoCol = 'Pendiente' AND MONTH($fechaCol) = $mesActual AND YEAR($fechaCol) = $anoActual")->fetchColumn();
            $pedidosEnProceso = $this->db->query("SELECT COUNT(*) FROM ped WHERE $estadoCol LIKE '%proceso%' AND MONTH($fechaCol) = $mesActual AND YEAR($fechaCol) = $anoActual")->fetchColumn();
            $pedidosCancelados = $this->db->query("SELECT COUNT(*) FROM ped WHERE $estadoCol IN ('Cancelado','Rechazado') AND MONTH($fechaCol) = $mesActual AND YEAR($fechaCol) = $anoActual")->fetchColumn();
            
            // Si no hay datos del mes actual, mostrar datos del mes anterior o del último mes con datos
            if($pedidosMes == 0) {
                // Buscar el último mes con datos
                $ultimoMes = $this->db->query("SELECT MONTH($fechaCol) as mes, YEAR($fechaCol) as ano FROM ped ORDER BY $fechaCol DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);
                
                if($ultimoMes) {
                    $mesConsulta = $ultimoMes['mes'];
                    $anoConsulta = $ultimoMes['ano'];
                    
                    $pedidosMes = $this->db->query("SELECT COUNT(*) FROM ped WHERE MONTH($fechaCol) = $mesConsulta AND YEAR($fechaCol) = $anoConsulta")->fetchColumn();
                    $pedidosCompletados = $this->db->query("SELECT COUNT(*) FROM ped WHERE $estadoCol = 'Completado' AND MONTH($fechaCol) = $mesConsulta AND YEAR($fechaCol) = $anoConsulta")->fetchColumn();
                    $pedidosPendientes = $this->db->query("SELECT COUNT(*) FROM ped WHERE $estadoCol = 'Pendiente' AND MONTH($fechaCol) = $mesConsulta AND YEAR($fechaCol) = $anoConsulta")->fetchColumn();
                    $pedidosEnProceso = $this->db->query("SELECT COUNT(*) FROM ped WHERE $estadoCol LIKE '%proceso%' AND MONTH($fechaCol) = $mesConsulta AND YEAR($fechaCol) = $anoConsulta")->fetchColumn();
                    $pedidosCancelados = $this->db->query("SELECT COUNT(*) FROM ped WHERE $estadoCol IN ('Cancelado','Rechazado') AND MONTH($fechaCol) = $mesConsulta AND YEAR($fechaCol) = $anoConsulta")->fetchColumn();
                }
            }
            
            return [
                'pedidosMes' => $pedidosMes,
                'pedidosCompletados' => $pedidosCompletados,
                'pedidosPendientes' => $pedidosPendientes,
                'pedidosEnProceso' => $pedidosEnProceso,
                'pedidosCancelados' => $pedidosCancelados,
                'mesReferencia' => isset($mesConsulta) ? "$mesConsulta/$anoConsulta" : "$mesActual/$anoActual"
            ];
            
        } catch(Exception $e) {
            // Log del error para debugging
            error_log("Error en getResumenPedidosMes: " . $e->getMessage());
            
            return [
                'pedidosMes' => 0,
                'pedidosCompletados' => 0,
                'pedidosPendientes' => 0,
                'pedidosEnProceso' => 0,
                'pedidosCancelados' => 0,
                'mesReferencia' => date('m/Y'),
                'error' => $e->getMessage()
            ];
        }
    }
}
