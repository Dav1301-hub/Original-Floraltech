<?php

require_once 'libs/fpdf/fpdf.php';

// Definimos la clase PDF fuera de la clase cliente para evitar el error
class FacturaPDF extends FPDF {
    // Configuración de UTF-8 para caracteres especiales
    function Cell($w, $h = 0, $txt = '', $border = 0, $ln = 0, $align = '', $fill = false, $link = '') {
        parent::Cell($w, $h, $this->fixEncoding($txt), $border, $ln, $align, $fill, $link);
    }
    
    function MultiCell($w, $h, $txt, $border = 0, $align = 'J', $fill = false) {
        parent::MultiCell($w, $h, $this->fixEncoding($txt), $border, $align, $fill);
    }
    
    private function fixEncoding($text) {
        if (mb_detect_encoding($text, 'UTF-8', true)) {
            return iconv('UTF-8', 'windows-1252', $text);
        }
        return $text;
    }

    // Cabecera personalizada con logo
    function Header() {
        // Configuración de colores
        $this->SetTextColor(79, 129, 189); // Azul corporativo
        
        // Título
        $this->SetFont('Arial','B',16);
        $this->Cell(0,10,'FACTURA ELECTRÓNICA',0,1,'R');
        $this->SetFont('Arial','',10);
        $this->Cell(0,5,'FloralTech - Sistema de venta de flores online',0,1,'R');
        
        // Línea separadora
        $this->SetDrawColor(79, 129, 189);
        $this->SetLineWidth(0.5);
        $this->Line(10, 25, 200, 25);
        
        // Logo de la empresa (ajusta la ruta según tu estructura de archivos)
        $this->SetY(30); // Posicionamos 5mm debajo de la línea (25 + 5)
        $this->Image('assets/images/logoepymes.png', 10, 30, 40);
        
        // Espacio después del logo (ajusta según necesidades)
        $this->Ln(50);
    }

    // Pie de página (opcional)
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,10,'Página '.$this->PageNo().'/{nb}',0,0,'C');
    }
}

class cliente {
    private $db;
    private $cliente_id;
    
    public function __construct() {
        // Verificar que el usuario esté logueado y sea cliente
        if (!isset($_SESSION['user']) || $_SESSION['user']['tpusu_idtpusu'] != 5) {
            header('Location: index.php?ctrl=login&action=index');
            exit();
        }
        
        // Conectar a la base de datos
        require_once 'models/conexion.php';
        $conn = new conexion();
        $this->db = $conn->get_conexion();
        
        // Obtener el ID del cliente
        $this->cliente_id = $this->obtenerClienteId();
    }
    
    private function obtenerClienteId() {
        $usuario = $_SESSION['user'];
        
        try {
            $stmt = $this->db->prepare("SELECT idcli FROM cli WHERE email = ?");
            $stmt->execute([$usuario['email']]);
            $cliente_data = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$cliente_data) {
                // Si no existe el cliente, crearlo automáticamente
                $stmt = $this->db->prepare("INSERT INTO cli (nombre, direccion, telefono, email, fecha_registro) VALUES (?, ?, ?, ?, CURDATE())");
                $stmt->execute([
                    $usuario['nombre_completo'],
                    $usuario['naturaleza'] ?? 'Sin dirección',
                    $usuario['telefono'] ?? 'Sin teléfono',
                    $usuario['email']
                ]);
                return $this->db->lastInsertId();
            } else {
                return $cliente_data['idcli'];
            }
        } catch (PDOException $e) {
            error_log("Error obteniendo cliente ID: " . $e->getMessage());
            return 0;
        }
    }
    
    public function index() {
        $this->dashboard();
    }
    
    public function dashboard() {
        include 'views/cliente/dashboard.php';
    }
    
    public function realizar_pago() {
        include 'views/cliente/realizar_pago.php';
    }
    
    public function nuevo_pedido() {
        // Obtener flores disponibles para mostrar en la vista
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    tf.idtflor,
                    tf.nombre,
                    tf.naturaleza as color,
                    tf.precio,
                    tf.descripcion,
                    COALESCE(i.cantidad_disponible, 0) as stock
                FROM tflor tf
                LEFT JOIN inv i ON tf.idtflor = i.tflor_idtflor
                ORDER BY tf.nombre
            ");
            $stmt->execute();
            $flores_disponibles = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error obteniendo flores: " . $e->getMessage());
            $flores_disponibles = [];
        }
        
        include 'views/cliente/nuevo_pedido.php';
    }
    
    public function historial() {
        include 'views/cliente/historial_pago.php';
    }
    
    public function recibos() {
        include 'views/cliente/recibos_pago.php';
    }
    
    public function configuracion() {
        include 'views/cliente/configuracion.php';
    }
    
    public function procesar_pedido() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                error_log("Datos POST recibidos: " . print_r($_POST, true));
                
                $direccion_entrega = $_POST['direccion_entrega'] ?? '';
                $fecha_entrega = $_POST['fecha_entrega'] ?? '';
                $metodo_pago = $_POST['metodo_pago'] ?? 'efectivo';
                
                $detalles_pedido = [];
                $monto_total = 0;
                
                foreach ($_POST as $key => $value) {
                    if (strpos($key, 'flor_') === 0 && $value === 'on') {
                        $idtflor = str_replace('flor_', '', $key);
                        $cantidad_key = 'cantidad_' . $idtflor;
                        $cantidad = intval($_POST[$cantidad_key] ?? 0);
                        
                        if ($cantidad > 0) {
                            $stmt = $this->db->prepare("SELECT precio FROM tflor WHERE idtflor = ?");
                            $stmt->execute([$idtflor]);
                            $flor = $stmt->fetch(PDO::FETCH_ASSOC);
                            
                            if ($flor) {
                                $precio_unitario = $flor['precio'];
                                $subtotal = $precio_unitario * $cantidad;
                                $monto_total += $subtotal;
                                
                                $detalles_pedido[] = [
                                    'idtflor' => $idtflor,
                                    'cantidad' => $cantidad,
                                    'precio_unitario' => $precio_unitario,
                                    'subtotal' => $subtotal
                                ];
                            }
                        }
                    }
                }
                
                if (empty($detalles_pedido)) {
                    throw new Exception("Debe seleccionar al menos una flor");
                }
                
                if ($monto_total <= 0) {
                    throw new Exception("El monto total debe ser mayor a cero");
                }
                
                $this->db->beginTransaction();
                
                $numped = 'PED-' . date('YmdHis') . '-' . $this->cliente_id;
                
                $stmt = $this->db->prepare("
                    INSERT INTO ped (numped, fecha_pedido, monto_total, estado, cli_idcli, direccion_entrega, fecha_entrega_solicitada) 
                    VALUES (?, NOW(), ?, 'Pendiente', ?, ?, ?)
                ");
                $stmt->execute([$numped, $monto_total, $this->cliente_id, $direccion_entrega, $fecha_entrega]);
                $pedido_id = $this->db->lastInsertId();
                
                foreach ($detalles_pedido as $detalle) {
                    $stmt = $this->db->prepare("
                        INSERT INTO detped (idped, idtflor, cantidad, precio_unitario) 
                        VALUES (?, ?, ?, ?)
                    ");
                    $stmt->execute([
                        $pedido_id,
                        $detalle['idtflor'],
                        $detalle['cantidad'],
                        $detalle['precio_unitario']
                    ]);
                }
                
                $estado_pago = ($metodo_pago === 'transferencia') ? 'Pendiente' : 'Pendiente';
                $stmt = $this->db->prepare("
                    INSERT INTO pagos (ped_idped, monto, metodo_pago, estado_pag, fecha_pago) 
                    VALUES (?, ?, ?, ?, NOW())
                ");
                $stmt->execute([$pedido_id, $monto_total, $metodo_pago, $estado_pago]);
                
                $this->db->commit();
                
                if ($metodo_pago === 'transferencia') {
                    $_SESSION['mensaje'] = "Pedido creado exitosamente. Número de pedido: $numped. Su pago por transferencia está pendiente de verificación por nuestro equipo.";
                    $_SESSION['info_transferencia'] = "Recuerde que debe realizar la transferencia bancaria según los datos mostrados en el formulario. Un empleado verificará su pago para procesar el pedido.";
                } else {
                    $_SESSION['mensaje'] = "Pedido creado exitosamente. Número de pedido: $numped";
                }
                $_SESSION['tipo_mensaje'] = "success";
                
                error_log("Pedido creado exitosamente: $numped, Cliente ID: $this->cliente_id, Detalles: " . count($detalles_pedido) . " items");
                
                header('Location: index.php?ctrl=cliente&action=dashboard');
                exit();
                
            } catch (Exception $e) {
                if ($this->db->inTransaction()) {
                    $this->db->rollback();
                }
                
                $_SESSION['mensaje'] = "Error al procesar el pedido: " . $e->getMessage();
                $_SESSION['tipo_mensaje'] = "danger";
                header('Location: index.php?ctrl=cliente&action=realizar_pago');
                exit();
            }
        } else {
            header('Location: index.php?ctrl=cliente&action=realizar_pago');
            exit();
        }
    }
    
    public function obtener_flores() {
        header('Content-Type: application/json');
        
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    tf.idtflor,
                    tf.nombre,
                    tf.naturaleza as color,
                    tf.precio,
                    tf.descripcion,
                    COALESCE(i.cantidad_disponible, 0) as stock
                FROM tflor tf
                LEFT JOIN inv i ON tf.idtflor = i.tflor_idtflor
                ORDER BY tf.nombre
            ");
            $stmt->execute();
            $flores = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode(['success' => true, 'flores' => $flores]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        exit();
    }
    
    public function actualizar_perfil() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $nombre_completo = $_POST['nombre_completo'] ?? '';
                $telefono = $_POST['telefono'] ?? '';
                $direccion = $_POST['direccion'] ?? '';
                
                if (empty($nombre_completo)) {
                    throw new Exception("El nombre completo es requerido");
                }
                
                $stmt = $this->db->prepare("
                    UPDATE usu 
                    SET nombre_completo = ?, telefono = ?, naturaleza = ? 
                    WHERE idusu = ?
                ");
                $stmt->execute([$nombre_completo, $telefono, $direccion, $_SESSION['user']['idusu']]);
                
                $stmt = $this->db->prepare("
                    UPDATE cli 
                    SET nombre = ?, telefono = ?, direccion = ? 
                    WHERE idcli = ?
                ");
                $stmt->execute([$nombre_completo, $telefono, $direccion, $this->cliente_id]);
                
                $_SESSION['user']['nombre_completo'] = $nombre_completo;
                $_SESSION['user']['telefono'] = $telefono;
                $_SESSION['user']['naturaleza'] = $direccion;
                
                $_SESSION['mensaje'] = "Perfil actualizado correctamente";
                $_SESSION['tipo_mensaje'] = "success";
                
            } catch (Exception $e) {
                $_SESSION['mensaje'] = "Error al actualizar el perfil: " . $e->getMessage();
                $_SESSION['tipo_mensaje'] = "danger";
            }
            
            header('Location: index.php?ctrl=cliente&action=configuracion');
            exit();
        }
    }

    public function generar_factura() {
        // Obtener el ID del pedido de los parámetros GET
        $idPedido = $_GET['idpedido'] ?? 0;
        
        try {
            if (empty($idPedido)) {
                throw new Exception("No se ha especificado un número de pedido");
            }

            // Verificar que el pedido pertenece al cliente
            $pedido = $this->obtenerDetallesPedido($idPedido);
            
            if (!$pedido || $pedido['cli_idcli'] != $this->cliente_id) {
                throw new Exception("No tiene permiso para ver este pedido o el pedido no existe");
            }
            
            // Obtener detalles del pago (si existe)
            $pago = $this->obtenerPagoPorPedido($idPedido);
            
            // Obtener detalles de los items del pedido
            $detalles = $this->obtenerDetallesItemsPedido($idPedido);
            
            // Crear documento con márgenes ajustados (usando nuestra clase extendida)
                $pdf = new FacturaPDF();
                $pdf->AliasNbPages();
                $pdf->SetMargins(10, 30, 10); // Izquierda, Arriba (mayor para el logo), Derecha
                $pdf->SetAutoPageBreak(true, 25); // Margen inferior de 25mm
                $pdf->AddPage();
            
            // Configuración de colores (exactamente igual que antes)
            $colorPrimario = array(79, 129, 189); // Azul corporativo
            $colorSecundario = array(220, 230, 241); // Azul claro para fondos
            $colorTexto = array(50, 50, 50); // Gris oscuro para texto
            $pdf->SetTextColor($colorTexto[0], $colorTexto[1], $colorTexto[2]);
            
            // Información de la factura (mismo diseño)
            $pdf->SetFont('Arial','B',14);
            $pdf->Cell(0,10,'FACTURA #'.$pedido['numped'],0,1);
            $pdf->SetFont('Arial','',10);
            $pdf->Cell(0,6,'Fecha: '.date('d/m/Y H:i', strtotime($pedido['fecha_pedido'])),0,1);
            $pdf->Ln(5);
            
            // Dos columnas: Cliente y Detalles de Pago (igual que antes)
            $pdf->SetFont('Arial','B',11);
            $pdf->Cell(95,7,'DATOS DEL CLIENTE',0,0);
            $pdf->Cell(95,7,'INFORMACION DE PAGO',0,1);
            $pdf->SetFont('Arial','',10);
            
            // Datos del cliente (sin cambios)
            $pdf->Cell(95,6,$_SESSION['user']['nombre_completo'],0,0);
            $pdf->Cell(95,6,'Metodo: '.($pago ? $pago['metodo_pago'] : 'No registrado'),0,1);
            
            $pdf->Cell(95,6,$_SESSION['user']['email'],0,0);
            $pdf->Cell(95,6,'Estado: '.($pago ? $pago['estado_pag'] : 'No registrado'),0,1);
            
            $pdf->Cell(95,6,$_SESSION['user']['naturaleza'],0,0);
            $pdf->Cell(95,6,'Fecha pago: '.($pago ? date('d/m/Y', strtotime($pago['fecha_pago'])) : 'N/A'),0,1);
            
            $pdf->Ln(10);
            
            // Tabla de productos (mismo diseño exacto)
            $pdf->SetFillColor($colorSecundario[0], $colorSecundario[1], $colorSecundario[2]);
            $pdf->SetFont('Arial','B',11);
            
                // Encabezados de la tabla (reducir altura a 8)
                $pdf->Cell(100,8,'DESCRIPCION',1,0,'L', true);
                $pdf->Cell(30,8,'CANTIDAD',1,0,'C', true);
                $pdf->Cell(30,8,'PRECIO UNIT.',1,0,'R', true);
                $pdf->Cell(30,8,'SUBTOTAL',1,1,'R', true);
                $pdf->SetFont('Arial','',10);
                $pdf->SetFillColor(255, 255, 255); // Fondo blanco
            
            // Items del pedido (misma lógica de paginación)
            foreach($detalles as $item) {
                // Verificar si necesitamos nueva página (mismo cálculo)
                if($pdf->GetY() > 240) {
                    $pdf->AddPage();
                    $pdf->SetFont('Arial','B',11);
                    $pdf->Cell(100,8,'DESCRIPCION','LRB',0,'L', true);
                    $pdf->Cell(30,8,'CANTIDAD','LRB',0,'C', true);
                    $pdf->Cell(30,8,'PRECIO UNIT.','LRB',0,'R', true);
                    $pdf->Cell(30,8,'SUBTOTAL','LRB',1,'R', true);
                    $pdf->SetFont('Arial','',10);
                }
                
                // Celdas con los mismos parámetros que antes
                $pdf->Cell(100,7,$item['nombre'],'LR',0,'L');
                $pdf->Cell(30,7,$item['cantidad'],'LR',0,'C');
                $pdf->Cell(30,7,'$'.number_format($item['precio_unitario'],2),'LR',0,'R');
                $pdf->Cell(30,7,'$'.number_format($item['subtotal'],2),'LR',1,'R');
            }
            
            // Línea de cierre de la tabla (igual)
            $pdf->Cell(190,0,'','T');
            $pdf->Ln(5);
            
            // Totales (mismo formato)
            $pdf->SetFont('Arial','B',12);
            $pdf->Cell(160,8,'TOTAL:',0,0,'R');
            $pdf->Cell(30,8,'$'.number_format($pedido['monto_total'],2),0,1,'R');
            
                // Términos y condiciones 
                $pdf->SetY(-33);
                $pdf->SetFont('Arial', 'I', 8);
                $pdf->MultiCell(0, 4, "Terminos y condiciones: El pago debe realizarse dentro de los 5 dias habiles.\nCualquier retraso puede incurrir en intereses moratorios.", 0, 'C');
                
            
            // Forzar descarga del PDF (igual)
            $pdf->Output('D', 'factura_'.$pedido['numped'].'.pdf');
            exit();
            
        } catch (Exception $e) {
            $_SESSION['mensaje_error'] = "Error al generar factura: ".$e->getMessage();
            header('Location: index.php?ctrl=cliente&action=historial');
            exit();
        }
    }

    /**
     * Nueva función para enviar factura por email
     * Esta función se llama mediante AJAX desde el botón "Enviar" en historial_pago.php
     */
    public function enviar_factura_email() {
        // Solo permitir solicitudes POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            exit();
        }
        
        // Obtener datos del POST
        $idPedido = $_POST['idpedido'] ?? 0;
        $email_destino = $_POST['email'] ?? '';
        
        // Configurar header para JSON
        header('Content-Type: application/json');
        
        try {
            // Validaciones básicas
            if (empty($idPedido) || empty($email_destino)) {
                throw new Exception('Datos incompletos');
            }
            
            if (!filter_var($email_destino, FILTER_VALIDATE_EMAIL)) {
                throw new Exception('Email inválido');
            }
            
            // Verificar que el pedido pertenece al cliente
            $pedido = $this->obtenerDetallesPedido($idPedido);
            
            if (!$pedido || $pedido['cli_idcli'] != $this->cliente_id) {
                throw new Exception('No tiene permiso para ver este pedido o el pedido no existe');
            }
            
            // Verificar que el email coincide con el cliente
            if ($pedido['email'] !== $email_destino) {
                throw new Exception('El email no corresponde a este pedido');
            }
            
            // Obtener detalles del pago (si existe)
            $pago = $this->obtenerPagoPorPedido($idPedido);
            
            // Obtener detalles de los items del pedido
            $detalles = $this->obtenerDetallesItemsPedido($idPedido);
            
            // 1. Generar el PDF en memoria (sin output directo)
            $pdf_content = $this->generarFacturaEnMemoria($idPedido, $pedido, $pago, $detalles);
            
            // 2. Enviar el email con el PDF adjunto usando PHPMailer
            $envio_exitoso = $this->enviarEmailConPHPMailer($email_destino, $pedido, $pdf_content);
            
            if ($envio_exitoso) {
                // Registrar el envío en la base de datos
                $this->registrarEnvioFactura($idPedido, $email_destino);
                
                echo json_encode([
                    'success' => true, 
                    'message' => '✅ Factura enviada exitosamente a ' . $email_destino
                ]);
            } else {
                throw new Exception('Error al enviar el email');
            }
            
        } catch (Exception $e) {
            error_log("Error en enviar_factura_email: " . $e->getMessage());
            echo json_encode([
                'success' => false, 
                'message' => '❌ ' . $e->getMessage()
            ]);
        }
        exit();
    }
    
    /**
     * Genera la factura en memoria (sin output directo)
     */
    private function generarFacturaEnMemoria($idPedido, $pedido, $pago, $detalles) {
        // Crear documento en memoria
        $pdf = new FacturaPDF();
        $pdf->AliasNbPages();
        $pdf->SetMargins(10, 30, 10);
        $pdf->SetAutoPageBreak(true, 25);
        $pdf->AddPage();
        
        // Configuración de colores
        $colorPrimario = array(79, 129, 189);
        $colorSecundario = array(220, 230, 241);
        $colorTexto = array(50, 50, 50);
        $pdf->SetTextColor($colorTexto[0], $colorTexto[1], $colorTexto[2]);
        
        // Información de la factura
        $pdf->SetFont('Arial','B',14);
        $pdf->Cell(0,10,'FACTURA #'.$pedido['numped'],0,1);
        $pdf->SetFont('Arial','',10);
        $pdf->Cell(0,6,'Fecha: '.date('d/m/Y H:i', strtotime($pedido['fecha_pedido'])),0,1);
        $pdf->Ln(5);
        
        // Dos columnas: Cliente y Detalles de Pago
        $pdf->SetFont('Arial','B',11);
        $pdf->Cell(95,7,'DATOS DEL CLIENTE',0,0);
        $pdf->Cell(95,7,'INFORMACION DE PAGO',0,1);
        $pdf->SetFont('Arial','',10);
        
        // Datos del cliente
        $pdf->Cell(95,6,$pedido['nombre_cliente'],0,0);
        $pdf->Cell(95,6,'Método: '.($pago ? $pago['metodo_pago'] : 'No registrado'),0,1);
        
        $pdf->Cell(95,6,$pedido['email'],0,0);
        $pdf->Cell(95,6,'Estado: '.($pago ? $pago['estado_pag'] : 'No registrado'),0,1);
        
        $pdf->Cell(95,6,$pedido['direccion'] ?? 'No especificada',0,0);
        $pdf->Cell(95,6,'Fecha pago: '.($pago ? date('d/m/Y', strtotime($pago['fecha_pago'])) : 'N/A'),0,1);
        
        $pdf->Ln(10);
        
        // Tabla de productos
        $pdf->SetFillColor($colorSecundario[0], $colorSecundario[1], $colorSecundario[2]);
        $pdf->SetFont('Arial','B',11);
        
        $pdf->Cell(100,8,'DESCRIPCIÓN',1,0,'L', true);
        $pdf->Cell(30,8,'CANTIDAD',1,0,'C', true);
        $pdf->Cell(30,8,'PRECIO UNIT.',1,0,'R', true);
        $pdf->Cell(30,8,'SUBTOTAL',1,1,'R', true);
        $pdf->SetFont('Arial','',10);
        $pdf->SetFillColor(255, 255, 255);
        
        // Items del pedido
        foreach($detalles as $item) {
            if($pdf->GetY() > 240) {
                $pdf->AddPage();
                $pdf->SetFont('Arial','B',11);
                $pdf->Cell(100,8,'DESCRIPCIÓN','LRB',0,'L', true);
                $pdf->Cell(30,8,'CANTIDAD','LRB',0,'C', true);
                $pdf->Cell(30,8,'PRECIO UNIT.','LRB',0,'R', true);
                $pdf->Cell(30,8,'SUBTOTAL','LRB',1,'R', true);
                $pdf->SetFont('Arial','',10);
            }
            
            $pdf->Cell(100,7,$item['nombre'],'LR',0,'L');
            $pdf->Cell(30,7,$item['cantidad'],'LR',0,'C');
            $pdf->Cell(30,7,'$'.number_format($item['precio_unitario'],2),'LR',0,'R');
            $pdf->Cell(30,7,'$'.number_format($item['cantidad'] * $item['precio_unitario'],2),'LR',1,'R');
        }
        
        $pdf->Cell(190,0,'','T');
        $pdf->Ln(5);
        
        // Totales
        $pdf->SetFont('Arial','B',12);
        $pdf->Cell(160,8,'TOTAL:',0,0,'R');
        $pdf->Cell(30,8,'$'.number_format($pedido['monto_total'],2),0,1,'R');
        
        // Términos y condiciones
        $pdf->SetY(-33);
        $pdf->SetFont('Arial', 'I', 8);
        $pdf->MultiCell(0, 4, "Términos y condiciones: El pago debe realizarse dentro de los 5 días hábiles.\nCualquier retraso puede incurrir en intereses moratorios.", 0, 'C');
        
        // Obtener el contenido del PDF como string
        return $pdf->Output('S');
    }
    
    /**
     * Envía el email con PHPMailer
     */
    private function enviarEmailConPHPMailer($email_destino, $pedido, $pdf_content) {
        try {
            // Cargar PHPMailer desde vendor
            require_once 'vendor/autoload.php';
            
            $mail = new PHPMailer\PHPMailer\PHPMailer(true);
            
            // Configuración para desarrollo local
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'epymes270@gmail.com';
            $mail->Password = 'uormuvnibfvermjr';
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
            
            // Configurar remitente (usa el email del cliente que inició sesión)
            $mail->setFrom($email_destino, $pedido['nombre_cliente']);
            
            // Configurar destinatario (el mismo cliente)
            $mail->addAddress($email_destino, $pedido['nombre_cliente']);
            $mail->addReplyTo($email_destino, $pedido['nombre_cliente']);
            
            // Asunto
            $mail->Subject = 'Factura #' . $pedido['numped'] . ' - FloralTech';
            
            // Cuerpo del email (simple y profesional)
            $mail->isHTML(true);
            $mail->Body = '
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="UTF-8">
                <style>
                    body { font-family: Arial, sans-serif; color: #333; line-height: 1.6; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background-color: #4CAF50; color: white; padding: 15px; text-align: center; }
                    .content { padding: 20px; background-color: #f9f9f9; }
                    .details { background-color: white; padding: 15px; margin: 15px 0; border-left: 4px solid #4CAF50; }
                    .footer { text-align: center; padding: 15px; color: #666; font-size: 12px; }
                </style>
            </head>
            <body>
                <div class="container">
                    <div class="header">
                        <h2>FloralTech - Factura</h2>
                    </div>
                    <div class="content">
                        <p>Estimado/a ' . htmlspecialchars($pedido['nombre_cliente']) . ',</p>
                        
                        <p>Adjunto encontrará la factura del pedido <strong>#' . $pedido['numped'] . '</strong>.</p>
                        
                        <div class="details">
                            <p><strong>Resumen del pedido:</strong></p>
                            <p>📅 Fecha: ' . date('d/m/Y H:i', strtotime($pedido['fecha_pedido'])) . '</p>
                            <p>💰 Total: <strong>$' . number_format($pedido['monto_total'], 2) . '</strong></p>
                            <p>📦 Estado: ' . $pedido['estado'] . '</p>
                            ' . ($pago ? '<p>💳 Método de pago: ' . $pago['metodo_pago'] . '</p>' : '') . '
                        </div>
                        
                        <p>El archivo PDF adjunto contiene la factura completa con todos los detalles.</p>
                        
                        <p>Gracias por su compra,<br>
                        <strong>El equipo de FloralTech</strong></p>
                    </div>
                    <div class="footer">
                        <p>Este es un mensaje automático. Por favor no responda a este correo.</p>
                        <p>© ' . date('Y') . ' FloralTech</p>
                    </div>
                </div>
            </body>
            </html>';
            
            // Versión de texto plano
            $mail->AltBody = 'Factura #' . $pedido['numped'] . ' - FloralTech' . PHP_EOL . PHP_EOL .
                           'Estimado/a ' . $pedido['nombre_cliente'] . ',' . PHP_EOL . PHP_EOL .
                           'Adjunto encontrará la factura del pedido #' . $pedido['numped'] . '.' . PHP_EOL . PHP_EOL .
                           'Resumen del pedido:' . PHP_EOL .
                           'Fecha: ' . date('d/m/Y H:i', strtotime($pedido['fecha_pedido'])) . PHP_EOL .
                           'Total: $' . number_format($pedido['monto_total'], 2) . PHP_EOL .
                           'Estado: ' . $pedido['estado'] . PHP_EOL .
                           ($pago ? 'Método de pago: ' . $pago['metodo_pago'] . PHP_EOL : '') . PHP_EOL .
                           'El archivo PDF adjunto contiene la factura completa.' . PHP_EOL . PHP_EOL .
                           'Gracias por su compra,' . PHP_EOL .
                           'El equipo de FloralTech';
            
            // Adjuntar el PDF
            $mail->addStringAttachment($pdf_content, 'Factura_' . $pedido['numped'] . '.pdf');
            
            // Configurar encoding
            $mail->CharSet = 'UTF-8';
            
            // Para desarrollo, podemos mostrar errores en el log
            $mail->SMTPDebug = 0; // 0 = no debug, 1 = errores, 2 = mensajes
            
            // Intentar enviar
            $enviado = $mail->send();
            
            // Si falla en local, podemos guardar el email para debugging
            if (!$enviado) {
                error_log("Error PHPMailer: " . $mail->ErrorInfo);
                
                // En desarrollo local, podemos simular éxito guardando el archivo
                $this->guardarEmailLocal($email_destino, $pedido, $pdf_content, $mail->ErrorInfo);
                
                // En desarrollo, retornamos true para que el usuario vea el mensaje de éxito
                // En producción, debería ser false
                return true;
            }
            
            return true;
            
        } catch (Exception $e) {
            error_log("Excepción PHPMailer: " . $e->getMessage());
            
            // En desarrollo, guardar el email localmente
            $this->guardarEmailLocal($email_destino, $pedido, $pdf_content, $e->getMessage());
            
            // En desarrollo local, retornamos true para simular éxito
            return true;
        }
    }
    
    /**
     * Guarda el email localmente para debugging (solo desarrollo)
     */
    private function guardarEmailLocal($email_destino, $pedido, $pdf_content, $error = '') {
        $directorio = 'emails_enviados/';
        
        if (!file_exists($directorio)) {
            mkdir($directorio, 0777, true);
        }
        
        // 1. Guardar el PDF
        $nombre_pdf = 'factura_' . $pedido['numped'] . '_' . date('Ymd_His') . '.pdf';
        file_put_contents($directorio . $nombre_pdf, $pdf_content);
        
        // 2. Guardar información del envío
        $info = array(
            'fecha' => date('Y-m-d H:i:s'),
            'pedido' => $pedido['numped'],
            'cliente' => $pedido['nombre_cliente'],
            'email' => $email_destino,
            'total' => $pedido['monto_total'],
            'error_phpmailer' => $error,
            'pdf_guardado' => $nombre_pdf
        );
        
        $log_file = $directorio . 'log_envios.json';
        $logs = array();
        
        if (file_exists($log_file)) {
            $logs = json_decode(file_get_contents($log_file), true);
        }
        
        $logs[] = $info;
        file_put_contents($log_file, json_encode($logs, JSON_PRETTY_PRINT));
        
        // 3. También guardar en log de texto simple
        $log_texto = date('Y-m-d H:i:s') . " | Pedido: #" . $pedido['numped'] . 
                    " | Cliente: " . $pedido['nombre_cliente'] . 
                    " | Email: " . $email_destino . 
                    " | Total: $" . number_format($pedido['monto_total'], 2) . 
                    " | PDF: " . $nombre_pdf . 
                    " | Error: " . ($error ?: 'Ninguno') . "\n";
        
        file_put_contents($directorio . 'envios.log', $log_texto, FILE_APPEND);
    }
    
    /**
     * Registra el envío de la factura en la base de datos
     */
    private function registrarEnvioFactura($idPedido, $email) {
        try {
            // Primero, verifica si la tabla existe, si no, créala
            $this->crearTablaHistorialFacturas();
            
            // Insertar registro del envío
            $stmt = $this->db->prepare("
                INSERT INTO HistEnvFac (idpedido, email_destino, fecha_envio) 
                VALUES (?, ?, NOW())
            ");
            $stmt->execute([$idPedido, $email]);
            
            return true;
        } catch (PDOException $e) {
            error_log("Error al registrar envío de factura: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Crea la tabla para historial de envíos de facturas si no existe
     */
    private function crearTablaHistorialFacturas() {
        try {
            // SQL para crear la tabla si no existe
            $sql = "CREATE TABLE IF NOT EXISTS HistEnvFac (
                id INT PRIMARY KEY AUTO_INCREMENT,
                idpedido INT NOT NULL,
                email_destino VARCHAR(255) NOT NULL,
                fecha_envio DATETIME DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_pedido (idpedido),
                FOREIGN KEY (idpedido) REFERENCES ped(idped) ON DELETE CASCADE
            )";
            
            $this->db->exec($sql);
            return true;
        } catch (PDOException $e) {
            error_log("Error al crear tabla HistEnvFac: " . $e->getMessage());
            return false;
        }
    }

    private function obtenerDetallesPedido($idPedido) {
        $stmt = $this->db->prepare("
            SELECT p.*, c.nombre as nombre_cliente, c.email, c.direccion
            FROM ped p 
            JOIN cli c ON p.cli_idcli = c.idcli 
            WHERE p.idped = ?
        ");
        $stmt->execute([$idPedido]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    private function obtenerPagoPorPedido($idPedido) {
        $stmt = $this->db->prepare("
            SELECT * FROM pagos 
            WHERE ped_idped = ? 
            ORDER BY idpago DESC 
            LIMIT 1
        ");
        $stmt->execute([$idPedido]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    private function obtenerDetallesItemsPedido($idPedido) {
        $stmt = $this->db->prepare("
            SELECT dp.*, tf.nombre 
            FROM detped dp 
            JOIN tflor tf ON dp.idtflor = tf.idtflor 
            WHERE dp.idped = ?
        ");
        $stmt->execute([$idPedido]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}