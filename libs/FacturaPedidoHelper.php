<?php
/**
 * Helper para generar y enviar facturas de pedidos.
 * Usado por admin (dashboard) y puede reutilizarse desde empleado/cliente.
 */

function factura_obtener_datos(PDO $db, $idPedido) {
    $stmt = $db->prepare("
        SELECT p.*, c.nombre as nombre_cliente, c.email, COALESCE(c.direccion, '') as naturaleza
        FROM ped p
        JOIN cli c ON p.cli_idcli = c.idcli
        WHERE p.idped = ?
    ");
    $stmt->execute([$idPedido]);
    $pedido = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$pedido) return null;

    $stmt = $db->prepare("SELECT * FROM pagos WHERE ped_idped = ? ORDER BY idpago DESC LIMIT 1");
    $stmt->execute([$idPedido]);
    $pago = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt = $db->prepare("
        SELECT dp.*, tf.nombre FROM detped dp
        JOIN tflor tf ON dp.idtflor = tf.idtflor
        WHERE dp.idped = ?
    ");
    $stmt->execute([$idPedido]);
    $detalles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($detalles as &$item) {
        if (!isset($item['subtotal'])) {
            $item['subtotal'] = ($item['cantidad'] ?? 0) * ($item['precio_unitario'] ?? 0);
        }
    }
    unset($item);

    return ['pedido' => $pedido, 'pago' => $pago, 'detalles' => $detalles];
}

function factura_generar_pdf_string($pedido, $pago, $detalles) {
    if (!function_exists('cliente_cargarFacturaPDF')) {
        require_once __DIR__ . '/../controllers/cliente.php';
    }
    cliente_cargarFacturaPDF();
    $pdf = new FacturaPDF();
    $pdf->AliasNbPages();
    $pdf->SetMargins(10, 30, 10);
    $pdf->SetAutoPageBreak(true, 25);
    $pdf->AddPage();
    $colorSecundario = [220, 230, 241];
    $colorTexto = [50, 50, 50];
    $pdf->SetTextColor($colorTexto[0], $colorTexto[1], $colorTexto[2]);
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 10, 'FACTURA #' . $pedido['numped'], 0, 1);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(0, 6, 'Fecha: ' . date('d/m/Y H:i', strtotime($pedido['fecha_pedido'])), 0, 1);
    $pdf->Ln(5);
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(95, 7, 'DATOS DEL CLIENTE', 0, 0);
    $pdf->Cell(95, 7, 'INFORMACION DE PAGO', 0, 1);
    $pdf->SetFont('Arial', '', 10);
    $nombreCliente = $pedido['nombre_cliente'] ?? 'Cliente';
    $emailCliente = $pedido['email'] ?? '';
    $naturalezaCliente = $pedido['naturaleza'] ?? '';
    $pdf->Cell(95, 6, $nombreCliente, 0, 0);
    $pdf->Cell(95, 6, 'Metodo: ' . ($pago ? $pago['metodo_pago'] : 'No registrado'), 0, 1);
    $pdf->Cell(95, 6, $emailCliente, 0, 0);
    $pdf->Cell(95, 6, 'Estado: ' . ($pago ? $pago['estado_pag'] : 'No registrado'), 0, 1);
    $pdf->Cell(95, 6, $naturalezaCliente, 0, 0);
    $pdf->Cell(95, 6, 'Fecha pago: ' . ($pago ? date('d/m/Y', strtotime($pago['fecha_pago'])) : 'N/A'), 0, 1);
    $pdf->Ln(10);
    $pdf->SetFillColor($colorSecundario[0], $colorSecundario[1], $colorSecundario[2]);
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(100, 8, 'DESCRIPCION', 1, 0, 'L', true);
    $pdf->Cell(30, 8, 'CANTIDAD', 1, 0, 'C', true);
    $pdf->Cell(30, 8, 'PRECIO UNIT.', 1, 0, 'R', true);
    $pdf->Cell(30, 8, 'SUBTOTAL', 1, 1, 'R', true);
    $pdf->SetFont('Arial', '', 10);
    $pdf->SetFillColor(255, 255, 255);
    foreach ($detalles as $item) {
        if ($pdf->GetY() > 240) {
            $pdf->AddPage();
            $pdf->SetFont('Arial', 'B', 11);
            $pdf->Cell(100, 8, 'DESCRIPCION', 'LRB', 0, 'L', true);
            $pdf->Cell(30, 8, 'CANTIDAD', 'LRB', 0, 'C', true);
            $pdf->Cell(30, 8, 'PRECIO UNIT.', 'LRB', 0, 'R', true);
            $pdf->Cell(30, 8, 'SUBTOTAL', 'LRB', 1, 'R', true);
            $pdf->SetFont('Arial', '', 10);
        }
        $sub = isset($item['subtotal']) ? $item['subtotal'] : ($item['cantidad'] * $item['precio_unitario']);
        $pdf->Cell(100, 7, $item['nombre'], 'LR', 0, 'L');
        $pdf->Cell(30, 7, $item['cantidad'], 'LR', 0, 'C');
        $pdf->Cell(30, 7, '$' . number_format($item['precio_unitario'], 2), 'LR', 0, 'R');
        $pdf->Cell(30, 7, '$' . number_format($sub, 2), 'LR', 1, 'R');
    }
    $pdf->Cell(190, 0, '', 'T');
    $pdf->Ln(5);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(160, 8, 'TOTAL:', 0, 0, 'R');
    $pdf->Cell(30, 8, '$' . number_format($pedido['monto_total'], 2), 0, 1, 'R');
    $pdf->SetY(-33);
    $pdf->SetFont('Arial', 'I', 8);
    $pdf->MultiCell(0, 4, "Términos y condiciones: El pago debe realizarse dentro de los 5 días hábiles.\nCualquier retraso puede incurrir en intereses moratorios.", 0, 'C');
    return $pdf->Output('S');
}

function factura_enviar_email($email_destino, $pedido, $pdf_content) {
    $autoload = __DIR__ . '/../vendor/autoload.php';
    if (file_exists($autoload)) require_once $autoload;
    if (file_exists(__DIR__ . '/../config/email_config.php')) require_once __DIR__ . '/../config/email_config.php';
    if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
        error_log("FacturaPedidoHelper: PHPMailer no disponible.");
        return false;
    }
    $host = defined('MAIL_HOST') ? MAIL_HOST : 'smtp.gmail.com';
    $port = defined('MAIL_PORT') ? (int) MAIL_PORT : 587;
    $user = defined('MAIL_USERNAME') ? MAIL_USERNAME : '';
    $pass = defined('MAIL_PASSWORD') ? MAIL_PASSWORD : '';
    $from = defined('MAIL_FROM_EMAIL') ? MAIL_FROM_EMAIL : $user;
    $fromName = defined('MAIL_FROM_NAME') ? MAIL_FROM_NAME : 'FloralTech';
    $enc = defined('MAIL_ENCRYPTION') ? strtolower(MAIL_ENCRYPTION) : 'tls';
    try {
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = $host;
        $mail->SMTPAuth = true;
        $mail->Username = $user;
        $mail->Password = $pass;
        $mail->SMTPSecure = ($enc === 'ssl') ? PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS : PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = $port;
        $mail->SMTPOptions = array('ssl' => array('verify_peer' => false, 'verify_peer_name' => false, 'allow_self_signed' => true));
        $mail->setFrom($from, $fromName);
        $mail->addAddress($email_destino, $pedido['nombre_cliente'] ?? 'Cliente');
        $mail->addReplyTo($from, $fromName);
        $mail->Subject = 'Factura #' . $pedido['numped'] . ' - FloralTech';
        $mail->isHTML(true);
        $mail->Body = '<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">'
            . '<h2 style="color: #4CAF50;">Factura #' . htmlspecialchars($pedido['numped']) . '</h2>'
            . '<p>Estimado/a ' . htmlspecialchars($pedido['nombre_cliente'] ?? 'Cliente') . ',</p>'
            . '<p>Adjunto encontrará la factura del pedido <strong>#' . htmlspecialchars($pedido['numped']) . '</strong>.</p>'
            . '<div style="background-color: #f9f9f9; padding: 15px; border-left: 4px solid #4CAF50; margin: 20px 0;">'
            . '<p><strong>Resumen del pedido:</strong></p>'
            . '<p>📅 Fecha: ' . date('d/m/Y H:i', strtotime($pedido['fecha_pedido'])) . '</p>'
            . '<p>💰 Total: <strong>$' . number_format($pedido['monto_total'], 2) . '</strong></p>'
            . '<p>📦 Estado: ' . htmlspecialchars($pedido['estado'] ?? '') . '</p></div>'
            . '<p>El archivo PDF adjunto contiene la factura completa con todos los detalles.</p>'
            . '<p>Gracias por su compra,<br><strong>El equipo de FloralTech</strong></p></div>';
        $mail->AltBody = 'Factura #' . $pedido['numped'] . ' - FloralTech. Adjunto encontrará la factura del pedido #' . $pedido['numped'];
        $mail->addStringAttachment($pdf_content, 'Factura_' . $pedido['numped'] . '.pdf');
        $mail->CharSet = 'UTF-8';
        $mail->SMTPDebug = 0;
        return $mail->send();
    } catch (Exception $e) {
        error_log("FacturaPedidoHelper enviar_email: " . $e->getMessage());
        return false;
    }
}
