-- Agregar columnas para gestión PEPS y control de fechas de flores
-- Ejecutar este script en phpMyAdmin o línea de comandos MySQL

USE flores;

-- Agregar columnas para gestión de fechas y calidad
ALTER TABLE inv 
ADD COLUMN IF NOT EXISTS fecha_ingreso DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha de ingreso al inventario',
ADD COLUMN IF NOT EXISTS dias_vida_util INT DEFAULT 7 COMMENT 'Días de vida útil estimados según tipo de flor',
ADD COLUMN IF NOT EXISTS estado_fisico VARCHAR(50) DEFAULT 'fresca' COMMENT 'Estado físico: fresca, revisar, urgente, vencida',
ADD COLUMN IF NOT EXISTS fecha_revision DATETIME NULL COMMENT 'Fecha de última revisión de calidad',
ADD COLUMN IF NOT EXISTS notas_calidad TEXT NULL COMMENT 'Observaciones sobre el estado de la flor';

-- Actualizar registros existentes con fecha de ingreso = fecha_actualizacion
UPDATE inv 
SET fecha_ingreso = fecha_actualizacion 
WHERE fecha_ingreso IS NULL;

-- Actualizar días de vida útil según el tipo de flor en tflor
-- Flores naturales delicadas: 3-5 días
-- Flores naturales resistentes: 7-10 días  
-- Flores artificiales: 999 días (no vencen)

UPDATE inv i
JOIN tflor t ON i.tflor_idtflor = t.idtflor
SET i.dias_vida_util = CASE
    WHEN t.naturaleza LIKE '%Artificial%' THEN 999
    WHEN t.nombre LIKE '%Rosa%' THEN 5
    WHEN t.nombre LIKE '%Tulip%' THEN 4
    WHEN t.nombre LIKE '%Girasol%' THEN 7
    WHEN t.nombre LIKE '%Orquídea%' THEN 10
    WHEN t.nombre LIKE '%Lirio%' THEN 6
    WHEN t.nombre LIKE '%Margarita%' THEN 5
    WHEN t.naturaleza LIKE '%Natural%' THEN 7
    ELSE 7
END
WHERE i.dias_vida_util = 7;

-- Crear índices para mejorar rendimiento en consultas por fechas
CREATE INDEX IF NOT EXISTS idx_inv_fecha_ingreso ON inv(fecha_ingreso);
CREATE INDEX IF NOT EXISTS idx_inv_estado_fisico ON inv(estado_fisico);
