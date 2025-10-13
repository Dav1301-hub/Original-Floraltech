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
    private $idusu;
    private $username;
    private $nombre_completo;
    private $naturaleza;
    private $telefono;
    private $email;
    private $clave;
    private $tpusu_idtpusu;
    private $fecha_registro;
    private $activo;
    private $vacaciones;

    
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
    function getIdusu() {
        return $this->idusu;
    }
    function getUsername() {
        return $this->username;
    }
    function getNombre_completo() {
        return $this->nombre_completo;
    }
    function getNaturaleza() {
        return $this->naturaleza;
    }
    function getTelefono() {
        return $this->telefono;
    }
    function getEmail() {
        return $this->email;
    }
    function getClave() {
        return $this->clave;
    }
    function getTpusu_idtpusu() {
        return $this->tpusu_idtpusu;
    }
    function getFecha_registro() {
        return $this->fecha_registro;
    }
    function getActivo() {
        return $this->activo;
    }
    function getVacaciones() {
        return $this->vacaciones;
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
    function setIdusu($idusu) {
        $this->idusu = $idusu;
    }
    function setUsername($username) {
        $this->username = $username;
    }
    function setNombre_completo($nombre_completo) {
        $this->nombre_completo = $nombre_completo;
    }
    function setNaturaleza($naturaleza) {
        $this->naturaleza = $naturaleza;
    }
    function setTelefono($telefono) {
        $this->telefono = $telefono;
    }
    function setEmail($email) {
        $this->email = $email;
    }
    function setClave($clave) {
        $this->clave = $clave;
    }
    function setTpusu_idtpusu($tpusu_idtpusu) {
        $this->tpusu_idtpusu = $tpusu_idtpusu;
    }
    function setFecha_registro($fecha_registro) {
        $this->fecha_registro = $fecha_registro;
    }
    function setActivo($activo) {
        $this->activo = $activo;
    }
    function setVacaciones($vacaciones) {
        $this->vacaciones = $vacaciones;
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

public function getAllusu() {
    try {
        $sql = "SELECT 
                    u.idusu,
                    u.username,
                    u.nombre_completo,
                    t.nombre AS tipo_usuario,
                    u.telefono,
                    u.email,
                    u.activo
                FROM usu u
                JOIN tpusu t ON u.tpusu_idtpusu = t.idtpusu";

        $modelo = new conexion();
        $conexion = $modelo->get_conexion();
        $res = $conexion->prepare($sql);
        $res->execute();
        return $res->fetchAll(PDO::FETCH_ASSOC);
    } catch(Exception $e) {
        echo 'Error: ' . $e->getMessage();
    }
}



}


?>