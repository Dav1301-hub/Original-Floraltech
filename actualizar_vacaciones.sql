-- Script para agregar la columna "tipo" a la tabla vacaciones
-- Ejecutar este script en phpMyAdmin o MySQL

USE flores;

-- Agregar la columna tipo si no existe
ALTER TABLE vacaciones ADD COLUMN IF NOT EXISTS tipo VARCHAR(50) DEFAULT 'Personales' AFTER motivo;

-- Actualizar registros existentes con valores por defecto basados en el motivo
UPDATE vacaciones 
SET tipo = CASE 
    WHEN motivo LIKE '%enferm%' THEN 'Por enfermedad'
    WHEN motivo LIKE '%matern%' THEN 'Por maternidad'
    WHEN motivo LIKE '%patern%' THEN 'Por paternidad'
    WHEN motivo LIKE '%anual%' THEN 'Anuales'
    ELSE 'Personales'
END
WHERE tipo = 'Personales';

-- Verificar la estructura de la tabla
DESCRIBE vacaciones;