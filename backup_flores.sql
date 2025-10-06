-- =====================================================
-- BACKUP MANUAL DE LA BASE DE DATOS FLORES
-- Generado el: 6 de Octubre de 2025
-- =====================================================

CREATE DATABASE IF NOT EXISTS `flores` CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `flores`;

-- =====================================================
-- ESTRUCTURA DE TABLAS PRINCIPALES
-- =====================================================

-- Tabla de usuarios
CREATE TABLE IF NOT EXISTS `usu` (
  `idusu` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_usu` varchar(255) NOT NULL,
  `usuario` varchar(100) NOT NULL UNIQUE,
  `pass` varchar(255) NOT NULL,
  `tpusu_idtpusu` int(11) NOT NULL DEFAULT 1,
  `fecha_creacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idusu`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Tabla de tipos de flores
CREATE TABLE IF NOT EXISTS `tflor` (
  `idtflor` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL,
  `naturaleza` enum('Natural','Artificial') NOT NULL DEFAULT 'Natural',
  `color` varchar(100) DEFAULT 'Multicolor',
  `descripcion` text,
  `precio` decimal(10,2) DEFAULT 0.00,
  `fecha_creacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idtflor`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Tabla de inventario
CREATE TABLE IF NOT EXISTS `inv` (
  `idinv` int(11) NOT NULL AUTO_INCREMENT,
  `tflor_idtflor` int(11) DEFAULT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `precio` decimal(10,2) NOT NULL DEFAULT 0.00,
  `alimentacion` varchar(255) DEFAULT 'Producto general',
  `fecha_actualizacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`idinv`),
  KEY `fk_tflor` (`tflor_idtflor`),
  FOREIGN KEY (`tflor_idtflor`) REFERENCES `tflor` (`idtflor`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Tabla de historial de inventario
CREATE TABLE IF NOT EXISTS `inv_historial` (
  `idhistorial` int(11) NOT NULL AUTO_INCREMENT,
  `idinv` int(11) NOT NULL,
  `stock_anterior` int(11) NOT NULL,
  `stock_nuevo` int(11) NOT NULL,
  `fecha_cambio` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `motivo` varchar(255) DEFAULT NULL,
  `idusu` int(11) DEFAULT NULL,
  PRIMARY KEY (`idhistorial`),
  KEY `fk_inv_hist` (`idinv`),
  FOREIGN KEY (`idinv`) REFERENCES `inv` (`idinv`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Tabla de pagos
CREATE TABLE IF NOT EXISTS `pagos` (
  `idpago` int(11) NOT NULL AUTO_INCREMENT,
  `fecha_pago` date NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `metodo_pago` varchar(100) DEFAULT 'Efectivo',
  `estado_pago` enum('Pendiente','Completado','Rechazado') DEFAULT 'Pendiente',
  `descripcion` text,
  PRIMARY KEY (`idpago`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- =====================================================
-- DATOS DE EJEMPLO (AJUSTAR SEGÚN TUS DATOS REALES)
-- =====================================================

-- Usuario administrador por defecto
INSERT IGNORE INTO `usu` (`idusu`, `nombre_usu`, `usuario`, `pass`, `tpusu_idtpusu`) VALUES
(1, 'Administrador', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1),
(2, 'Maria Sanchez', 'maria', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1);

-- Flores básicas
INSERT IGNORE INTO `tflor` (`idtflor`, `nombre`, `naturaleza`, `color`, `descripcion`) VALUES
(1, 'Rosa Roja', 'Natural', 'Rojo', 'Rosa roja clásica para expresar amor'),
(2, 'Tulipán', 'Natural', 'Multicolor', 'Tulipanes frescos de temporada'),
(3, 'Girasol', 'Natural', 'Amarillo', 'Girasoles grandes y brillantes'),
(4, 'Orquídea', 'Natural', 'Multicolor', 'Orquídeas exóticas elegantes'),
(5, 'Lirio', 'Natural', 'Blanco', 'Lirios blancos aromáticos');

-- Inventario básico
INSERT IGNORE INTO `inv` (`idinv`, `tflor_idtflor`, `stock`, `precio`, `alimentacion`) VALUES
(1, 1, 50, 15.00, 'Producto fresco'),
(2, 2, 30, 12.00, 'Producto fresco'),
(3, 3, 25, 10.00, 'Producto fresco'),
(4, 4, 20, 25.00, 'Producto premium'),
(5, 5, 15, 18.00, 'Producto premium');

-- =====================================================
-- CONFIGURACIÓN FINAL
-- =====================================================

-- Establecer AUTO_INCREMENT
ALTER TABLE `usu` AUTO_INCREMENT = 100;
ALTER TABLE `tflor` AUTO_INCREMENT = 100;
ALTER TABLE `inv` AUTO_INCREMENT = 100;
ALTER TABLE `inv_historial` AUTO_INCREMENT = 1;
ALTER TABLE `pagos` AUTO_INCREMENT = 1;

-- Mensaje de confirmación
SELECT 'Base de datos FLORES restaurada correctamente' AS mensaje;