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

    // Obtener parámetros de filtro
    $filtros = [
        'fecha_inicio' => $_GET['fecha_inicio'] ?? null,
        'fecha_fin' => $_GET['fecha_fin'] ?? null,
        'estado' => $_GET['estado'] ?? null
    ];

    // Obtener todos los pagos y filtrar manualmente
    $pagos = $this->model->obtenerTodosLosPagos();
    // Filtrar por fecha y estado si se proporcionan
    if ($filtros['fecha_inicio'] || $filtros['fecha_fin'] || $filtros['estado']) {
        $pagos = array_filter($pagos, function($pago) use ($filtros) {
            $fechaValida = true;
            if ($filtros['fecha_inicio']) {
                $fechaValida = $fechaValida && (strtotime($pago['fecha_pago']) >= strtotime($filtros['fecha_inicio']));
            }
            if ($filtros['fecha_fin']) {
                $fechaValida = $fechaValida && (strtotime($pago['fecha_pago']) <= strtotime($filtros['fecha_fin']));
            }
            $estadoValido = true;
            if ($filtros['estado']) {
                $estadoValido = strtolower($pago['estado_pag']) === strtolower($filtros['estado']);
            }
            return $fechaValida && $estadoValido;
        });
    }
    
    // Incluir vista
    include 'views/admin/auditoria.php';
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
