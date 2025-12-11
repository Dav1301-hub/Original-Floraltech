<?php
/**
 * Clase Mailer para envío de emails
 * Usa función mail() de PHP con configuración de email_config.php
 */

class Mailer {
    private $from_email;
    private $from_name;
    
    public function __construct() {
        // Cargar configuración
        if (file_exists(__DIR__ . '/../config/email_config.php')) {
            if (!defined('MAIL_FROM_EMAIL')) {
                require_once __DIR__ . '/../config/email_config.php';
            }
            $this->from_email = MAIL_FROM_EMAIL ?? 'epymes270@gmail.com';
            $this->from_name = MAIL_FROM_NAME ?? 'FloralTech Soporte';
        } else {
            $this->from_email = 'epymes270@gmail.com';
            $this->from_name = 'FloralTech Soporte';
        }
    }
    
    /**
     * Enviar email usando la función mail() de PHP
     */
    public function sendEmail($to, $subject, $body, $isHtml = false) {
        try {
            $headers = "From: {$this->from_name} <{$this->from_email}>\r\n";
            $headers .= "Reply-To: {$this->from_email}\r\n";
            
            if ($isHtml) {
                $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
            } else {
                $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
            }
            
            $headers .= "X-Mailer: FloralTech\r\n";
            
            // Intentar enviar el email
            $result = mail($to, $subject, $body, $headers);
            
            if (!$result) {
                throw new Exception("Error al enviar email a través de mail()");
            }
            
            return true;
            
        } catch (Exception $e) {
            error_log("Error enviando email: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Enviar email HTML formateado para tickets de soporte
     */
    public function sendSupportTicketEmail($to, $ticketId, $admin, $asunto, $descripcion, $archivo = null) {
        $subject = "[TICKET #$ticketId] " . htmlspecialchars($asunto);
        
        $html = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(120deg, #0d6efd 0%, #5b21b6 60%, #1e1b4b 100%); color: white; padding: 20px; border-radius: 8px 8px 0 0; }
                .body { background: #f9f9f9; padding: 20px; border: 1px solid #ddd; border-radius: 0 0 8px 8px; }
                .field { margin: 15px 0; }
                .label { font-weight: bold; color: #0d6efd; font-size: 12px; text-transform: uppercase; }
                .value { margin-top: 5px; padding: 10px; background: white; border-left: 3px solid #0d6efd; }
                .footer { margin-top: 20px; padding-top: 20px; border-top: 1px solid #ddd; font-size: 12px; color: #999; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2 style='margin: 0;'>Nuevo Ticket de Soporte</h2>
                    <p style='margin: 5px 0 0 0;'>Ticket #$ticketId</p>
                </div>
                <div class='body'>
                    <div class='field'>
                        <div class='label'>Administrador</div>
                        <div class='value'>" . htmlspecialchars($admin['nombre_completo']) . " (" . htmlspecialchars($admin['email']) . ")</div>
                    </div>
                    
                    <div class='field'>
                        <div class='label'>Asunto</div>
                        <div class='value'>" . htmlspecialchars($asunto) . "</div>
                    </div>
                    
                    <div class='field'>
                        <div class='label'>Descripción</div>
                        <div class='value'>" . nl2br(htmlspecialchars($descripcion)) . "</div>
                    </div>
                    
                    " . (!empty($archivo) ? "
                    <div class='field'>
                        <div class='label'>Archivo Adjunto</div>
                        <div class='value'>" . htmlspecialchars($archivo) . "</div>
                    </div>
                    " : "") . "
                    
                    <div class='footer'>
                        <p>Este es un email automático. Por favor, no responder directamente. Accede a tu panel de administración para responder el ticket.</p>
                    </div>
                </div>
            </div>
        </body>
        </html>
        ";
        
        return $this->sendEmail($to, $subject, $html, true);
    }
}
?>
