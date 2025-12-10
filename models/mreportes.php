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
    private $idtflor;
    private $nombre;
    private $descripcion;
    private $precio;
    private $fecha_creacion;
    private $color;
    private $precio_venta;
    private $idpago;
    private $fecha_pago;
    private $metodo_pago;
    private $estado_pag;
    private $monto;
    private $transaccion_id;
    private $comprobante_transferencia;


    
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
    function getIdtflor() {
        return $this->idtflor;
    }
    function getNombre() {
        return $this->nombre;
    }
    function getDescripcion() {
        return $this->descripcion;
    }
    function getPrecio() {
        return $this->precio;
    }
    function getFecha_creacion() {
        return $this->fecha_creacion;
    }
    function getColor() {
        return $this->color;
    }
    function getPrecio_venta() {
        return $this->precio_venta;
    }
    function getIdpago() {
        return $this->idpago;
    }
    function getFecha_pago() {
        return $this->fecha_pago;
    }
    function getMetodo_pago() {
        return $this->metodo_pago;
    }
    function getEstado_pag() {
        return $this->estado_pag;
    }
    function getMonto() {
        return $this->monto;
    }
    function getTransaccion_id() {
        return $this->transaccion_id;
    }
    function getComprobante_transferencia() {
        return $this->comprobante_transferencia;
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
    function setIdtflor($idtflor) {
        $this->idtflor = $idtflor;
    }
    function setNombre($nombre) {
        $this->nombre = $nombre;
    }
    function setDescripcion($descripcion) {
        $this->descripcion = $descripcion;
    }
    function setPrecio($precio) {
        $this->precio = $precio;
    }
    function setFecha_creacion($fecha_creacion) {
        $this->fecha_creacion = $fecha_creacion;
    }
    function setColor($color) {
        $this->color = $color;
    }
    function setPrecio_venta($precio_venta) {
        $this->precio_venta = $precio_venta;
    }
    function setIdpago($idpago) {
        $this->idpago = $idpago;
    }
    function setFecha_pago($fecha_pago) {
        $this->fecha_pago = $fecha_pago;
    }
    function setMetodo_pago($metodo_pago) {
        $this->metodo_pago = $metodo_pago;
    }
    function setEstado_pag($estado_pag) {
        $this->estado_pag = $estado_pag;
    }
    function setMonto($monto) {
        $this->monto = $monto;
    }
    function setTransaccion_id($transaccion_id) {
        $this->transaccion_id = $transaccion_id;
    }
    function setComprobante_transferencia($comprobante_transferencia) {
        $this->comprobante_transferencia = $comprobante_transferencia;
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
            error_log("Mreportes getAll: " . $e->getMessage());
            return [];
        }
    }

public function getAllusu($tipo = null) {
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
                JOIN tpusu t ON u.tpusu_idtpusu = t.idtpusu
                WHERE (:tipo IS NULL OR LOWER(t.nombre) = LOWER(:tipo))";

        $modelo = new conexion();
        $conexion = $modelo->get_conexion();
        $res = $conexion->prepare($sql);
        $res->bindValue(':tipo', $tipo);
        $res->execute();
        return $res->fetchAll(PDO::FETCH_ASSOC);
    } catch(Exception $e) {
        error_log('Mreportes getAllusu: ' . $e->getMessage());
        return [];
    }
}

public function getAllInventario() {
    try {
        $sql = "SELECT 
            i.idinv,
            i.nombre AS producto,
            t.nombre AS categoria,
            i.naturaleza,
            i.color,
            i.stock,
            CASE WHEN i.stock > 0 THEN 'Disponible' ELSE 'Agotado' END AS estado,
            i.precio AS precio_unitario,
            (i.stock * i.precio) AS valor_total
        FROM inv i
        LEFT JOIN tflor t ON i.tflor_idtflor = t.idtflor";
        
        $modelo = new conexion();
        $conexion = $modelo->get_conexion();
        $res = $conexion->prepare($sql);
        $res->execute();

        return $res->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Mreportes getAllInventario: " . $e->getMessage());
        return [];
    }
}

public function getAllPagos() {
    try {
        $sql = "SELECT 
                    p.idpago,
                    p.fecha_pago,
                    p.metodo_pago,
                    p.estado_pag,
                    p.monto,
                    p.transaccion_id,
                    p.comprobante_transferencia,
                    pe.numped,
                    pe.idped,
                    c.nombre AS cliente
                FROM pagos p
                LEFT JOIN ped pe ON p.ped_idped = pe.idped
                LEFT JOIN cli c ON pe.cli_idcli = c.idcli";
        $modelo = new conexion();
        $conexion = $modelo->get_conexion();
        $res = $conexion->prepare($sql);
        $res->execute();
        return $res->fetchAll(PDO::FETCH_ASSOC);
    } catch(Exception $e) {
        error_log("Mreportes getAllPagos: " . $e->getMessage());
        return [];
    }
}

}


?>
