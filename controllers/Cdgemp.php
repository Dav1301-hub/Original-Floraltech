<?php
class Cdgemp {
    private $userModel;
    
    public function __construct() {
        require_once __DIR__ . '/../models/Mdgemp.php';
        $this->userModel = new Mdgemp();
    }
    
    /**
     * Muestra la vista de gestión de empleados
     */
    public function index() {
        $empleados = $this->userModel->getAllEmpleados();
        $tipos_usuario = $this->userModel->getTiposUsuario();
        $empleados_activos = $this->userModel->getEmpleadosActivos();
        
        // Hacer disponibles las variables en la vista
        include __DIR__ . '/../views/admin/VagestionarEmpleados.php';
    }
    
    /**
     * Retorna empleados en formato JSON para AJAX/API
     */
    public function api() {
        header('Content-Type: application/json');
        $empleados = $this->userModel->getAllEmpleados();
        echo json_encode($empleados);
        exit();
    }
    
    /**
     * Crear nuevo empleado
     */
    public function crear() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->userModel->crearEmpleado($_POST);
                header('Location: index.php?ctrl=Cdgemp&action=index&msg=success');
                exit();
            } catch (Exception $e) {
                header('Location: index.php?ctrl=Cdgemp&action=index&error=' . urlencode($e->getMessage()));
                exit();
            }
        }
    }
}
?>