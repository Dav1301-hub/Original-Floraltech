<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../config/email_config.php';

function enviarCorreoRecuperacion($destinatario, $token) {
    $mail = new PHPMailer(true);

    try {
        // Configuración del servidor
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'epymes270@gmail.com';
        $mail->Password = 'oddh ytgf wtgk dzdh';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Usar TLS
        $mail->Port = 587;
        $mail->CharSet = 'UTF-8';

        // Configuración SSL/TLS
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
        
        // Contenido del correo
        $mail->isHTML(true);
        $mail->Subject = 'Recuperación de Contraseña - FloralTech';

        $link = "http://localhost/ProyectoFloralTechhh/index.php?ctrl=login&action=resetPassword&token=$token";
        $mail->Body = "
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset='UTF-8'>
                <style>
                    .container { max-width: 600px; margin: 0 auto; font-family: Arial, sans-serif; }
                    .header { background-color: #2293c3; color: white; padding: 20px; text-align: center; }
                    .content { padding: 20px; background-color: #f9f9f9; }
                    .button { display: inline-block; padding: 10px 20px; background-color: #2293c3; color: white; text-decoration: none; border-radius: 5px; }
                    .footer { padding: 10px; text-align: center; font-size: 12px; color: #666; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h2>Recuperación de Contraseña</h2>
                    </div>
                    <div class='content'>
                        <h3>Hola,</h3>
                        <p>Hemos recibido una solicitud para restablecer la contraseña de tu cuenta en FloralTech.</p>
                        <p>Para restablecer tu contraseña, haz clic en el siguiente enlace:</p>
                        <p style='text-align: center; margin: 20px 0;'>
                            <a href='$link' class='button'>Restablecer Contraseña</a>
                        </p>
                        <p>Si no puedes hacer clic en el botón, copia y pega el siguiente enlace en tu navegador:</p>
                        <p style='word-break: break-all; background-color: #eee; padding: 10px; border-radius: 3px;'>$link</p>
                        <p><strong>Este enlace expirará en 1 hora por seguridad.</strong></p>
                        <p>Si no solicitaste este cambio, puedes ignorar este correo.</p>
                    </div>
                    <div class='footer'>
                        <p>Este es un correo automático, por favor no respondas a este mensaje.</p>
                        <p>&copy; 2025 FloralTech - Sistema de Gestión de Floristería</p>
                    </div>
                </div>
            </body>
            </html>
        ";

        $mail->send();
        return true;
        
    } catch (Exception $e) {
        // Log del error para depuración (opcional)
        error_log("Error de PHPMailer: " . $mail->ErrorInfo);
        return false;
    }
}