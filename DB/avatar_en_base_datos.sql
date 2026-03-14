-- ============================================================
-- Guardar fotos de perfil en la base de datos (no en archivos)
-- Ejecutar en phpMyAdmin o consola MySQL.
-- ============================================================

-- Cliente (tabla cli): datos de la imagen en DB
ALTER TABLE `cli`
  ADD COLUMN `avatar_data` MEDIUMBLOB DEFAULT NULL COMMENT 'Imagen de perfil en binario' AFTER `avatar`,
  ADD COLUMN `avatar_tipo` VARCHAR(30) DEFAULT NULL COMMENT 'MIME type ej. image/jpeg, image/png' AFTER `avatar_data`;

-- Usuario/Empleado (tabla usu): datos de la imagen en DB
ALTER TABLE `usu`
  ADD COLUMN `avatar_data` MEDIUMBLOB DEFAULT NULL COMMENT 'Imagen de perfil en binario' AFTER `avatar`,
  ADD COLUMN `avatar_tipo` VARCHAR(30) DEFAULT NULL COMMENT 'MIME type ej. image/jpeg, image/png' AFTER `avatar_data`;
