<?php
class CadminPagos {
    public function index() {
        // Puedes personalizar la vista que se muestra aquí
        $this->dashboard();
    }
    private $model;
    private $rol;

    public function __construct() {
        require_once __DIR__ . '/../config/db.php';
        require_once __DIR__ . '/../models/Mpago.php';
        $db = (new Database())->connect();
        $this->model = new Mpago($db);
        $this->rol = $_SESSION['rol'] ?? null;
    }

    public function dashboard() {
        // Aquí puedes agregar lógica de permisos si lo necesitas
        // Obtener estadísticas y pagos recientes desde el modelo
        $estadisticas = $this->model->obtenerEstadisticasPagos();
        $pagosRecientes = array_slice($this->model->obtenerTodosLosPagos(), 0, 10);
        $resumenMetodosPago = $this->model->obtenerResumenMetodosPago();
        include 'views/admin/VadashboardPagos.php';
    }

    public function generarReporte($tipo) {
        if ($this->rol !== 'admin') {
            header("Location: /acceso-denegado");
            exit();
        }

        $datos = [];
        switch ($tipo) {
            case 'ganancias':
                $datos = $this->model->obtenerEstadisticasPagos();
                break;
            case 'auditoria':
                $datos = $this->model->obtenerTodosLosPagos();
                break;
        }
        
        include 'view/admin/reportes.php';
    }

public function auditoriaFinanciera() {
    if ($this->rol !== 'admin') {
        header("Location: /acceso-denegado");
        exit();
    }
    // Reutilizar la vista unificada del dashboard
    header("Location: index.php?ctrl=dashboard&action=admin&page=auditoria");
    exit();
}

    public function actualizarProyecciones() {
        if ($this->rol !== 'admin') {
            header("Location: /acceso-denegado");
            exit();
        }

        // Lógica para actualizar proyecciones
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Procesar datos del formulario
            // ...
            $_SESSION['mensaje'] = "Proyecciones actualizadas correctamente";
            header("Location: /admin/pagos/dashboard");
            exit();
        }
        
        // Mostrar formulario de proyecciones
        include 'view/admin/proyecciones.php';
    }
}
?>
