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
        $permisos = $this->userModel->getPermisosEmpleados();
        $turnos = $this->userModel->getTurnosEmpleados();
        $vacaciones = $this->userModel->getVacacionesEmpleados();
        $mensaje = isset($_GET['msg']) && $_GET['msg'] === 'success' ? 'Empleado creado exitosamente.' : (isset($_GET['error']) ? $_GET['error'] : '');
        $tipo_mensaje = isset($_GET['msg']) && $_GET['msg'] === 'success' ? 'success' : (isset($_GET['error']) ? 'danger' : '');
        // Calcular permisos pendientes
        $permisos_pendientes = 0;
        if (is_array($permisos)) {
            foreach ($permisos as $permiso) {
                if (isset($permiso['estado']) && $permiso['estado'] === 'Pendiente') {
                    $permisos_pendientes++;
                }
            }
        }
        // Hacer disponibles las variables en la vista
        include __DIR__ . '/../views/admin/VagestionarEmpleados.php';
    }
    
    /**
     * Retorna empleados en formato JSON para AJAX/API
     */
    public function api() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        header('Content-Type: application/json');
        if (!isset($_SESSION['user'])) {
            echo json_encode(['error' => 'No autenticado']);
            exit;
        }
        // Parámetros de paginación y filtro
        $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $perPage = isset($_GET['perPage']) ? max(1, intval($_GET['perPage'])) : 10;
        $estado = isset($_GET['estado']) ? $_GET['estado'] : '';
        $tipo = isset($_GET['tipo']) ? intval($_GET['tipo']) : 0;
        $nombre = isset($_GET['nombre']) ? trim($_GET['nombre']) : '';

        $result = $this->userModel->getEmpleadosPaginados($page, $perPage, $estado, $tipo, $nombre);
        echo json_encode($result);
        exit;
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

    /**
     * Crear turno vía AJAX
     */
    public function crearTurnoAjax() {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $id = $this->userModel->crearTurno(
                    $_POST['idempleado'],
                    $_POST['fecha_inicio'],
                    $_POST['fecha_fin'],
                    $_POST['horario'],
                    $_POST['tipo_temporada'],
                    $_POST['turno'],
                    $_POST['observaciones']
                );
                echo json_encode(['success' => true, 'id' => $id]);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'Método no permitido']);
        }
        exit();
    }

    /**
     * Crear vacación vía AJAX
     */
    public function crearVacacionAjax() {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $id = $this->userModel->crearVacacion(
                    $_POST['id_empleado'],
                    $_POST['fecha_inicio'],
                    $_POST['fecha_fin'],
                    $_POST['motivo'],
                    $_POST['estado']
                );
                echo json_encode(['success' => true, 'id' => $id]);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'Método no permitido']);
        }
        exit();
    }
}
?>