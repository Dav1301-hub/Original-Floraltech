-- ============================================================
-- Añadir columna avatar a la tabla usu (foto de perfil empleados/admin)
-- Si ya ejecutaste update_configuracion_completa.sql, la columna puede existir:
-- en ese caso omite este script o ignora el error "Duplicate column".
-- ============================================================

ALTER TABLE `usu`
  ADD COLUMN `avatar` VARCHAR(255) DEFAULT NULL COMMENT 'Ruta de la foto de perfil' AFTER `email`;
