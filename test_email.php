<?php
/**
 * Script de prueba para verificar que los emails funcionan correctamente
 * Accede a: http://localhost/Original-Floraltech/test_email.php
 */

require_once __DIR__ . '/models/Mailer.php';

echo "<h1>Test de Envío de Email</h1>";
echo "<hr>";

try {
    $mailer = new Mailer();
    
    echo "<p><strong>Configuración detectada:</strong></p>";
    echo "<ul>";
    echo "<li>Host SMTP: " . (defined('MAIL_HOST') ? MAIL_HOST : 'No definido') . "</li>";
    echo "<li>Puerto: " . (defined('MAIL_PORT') ? MAIL_PORT : 'No definido') . "</li>";
    echo "<li>Usuario: " . (defined('MAIL_USERNAME') ? MAIL_USERNAME : 'No definido') . "</li>";
    echo "<li>Desde: " . (defined('MAIL_FROM_EMAIL') ? MAIL_FROM_EMAIL : 'No definido') . "</li>";
    echo "</ul>";
    
    echo "<p><strong>Intentando enviar email de prueba...</strong></p>";
    
    $to = 'epymes270@gmail.com';
    $subject = '[PRUEBA] Email de Configuración - ' . date('d/m/Y H:i:s');
    $body = "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <style>
            body { font-family: Arial, sans-serif; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: #0d6efd; color: white; padding: 20px; border-radius: 8px; }
            .content { background: #f9f9f9; padding: 20px; border: 1px solid #ddd; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>Email de Prueba</h2>
            </div>
            <div class='content'>
                <p>Este es un email de prueba de configuración.</p>
                <p><strong>Timestamp:</strong> " . date('d/m/Y H:i:s') . "</p>
                <p>Si recibes este email, la configuración SMTP es correcta.</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    $result = $mailer->sendEmail($to, $subject, $body, true);
    
    echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
    echo "<strong style='color: #155724;'>✓ Email enviado exitosamente a: " . htmlspecialchars($to) . "</strong>";
    echo "<p style='margin: 10px 0 0 0; color: #155724;'>Asunto: " . htmlspecialchars($subject) . "</p>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
    echo "<strong style='color: #721c24;'>✗ Error al enviar email:</strong>";
    echo "<p style='margin: 10px 0 0 0; color: #721c24;'>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "</div>";
    
    echo "<hr>";
    echo "<p><strong>Posibles soluciones:</strong></p>";
    echo "<ul>";
    echo "<li>Verifica que la contraseña de aplicación sea correcta en email_config.php</li>";
    echo "<li>Asegúrate de que Gmail tenga habilitada la autenticación de dos factores</li>";
    echo "<li>Revisa los logs de error en htdocs/error.log</li>";
    echo "<li>Verifica la conexión a internet</li>";
    echo "</ul>";
}

echo "<hr>";
echo "<p><small>Este archivo es solo para pruebas. Puedes eliminarlo después.</small></p>";
?>
