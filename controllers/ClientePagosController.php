<?php
class ClientePagosController {
    private $model;
    private $idCliente;

    public function __construct($model, $idCliente) {
        $this->model = $model;
        $this->idCliente = $idCliente;
    }

    public function realizarPago($idPedido) {
        $pedido = $this->model->obtenerDetallesPedido($idPedido);
        
        if (!$pedido || $pedido['cli_idcli'] != $this->idCliente) {
            header("Location: /acceso-denegado");
            exit();
        }

        $error = null; // Inicializar variable de error
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Procesar el pago con QR
            $datosPago = [
                'metodo' => 'QR',
                'monto' => $pedido['monto_total'],
                'pedido' => $idPedido,
                'transaccion' => 'QR-' . uniqid()
            ];

            if ($this->model->registrarPago($datosPago)) {
                $_SESSION['mensaje'] = "Pago realizado con éxito";
                header("Location: /cliente/pagos/historial");
                exit();
            } else {
                $error = "Error al procesar el pago";
            }
        }

        // Pasar las variables a la vista
        $datosVista = [
            'pedido' => $pedido,
            'error' => $error
        ];
        
        extract($datosVista);
        include 'view/cliente/realizar_pago.php';
    }

    public function historialPagos() {
        $pagos = $this->model->obtenerPagosPorCliente($this->idCliente);
        
        // Pasar los datos a la vista
        $datosVista = [
            'pagos' => $pagos
        ];
        
        extract($datosVista);
        include 'view/cliente/recibos_pago.php';
    }

    public function descargarRecibo($idPago) {
        $pago = $this->model->obtenerPagoPorId($idPago);
        
        if (!$pago) {
            header("Location: /acceso-denegado");
            exit();
        }

        $pedido = $this->model->obtenerDetallesPedido($pago['ped_idped']);
        
        // Verificar que el pedido pertenece al cliente
        if (!$pedido || $pedido['cli_idcli'] != $this->idCliente) {
            header("Location: /acceso-denegado");
            exit();
        }

        // Generar PDF de la factura
        $this->generarFacturaPDF($pedido, $pago);
    }

    public function recibos() {
        // Verificar sesión
        if (!isset($_SESSION['user']) || $_SESSION['user']['tpusu_idtpusu'] != 5) {
            header('Location: index.php?ctrl=login&action=index');
            exit();
        }

        // Obtener conexión
        require_once 'models/conexion.php';
        $conn = new Conexion();
        $db = $conn->get_conexion();

        // Obtener ID cliente
        $usuario = $_SESSION['user'];
        try {
            $stmt = $db->prepare("SELECT idcli FROM cli WHERE email = ?");
            $stmt->execute([$usuario['email']]);
            $cliente_data = $stmt->fetch(PDO::FETCH_ASSOC);
            $cliente_id = $cliente_data['idcli'] ?? 0;

            // Consulta para pedidos con pagos completados
            $query = "
                SELECT 
                    p.*,
                    DATE_FORMAT(p.fecha_pedido, '%d/%m/%Y %H:%i') as fecha_formato,
                    pg.estado_pag,
                    pg.metodo_pago,
                    pg.fecha_pago,
                    pg.idpago,
                    pg.transaccion_id,
                    COUNT(dp.iddetped) as total_items,
                    GROUP_CONCAT(DISTINCT CONCAT(tf.nombre, ' (', dp.cantidad, ')') SEPARATOR ', ') as items_detalle
                FROM ped p 
                JOIN pagos pg ON p.idped = pg.ped_idped
                LEFT JOIN detped dp ON p.idped = dp.idped
                LEFT JOIN tflor tf ON dp.idtflor = tf.idtflor
                WHERE p.cli_idcli = ? AND pg.estado_pag = 'Completado'
                GROUP BY p.idped
                ORDER BY p.fecha_pedido DESC
            ";
            
            $stmt = $db->prepare($query);
            $stmt->execute([$cliente_id]);
            $pedidos_con_recibo = $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Error en recibos(): " . $e->getMessage());
            $pedidos_con_recibo = [];
        }

        // Pasar variables a la vista
        $datos_vista = [
            'pedidos_con_recibo' => $pedidos_con_recibo,
            'usuario' => $usuario
        ];
        
        extract($datos_vista);
        require 'views/cliente/recibos_pago.php';
    }

    /**
     * Genera un PDF con la factura del pedido
     */
    private function generarFacturaPDF($pedido, $pago) {
        // Obtener detalles del pedido
        $detalles = $this->model->obtenerDetallesItemsPedido($pedido['idped']);
        
        require_once 'libs/fpdf/fpdf.php';
        
        $pdf = new FPDF();
        $pdf->AddPage();
        
        // Encabezado de la factura
        $pdf->SetFont('Arial','B',16);
        $pdf->Cell(0,10,'FloralTech - Factura Electronica',0,1,'C');
        $pdf->Ln(10);
        
        // Información del cliente
        $pdf->SetFont('Arial','',12);
        $pdf->Cell(0,10,'Cliente: '.$pedido['nombre_cliente'],0,1);
        $pdf->Cell(0,10,'Direccion: '.$pedido['direccion'],0,1);
        $pdf->Cell(0,10,'Email: '.$pedido['email'],0,1);
        $pdf->Ln(10);
        
        // Detalles de la factura
        $pdf->Cell(0,10,'Factura #'.$pedido['numped'],0,1);
        $pdf->Cell(0,10,'Fecha: '.date('d/m/Y H:i', strtotime($pedido['fecha_pedido'])),0,1);
        $pdf->Cell(0,10,'Transaccion: '.$pago['transaccion_id'],0,1);
        $pdf->Ln(10);
        
        // Tabla de productos
        $pdf->SetFont('Arial','B',12);
        $pdf->Cell(100,10,'Producto',1,0);
        $pdf->Cell(30,10,'Cantidad',1,0,'C');
        $pdf->Cell(30,10,'Precio',1,0,'R');
        $pdf->Cell(30,10,'Total',1,1,'R');
        
        $pdf->SetFont('Arial','',10);
        foreach($detalles as $item) {
            $total = $item['cantidad'] * $item['precio'];
            $pdf->Cell(100,10,$item['nombre'],1,0);
            $pdf->Cell(30,10,$item['cantidad'],1,0,'C');
            $pdf->Cell(30,10,'$'.number_format($item['precio'],2),1,0,'R');
            $pdf->Cell(30,10,'$'.number_format($total,2),1,1,'R');
        }
        
        // Totales
        $pdf->SetFont('Arial','B',12);
        $pdf->Cell(160,10,'Subtotal:',1,0,'R');
        $pdf->Cell(30,10,'$'.number_format($pedido['monto_total'],2),1,1,'R');
        
        // Método de pago
        $pdf->Ln(10);
        $pdf->Cell(0,10,'Metodo de pago: '.$pago['metodo_pago'],0,1);
        
        // Pie de página
        $pdf->SetY(-15);
        $pdf->SetFont('Arial','I',8);
        $pdf->Cell(0,10,'Gracias por su compra - FloralTech',0,0,'C');
        
        // Forzar descarga del PDF
        $pdf->Output('D', 'factura_'.$pedido['numped'].'.pdf');
        exit();
    }

    private function obtenerIdCliente() {
        if (!isset($_SESSION['user']) || !isset($_SESSION['user']['id_cliente'])) {
            throw new Exception('Usuario no autenticado o información incompleta');
        }
        
        return (int)$_SESSION['user']['id_cliente'];
    }
}
?>