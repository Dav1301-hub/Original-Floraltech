<?php
require_once __DIR__ . '/conexion.php';
class InventarioModel {
    private $db;
    public function __construct() {
        $this->db = new conexion();
    }
    public function getInventario() {
        $sql = "SELECT inv.idinv, tflor.nombre AS nombre_flor, inv.stock, inv.precio, inv.fecha_actualizacion
                FROM inv
                JOIN tflor ON inv.tflor_idtflor = tflor.idtflor";
        $stmt = $this->db->get_conexion()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
