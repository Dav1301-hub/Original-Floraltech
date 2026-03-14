-- ============================================================
-- Añadir columna de imagen de perfil para clientes (panel cliente)
-- Ejecutar en phpMyAdmin (pestaña SQL) o por línea de comandos.
-- ============================================================

-- Añadir columna avatar a la tabla cli (ruta del archivo, ej: uploads/avatars/cliente/avatar_5_1234567890.jpg)
ALTER TABLE `cli`
  ADD COLUMN `avatar` VARCHAR(255) DEFAULT NULL COMMENT 'Ruta de la foto de perfil del cliente' AFTER `email`;

-- Opcional: índice si en el futuro buscas por clientes con/sin avatar
-- CREATE INDEX idx_cli_avatar ON cli (avatar(50));
