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
        $entregasProximas = $this->modelo->getEntregasProximas();
        $tendenciaVentas = $this->modelo->getTendenciaVentas(14);
        $topProductos = $this->modelo->getTopProductos(5, 30);
        
        return array_merge($kpis, $tendencias, [
            'actividadReciente' => $actividadReciente,
            'resumenPedidosMes' => $resumenPedidosMes,
            'entregasProximas' => $entregasProximas,
            'tendenciaVentas' => $tendenciaVentas,
            'topProductos' => $topProductos
        ]);
    }

    // Nuevo método público para AJAX
    public function getActividadReciente() {
        return $this->modelo->getActividadReciente();
    }

}

// Endpoint para AJAX: ?action=actividadReciente
if (isset($_GET['action']) && $_GET['action'] === 'actividadReciente') {
    header('Content-Type: application/json');
    try {
        $controller = new CDashboardGeneral();
        $actividad = $controller->getActividadReciente();
        echo json_encode($actividad);
    } catch (Exception $e) {
        echo json_encode([]);
    }
    exit;
}
