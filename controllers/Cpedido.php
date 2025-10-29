<?php
// controllers/PedidoController.php
require_once __DIR__ . '/../models/conexion.php';

class Cpedido {
    private $db;

    public function __construct() {
        $conn = new conexion();
        $this->db = $conn->get_conexion();
    }

    public function obtenerPedidos() {
        $stmt = $this->db->prepare("SELECT p.idped AS id, c.nombre AS cliente, p.estado FROM ped p JOIN cli c ON p.cli_idcli = c.idcli");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function actualizarEstadoPedido($pedidoId, $nuevoEstado) {
        $stmt = $this->db->prepare("UPDATE ped SET estado = :estado WHERE idped = :id");
        $stmt->bindParam(':estado', $nuevoEstado);
        $stmt->bindParam(':id', $pedidoId);
        return $stmt->execute();
    }

    // Nuevo método para obtener pedidos filtrados
    public function obtenerPedidosFiltrados($estadoPedido = '', $estadoPago = '', $fechaDesde = '', $fechaHasta = '') {
        $sql = "SELECT p.idped AS id, c.nombre AS cliente, p.estado
                FROM ped p
                JOIN cli c ON p.cli_idcli = c.idcli";
        $where = [];
        $params = [];

        if ($estadoPedido) {
            $where[] = "p.estado = :estadoPedido";
            $params[':estadoPedido'] = $estadoPedido;
        }
        // Si tienes estado de pago en la tabla, agrega aquí el filtro
        // if ($estadoPago) { ... }
        if ($fechaDesde) {
            $where[] = "p.fecha_pedido >= :fechaDesde";
            $params[':fechaDesde'] = $fechaDesde;
        }
        if ($fechaHasta) {
            $where[] = "p.fecha_pedido <= :fechaHasta";
            $params[':fechaHasta'] = $fechaHasta;
        }
        if ($where) {
            $sql .= " WHERE " . implode(' AND ', $where);
        }
        $sql .= " ORDER BY p.idped DESC";
        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
