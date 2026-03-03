-- ============================================
-- Script OPCIONAL para mejorar protección contra doble descuento
-- ============================================
-- Este script agrega una columna a detped para marcar qué flores
-- ya fueron descontadas del inventario
--
-- EJECUTAR SOLO SI QUIERES MÁXIMA SEGURIDAD
-- ============================================

-- 1. Agregar columna fue_descontado a detped
ALTER TABLE detped ADD COLUMN fue_descontado BOOLEAN DEFAULT FALSE COMMENT 'Indica si ya fue descontado del inventario';

-- 2. Marcar como descontados todos los movimientos históricos
-- (los que tienen registro en inv_historial)
UPDATE detped dp
SET fue_descontado = TRUE
WHERE EXISTS (
    SELECT 1 FROM inv_historial ih
    WHERE ih.motivo LIKE CONCAT('Descuento por pedido #', dp.idped)
);

-- 3. Crear índice para optimizar búsquedas
CREATE INDEX idx_detped_descuento ON detped(idped, fue_descontado);

-- ============================================
-- Verificación (ejecutar después para confirmar)
-- ============================================

-- Ver cuántos movimientos fueron marcados como descontados
SELECT 
    COUNT(*) as total_detalles,
    SUM(CASE WHEN fue_descontado = TRUE THEN 1 ELSE 0 END) as ya_descontados,
    SUM(CASE WHEN fue_descontado = FALSE THEN 1 ELSE 0 END) as pendientes
FROM detped;

-- Ver todos los detalles de pedidos con estado de descuento
SELECT 
    dp.iddetped,
    dp.idped,
    dp.idtflor,
    dp.cantidad,
    dp.fue_descontado,
    p.estado,
    p.fecha_pedido
FROM detped dp
JOIN ped p ON dp.idped = p.idped
ORDER BY p.idped DESC
LIMIT 20;
