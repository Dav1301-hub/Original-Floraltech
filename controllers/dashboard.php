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
    $pageTitle = "Dashboard - Administrador";
    $totalUsuarios = $this->userModel->getTotalUsuarios();
    require_once 'views/admin/VadashboardPrincipal.php';
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

    /**
     * Descargar factura PDF de un pedido (solo admin).
     */
    public function generar_factura() {
        if (!isset($_SESSION['user']) || (int)($_SESSION['user']['tpusu_idtpusu'] ?? 0) !== 1) {
            header('Location: index.php?ctrl=login&action=index');
            exit;
        }
        $idPedido = (int)($_GET['idpedido'] ?? 0);
        if ($idPedido <= 0) {
            $_SESSION['mensaje'] = 'ID de pedido no válido';
            header('Location: index.php?ctrl=dashboard&action=admin&page=pedidos');
            exit;
        }
        require_once __DIR__ . '/../models/conexion.php';
        require_once __DIR__ . '/../libs/FacturaPedidoHelper.php';
        $conn = (new conexion())->get_conexion();
        $datos = factura_obtener_datos($conn, $idPedido);
        if (!$datos) {
            $_SESSION['mensaje'] = 'Pedido no encontrado';
            header('Location: index.php?ctrl=dashboard&action=admin&page=pedidos');
            exit;
        }
        $pdf_content = factura_generar_pdf_string($datos['pedido'], $datos['pago'], $datos['detalles']);
        if (!$pdf_content) {
            $_SESSION['mensaje'] = 'Error al generar la factura';
            header('Location: index.php?ctrl=dashboard&action=admin&page=pedidos');
            exit;
        }
        $nombre = 'factura_' . $datos['pedido']['numped'] . '.pdf';
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $nombre . '"');
        header('Content-Length: ' . strlen($pdf_content));
        echo $pdf_content;
        exit;
    }

    /**
     * Enviar factura por email (solo admin). Espera POST idpedido y email. Responde JSON si es AJAX.
     */
    public function enviar_factura_email() {
        if (!isset($_SESSION['user']) || (int)($_SESSION['user']['tpusu_idtpusu'] ?? 0) !== 1) {
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'No autorizado']);
                exit;
            }
            header('Location: index.php?ctrl=login&action=index');
            exit;
        }
        $idPedido = (int)($_POST['idpedido'] ?? $_GET['idpedido'] ?? 0);
        $email = trim($_POST['email'] ?? '');
        if ($idPedido <= 0 || $email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $msg = 'Datos inválidos (pedido y email requeridos).';
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => $msg]);
                exit;
            }
            $_SESSION['mensaje'] = $msg;
            header('Location: index.php?ctrl=dashboard&action=admin&page=pedidos');
            exit;
        }
        require_once __DIR__ . '/../models/conexion.php';
        require_once __DIR__ . '/../libs/FacturaPedidoHelper.php';
        $conn = (new conexion())->get_conexion();
        $datos = factura_obtener_datos($conn, $idPedido);
        if (!$datos) {
            $msg = 'Pedido no encontrado.';
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => $msg]);
                exit;
            }
            $_SESSION['mensaje'] = $msg;
            header('Location: index.php?ctrl=dashboard&action=admin&page=pedidos');
            exit;
        }
        $pdf_content = factura_generar_pdf_string($datos['pedido'], $datos['pago'], $datos['detalles']);
        if (!$pdf_content) {
            $msg = 'Error al generar el PDF de la factura.';
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => $msg]);
                exit;
            }
            $_SESSION['mensaje'] = $msg;
            header('Location: index.php?ctrl=dashboard&action=admin&page=pedidos');
            exit;
        }
        $enviado = factura_enviar_email($email, $datos['pedido'], $pdf_content);
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => $enviado,
                'message' => $enviado ? 'Factura enviada correctamente a ' . $email : 'No se pudo enviar el correo. Revise la configuración de email.'
            ]);
            exit;
        }
        $_SESSION['mensaje'] = $enviado ? 'Factura enviada a ' . $email : 'No se pudo enviar el correo.';
        header('Location: index.php?ctrl=dashboard&action=admin&page=pedidos');
        exit;
    }
}