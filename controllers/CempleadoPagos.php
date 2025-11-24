<?php
class CempleadoPagos {
    private $db;
    private $empleado_id;

    public function __construct() {
        // Verificar que el usuario esté logueado y sea empleado (tipos 2, 3, 4)
        if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['tpusu_idtpusu'], [2, 3, 4])) {
            header('Location: index.php?ctrl=login&action=index');
            exit();
        }
        
        // Conectar a la base de datos
        require_once 'models/conexion.php';
        $conn = new conexion();
        $this->db = $conn->get_conexion();
        
        $this->empleado_id = $_SESSION['user']['idusu'];
    }
    
    public function index() {
        $this->reportes();
    }

    public function reportes() {
        try {
            $user = $_SESSION['user'];
            
            // Obtener filtros de la URL
            $filtros = $this->obtenerFiltros();
            
            // Obtener datos con filtros aplicados
            $stats = $this->obtenerEstadisticasPagos($filtros);
            $pagosPendientes = $this->obtenerPagosPendientes($filtros);
            $pagosCompletados = $this->obtenerPagosCompletadosRecientes($filtros);
            $reporteMensual = $this->obtenerReporteMensual($filtros);
            
            // Obtener listas para filtros
            $clientes = $this->obtenerClientes();
            $metodosPago = $this->obtenerMetodosPago();
            
            include 'views/empleado/reportes_pagos.php';
            
        } catch (Exception $e) {
            error_log("Error en reportes: " . $e->getMessage());
            $_SESSION['mensaje'] = "Ocurrió un error al cargar los reportes de pagos";
            $_SESSION['tipo_mensaje'] = "error";
            header("Location: index.php?ctrl=empleado&action=dashboard");
            exit();
        }
    }

    public function gestionPagos() {
        $this->reportes();
    }

    public function generarPDF() {
        try {
            require_once 'libs/FPDF/fpdf.php';
            
            $filtros = $this->obtenerFiltros();
            $stats = $this->obtenerEstadisticasPagos($filtros);
            $reporteCompleto = $this->obtenerReporteCompleto($filtros);
            
            $pdf = new FPDF();
            $pdf->AddPage();
            $pdf->SetFont('Arial', 'B', 16);
            
            // Título
            $pdf->Cell(0, 10, 'Reporte de Pagos - FloralTech', 0, 1, 'C');
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(0, 5, 'Generado el: ' . date('d/m/Y H:i:s'), 0, 1, 'C');
            $pdf->Cell(0, 5, 'Empleado: ' . $_SESSION['user']['nombre_completo'], 0, 1, 'C');
            $pdf->Ln(10);
            
            // Filtros aplicados
            if (!empty($filtros['fecha_inicio']) || !empty($filtros['fecha_fin'])) {
                $pdf->SetFont('Arial', 'B', 12);
                $pdf->Cell(0, 8, 'Filtros Aplicados:', 0, 1);
                $pdf->SetFont('Arial', '', 10);
                
                if (!empty($filtros['fecha_inicio'])) {
                    $pdf->Cell(0, 5, 'Fecha inicio: ' . date('d/m/Y', strtotime($filtros['fecha_inicio'])), 0, 1);
                }
                if (!empty($filtros['fecha_fin'])) {
                    $pdf->Cell(0, 5, 'Fecha fin: ' . date('d/m/Y', strtotime($filtros['fecha_fin'])), 0, 1);
                }
                if (!empty($filtros['estado'])) {
                    $pdf->Cell(0, 5, 'Estado: ' . $filtros['estado'], 0, 1);
                }
                if (!empty($filtros['cliente'])) {
                    $pdf->Cell(0, 5, 'Cliente: ' . $filtros['cliente'], 0, 1);
                }
                $pdf->Ln(5);
            }
            
            // Estadísticas
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(0, 8, 'Resumen General:', 0, 1);
            $pdf->SetFont('Arial', '', 10);
            
            $pdf->Cell(50, 6, 'Total Pendientes:', 0, 0);
            $pdf->Cell(50, 6, number_format($stats['pendientes']), 0, 0);
            $pdf->Cell(50, 6, 'Monto:', 0, 0);
            $pdf->Cell(0, 6, '$' . number_format($stats['monto_pendiente'], 2), 0, 1);
            
            $pdf->Cell(50, 6, 'Total Completados:', 0, 0);
            $pdf->Cell(50, 6, number_format($stats['completados']), 0, 0);
            $pdf->Cell(50, 6, 'Monto:', 0, 0);
            $pdf->Cell(0, 6, '$' . number_format($stats['monto_completado'], 2), 0, 1);
            
            $pdf->Ln(10);
            
            // Tabla de pagos
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(0, 8, 'Detalle de Pagos:', 0, 1);
            
            // Headers de tabla
            $pdf->SetFont('Arial', 'B', 8);
            $pdf->Cell(20, 6, 'Pedido', 1, 0, 'C');
            $pdf->Cell(50, 6, 'Cliente', 1, 0, 'C');
            $pdf->Cell(25, 6, 'Monto', 1, 0, 'C');
            $pdf->Cell(25, 6, 'Fecha', 1, 0, 'C');
            $pdf->Cell(25, 6, 'Metodo', 1, 0, 'C');
            $pdf->Cell(25, 6, 'Estado', 1, 1, 'C');
            
            // Datos de tabla
            $pdf->SetFont('Arial', '', 8);
            foreach ($reporteCompleto as $pago) {
                $pdf->Cell(20, 5, substr($pago['numped'], 0, 8), 1, 0);
                $pdf->Cell(50, 5, substr($pago['cliente_nombre'], 0, 25), 1, 0);
                $pdf->Cell(25, 5, '$' . number_format($pago['monto'], 2), 1, 0, 'R');
                $pdf->Cell(25, 5, date('d/m/Y', strtotime($pago['fecha_pago'])), 1, 0, 'C');
                $pdf->Cell(25, 5, substr($pago['metodo_pago'], 0, 12), 1, 0, 'C');
                $pdf->Cell(25, 5, $pago['estado_pag'], 1, 1, 'C');
            }
            
            // Generar nombre del archivo
            $nombreArchivo = 'reporte_pagos_' . date('Y-m-d_H-i-s') . '.pdf';
            
            $pdf->Output('D', $nombreArchivo);
            exit();
            
        } catch (Exception $e) {
            error_log("Error al generar PDF: " . $e->getMessage());
            $_SESSION['mensaje'] = "Error al generar el PDF";
            $_SESSION['tipo_mensaje'] = "error";
            header("Location: index.php?ctrl=CempleadoPagos&action=reportes");
            exit();
        }
    }

    public function exportarExcel() {
        try {
            $filtros = $this->obtenerFiltros();
            $reporteCompleto = $this->obtenerReporteCompleto($filtros);
            
            // Crear contenido CSV
            $output = fopen('php://output', 'w');
            
            // Headers para descarga
            $nombreArchivo = 'reporte_pagos_' . date('Y-m-d_H-i-s') . '.csv';
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="' . $nombreArchivo . '"');
            header('Pragma: no-cache');
            header('Expires: 0');
            
            // BOM para UTF-8
            fputs($output, "\xEF\xBB\xBF");
            
            // Headers del CSV
            fputcsv($output, [
                'Numero Pedido',
                'Cliente',
                'Monto',
                'Fecha Pago',
                'Metodo Pago',
                'Estado',
                'Referencia',
                'Observaciones'
            ]);
            
            // Datos
            foreach ($reporteCompleto as $pago) {
                fputcsv($output, [
                    $pago['numped'],
                    $pago['cliente_nombre'],
                    number_format($pago['monto'], 2),
                    date('d/m/Y H:i', strtotime($pago['fecha_pago'])),
                    $pago['metodo_pago'],
                    $pago['estado_pag'],
                    $pago['referencia_pago'] ?? '',
                    $pago['observaciones'] ?? ''
                ]);
            }
            
            fclose($output);
            exit();
            
        } catch (Exception $e) {
            error_log("Error al exportar Excel: " . $e->getMessage());
            $_SESSION['mensaje'] = "Error al exportar Excel";
            $_SESSION['tipo_mensaje'] = "error";
            header("Location: index.php?ctrl=CempleadoPagos&action=reportes");
            exit();
        }
    }

    public function actualizarEstadoPago() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $idPago = filter_input(INPUT_POST, 'id_pago', FILTER_VALIDATE_INT);
                $nuevoEstado = filter_input(INPUT_POST, 'nuevo_estado', FILTER_SANITIZE_STRING);

                if (!$idPago || !$nuevoEstado) {
                    throw new Exception("Datos de pago inválidos");
                }

                if ($this->actualizarEstadoPagoDirecto($idPago, $nuevoEstado)) {
                    $_SESSION['mensaje'] = "Estado del pago actualizado correctamente";
                    $_SESSION['tipo_mensaje'] = "success";
                } else {
                    $_SESSION['mensaje'] = "No se pudo actualizar el estado del pago";
                    $_SESSION['tipo_mensaje'] = "error";
                }
                
            } catch (Exception $e) {
                error_log("Error al actualizar estado de pago: " . $e->getMessage());
                $_SESSION['mensaje'] = "Error al procesar la solicitud";
                $_SESSION['tipo_mensaje'] = "error";
            }
            
            header("Location: index.php?ctrl=CempleadoPagos&action=reportes");
            exit();
        }
    }
    
    // Métodos privados para obtener datos
    private function obtenerFiltros() {
        return [
            'fecha_inicio' => $_GET['fecha_inicio'] ?? '',
            'fecha_fin' => $_GET['fecha_fin'] ?? '',
            'estado' => $_GET['estado'] ?? '',
            'cliente' => $_GET['cliente'] ?? '',
            'metodo_pago' => $_GET['metodo_pago'] ?? '',
            'limite' => (int)($_GET['limite'] ?? 20)
        ];
    }
    
    private function construirFiltroSQL($filtros, $alias = 'pg') {
        $condiciones = [];
        $parametros = [];
        
        if (!empty($filtros['fecha_inicio'])) {
            $condiciones[] = "DATE({$alias}.fecha_pago) >= ?";
            $parametros[] = $filtros['fecha_inicio'];
        }
        
        if (!empty($filtros['fecha_fin'])) {
            $condiciones[] = "DATE({$alias}.fecha_pago) <= ?";
            $parametros[] = $filtros['fecha_fin'];
        }
        
        if (!empty($filtros['estado'])) {
            $condiciones[] = "{$alias}.estado_pag = ?";
            $parametros[] = $filtros['estado'];
        }
        
        if (!empty($filtros['cliente'])) {
            $condiciones[] = "c.nombre LIKE ?";
            $parametros[] = "%{$filtros['cliente']}%";
        }
        
        if (!empty($filtros['metodo_pago'])) {
            $condiciones[] = "{$alias}.metodo_pago = ?";
            $parametros[] = $filtros['metodo_pago'];
        }
        
        return [
            'where' => !empty($condiciones) ? 'AND ' . implode(' AND ', $condiciones) : '',
            'parametros' => $parametros
        ];
    }
    
    private function obtenerEstadisticasPagos($filtros = []) {
        try {
            $filtroSQL = $this->construirFiltroSQL($filtros);
            
            $stmt = $this->db->prepare("
                SELECT 
                    COUNT(CASE WHEN pg.estado_pag = 'Pendiente' THEN 1 END) as pendientes,
                    COALESCE(SUM(CASE WHEN pg.estado_pag = 'Pendiente' THEN pg.monto ELSE 0 END), 0) as monto_pendiente,
                    COUNT(CASE WHEN pg.estado_pag = 'Completado' THEN 1 END) as completados,
                    COALESCE(SUM(CASE WHEN pg.estado_pag = 'Completado' THEN pg.monto ELSE 0 END), 0) as monto_completado,
                    COUNT(CASE WHEN pg.estado_pag = 'Completado' AND DATE(pg.fecha_pago) = CURDATE() THEN 1 END) as completados_hoy,
                    COALESCE(SUM(CASE WHEN pg.estado_pag = 'Completado' AND DATE(pg.fecha_pago) = CURDATE() THEN pg.monto ELSE 0 END), 0) as monto_hoy
                FROM pagos pg
                LEFT JOIN ped p ON pg.ped_idped = p.idped
                LEFT JOIN cli c ON p.cli_idcli = c.idcli
                WHERE 1=1 {$filtroSQL['where']}
            ");
            $stmt->execute($filtroSQL['parametros']);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: [
                'pendientes' => 0,
                'monto_pendiente' => 0,
                'completados' => 0,
                'monto_completado' => 0,
                'completados_hoy' => 0,
                'monto_hoy' => 0
            ];
        } catch (Exception $e) {
            error_log("Error en obtenerEstadisticasPagos: " . $e->getMessage());
            return [
                'pendientes' => 0,
                'monto_pendiente' => 0,
                'completados' => 0,
                'monto_completado' => 0,
                'completados_hoy' => 0,
                'monto_hoy' => 0
            ];
        }
    }
    
    private function obtenerPagosPendientes($filtros = []) {
        try {
            $filtroSQL = $this->construirFiltroSQL($filtros);
            
            $stmt = $this->db->prepare("
                SELECT 
                    pg.*,
                    COALESCE(p.numped, CONCAT('PED-', pg.ped_idped)) as numped,
                    COALESCE(c.nombre, 'Cliente no encontrado') as cliente_nombre,
                    COALESCE(pg.transaccion_id, '') as referencia_pago,
                    COALESCE(pg.comprobante_transferencia, '') as observaciones
                FROM pagos pg
                LEFT JOIN ped p ON pg.ped_idped = p.idped
                LEFT JOIN cli c ON p.cli_idcli = c.idcli
                WHERE pg.estado_pag = 'Pendiente' {$filtroSQL['where']}
                ORDER BY pg.fecha_pago DESC
                LIMIT {$filtros['limite']}
            ");
            $stmt->execute($filtroSQL['parametros']);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error en obtenerPagosPendientes: " . $e->getMessage());
            return [];
        }
    }
    
    private function obtenerPagosCompletadosRecientes($filtros = []) {
        try {
            $filtroSQL = $this->construirFiltroSQL($filtros);
            
            $stmt = $this->db->prepare("
                SELECT 
                    pg.*,
                    COALESCE(p.numped, CONCAT('PED-', pg.ped_idped)) as numped,
                    COALESCE(c.nombre, 'Cliente no encontrado') as cliente_nombre,
                    COALESCE(pg.transaccion_id, '') as referencia_pago,
                    COALESCE(pg.comprobante_transferencia, '') as observaciones_empleado
                FROM pagos pg
                LEFT JOIN ped p ON pg.ped_idped = p.idped
                LEFT JOIN cli c ON p.cli_idcli = c.idcli
                WHERE pg.estado_pag = 'Completado' {$filtroSQL['where']}
                ORDER BY pg.fecha_pago DESC
                LIMIT {$filtros['limite']}
            ");
            $stmt->execute($filtroSQL['parametros']);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error en obtenerPagosCompletadosRecientes: " . $e->getMessage());
            return [];
        }
    }
    
    private function obtenerReporteMensual($filtros = []) {
        try {
            $filtroSQL = $this->construirFiltroSQL($filtros);
            
            // Ajustar filtro de fecha para reporte mensual
            $whereClause = "WHERE 1=1 {$filtroSQL['where']}";
            if (empty($filtros['fecha_inicio']) && empty($filtros['fecha_fin'])) {
                $whereClause = "WHERE MONTH(pg.fecha_pago) = MONTH(CURDATE()) AND YEAR(pg.fecha_pago) = YEAR(CURDATE())";
            }
            
            $stmt = $this->db->prepare("
                SELECT 
                    DATE(pg.fecha_pago) as fecha,
                    COUNT(*) as total_pagos,
                    COALESCE(SUM(pg.monto), 0) as monto_total,
                    COUNT(CASE WHEN pg.estado_pag = 'Completado' THEN 1 END) as completados,
                    COUNT(CASE WHEN pg.estado_pag = 'Pendiente' THEN 1 END) as pendientes
                FROM pagos pg
                LEFT JOIN ped p ON pg.ped_idped = p.idped
                LEFT JOIN cli c ON p.cli_idcli = c.idcli
                {$whereClause}
                GROUP BY DATE(pg.fecha_pago)
                ORDER BY fecha DESC
                LIMIT 30
            ");
            $stmt->execute($filtroSQL['parametros']);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error en obtenerReporteMensual: " . $e->getMessage());
            return [];
        }
    }
    
    private function obtenerReporteCompleto($filtros = []) {
        try {
            $filtroSQL = $this->construirFiltroSQL($filtros);
            
            $stmt = $this->db->prepare("
                SELECT 
                    pg.*,
                    COALESCE(p.numped, CONCAT('PED-', pg.ped_idped)) as numped,
                    COALESCE(c.nombre, 'Cliente no encontrado') as cliente_nombre,
                    COALESCE(pg.transaccion_id, '') as referencia_pago,
                    COALESCE(pg.comprobante_transferencia, '') as observaciones
                FROM pagos pg
                LEFT JOIN ped p ON pg.ped_idped = p.idped
                LEFT JOIN cli c ON p.cli_idcli = c.idcli
                WHERE 1=1 {$filtroSQL['where']}
                ORDER BY pg.fecha_pago DESC
                LIMIT 1000
            ");
            $stmt->execute($filtroSQL['parametros']);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error en obtenerReporteCompleto: " . $e->getMessage());
            return [];
        }
    }
    
    private function obtenerClientes() {
        try {
            $stmt = $this->db->prepare("
                SELECT DISTINCT COALESCE(c.nombre, 'Cliente no encontrado') as nombre
                FROM cli c 
                LEFT JOIN ped p ON c.idcli = p.cli_idcli 
                LEFT JOIN pagos pg ON p.idped = pg.ped_idped 
                WHERE c.nombre IS NOT NULL
                ORDER BY c.nombre
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (Exception $e) {
            error_log("Error en obtenerClientes: " . $e->getMessage());
            return [];
        }
    }
    
    private function obtenerMetodosPago() {
        try {
            $stmt = $this->db->prepare("
                SELECT DISTINCT metodo_pago 
                FROM pagos 
                WHERE metodo_pago IS NOT NULL 
                ORDER BY metodo_pago
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (Exception $e) {
            error_log("Error en obtenerMetodosPago: " . $e->getMessage());
            return [];
        }
    }
    
    private function actualizarEstadoPagoDirecto($idPago, $nuevoEstado) {
        try {
            $stmt = $this->db->prepare("
                UPDATE pagos 
                SET estado_pag = ?, 
                    verificado_por = ?,
                    fecha_verificacion = NOW()
                WHERE idpago = ?
            ");
            return $stmt->execute([$nuevoEstado, $this->empleado_id, $idPago]);
        } catch (Exception $e) {
            error_log("Error en actualizarEstadoPagoDirecto: " . $e->getMessage());
            return false;
        }
    }
}
?>