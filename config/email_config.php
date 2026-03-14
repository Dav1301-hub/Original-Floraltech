<?php
// Configuración de email para PHPMailer
// En InfinityFree: SMTP externo (Gmail) sí está permitido. Si falla el envío, prueba MAIL_PORT = 465 y MAIL_ENCRYPTION = 'ssl'
define('MAIL_HOST', 'smtp.gmail.com');
define('MAIL_PORT', 587);           // En algunos hostings gratuitos probar 465 (ssl)
define('MAIL_USERNAME', 'epymes270@gmail.com');
define('MAIL_PASSWORD', 'hadm asrg qkww kjcr'); // Contraseña de aplicación de Google
define('MAIL_FROM_EMAIL', 'epymes270@gmail.com');
define('MAIL_FROM_NAME', 'FloralTech');
define('MAIL_ENCRYPTION', 'tls');   // Para puerto 465 usar 'ssl'

// Envío de factura al crear pedido (empleado). Poner false si en tu hosting no llegan los correos.
if (!defined('ENVIAR_FACTURA_AL_CREAR_PEDIDO')) {
    define('ENVIAR_FACTURA_AL_CREAR_PEDIDO', true);
}
?>
