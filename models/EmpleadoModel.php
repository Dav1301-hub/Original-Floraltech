<?php
class EmpleadoModel {
    private $db;
    private $id;
    private $nombre;
    private $puesto;

    public function __construct($db) {
        $this->db = $db;
    }

    // Setters
    public function setId($id) {
        $this->id = $id;
    }
    public function setNombre($nombre) {
        $this->nombre = $nombre;
    }
    public function setPuesto($puesto) {
        $this->puesto = $puesto;
    }

    // Getters
    public function getId() {
        return $this->id;
    }
    public function getNombre() {
        return $this->nombre;
    }
    public function getPuesto() {
        return $this->puesto;
    }

    // Obtener todos los empleados
    public function getAll() {
        $result = $this->db->query("SELECT * FROM empleados");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Obtener un empleado por id
    public function getOne($id) {
        $stmt = $this->db->prepare("SELECT * FROM empleados WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Guardar (insertar o actualizar) empleado
    public function save() {
        if ($this->id) {
            // Actualizar
            $stmt = $this->db->prepare("UPDATE empleados SET nombre = ?, puesto = ? WHERE id = ?");
            $stmt->bind_param("ssi", $this->nombre, $this->puesto, $this->id);
            return $stmt->execute();
        } else {
            // Insertar
            $stmt = $this->db->prepare("INSERT INTO empleados (nombre, puesto) VALUES (?, ?)");
            $stmt->bind_param("ss", $this->nombre, $this->puesto);
            return $stmt->execute();
        }
    }

    // Eliminar empleado
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM empleados WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
?>