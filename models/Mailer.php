<?php
/**
 * Clase Mailer para envío de emails
 * Usa PHPMailer con configuración SMTP desde email_config.php
 */

require_once __DIR__ . '/../vendor/phpmailer/phpmailer/src/Exception.php';
require_once __DIR__ . '/../vendor/phpmailer/phpmailer/src/PHPMailer.php';
require_once __DIR__ . '/../vendor/phpmailer/phpmailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mailer {
    private $from_email;
    private $from_name;
    private $host;
    private $port;
    private $username;
    private $password;
    private $encryption;
    
    public function __construct() {
        // Cargar configuración
        if (file_exists(__DIR__ . '/../config/email_config.php')) {
            require_once __DIR__ . '/../config/email_config.php';
        }
        
        $this->from_email = MAIL_FROM_EMAIL ?? 'soporte@floraltech.com';
        $this->from_name = MAIL_FROM_NAME ?? 'FloralTech Soporte';
        $this->host = MAIL_HOST ?? 'smtp.gmail.com';
        $this->port = MAIL_PORT ?? 587;
        $this->username = MAIL_USERNAME ?? '';
        $this->password = MAIL_PASSWORD ?? '';
        $this->encryption = MAIL_ENCRYPTION ?? 'tls';
    }
    
    /**
     * Enviar email usando PHPMailer con SMTP
     */
    public function sendEmail($to, $subject, $body, $isHtml = false) {
        try {
            $mail = new PHPMailer(true);
            
            // Configuración del servidor SMTP
            $mail->isSMTP();
            $mail->Host = $this->host;
            $mail->Port = $this->port;
            $mail->SMTPAuth = true;
            $mail->Username = $this->username;
            $mail->Password = $this->password;
            $mail->SMTPSecure = $this->encryption === 'ssl' ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
            
            // Remitente
            $mail->setFrom($this->from_email, $this->from_name);
            
            // Destinatario
            $mail->addAddress($to);
            
            // Contenido
            $mail->isHTML($isHtml);
            $mail->Subject = $subject;
            $mail->Body = $body;
            
            // Enviar
            $result = $mail->send();
            
            return true;
            
        } catch (Exception $e) {
            error_log("Error enviando email: " . $e->getMessage());
            throw new Exception("Error al enviar email: " . $e->getMessage());
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
?>
