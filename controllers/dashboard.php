<?php
// controllers/dashboard.php
require_once 'models/user.php';

class dashboard {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
        
        // Verificar que el usuario esté logueado
        if (!isset($_SESSION['user'])) {
            header('Location: index.php?ctrl=login&action=index');
            exit();

        }
    }

    /**
     * Dashboard principal - redirige según el tipo de usuario
     */
    public function index() {
        $userType = $_SESSION['user']['tpusu_idtpusu'];
        
        switch ($userType) {
            case 1: // Administrador
                $this->admin();
                break;
            case 2: // Vendedor
                $this->empleado();
                break;
            case 3: // Inventario
                $this->empleado();
                break;
            case 4: // Repartidor
                $this->empleado();
                break;
            case 5: // Cliente
                $this->cliente();
                break;
            default:
                $this->cliente();
                break;
        }
    }

    /**
     * Dashboard para administradores
     */
    public function admin() {
    $user = $_SESSION['user'];
    $usu = $_SESSION['user']; // Variable adicional para compatibilidad con las vistas
    $pageTitle = "Dashboard - Administrador";
    $totalUsuarios = $this->userModel->getTotalUsuarios();
    require_once 'views/admin/dashboard.php';
    }

    /**
     * Dashboard para empleados - Redirige al controlador específico
     */
    public function empleado() {
        // Redirigir directamente al nuevo controlador de empleado
        header('Location: index.php?ctrl=empleado&action=dashboard');
        exit();
    }

    /**
     * Dashboard para clientes
     */
    public function cliente() {
        $user = $_SESSION['user'];
        $pageTitle = "Dashboard - Cliente";
        require_once 'views/cliente/dashboard.php';
    }

    /**
     * Perfil del usuario
     */
    public function profile() {
        $user = $_SESSION['user'];
        require_once 'views/profile.php';
    }
}
?>