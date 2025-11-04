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
        $this->gestionPagos();
    }

    public function gestionPagos() {
        try {
            $user = $_SESSION['user'];
            
            // Obtener estadísticas de pagos
            $stats = $this->obtenerEstadisticasPagos();
            $pagosPendientes = $this->obtenerPagosPendientes();
            $pagosCompletados = $this->obtenerPagosCompletadosRecientes();
            $reporteMensual = $this->obtenerReporteMensual();
            
            include 'views/empleado/reportes_pagos.php';
            
        } catch (Exception $e) {
            error_log("Error en gestión de pagos: " . $e->getMessage());
            $_SESSION['mensaje'] = "Ocurrió un error al cargar los reportes de pagos";
            $_SESSION['tipo_mensaje'] = "error";
            header("Location: index.php?ctrl=empleado&action=dashboard");
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
            
            header("Location: index.php?ctrl=CempleadoPagos&action=index");
            exit();
        }
    }
    
    // Métodos privados para obtener datos
    private function obtenerEstadisticasPagos() {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    COUNT(CASE WHEN estado_pag = 'Pendiente' THEN 1 END) as pendientes,
                    COALESCE(SUM(CASE WHEN estado_pag = 'Pendiente' THEN monto ELSE 0 END), 0) as monto_pendiente,
                    COUNT(CASE WHEN estado_pag = 'Completado' THEN 1 END) as completados,
                    COALESCE(SUM(CASE WHEN estado_pag = 'Completado' THEN monto ELSE 0 END), 0) as monto_completado,
                    COUNT(CASE WHEN estado_pag = 'Completado' AND DATE(fecha_pago) = CURDATE() THEN 1 END) as completados_hoy,
                    COALESCE(SUM(CASE WHEN estado_pag = 'Completado' AND DATE(fecha_pago) = CURDATE() THEN monto ELSE 0 END), 0) as monto_hoy
                FROM pagos
            ");
            $stmt->execute();
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
    
    private function obtenerPagosPendientes() {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    pg.*,
                    p.numped,
                    c.nombre as cliente_nombre,
                    COALESCE(pg.referencia_pago, '') as referencia_pago,
                    COALESCE(pg.observaciones, '') as observaciones
                FROM pagos pg
                INNER JOIN ped p ON pg.ped_idped = p.idped
                INNER JOIN cli c ON p.cli_idcli = c.idcli
                WHERE pg.estado_pag = 'Pendiente'
                ORDER BY pg.fecha_pago DESC
                LIMIT 20
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error en obtenerPagosPendientes: " . $e->getMessage());
            return [];
        }
    }
    
    private function obtenerPagosCompletadosRecientes() {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    pg.*,
                    p.numped,
                    c.nombre as cliente_nombre,
                    COALESCE(pg.referencia_pago, '') as referencia_pago,
                    COALESCE(pg.observaciones_empleado, '') as observaciones_empleado
                FROM pagos pg
                INNER JOIN ped p ON pg.ped_idped = p.idped
                INNER JOIN cli c ON p.cli_idcli = c.idcli
                WHERE pg.estado_pag = 'Completado'
                ORDER BY pg.fecha_pago DESC
                LIMIT 20
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error en obtenerPagosCompletadosRecientes: " . $e->getMessage());
            return [];
        }
    }
    
    private function obtenerReporteMensual() {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    DATE(fecha_pago) as fecha,
                    COUNT(*) as total_pagos,
                    COALESCE(SUM(monto), 0) as monto_total,
                    COUNT(CASE WHEN estado_pag = 'Completado' THEN 1 END) as completados,
                    COUNT(CASE WHEN estado_pag = 'Pendiente' THEN 1 END) as pendientes
                FROM pagos 
                WHERE MONTH(fecha_pago) = MONTH(CURDATE()) 
                AND YEAR(fecha_pago) = YEAR(CURDATE())
                GROUP BY DATE(fecha_pago)
                ORDER BY fecha DESC
                LIMIT 30
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error en obtenerReporteMensual: " . $e->getMessage());
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