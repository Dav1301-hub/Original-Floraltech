-- Agregar campos a la tabla empresa para configuración completa
ALTER TABLE empresa 
ADD COLUMN IF NOT EXISTS logo VARCHAR(255) DEFAULT NULL COMMENT 'Ruta del logo de la empresa',
ADD COLUMN IF NOT EXISTS facebook VARCHAR(255) DEFAULT NULL COMMENT 'URL de Facebook',
ADD COLUMN IF NOT EXISTS instagram VARCHAR(255) DEFAULT NULL COMMENT 'URL de Instagram',
ADD COLUMN IF NOT EXISTS whatsapp VARCHAR(50) DEFAULT NULL COMMENT 'Número de WhatsApp',
ADD COLUMN IF NOT EXISTS moneda VARCHAR(10) DEFAULT 'CRC' COMMENT 'Código de moneda (CRC, USD, etc)',
ADD COLUMN IF NOT EXISTS iva_porcentaje DECIMAL(5,2) DEFAULT 13.00 COMMENT 'Porcentaje de IVA',
ADD COLUMN IF NOT EXISTS zona_horaria VARCHAR(50) DEFAULT 'America/Costa_Rica' COMMENT 'Zona horaria',
ADD COLUMN IF NOT EXISTS formato_fecha VARCHAR(20) DEFAULT 'd/m/Y' COMMENT 'Formato de fecha';

-- Agregar campos a la tabla usu para configuración de usuario
ALTER TABLE usu 
ADD COLUMN IF NOT EXISTS avatar VARCHAR(255) DEFAULT NULL COMMENT 'Ruta del avatar del usuario',
ADD COLUMN IF NOT EXISTS notificaciones_email BOOLEAN DEFAULT TRUE COMMENT 'Recibir notificaciones por email';

-- Verificar que los campos se agregaron correctamente
SELECT 'Campos agregados exitosamente a las tablas empresa y usu' AS mensaje;
