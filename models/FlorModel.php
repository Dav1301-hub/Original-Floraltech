<?php
require_once 'conexion.php';

class FlorModel {
    private $db;

    public function __construct() {
        $conexion = new conexion();
        $this->db = $conexion->get_conexion();
    }

    public function getAllFlores() {
        $stmt = $this->db->prepare("SELECT tf.*, i.stock, i.precio FROM tflor tf LEFT JOIN inv i ON tf.idtflor = i.tflor_idtflor ORDER BY tf.nombre");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getFlor($id) {
        $stmt = $this->db->prepare("SELECT tf.*, i.stock, i.precio FROM tflor tf LEFT JOIN inv i ON tf.idtflor = i.tflor_idtflor WHERE tf.idtflor = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function addFlor($nombre, $naturaleza, $descripcion, $stock, $precio) {
        $stmt = $this->db->prepare("INSERT INTO tflor (nombre, naturaleza, descripcion) VALUES (?, ?, ?)");
        $stmt->execute([$nombre, $naturaleza, $descripcion]);
        $idtflor = $this->db->lastInsertId();
        $stmt2 = $this->db->prepare("INSERT INTO inv (tflor_idtflor, stock, precio) VALUES (?, ?, ?)");
        $stmt2->execute([$idtflor, $stock, $precio]);
        return $idtflor;
    }

    public function updateFlor($id, $nombre, $naturaleza, $descripcion, $stock, $precio) {
        $stmt = $this->db->prepare("UPDATE tflor SET nombre = ?, naturaleza = ?, descripcion = ? WHERE idtflor = ?");
        $stmt->execute([$nombre, $naturaleza, $descripcion, $id]);
        $stmt2 = $this->db->prepare("UPDATE inv SET stock = ?, precio = ? WHERE tflor_idtflor = ?");
        $stmt2->execute([$stock, $precio, $id]);
        return true;
    }

    public function deleteFlor($id) {
        $stmt2 = $this->db->prepare("DELETE FROM inv WHERE tflor_idtflor = ?");
        $stmt2->execute([$id]);
        $stmt = $this->db->prepare("DELETE FROM tflor WHERE idtflor = ?");
        $stmt->execute([$id]);
        return true;
    }
}
