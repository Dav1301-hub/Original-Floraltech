<?php
// Modelo para procesar pagos
require_once __DIR__ . '/conexion.php';

class MprocesarPagos {
    private $db;
    public function __construct() {
        $this->db = new conexion();
    }
    public function getPagosPendientes() {
        $sql = "SELECT p.idpago, p.monto, p.estado_pag, p.metodo_pago, ped.numped, cli.nombre as cliente
                FROM pagos p
                JOIN ped ON p.ped_idped = ped.idped
                JOIN cli ON ped.cli_idcli = cli.idcli";
        $stmt = $this->db->get_conexion()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function procesarPago($pagoId) {
        $sql = "UPDATE pagos SET estado_pag = 'Completado' WHERE idpago = ?";
        $stmt = $this->db->get_conexion()->prepare($sql);
        return $stmt->execute([$pagoId]);
    }
}
