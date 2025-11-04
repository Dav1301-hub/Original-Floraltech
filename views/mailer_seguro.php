<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../config/email_config.php';

function enviarCorreoRecuperacionSeguro($destinatario, $token) {
    $mail = new PHPMailer(true);

    try {
        // Configuración del servidor SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'epymes270@gmail.com';
        $mail->Password = 'oddh ytgf wtgk dzdh';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Usar TLS
        $mail->Port = 587;
        $mail->CharSet = 'UTF-8';

        // Configuración SSL/TLS para evitar errores de certificado
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ];

        // Configurar remitente y destinatario
        $mail->setFrom('epymes270@gmail.com', 'FloralTech');
        $mail->addAddress($destinatario);
        
        // Configurar el contenido del correo
        $mail->isHTML(true);
        $mail->Subject = '🔐 Recuperación de Contraseña - FloralTech';

        // Generar enlace de recuperación
        $baseUrl = "http://" . $_SERVER['HTTP_HOST'];
        $projectPath = "/Original-Floraltech";
        $link = $baseUrl . $projectPath . "/index.php?ctrl=login&action=resetPassword&token=" . $token;

        // Template HTML del correo
        $mail->Body = "
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset='UTF-8'>
                <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                <style>
                    .container { max-width: 600px; margin: 0 auto; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
                    .header { background: linear-gradient(135deg, #2293c3, #1a7ba3); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
                    .logo { font-size: 28px; font-weight: bold; margin-bottom: 10px; }
                    .content { padding: 40px 30px; background: #ffffff; }
                    .button { 
                        background: linear-gradient(135deg, #2293c3, #1a7ba3); 
                        color: white; 
                        padding: 15px 30px; 
                        text-decoration: none; 
                        border-radius: 25px; 
                        display: inline-block; 
                        margin: 25px 0; 
                        font-weight: bold;
                        text-align: center;
                    }
                    .warning { 
                        background: #fff3cd; 
                        border: 1px solid #ffeaa7; 
                        color: #856404; 
                        padding: 15px; 
                        border-radius: 5px; 
                        margin: 20px 0; 
                    }
                    .footer { 
                        background: #f8f9fa; 
                        color: #6c757d; 
                        padding: 20px; 
                        text-align: center; 
                        font-size: 12px; 
                        border-radius: 0 0 10px 10px; 
                    }
                    .link-backup { 
                        word-break: break-all; 
                        background: #e9ecef; 
                        padding: 10px; 
                        border-radius: 5px; 
                        font-family: monospace; 
                        font-size: 12px; 
                    }
                </style>
            </head>
            <body style='background-color: #f8f9fa; padding: 20px;'>
                <div class='container'>
                    <div class='header'>
                        <div class='logo'>🌸 FloralTech</div>
                        <p style='margin: 0; opacity: 0.9;'>Sistema de Gestión Floral</p>
                    </div>
                    <div class='content'>
                        <h2 style='color: #2293c3; margin-top: 0;'>¡Hola!</h2>
                        <p>Hemos recibido una solicitud para recuperar la contraseña de tu cuenta en FloralTech.</p>
                        <p>Para crear una nueva contraseña, haz clic en el siguiente botón:</p>
                        
                        <div style='text-align: center;'>
                            <a href='$link' class='button' style='color: white;'>🔐 Recuperar Contraseña</a>
                        </div>
                        
                        <div class='warning'>
                            <strong>⚠️ Importante:</strong>
                            <ul style='margin: 10px 0 0 0; padding-left: 20px;'>
                                <li>Este enlace expira en <strong>1 hora</strong> por seguridad</li>
                                <li>Solo puedes usar este enlace una vez</li>
                                <li>Si no solicitaste este cambio, ignora este correo</li>
                            </ul>
                        </div>
                        
                        <p><strong>¿No puedes hacer clic en el botón?</strong><br>
                        Copia y pega el siguiente enlace en tu navegador:</p>
                        <div class='link-backup'>$link</div>
                        
                        <p style='margin-bottom: 0;'>Si tienes problemas, contacta con el administrador del sistema.</p>
                    </div>
                    <div class='footer'>
                        <p style='margin: 0 0 10px 0;'>Este es un correo automático, por favor no respondas.</p>
                        <p style='margin: 0;'>&copy; " . date('Y') . " FloralTech - Todos los derechos reservados</p>
                    </div>
                </div>
            </body>
            </html>
        ";

        // Versión de texto plano como alternativa
        $mail->AltBody = "
            Recuperación de Contraseña - FloralTech
            
            Hola,
            
            Hemos recibido una solicitud para recuperar tu contraseña.
            
            Para restablecer tu contraseña, visita el siguiente enlace:
            $link
            
            Este enlace expira en 1 hora por seguridad.
            
            Si no solicitaste este cambio, puedes ignorar este correo.
            
            © " . date('Y') . " FloralTech
        ";

        // Enviar el correo
        $result = $mail->send();
        
        // Log exitoso para depuración
        error_log("✅ Correo de recuperación enviado exitosamente a: $destinatario");
        
        return true;
        
    } catch (Exception $e) {
        // Log del error para depuración
        error_log("❌ Error de PHPMailer: " . $mail->ErrorInfo);
        error_log("❌ Excepción: " . $e->getMessage());
        
        return false;
    }
}

/**
 * Función auxiliar para obtener información del usuario por email
 */
function obtenerUsuarioPorEmail($email) {
    try {
        require_once __DIR__ . '/../models/conexion.php';
        $conexion = new conexion();
        $db = $conexion->get_conexion();
        
        $stmt = $db->prepare("SELECT idusu, username, nombre_completo, email FROM usu WHERE email = ? AND activo = 1");
        $stmt->execute([$email]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Error al obtener usuario por email: " . $e->getMessage());
        return false;
    }
}
?>
