<?php
require_once __DIR__ . '/../models/conexion.php';
require_once __DIR__ . '/../models/MDashboardGeneral.php';

class CDashboardGeneral {
    private $modelo;
    public function __construct() {
        $conn = new conexion();
        $db = $conn->get_conexion();
        $this->modelo = new MDashboardGeneral($db);
    }
    public function getDashboardData() {
        $kpis = $this->modelo->getKPIs();
        $tendencias = $this->modelo->getTendencias();
        $actividadReciente = $this->modelo->getActividadReciente();
        $resumenPedidosMes = $this->modelo->getResumenPedidosMes();
        return array_merge($kpis, $tendencias, [
            'actividadReciente' => $actividadReciente,
            'resumenPedidosMes' => $resumenPedidosMes
        ]);
    }
}
