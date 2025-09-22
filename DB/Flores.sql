-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 27-08-2025 a las 17:46:18
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `flores`
--
CREATE DATABASE IF NOT EXISTS `flores` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `flores`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cfg_sis`
--

CREATE TABLE `cfg_sis` (
  `id_cfg` int(11) NOT NULL AUTO_INCREMENT,
  `idusu` int(11) NOT NULL,
  `idioma` varchar(50) NOT NULL DEFAULT 'Español',
  `zona_hor` varchar(100) NOT NULL DEFAULT 'America/Bogota',
  `fmt_fecha` varchar(50) NOT NULL DEFAULT 'dd/mm/yyyy',
  `estilo_ui` varchar(50) NOT NULL DEFAULT 'Claro',
  `act_auto` tinyint(1) NOT NULL DEFAULT 1,
  `notif_act` tinyint(1) NOT NULL DEFAULT 1,
  `act_prog` varchar(50) DEFAULT NULL,
  `auth_2fa` tinyint(1) NOT NULL DEFAULT 0,
  `intentos_max` int(11) NOT NULL DEFAULT 3,
  `bloqueo_min` int(11) NOT NULL DEFAULT 30,
  `log_cambios` tinyint(1) NOT NULL DEFAULT 1,
  `retencion_log` int(11) NOT NULL DEFAULT 365,
  `id_usu_mod` int(11) DEFAULT NULL,
  `fch_ult_mod` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cfg_sis`
--

INSERT INTO `cfg_sis` (`id_cfg`, `moneda`, `idioma`, `zona_hor`, `fmt_fecha`, `estilo_ui`, `act_auto`, `notif_act`, `act_prog`, `auth_2fa`, `intentos_max`, `bloqueo_min`, `log_cambios`, `retencion_log`, `id_usu_mod`, `fch_ult_mod`) VALUES
(1, 'USD', 'Inglés', 'America/New_York', 'dd/mm/yyyy', 'Oscuro', 0, 0, '', 0, 3, 30, 1, 365, NULL, '2025-07-07 20:28:26');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cli`
--

CREATE TABLE `cli` (
  `idcli` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `telefono` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `fecha_registro` date DEFAULT curdate(),
  `fecha_actualizacion` datetime DEFAULT NULL COMMENT 'Fecha de última actualización',
  `empleado_registro` int(11) DEFAULT NULL COMMENT 'ID del empleado que registró al cliente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cli`
--

INSERT INTO `cli` (`idcli`, `nombre`, `direccion`, `telefono`, `email`, `fecha_registro`, `fecha_actualizacion`, `empleado_registro`) VALUES
(1, 'María González', 'Calle 123 #45-67', '3101234567', 'maria.g@example.com', '2023-01-15', NULL, NULL),
(2, 'Juan Pérez', 'Avenida 8 #12-34', '3157654321', 'juan.p@example.com', '2023-02-20', NULL, NULL),
(3, 'Floristería Bella', 'Carrera 56 #78-90', '6012345678', 'bellaflor@example.com', '2023-03-10', NULL, NULL),
(4, 'Eventos Especiales S.A.', 'Diagonal 23 #34-56', '6076543210', 'eventos@example.com', '2023-04-05', NULL, NULL),
(5, 'Ana Rodríguez', 'Transversal 45 #67-89', '3209876543', 'ana.r@example.com', '2023-05-12', NULL, NULL),
(6, 'Jorge Puentes', '', '330425425', 'jorge@gmail.com', '2025-07-22', NULL, NULL),
(7, 'Juan Pérez', 'Calle 123 #45-67', '555-0123', 'cliente@test.com', '2025-07-22', NULL, NULL),
(8, 'Jorge Luis Puentes Brochero', 'Sin dirección', '3217837594', 'jorgepb2007@gmail.com', '2025-07-30', NULL, NULL),
(9, 'juan', 'Sin dirección', '789456', 'juan@gmail.com', '2025-08-05', NULL, NULL),
(10, 'david parada', 'Sin dirección', '32135', 'david@gmail.com', '2025-08-11', NULL, NULL),
(11, 'maria sanchez', 'Sin dirección', '456', 'maria@gmail.com', '2025-08-14', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detped`
--

CREATE TABLE `detped` (
  `iddetped` int(11) NOT NULL,
  `idped` int(11) NOT NULL,
  `idtflor` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) GENERATED ALWAYS AS (`cantidad` * `precio_unitario`) STORED
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `detped`
--

INSERT INTO `detped` (`iddetped`, `idped`, `idtflor`, `cantidad`, `precio_unitario`) VALUES
(1, 1, 1, 5, 5.99),
(2, 1, 3, 2, 4.75),
(3, 1, 8, 3, 2.99),
(4, 2, 2, 4, 7.50),
(5, 2, 5, 1, 6.25),
(6, 3, 4, 5, 12.99),
(7, 3, 7, 2, 8.99),
(8, 4, 6, 5, 3.50),
(9, 4, 1, 2, 5.99),
(10, 5, 3, 10, 4.75),
(11, 5, 5, 2, 6.25),
(12, 6, 8, 6, 2.99),
(14, 8, 3, 1, 4.75),
(15, 8, 6, 1, 3.50),
(16, 9, 1, 6, 25.99),
(17, 9, 2, 3, 18.50),
(18, 10, 3, 2, 35.00),
(19, 10, 4, 1, 22.75),
(20, 11, 5, 3, 85.00),
(21, 12, 1, 2, 25.99),
(22, 12, 4, 1, 22.75),
(23, 13, 1, 5, 25.99),
(24, 13, 3, 3, 35.00),
(25, 13, 5, 2, 85.00),
(26, 14, 10, 1, 15.00),
(27, 15, 12, 1, 15.00),
(28, 16, 12, 1, 15.00),
(29, 16, 11, 1, 15.00),
(30, 17, 12, 1, 15.00),
(31, 18, 12, 7, 15.00),
(32, 18, 11, 7, 15.00),
(33, 19, 6, 189, 3.50),
(34, 19, 5, 1, 6.25),
(35, 19, 4, 1, 12.99),
(36, 19, 7, 1, 8.99),
(37, 19, 1, 1, 5.99),
(38, 19, 8, 1, 2.99),
(39, 19, 2, 1, 7.50),
(40, 20, 3, 1, 4.75),
(41, 21, 6, 1, 10000.00),
(42, 22, 6, 180, 10000.00),
(43, 23, 6, 1, 10000.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ent`
--

CREATE TABLE `ent` (
  `ident` int(11) NOT NULL,
  `fecha_ent` date NOT NULL,
  `hora_ent` time DEFAULT NULL,
  `direccion` varchar(255) NOT NULL,
  `estado_ent` varchar(255) DEFAULT 'Programada',
  `ped_idped` int(11) NOT NULL,
  `notas` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ent`
--

INSERT INTO `ent` (`ident`, `fecha_ent`, `hora_ent`, `direccion`, `estado_ent`, `ped_idped`, `notas`) VALUES
(1, '2023-06-15', '14:30:00', 'Calle 123 #45-67', 'Completada', 1, 'Entregado en recepción'),
(2, '2023-06-16', '10:00:00', 'Avenida 8 #12-34', 'En camino', 2, 'Llamar antes de llegar'),
(3, '2023-06-17', '16:00:00', 'Carrera 56 #78-90', 'Programada', 3, 'Pedido grande, llevar ayuda'),
(4, '2023-06-15', '11:30:00', 'Diagonal 23 #34-56', 'Completada', 4, 'Entregado a seguridad'),
(5, '2023-06-18', '09:00:00', 'Transversal 45 #67-89', 'Cancelada', 5, 'Cliente canceló pedido');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inv`
--

CREATE TABLE `inv` (
  `idinv` int(11) NOT NULL,
  `alimentacion` varchar(255) DEFAULT NULL,
  `tflor_idtflor` int(11) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `precio` decimal(10,2) NOT NULL,
  `fecha_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `empleado_id` int(11) DEFAULT NULL COMMENT 'ID del empleado que realizó el movimiento',
  `motivo` varchar(255) DEFAULT NULL COMMENT 'Motivo del movimiento de inventario',
  `cantidad_disponible` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `inv`
--

INSERT INTO `inv` (`idinv`, `alimentacion`, `tflor_idtflor`, `stock`, `precio`, `fecha_actualizacion`, `empleado_id`, `motivo`, `cantidad_disponible`) VALUES
(1, 'Agua y nutrientes', 1, 150, 5.99, '2025-07-28 22:13:32', NULL, NULL, 150),
(2, 'Agua y nutrientes', 2, 80, 7.50, '2025-07-28 22:13:32', NULL, NULL, 80),
(3, 'Agua y luz solar', 3, 59, 4.75, '2025-07-28 22:13:32', NULL, NULL, 59),
(4, 'Humedad controlada', 4, 40, 12.99, '2025-07-28 22:13:32', NULL, NULL, 40),
(5, 'Agua y nutrientes', 5, 70, 6.25, '2025-07-28 22:13:32', NULL, NULL, 70),
(6, 'N/A', 6, 18, 3.50, '2025-08-11 15:57:37', NULL, NULL, 199),
(7, 'Luz indirecta', 7, 90, 8.99, '2025-07-28 22:13:32', NULL, NULL, 90),
(8, 'Poca agua', 8, 120, 2.99, '2025-07-28 22:13:32', NULL, NULL, 120),
(9, 'Agua diaria', 1, 50, 25.99, '2025-07-28 22:13:32', NULL, NULL, 50),
(10, 'Agua cada 2 días', 2, 30, 18.50, '2025-07-28 22:13:32', NULL, NULL, 30),
(11, 'Agua semanal', 3, 25, 35.00, '2025-07-28 22:13:32', NULL, NULL, 25),
(12, 'Agua diaria', 4, 40, 22.75, '2025-07-28 22:13:32', NULL, NULL, 40),
(13, 'Agua semanal', 5, 15, 85.00, '2025-07-28 22:13:32', NULL, NULL, 15),
(14, 'Agua diaria', 20, 22, 16.55, '2025-08-05 13:28:10', NULL, NULL, 0),
(15, 'Agua diaria', 21, 16, 15.96, '2025-08-05 13:28:10', NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inv_historial`
--

CREATE TABLE `inv_historial` (
  `idhistorial` int(11) NOT NULL,
  `idinv` int(11) NOT NULL,
  `stock_anterior` int(11) NOT NULL,
  `stock_nuevo` int(11) NOT NULL,
  `fecha_cambio` datetime DEFAULT current_timestamp(),
  `idusu` int(11) DEFAULT NULL,
  `motivo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `inv_historial`
--

INSERT INTO `inv_historial` (`idhistorial`, `idinv`, `stock_anterior`, `stock_nuevo`, `fecha_cambio`, `idusu`, `motivo`) VALUES
(1, 1, 150, 145, '2025-07-07 14:43:34', 4, 'Venta PED-20230001'),
(2, 3, 60, 58, '2025-07-07 14:43:34', 4, 'Venta PED-20230001'),
(3, 8, 120, 117, '2025-07-07 14:43:34', 4, 'Venta PED-20230001'),
(4, 2, 80, 76, '2025-07-07 14:43:34', 4, 'Venta PED-20230002'),
(5, 5, 70, 69, '2025-07-07 14:43:34', 4, 'Venta PED-20230002'),
(6, 1, 145, 143, '2025-07-07 14:43:34', 4, 'Venta PED-20230004'),
(7, 6, 200, 195, '2025-07-07 14:43:34', 4, 'Venta PED-20230004'),
(8, 8, 117, 111, '2025-07-07 14:43:34', 4, 'Venta PED-20230006');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pag`
--

CREATE TABLE `pag` (
  `idpag` int(11) NOT NULL,
  `modulo` varchar(255) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `ruta` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `icono` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pag`
--

INSERT INTO `pag` (`idpag`, `modulo`, `nombre`, `ruta`, `descripcion`, `icono`) VALUES
(1, 'Ventas', 'Pedidos', '/ventas/pedidos', 'Gestión de pedidos de clientes', 'shopping-cart'),
(2, 'Ventas', 'Clientes', '/ventas/clientes', 'Gestión de clientes', 'users'),
(3, 'Inventario', 'Productos', '/inventario/productos', 'Gestión de productos', 'package'),
(4, 'Inventario', 'Stock', '/inventario/stock', 'Control de inventario', 'database'),
(5, 'Entregas', 'Programación', '/entregas/programacion', 'Programación de entregas', 'truck'),
(6, 'Reportes', 'Ventas', '/reportes/ventas', 'Reportes de ventas', 'bar-chart-2'),
(7, 'Configuración', 'Usuarios', '/config/usuarios', 'Gestión de usuarios', 'user'),
(8, 'Configuración', 'Sistema', '/config/sistema', 'Configuración del sistema', 'settings');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagos`
--

CREATE TABLE `pagos` (
  `idpago` int(11) NOT NULL,
  `fecha_pago` datetime DEFAULT current_timestamp(),
  `metodo_pago` varchar(50) NOT NULL,
  `estado_pag` varchar(255) DEFAULT 'Pendiente',
  `monto` decimal(10,2) NOT NULL,
  `ped_idped` int(11) NOT NULL,
  `transaccion_id` varchar(255) DEFAULT NULL,
  `comprobante_transferencia` varchar(255) DEFAULT NULL,
  `verificado_por` int(11) DEFAULT NULL,
  `fecha_verificacion` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pagos`
--

INSERT INTO `pagos` (`idpago`, `fecha_pago`, `metodo_pago`, `estado_pag`, `monto`, `ped_idped`, `transaccion_id`, `comprobante_transferencia`, `verificado_por`, `fecha_verificacion`) VALUES
(1, '2025-07-07 14:43:34', 'Tarjeta crédito', 'Completado', 45.97, 1, 'TXN123456789', NULL, NULL, NULL),
(2, '2025-07-07 14:43:34', 'Efectivo', 'Completado', 32.50, 2, NULL, NULL, NULL, NULL),
(3, '2025-07-07 14:43:34', 'Transferencia', 'Pendiente', 89.95, 3, 'TXN987654321', NULL, NULL, NULL),
(4, '2025-07-07 14:43:34', 'Tarjeta débito', 'Completado', 24.75, 4, 'TXN456789123', NULL, NULL, NULL),
(5, '2025-07-07 14:43:34', 'Efectivo', 'Reembolsado', 56.25, 5, NULL, NULL, NULL, NULL),
(6, '2025-07-07 14:43:34', 'Tarjeta crédito', 'Procesando', 18.00, 6, 'TXN654321987', NULL, NULL, NULL),
(7, '2025-07-19 20:32:07', 'Tarjeta', 'Completado', 150.75, 9, 'TXN-001', NULL, NULL, NULL),
(8, '2025-07-20 20:32:07', 'PayPal', 'Completado', 89.50, 10, 'TXN-002', NULL, NULL, NULL),
(9, '2025-07-21 20:32:07', 'Tarjeta', 'Pendiente', 245.00, 11, 'TXN-003', NULL, NULL, NULL),
(10, '2025-07-22 20:32:07', 'Efectivo', 'Pendiente', 67.25, 12, 'TXN-004', NULL, NULL, NULL),
(11, '2025-07-17 20:32:07', 'Tarjeta', 'Completado', 320.99, 13, 'TXN-005', NULL, NULL, NULL),
(12, '2025-07-28 17:41:30', 'efectivo', 'Pendiente', 15.00, 14, NULL, NULL, NULL, NULL),
(13, '2025-07-28 17:51:06', 'efectivo', 'Pendiente', 15.00, 15, NULL, NULL, NULL, NULL),
(14, '2025-07-29 07:11:45', 'efectivo', 'Pendiente', 30.00, 16, NULL, NULL, NULL, NULL),
(15, '2025-07-29 08:01:51', 'efectivo', 'Pendiente', 15.00, 17, NULL, NULL, NULL, NULL),
(16, '2025-07-29 08:23:47', 'efectivo', 'Pendiente', 210.00, 18, NULL, NULL, NULL, NULL),
(17, '2025-08-05 08:31:14', 'nequi', 'Pendiente', 706.21, 19, NULL, NULL, NULL, NULL),
(18, '2025-08-05 08:53:16', 'nequi', 'Pendiente', 4.75, 20, NULL, NULL, NULL, NULL),
(19, '2025-08-05 10:40:06', 'nequi', 'Pendiente', 10000.00, 21, NULL, NULL, NULL, NULL),
(20, '2025-08-11 09:35:36', 'efectivo', 'Pendiente', 1800000.00, 22, NULL, NULL, NULL, NULL),
(21, '2025-08-11 10:57:37', 'efectivo', 'Pendiente', 10000.00, 23, NULL, NULL, NULL, NULL),
(22, '2025-08-14 22:38:49', 'nequi', 'Completado', 50000.00, 1, 'TXN001', NULL, NULL, NULL),
(23, '2025-08-14 22:38:49', 'transferencia', 'Completado', 75000.00, 2, 'TXN002', NULL, NULL, NULL),
(24, '2025-08-14 22:38:49', 'nequi', 'Completado', 25000.00, 3, 'TXN003', NULL, NULL, NULL),
(25, '2025-08-14 22:38:49', 'transferencia', 'Pendiente', 10000.00, 4, 'TXN004', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ped`
--

CREATE TABLE `ped` (
  `idped` int(11) NOT NULL,
  `numped` varchar(255) NOT NULL,
  `fecha_pedido` datetime DEFAULT current_timestamp(),
  `monto_total` decimal(10,2) NOT NULL,
  `cli_idcli` int(11) NOT NULL,
  `estado` varchar(50) DEFAULT 'Pendiente',
  `empleado_id` int(11) DEFAULT NULL COMMENT 'ID del empleado que creó el pedido',
  `fecha_actualizacion` datetime DEFAULT NULL COMMENT 'Fecha de última actualización',
  `empleado_actualizacion` int(11) DEFAULT NULL COMMENT 'ID del empleado que actualizó por última vez',
  `notas` text DEFAULT NULL COMMENT 'Notas adicionales del pedido',
  `direccion_entrega` text DEFAULT NULL,
  `fecha_entrega_solicitada` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ped`
--

INSERT INTO `ped` (`idped`, `numped`, `fecha_pedido`, `monto_total`, `cli_idcli`, `estado`, `empleado_id`, `fecha_actualizacion`, `empleado_actualizacion`, `notas`, `direccion_entrega`, `fecha_entrega_solicitada`) VALUES
(1, 'PED-20230001', '2025-07-07 14:43:34', 45.97, 1, 'Completado', NULL, NULL, NULL, NULL, NULL, NULL),
(2, 'PED-20230002', '2025-07-07 14:43:34', 32.50, 2, 'En proceso', NULL, NULL, NULL, NULL, NULL, NULL),
(3, 'PED-20230003', '2025-07-07 14:43:34', 89.95, 3, 'Pendiente', NULL, NULL, NULL, NULL, NULL, NULL),
(4, 'PED-20230004', '2025-07-07 14:43:34', 24.75, 4, 'Completado', NULL, NULL, NULL, NULL, NULL, NULL),
(5, 'PED-20230005', '2025-07-07 14:43:34', 56.25, 5, 'Cancelado', NULL, NULL, NULL, NULL, NULL, NULL),
(6, 'PED-20230006', '2025-07-07 14:43:34', 18.00, 1, 'En proceso', NULL, NULL, NULL, NULL, NULL, NULL),
(8, 'PED-2025-8989', '2025-07-22 12:01:05', 8.25, 6, 'Pendiente', NULL, NULL, NULL, NULL, NULL, NULL),
(9, 'PED-001', '2025-07-18 20:32:07', 150.75, 7, 'Completado', NULL, NULL, NULL, NULL, NULL, NULL),
(10, 'PED-002', '2025-07-19 20:32:07', 89.50, 7, 'Completado', NULL, NULL, NULL, NULL, NULL, NULL),
(11, 'PED-003', '2025-07-20 20:32:07', 245.00, 7, 'En Preparación', NULL, NULL, NULL, NULL, NULL, NULL),
(12, 'PED-004', '2025-07-21 20:32:07', 67.25, 7, 'Pendiente', NULL, NULL, NULL, NULL, NULL, NULL),
(13, 'PED-005', '2025-07-22 20:32:07', 320.99, 7, 'Completado', NULL, NULL, NULL, NULL, NULL, NULL),
(14, 'PED-20250729004130-6', '2025-07-28 17:41:30', 15.00, 6, 'Pendiente', NULL, NULL, NULL, NULL, 'Carrera5 #20-65', '2025-07-30'),
(15, 'PED-20250729005106-6', '2025-07-28 17:51:06', 15.00, 6, 'Pendiente', NULL, NULL, NULL, NULL, 'cc', '2025-07-30'),
(16, 'PED-20250729141145-6', '2025-07-29 07:11:45', 30.00, 6, 'Pendiente', NULL, NULL, NULL, NULL, 'Caareaefa#ecfw123', '2025-07-30'),
(17, 'PED-20250729150151-6', '2025-07-29 08:01:51', 15.00, 6, 'Pendiente', NULL, NULL, NULL, NULL, 'CAreraass#3e2', '2025-07-30'),
(18, 'PED-20250729152347-6', '2025-07-29 08:23:47', 210.00, 6, 'Pendiente', NULL, NULL, NULL, NULL, 'CArara', '2025-07-30'),
(19, 'PED-20250805153114-6', '2025-08-05 08:31:14', 706.21, 6, 'Pendiente', NULL, NULL, NULL, NULL, 'career', '2025-08-06'),
(20, 'PED-20250805155316-6', '2025-08-05 08:53:16', 4.75, 6, 'Pendiente', NULL, NULL, NULL, NULL, '3r3r', '2025-08-06'),
(21, 'PED-20250805174006-8', '2025-08-05 10:40:06', 10000.00, 8, 'Pendiente', NULL, NULL, NULL, NULL, 'cc', '2025-08-06'),
(22, 'PED-20250811163536-8', '2025-08-11 09:35:36', 1800000.00, 8, 'Pendiente', NULL, NULL, NULL, NULL, 'jh', '2025-08-12'),
(23, 'PED-20250811175737-8', '2025-08-11 10:57:37', 10000.00, 8, 'Pendiente', NULL, NULL, NULL, NULL, 'kjnv', '2025-08-12');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `perf`
--

CREATE TABLE `perf` (
  `idperf` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `perf`
--

INSERT INTO `perf` (`idperf`, `nombre`, `descripcion`) VALUES
(1, 'Super Admin', 'Acceso total al sistema sin restricciones'),
(2, 'Gerente', 'Acceso a todas las funciones excepto configuración del sistema'),
(3, 'Vendedor Senior', 'Puede realizar ventas y gestionar clientes'),
(4, 'Vendedor Junior', 'Puede realizar ventas básicas'),
(5, 'Repartidor', 'Solo acceso a módulo de entregas');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `perf_perm`
--

CREATE TABLE `perf_perm` (
  `idperf` int(11) NOT NULL,
  `idperm` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `perf_perm`
--

INSERT INTO `perf_perm` (`idperf`, `idperm`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(1, 5),
(1, 6),
(1, 7),
(1, 8),
(1, 9),
(1, 10),
(2, 1),
(2, 2),
(2, 3),
(2, 4),
(2, 5),
(2, 6),
(2, 7),
(2, 10),
(3, 1),
(3, 2),
(3, 4),
(3, 5),
(3, 6),
(4, 1),
(4, 4),
(5, 4);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `perm`
--

CREATE TABLE `perm` (
  `idperm` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `codigo` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `perm`
--

INSERT INTO `perm` (`idperm`, `nombre`, `codigo`, `descripcion`) VALUES
(1, 'Crear pedidos', 'PED_CREATE', 'Permite crear nuevos pedidos'),
(2, 'Editar pedidos', 'PED_EDIT', 'Permite modificar pedidos existentes'),
(3, 'Eliminar pedidos', 'PED_DELETE', 'Permite eliminar pedidos'),
(4, 'Ver todos los pedidos', 'PED_VIEW_ALL', 'Permite ver todos los pedidos del sistema'),
(5, 'Crear clientes', 'CLI_CREATE', 'Permite crear nuevos clientes'),
(6, 'Editar clientes', 'CLI_EDIT', 'Permite modificar clientes existentes'),
(7, 'Gestionar inventario', 'INV_MANAGE', 'Permite gestionar el inventario'),
(8, 'Administrar usuarios', 'USER_ADMIN', 'Permite gestionar usuarios del sistema'),
(9, 'Configurar sistema', 'SYS_CONFIG', 'Permite modificar configuración del sistema'),
(10, 'Ver reportes', 'REP_VIEW', 'Permite acceder a los reportes');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `perm_tpusu`
--

CREATE TABLE `perm_tpusu` (
  `idtpusu` int(11) NOT NULL,
  `idperm` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `perm_tpusu`
--

INSERT INTO `perm_tpusu` (`idtpusu`, `idperm`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(1, 5),
(1, 6),
(1, 7),
(1, 8),
(1, 9),
(1, 10),
(2, 1),
(2, 2),
(2, 4),
(2, 5),
(2, 6),
(3, 7),
(4, 4);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pxp`
--

CREATE TABLE `pxp` (
  `idperf` int(11) NOT NULL,
  `idpag` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pxp`
--

INSERT INTO `pxp` (`idperf`, `idpag`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(1, 5),
(1, 6),
(1, 7),
(1, 8),
(2, 1),
(2, 2),
(2, 3),
(2, 4),
(2, 5),
(2, 6),
(3, 1),
(3, 2),
(3, 6),
(4, 1),
(5, 5);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tflor`
--

CREATE TABLE `tflor` (
  `idtflor` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `naturaleza` varchar(255) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `precio` decimal(10,2) NOT NULL DEFAULT 0.00,
  `fecha_creacion` datetime DEFAULT current_timestamp() COMMENT 'Fecha de creación del producto',
  `activo` tinyint(1) DEFAULT 1 COMMENT 'Si el producto está activo',
  `color` varchar(100) DEFAULT 'Multicolor',
  `precio_venta` decimal(10,2) DEFAULT 0.00,
  `estado` varchar(50) DEFAULT 'activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tflor`
--

INSERT INTO `tflor` (`idtflor`, `nombre`, `naturaleza`, `descripcion`, `precio`, `fecha_creacion`, `activo`, `color`, `precio_venta`, `estado`) VALUES
(1, 'Rosas', 'Natural', 'Rosas de diversos colores, ideales para arreglos florales', 15000.00, '2025-07-22 12:07:00', 1, 'Multicolor', 15.00, 'activo'),
(2, 'Tulipanes', 'Natural', 'Tulipanes frescos importados de Holanda', 18000.00, '2025-07-22 12:07:00', 1, 'Multicolor', 15.00, 'activo'),
(3, 'Girasoles', 'Natural', 'Girasoles grandes y brillantes', 10000.00, '2025-07-22 12:07:00', 1, 'Multicolor', 15.00, 'activo'),
(4, 'Orquídeas', 'Natural', 'Orquídeas exóticas de diversas variedades', 25000.00, '2025-07-22 12:07:00', 1, 'Multicolor', 15.00, 'activo'),
(5, 'Lirios', 'Natural', 'Lirios elegantes y fragantes', 10000.00, '2025-07-22 12:07:00', 1, 'Multicolor', 15.00, 'activo'),
(6, 'Flores Artificiales', 'Artificial', 'Flores decorativas de alta calidad', 10000.00, '2025-07-22 12:07:00', 1, 'Multicolor', 15.00, 'activo'),
(7, 'Plantas de Interior', 'Natural', 'Variedad de plantas para decoración interior', 10000.00, '2025-07-22 12:07:00', 1, 'Multicolor', 15.00, 'activo'),
(8, 'Suculentas', 'Natural', 'Pequeñas plantas suculentas para arreglos', 10000.00, '2025-07-22 12:07:00', 1, 'Multicolor', 15.00, 'activo'),
(9, 'Rosa Roja', 'Natural', 'Rosa roja clásica para ocasiones especiales', 15000.00, '2025-07-22 13:32:07', 1, 'Multicolor', 15.00, 'activo'),
(10, 'Tulipán Amarillo', 'Natural', 'Tulipán amarillo fresco de temporada', 18000.00, '2025-07-22 13:32:07', 1, 'Multicolor', 15.00, 'activo'),
(11, 'Lirio Blanco', 'Natural', 'Lirio blanco elegante para eventos', 10000.00, '2025-07-22 13:32:07', 1, 'Multicolor', 15.00, 'activo'),
(12, 'Girasol', 'Natural', 'Girasol brillante y alegre', 10000.00, '2025-07-22 13:32:07', 1, 'Multicolor', 15.00, 'activo'),
(13, 'Orquídea Morada', 'Natural', 'Orquídea exótica de alta calidad', 25000.00, '2025-07-22 13:32:07', 1, 'Multicolor', 15.00, 'activo'),
(14, 'Rosa Roja', 'Flor natural', 'Hermosa rosa roja perfecta para ocasiones especiales', 15000.00, '2025-07-28 17:15:22', 1, 'Rojo', 15.50, 'activo'),
(15, 'Tulipán Amarillo', 'Flor natural', 'Tulipán amarillo vibrante, ideal para primavera', 18000.00, '2025-07-28 17:15:22', 1, 'Amarillo', 12.00, 'activo'),
(17, 'Orquídea Blanca', 'Flor natural', 'Elegante orquídea blanca para ocasiones elegantes', 25000.00, '2025-07-28 17:15:22', 1, 'Blanco', 35.00, 'activo'),
(19, 'Margarita', 'Flor natural', 'Margarita blanca simple y hermosa', 10000.00, '2025-07-28 17:15:22', 1, 'Blanco', 6.75, 'activo'),
(20, 'Margarita Blanca', 'Blanco', 'Margarita blanca pura, representa la inocencia', 10000.00, '2025-08-05 08:28:10', 1, 'Multicolor', 0.00, 'activo'),
(21, 'Lirio Morado', 'Morado', 'Elegante lirio morado, representa la nobleza', 10000.00, '2025-08-05 08:28:10', 1, 'Multicolor', 0.00, 'activo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tokens_recuperacion`
--

CREATE TABLE `tokens_recuperacion` (
  `id` int(11) NOT NULL,
  `idUsuario` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expiracion` datetime NOT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tokens_recuperacion`
--

INSERT INTO `tokens_recuperacion` (`id`, `idUsuario`, `token`, `expiracion`, `creado_en`) VALUES
(7, 40, 'cce08aa5f07ffd1f1efb5154d512ea9b47036a8110a19a984e4823ea259178dc', '2025-08-26 18:40:30', '2025-08-26 15:40:30');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tpusu`
--

CREATE TABLE `tpusu` (
  `idtpusu` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tpusu`
--

INSERT INTO `tpusu` (`idtpusu`, `nombre`, `descripcion`) VALUES
(1, 'Administrador', 'Acceso completo al sistema'),
(2, 'Vendedor', 'Puede realizar ventas y gestionar clientes'),
(3, 'Inventario', 'Gestiona el stock y productos'),
(4, 'Repartidor', 'Encargado de entregas a clientes'),
(5, 'Cliente', 'Usuario que realiza compras');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usu`
--

CREATE TABLE `usu` (
  `idusu` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `nombre_completo` varchar(255) NOT NULL,
  `naturaleza` varchar(255) DEFAULT NULL,
  `telefono` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `clave` varchar(255) NOT NULL,
  `tpusu_idtpusu` int(11) NOT NULL,
  `fecha_registro` datetime DEFAULT current_timestamp(),
  `activo` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`idusu`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `permisos` (
  `idpermiso` INT NOT NULL AUTO_INCREMENT,
  `idempleado` INT NOT NULL,
  `tipo` VARCHAR(100) NOT NULL,
  `fecha_inicio` DATE NOT NULL,
  `fecha_fin` DATE NOT NULL,
  `estado` VARCHAR(50) NOT NULL DEFAULT 'Pendiente',
  PRIMARY KEY (`idpermiso`),
  FOREIGN KEY (`idempleado`) REFERENCES `usu`(`idusu`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usu`
--

INSERT INTO `usu` (`idusu`, `username`, `nombre_completo`, `naturaleza`, `telefono`, `email`, `clave`, `tpusu_idtpusu`, `fecha_registro`, `activo`) VALUES
(4, 'inventario', 'Pedro Rojas', 'Persona', '3204567890', 'pedro@floreria.com', '35de1a09ec425493170e5b83380e4fd5fba692fc2e6b48f2b9c042644bcfef74', 3, '2025-07-07 14:43:34', 1),
(40, 'jorge', 'Jorge Luis Puentes Brochero', 'Carrera 5#20-65', '3217837594', 'jorgepb2007@gmail.com', '$2y$10$7nY2k7SQxRQ8rPXf/mcZW..il8iOAZZs.IGOonV4eHMZAPvtlV7nK', 5, '2025-08-05 09:52:19', 1),
(41, 'david', 'david parada', 'ljjhh', '32135', 'david@gmail.com', '$2y$10$zykB2BQdQwivfueZ6a1KF.IXDvnODmn2vo9n1N8a1sZkqzc6DL8TW', 2, '2025-08-05 10:41:00', 1),
(42, 'juan', 'juan luis', '23', '5', 'juan@gmail.com', '$2y$10$GSHbrGQ2eZDJDHeyviNxluSZcMvm8MjTi8E7IGm8rkyM8XT8A/Xpy', 1, '2025-08-14 21:27:21', 1),
(43, 'maria', 'maria sanchez', 'sd', '456', 'maria@gmail.com', '$2y$10$KIkPfVcQMKLKVDGFbh9sCO1vgk9N4m.fzIopEV8bzh02Vl6fgCMVG', 5, '2025-08-14 21:28:27', 1),
(44, 'mauecheveria', 'mauricio', 'vcs#', '32145', 'mau@gmail.com', '$2y$10$gqUA70VS4LNnmsj9mKdUbeA4OP.VRU4Z0CVFjZjRtST4tsSoVn/KK', 5, '2025-08-26 10:03:00', 1);

-- --------------------------------------------------------

--
-- Tabla de turnos para gestión de empleados
--

CREATE TABLE `turnos` (
  `idturno` INT NOT NULL AUTO_INCREMENT,
  `idempleado` INT NOT NULL,
  `fecha_inicio` DATE NOT NULL,
  `fecha_fin` DATE NOT NULL,
  `horario` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`idturno`),
  FOREIGN KEY (`idempleado`) REFERENCES `usu`(`idusu`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Puedes agregar más campos si lo necesitas (tipo_turno, temporada, observaciones)

-- --------------------------------------------------------

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `cfg_sis`
--
ALTER TABLE `cfg_sis`
  ADD PRIMARY KEY (`id_cfg`),
  ADD KEY `fk_cfg_usu` (`id_usu_mod`);

--
-- Indices de la tabla `cli`
--
ALTER TABLE `cli`
  ADD PRIMARY KEY (`idcli`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_cli_empleado` (`empleado_registro`),
  ADD KEY `idx_cli_fecha` (`fecha_registro`);

--
-- Indices de la tabla `detped`
--
ALTER TABLE `detped`
  ADD PRIMARY KEY (`iddetped`),
  ADD KEY `fk_detped_ped` (`idped`),
  ADD KEY `fk_detped_tflor` (`idtflor`);

--
-- Indices de la tabla `ent`
--
ALTER TABLE `ent`
  ADD PRIMARY KEY (`ident`),
  ADD KEY `fk_ent_ped` (`ped_idped`);

--
-- Indices de la tabla `inv`
--
ALTER TABLE `inv`
  ADD PRIMARY KEY (`idinv`),
  ADD KEY `fk_inv_tflor` (`tflor_idtflor`),
  ADD KEY `idx_inv_empleado` (`empleado_id`);

--
-- Indices de la tabla `inv_historial`
--
ALTER TABLE `inv_historial`
  ADD PRIMARY KEY (`idhistorial`),
  ADD KEY `fk_invhist_inv` (`idinv`),
  ADD KEY `fk_invhist_usu` (`idusu`);

--
-- Indices de la tabla `pag`
--
ALTER TABLE `pag`
  ADD PRIMARY KEY (`idpag`);

--
-- Indices de la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD PRIMARY KEY (`idpago`),
  ADD KEY `fk_pagos_ped` (`ped_idped`);

--
-- Indices de la tabla `ped`
--
ALTER TABLE `ped`
  ADD PRIMARY KEY (`idped`),
  ADD UNIQUE KEY `numped` (`numped`),
  ADD KEY `fk_ped_cli` (`cli_idcli`),
  ADD KEY `idx_ped_empleado` (`empleado_id`),
  ADD KEY `idx_ped_estado` (`estado`),
  ADD KEY `idx_ped_fecha` (`fecha_pedido`);

--
-- Indices de la tabla `perf`
--
ALTER TABLE `perf`
  ADD PRIMARY KEY (`idperf`);

--
-- Indices de la tabla `perf_perm`
--
ALTER TABLE `perf_perm`
  ADD PRIMARY KEY (`idperf`,`idperm`),
  ADD KEY `fk_perfperm_perf` (`idperf`),
  ADD KEY `fk_perfperm_perm` (`idperm`);

--
-- Indices de la tabla `perm`
--
ALTER TABLE `perm`
  ADD PRIMARY KEY (`idperm`),
  ADD UNIQUE KEY `codigo` (`codigo`);

--
-- Indices de la tabla `perm_tpusu`
--
ALTER TABLE `perm_tpusu`
  ADD PRIMARY KEY (`idtpusu`,`idperm`),
  ADD KEY `fk_perm_tpusu` (`idtpusu`),
  ADD KEY `fk_perm_perm` (`idperm`);

--
-- Indices de la tabla `pxp`
--
ALTER TABLE `pxp`
  ADD PRIMARY KEY (`idperf`,`idpag`),
  ADD KEY `fk_pxp_perf` (`idperf`),
  ADD KEY `fk_pxp_pag` (`idpag`);

--
-- Indices de la tabla `tflor`
--
ALTER TABLE `tflor`
  ADD PRIMARY KEY (`idtflor`);

--
-- Indices de la tabla `tokens_recuperacion`
--
ALTER TABLE `tokens_recuperacion`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idUsuario` (`idUsuario`),
  ADD KEY `idx_token` (`token`),
  ADD KEY `idx_expiracion` (`expiracion`);

--
-- Indices de la tabla `tpusu`
--
ALTER TABLE `tpusu`
  ADD PRIMARY KEY (`idtpusu`);

--
-- Indices de la tabla `usu`
--
ALTER TABLE `usu`
  ADD PRIMARY KEY (`idusu`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_usu_tpusu` (`tpusu_idtpusu`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `cfg_sis`
--
ALTER TABLE `cfg_sis`
  MODIFY `id_cfg` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `cli`
--
ALTER TABLE `cli`
  MODIFY `idcli` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `detped`
--
ALTER TABLE `detped`
  MODIFY `iddetped` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT de la tabla `ent`
--
ALTER TABLE `ent`
  MODIFY `ident` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `inv`
--
ALTER TABLE `inv`
  MODIFY `idinv` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `inv_historial`
--
ALTER TABLE `inv_historial`
  MODIFY `idhistorial` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `pag`
--
ALTER TABLE `pag`
  MODIFY `idpag` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `pagos`
--
ALTER TABLE `pagos`
  MODIFY `idpago` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT de la tabla `ped`
--
ALTER TABLE `ped`
  MODIFY `idped` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT de la tabla `perf`
--
ALTER TABLE `perf`
  MODIFY `idperf` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `perm`
--
ALTER TABLE `perm`
  MODIFY `idperm` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `tflor`
--
ALTER TABLE `tflor`
  MODIFY `idtflor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de la tabla `tokens_recuperacion`
--
ALTER TABLE `tokens_recuperacion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `tpusu`
--
ALTER TABLE `tpusu`
  MODIFY `idtpusu` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `usu`
--
ALTER TABLE `usu`
  MODIFY `idusu` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `cfg_sis`
--
ALTER TABLE `cfg_sis`
  ADD CONSTRAINT `cfg_sis_ibfk_1` FOREIGN KEY (`id_usu_mod`) REFERENCES `usu` (`idusu`);

--
-- Filtros para la tabla `detped`
--
ALTER TABLE `detped`
  ADD CONSTRAINT `detped_ibfk_1` FOREIGN KEY (`idped`) REFERENCES `ped` (`idped`),
  ADD CONSTRAINT `detped_ibfk_2` FOREIGN KEY (`idtflor`) REFERENCES `tflor` (`idtflor`);

--
-- Filtros para la tabla `ent`
--
ALTER TABLE `ent`
  ADD CONSTRAINT `ent_ibfk_1` FOREIGN KEY (`ped_idped`) REFERENCES `ped` (`idped`);

--
-- Filtros para la tabla `inv`
--
ALTER TABLE `inv`
  ADD CONSTRAINT `inv_ibfk_1` FOREIGN KEY (`tflor_idtflor`) REFERENCES `tflor` (`idtflor`);

--
-- Filtros para la tabla `inv_historial`
--
ALTER TABLE `inv_historial`
  ADD CONSTRAINT `inv_historial_ibfk_1` FOREIGN KEY (`idinv`) REFERENCES `inv` (`idinv`),
  ADD CONSTRAINT `inv_historial_ibfk_2` FOREIGN KEY (`idusu`) REFERENCES `usu` (`idusu`);

--
-- Filtros para la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD CONSTRAINT `pagos_ibfk_1` FOREIGN KEY (`ped_idped`) REFERENCES `ped` (`idped`);

--
-- Filtros para la tabla `ped`
--
ALTER TABLE `ped`
  ADD CONSTRAINT `ped_ibfk_1` FOREIGN KEY (`cli_idcli`) REFERENCES `cli` (`idcli`);

--
-- Filtros para la tabla `perf_perm`
--
ALTER TABLE `perf_perm`
  ADD CONSTRAINT `perf_perm_ibfk_1` FOREIGN KEY (`idperf`) REFERENCES `perf` (`idperf`),
  ADD CONSTRAINT `perf_perm_ibfk_2` FOREIGN KEY (`idperm`) REFERENCES `perm` (`idperm`);

--
-- Filtros para la tabla `perm_tpusu`
--
ALTER TABLE `perm_tpusu`
  ADD CONSTRAINT `perm_tpusu_ibfk_1` FOREIGN KEY (`idtpusu`) REFERENCES `tpusu` (`idtpusu`),
  ADD CONSTRAINT `perm_tpusu_ibfk_2` FOREIGN KEY (`idperm`) REFERENCES `perm` (`idperm`);

--
-- Filtros para la tabla `pxp`
--
ALTER TABLE `pxp`
  ADD CONSTRAINT `pxp_ibfk_1` FOREIGN KEY (`idperf`) REFERENCES `perf` (`idperf`),
  ADD CONSTRAINT `pxp_ibfk_2` FOREIGN KEY (`idpag`) REFERENCES `pag` (`idpag`);

--
-- Filtros para la tabla `tokens_recuperacion`
--
ALTER TABLE `tokens_recuperacion`
  ADD CONSTRAINT `tokens_recuperacion_ibfk_1` FOREIGN KEY (`idUsuario`) REFERENCES `usu` (`idusu`) ON DELETE CASCADE;

--
-- Filtros para la tabla `usu`
--
ALTER TABLE `usu`
  ADD CONSTRAINT `usu_ibfk_1` FOREIGN KEY (`tpusu_idtpusu`) REFERENCES `tpusu` (`idtpusu`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- --------------------------------------------------------
--
-- Estructura de la tabla `vacaciones`
--
CREATE TABLE `vacaciones` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `id_empleado` INT NOT NULL,
  `fecha_inicio` DATE NOT NULL,
  `fecha_fin` DATE NOT NULL,
  `estado` VARCHAR(20) DEFAULT 'En curso',
  `motivo` VARCHAR(255),
  `fecha_creacion` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`id_empleado`) REFERENCES `usu`(`idusu`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
--
-- Agregar columna de vacaciones a la tabla usuALTER TABLE usu ADD COLUMN vacaciones INT DEFAULT 0;
--
ALTER TABLE usu ADD COLUMN vacaciones INT DEFAULT 0;
-- --------------------------------------------------------
-- Script para crear la tabla empresa faltante
USE flores;

CREATE TABLE IF NOT EXISTS `empresa` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL DEFAULT '',
  `direccion` varchar(255) DEFAULT '',
  `telefono` varchar(100) DEFAULT '',
  `email` varchar(255) DEFAULT '',
  `horario` varchar(255) DEFAULT '',
  `fecha_creacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insertar un registro inicial para la empresa
INSERT INTO `empresa` (`nombre`, `direccion`, `telefono`, `email`, `horario`) VALUES
('FloralTech', 'Dirección de la empresa', '555-0123', 'info@floraltech.com', 'Lunes a Viernes 8:00 AM - 6:00 PM')
ON DUPLICATE KEY UPDATE
`nombre` = VALUES(`nombre`);