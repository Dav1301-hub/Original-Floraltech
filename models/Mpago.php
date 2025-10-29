<?php

class Mpago {
    // Resumen de Ganancias
    public function getResumenGanancias() {
        $query = "SELECT SUM(CASE WHEN estado_pag = 'Completado' THEN monto ELSE 0 END) as total_recaudado,
                          SUM(CASE WHEN estado_pag = 'Completado' THEN monto ELSE 0 END) - SUM(CASE WHEN estado_pag = 'Pendiente' THEN monto ELSE 0 END) as ganancia_neta,
                          SUM(CASE WHEN estado_pag = 'Completado' THEN monto ELSE 0 END) as total_ventas,
                          SUM(CASE WHEN estado_pag = 'Pendiente' THEN monto ELSE 0 END) as total_costos,
                          SUM(CASE WHEN estado_pag = 'Completado' THEN 1 ELSE 0 END) as pagos_completados,
                          SUM(CASE WHEN estado_pag = 'Pendiente' THEN 1 ELSE 0 END) as pagos_pendientes
                   FROM pagos";
        return $this->db->query($query)->fetch(PDO::FETCH_ASSOC);
    }

    // Resumen de Ventas
    public function getResumenVentas() {
        $query = "SELECT SUM(monto) as total,
                          COUNT(*) as pedidos,
                          COUNT(DISTINCT ped_idped) as clientes,
                          AVG(monto) as promedio
                   FROM pagos WHERE estado_pag = 'Completado'";
        return $this->db->query($query)->fetch(PDO::FETCH_ASSOC);
    }

    // Resumen de Costos
    public function getResumenCostos() {
        $query = "SELECT SUM(monto) as total,
                          SUM(CASE WHEN estado_pag = 'Fijo' THEN monto ELSE 0 END) as fijos,
                          SUM(CASE WHEN estado_pag = 'Variable' THEN monto ELSE 0 END) as variables,
                          SUM(CASE WHEN estado_pag = 'Otro' THEN monto ELSE 0 END) as otros
                   FROM pagos";
        return $this->db->query($query)->fetch(PDO::FETCH_ASSOC);
    }

    // Resumen de Inventario
    public function getResumenInventario() {
        $query = "SELECT SUM(stock) as stock_total,
                          COUNT(*) as productos,
                          SUM(CASE WHEN stock < 10 THEN 1 ELSE 0 END) as stock_bajo,
                          SUM(CASE WHEN stock < 3 THEN 1 ELSE 0 END) as stock_critico
                   FROM inventario";
        return $this->db->query($query)->fetch(PDO::FETCH_ASSOC);
    }

    // Resumen de Cuentas
    public function getResumenCuentas() {
        $query = "SELECT SUM(CASE WHEN tipo = 'por_cobrar' THEN monto ELSE 0 END) as por_cobrar,
                          SUM(CASE WHEN tipo = 'por_pagar' THEN monto ELSE 0 END) as por_pagar,
                          SUM(monto) as saldo_neto,
                          COUNT(*) as movimientos
                   FROM cuentas";
        return $this->db->query($query)->fetch(PDO::FETCH_ASSOC);
    }

    // Resumen de Pagos
    public function getResumenPagos() {
        $query = "SELECT SUM(CASE WHEN estado_pag = 'Completado' THEN monto ELSE 0 END) as realizados,
                          SUM(CASE WHEN estado_pag = 'Pendiente' THEN monto ELSE 0 END) as pendientes,
                          SUM(CASE WHEN estado_pag = 'Rechazado' THEN monto ELSE 0 END) as rechazados,
                          COUNT(*) as transacciones
                   FROM pagos";
        return $this->db->query($query)->fetch(PDO::FETCH_ASSOC);
    }

    // Resumen de Proyecciones
    public function getResumenProyecciones() {
        $query = "SELECT SUM(ventas) as ventas,
                          SUM(ganancias) as ganancias,
                          SUM(costos) as costos,
                          MAX(periodo) as periodo
                   FROM proyecciones";
        return $this->db->query($query)->fetch(PDO::FETCH_ASSOC);
    }

    // Resumen de Auditoría
    public function getResumenAuditoria() {
        $query = "SELECT COUNT(*) as acciones,
                          COUNT(DISTINCT usuario_id) as usuarios,
                          MAX(fecha) as ultima,
                          SUM(CASE WHEN tipo = 'incidencia' THEN 1 ELSE 0 END) as incidencias
                   FROM auditoria";
        return $this->db->query($query)->fetch(PDO::FETCH_ASSOC);
    }
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // Resumen de métodos de pago: cantidad y sumatoria por método
    public function obtenerResumenMetodosPago() {
        $query = "SELECT metodo_pago, COUNT(*) as cantidad, SUM(monto) as total
                  FROM pagos
                  WHERE estado_pag = 'Completado'
                  GROUP BY metodo_pago";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener todos los pagos (para admin)
    public function obtenerTodosLosPagos() {
        $query = "SELECT p.*, pe.numped, c.nombre as cliente 
                FROM pagos p
                JOIN ped pe ON p.ped_idped = pe.idped
                JOIN cli c ON pe.cli_idcli = c.idcli
                ORDER BY p.fecha_pago DESC";
        return $this->db->query($query)->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener pagos por cliente
    public function obtenerPagosPorCliente($idCliente) {
        $query = "SELECT p.*, pe.numped 
                FROM pagos p
                JOIN ped pe ON p.ped_idped = pe.idped
                WHERE pe.cli_idcli = :idCliente
                ORDER BY p.fecha_pago DESC";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':idCliente', $idCliente, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener pagos por estado (para empleados)
    public function obtenerPagosPorEstado($estado) {
        $query = "SELECT p.*, pe.numped, c.nombre as cliente 
                FROM pagos p
                JOIN ped pe ON p.ped_idped = pe.idped
                JOIN cli c ON pe.cli_idcli = c.idcli
                WHERE p.estado_pag = :estado
                ORDER BY p.fecha_pago DESC";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':estado', $estado, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Registrar un nuevo pago
    public function registrarPago($datosPago) {
        $query = "INSERT INTO pagos (metodo_pago, estado_pag, monto, ped_idped, transaccion_id)
                VALUES (:metodo, 'Completado', :monto, :pedido, :transaccion)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':metodo', $datosPago['metodo'], PDO::PARAM_STR);
        $stmt->bindParam(':monto', $datosPago['monto'], PDO::PARAM_STR);
        $stmt->bindParam(':pedido', $datosPago['pedido'], PDO::PARAM_INT);
        $stmt->bindParam(':transaccion', $datosPago['transaccion'], PDO::PARAM_STR);
        return $stmt->execute();
    }

    // Actualizar estado de pago
    public function actualizarEstadoPago($idPago, $nuevoEstado) {
        $query = "UPDATE pagos SET estado_pag = :estado WHERE idpago = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':estado', $nuevoEstado, PDO::PARAM_STR);
        $stmt->bindParam(':id', $idPago, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Obtener estadísticas de pagos (para dashboard admin)
    public function obtenerEstadisticasPagos() {
        $query = "SELECT 
                    COUNT(*) as total_pagos,
                    SUM(CASE WHEN estado_pag = 'Completado' THEN monto ELSE 0 END) as ingresos_totales,
                    SUM(CASE WHEN estado_pag = 'Completado' THEN 1 ELSE 0 END) as pagos_completados,
                    SUM(CASE WHEN estado_pag = 'Pendiente' THEN 1 ELSE 0 END) as pagos_pendientes,
                    metodo_pago, COUNT(*) as cantidad
                FROM pagos
                GROUP BY metodo_pago";
        return $this->db->query($query)->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener detalles de un pedido para pago
    public function obtenerDetallesPedido($idPedido) {
        $query = "SELECT p.*, c.nombre as cliente, c.email
                FROM ped p
                JOIN cli c ON p.cli_idcli = c.idcli
                WHERE p.idped = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $idPedido, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function obtenerRecibosPorCliente($idCliente) {
        $query = "
            SELECT 
                p.*,
                DATE_FORMAT(p.fecha_pedido, '%d/%m/%Y %H:%i') as fecha_formato,
                pg.estado_pag,
                pg.metodo_pago,
                pg.fecha_pago,
                pg.idpago,
                pg.transaccion_id,
                COUNT(dp.iddetped) as total_items,
                GROUP_CONCAT(DISTINCT CONCAT(tf.nombre, ' (', dp.cantidad, ')') as items_detalle
            FROM ped p 
            JOIN pagos pg ON p.idped = pg.ped_idped
            LEFT JOIN detped dp ON p.idped = dp.idped
            LEFT JOIN tflor tf ON dp.idtflor = tf.idtflor
            WHERE p.cli_idcli = :idCliente AND pg.estado_pag = 'Completado'
            GROUP BY p.idped
            ORDER BY p.fecha_pedido DESC
        ";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':idCliente', $idCliente, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener detalle completo de un pago
    public function obtenerDetallePago($idPago) {
        $query = "SELECT p.*, pe.numped, pe.monto_total, c.nombre as cliente, c.email
                  FROM pagos p
                  JOIN ped pe ON p.ped_idped = pe.idped
                  JOIN cli c ON pe.cli_idcli = c.idcli
                  WHERE p.idpago = :id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $idPago, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
