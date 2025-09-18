<?php
require_once __DIR__ . '/../models/conexion.php';
require_once __DIR__ . '/../models/mcalendar.php';

class ccalendar {
    private $modelo;
    public function __construct() {
        $conn = new conexion();
        $db = $conn->get_conexion();
        $this->modelo = new mcalendar($db);
    }
    // Obtener pedidos y resumen por fecha
    public function getPedidosYResumen($fecha) {
        $pedidos = $this->modelo->getPedidosPorFecha($fecha);
        $resumen = $this->modelo->getResumenPorFecha($fecha);
        return [
            'pedidos' => $pedidos,
            'resumen' => $resumen
        ];
    }
}
