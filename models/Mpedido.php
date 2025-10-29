<?php
// models/Mpedido.php
require_once __DIR__ . '/conexion.php';

class Mpedido {
    private $db;

    public function __construct() {
        $conn = new conexion();
        $this->db = $conn->get_conexion();
    }

    public function obtenerPedidos() {
        $stmt = $this->db->prepare("SELECT p.idped AS id, c.nombre AS cliente, p.estado FROM ped p JOIN cli c ON p.cli_idcli = c.idcli");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function actualizarEstadoPedido($pedidoId, $nuevoEstado) {
        $stmt = $this->db->prepare("UPDATE ped SET estado = :estado WHERE idped = :id");
        $stmt->bindParam(':estado', $nuevoEstado);
        $stmt->bindParam(':id', $pedidoId);
        return $stmt->execute();
    }
}
