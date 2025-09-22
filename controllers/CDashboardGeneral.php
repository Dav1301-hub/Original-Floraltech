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

    // Método para obtener todos los datos del dashboard
    public function getDashboardData() {
        $kpis = $this->modelo->getKPIs();
        if (!is_array($kpis)) {
            $kpis = [
                'totalPagos' => 0,
                'pagosPendientes' => 0,
                'pagosRechazados' => 0,
                'usuariosRegistrados' => 0,
                'nuevosUsuarios' => 0
            ];
        }

        $tendencias = $this->modelo->getTendencias();
        if (!is_array($tendencias)) {
            $tendencias = [
                'tendenciaPagos' => 0,
                'tendenciaPendientes' => 0,
                'tendenciaRechazados' => 0
            ];
        }

        $actividadReciente = $this->modelo->getActividadReciente();
        if (!is_array($actividadReciente)) {
            $actividadReciente = [];
        }

        $resumenPedidosMes = $this->modelo->getResumenPedidosMes();
        if (!is_array($resumenPedidosMes)) {
            $resumenPedidosMes = [
                'pedidosMes' => 0,
                'pedidosCompletados' => 0,
                'pedidosPendientes' => 0,
                'pedidosRechazados' => 0
            ];
        }

        return array_merge($kpis, $tendencias, [
            'actividadReciente' => $actividadReciente,
            'resumenPedidosMes' => $resumenPedidosMes
        ]);
    }

    // Acción por defecto
    public function index() {
        $dashboardData = $this->getDashboardData();
        require __DIR__ . '/../views/admin/dashboard_general.php';
    }

    // Acción alternativa
    public function mostrarDashboard() {
        $dashboardData = $this->getDashboardData();
        require __DIR__ . '/../views/admin/dashboard_general.php';
    }
}