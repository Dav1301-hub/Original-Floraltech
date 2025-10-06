<?php
require_once 'conexion.php';

class Mreportes{

    private $idped;
    private $numped;
    private $fecha_pedido;
    private $monto_total;
    private $cli_idcli;
    private $estado;
    private $empleado_id;
    private $notas;
    private $direccion_entrega;
    private $fecha_entrega_solicitada;
    
    function getIdped() {
        return $this->idped;
    }
    function getNumped() {
        return $this->numped;
    }
    function getFecha_pedido() {
        return $this->fehcha_pedido;
    }
    function getMonto_total() {
        return $this->monto_total;
    }
    function getCli_idcli() {
        return $this->cli_idcli;
    }
    function getEstado() {
        return $this->estado;
    }
    function getEmpleado_id() {
        return $this->empleado_id;
    }
    function getNotas() {
        return $this->notas;
    }
    function getDireccion_entrega() {
        return $this->direccion_entrega;
    }
    function getFecha_entrega_solicitada() {
        return $this->fecha_entrega_solicitada;
    }
    function setIdped($idped) {
        $this->idped = $idped;
    }
    function setNumped($numped) {
        $this->numped = $numped;
    }
    function setFecha_pedido($fecha_pedido) {
        $this->fecha_pedido = $fecha_pedido;
    }
    function setMonto_total($monto_total) {
        $this->monto_total = $monto_total;
    }
    function setCli_idcli($cli_idcli) {
        $this->cli_idcli = $cli_idcli;
    }
    function setEstado($estado) {
        $this->estado = $estado;
    }
    function setEmpleado_id($empleado_id) {
        $this->empleado_id = $empleado_id;
    }
    function setNotas($notas) {
        $this->notas = $notas;
    }
    function setDireccion_entrega($direccion_entrega) {
        $this->direccion_entrega = $direccion_entrega;
    }
    function setFecha_entrega_solicitada($fecha_entrega_solicitada) {
        $this->fecha_entrega_solicitada = $fecha_entrega_solicitada;
    }

    public function getAll() {
    try {
        $sql = "SELECT idped, numped, fecha_pedido, monto_total, cli_idcli, estado, empleado_id, notas, direccion_entrega, fecha_entrega_solicitada  FROM ped";
        $modelo = new conexion();
        $conexion = $modelo->get_conexion();
        $res = $conexion->prepare($sql);
        $res->execute();
        return $res->fetchAll(PDO::FETCH_ASSOC);
    } catch(Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}

}


?>