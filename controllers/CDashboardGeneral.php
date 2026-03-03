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
        $mes = isset($_GET['mes']) ? (int)$_GET['mes'] : null;
        $ano = isset($_GET['ano']) ? (int)$_GET['ano'] : null;
        
        if (isset($_GET['periodo']) && !empty($_GET['periodo'])) {
            $parts = explode('-', $_GET['periodo']);
            if (count($parts) === 2) {
                $mes = (int)$parts[0];
                $ano = (int)$parts[1];
            }
        }

        $kpis = $this->modelo->getKPIs($mes, $ano);
        $tendencias = $this->modelo->getTendencias($mes, $ano);
        $actividadReciente = $this->modelo->getActividadReciente();
        $resumenPedidosMes = $this->modelo->getResumenPedidosMes($mes, $ano);
        $entregasProximas = $this->modelo->getEntregasProximas();
        $tendenciaVentas = $this->modelo->getTendenciaVentas(14, $mes, $ano);
        $topProductos = $this->modelo->getTopProductos(5, $mes, $ano);
        $periodosDisponibles = $this->modelo->getPeriodosDisponibles();
        
        return array_merge($kpis, $tendencias, [
            'actividadReciente' => $actividadReciente,
            'resumenPedidosMes' => $resumenPedidosMes,
            'entregasProximas' => $entregasProximas,
            'tendenciaVentas' => $tendenciaVentas,
            'topProductos' => $topProductos,
            'periodos' => $periodosDisponibles,
            'filtro' => ['mes' => $mes, 'ano' => $ano]
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
